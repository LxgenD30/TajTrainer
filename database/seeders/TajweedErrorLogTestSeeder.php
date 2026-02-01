<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TajweedErrorLog;
use Illuminate\Support\Facades\DB;

class TajweedErrorLogTestSeeder extends Seeder
{
    /**
     * Seed test data for tajweed error logs (using proper FK columns)
     */
    public function run(): void
    {
        // Get existing practice sessions and assignment submissions
        $practiceSessions = DB::table('practice_sessions')->get();
        $assignmentSubmissions = DB::table('assignment_submissions')->get();
        
        echo "Found {$practiceSessions->count()} practice sessions\n";
        echo "Found {$assignmentSubmissions->count()} assignment submissions\n";
        
        // Seed error logs for practice sessions
        foreach ($practiceSessions as $session) {
            // Add some Madd errors
            TajweedErrorLog::create([
                'practice_session_id' => $session->id,
                'error_type' => 'madd',
                'rule_name' => 'Madd Elongation',
                'timestamp_in_audio' => rand(100, 5000) / 100, // Random time 1-50 seconds
                'severity' => ['minor', 'moderate', 'major'][rand(0, 2)],
                'was_correct' => rand(0, 1) === 1,
                'issue_description' => 'Elongation test data',
                'recommendation' => 'Practice Madd elongation',
            ]);
            
            // Add some Noon Sakin errors
            TajweedErrorLog::create([
                'practice_session_id' => $session->id,
                'error_type' => 'noon_sakin',
                'rule_name' => ['Idhar', 'Idgham', 'Iqlab', 'Ikhfa'][rand(0, 3)],
                'timestamp_in_audio' => rand(100, 5000) / 100,
                'severity' => ['minor', 'moderate', 'major'][rand(0, 2)],
                'was_correct' => rand(0, 1) === 1,
                'issue_description' => 'Noon Sakin test data',
                'recommendation' => 'Review Noon Sakin rules',
            ]);
        }
        
        // Seed error logs for assignment submissions
        foreach ($assignmentSubmissions as $submission) {
            // Add some Madd errors
            TajweedErrorLog::create([
                'assignment_submission_id' => $submission->id,
                'error_type' => 'madd',
                'rule_name' => 'Madd Elongation',
                'timestamp_in_audio' => rand(100, 5000) / 100,
                'severity' => ['minor', 'moderate', 'major'][rand(0, 2)],
                'was_correct' => rand(0, 1) === 1,
                'issue_description' => 'Assignment elongation test',
                'recommendation' => 'Review Madd rules',
            ]);
            
            // Add some Noon Sakin errors
            TajweedErrorLog::create([
                'assignment_submission_id' => $submission->id,
                'error_type' => 'noon_sakin',
                'rule_name' => ['Idhar', 'Idgham', 'Iqlab', 'Ikhfa'][rand(0, 3)],
                'timestamp_in_audio' => rand(100, 5000) / 100,
                'severity' => ['minor', 'moderate', 'major'][rand(0, 2)],
                'was_correct' => rand(0, 1) === 1,
                'issue_description' => 'Assignment pronunciation test',
                'recommendation' => 'Practice proper articulation',
            ]);
        }
        
        $totalLogs = TajweedErrorLog::count();
        echo "Created {$totalLogs} tajweed error logs\n";
    }
}
