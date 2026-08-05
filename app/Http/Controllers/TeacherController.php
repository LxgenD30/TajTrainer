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
            'title' => 'required|string|in:Ustaz,Ustazah,Sheikh',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $teacher->user;
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture && \Storage::disk('public')->exists($user->profile_picture)) {
                \Storage::disk('public')->delete($user->profile_picture);
            }
            
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

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
            'title' => $validated['title'],
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

            $expectedRecitationDisplay = $this->buildExpectedRecitationDisplay($submission->assignment);
            
            // Note: Score is loaded via custom accessor getScoreAttribute() - no need to eager load

            return view('teachers.grade-submission', compact('submission', 'expectedRecitationDisplay'));
        } catch (\Exception $e) {
            \Log::error('Error loading submission for grading: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to load submission: ' . $e->getMessage()]);
        }
    }

    /**
     * Build a tajweed-formatted expected recitation for the grading page.
     */
    private function buildExpectedRecitationDisplay($assignment)
    {
        $expectedRecitation = (string) ($assignment->expected_recitation ?? '');

        if ($expectedRecitation === '') {
            return '';
        }

        if (str_contains($expectedRecitation, '<tajweed') || str_contains($expectedRecitation, '<span class=end')) {
            return $expectedRecitation;
        }

        $surahNumber = $this->getSurahNumber($assignment->surah ?? '');
        $startVerse = (int) ($assignment->start_verse ?? 0);
        $endVerse = (int) ($assignment->end_verse ?? $startVerse);

        if ($surahNumber && $startVerse > 0) {
            $verseData = $this->fetchQuranVerseRange($surahNumber, $startVerse, $endVerse);

            if (!empty($verseData['arabic_html'])) {
                return $verseData['arabic_html'];
            }

            if (!empty($verseData['arabic_plain'])) {
                return $verseData['arabic_plain'];
            }
        }

        return $expectedRecitation;
    }

    /**
     * Fetch Quran verses from Qurancdn for display on grading pages.
     */
    private function fetchQuranVerseRange($surahNumber, $startVerse, $endVerse = null)
    {
        $endVerse = $endVerse ?? $startVerse;

        try {
            $chapterResponse = \Illuminate\Support\Facades\Http::timeout(15)->get(
                "https://api.qurancdn.com/api/qdc/chapters/{$surahNumber}",
                ['language' => 'en']
            );

            $versesResponse = \Illuminate\Support\Facades\Http::timeout(15)->get(
                "https://api.qurancdn.com/api/qdc/verses/by_chapter/{$surahNumber}",
                [
                    'translations' => 131,
                    'per_page' => 300,
                    'page' => 1,
                    'fields' => 'text_uthmani,text_uthmani_tajweed',
                ]
            );

            if ($chapterResponse->failed() || $versesResponse->failed()) {
                \Log::warning("Qurancdn verse API failed for surah {$surahNumber}", [
                    'chapter_status' => $chapterResponse->status(),
                    'verses_status' => $versesResponse->status(),
                ]);

                return null;
            }

            $chapter = $chapterResponse->json('chapter') ?? [];
            $verses = $versesResponse->json('verses') ?? [];

            if (empty($verses)) {
                return null;
            }

            $selectedVerses = [];
            foreach ($verses as $verse) {
                $verseKey = $verse['verse_key'] ?? '';
                $verseParts = explode(':', $verseKey);
                $verseNumber = (int) ($verseParts[1] ?? 0);

                if ($verseNumber >= $startVerse && $verseNumber <= $endVerse) {
                    $selectedVerses[] = $verse;
                }
            }

            if (empty($selectedVerses)) {
                return null;
            }

            $arabicHtml = [];
            $arabicPlain = [];

            foreach ($selectedVerses as $verse) {
                $tajweedText = $verse['text_uthmani_tajweed'] ?? $verse['text_uthmani'] ?? '';
                $plainText = trim(strip_tags($tajweedText));

                if ($tajweedText !== '') {
                    $arabicHtml[] = $tajweedText;
                }

                if ($plainText !== '') {
                    $arabicPlain[] = $plainText;
                }
            }

            return [
                'surah_number' => (int) $surahNumber,
                'surah_name' => $chapter['name_simple'] ?? '',
                'surah_name_arabic' => $chapter['name_arabic'] ?? '',
                'start_verse' => (int) $startVerse,
                'end_verse' => (int) $endVerse,
                'arabic_html' => implode(' ۝ ', $arabicHtml),
                'arabic_plain' => implode(' ۝ ', $arabicPlain),
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching Qurancdn verses for grading view: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Resolve a surah name or alias to its chapter number.
     */
    private function getSurahNumber($surahName)
    {
        if (is_numeric($surahName)) {
            $value = (int) $surahName;

            if ($value >= 1 && $value <= 114) {
                return $value;
            }
        }

        $surahName = trim((string) $surahName);

        if ($surahName === '') {
            return null;
        }

        if (preg_match('/^(\d{1,3})\b/', $surahName, $matches)) {
            $value = (int) $matches[1];

            if ($value >= 1 && $value <= 114) {
                return $value;
            }
        }

        $surahs = [
            'Al-Faatiha' => 1, 'Al-Baqara' => 2, 'Aal-i-Imraan' => 3, 'An-Nisaa' => 4,
            'Al-Maaida' => 5, 'Al-An\'aam' => 6, 'Al-A\'raaf' => 7, 'Al-Anfaal' => 8,
            'At-Tawba' => 9, 'Yunus' => 10, 'Hud' => 11, 'Yusuf' => 12,
            'Ar-Ra\'d' => 13, 'Ibrahim' => 14, 'Al-Hijr' => 15, 'An-Nahl' => 16,
            'Al-Israa' => 17, 'Al-Kahf' => 18, 'Maryam' => 19, 'Taa-Haa' => 20,
            'Al-Anbiyaa' => 21, 'Al-Hajj' => 22, 'Al-Muminoon' => 23, 'An-Noor' => 24,
            'Al-Furqaan' => 25, 'Ash-Shu\'araa' => 26, 'An-Naml' => 27, 'Al-Qasas' => 28,
            'Al-Ankaboot' => 29, 'Ar-Room' => 30, 'Luqman' => 31, 'As-Sajda' => 32,
            'Al-Ahzaab' => 33, 'Saba' => 34, 'Faatir' => 35, 'Yaseen' => 36,
            'As-Saaffaat' => 37, 'Saad' => 38, 'Az-Zumar' => 39, 'Ghafir' => 40,
            'Fussilat' => 41, 'Ash-Shura' => 42, 'Az-Zukhruf' => 43, 'Ad-Dukhaan' => 44,
            'Al-Jaathiya' => 45, 'Al-Ahqaf' => 46, 'Muhammad' => 47, 'Al-Fath' => 48,
            'Al-Hujuraat' => 49, 'Qaaf' => 50, 'Adh-Dhaariyat' => 51, 'At-Tur' => 52,
            'An-Najm' => 53, 'Al-Qamar' => 54, 'Ar-Rahmaan' => 55, 'Al-Waaqia' => 56,
            'Al-Hadid' => 57, 'Al-Mujaadila' => 58, 'Al-Hashr' => 59, 'Al-Mumtahana' => 60,
            'As-Saff' => 61, 'Al-Jumu\'a' => 62, 'Al-Munaafiqoon' => 63, 'At-Taghaabun' => 64,
            'At-Talaaq' => 65, 'At-Tahrim' => 66, 'Al-Mulk' => 67, 'Al-Qalam' => 68,
            'Al-Haaqqa' => 69, 'Al-Ma\'aarij' => 70, 'Nooh' => 71, 'Al-Jinn' => 72,
            'Al-Muzzammil' => 73, 'Al-Muddaththir' => 74, 'Al-Qiyaama' => 75, 'Al-Insaan' => 76,
            'Al-Mursalaat' => 77, 'An-Naba' => 78, 'An-Naazi\'aat' => 79, 'Abasa' => 80,
            'At-Takwir' => 81, 'Al-Infitaar' => 82, 'Al-Mutaffifin' => 83, 'Al-Inshiqaaq' => 84,
            'Al-Burooj' => 85, 'At-Taariq' => 86, 'Al-A\'laa' => 87, 'Al-Ghaashiya' => 88,
            'Al-Fajr' => 89, 'Al-Balad' => 90, 'Ash-Shams' => 91, 'Al-Lail' => 92,
            'Ad-Dhuhaa' => 93, 'Ash-Sharh' => 94, 'At-Tin' => 95, 'Al-Alaq' => 96,
            'Al-Qadr' => 97, 'Al-Bayyina' => 98, 'Az-Zalzala' => 99, 'Al-Aadiyaat' => 100,
            'Al-Qaari\'a' => 101, 'At-Takaathur' => 102, 'Al-Asr' => 103, 'Al-Humaza' => 104,
            'Al-Fil' => 105, 'Quraish' => 106, 'Al-Maa\'oon' => 107, 'Al-Kawthar' => 108,
            'Al-Kaafiroon' => 109, 'An-Nasr' => 110, 'Al-Masad' => 111, 'Al-Ikhlaas' => 112,
            'Al-Falaq' => 113, 'An-Naas' => 114,
            'Al-Fatiha' => 1, 'Al-Baqarah' => 2, 'Ali Imran' => 3, 'An-Nisa' => 4,
            'Al-Ma\'idah' => 5, 'Al-An\'am' => 6, 'Al-A\'raf' => 7, 'Al-Anfal' => 8,
            'Tawbah' => 9, 'Al-Anbiya' => 21, 'Al-Mu\'minun' => 23, 'An-Nur' => 24,
            'Al-Furqan' => 25, 'Al-Shu\'ara' => 26, 'Al-Ankabut' => 29, 'Ar-Rum' => 30,
            'Al-Ahzab' => 33, 'Fatir' => 35, 'Ya-Sin' => 36, 'Yasin' => 36, 'As-Saffat' => 37,
            'Sad' => 38, 'Zumar' => 39, 'Shura' => 42, 'Dukhan' => 44, 'Jathiya' => 45,
            'Ahqaf' => 46, 'Fath' => 48, 'Hujurat' => 49, 'Qaf' => 50,
            'Dhariyat' => 51, 'Tur' => 52, 'Najm' => 53, 'Rahman' => 55,
            'Waqia' => 56, 'Hadid' => 57, 'Mujadila' => 58, 'Hashr' => 59,
            'Mumtahana' => 60, 'Saff' => 61, 'Jumu\'a' => 62, 'Munafiqun' => 63,
            'Taghabun' => 64, 'Talaq' => 65, 'Tahrim' => 66, 'Mulk' => 67,
            'Qalam' => 68, 'Haaqqa' => 69, 'Ma\'arij' => 70, 'Nuh' => 71,
            'Jinn' => 72, 'Muzzammil' => 73, 'Muddathir' => 74, 'Qiyama' => 75,
            'Insan' => 76, 'Mursalat' => 77, 'Naba' => 78, 'Nazi\'at' => 79,
            'Takwir' => 81, 'Infitar' => 82, 'Mutaffifin' => 83,
            'Inshiqaq' => 84, 'Buruj' => 85, 'Tariq' => 86, 'A\'la' => 87,
            'Ghashiya' => 88, 'Fajr' => 89, 'Balad' => 90, 'Shams' => 91,
            'Lail' => 92, 'Duhaa' => 93, 'Sharh' => 94, 'Tin' => 95,
            'Alaq' => 96, 'Qadr' => 97, 'Bayyina' => 98, 'Zalzala' => 99,
            'Adiyat' => 100, 'Qari\'a' => 101, 'Takathur' => 102, 'Asr' => 103,
            'Humaza' => 104, 'Fil' => 105, 'Ma\'un' => 107,
            'Kawthar' => 108, 'Kafirun' => 109, 'Nasr' => 110, 'Masad' => 111,
            'Ikhlas' => 112, 'Falaq' => 113, 'Nas' => 114,
        ];

        if (isset($surahs[$surahName])) {
            return (int) $surahs[$surahName];
        }

        $normalize = static function (string $value): string {
            $value = strtolower($value);
            $value = preg_replace('/[^a-z0-9]+/', '', $value);

            return $value ?? '';
        };

        $normalizedInput = $normalize($surahName);

        if ($normalizedInput !== '') {
            foreach ($surahs as $name => $number) {
                if ($normalize((string) $name) === $normalizedInput) {
                    return (int) $number;
                }
            }
        }

        return null;
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
