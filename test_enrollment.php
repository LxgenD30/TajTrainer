<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== STUDENTS ===" . PHP_EOL;
$students = App\Models\Student::with('classrooms')->get();
foreach ($students as $student) {
    echo "Student ID: {$student->id} - {$student->name}" . PHP_EOL;
    echo "  Classrooms count: {$student->classrooms->count()}" . PHP_EOL;
    foreach ($student->classrooms as $classroom) {
        echo "    - {$classroom->name}" . PHP_EOL;
    }
}

echo PHP_EOL . "=== ENROLLMENT TABLE ===" . PHP_EOL;
$enrollments = DB::table('enrollment')->get();
foreach ($enrollments as $enrollment) {
    echo "user_id: {$enrollment->user_id}, class_id: {$enrollment->class_id}" . PHP_EOL;
}

echo PHP_EOL . "=== CLASSROOMS ===" . PHP_EOL;
$classrooms = App\Models\Classroom::all();
foreach ($classrooms as $classroom) {
    echo "Classroom ID: {$classroom->id} - {$classroom->name}" . PHP_EOL;
}
