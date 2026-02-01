<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First seed the roles
        $this->call(RoleSeeder::class);
        
        // Seed materials
        $this->call(MaterialSeeder::class);
        
        // Create predefined users for each role
        
        // 1. Student User
        $studentUser = User::create([
            'email' => 'student@gmail.com',
            'password' => bcrypt('12345678'),
            'role_id' => 2, // Student
        ]);
        
        // Create corresponding Student record
        Student::create([
            'id' => $studentUser->id,
            'name' => 'Student',
            'biodata' => 'This is a test student account for development.',
            'current_level' => 'beginner',
        ]);
        
        // 2. Teacher User
        $teacherUser = User::create([
            'email' => 'teacher@gmail.com',
            'password' => bcrypt('12345678'),
            'role_id' => 3, // Teacher
        ]);
        
        // Create corresponding Teacher record
        Teacher::create([
            'id' => $teacherUser->id,
            'name' => 'Teacher',
            'biodata' => 'This is a test teacher account for development.',
            'title' => 'Ustadh',
        ]);
        
        // Seed test submissions and enrollments
        $this->call(TestSubmissionSeeder::class);
    }
}
