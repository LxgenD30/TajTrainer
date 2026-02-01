<?php

namespace App\Services;

use App\Models\TajweedErrorLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProgressTracker
{
    /**
     * Build base query for user's error logs through proper FK joins
     */
    private function getUserErrorLogsQuery($userId)
    {
        return DB::table('tajweed_error_logs as tel')
            ->leftJoin('practice_sessions as ps', function($join) use ($userId) {
                $join->on('tel.practice_session_id', '=', 'ps.id')
                     ->where('ps.student_id', '=', $userId);
            })
            ->leftJoin('assignment_submissions as asub', function($join) use ($userId) {
                $join->on('tel.assignment_submission_id', '=', 'asub.id')
                     ->where('asub.student_id', '=', $userId);
            })
            ->whereNotNull(DB::raw('COALESCE(ps.id, asub.id)'));
    }
    
    /**
     * Get user's overall progress statistics
     */
    public function getUserProgress($userId, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        $totalLogs = $this->getUserErrorLogsQuery($userId)
            ->where('tel.created_at', '>=', $startDate)
            ->count();
            
        $correctCount = $this->getUserErrorLogsQuery($userId)
            ->where('tel.created_at', '>=', $startDate)
            ->where('tel.was_correct', true)
            ->count();
            
        $accuracy = $totalLogs > 0 ? round(($correctCount / $totalLogs) * 100, 2) : 0;
        
        // Get assignment-specific stats
        $assignmentLogs = $this->getUserErrorLogsQuery($userId)
            ->where('tel.created_at', '>=', $startDate)
            ->whereNotNull('tel.assignment_submission_id')
            ->count();
            
        $assignmentCorrect = $this->getUserErrorLogsQuery($userId)
            ->where('tel.created_at', '>=', $startDate)
            ->whereNotNull('tel.assignment_submission_id')
            ->where('tel.was_correct', true)
            ->count();
            
        $assignmentAccuracy = $assignmentLogs > 0 ? round(($assignmentCorrect / $assignmentLogs) * 100, 2) : 0;
        
        // Get practice-specific stats
        $practiceLogs = $this->getUserErrorLogsQuery($userId)
            ->where('tel.created_at', '>=', $startDate)
            ->whereNotNull('tel.practice_session_id')
            ->count();
            
        $practiceCorrect = $this->getUserErrorLogsQuery($userId)
            ->where('tel.created_at', '>=', $startDate)
            ->whereNotNull('tel.practice_session_id')
            ->where('tel.was_correct', true)
            ->count();
            
        $practiceAccuracy = $practiceLogs > 0 ? round(($practiceCorrect / $practiceLogs) * 100, 2) : 0;
        
        return [
            'total_attempts' => $totalLogs,
            'correct_count' => $correctCount,
            'error_count' => $totalLogs - $correctCount,
            'accuracy' => $accuracy,
            'period_days' => $days,
            
            // Assignment stats
            'assignment_attempts' => $assignmentLogs,
            'assignment_correct' => $assignmentCorrect,
            'assignment_errors' => $assignmentLogs - $assignmentCorrect,
            'assignment_accuracy' => $assignmentAccuracy,
            
            // Practice stats
            'practice_attempts' => $practiceLogs,
            'practice_correct' => $practiceCorrect,
            'practice_errors' => $practiceLogs - $practiceCorrect,
            'practice_accuracy' => $practiceAccuracy,
        ];
    }
    
    /**
     * Get user's top weaknesses (most common errors)
     */
    public function getTopWeaknesses($userId, $limit = 5)
    {
        $errors = $this->getUserErrorLogsQuery($userId)
            ->where('tel.was_correct', false)
            ->select('tel.rule_name', 'tel.error_type', DB::raw('count(*) as error_count'))
            ->groupBy('tel.rule_name', 'tel.error_type')
            ->orderBy('error_count', 'desc')
            ->limit($limit)
            ->get();
            
        return $errors->map(function($error) use ($userId) {
            $total = $this->getUserErrorLogsQuery($userId)
                ->where('tel.rule_name', $error->rule_name)
                ->where('tel.error_type', $error->error_type)
                ->count();
                
            return (object) [
                'rule_name' => $error->rule_name ?? $error->error_type,
                'error_type' => $error->error_type,
                'error_count' => $error->error_count,
                'total_attempts' => $total,
                'fail_rate' => $total > 0 ? round(($error->error_count / $total) * 100, 2) : 0,
            ];
        });
    }
    
    /**
     * Get improvement trends (comparing two periods)
     */
    public function getImprovementTrends($userId)
    {
        // Current week
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekStats = $this->getPeriodStats($userId, $currentWeekStart, Carbon::now());
        
        // Previous week
        $prevWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $prevWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        $prevWeekStats = $this->getPeriodStats($userId, $prevWeekStart, $prevWeekEnd);
        
        $improvement = $currentWeekStats['accuracy'] - $prevWeekStats['accuracy'];
        
        return [
            'current_week_accuracy' => $currentWeekStats['accuracy'],
            'current_week_total' => $currentWeekStats['total'],
            'previous_week_accuracy' => $prevWeekStats['accuracy'],
            'previous_week_total' => $prevWeekStats['total'],
            'accuracy_change' => round($improvement, 2),
            'is_improving' => $improvement > 0,
        ];
    }
    
    /**
     * Get stats for a specific period
     */
    private function getPeriodStats($userId, $start, $end)
    {
        $total = $this->getUserErrorLogsQuery($userId)
            ->whereBetween('tel.created_at', [$start, $end])
            ->count();
            
        $correct = $this->getUserErrorLogsQuery($userId)
            ->whereBetween('tel.created_at', [$start, $end])
            ->where('tel.was_correct', true)
            ->count();
            
        return [
            'total' => $total,
            'correct' => $correct,
            'errors' => $total - $correct,
            'accuracy' => $total > 0 ? round(($correct / $total) * 100, 2) : 0,
        ];
    }
    
    /**
     * Get daily progress data for charts
     */
    public function getDailyProgress($userId, $days = 7)
    {
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();
            
            $stats = $this->getPeriodStats($userId, $startOfDay, $endOfDay);
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->format('D'),
                'accuracy' => $stats['accuracy'],
                'total' => $stats['total'],
                'correct' => $stats['correct'],
                'errors' => $stats['errors'],
            ];
        }
        
        return $data;
    }
    
    /**
     * Get most improved rules
     */
    public function getMostImproved($userId, $limit = 3)
    {
        $rules = $this->getUserErrorLogsQuery($userId)
            ->select('tel.rule_name', 'tel.error_type')
            ->distinct()
            ->get();
            
        $improvements = [];
        
        foreach ($rules as $rule) {
            // Last 7 days
            $recentStart = Carbon::now()->subDays(7);
            $recentStats = $this->getRuleStats($userId, $rule->error_type, $rule->rule_name, $recentStart, Carbon::now());
            
            // Previous 7 days
            $previousStart = Carbon::now()->subDays(14);
            $previousEnd = Carbon::now()->subDays(7);
            $previousStats = $this->getRuleStats($userId, $rule->error_type, $rule->rule_name, $previousStart, $previousEnd);
            
            if ($previousStats['total'] > 0 && $recentStats['total'] > 0) {
                $improvement = $recentStats['accuracy'] - $previousStats['accuracy'];
                
                if ($improvement > 0) {
                    $improvements[] = (object) [
                        'rule_name' => $rule->rule_name ?? $rule->error_type,
                        'error_type' => $rule->error_type,
                        'old_accuracy' => $previousStats['accuracy'],
                        'new_accuracy' => $recentStats['accuracy'],
                        'improvement' => round($improvement, 2),
                    ];
                }
            }
        }
        
        // Sort by improvement descending
        usort($improvements, function($a, $b) {
            return $b->improvement <=> $a->improvement;
        });
        
        return array_slice($improvements, 0, $limit);
    }
    
    /**
     * Get stats for a specific rule
     */
    private function getRuleStats($userId, $errorType, $ruleName, $start, $end)
    {
        $query = $this->getUserErrorLogsQuery($userId)
            ->where('tel.error_type', $errorType)
            ->whereBetween('tel.created_at', [$start, $end]);
            
        if ($ruleName) {
            $query->where('tel.rule_name', $ruleName);
        }
        
        $total = $query->count();
        
        $correctQuery = $this->getUserErrorLogsQuery($userId)
            ->where('tel.error_type', $errorType)
            ->whereBetween('tel.created_at', [$start, $end])
            ->where('tel.was_correct', true);
            
        if ($ruleName) {
            $correctQuery->where('tel.rule_name', $ruleName);
        }
        
        $correct = $correctQuery->count();
        
        return [
            'total' => $total,
            'correct' => $correct,
            'accuracy' => $total > 0 ? round(($correct / $total) * 100, 2) : 0,
        ];
    }
    
    /**
     * Get recurring errors (errors that appear multiple times)
     */
    public function getRecurringErrors($userId, $threshold = 3)
    {
        return $this->getUserErrorLogsQuery($userId)
            ->where('tel.was_correct', false)
            ->select('tel.rule_name', 'tel.error_type', 'tel.issue_description', DB::raw('count(*) as occurrences'))
            ->groupBy('tel.rule_name', 'tel.error_type', 'tel.issue_description')
            ->having('occurrences', '>=', $threshold)
            ->orderBy('occurrences', 'desc')
            ->get();
    }
    
    /**
     * Build base query for class error logs through proper FK joins
     */
    private function getClassErrorLogsQuery($students)
    {
        return DB::table('tajweed_error_logs as tel')
            ->leftJoin('practice_sessions as ps', function($join) use ($students) {
                $join->on('tel.practice_session_id', '=', 'ps.id')
                     ->whereIn('ps.student_id', $students);
            })
            ->leftJoin('assignment_submissions as asub', function($join) use ($students) {
                $join->on('tel.assignment_submission_id', '=', 'asub.id')
                     ->whereIn('asub.student_id', $students);
            })
            ->whereNotNull(DB::raw('COALESCE(ps.id, asub.id)'));
    }
    
    /**
     * Get class-wide statistics (for teachers)
     */
    public function getClassStats($classId)
    {
        // Get all students in class
        $students = DB::table('enrollment')
            ->where('class_id', $classId)
            ->pluck('user_id');
            
        if ($students->isEmpty()) {
            return [
                'total_students' => 0,
                'active_students' => 0,
                'class_average_accuracy' => 0,
                'total_practice_sessions' => 0,
                'total_attempts' => 0,
                'common_errors' => collect([]),
            ];
        }
        
        $totalLogs = $this->getClassErrorLogsQuery($students)->count();
        $correctCount = $this->getClassErrorLogsQuery($students)
            ->where('tel.was_correct', true)
            ->count();
            
        $classAccuracy = $totalLogs > 0 ? round(($correctCount / $totalLogs) * 100, 2) : 0;
        
        // Count active students (practiced in last 30 days)
        $activeStudents = $this->getClassErrorLogsQuery($students)
            ->where('tel.created_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('COALESCE(ps.student_id, asub.student_id) as student_id'))
            ->distinct()
            ->count();
        
        // Count total practice sessions using student_id
        $totalPracticeSessions = DB::table('practice_sessions')
            ->whereIn('student_id', $students)
            ->count();
        
        // Most common class-wide errors with student count
        $commonErrors = $this->getClassErrorLogsQuery($students)
            ->where('tel.was_correct', false)
            ->select(
                'tel.rule_name', 
                'tel.error_type', 
                DB::raw('count(*) as total_errors'),
                DB::raw('count(distinct COALESCE(ps.student_id, asub.student_id)) as student_count')
            )
            ->groupBy('tel.rule_name', 'tel.error_type')
            ->orderBy('total_errors', 'desc')
            ->limit(5)
            ->get();
            
        return [
            'total_students' => $students->count(),
            'active_students' => $activeStudents,
            'class_average_accuracy' => $classAccuracy,
            'total_practice_sessions' => $totalPracticeSessions,
            'total_attempts' => $totalLogs,
            'common_errors' => $commonErrors,
        ];
    }
}
