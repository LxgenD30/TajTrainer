<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssignmentSubmission;
use App\Models\Assignment;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Material;

class TestSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        // Find teacher and student
        $teacher = User::where('role_id', 3)->first();
        $student = User::where('role_id', 2)->first();

        if (!$teacher || !$student) {
            $this->command->error('❌ Could not find teacher or student');
            return;
        }

        // Create a classroom
        $classroom = Classroom::create([
            'class_name' => 'Tajweed Basics - Test Class',
            'teacher_id' => $teacher->id,
            'description' => 'Test classroom for Tajweed analyzer demonstration. Students will learn proper Madd and Noon Sakin pronunciation.',
            'access_code' => 'TEST' . rand(1000, 9999),
        ]);

        // Enroll student
        \DB::table('enrollment')->insert([
            'class_id' => $classroom->id,
            'user_id' => $student->id,
            'date_joined' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get a material
        $material = Material::first();

        // Create an assignment
        $assignment = Assignment::create([
            'class_id' => $classroom->id,
            'material_id' => $material ? $material->material_id : 1,
            'instructions' => 'Recite Surah Al-Fatiha with proper Tajweed rules, focusing on Madd elongations and Noon Sakin pronunciation. Apply Ikhfa, Idgham, Iqlab and Idhar rules correctly.',
            'due_date' => now()->addDays(7),
            'total_marks' => 100,
            'is_voice_submission' => true,
            'surah' => 'Al-Fatiha',
            'start_verse' => 1,
            'end_verse' => 7,
        ]);

        // Create a test submission with sample Tajweed data
        AssignmentSubmission::create([
            'assignment_id' => $assignment->assignment_id,
            'student_id' => $student->id,
            'text_submission' => null,
            'audio_file_path' => 'submissions/test_audio.wav',
            'transcription' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ ۝ الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ ۝ الرَّحْمَٰنِ الرَّحِيمِ ۝ مَالِكِ يَوْمِ الدِّينِ',
            'tajweed_analysis' => json_encode([
                'madd_analysis' => [
                    'total_elongations' => 12,
                    'correct_elongations' => 10,
                    'percentage' => 83.33,
                    'issues' => [
                        [
                            'time' => 2.5,
                            'issue' => 'Elongation duration too short (1.2s) - should be at least 2 counts'
                        ],
                        [
                            'time' => 8.3,
                            'issue' => 'Madd Al-Arid Lissukoon not properly extended before pause'
                        ]
                    ],
                    'details' => []
                ],
                'noon_sakin_analysis' => [
                    'total_occurrences' => 8,
                    'correct_pronunciation' => 7,
                    'percentage' => 87.5,
                    'issues' => [
                        [
                            'time' => 5.3,
                            'issue' => 'Ikhfa (concealment) - nasalization not clear enough, check tongue position'
                        ]
                    ]
                ],
                'overall_score' => [
                    'score' => 85.42,
                    'grade' => 'Very Good',
                    'feedback' => 'Excellent recitation overall! Your Madd elongations are mostly correct with good timing. Focus on maintaining consistent elongation durations, especially for Madd Al-Arid Lissukoon before pauses. Your Noon Sakin pronunciation is strong - just work on clearer nasalization during Ikhfa. Keep practicing with a teacher to perfect these advanced rules.'
                ]
            ]),
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->command->info('✅ Test classroom, assignment, and submission created with Tajweed analysis data!');
        $this->command->info('📝 Classroom ID: ' . $classroom->id);
        $this->command->info('📋 Assignment ID: ' . $assignment->assignment_id);
        $this->command->info('👤 Student enrolled: ' . $student->email);
    }
}
