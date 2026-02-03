<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\Material;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $stats = [];
        
        \Log::info('HomeController index accessed', [
            'user_id' => Auth::id(),
            'role_id' => Auth::user()->role_id ?? 'null',
            'user_name' => Auth::user()->name ?? 'unknown'
        ]);
        
        if (Auth::user()->role_id == 3) {
            \Log::info('Loading teacher dashboard for user: ' . Auth::id());
            $teacherId = Auth::id();
            $stats['total_classes'] = Classroom::where('teacher_id', $teacherId)->count();
            $classroomIds = Classroom::where('teacher_id', $teacherId)->pluck('id')->toArray();
            
            if (count($classroomIds) > 0) {
                $stats['total_students'] = \DB::table('enrollment')
                    ->whereIn('class_id', $classroomIds)
                    ->distinct('user_id')
                    ->count('user_id');
            } else {
                $stats['total_students'] = 0;
            }
            
            $stats['total_assignments'] = count($classroomIds) > 0 
                ? Assignment::whereIn('class_id', $classroomIds)->count() 
                : 0;
            
            $stats['total_materials'] = Material::count();
            
            \Log::info('Teacher stats calculated', $stats);
            return view('teachers.index', compact('stats'));
        } elseif (Auth::user()->role_id == 2) {
            // Student dashboard - show stats
            $student = Student::with(['classrooms.teacher', 'classrooms.assignments', 'scores'])
                ->findOrFail(Auth::id());
            
            $enrolledClassesCount = $student->classrooms->count();
            $completedAssignments = $student->scores->count();
            $averageScore = $student->scores->avg('score') ?? 0;
            $totalAssignments = $student->classrooms->flatMap->assignments->count();
            $pendingAssignments = $totalAssignments - $completedAssignments;
            
            return view('students.index', compact(
                'student',
                'enrolledClassesCount',
                'completedAssignments',
                'averageScore',
                'pendingAssignments'
            ));
        }
        
        // Default fallback - show generic home
        return view('home');
    }
}
