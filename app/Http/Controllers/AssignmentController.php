<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
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

        // Get all available materials with their items
        $materials = Material::with('items')->orderBy('created_at', 'desc')->get();

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
            'surah_number' => 'required|integer|min:1|max:114',
            'start_verse' => 'required|integer|min:1',
            'end_verse' => 'nullable|integer|min:1',
            'due_date' => 'required|date|after:now',
            'instructions' => 'nullable|string',
            'total_marks' => 'required|integer|min:1',
            'is_voice_submission' => 'required|boolean',
            'tajweed_rules' => 'required|string|in:Madd,Idgham Bi Ghunnah,Idgham Billa Ghunnah',
        ]);

        // Verify classroom ownership
        $classroom = Classroom::findOrFail($validated['class_id']);
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this classroom.');
        }

        // Fetch expected recitation text and reference audio from Quran API
        $expectedRecitation = $this->getQuranText($validated['surah_number'], $validated['start_verse'], $validated['end_verse']);
        $referenceAudioUrl = $this->getReferenceAudioUrl($validated['surah_number'], $validated['start_verse'], $validated['end_verse']);

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
            'expected_recitation' => $expectedRecitation,
            'reference_audio_url' => $referenceAudioUrl,
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
        $user = Auth::user();
        
        // Check if user is the teacher OR a student enrolled in this classroom
        $isTeacher = $classroom->teacher_id === Auth::id();
        $isEnrolledStudent = $user->role_id == 2 && $classroom->students()->where('user_id', Auth::id())->exists();
        
        if (!$isTeacher && !$isEnrolledStudent) {
            abort(403, 'Unauthorized access to this assignment. You must be enrolled in this classroom.');
        }

        $assignment->load('material', 'classroom');
        
        // If student, check if they have a submission
        $submission = null;
        $submissions = collect();
        
        if ($isEnrolledStudent) {
            $submission = AssignmentSubmission::where('assignment_id', $assignment->assignment_id)
                ->where('student_id', Auth::id())
                ->first();
            // Note: Score is loaded via custom accessor getScoreAttribute() - no need to eager load
        }
        
        // If teacher, load all submissions for this assignment
        if ($isTeacher) {
            $submissions = AssignmentSubmission::where('assignment_id', $assignment->assignment_id)
                ->with(['student'])
                ->orderBy('submitted_at', 'desc')
                ->get();
            
            // Get all students enrolled in this classroom (students() returns User models)
            $allStudents = $classroom->students()->get();
            $submittedStudentIds = $submissions->pluck('student_id')->toArray();
            $notSubmittedStudents = $allStudents->filter(function($student) use ($submittedStudentIds) {
                return !in_array($student->id, $submittedStudentIds);
            });
        } else {
            $allStudents = collect();
            $notSubmittedStudents = collect();
        }

        return view('assignment.show', compact('assignment', 'classroom', 'submission', 'submissions', 'allStudents', 'notSubmittedStudents'));
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

        // Get all available materials with their items
        $materials = Material::with('items')->orderBy('created_at', 'desc')->get();

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
            'surah_number' => 'required|integer|min:1|max:114',
            'start_verse' => 'required|integer|min:1',
            'end_verse' => 'nullable|integer|min:1',
            'due_date' => 'required|date|after:now',
            'instructions' => 'required|string',
            'total_marks' => 'required|integer|min:1',
            'is_voice_submission' => 'required|boolean',
            'tajweed_rules' => 'required|string|in:Madd,Idgham Bi Ghunnah,Idgham Billa Ghunnah',
        ]);

        $classroom = Classroom::findOrFail($assignment->class_id);
        
        // Ensure the assignment belongs to the authenticated teacher's classroom
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this assignment.');
        }

        // Fetch updated expected recitation text and reference audio from Quran API
        $expectedRecitation = $this->getQuranText($validated['surah_number'], $validated['start_verse'], $validated['end_verse']);
        $referenceAudioUrl = $this->getReferenceAudioUrl($validated['surah_number'], $validated['start_verse'], $validated['end_verse']);

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
            'expected_recitation' => $expectedRecitation,
            'reference_audio_url' => $referenceAudioUrl,
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

    /**
     * Get Quran text from API
     */
    private function getQuranText($surahNumber, $startVerse, $endVerse)
    {
        $verses = [];
        
        for ($verse = $startVerse; $verse <= ($endVerse ?? $startVerse); $verse++) {
            $url = "https://api.alquran.cloud/v1/ayah/{$surahNumber}:{$verse}/quran-uthmani";
            
            try {
                $response = @file_get_contents($url);
                if ($response !== false) {
                    $data = json_decode($response, true);
                    if ($data['code'] == 200 && isset($data['data']['text'])) {
                        $verses[] = $data['data']['text'];
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error fetching Quran text: " . $e->getMessage());
            }
        }
        
        // Join verses with Arabic verse separator ۝
        return implode(" ۝ ", $verses);
    }

    /**
     * Get reference audio - downloads and concatenates multiple verses into one audio file
     */
    private function getReferenceAudioUrl($surahNumber, $startVerse, $endVerse)
    {
        $audioUrls = [];
        
        // Fetch audio URLs for each verse in the range
        for ($verse = $startVerse; $verse <= ($endVerse ?? $startVerse); $verse++) {
            $url = "https://api.alquran.cloud/v1/ayah/{$surahNumber}:{$verse}/ar.alafasy";
            
            try {
                $response = @file_get_contents($url);
                if ($response !== false) {
                    $data = json_decode($response, true);
                    if ($data['code'] == 200 && isset($data['data']['audio'])) {
                        $audioUrls[] = $data['data']['audio'];
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error fetching reference audio for verse {$verse}: " . $e->getMessage());
            }
        }
        
        if (count($audioUrls) === 0) {
            return null;
        }
        
        // If single verse, just return the URL directly
        if (count($audioUrls) === 1) {
            return $audioUrls[0];
        }
        
        // Multiple verses: download, concatenate with ffmpeg, and save
        return $this->concatenateAudioFiles($audioUrls, $surahNumber, $startVerse, $endVerse);
    }
    
    /**
     * Download audio files, concatenate them using ffmpeg, and save as single file
     */
    private function concatenateAudioFiles($audioUrls, $surahNumber, $startVerse, $endVerse)
    {
        try {
            // Create temporary directory for downloads
            $tempDir = storage_path('app/temp_audio');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            // Create references directory
            $referencesDir = storage_path('app/public/references');
            if (!file_exists($referencesDir)) {
                mkdir($referencesDir, 0755, true);
            }
            
            $tempFiles = [];
            
            // Download each verse audio
            foreach ($audioUrls as $index => $url) {
                $tempFile = $tempDir . '/verse_' . $index . '.mp3';
                $audioContent = @file_get_contents($url);
                if ($audioContent !== false) {
                    file_put_contents($tempFile, $audioContent);
                    $tempFiles[] = $tempFile;
                }
            }
            
            if (empty($tempFiles)) {
                \Log::error("Failed to download any audio files for concatenation");
                return null;
            }
            
            // Generate output filename
            $outputFilename = "surah_{$surahNumber}_verses_{$startVerse}_{$endVerse}_" . time() . ".mp3";
            $outputPath = $referencesDir . '/' . $outputFilename;
            
            // Create concat file list for ffmpeg
            $concatListFile = $tempDir . '/concat_list.txt';
            $concatList = '';
            foreach ($tempFiles as $file) {
                $concatList .= "file '" . $file . "'\n";
            }
            file_put_contents($concatListFile, $concatList);
            
            // Run ffmpeg to concatenate
            $command = "ffmpeg -f concat -safe 0 -i " . escapeshellarg($concatListFile) . " -c copy " . escapeshellarg($outputPath) . " 2>&1";
            $output = shell_exec($command);
            
            // Clean up temp files
            foreach ($tempFiles as $file) {
                @unlink($file);
            }
            @unlink($concatListFile);
            
            // Check if output file was created
            if (file_exists($outputPath)) {
                \Log::info("Successfully concatenated audio: " . $outputFilename);
                return 'references/' . $outputFilename;
            } else {
                \Log::error("ffmpeg concatenation failed", ['output' => $output]);
                return null;
            }
            
        } catch (\Exception $e) {
            \Log::error("Error concatenating audio files: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Surah number from name
     */
    private function getSurahNumber($surahName)
    {
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
            'Al-Fil' => 105, 'Quraish' => 106, 'Al-Maa\'un' => 107, 'Al-Kawthar' => 108,
            'Al-Kaafiroon' => 109, 'An-Nasr' => 110, 'Al-Masad' => 111, 'Al-Ikhlaas' => 112,
            'Al-Falaq' => 113, 'An-Naas' => 114
        ];
        
        return $surahs[$surahName] ?? 1;
    }
}
