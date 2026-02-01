<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Material;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($classroomId)
    {
        $classroom = Classroom::findOrFail($classroomId);
        
        // Ensure the classroom belongs to the authenticated teacher
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this classroom.');
        }

        // Get all available materials
        $materials = Material::orderBy('created_at', 'desc')->get();

        return view('assignment.create', compact('classroom', 'materials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classrooms,id',
            'material_id' => 'nullable|exists:materials,material_id',
            'surah' => 'required|string',
            'start_verse' => 'required|integer|min:1',
            'end_verse' => 'nullable|integer|min:1',
            'due_date' => 'required|date|after:now',
            'instructions' => 'nullable|string',
            'total_marks' => 'required|integer|min:1',
            'is_voice_submission' => 'required|boolean',
            'tajweed_rules' => 'required|string|in:Madd,Noon Saakin',
        ]);

        // Verify classroom ownership
        $classroom = Classroom::findOrFail($validated['class_id']);
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this classroom.');
        }

        Assignment::create([
            'material_id' => $validated['material_id'] ?? null,
            'class_id' => $validated['class_id'],
            'surah' => $validated['surah'],
            'start_verse' => $validated['start_verse'],
            'end_verse' => $validated['end_verse'],
            'due_date' => $validated['due_date'],
            'instructions' => $validated['instructions'],
            'total_marks' => $validated['total_marks'],
            'is_voice_submission' => $validated['is_voice_submission'],
            'tajweed_rules' => [$validated['tajweed_rules']],
        ]);

        return redirect()->route('classroom.show', $validated['class_id'])
            ->with('success', 'Assignment created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        $classroom = Classroom::findOrFail($assignment->class_id);
        
        // Ensure the assignment belongs to the authenticated teacher's classroom
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this assignment.');
        }

        $assignment->load('material', 'classroom');

        return view('assignment.show', compact('assignment', 'classroom'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment)
    {
        $classroom = Classroom::findOrFail($assignment->class_id);
        
        // Ensure the assignment belongs to the authenticated teacher's classroom
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this assignment.');
        }

        // Get all available materials
        $materials = Material::orderBy('created_at', 'desc')->get();

        return view('assignment.edit', compact('assignment', 'classroom', 'materials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'material_id' => 'nullable|exists:materials,material_id',
            'surah' => 'required|string',
            'start_verse' => 'required|integer|min:1',
            'end_verse' => 'nullable|integer|min:1',
            'due_date' => 'required|date|after:now',
            'instructions' => 'required|string',
            'total_marks' => 'required|integer|min:1',
            'is_voice_submission' => 'required|boolean',
            'tajweed_rules' => 'required|string|in:Madd,Noon Saakin',
        ]);

        $classroom = Classroom::findOrFail($assignment->class_id);
        
        // Ensure the assignment belongs to the authenticated teacher's classroom
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this assignment.');
        }

        $assignment->update([
            'material_id' => $validated['material_id'] ?? null,
            'surah' => $validated['surah'],
            'start_verse' => $validated['start_verse'],
            'end_verse' => $validated['end_verse'],
            'due_date' => $validated['due_date'],
            'instructions' => $validated['instructions'],
            'total_marks' => $validated['total_marks'],
            'is_voice_submission' => $validated['is_voice_submission'],
            'tajweed_rules' => [$validated['tajweed_rules']],
        ]);

        return redirect()->route('assignment.show', $assignment->assignment_id)
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $classroomId = $assignment->class_id;
        $classroom = Classroom::findOrFail($classroomId);
        
        // Ensure the assignment belongs to the authenticated teacher's classroom
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this assignment.');
        }

        // Delete associated material file if exists
        if ($assignment->material && $assignment->material->file_path) {
            \Storage::disk('public')->delete($assignment->material->file_path);
        }

        $assignment->delete();

        return redirect()->route('classroom.show', $classroomId)
            ->with('success', 'Assignment deleted successfully!');
    }
}
