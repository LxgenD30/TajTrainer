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
        
        if (Auth::user()->role_id == 3) {
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
            
            return view('layouts.tlayout.dashboard', compact('stats'));
        } elseif (Auth::user()->role_id == 2) {
            // Student dashboard
            return redirect()->route('student.classes');
        }
        
        // Default fallback
        return redirect('/home');
    }
}
