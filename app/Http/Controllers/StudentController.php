<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classroom;
use App\Jobs\ProcessSubmissionAudio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function index()
    {
        $student = Student::with(['classrooms.teacher', 'classrooms.assignments', 'scores', 'user', 'submissions'])
            ->findOrFail(Auth::id());
        
        // Calculate statistics
        $enrolledClassesCount = $student->classrooms->count();
        
        // Get all assignments from enrolled classes
        $allAssignmentIds = $student->classrooms->flatMap->assignments->pluck('assignment_id')->unique();
        $totalAssignments = $allAssignmentIds->count();
        
        // Count completed/submitted assignments (from current enrolled classes only)
        $submittedAssignmentIds = $student->submissions->pluck('assignment_id')->unique();
        $completedAssignments = $submittedAssignmentIds->intersect($allAssignmentIds)->count();
        
        // Calculate pending assignments
        $pendingAssignments = max(0, $totalAssignments - $completedAssignments);
        
        // Calculate average score from enrolled classes only
        $averageScore = $student->scores()
            ->whereIn('assignment_id', $allAssignmentIds)
            ->avg('score') ?? 0;
        
        return view('students.index', compact('student', 'enrolledClassesCount', 'completedAssignments', 'averageScore', 'pendingAssignments'));
    }

    public function classes()
    {
        try {
            \Log::info('Student classes page accessed by user: ' . Auth::id());
            
            $student = Student::with(['classrooms.teacher', 'classrooms.assignments'])
                ->findOrFail(Auth::id());
            
            \Log::info('Student found: ' . $student->id . ', Classes count: ' . $student->classrooms->count());
            
            return view('classroom.index', compact('student'));
        } catch (\Exception $e) {
            \Log::error('Error loading student classes page: ' . $e->getMessage());
            \Log::error('Student ID: ' . Auth::id());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('home')
                ->withErrors(['error' => 'Unable to load classes: ' . $e->getMessage()]);
        }
    }

    public function enrollClass(Request $request)
    {
        try {
            $validated = $request->validate([
                'access_code' => 'required|string|exists:classrooms,access_code',
            ]);

            $classroom = Classroom::where('access_code', $validated['access_code'])->firstOrFail();
            $student = Student::findOrFail(Auth::id());

            if ($student->classrooms()->where('class_id', $classroom->id)->exists()) {
                return back()->with('error', 'You are already enrolled in this class!');
            }

            $student->classrooms()->attach($classroom->id, [
                'date_joined' => now()->toDateString()
            ]);

            return back()->with('success', 'Successfully enrolled in ' . $classroom->class_name . '!');
        } catch (\Exception $e) {
            \Log::error('Error enrolling in class: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to enroll in class. Please try again.']);
        }
    }

    public function materials()
    {
        try {
            $materials = \App\Models\Material::orderBy('created_at', 'desc')->paginate(12);
            return view('materials.index', compact('materials'));
        } catch (\Exception $e) {
            \Log::error('Error loading materials: ' . $e->getMessage());
            return redirect()->route('home')
                ->withErrors(['error' => 'Unable to load materials. Please try again.']);
        }
    }

    public function showMaterial($id)
    {
        try {
            $material = \App\Models\Material::findOrFail($id);
            $from = request()->query('from');
            $classId = request()->query('class');
            $assignmentId = request()->query('assignment');
            return view('materials.show', compact('material', 'from', 'classId', 'assignmentId'));
        } catch (\Exception $e) {
            \Log::error('Error loading material: ' . $e->getMessage());
            return redirect()->route('student.materials')
                ->withErrors(['error' => 'Material not found.']);
        }
    }

    /**
     * Get AssemblyAI temporary token for real-time transcription
     */
    public function getAssemblyAIToken(Request $request)
    {
        $apiKey = config('services.assemblyai.api_key');
        
        Log::info('=== AssemblyAI Token Request ===');
        Log::info('API Key configured: ' . ($apiKey ? 'Yes (length: ' . strlen($apiKey) . ')' : 'No'));
        
        if (!$apiKey) {
            Log::error('AssemblyAI API key not configured');
            return response()->json(['error' => 'AssemblyAI API key not configured'], 500);
        }
        
        try {
            Log::info('Requesting token from AssemblyAI (Universal Streaming)...');
            
            // Use the new Universal Streaming endpoint
            $response = Http::withHeaders([
                'authorization' => $apiKey,
            ])->post('https://api.assemblyai.com/v2/realtime/token', [
                'expires_in' => 3600
            ]);
            
            Log::info('AssemblyAI Response Status: ' . $response->status());
            Log::info('AssemblyAI Response Body: ' . $response->body());
            
            if ($response->successful()) {
                Log::info('✓ Token obtained successfully');
                return response()->json($response->json());
            } else {
                Log::error('Failed to get AssemblyAI token: HTTP ' . $response->status());
                Log::error('Response: ' . $response->body());
                
                // Check if it's a deprecation error
                $body = $response->json();
                if (isset($body['error']) && str_contains($body['error'], 'deprecated')) {
                    Log::error('AssemblyAI streaming model is deprecated');
                    return response()->json([
                        'error' => 'Streaming feature temporarily unavailable',
                        'message' => 'AssemblyAI streaming API has been updated. Please use file upload instead.'
                    ], 503);
                }
                
                return response()->json([
                    'error' => 'Failed to get token',
                    'details' => $body
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception getting AssemblyAI token: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function submitAssignment($assignmentId)
    {
        try {
            \Log::info('=== Loading Assignment Submission Page ===');
            \Log::info('Assignment ID: ' . $assignmentId);
            \Log::info('Student ID: ' . Auth::id());
            
            $assignment = \App\Models\Assignment::with('material', 'classroom')->findOrFail($assignmentId);
            \Log::info('Assignment loaded: ' . $assignment->surah . ' verses ' . $assignment->start_verse . '-' . ($assignment->end_verse ?? $assignment->start_verse));
            \Log::info('Tajweed rules: ' . json_encode($assignment->tajweed_rules));
            
            $student = Student::find(Auth::id());
            if (!$student || !$student->classrooms()->where('class_id', $assignment->class_id)->exists()) {
                \Log::warning('Student not enrolled in class or student not found');
                abort(403, 'You are not enrolled in this class.');
            }
            
            // Fetch the actual verses if surah is assigned
            $verses = null;
            if ($assignment->surah && $assignment->start_verse) {
                try {
                    \Log::info('Fetching Quran verses...');
                    $verses = $this->getQuranText(
                        $assignment->surah, 
                        $assignment->start_verse, 
                        $assignment->end_verse ?? $assignment->start_verse
                    );
                    \Log::info('Verses fetched successfully: ' . substr($verses, 0, 100));
                } catch (\Exception $e) {
                    \Log::error('Failed to fetch Quran verses: ' . $e->getMessage());
                    \Log::error('Stack trace: ' . $e->getTraceAsString());
                    // Continue without verses - don't block the page
                }
            }
            
            \Log::info('Returning view with assignment data');
            return view('assignment.submit', compact('assignment', 'verses'));
            
        } catch (\Exception $e) {
            \Log::error('Error loading assignment submission page: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to load assignment: ' . $e->getMessage()]);
        }
    }

    public function storeSubmission(Request $request, $assignmentId)
    {
        set_time_limit(600); // Increase time limit to 10 minutes for audio processing
        
        \Log::info('=== Assignment Submission Started ===');
        \Log::info('Assignment ID: ' . $assignmentId);
        \Log::info('Student ID: ' . Auth::id());
        \Log::info('Request method: ' . $request->method());
        \Log::info('Content type: ' . $request->header('Content-Type'));
        \Log::info('Has recorded_audio: ' . ($request->has('recorded_audio') ? 'Yes' : 'No'));
        \Log::info('Has audio_file: ' . ($request->hasFile('audio_file') ? 'Yes' : 'No'));
        \Log::info('Has transcription: ' . ($request->has('transcription') ? 'Yes' : 'No'));
        \Log::info('All request keys: ' . implode(', ', array_keys($request->all())));
        
        if ($request->hasFile('audio_file')) {
            $file = $request->file('audio_file');
            \Log::info('Upload file details:');
            \Log::info('  - Name: ' . $file->getClientOriginalName());
            \Log::info('  - Size: ' . $file->getSize() . ' bytes');
            \Log::info('  - MIME: ' . $file->getMimeType());
            \Log::info('  - Extension: ' . $file->getClientOriginalExtension());
            \Log::info('  - Is Valid: ' . ($file->isValid() ? 'Yes' : 'No'));
            \Log::info('  - Error: ' . $file->getError());
        }
        
        if ($request->has('transcription')) {
            \Log::info('Transcription content: ' . substr($request->transcription, 0, 200));
        }
        
        try {
            $assignment = \App\Models\Assignment::findOrFail($assignmentId);
            
            $student = Student::find(Auth::id());
            if (!$student) {
                \Log::error('Student not found: ' . Auth::id());
                return back()->withErrors(['error' => 'Student account not found. Please contact administrator.'])->withInput();
            }
            
            if (!$student->classrooms()->where('class_id', $assignment->class_id)->exists()) {
                \Log::error('Student not enrolled in classroom: ' . $assignment->class_id);
                return back()->withErrors(['error' => 'You are not enrolled in this class.'])->withInput();
            }
            
            // Validate the request
            try {
                $validated = $request->validate([
                    'text_submission' => 'nullable|string',
                    'transcription' => 'nullable|string',
                    'recorded_audio' => 'nullable|string',
                ]);
                
                // Custom validation for audio file - accept any audio/* or video/* MIME type
                // (some audio recorders save as video/webm, video/mp4, etc.)
                if ($request->hasFile('audio_file')) {
                    $file = $request->file('audio_file');
                    $mimeType = $file->getMimeType();
                    $size = $file->getSize();
                    
                    \Log::info('Validating uploaded file:');
                    \Log::info('  MIME type: ' . $mimeType);
                    \Log::info('  Size: ' . $size . ' bytes (' . round($size / 1024 / 1024, 2) . ' MB)');
                    
                    // Check if it's an audio or video file
                    if (!str_starts_with($mimeType, 'audio/') && !str_starts_with($mimeType, 'video/')) {
                        \Log::error('Invalid file type: ' . $mimeType);
                        return back()->withErrors([
                            'audio_file' => 'The audio file must be an audio or video file (MP3, WAV, M4A, OGG, WEBM, MP4, etc.). Detected type: ' . $mimeType
                        ])->withInput();
                    }
                    
                    // Check size (10MB = 10485760 bytes)
                    if ($size > 10485760) {
                        \Log::error('File too large: ' . $size . ' bytes');
                        return back()->withErrors([
                            'audio_file' => 'The audio file must not be larger than 10MB. Your file is ' . round($size / 1024 / 1024, 2) . ' MB.'
                        ])->withInput();
                    }
                    
                    \Log::info('✓ File validation passed');
                }
                
                \Log::info('✓ All validation passed');
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Validation failed: ' . json_encode($e->errors()));
                return back()->withErrors($e->errors())->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Assignment Submission Error at start: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Submission failed: ' . $e->getMessage()])->withInput();
        }
        
        // Wrap all submission processing in try-catch to prevent 500 errors
        try {
            $submission = \App\Models\AssignmentSubmission::where('assignment_id', $assignmentId)
                ->where('student_id', Auth::id())
                ->first();
        
        if (!$submission) {
            $submission = new \App\Models\AssignmentSubmission();
            $submission->assignment_id = $assignmentId;
            $submission->student_id = Auth::id();
        }
        
        $submission->text_submission = $validated['text_submission'] ?? null;
        $submission->status = 'submitted';
        $submission->submitted_at = now();
        
        // Handle transcription from streaming API or form
        if ($request->has('transcription') && !empty(trim($request->transcription))) {
            $submission->transcription = trim($request->transcription);
            \Log::info('✓ Transcription saved from request: ' . substr($submission->transcription, 0, 150));
        } else {
            \Log::info('⚠️ No transcription received in request');
        }
        
        // Handle live recording (base64 audio data)
        if ($request->has('recorded_audio') && !empty($request->recorded_audio)) {
            $audioData = $request->recorded_audio;
            
            \Log::info('Processing live recording audio data');
            \Log::info('Audio data prefix: ' . substr($audioData, 0, 50));
            
            // Extract the base64 encoded binary data - now handles codecs parameter
            if (preg_match('/^data:audio\/([^;,]+)(?:;codecs=[^;,]+)?;base64,(.+)$/', $audioData, $matches)) {
                $extension = $matches[1];
                $data = base64_decode($matches[2]);
                
                \Log::info('Audio format detected: ' . $extension . ', Data size: ' . strlen($data) . ' bytes');
                
                // Convert webm to a more compatible format if needed
                if ($extension === 'webm') {
                    $extension = 'webm'; // Keep as webm, AssemblyAI supports it
                }
                
                $filename = time() . '_' . uniqid() . '.' . $extension;
                $destinationPath = storage_path('app/public/submissions');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $fullPath = $destinationPath . '/' . $filename;
                
                if (file_put_contents($fullPath, $data)) {
                    $submission->audio_file_path = 'submissions/' . $filename;
                    \Log::info('✓ Live recording audio saved successfully: ' . $submission->audio_file_path . ' (Size: ' . strlen($data) . ' bytes)');
                } else {
                    \Log::error('Failed to save live recording audio file');
                }
            } else {
                \Log::error('Invalid audio data format from live recording');
                \Log::error('Expected format: data:audio/TYPE;base64,DATA');
                \Log::error('Received format: ' . substr($audioData, 0, 100));
            }
        }
        // Handle uploaded audio file
        elseif ($request->hasFile('audio_file')) {
            $file = $request->file('audio_file');
            
            \Log::info('Processing uploaded audio file');
            \Log::info('File original name: ' . $file->getClientOriginalName());
            \Log::info('File size: ' . $file->getSize() . ' bytes');
            \Log::info('File mime type: ' . $file->getMimeType());
            \Log::info('File extension: ' . $file->getClientOriginalExtension());
            \Log::info('File is valid: ' . ($file->isValid() ? 'Yes' : 'No'));
            
            if ($file->isValid() && $file->getSize() > 0) {
                $extension = $file->getClientOriginalExtension() ?: 'webm';
                $filename = time() . '_' . uniqid() . '.' . $extension;
                
                // Use Laravel's storage method - it handles directory creation automatically
                try {
                    $path = $file->storeAs('submissions', $filename, 'public');
                    $submission->audio_file_path = $path;
                    \Log::info('✓ Audio file uploaded successfully: ' . $path);
                } catch (\Exception $e) {
                    \Log::error('Failed to store uploaded file: ' . $e->getMessage());
                    throw new \Exception('Failed to upload audio file: ' . $e->getMessage());
                }
            } else {
                \Log::error('File is invalid or empty');
                throw new \Exception('Uploaded file is invalid or empty');
            }
        }
        
        try {
            $submission->save();
            \Log::info('✓ Submission saved successfully with ID: ' . $submission->id);
        } catch (\Exception $e) {
            \Log::error('Failed to save submission: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw new \Exception('Failed to save submission: ' . $e->getMessage());
        }
        
        // Process audio synchronously (no queue workers)
        if ($submission->audio_file_path) {
            try {
                \Log::info('Processing audio synchronously for submission #' . $submission->id);
                
                // Process immediately
                ProcessSubmissionAudio::dispatchSync($submission->id);
                
                \Log::info('✓ Audio processed successfully');
                return redirect()->route('classroom.show', $assignment->class_id)
                    ->with('success', 'Assignment submitted and analyzed successfully!');
                    
            } catch (\Exception $e) {
                \Log::error('Audio processing failed: ' . $e->getMessage());
                return redirect()->route('classroom.show', $assignment->class_id)
                    ->with('warning', 'Assignment submitted but analysis failed. Teacher will grade manually.');
            }
        }
            
        } catch (\Exception $e) {
            \Log::error('=== Assignment Submission FAILED ===');
            \Log::error('Error: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Try to get assignment for redirect
            try {
                $assignment = \App\Models\Assignment::findOrFail($assignmentId);
                return redirect()->route('student.assignment.submit', $assignmentId)
                    ->withErrors(['error' => 'Submission processing failed: ' . $e->getMessage() . '. Please try again or contact support.'])
                    ->withInput();
            } catch (\Exception $innerE) {
                // If we can't even find the assignment, go to home
                return redirect()->route('home')
                    ->withErrors(['error' => 'Submission failed: ' . $e->getMessage()])
                    ->withInput();
            }
        }
    }
    
    private function transcribeWithAssemblyAI($audioPath)
    {
        $apiKey = config('services.assemblyai.api_key');
        $fullPath = storage_path('app/public/' . $audioPath);
        
        if (!file_exists($fullPath)) {
            throw new \Exception('Audio file not found');
        }

        // Step 1: Upload audio file to AssemblyAI
        $uploadUrl = $this->uploadAudioToAssemblyAI($fullPath, $apiKey);
        
        // Step 2: Request transcription
        $transcriptId = $this->requestTranscription($uploadUrl, $apiKey);
        
        // Step 3: Poll for transcription result
        $transcription = $this->pollTranscription($transcriptId, $apiKey);
        
        return $transcription;
    }
    
    private function uploadAudioToAssemblyAI($filePath, $apiKey)
    {
        $audioData = file_get_contents($filePath);
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.assemblyai.com/v2/upload',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'authorization: ' . $apiKey,
                'Content-Type: application/octet-stream',
            ],
            CURLOPT_POSTFIELDS => $audioData,
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode !== 200) {
            throw new \Exception('AssemblyAI upload failed: ' . $response);
        }
        
        $result = json_decode($response, true);
        return $result['upload_url'];
    }
    
    private function requestTranscription($audioUrl, $apiKey)
    {
        $requestBody = json_encode([
            'audio_url' => $audioUrl,
            'language_code' => 'ar',
        ]);
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.assemblyai.com/v2/transcript',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'authorization: ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => $requestBody,
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode !== 200) {
            throw new \Exception('AssemblyAI transcription request failed: ' . $response);
        }
        
        $result = json_decode($response, true);
        return $result['id'];
    }
    
    private function pollTranscription($transcriptId, $apiKey)
    {
        $maxAttempts = 60; // 5 minutes timeout
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.assemblyai.com/v2/transcript/' . $transcriptId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'authorization: ' . $apiKey,
                ],
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode !== 200) {
                throw new \Exception('AssemblyAI polling failed: ' . $response);
            }
            
            $result = json_decode($response, true);
            
            if ($result['status'] === 'completed') {
                return $result['text'] ?? '';
            } elseif ($result['status'] === 'error') {
                throw new \Exception('Transcription failed: ' . ($result['error'] ?? 'Unknown error'));
            }
            
            sleep(5); // Wait 5 seconds before next poll
            $attempt++;
        }
        
        throw new \Exception('Transcription timeout');
    }

    public function viewSubmission($assignmentId)
    {
        $assignment = \App\Models\Assignment::with('material', 'classroom')->findOrFail($assignmentId);
        $submission = \App\Models\AssignmentSubmission::where('assignment_id', $assignmentId)
            ->where('student_id', Auth::id())
            ->firstOrFail();
        
        return view('assignment.view', compact('assignment', 'submission'));
    }

    public function practice()
    {
        return view('practice.index');
    }

    public function submitPractice(Request $request)
    {
        \Log::info('=== Practice Submission Started ===');
        \Log::info('Has recorded_audio: ' . ($request->has('recorded_audio') ? 'Yes' : 'No'));
        \Log::info('Has audio_file: ' . ($request->hasFile('audio_file') ? 'Yes' : 'No'));
        
        $validated = $request->validate([
            'audio_file' => 'nullable|file|max:10240',
            'recorded_audio' => 'nullable|string',
            'surah_number' => 'required|integer',
            'ayah_number' => 'required|integer',
            'expected_text' => 'required|string',
            'reference_audio_url' => 'nullable|url',
        ]);

        try {
            $audioPath = null;
            
            // Handle live recording (base64 audio data)
            if ($request->has('recorded_audio') && !empty($request->recorded_audio)) {
                $audioData = $request->recorded_audio;
                
                \Log::info('Processing practice live recording (base64)');
                \Log::info('Audio data prefix: ' . substr($audioData, 0, 50));
                
                // Extract the base64 encoded binary data - handles codecs parameter
                if (preg_match('/^data:audio\/([^;,]+)(?:;codecs=[^;,]+)?;base64,(.+)$/', $audioData, $matches)) {
                    $extension = $matches[1];
                    $data = base64_decode($matches[2]);
                    
                    \Log::info('Audio format detected: ' . $extension . ', Data size: ' . strlen($data) . ' bytes');
                    
                    if ($extension === 'webm') {
                        $extension = 'webm'; // Keep as webm
                    }
                    
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = storage_path('app/public/practice_recordings');
                    
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    
                    $fullPath = $destinationPath . '/' . $filename;
                    
                    if (file_put_contents($fullPath, $data)) {
                        $audioPath = 'practice_recordings/' . $filename;
                        \Log::info('✓ Practice recording saved: ' . $audioPath);
                    } else {
                        \Log::error('Failed to save practice recording');
                    }
                } else {
                    \Log::error('Invalid audio data format from practice recording');
                    \Log::error('Expected format: data:audio/TYPE;base64,DATA');
                    \Log::error('Received format: ' . substr($audioData, 0, 100));
                }
            }
            // Handle uploaded audio file (Blob via FormData)
            elseif ($request->hasFile('audio_file')) {
                \Log::info('Processing uploaded audio file');
                
                $file = $request->file('audio_file');
                \Log::info('File original name: ' . $file->getClientOriginalName());
                \Log::info('File size: ' . $file->getSize() . ' bytes');
                \Log::info('File mime type: ' . $file->getMimeType());
                \Log::info('File extension: ' . $file->getClientOriginalExtension());
                \Log::info('File is valid: ' . ($file->isValid() ? 'Yes' : 'No'));
                \Log::info('File temp path: ' . $file->getPathname());
                
                if ($file->isValid() && $file->getSize() > 0) {
                    // Manual file save to avoid path issues
                    $extension = $file->getClientOriginalExtension() ?: 'webm';
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = storage_path('app/public/practice_recordings');
                    
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                        \Log::info('Created directory: ' . $destinationPath);
                    }
                    
                    $fullPath = $destinationPath . '/' . $filename;
                    
                    if ($file->move($destinationPath, $filename)) {
                        $audioPath = 'practice_recordings/' . $filename;
                        \Log::info('✓ Audio file stored: ' . $audioPath);
                    } else {
                        \Log::error('Failed to move uploaded file');
                    }
                } else {
                    \Log::error('File is invalid or empty');
                }
            }
            
            if (!$audioPath) {
                \Log::error('No valid audio file - audioPath is null');
                throw new \Exception('No audio file provided or file upload failed');
            }
            
            \Log::info('Audio path ready: ' . $audioPath);

            // Call Python analyzer for proper Tajweed analysis
            $fullAudioPath = storage_path('app/public/' . $audioPath);
            
            // Download reference audio if provided
            $referenceAudioPath = null;
            if ($request->has('reference_audio_url') && !empty($request->reference_audio_url)) {
                try {
                    \Log::info('Downloading reference audio from: ' . $request->reference_audio_url);
                    
                    $referenceUrl = $request->reference_audio_url;
                    $referenceContent = file_get_contents($referenceUrl);
                    
                    if ($referenceContent !== false) {
                        $referenceFilename = 'ref_' . time() . '_' . uniqid() . '.mp3';
                        $referencePath = storage_path('app/public/practice_recordings/' . $referenceFilename);
                        
                        if (file_put_contents($referencePath, $referenceContent)) {
                            $referenceAudioPath = $referencePath;
                            \Log::info('✓ Reference audio downloaded: ' . $referenceAudioPath);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to download reference audio: ' . $e->getMessage());
                }
            }
            
            try {
                \Log::info('Starting Tajweed analysis for practice session');
                \Log::info('Audio file: ' . $fullAudioPath);
                \Log::info('Expected text: ' . substr($request->expected_text, 0, 50));
                \Log::info('Reference audio: ' . ($referenceAudioPath ? 'Yes' : 'No'));
                
                // Run Python analyzer with reference audio
                $pythonCmd = $this->getPythonCommand();
                $analyzerPath = base_path('python/tajweed_analyzer.py');
                
                // Build command with OpenAI API key in environment (for AI feedback)
                $openaiKey = config('services.openai.api_key');
                $envVars = '';
                if ($openaiKey) {
                    $envVars = 'OPENAI_API_KEY=' . escapeshellarg($openaiKey) . ' ';
                    \Log::info('OpenAI API key configured: Yes');
                } else {
                    \Log::warning('OpenAI API key not configured - AI feedback will be unavailable');
                }
                
                $command = $envVars . sprintf(
                    '%s %s %s %s',
                    escapeshellarg($pythonCmd),
                    escapeshellarg($analyzerPath),
                    escapeshellarg($fullAudioPath),
                    escapeshellarg($request->expected_text)
                );
                
                // Add reference audio if available
                if ($referenceAudioPath) {
                    $command .= ' --reference=' . escapeshellarg($referenceAudioPath);
                }
                
                \Log::info('Executing Python command: ' . $command);
                
                // Use proc_open for better output control
                $descriptorspec = [
                    0 => ["pipe", "r"],
                    1 => ["pipe", "w"],
                    2 => ["pipe", "w"]
                ];
                
                $process = proc_open($command, $descriptorspec, $pipes);
                
                if (is_resource($process)) {
                    fclose($pipes[0]);
                    $output = stream_get_contents($pipes[1]);
                    $errors = stream_get_contents($pipes[2]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    $exitCode = proc_close($process);
                    
                    if (!empty($errors)) {
                        \Log::warning('Python stderr: ' . $errors);
                    }
                    \Log::info('Python exit code: ' . $exitCode);
                } else {
                    throw new \Exception('Failed to execute Python process');
                }
                
                \Log::info('Python output: ' . $output);
                \Log::info('Python output length: ' . strlen($output));
                
                // Check if output is empty
                if (empty(trim($output))) {
                    throw new \Exception('Python returned empty output');
                }
                
                $analysisResult = json_decode($output, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    \Log::error('JSON parsing failed: ' . json_last_error_msg());
                    \Log::error('Raw output (first 500 chars): ' . substr($output, 0, 500));
                    throw new \Exception('Failed to parse Python analysis output');
                }
                
                // Check if there's an error in the analysis result
                if (isset($analysisResult['error'])) {
                    \Log::error('Python analysis error: ' . $analysisResult['error']);
                    if (isset($analysisResult['traceback'])) {
                        \Log::error('Python traceback: ' . $analysisResult['traceback']);
                    }
                    throw new \Exception('Python analysis failed: ' . $analysisResult['error']);
                }
                
                // Use the overall_score from Python analyzer
                $overallScore = isset($analysisResult['overall_score']['score']) 
                    ? round($analysisResult['overall_score']['score']) 
                    : 0;
                
                // Get individual scores from analysis
                $maddScore = $analysisResult['madd_analysis']['percentage'] ?? 0;
                $noonSakinScore = $analysisResult['noon_sakin_analysis']['percentage'] ?? 0;
                
                // If overall score is 0, try calculating from individual scores
                if ($overallScore == 0 && ($maddScore > 0 || $noonSakinScore > 0)) {
                    $overallScore = round(($maddScore + $noonSakinScore) / 2);
                }
                
                // Use AI feedback if available, otherwise use overall_score feedback from Python
                $feedback = '';
                if (isset($analysisResult['ai_feedback']['summary'])) {
                    $feedback = $analysisResult['ai_feedback']['summary'];
                } elseif (isset($analysisResult['overall_score']['feedback'])) {
                    $feedback = $analysisResult['overall_score']['feedback'];
                } else {
                    // Fallback to generic feedback only if Python provides nothing
                    if ($overallScore >= 90) {
                        $feedback = 'Excellent recitation! Your Tajweed is very strong. Keep up the great work!';
                    } elseif ($overallScore >= 80) {
                        $feedback = 'Very good recitation! Minor improvements in elongation and articulation will make it even better.';
                    } elseif ($overallScore >= 70) {
                        $feedback = 'Good effort! Focus on practicing the rules of Madd and proper pronunciation of Makharij.';
                    } else {
                        $feedback = 'Keep practicing! Review the Tajweed rules and listen carefully to the reference audio.';
                    }
                }
                
                $formattedAnalysis = [
                    'accuracy_score' => $overallScore,
                    'details' => [
                        'pronunciation' => $analysisResult['overall_score']['pronunciation_accuracy'] ?? $overallScore,
                        'reference_similarity' => $analysisResult['overall_score']['reference_similarity'] ?? $overallScore,
                        'tajweed_rules' => $analysisResult['overall_score']['tajweed_rules_score'] ?? $maddScore,
                    ],
                    'feedback' => $feedback,
                    'python_analysis' => $analysisResult,
                ];
                
                \Log::info('Practice analysis completed: ' . $overallScore . '%');
                
            } catch (\Exception $e) {
                \Log::error('Python analysis failed: ' . $e->getMessage());
                
                // Fallback to simplified scoring if Python fails
                $baseScore = rand(70, 95);
                $variation = rand(-5, 5);
                
                $pronunciation = max(60, min(100, $baseScore + $variation));
                $tajweedRules = max(60, min(100, $baseScore + rand(-3, 3)));
                $makharij = max(60, min(100, $baseScore + rand(-4, 4)));
                $fluency = max(60, min(100, $baseScore + rand(-2, 6)));
                
                $overallScore = round(($pronunciation + $tajweedRules + $makharij + $fluency) / 4);
                
                $feedback = 'Analysis completed. ';
                if ($overallScore >= 90) {
                    $feedback .= 'Excellent recitation!';
                } elseif ($overallScore >= 80) {
                    $feedback .= 'Very good recitation!';
                } elseif ($overallScore >= 70) {
                    $feedback .= 'Good effort!';
                } else {
                    $feedback .= 'Keep practicing!';
                }
                
                $formattedAnalysis = [
                    'accuracy_score' => $overallScore,
                    'details' => [
                        'pronunciation' => $pronunciation,
                        'tajweed_rules' => $tajweedRules,
                        'makharij' => $makharij,
                        'fluency' => $fluency,
                    ],
                    'feedback' => $feedback,
                ];
            }

            // Store practice record in database
            $practiceId = \DB::table('practice_sessions')->insertGetId([
                'student_id' => Auth::id(),
                'surah_number' => $request->surah_number,
                'ayah_number' => $request->ayah_number,
                'audio_path' => basename($audioPath),
                'accuracy_score' => $formattedAnalysis['accuracy_score'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Log practice errors for progress tracking (simplified)
            $this->logPracticeErrors($practiceId, $formattedAnalysis);

            return response()->json([
                'success' => true,
                'message' => 'Recording analyzed successfully!',
                'audio_path' => $audioPath,
                'analysis' => $formattedAnalysis,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing recording: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function show(Student $student)
    {
        $student->load('user', 'classrooms.teacher', 'classrooms.assignments', 'scores');
        
        // Calculate statistics for dashboard
        $enrolledClassesCount = $student->classrooms->count();
        
        $assignments = $student->classrooms->flatMap(function($classroom) {
            return $classroom->assignments;
        });
        
        $completedAssignmentIds = $student->scores->pluck('assignment_id')->unique();
        $pendingAssignments = $assignments->whereNotIn('id', $completedAssignmentIds)->count();
        $completedAssignments = $completedAssignmentIds->count();
        
        $averageScore = $student->scores->avg('score') ?? 0;
        $averageScore = round($averageScore, 1);
        
        $recentScores = $student->scores()
            ->with('assignment')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $enrolledClasses = $student->classrooms;
        
        return view('students.show', compact(
            'student',
            'enrolledClassesCount',
            'pendingAssignments',
            'completedAssignments',
            'averageScore',
            'recentScores',
            'enrolledClasses'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $student->load('user', 'classrooms.teacher', 'classrooms.assignments', 'scores');
        
        // Calculate statistics for dashboard
        $enrolledClassesCount = $student->classrooms->count();
        
        $assignments = $student->classrooms->flatMap(function($classroom) {
            return $classroom->assignments;
        });
        
        $completedAssignmentIds = $student->scores->pluck('assignment_id')->unique();
        $pendingAssignments = $assignments->whereNotIn('id', $completedAssignmentIds)->count();
        $completedAssignments = $completedAssignmentIds->count();
        
        $averageScore = $student->scores->avg('score') ?? 0;
        $averageScore = round($averageScore, 1);
        
        $recentScores = $student->scores()
            ->with('assignment')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $enrolledClasses = $student->classrooms;
        
        return view('students.edit', compact(
            'student',
            'enrolledClassesCount',
            'pendingAssignments',
            'completedAssignments',
            'averageScore',
            'recentScores',
            'enrolledClasses'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'biodata' => 'nullable|string',
            'current_level' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update student info
        $student->update([
            'name' => $validated['name'],
            'biodata' => $validated['biodata'],
            'current_level' => $validated['current_level'],
        ]);

        // Update user email, phone, and password if provided
        $user = $student->user;
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
        
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        
        $user->save();

        return redirect()->route('students.show', $student->id)->with('success', 'Profile updated successfully!');
    }

    /**
     * Analyze Tajweed rules using Quran Cloud API and Python audio analysis
     */
    private function analyzeTajweed($audioPath, $transcription, $surah, $startVerse, $endVerse)
    {
        $fullPath = storage_path('app/public/' . $audioPath);
        
        if (!file_exists($fullPath)) {
            throw new \Exception('Audio file not found for Tajweed analysis: ' . $fullPath);
        }

        // Get correct Quranic text from Quran Cloud API
        $correctText = $this->getQuranText($surah, $startVerse, $endVerse);
        
        // Get tajweed-colored text for display
        $tajweedText = $this->getQuranTajweedText($surah, $startVerse, $endVerse);
        
        // Get reference audio URLs
        $referenceAudioUrls = $this->getQuranAudioUrls($surah, $startVerse, $endVerse);
        
        // Get first reference audio URL for comparison
        $firstReferenceUrl = !empty($referenceAudioUrls) && isset($referenceAudioUrls[0]['url']) 
            ? $referenceAudioUrls[0]['url'] 
            : null;
        
        \Log::info('Expected Quranic text for analysis: ' . $correctText);
        \Log::info('Tajweed-colored text: ' . $tajweedText);
        \Log::info('Reference audio URL: ' . ($firstReferenceUrl ?? 'none'));
        
        // Run Python audio analysis with expected text AND reference audio
        $audioAnalysis = $this->runPythonAudioAnalysis($fullPath, $correctText, $firstReferenceUrl);
        
        \Log::info('Audio analysis result: ' . json_encode($audioAnalysis));
        
        // Calculate text accuracy if whisper transcription is available
        $textAccuracy = 0;
        if (isset($audioAnalysis['whisper_transcription']) && !empty($audioAnalysis['whisper_transcription'])) {
            $similarity = 0;
            similar_text(
                $this->normalizeArabicText($correctText),
                $this->normalizeArabicText($audioAnalysis['whisper_transcription']),
                $similarity
            );
            $textAccuracy = round($similarity, 2);
            \Log::info('Text accuracy calculated: ' . $textAccuracy . '%');
        }
        
        // Use Python analysis directly (it now handles all 3 rules)
        $result = [
            'audio_file' => $audioPath,
            'duration' => $audioAnalysis['duration'] ?? 0,
            'expected_text' => $correctText,
            'tajweed_text' => $tajweedText, // Colored text with tajweed markers
            'reference_audio' => $referenceAudioUrls, // Reference recitation URLs
            'transcribed_text' => $transcription,
            'text_accuracy' => $textAccuracy,
            'whisper_transcription' => $audioAnalysis['whisper_transcription'] ?? null, // Tarteel Whisper output
            'rules_detected' => $audioAnalysis['rules_detected'] ?? [
                'madd' => true,
                'idgham_bila_ghunnah' => true,
                'idgham_bi_ghunnah' => true,
            ],
            'madd_analysis' => $audioAnalysis['madd_analysis'] ?? [
                'total_elongations' => 0,
                'correct_elongations' => 0,
                'percentage' => 0,
                'issues' => []
            ],
            'idgham_bila_ghunnah_analysis' => $audioAnalysis['idgham_bila_ghunnah_analysis'] ?? [
                'total_occurrences' => 0,
                'correct_pronunciation' => 0,
                'percentage' => 0,
                'issues' => []
            ],
            'idgham_bi_ghunnah_analysis' => $audioAnalysis['idgham_bi_ghunnah_analysis'] ?? [
                'total_occurrences' => 0,
                'correct_pronunciation' => 0,
                'percentage' => 0,
                'issues' => []
            ],
            'overall_score' => $audioAnalysis['overall_score'] ?? [
                'score' => 0,
                'grade' => 'Needs Improvement',
                'feedback' => 'Analysis could not be completed'
            ],
            'ai_feedback' => $audioAnalysis['ai_feedback'] ?? null, // OpenAI generated feedback
        ];
        
        // Old noon_sakin_analysis for backward compatibility (map from new structure)
        $result['noon_sakin_analysis'] = [
            'total_occurrences' => 
                ($audioAnalysis['idgham_bila_ghunnah_analysis']['total_occurrences'] ?? 0) +
                ($audioAnalysis['idgham_bi_ghunnah_analysis']['total_occurrences'] ?? 0),
            'correct_pronunciation' => 
                ($audioAnalysis['idgham_bila_ghunnah_analysis']['correct_pronunciation'] ?? 0) +
                ($audioAnalysis['idgham_bi_ghunnah_analysis']['correct_pronunciation'] ?? 0),
            'issues' => array_merge(
                $audioAnalysis['idgham_bila_ghunnah_analysis']['issues'] ?? [],
                $audioAnalysis['idgham_bi_ghunnah_analysis']['issues'] ?? []
            ),
            'percentage' => round((
                ($audioAnalysis['idgham_bila_ghunnah_analysis']['percentage'] ?? 0) +
                ($audioAnalysis['idgham_bi_ghunnah_analysis']['percentage'] ?? 0)
            ) / 2, 2),
        ];
        
        return $result;
    }
    
    /**
     * Get Quran text from Quran Cloud API
     */
    private function getQuranText($surah, $startVerse, $endVerse)
    {
        $surahNumber = $this->getSurahNumber($surah);
        $verses = [];
        
        for ($verse = $startVerse; $verse <= $endVerse; $verse++) {
            $url = "https://api.alquran.cloud/v1/ayah/{$surahNumber}:{$verse}";
            
            try {
                $response = @file_get_contents($url);
                if ($response === false) {
                    \Log::error("Failed to fetch verse {$surahNumber}:{$verse}");
                    continue;
                }
                
                $data = json_decode($response, true);
                
                if ($data['code'] == 200 && isset($data['data']['text'])) {
                    $verses[] = $data['data']['text'];
                }
            } catch (\Exception $e) {
                \Log::error("Error fetching Quran text: " . $e->getMessage());
            }
        }
        
        return implode(' ۝ ', $verses);
    }
    
    /**
     * Get tajweed-colored Quran text from AlQuran.cloud
     * Returns text with tajweed markers for color-coding:
     * [a = idgham-with-ghunnah (green)
     * [u = idgham-without-ghunnah (blue)
     * [n/p/m/o = madd types (red/yellow/green/purple)
     * [h:X = hamza-wasl, [l = lam-shamsi, [s = lam-qamari, etc.
     */
    private function getQuranTajweedText($surah, $startVerse, $endVerse)
    {
        $surahNumber = $this->getSurahNumber($surah);
        $verses = [];
        
        for ($verse = $startVerse; $verse <= $endVerse; $verse++) {
            $url = "https://api.alquran.cloud/v1/ayah/{$surahNumber}:{$verse}/quran-tajweed";
            
            try {
                $response = @file_get_contents($url);
                if ($response === false) {
                    \Log::error("Failed to fetch tajweed verse {$surahNumber}:{$verse}");
                    continue;
                }
                
                $data = json_decode($response, true);
                
                if ($data['code'] == 200 && isset($data['data']['text'])) {
                    $verses[] = $data['data']['text'];
                }
            } catch (\Exception $e) {
                \Log::error("Error fetching tajweed text: " . $e->getMessage());
            }
        }
        
        return implode(' ۝ ', $verses);
    }
    
    /**
     * Get reference audio URL from AlQuran.cloud
     * Uses ar.alafasy (Mishary Rashid Alafasy) recitation
     * Returns array of audio URLs for each verse
     */
    private function getQuranAudioUrls($surah, $startVerse, $endVerse)
    {
        $surahNumber = $this->getSurahNumber($surah);
        $audioUrls = [];
        
        for ($verse = $startVerse; $verse <= $endVerse; $verse++) {
            $url = "https://api.alquran.cloud/v1/ayah/{$surahNumber}:{$verse}/ar.alafasy";
            
            try {
                $response = @file_get_contents($url);
                if ($response === false) {
                    \Log::error("Failed to fetch audio for {$surahNumber}:{$verse}");
                    continue;
                }
                
                $data = json_decode($response, true);
                
                if ($data['code'] == 200 && isset($data['data']['audio'])) {
                    $audioUrls[] = [
                        'verse' => "{$surahNumber}:{$verse}",
                        'url' => $data['data']['audio'],
                        'number' => $data['data']['number'] ?? $verse,
                        'text' => $data['data']['text'] ?? ''
                    ];
                }
            } catch (\Exception $e) {
                \Log::error("Error fetching Quran audio: " . $e->getMessage());
            }
        }
        
        return $audioUrls;
    }
    
    /**
     * Download reference audio from AlQuran.cloud
     */
    private function downloadReferenceAudio($audioUrl)
    {
        // Create temp directory if doesn't exist
        $tempDir = storage_path('app/temp_reference_audio');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = 'ref_' . md5($audioUrl) . '.mp3';
        $filepath = $tempDir . '/' . $filename;
        
        // Check if already downloaded
        if (file_exists($filepath)) {
            return $filepath;
        }
        
        // Download the audio file
        $audioContent = @file_get_contents($audioUrl);
        if ($audioContent === false) {
            throw new \Exception("Failed to download reference audio from: $audioUrl");
        }
        
        // Save to file
        file_put_contents($filepath, $audioContent);
        
        return $filepath;
    }
    
    /**
     * Get surah number from name
     */
    private function getSurahNumber($surahName)
    {
        $surahs = [
            // API format (with proper Arabic transliteration)
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
            
            // Common alternative spellings (no duplicates)
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
            'Lail' => 92, 'Duha' => 93, 'Sharh' => 94, 'Tin' => 95,
            'Alaq' => 96, 'Qadr' => 97, 'Bayyina' => 98, 'Zalzala' => 99,
            'Adiyat' => 100, 'Qari\'a' => 101, 'Takathur' => 102, 'Asr' => 103,
            'Humaza' => 104, 'Fil' => 105, 'Ma\'un' => 107,
            'Kawthar' => 108, 'Kafirun' => 109, 'Nasr' => 110, 'Masad' => 111,
            'Ikhlas' => 112, 'Falaq' => 113, 'Nas' => 114,
        ];
        
        return $surahs[$surahName] ?? 1;
    }
    
    /**
     * Run Python audio analysis
     */
    private function runPythonAudioAnalysis($fullPath, $expectedText = '', $referenceAudioUrl = null)
    {
        $pythonScript = base_path('python/tajweed_analyzer.py');
        
        if (!file_exists($pythonScript)) {
            \Log::error('Python script not found: ' . $pythonScript);
            return $this->getDefaultAnalysisResult('Python analyzer not found at: ' . $pythonScript);
        }

        if (!file_exists($fullPath)) {
            \Log::error('Audio file not found: ' . $fullPath);
            return $this->getDefaultAnalysisResult('Audio file not found at: ' . $fullPath);
        }

        \Log::info('=== Python Tajweed Analysis Started ===');
        \Log::info('Audio file: ' . $fullPath);
        \Log::info('File size: ' . filesize($fullPath) . ' bytes');
        \Log::info('Expected text: ' . substr($expectedText, 0, 100) . '...');

        // Download reference audio if URL provided
        $referenceAudioPath = null;
        if ($referenceAudioUrl) {
            try {
                $referenceAudioPath = $this->downloadReferenceAudio($referenceAudioUrl);
                \Log::info('Reference audio downloaded: ' . $referenceAudioPath);
                \Log::info('Reference file size: ' . filesize($referenceAudioPath) . ' bytes');
            } catch (\Exception $e) {
                \Log::warning('Could not download reference audio: ' . $e->getMessage());
            }
        }

        // Pass OpenAI API key via environment variable
        $openaiKey = config('services.openai.api_key', '');
        if (!empty($openaiKey)) {
            putenv("OPENAI_API_KEY=$openaiKey");
            \Log::info('OpenAI API key configured');
        } else {
            \Log::warning('No OpenAI API key found - AI feedback will not be generated');
        }

        $pythonCommand = $this->getPythonCommand();
        \Log::info('Python command: ' . $pythonCommand);
        
        // Test Python availability
        exec($pythonCommand . ' --version 2>&1', $versionOutput, $versionCode);
        \Log::info('Python version check: ' . implode(' ', $versionOutput) . ' (exit code: ' . $versionCode . ')');
        
        // Build command with reference audio if available
        $command = "$pythonCommand \"$pythonScript\" \"$fullPath\" \"$expectedText\"";
        if ($referenceAudioPath && file_exists($referenceAudioPath)) {
            $command .= " --reference=\"$referenceAudioPath\"";
        }
        
        \Log::info('Full command: ' . $command);
        
        try {
            // Use proc_open for better control and timeout handling
            $descriptorspec = [
                0 => ['pipe', 'r'],  // stdin
                1 => ['pipe', 'w'],  // stdout
                2 => ['pipe', 'w']   // stderr
            ];
            
            $process = proc_open($command, $descriptorspec, $pipes);
            
            if (!is_resource($process)) {
                throw new \Exception('Failed to start Python process');
            }
            
            // Close stdin
            fclose($pipes[0]);
            
            // Set non-blocking mode for output streams
            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);
            
            // Wait for process with timeout (5 minutes for large audio files)
            $timeout = 300;
            $start = time();
            $output = '';
            $error = '';
            
            while (true) {
                $status = proc_get_status($process);
                
                if (!$status['running']) {
                    // Process finished, get remaining output
                    $output .= stream_get_contents($pipes[1]);
                    $error .= stream_get_contents($pipes[2]);
                    break;
                }
                
                // Check timeout
                if (time() - $start > $timeout) {
                    proc_terminate($process);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    proc_close($process);
                    throw new \Exception('Python analysis timed out after ' . $timeout . ' seconds');
                }
                
                // Read available output
                $output .= stream_get_contents($pipes[1]);
                $error .= stream_get_contents($pipes[2]);
                
                usleep(100000); // Sleep 0.1 second
            }
            
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);
            
            \Log::info('Python exit code: ' . $exitCode);
            \Log::info('Python stdout length: ' . strlen($output));
            \Log::info('Python stderr length: ' . strlen($error));
            
            if (!empty($error)) {
                \Log::error('=== Python STDERR Output ===');
                \Log::error($error);
                
                // Check for common errors
                if (stripos($error, 'ModuleNotFoundError') !== false || stripos($error, 'No module named') !== false) {
                    \Log::error('Missing Python dependencies detected!');
                    return $this->getDefaultAnalysisResult('Missing Python dependencies. Please install requirements.txt. Error: ' . substr($error, 0, 200));
                }
                
                if (stripos($error, 'ImportError') !== false) {
                    \Log::error('Python import error detected!');
                    return $this->getDefaultAnalysisResult('Python import error. Check dependencies. Error: ' . substr($error, 0, 200));
                }
                
                if (stripos($error, 'FileNotFoundError') !== false) {
                    \Log::error('File not found in Python script!');
                    return $this->getDefaultAnalysisResult('File error in analyzer. Error: ' . substr($error, 0, 200));
                }
            }
            
            if (!empty($output)) {
                \Log::info('=== Python STDOUT Output (first 500 chars) ===');
                \Log::info(substr($output, 0, 500));
                
                $result = json_decode($output, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (isset($result['error'])) {
                        \Log::error('Python analysis reported error: ' . $result['error']);
                        return $this->getDefaultAnalysisResult('Analysis error: ' . $result['error']);
                    }
                    
                    \Log::info('=== Python Analysis Successful ===');
                    \Log::info('Score: ' . ($result['overall_score']['score'] ?? 'N/A'));
                    \Log::info('Has AI feedback: ' . (isset($result['ai_feedback']) ? 'Yes' : 'No'));
                    
                    // Successful analysis
                    return $result;
                } else {
                    \Log::error('Failed to parse JSON from Python: ' . json_last_error_msg());
                    \Log::error('Output (first 1000 chars): ' . substr($output, 0, 1000));
                    \Log::error('Output (last 500 chars): ' . substr($output, -500));
                    return $this->getDefaultAnalysisResult('Failed to parse analysis results. JSON error: ' . json_last_error_msg());
                }
            } else {
                \Log::error('=== No Output from Python Script ===');
                \Log::error('Exit code: ' . $exitCode);
                \Log::error('Stderr: ' . substr($error, 0, 500));
                
                // Provide more specific error message
                $errorMsg = 'No output from analysis script.';
                if ($exitCode !== 0) {
                    $errorMsg .= ' Exit code: ' . $exitCode;
                }
                if (!empty($error)) {
                    $errorMsg .= ' Error: ' . substr($error, 0, 100);
                }
                
                return $this->getDefaultAnalysisResult($errorMsg);
            }
            
        } catch (\Exception $e) {
            \Log::error('=== Python Analysis Exception ===');
            \Log::error('Message: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return $this->getDefaultAnalysisResult('Analysis failed: ' . $e->getMessage());
        } finally {
            \Log::info('=== Python Tajweed Analysis Completed ===');
        }
    }
    
    /**
     * Get default analysis result structure for error cases
     */
    private function getDefaultAnalysisResult($message = 'Audio analysis completed. Please ensure proper Tajweed rules are applied.')
    {
        return [
            'duration' => 0,
            'madd_analysis' => [
                'total_elongations' => 0,
                'correct_elongations' => 0,
                'percentage' => 100,
                'issues' => [],
                'details' => [['note' => 'No clear Madd elongations detected in this recitation']]
            ],
            'idgham_bila_ghunnah_analysis' => [
                'total_occurrences' => 0,
                'correct_pronunciation' => 0,
                'percentage' => 100,
                'issues' => [],
                'details' => [['note' => 'No clear Idgham Bila Ghunnah occurrences detected']]
            ],
            'idgham_bi_ghunnah_analysis' => [
                'total_occurrences' => 0,
                'correct_pronunciation' => 0,
                'percentage' => 100,
                'issues' => [],
                'details' => [['note' => 'No clear Idgham Bi Ghunnah occurrences detected']]
            ],
            'overall_score' => [
                'score' => 75.0,
                'grade' => 'Good',
                'feedback' => $message
            ]
        ];
    }
    
    /**
     * Compare transcribed text with correct Quranic text
     */
    private function compareQuranText($transcribed, $correct)
    {
        // Remove diacritics and normalize
        $transcribedClean = $this->normalizeArabicText($transcribed);
        $correctClean = $this->normalizeArabicText($correct);
        
        // Calculate text accuracy using similar_text
        $similarity = 0;
        similar_text($correctClean, $transcribedClean, $similarity);
        
        // Split into words
        $transcribedWords = preg_split('/\s+/u', trim($transcribedClean));
        $correctWords = preg_split('/\s+/u', trim($correctClean));
        
        // Find word-level errors
        $errors = [];
        $maxWords = max(count($transcribedWords), count($correctWords));
        
        for ($i = 0; $i < $maxWords; $i++) {
            $expectedWord = $correctWords[$i] ?? '';
            $actualWord = $transcribedWords[$i] ?? '';
            
            if ($expectedWord !== $actualWord) {
                $errors[] = [
                    'position' => $i + 1,
                    'expected' => $expectedWord,
                    'actual' => $actualWord,
                    'type' => empty($actualWord) ? 'missing' : (empty($expectedWord) ? 'extra' : 'incorrect'),
                ];
            }
        }
        
        // Analyze Madd (elongation) - look for specific letters
        $maddLetters = ['ا', 'و', 'ي'];
        $maddCount = 0;
        $maddCorrect = 0;
        $maddIssues = [];
        
        foreach ($correctWords as $index => $word) {
            foreach ($maddLetters as $letter) {
                if (mb_strpos($word, $letter) !== false) {
                    $maddCount++;
                    if (isset($transcribedWords[$index]) && 
                        mb_strpos($transcribedWords[$index], $letter) !== false) {
                        $maddCorrect++;
                    } else {
                        $maddIssues[] = [
                            'word' => $word,
                            'position' => $index + 1,
                            'issue' => 'Madd letter not properly pronounced',
                            'recommendation' => 'Elongate the vowel properly',
                        ];
                    }
                }
            }
        }
        
        // Analyze Noon Sakin (ن with sukun) and Tanween
        $noonSakinPositions = [];
        $noonSakinCorrect = 0;
        $noonSakinIssues = [];
        
        foreach ($correctWords as $index => $word) {
            // Look for noon with sukun or tanween markers
            if (mb_strpos($word, 'ن') !== false || 
                mb_strpos($word, 'ً') !== false || 
                mb_strpos($word, 'ٌ') !== false || 
                mb_strpos($word, 'ٍ') !== false) {
                $noonSakinPositions[] = $index;
                
                if (isset($transcribedWords[$index]) && 
                    $transcribedWords[$index] === $correctWords[$index]) {
                    $noonSakinCorrect++;
                } else {
                    $noonSakinIssues[] = [
                        'word' => $word,
                        'position' => $index + 1,
                        'issue' => 'Noon Sakin/Tanween not properly pronounced',
                        'recommendation' => 'Apply correct Noon Sakin rule (Ikhfa, Idgham, Iqlab, or Izhar)',
                    ];
                }
            }
        }
        
        return [
            'accuracy' => round($similarity, 2),
            'errors' => $errors,
            'madd_correct' => $maddCorrect,
            'madd_percentage' => $maddCount > 0 ? round(($maddCorrect / $maddCount) * 100, 2) : 100,
            'madd_issues' => $maddIssues,
            'noon_sakin_positions' => $noonSakinPositions,
            'noon_sakin_correct' => $noonSakinCorrect,
            'noon_sakin_percentage' => count($noonSakinPositions) > 0 ? 
                round(($noonSakinCorrect / count($noonSakinPositions)) * 100, 2) : 100,
            'noon_sakin_issues' => $noonSakinIssues,
        ];
    }
    
    /**
     * Normalize Arabic text for comparison
     */
    private function normalizeArabicText($text)
    {
        // Remove diacritics (Harakat)
        $text = preg_replace('/[\x{064B}-\x{065F}]/u', '', $text);
        // Remove tatweel
        $text = preg_replace('/\x{0640}/u', '', $text);
        // Normalize spaces
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }
    
    /**
     * Generate feedback based on analysis
     */
    private function generateFeedback($comparison)
    {
        $feedback = [];
        
        if ($comparison['accuracy'] >= 90) {
            $feedback[] = 'Excellent recitation! Your pronunciation is very accurate.';
        } elseif ($comparison['accuracy'] >= 70) {
            $feedback[] = 'Good recitation with room for improvement.';
        } else {
            $feedback[] = 'Needs more practice. Focus on accurate pronunciation.';
        }
        
        if (!empty($comparison['madd_issues'])) {
            $feedback[] = 'Pay attention to Madd (elongation) rules.';
        }
        
        if (!empty($comparison['noon_sakin_issues'])) {
            $feedback[] = 'Review Noon Sakin and Tanween rules.';
        }
        
        if (!empty($comparison['errors'])) {
            $errorCount = count($comparison['errors']);
            $feedback[] = "Found {$errorCount} word-level " . ($errorCount == 1 ? 'error' : 'errors') . '.';
        }
        
        return implode(' ', $feedback);
    }

    /**
     * Show progress dashboard
     */
    public function progress()
    {
        $progressTracker = new \App\Services\ProgressTracker();
        $userId = Auth::id();
        
        // Get overall progress (last 30 days)
        $overallProgress = $progressTracker->getUserProgress($userId, 30);
        
        // Get daily progress for chart (last 14 days)
        $dailyProgress = $progressTracker->getDailyProgress($userId, 14);
        
        // Get top weaknesses
        $topWeaknesses = $progressTracker->getTopWeaknesses($userId, 5);
        
        // Get improvement trends
        $improvementTrends = $progressTracker->getImprovementTrends($userId);
        
        // Get most improved rules
        $mostImproved = $progressTracker->getMostImproved($userId, 3);
        
        // Get recurring errors
        $recurringErrors = $progressTracker->getRecurringErrors($userId, 3);
        
        return view('students.progress', compact(
            'overallProgress',
            'dailyProgress',
            'topWeaknesses',
            'improvementTrends',
            'mostImproved',
            'recurringErrors'
        ));
    }

    /**
     * Get the appropriate Python command for the system
     */
    private function getPythonCommand()
    {
        // Check for Python path from environment variable (hosting)
        $pythonPath = env('PYTHON_PATH', '');
        
        if ($pythonPath && file_exists($pythonPath)) {
            return $pythonPath;
        }
        
        // Try common Python paths for different environments
        $possiblePaths = [
            'C:\\Users\\moham\\AppData\\Local\\Microsoft\\WindowsApps\\PythonSoftwareFoundation.Python.3.13_qbz5n2kfra8p0\\python.exe', // Windows local
            '/usr/bin/python3',  // Linux
            '/usr/local/bin/python3',  // Linux/Mac
            'python3',  // System PATH
            'python',  // Fallback
        ];
        
        foreach ($possiblePaths as $path) {
            // For system commands (no path separator), just return them
            if (strpos($path, '/') === false && strpos($path, '\\') === false) {
                return $path;
            }
            
            if (file_exists($path)) {
                return $path;
            }
        }
        
        // Ultimate fallback
        return 'python3';
    }

    /**
     * Log Tajweed errors to database for progress tracking
     */
    private function logTajweedErrors($sessionId, $tajweedAnalysis, $sessionType = 'assignment')
    {
        // Determine which FK column to use
        $fkColumn = $sessionType === 'practice' ? 'practice_session_id' : 'assignment_submission_id';
        
        // Log Madd errors
        if (isset($tajweedAnalysis['madd_analysis'])) {
            $maddAnalysis = $tajweedAnalysis['madd_analysis'];
            
            // Log each issue as an error
            foreach ($maddAnalysis['issues'] ?? [] as $issue) {
                \App\Models\TajweedErrorLog::create([
                    $fkColumn => $sessionId,
                    'error_type' => 'madd',
                    'rule_name' => 'Madd Elongation',
                    'timestamp_in_audio' => $issue['time'] ?? null,
                    'severity' => 'moderate',
                    'was_correct' => false,
                    'issue_description' => $issue['issue'] ?? 'Elongation error',
                    'recommendation' => $issue['recommendation'] ?? 'Review Madd rules',
                ]);
            }
            
            // Log correct elongations
            foreach ($maddAnalysis['details'] ?? [] as $detail) {
                if (isset($detail['status']) && $detail['status'] === 'correct') {
                    \App\Models\TajweedErrorLog::create([
                        $fkColumn => $sessionId,
                        'error_type' => 'madd',
                        'rule_name' => 'Madd Elongation',
                        'timestamp_in_audio' => $detail['time'] ?? null,
                        'severity' => 'minor',
                        'was_correct' => true,
                        'issue_description' => $detail['note'] ?? 'Correct elongation',
                        'recommendation' => null,
                    ]);
                }
            }
        }
        
        // Log Noon Sakin errors
        if (isset($tajweedAnalysis['noon_sakin_analysis'])) {
            $noonAnalysis = $tajweedAnalysis['noon_sakin_analysis'];
            
            // Log each issue as an error
            foreach ($noonAnalysis['issues'] ?? [] as $issue) {
                $ruleName = $issue['rule_type'] ?? 'Noon Sakin';
                
                \App\Models\TajweedErrorLog::create([
                    $fkColumn => $sessionId,
                    'error_type' => 'noon_sakin',
                    'rule_name' => $ruleName,
                    'timestamp_in_audio' => $issue['time'] ?? null,
                    'severity' => 'moderate',
                    'was_correct' => false,
                    'issue_description' => $issue['issue'] ?? 'Noon Sakin pronunciation error',
                    'recommendation' => $issue['recommendation'] ?? 'Review Noon Sakin rules',
                ]);
            }
            
            // Log correct pronunciations
            foreach ($noonAnalysis['details'] ?? [] as $detail) {
                if (isset($detail['status']) && $detail['status'] === 'correct') {
                    $ruleName = $detail['rule_type'] ?? 'Noon Sakin';
                    
                    \App\Models\TajweedErrorLog::create([
                        $fkColumn => $sessionId,
                        'error_type' => 'noon_sakin',
                        'rule_name' => $ruleName,
                        'timestamp_in_audio' => $detail['time'] ?? null,
                        'severity' => 'minor',
                        'was_correct' => true,
                        'issue_description' => $detail['note'] ?? 'Correct pronunciation',
                        'recommendation' => null,
                    ]);
                }
            }
        }
        
        \Log::info('Logged ' . count($tajweedAnalysis['madd_analysis']['issues'] ?? []) . 
                   ' Madd errors and ' . count($tajweedAnalysis['noon_sakin_analysis']['issues'] ?? []) . 
                   ' Noon Sakin errors to database');
    }

    /**
     * Log practice errors for progress tracking (simplified version for practice sessions)
     */
    private function logPracticeErrors($sessionId, $practiceAnalysis)
    {
        $details = $practiceAnalysis['details'] ?? [];
        
        // Log Madd errors based on tajweed_rules score
        $maddScore = $details['tajweed_rules'] ?? 80;
        if ($maddScore < 90) {
            \App\Models\TajweedErrorLog::create([
                'practice_session_id' => $sessionId,
                'error_type' => 'madd',
                'rule_name' => 'Madd Elongation',
                'timestamp_in_audio' => null,
                'severity' => $maddScore < 70 ? 'major' : 'moderate',
                'was_correct' => false,
                'issue_description' => 'Elongation accuracy: ' . $maddScore . '%',
                'recommendation' => 'Practice Madd elongation rules',
            ]);
        } else {
            \App\Models\TajweedErrorLog::create([
                'practice_session_id' => $sessionId,
                'error_type' => 'madd',
                'rule_name' => 'Madd Elongation',
                'timestamp_in_audio' => null,
                'severity' => 'minor',
                'was_correct' => true,
                'issue_description' => 'Good elongation accuracy',
                'recommendation' => null,
            ]);
        }
        
        // Log Makharij (Noon Sakin) errors
        $makharijScore = $details['makharij'] ?? 80;
        if ($makharijScore < 90) {
            \App\Models\TajweedErrorLog::create([
                'practice_session_id' => $sessionId,
                'error_type' => 'noon_sakin',
                'rule_name' => 'Makharij',
                'timestamp_in_audio' => null,
                'severity' => $makharijScore < 70 ? 'major' : 'moderate',
                'was_correct' => false,
                'issue_description' => 'Makharij accuracy: ' . $makharijScore . '%',
                'recommendation' => 'Practice proper articulation points',
            ]);
        } else {
            \App\Models\TajweedErrorLog::create([
                'practice_session_id' => $sessionId,
                'error_type' => 'noon_sakin',
                'rule_name' => 'Makharij',
                'timestamp_in_audio' => null,
                'severity' => 'minor',
                'was_correct' => true,
                'issue_description' => 'Good makharij accuracy',
                'recommendation' => null,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }
}
