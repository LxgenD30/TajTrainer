<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = Classroom::where('teacher_id', Auth::id())
            ->withCount([
                'students',
                'assignments',
                'assignments as pending_assignments' => function($query) {
                    $query->where('due_date', '>', now());
                },
                'assignments as completed_assignments' => function($query) {
                    $query->where('due_date', '<=', now());
                }
            ])
            ->latest()
            ->get();
        
        return view('layouts.tlayout.classrooms', compact('classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('classroom.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Generate unique 6-digit access code
        do {
            $accessCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Classroom::where('access_code', $accessCode)->exists());

        // Create classroom with authenticated teacher's ID
        Classroom::create([
            'teacher_id' => Auth::id(),
            'class_name' => $validated['class_name'],
            'description' => $validated['description'],
            'access_code' => $accessCode,
        ]);

        return redirect()->route('classroom.index')->with('success', 'Classroom created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        $user = Auth::user();
        
        if ($user->role_id == 3) {
            if ($classroom->teacher_id !== Auth::id()) {
                abort(403, 'Unauthorized access to this classroom.');
            }
            
            $assignments = \App\Models\Assignment::where('class_id', $classroom->id)
                ->with('material')
                ->orderBy('due_date', 'asc')
                ->get();

            // Get enrolled students with their submission statistics
            $students = \App\Models\User::whereHas('student.classrooms', function($query) use ($classroom) {
                    $query->where('class_id', $classroom->id);
                })
                ->with(['student' => function($query) use ($classroom) {
                    $query->with(['classrooms' => function($q) use ($classroom) {
                        $q->where('class_id', $classroom->id);
                    }]);
                }])
                ->withCount([
                    'assignmentSubmissions as total_submissions' => function($query) use ($classroom) {
                        $query->whereHas('assignment', function($q) use ($classroom) {
                            $q->where('class_id', $classroom->id);
                        });
                    },
                    'assignmentSubmissions as graded_submissions' => function($query) use ($classroom) {
                        $query->where('status', 'graded')
                            ->whereHas('assignment', function($q) use ($classroom) {
                                $q->where('class_id', $classroom->id);
                            });
                    }
                ])
                ->get();

            return view('classroom.show', compact('classroom', 'assignments', 'students'));
        } elseif ($user->role_id == 2) {
            $student = \App\Models\Student::find(Auth::id());
            if (!$student || !$student->classrooms()->where('class_id', $classroom->id)->exists()) {
                abort(403, 'You are not enrolled in this classroom.');
            }
            
            $classroom->load('teacher');
            $assignments = \App\Models\Assignment::where('class_id', $classroom->id)
                ->with(['material'])
                ->orderBy('due_date', 'asc')
                ->get();
            
            $submissions = \App\Models\AssignmentSubmission::where('student_id', Auth::id())
                ->whereIn('assignment_id', $assignments->pluck('assignment_id'))
                ->get()
                ->keyBy('assignment_id');
            
            return view('students.classroom-show', compact('classroom', 'assignments', 'submissions'));
        } else {
            abort(403, 'Unauthorized access to this classroom.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
        // Ensure the classroom belongs to the authenticated teacher
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this classroom.');
        }

        return view('classroom.edit', compact('classroom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        // Ensure the classroom belongs to the authenticated teacher
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this classroom.');
        }

        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $classroom->update([
            'class_name' => $validated['class_name'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('classroom.index')->with('success', 'Classroom updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        // Ensure the classroom belongs to the authenticated teacher
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this classroom.');
        }

        $classroom->delete();

        return redirect()->route('classroom.index')->with('success', 'Classroom deleted successfully!');
    }

    /**
     * Regenerate the access code for the classroom.
     */
    public function regenerateAccessCode(Classroom $classroom)
    {
        // Ensure the classroom belongs to the authenticated teacher
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this classroom.');
        }

        // Generate new unique 6-digit access code
        do {
            $accessCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Classroom::where('access_code', $accessCode)->exists());

        $classroom->update([
            'access_code' => $accessCode,
        ]);

        return redirect()->route('classroom.edit', $classroom->id)->with('success', 'Access code regenerated successfully!');
    }
}
