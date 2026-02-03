<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classroom;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentListController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::where('teacher_id', Auth::id())
            ->with(['students'])
            ->get();
        
        $students = Student::whereHas('classrooms', function($query) {
                $query->where('teacher_id', Auth::id());
            })
            ->with(['classrooms' => function($query) {
                $query->where('teacher_id', Auth::id());
            }])
            ->get();
        
        return view('teachers.students', compact('students', 'classrooms'));
    }
    
    public function show($id)
    {
        $student = Student::with(['user', 'classrooms' => function($query) {
                $query->where('teacher_id', Auth::id())
                    ->with(['assignments']);
            }])
            ->findOrFail($id);
        
        // Get submissions for this student in teacher's classes
        $submissions = AssignmentSubmission::whereHas('assignment.classroom', function($query) {
                $query->where('teacher_id', Auth::id());
            })
            ->where('student_id', $student->id)
            ->with(['assignment'])
            ->latest()
            ->get();
        
        return view('teachers.student-profile', compact('student', 'submissions'));
    }
}
