<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classroom;
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
}
