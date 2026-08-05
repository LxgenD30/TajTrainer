<?php

namespace App\Services;

use App\Models\TajweedErrorLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProgressTracker
{
    /**
     * Build base query for user's practice sessions with scored accuracy
     */
    private function getUserPracticeSessionsQuery($userId)
    {
        return DB::table('practice_sessions')
            ->where('student_id', $userId)
            ->whereNotNull('accuracy_score');
    }

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
     * Compute normalized rule correctness.
     *
     * For practice logs, this normalizes correctness using the session accuracy score
     * so historical logs are not skewed by old fallback/default behavior.
     */
    private function getNormalizedRuleStats($userId, $start = null, $end = null, $sessionType = null)
    {
        $query = $this->getUserErrorLogsQuery($userId);

        if ($start && $end) {
            $query->whereBetween('tel.created_at', [$start, $end]);
        } elseif ($start) {
            $query->where('tel.created_at', '>=', $start);
        }

        if ($sessionType === 'practice') {
            $query->whereNotNull('tel.practice_session_id');
        } elseif ($sessionType === 'assignment') {
            $query->whereNotNull('tel.assignment_submission_id');
        }

        $stats = $query
            ->selectRaw('COUNT(*) as total_rules')
            ->selectRaw(
                "SUM(CASE
                    WHEN tel.practice_session_id IS NOT NULL THEN
                        CASE
                            WHEN ps.accuracy_score IS NULL THEN CASE WHEN tel.was_correct = 1 THEN 1 ELSE 0 END
                            WHEN ps.accuracy_score >= 80 THEN 1
                            ELSE 0
                        END
                    ELSE CASE WHEN tel.was_correct = 1 THEN 1 ELSE 0 END
                END) as correct_rules"
            )
            ->first();

        $total = (int) ($stats->total_rules ?? 0);
        $correct = (int) ($stats->correct_rules ?? 0);

        return [
            'total' => $total,
            'correct' => $correct,
            'errors' => max(0, $total - $correct),
            'accuracy' => $total > 0 ? round(($correct / $total) * 100, 2) : 0,
        ];
    }
    
    /**
     * Get user's overall progress statistics
     */
    public function getUserProgress($userId, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        // Session-based practice performance (what users expect as attempts/performance)
        $practiceAttempts = $this->getUserPracticeSessionsQuery($userId)
            ->where('created_at', '>=', $startDate)
            ->count();

        $practiceAccuracyRaw = $this->getUserPracticeSessionsQuery($userId)
            ->where('created_at', '>=', $startDate)
            ->avg('accuracy_score');

        $practiceAccuracy = $practiceAttempts > 0
            ? round((float) $practiceAccuracyRaw, 2)
            : 0;

        // Rule-level correctness (normalized for historical practice logs)
        $ruleStats = $this->getNormalizedRuleStats($userId, $startDate);

        // Assignment-specific rule stats
        $assignmentRuleStats = $this->getNormalizedRuleStats($userId, $startDate, null, 'assignment');

        // Assignment attempts are distinct submissions represented in logs
        $assignmentAttempts = $this->getUserErrorLogsQuery($userId)
            ->where('tel.created_at', '>=', $startDate)
            ->whereNotNull('tel.assignment_submission_id')
            ->distinct('tel.assignment_submission_id')
            ->count('tel.assignment_submission_id');

        // Blend practice-session and assignment-rule accuracy by attempt count
        $totalAttempts = $practiceAttempts + $assignmentAttempts;
        if ($totalAttempts > 0) {
            $accuracy = round(
                (($practiceAccuracy * $practiceAttempts) + ($assignmentRuleStats['accuracy'] * $assignmentAttempts)) / $totalAttempts,
                2
            );
        } else {
            // Fallback for legacy users with only rule logs
            $accuracy = $ruleStats['accuracy'];
        }
        
        return [
            'total_attempts' => $totalAttempts,
            'correct_count' => $ruleStats['correct'],
            'error_count' => $ruleStats['errors'],
            'accuracy' => $accuracy,
            'period_days' => $days,
            
            // Assignment stats
            'assignment_attempts' => $assignmentAttempts,
            'assignment_correct' => $assignmentRuleStats['correct'],
            'assignment_errors' => $assignmentRuleStats['errors'],
            'assignment_accuracy' => $assignmentRuleStats['accuracy'],
            
            // Practice stats
            'practice_attempts' => $practiceAttempts,
            'practice_correct' => $practiceAccuracy >= 80 ? $practiceAttempts : 0,
            'practice_errors' => $practiceAccuracy >= 80 ? 0 : $practiceAttempts,
            'practice_accuracy' => $practiceAccuracy,
        ];
    }
    
    /**
     * Get user's top weaknesses (most common errors)
     */
    public function getTopWeaknesses($userId, $limit = 5)
    {
        $errors = $this->getUserErrorLogsQuery($userId)
            ->whereRaw("CASE
                WHEN tel.practice_session_id IS NOT NULL AND ps.accuracy_score IS NOT NULL THEN ps.accuracy_score < 80
                ELSE tel.was_correct = 0
            END")
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
        $hasData = ($currentWeekStats['total'] + $prevWeekStats['total']) > 0;

        $trendDirection = 'stable';
        if ($improvement > 0.01) {
            $trendDirection = 'improving';
        } elseif ($improvement < -0.01) {
            $trendDirection = 'declining';
        }
        
        return [
            'current_week_accuracy' => $currentWeekStats['accuracy'],
            'current_week_total' => $currentWeekStats['total'],
            'previous_week_accuracy' => $prevWeekStats['accuracy'],
            'previous_week_total' => $prevWeekStats['total'],
            'accuracy_change' => round($improvement, 2),
            'is_improving' => $trendDirection === 'improving',
            'trend_direction' => $trendDirection,
            'has_data' => $hasData,
        ];
    }
    
    /**
     * Get stats for a specific period
     */
    private function getPeriodStats($userId, $start, $end)
    {
        $practiceAttempts = $this->getUserPracticeSessionsQuery($userId)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $practiceAccuracyRaw = $this->getUserPracticeSessionsQuery($userId)
            ->whereBetween('created_at', [$start, $end])
            ->avg('accuracy_score');

        $practiceAccuracy = $practiceAttempts > 0
            ? round((float) $practiceAccuracyRaw, 2)
            : 0;

        $assignmentRuleStats = $this->getNormalizedRuleStats($userId, $start, $end, 'assignment');

        $assignmentAttempts = $this->getUserErrorLogsQuery($userId)
            ->whereBetween('tel.created_at', [$start, $end])
            ->whereNotNull('tel.assignment_submission_id')
            ->distinct('tel.assignment_submission_id')
            ->count('tel.assignment_submission_id');

        $total = $practiceAttempts + $assignmentAttempts;

        if ($total > 0) {
            $accuracy = round(
                (($practiceAccuracy * $practiceAttempts) + ($assignmentRuleStats['accuracy'] * $assignmentAttempts)) / $total,
                2
            );
        } else {
            $accuracy = 0;
        }

        $ruleStats = $this->getNormalizedRuleStats($userId, $start, $end);
        $correct = $ruleStats['correct'];
            
        return [
            'total' => $total,
            'correct' => $correct,
            'errors' => $ruleStats['errors'],
            'accuracy' => $accuracy,
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
            ->whereRaw("CASE
                WHEN tel.practice_session_id IS NOT NULL AND ps.accuracy_score IS NOT NULL THEN ps.accuracy_score >= 80
                ELSE tel.was_correct = 1
            END");
            
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
            ->whereRaw("CASE
                WHEN tel.practice_session_id IS NOT NULL AND ps.accuracy_score IS NOT NULL THEN ps.accuracy_score < 80
                ELSE tel.was_correct = 0
            END")
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
