<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index()
    {
        return redirect()->route('home');
    }

    public function create()
    {
        return view('teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'biodata' => 'nullable|string',
            'title' => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 3,
            'phone' => $validated['phone'] ?? null,
        ]);

        Teacher::create([
            'id' => $user->id,
            'name' => $validated['name'],
            'biodata' => $validated['biodata'] ?? null,
            'title' => $validated['title'] ?? null,
        ]);

        return redirect()->route('teachers.index')->with('success', 'Teacher profile created successfully!');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load('user', 'classrooms');
        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
            'phone' => 'nullable|string|max:20',
            'biodata' => 'nullable|string',
            'title' => 'nullable|string|max:100',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user = $teacher->user;
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        $teacher->update([
            'name' => $validated['name'],
            'biodata' => $validated['biodata'] ?? null,
            'title' => $validated['title'] ?? null,
        ]);

        return redirect()->route('teachers.show', $teacher)->with('success', 'Profile updated successfully!');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->user->delete();
        return redirect()->route('teachers.index')->with('success', 'Teacher profile deleted successfully!');
    }

    /**
     * Display all submissions from a specific student in a classroom
     */
    public function studentSubmissions($classroomId, $studentId)
    {
        try {
            \Log::info("=== STUDENT SUBMISSIONS DEBUG START ===");
            \Log::info("Classroom ID: {$classroomId}, Student ID: {$studentId}");
            
            $classroom = \App\Models\Classroom::findOrFail($classroomId);
            \Log::info("Classroom loaded: {$classroom->name}");
            
            // Verify teacher owns this classroom
            if ($classroom->teacher_id !== Auth::id()) {
                abort(403, 'Unauthorized access to this classroom.');
            }

            $student = \App\Models\User::findOrFail($studentId);
            \Log::info("Student loaded: {$student->name}");
            
            // Get all assignments for this classroom
            $assignments = \App\Models\Assignment::where('class_id', $classroomId)
                ->with('material')
                ->orderBy('due_date', 'desc')
                ->get();
            \Log::info("Assignments loaded: " . $assignments->count());

            // Get all submissions from this student for this classroom's assignments
            \Log::info("Fetching submissions...");
            $submissions = \App\Models\AssignmentSubmission::where('student_id', $studentId)
                ->whereIn('assignment_id', $assignments->pluck('assignment_id'))
                ->with(['assignment'])
                ->orderBy('created_at', 'desc')
                ->get();
            \Log::info("Submissions loaded: " . $submissions->count());
            
            // Test each submission's score accessor
            foreach ($submissions as $index => $submission) {
                \Log::info("Submission {$index}: ID={$submission->id}, Assignment={$submission->assignment_id}, Student={$submission->student_id}");
                try {
                    $score = $submission->score;
                    \Log::info("  Score accessor worked: " . ($score ? "Score ID {$score->score_id}" : "No score"));
                } catch (\Exception $e) {
                    \Log::error("  Score accessor FAILED: " . $e->getMessage());
                    \Log::error("  Stack trace: " . $e->getTraceAsString());
                }
            }
            
            \Log::info("=== STUDENT SUBMISSIONS DEBUG END ===");

            return view('teachers.student-submissions', compact('classroom', 'student', 'submissions', 'assignments'));
            
        } catch (\Exception $e) {
            \Log::error("=== STUDENT SUBMISSIONS ERROR ===");
            \Log::error("Error: " . $e->getMessage());
            \Log::error("File: " . $e->getFile() . " Line: " . $e->getLine());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to load submission: ' . $e->getMessage()]);
        }
    }

    /**
     * Show grading form for a specific submission
     */
    public function gradeSubmission($submissionId)
    {
        try {
            \Log::info("=== GRADE SUBMISSION DEBUG START ===");
            \Log::info("Loading submission ID: {$submissionId}");
            
            $submission = \App\Models\AssignmentSubmission::with([
                'assignment.classroom', 
                'student.user'
                // Note: 'score' is loaded via custom accessor, not relationship
            ])->findOrFail($submissionId);
            
            \Log::info("Submission loaded: ID={$submission->id}, Assignment={$submission->assignment_id}, Student={$submission->student_id}");
            \Log::info("Testing score accessor...");
            
            try {
                $testScore = $submission->score;
                \Log::info("Score accessor works: " . ($testScore ? "Score ID {$testScore->score_id}, Score: {$testScore->score}" : "No score"));
            } catch (\Exception $e) {
                \Log::error("Score accessor FAILED: " . $e->getMessage());
            }
            
            \Log::info("=== GRADE SUBMISSION DEBUG END ===");

            // Verify teacher owns the classroom
            if (!$submission->assignment || !$submission->assignment->classroom) {
                \Log::error('Missing assignment or classroom for submission: ' . $submissionId);
                return back()->withErrors(['error' => 'Assignment or classroom not found for this submission.']);
            }

            if ($submission->assignment->classroom->teacher_id !== Auth::id()) {
                abort(403, 'Unauthorized access to this submission.');
            }

            // Verify audio file exists if audio_file_path is set
            if ($submission->audio_file_path) {
                if (!\Storage::disk('public')->exists($submission->audio_file_path)) {
                    \Log::warning('Audio file not found for submission ' . $submissionId . ': ' . $submission->audio_file_path);
                    // Don't fail - just log it, the view will handle it
                }
            }
            
            // Note: Score is loaded via custom accessor getScoreAttribute() - no need to eager load

            return view('teachers.grade-submission', compact('submission'));
        } catch (\Exception $e) {
            \Log::error('Error loading submission for grading: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to load submission: ' . $e->getMessage()]);
        }
    }

    /**
     * Update grade for a submission
     */
    public function updateGrade(Request $request, $submissionId)
    {
        $submission = \App\Models\AssignmentSubmission::with('assignment.classroom')
            ->findOrFail($submissionId);

        // Verify teacher owns the classroom
        if ($submission->assignment->classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this submission.');
        }

        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:' . $submission->assignment->total_marks,
            'feedback' => 'required|string|min:10',
        ]);

        // Update or create score
        \App\Models\Score::updateOrCreate(
            [
                'user_id' => $submission->student_id,
                'assignment_id' => $submission->assignment_id,
            ],
            [
                'score' => $validated['score'],
                'feedback' => $validated['feedback'],
            ]
        );

        // Update submission status to graded after teacher reviews
        $submission->update(['status' => 'graded']);

        return redirect()
            ->route('teacher.student.submissions', [
                'classroom' => $submission->assignment->class_id,
                'student' => $submission->student_id
            ])
            ->with('success', 'Submission graded successfully!');
    }
    
    /**
     * Show class progress dashboard
     */
    public function classProgress($classroomId)
    {
        $classroom = \App\Models\Classroom::with(['students', 'teacher'])
            ->findOrFail($classroomId);
        
        // Verify teacher owns the classroom
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this classroom.');
        }
        
        $progressTracker = new \App\Services\ProgressTracker();
        
        // Get class-wide statistics
        $classStats = $progressTracker->getClassStats($classroomId);
        
        // Get individual student progress
        $studentsProgress = [];
        foreach ($classroom->students as $student) {
            $studentData = $progressTracker->getUserProgress($student->id, 30);
            $weaknesses = $progressTracker->getTopWeaknesses($student->id, 3);
            
            $studentsProgress[] = [
                'student' => $student,
                'progress' => $studentData,
                'top_weaknesses' => $weaknesses,
            ];
        }
        
        return view('teachers.class_progress', compact(
            'classroom',
            'classStats',
            'studentsProgress'
        ));
    }
}
