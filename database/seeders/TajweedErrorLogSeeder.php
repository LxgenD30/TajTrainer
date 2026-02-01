<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TajweedErrorLog;
use Carbon\Carbon;

class TajweedErrorLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 2; // Student user ID
        
        // Create 20 Madd errors/corrections over last 14 days
        for ($i = 0; $i < 20; $i++) {
            TajweedErrorLog::create([
                'user_id' => $userId,
                'session_type' => 'practice',
                'session_id' => rand(1, 10),
                'error_type' => 'Madd',
                'rule_name' => 'Madd',
                'timestamp_in_audio' => rand(10, 300) / 10,
                'severity' => rand(0, 1) ? 'moderate' : 'minor',
                'was_correct' => rand(0, 100) > 30, // 70% correct
                'issue_description' => 'Madd elongation practice',
                'recommendation' => 'Hold vowel for proper duration',
                'created_at' => Carbon::now()->subDays(rand(0, 13))->subHours(rand(0, 23)),
            ]);
        }
        
        // Create 20 Noon Sakin errors/corrections over last 14 days
        for ($i = 0; $i < 20; $i++) {
            TajweedErrorLog::create([
                'user_id' => $userId,
                'session_type' => 'practice',
                'session_id' => rand(1, 10),
                'error_type' => 'Noon Sakin',
                'rule_name' => 'Noon Sakin',
                'timestamp_in_audio' => rand(10, 300) / 10,
                'severity' => rand(0, 1) ? 'major' : 'moderate',
                'was_correct' => rand(0, 100) > 40, // 60% correct
                'issue_description' => 'Noon Sakin pronunciation practice',
                'recommendation' => 'Focus on correct articulation points',
                'created_at' => Carbon::now()->subDays(rand(0, 13))->subHours(rand(0, 23)),
            ]);
        }
        
        // Create some assignment errors with more detail
        $rules = ['Idhar', 'Idgham', 'Iqlab', 'Ikhfa'];
        for ($i = 0; $i < 10; $i++) {
            $rule = $rules[array_rand($rules)];
            TajweedErrorLog::create([
                'user_id' => $userId,
                'session_type' => 'assignment',
                'session_id' => rand(1, 5),
                'error_type' => 'Noon Sakin',
                'rule_name' => $rule,
                'timestamp_in_audio' => rand(10, 300) / 10,
                'severity' => rand(0, 1) ? 'major' : 'moderate',
                'was_correct' => rand(0, 100) > 35, // 65% correct
                'issue_description' => "Rule: {$rule} - Articulation needs improvement",
                'recommendation' => "Review {$rule} rules and practice pronunciation",
                'created_at' => Carbon::now()->subDays(rand(0, 29))->subHours(rand(0, 23)),
            ]);
        }
        
        $this->command->info('Created 50 test error log entries for user ' . $userId);
    }
}
