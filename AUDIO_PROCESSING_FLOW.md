# Audio Processing Flow & Methodology

## Complete TajTrainer Audio Processing Architecture

This document outlines the complete flow of how audio recordings are processed in the TajTrainer system, from recording to analysis to display.

---

## Table of Contents
1. [Practice Mode Flow](#practice-mode-flow)
2. [Assignment Submission Flow](#assignment-submission-flow)
3. [Common Processing Components](#common-processing-components)
4. [Database Schema](#database-schema)
5. [Python Analyzer Architecture](#python-analyzer-architecture)

---

## Practice Mode Flow

### Overview
Practice mode allows students to record or upload Quranic recitation for immediate AI analysis without teacher grading.

### Step 1: User Interface (Frontend)
**File:** `resources/views/practice/index.blade.php`

**Recording Options:**
- **Live Recording:** MediaRecorder API captures audio as WebM/Opus
- **File Upload:** User uploads existing audio file (MP3, WAV, M4A, WebM, etc.)

**Frontend Process:**
```javascript
// 1. Start Recording
async function startRecording() {
    // Request microphone access
    audioStream = await navigator.mediaDevices.getUserMedia({
        audio: {
            echoCancellation: true,
            noiseSuppression: true,
            sampleRate: 44100
        }
    });
    
    // Use MediaRecorder with WebM/Opus codec
    mediaRecorder = new MediaRecorder(audioStream, {
        mimeType: 'audio/webm;codecs=opus'
    });
    
    // Collect audio chunks
    mediaRecorder.ondataavailable = (event) => {
        audioChunks.push(event.data);
    };
}

// 2. Stop & Process Recording
function stopRecording() {
    mediaRecorder.stop();
    
    // Create Blob from chunks
    audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
    
    // Convert to Base64 for submission
    reader.readAsDataURL(audioBlob);
    // Result: "data:audio/webm;base64,GkXfo59ChoEBQve..."
}

// 3. Submit to Backend
async function submitPractice() {
    const formData = {
        audio_file: audioBlob,  // or base64 string
        surah_number: selectedSurah,
        ayah_number: selectedAyah,
        expected_text: arabicText,
        reference_audio_url: alafasyAudioUrl
    };
    
    fetch('/student/practice/submit', {
        method: 'POST',
        body: formData
    });
}
```

---

### Step 2: Backend Reception (Laravel Controller)
**File:** `app/Http/Controllers/StudentController.php`
**Method:** `submitPractice(Request $request)`

**Data Reception & Validation:**
```php
public function submitPractice(Request $request) {
    // 1. Validate incoming data
    $validated = $request->validate([
        'audio_file' => 'nullable|file|max:10240',  // 10MB max
        'recorded_audio' => 'nullable|string',      // Base64 string
        'surah_number' => 'required|integer',
        'ayah_number' => 'required|integer',
        'expected_text' => 'required|string',
        'reference_audio_url' => 'nullable|url',
    ]);
    
    // 2. Handle Live Recording (Base64 Audio)
    if ($request->has('recorded_audio')) {
        // Extract base64 data
        preg_match('/^data:audio\/([^;,]+)(?:;codecs=[^;,]+)?;base64,(.+)$/', 
                   $audioData, $matches);
        
        $extension = $matches[1];  // 'webm', 'mp3', etc.
        $data = base64_decode($matches[2]);
        
        // Save to storage
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $path = storage_path('app/public/practice_recordings/' . $filename);
        file_put_contents($path, $data);
        
        $audioPath = 'practice_recordings/' . $filename;
    }
    
    // 3. Handle File Upload
    elseif ($request->hasFile('audio_file')) {
        $file = $request->file('audio_file');
        $extension = $file->getClientOriginalExtension() ?: 'webm';
        $filename = time() . '_' . uniqid() . '.' . $extension;
        
        // Store using Laravel's storage system
        $audioPath = $file->storeAs('practice_recordings', $filename, 'public');
    }
    
    // Continue to Python analysis...
}
```

---

### Step 3: Python Analyzer Execution
**Controller continues:**

```php
public function submitPractice(Request $request) {
    // ... (audio saved to $audioPath)
    
    // 1. Download Reference Audio (Alafasy recitation)
    if ($request->has('reference_audio_url')) {
        $referenceContent = file_get_contents($request->reference_audio_url);
        $referencePath = storage_path('app/public/practice_recordings/ref_' . time() . '.mp3');
        file_put_contents($referencePath, $referenceContent);
    }
    
    // 2. Build Python Command
    $pythonCmd = $this->getPythonCommand();  // Get Python executable path
    $analyzerPath = base_path('python/tajweed_analyzer.py');
    $fullAudioPath = storage_path('app/public/' . $audioPath);
    
    $command = sprintf(
        '%s %s %s %s',
        $pythonCmd,
        escapeshellarg($analyzerPath),
        escapeshellarg($fullAudioPath),
        escapeshellarg($request->expected_text)
    );
    
    // Add reference audio for comparison
    if ($referencePath) {
        $command .= ' --reference=' . escapeshellarg($referencePath);
    }
    
    // 3. Execute Python Script using proc_open
    $descriptorspec = [
        0 => ["pipe", "r"],  // stdin
        1 => ["pipe", "w"],  // stdout
        2 => ["pipe", "w"]   // stderr
    ];
    
    $process = proc_open($command, $descriptorspec, $pipes);
    
    if (is_resource($process)) {
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);  // Get JSON output
        $errors = stream_get_contents($pipes[2]);  // Get errors
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);
    }
    
    // 4. Parse JSON Result
    $analysisResult = json_decode($output, true);
    
    // 5. Extract Scores
    $overallScore = $analysisResult['overall_score']['score'] ?? 0;
    $maddScore = $analysisResult['madd_analysis']['percentage'] ?? 0;
    $noonSakinScore = $analysisResult['noon_sakin_analysis']['percentage'] ?? 0;
    
    // 6. Format for Frontend Display
    $formattedAnalysis = [
        'accuracy_score' => $overallScore,
        'details' => [
            'pronunciation' => $overallScore,
            'tajweed_rules' => $maddScore,
            'makharij' => $noonSakinScore,
            'fluency' => $overallScore,
        ],
        'feedback' => 'AI-generated feedback based on score...',
        'python_analysis' => $analysisResult,  // Full analysis data
    ];
    
    // 7. Return to Frontend
    return response()->json([
        'success' => true,
        'accuracy_score' => $overallScore,
        'analysis_result' => $formattedAnalysis
    ]);
}
```

---

### Step 4: Python Analyzer Processing
**File:** `python/tajweed_analyzer.py`

**Core Functionality:**
```python
def main():
    # 1. Parse Command Line Arguments
    parser = argparse.ArgumentParser()
    parser.add_argument('audio_file', help='Student audio recording path')
    parser.add_argument('expected_text', help='Expected Quranic text')
    parser.add_argument('--reference', help='Reference audio path (Alafasy)')
    args = parser.parse_args()
    
    # 2. Transcribe Student Audio using Whisper
    print("🎤 Transcribing student audio with Whisper...")
    model = whisper.load_model("base")
    result = model.transcribe(args.audio_file, language='ar')
    transcription = result['text']
    
    # 3. Audio Feature Extraction
    print("🎵 Extracting audio features...")
    y, sr = librosa.load(args.audio_file)
    
    # Extract MFCCs (Mel-frequency cepstral coefficients)
    mfccs = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=13)
    
    # Extract pitch (F0)
    f0 = librosa.yin(y, fmin=50, fmax=400)
    
    # Energy analysis
    rms = librosa.feature.rms(y=y)
    
    # 4. Reference Audio Comparison (if provided)
    if args.reference:
        ref_y, ref_sr = librosa.load(args.reference)
        ref_mfccs = librosa.feature.mfcc(y=ref_y, sr=ref_sr, n_mfcc=13)
        
        # Calculate similarity (cosine distance)
        similarity = cosine_similarity(mfccs.T, ref_mfccs.T)
    
    # 5. Tajweed Rules Analysis
    
    # A. Madd (Elongation) Detection
    madd_rules = ['مَآ', 'قَآلَ', 'آمَنُوا']  # Madd patterns
    madd_issues = []
    for pattern in madd_rules:
        if pattern in expected_text:
            # Check elongation duration in audio
            if not check_elongation(y, sr, expected_position):
                madd_issues.append({
                    'pattern': pattern,
                    'type': 'Madd Munfasil',
                    'expected_duration': '2 counts',
                    'issue': 'Elongation too short'
                })
    
    # B. Noon Sakin / Tanween Rules
    noon_patterns = ['نْ', 'ٌ', 'ٍ', 'ً']
    noon_issues = []
    for pattern in noon_patterns:
        if pattern in expected_text:
            # Check for proper nasalization (ghunnah)
            if not check_ghunnah(y, sr, expected_position):
                noon_issues.append({
                    'pattern': pattern,
                    'rule': 'Idgham bi Ghunnah',
                    'issue': 'Missing nasalization'
                })
    
    # C. Makharij (Articulation Points) Analysis
    makharij_score = analyze_makharij(y, sr, expected_text)
    
    # 6. Calculate Scores
    madd_score = ((len(madd_rules) - len(madd_issues)) / len(madd_rules)) * 100
    noon_score = ((len(noon_patterns) - len(noon_issues)) / len(noon_patterns)) * 100
    
    overall_score = (madd_score + noon_score + makharij_score) / 3
    
    # 7. Generate Feedback using OpenAI GPT
    openai_feedback = openai.ChatCompletion.create(
        model="gpt-4",
        messages=[{
            "role": "system",
            "content": "You are a Quran Tajweed expert..."
        }, {
            "role": "user",
            "content": f"Analyze this recitation: {transcription}"
        }]
    )
    
    # 8. Output JSON Result
    result = {
        'audio_file': audio_file,
        'whisper_transcription': transcription,
        'expected_text': expected_text,
        'overall_score': {
            'score': overall_score,
            'feedback': openai_feedback
        },
        'madd_analysis': {
            'percentage': madd_score,
            'issues': madd_issues
        },
        'noon_sakin_analysis': {
            'percentage': noon_score,
            'issues': noon_issues
        },
        'makharij_score': makharij_score,
        'reference_similarity': similarity if reference else None
    }
    
    print(json.dumps(result))  # Output to stdout
```

---

### Step 5: Frontend Display
**File:** `resources/views/practice/index.blade.php`

**JavaScript Handling Response:**
```javascript
fetch('/student/practice/submit', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        displayAnalysisResults(data.analysis_result);
    }
});

function displayAnalysisResults(analysis) {
    const container = document.getElementById('resultsContainer');
    
    // 1. Overall Score Display
    html = `
        <div class="overall-score">
            <div class="score-circle">${analysis.accuracy_score}%</div>
            <div class="score-label">Overall Accuracy</div>
        </div>
    `;
    
    // 2. Detailed Breakdown
    html += `
        <div class="metrics-grid">
            <div class="metric-card">
                <i class="fas fa-volume-up"></i>
                <div class="metric-value">${analysis.details.pronunciation}%</div>
                <div class="metric-label">Pronunciation</div>
            </div>
            
            <div class="metric-card">
                <i class="fas fa-book-quran"></i>
                <div class="metric-value">${analysis.details.tajweed_rules}%</div>
                <div class="metric-label">Tajweed Rules</div>
            </div>
            
            <div class="metric-card">
                <i class="fas fa-language"></i>
                <div class="metric-value">${analysis.details.makharij}%</div>
                <div class="metric-label">Makharij</div>
            </div>
            
            <div class="metric-card">
                <i class="fas fa-stream"></i>
                <div class="metric-value">${analysis.details.fluency}%</div>
                <div class="metric-label">Fluency</div>
            </div>
        </div>
    `;
    
    // 3. AI Feedback
    html += `
        <div class="feedback-section">
            <h3>AI Feedback</h3>
            <p>${analysis.feedback}</p>
        </div>
    `;
    
    // 4. Detailed Tajweed Analysis (from Python)
    if (analysis.python_analysis) {
        const pythonData = analysis.python_analysis;
        
        // Madd Issues
        if (pythonData.madd_analysis.issues.length > 0) {
            html += '<h4>Madd (Elongation) Issues:</h4><ul>';
            pythonData.madd_analysis.issues.forEach(issue => {
                html += `<li>${issue.pattern}: ${issue.issue}</li>`;
            });
            html += '</ul>';
        }
        
        // Noon Sakin Issues
        if (pythonData.noon_sakin_analysis.issues.length > 0) {
            html += '<h4>Noon Sakin / Tanween Issues:</h4><ul>';
            pythonData.noon_sakin_analysis.issues.forEach(issue => {
                html += `<li>${issue.rule}: ${issue.issue}</li>`;
            });
            html += '</ul>';
        }
    }
    
    container.innerHTML = html;
}
```

**Visual Display:**
- ✅ Large circular score display (0-100%)
- 📊 4 metric cards (Pronunciation, Tajweed, Makharij, Fluency)
- 💬 AI-generated feedback paragraph
- 📝 Detailed issue list with specific Tajweed errors
- 🎨 Color-coded Quranic text with Tajweed highlighting

---

## Assignment Submission Flow

### Overview
Assignment submissions are graded audio recordings that teachers can review. Processing is done asynchronously via queued jobs.

### Step 1: User Interface (Frontend)
**File:** `resources/views/assignment/submit.blade.php`

**Similar to Practice Mode:**
- Live recording using MediaRecorder API
- File upload option
- Same audio capture process

**Key Difference:**
- Saves submission to database immediately
- Processing happens in background job
- Teacher can later review and adjust score

---

### Step 2: Backend Reception
**File:** `app/Http/Controllers/StudentController.php`
**Method:** `storeSubmission(Request $request, $assignmentId)`

**Data Reception & Database Storage:**
```php
public function storeSubmission(Request $request, $assignmentId) {
    // 1. Validate Request
    $validated = $request->validate([
        'text_submission' => 'nullable|string',
        'transcription' => 'nullable|string',
        'recorded_audio' => 'nullable|string',
    ]);
    
    // 2. Find or Create Submission Record
    $submission = AssignmentSubmission::where('assignment_id', $assignmentId)
        ->where('student_id', Auth::id())
        ->firstOrNew();
    
    // 3. Save Audio File (same process as practice)
    if ($request->has('recorded_audio')) {
        // Process base64 audio
        preg_match('/^data:audio\/([^;,]+)(?:;codecs=[^;,]+)?;base64,(.+)$/', 
                   $audioData, $matches);
        $data = base64_decode($matches[2]);
        $filename = time() . '_' . uniqid() . '.' . $matches[1];
        
        file_put_contents(
            storage_path('app/public/submissions/' . $filename), 
            $data
        );
        
        $submission->audio_file_path = 'submissions/' . $filename;
    }
    elseif ($request->hasFile('audio_file')) {
        // Process uploaded file
        $path = $request->file('audio_file')
            ->storeAs('submissions', time() . '_' . uniqid() . '.webm', 'public');
        
        $submission->audio_file_path = $path;
    }
    
    // 4. Save Submission Metadata
    $submission->assignment_id = $assignmentId;
    $submission->student_id = Auth::id();
    $submission->status = 'submitted';
    $submission->submitted_at = now();
    $submission->transcription = $validated['transcription'] ?? null;
    $submission->text_submission = $validated['text_submission'] ?? null;
    
    $submission->save();
    
    // 5. Dispatch Background Job for Processing
    ProcessSubmissionAudio::dispatch($submission->id);
    
    return redirect()
        ->route('student.assignment.view', $assignmentId)
        ->with('success', 'Assignment submitted! Analysis in progress...');
}
```

**Database Tables Updated:**
```sql
INSERT INTO assignment_submissions (
    assignment_id,
    student_id,
    audio_file_path,
    transcription,
    status,
    submitted_at
) VALUES (
    38,
    12,
    'submissions/1738705921_67890def.webm',
    NULL,  -- Will be filled by job
    'submitted',
    '2026-02-04 14:32:01'
);
```

---

### Step 3: Background Job Processing
**File:** `app/Jobs/ProcessSubmissionAudio.php`
**Triggered by:** Laravel Queue System

**Job Execution:**
```php
class ProcessSubmissionAudio implements ShouldQueue {
    public $submissionId;
    public $timeout = 600;  // 10 minutes
    
    public function handle(): void {
        // 1. Load Submission & Assignment Data
        $submission = AssignmentSubmission::findOrFail($this->submissionId);
        $assignment = Assignment::findOrFail($submission->assignment_id);
        
        Log::info('Processing submission #' . $submission->id);
        Log::info('Assignment: ' . $assignment->surah . ' ' . 
                  $assignment->start_verse . '-' . $assignment->end_verse);
        
        // 2. Get Expected Quranic Text from API
        $expectedText = $this->getQuranText(
            $assignment->surah,
            $assignment->start_verse,
            $assignment->end_verse
        );
        
        // Example API call:
        // GET https://api.alquran.cloud/v1/ayah/1:1/quran-uthmani
        // Response: {"data": {"text": "بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ"}}
        
        // 3. Get Reference Audio (Alafasy Recitation)
        $referenceAudio = $this->getReferenceAudio(
            $assignment->surah,
            $assignment->start_verse,
            $assignment->end_verse
        );
        
        // Example API call:
        // GET https://api.alquran.cloud/v1/ayah/1:1/ar.alafasy
        // Response: {"data": {"audio": "https://cdn.alquran.cloud/media/audio/ayah/ar.alafasy/1"}}
        
        // Download reference audio
        $referenceUrl = $referenceAudio[0]['url'];
        $referencePath = $this->downloadReferenceAudio($referenceUrl);
        
        // 4. Call Python Analyzer
        $analysisResult = $this->analyzeTajweed(
            $submission->audio_file_path,  // Student audio
            '',                            // No pre-transcription
            $assignment->surah,
            $assignment->start_verse,
            $assignment->end_verse
        );
        
        // 5. Extract Transcription from Python Output
        if (isset($analysisResult['whisper_transcription'])) {
            $submission->transcription = $analysisResult['whisper_transcription'];
        }
        
        // 6. Store Full Analysis as JSON
        $submission->tajweed_analysis = json_encode($analysisResult);
        $submission->save();
        
        // 7. Calculate Score
        $overallScore = $analysisResult['overall_score']['score'] ?? 0;
        $feedback = $analysisResult['overall_score']['feedback'] ?? 'Analysis completed.';
        
        // Convert percentage to points
        $scoreValue = round(($overallScore / 100) * $assignment->total_marks);
        
        // 8. Create Score Record
        Score::updateOrCreate(
            [
                'assignment_id' => $assignment->assignment_id,
                'user_id' => $submission->student_id,
            ],
            [
                'score' => $scoreValue,
                'feedback' => $feedback,
            ]
        );
        
        // 9. Log Specific Tajweed Errors to Database
        $this->logTajweedErrors($submission, $analysisResult, 'assignment');
        
        // 10. Mark as Graded
        $submission->status = 'graded';
        $submission->save();
        
        Log::info('Processing completed for submission #' . $submission->id);
    }
}
```

**Python Analyzer Call (Same as Practice):**
```php
private function callPythonAnalyzer($audioPath, $expectedText, $referenceAudio) {
    $fullPath = storage_path('app/public/' . $audioPath);
    $pythonScript = base_path('python/tajweed_analyzer.py');
    $pythonExecutable = $this->getPythonCommand();
    
    // Build command
    $command = escapeshellarg($pythonExecutable) . ' ' . 
               escapeshellarg($pythonScript) . ' ' . 
               escapeshellarg($fullPath) . ' ' . 
               escapeshellarg($expectedText);
    
    if ($referencePath) {
        $command .= ' --reference=' . escapeshellarg($referencePath);
    }
    
    // Execute using proc_open (same as practice)
    $process = proc_open($command, $descriptorspec, $pipes);
    
    // ... (same execution logic)
    
    // Parse JSON output
    $result = json_decode($output, true);
    
    return $result;
}
```

**Database Updates After Job:**
```sql
-- assignment_submissions table
UPDATE assignment_submissions
SET transcription = 'بسم الله الرحمن الرحيم',
    tajweed_analysis = '{"overall_score": {...}, "madd_analysis": {...}}',
    status = 'graded'
WHERE id = 123;

-- scores table
INSERT INTO scores (assignment_id, user_id, score, feedback)
VALUES (38, 12, 85, 'Good recitation! Minor improvements needed in Madd.')
ON DUPLICATE KEY UPDATE
    score = 85,
    feedback = 'Good recitation! Minor improvements needed in Madd.';

-- tajweed_error_logs table
INSERT INTO tajweed_error_logs (
    assignment_submission_id,
    error_type,
    rule_name,
    severity,
    issue_description
) VALUES
(123, 'madd', 'Madd Munfasil', 'moderate', 'Elongation too short at 2.5s'),
(123, 'idgham_bi_ghunnah', 'Idgham Bi Ghunnah', 'moderate', 'Missing nasalization');
```

---

### Step 4: Display to Student
**File:** `resources/views/assignment/show.blade.php`

**Student View:**
```php
@extends('layouts.dashboard')

@section('content')
    <!-- Submission Status -->
    <div class="submission-status">
        @if($submission->status === 'graded')
            <span class="badge badge-success">✓ Graded</span>
        @elseif($submission->status === 'submitted')
            <span class="badge badge-warning">⏳ Pending Review</span>
        @endif
    </div>
    
    <!-- Score Display -->
    @if($submission->score)
        <div class="score-card">
            <div class="score-label">Your Score</div>
            <div class="score-value">
                {{ $submission->score->score }} / {{ $assignment->total_marks }}
            </div>
            <div class="score-percentage">
                {{ round(($submission->score->score / $assignment->total_marks) * 100) }}%
            </div>
        </div>
        
        <!-- AI Feedback -->
        <div class="feedback-section">
            <h3>Feedback</h3>
            <p>{{ $submission->score->feedback }}</p>
        </div>
    @endif
    
    <!-- Audio Playback -->
    <div class="audio-player">
        <h3>Your Recording</h3>
        <audio controls>
            <source src="{{ Storage::url($submission->audio_file_path) }}" type="audio/webm">
        </audio>
    </div>
    
    <!-- Transcription -->
    @if($submission->transcription)
        <div class="transcription-box">
            <h3>Transcription</h3>
            <p class="arabic-text">{{ $submission->transcription }}</p>
        </div>
    @endif
    
    <!-- Detailed Tajweed Analysis -->
    @if($submission->tajweed_analysis)
        @php
            $analysis = json_decode($submission->tajweed_analysis, true);
        @endphp
        
        <div class="tajweed-analysis">
            <h3>Tajweed Analysis</h3>
            
            <!-- Madd Analysis -->
            @if(isset($analysis['madd_analysis']))
                <div class="analysis-section">
                    <h4>Madd (Elongation): {{ $analysis['madd_analysis']['percentage'] }}%</h4>
                    @if(count($analysis['madd_analysis']['issues']) > 0)
                        <ul class="issues-list">
                            @foreach($analysis['madd_analysis']['issues'] as $issue)
                                <li>{{ $issue['pattern'] ?? 'Unknown' }}: {{ $issue['issue'] ?? 'Issue detected' }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="success-msg">✓ No issues detected</p>
                    @endif
                </div>
            @endif
            
            <!-- Noon Sakin Analysis -->
            @if(isset($analysis['noon_sakin_analysis']))
                <div class="analysis-section">
                    <h4>Noon Sakin / Tanween: {{ $analysis['noon_sakin_analysis']['percentage'] }}%</h4>
                    <!-- Similar issue display -->
                </div>
            @endif
        </div>
    @endif
@endsection
```

---

### Step 5: Teacher Review
**File:** `resources/views/teachers/grade-submission.blade.php`

**Teacher Grading Interface:**
```php
<!-- 3-Column Layout -->
<div class="grading-layout">
    <!-- Left Column: Grading Form -->
    <div class="grading-form">
        <h3>Update Grade</h3>
        
        <form action="{{ route('teacher.submission.update.grade', $submission->id) }}" method="POST">
            @csrf
            
            <!-- AI-Generated Score (Read-Only) -->
            <div class="form-group">
                <label>AI Score (Reference)</label>
                <input type="text" readonly value="{{ $submission->score->score ?? 'N/A' }}">
            </div>
            
            <!-- Teacher's Final Score -->
            <div class="form-group">
                <label>Final Score</label>
                <input type="number" name="score" 
                       value="{{ $submission->score->score ?? 0 }}"
                       min="0" max="{{ $assignment->total_marks }}">
            </div>
            
            <!-- Teacher Feedback -->
            <div class="form-group">
                <label>Your Feedback</label>
                <textarea name="feedback" rows="5">{{ $submission->score->feedback ?? '' }}</textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Grade</button>
        </form>
    </div>
    
    <!-- Middle Column: Submission Details -->
    <div class="submission-details">
        <h3>Submission</h3>
        
        <!-- Audio Player -->
        <audio controls>
            <source src="{{ Storage::url($submission->audio_file_path) }}">
        </audio>
        
        <!-- Student Info -->
        <p><strong>Student:</strong> {{ $submission->student->name }}</p>
        <p><strong>Submitted:</strong> {{ $submission->submitted_at->format('M d, Y H:i') }}</p>
        
        <!-- Transcription -->
        <div class="transcription">
            <h4>Transcription</h4>
            <p class="arabic">{{ $submission->transcription }}</p>
        </div>
    </div>
    
    <!-- Right Column: AI Analysis -->
    <div class="ai-analysis">
        <h3>AI Analysis</h3>
        
        @if($submission->tajweed_analysis)
            @php $analysis = json_decode($submission->tajweed_analysis, true); @endphp
            
            <!-- Overall Score -->
            <div class="overall-score">
                {{ $analysis['overall_score']['score'] ?? 0 }}%
            </div>
            
            <!-- Detailed Breakdown -->
            <div class="analysis-breakdown">
                <h4>Madd Analysis: {{ $analysis['madd_analysis']['percentage'] }}%</h4>
                <!-- Issues list -->
                
                <h4>Noon Sakin: {{ $analysis['noon_sakin_analysis']['percentage'] }}%</h4>
                <!-- Issues list -->
                
                <h4>Makharij: {{ $analysis['makharij_score'] ?? 0 }}%</h4>
            </div>
            
            <!-- AI Feedback -->
            <div class="ai-feedback">
                <h4>AI Feedback</h4>
                <p>{{ $analysis['overall_score']['feedback'] ?? 'No feedback available' }}</p>
            </div>
        @endif
    </div>
</div>
```

**Teacher Grade Update:**
```php
// TeacherController.php
public function updateGrade(Request $request, $submissionId) {
    $validated = $request->validate([
        'score' => 'required|numeric|min:0',
        'feedback' => 'nullable|string',
    ]);
    
    $submission = AssignmentSubmission::findOrFail($submissionId);
    
    // Update score record
    Score::updateOrCreate(
        [
            'assignment_id' => $submission->assignment_id,
            'user_id' => $submission->student_id,
        ],
        [
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
        ]
    );
    
    // Mark as graded if not already
    $submission->status = 'graded';
    $submission->save();
    
    return back()->with('success', 'Grade updated successfully!');
}
```

---

## Common Processing Components

### Python Command Resolution
**Used in Both Practice & Assignment:**

```php
private function getPythonCommand() {
    // 1. Check environment variable (production hosting)
    $pythonPath = env('PYTHON_PATH', '');
    if ($pythonPath && file_exists($pythonPath)) {
        return $pythonPath;
    }
    
    // 2. Try common paths
    $possiblePaths = [
        'C:\\Users\\moham\\AppData\\Local\\Microsoft\\WindowsApps\\PythonSoftwareFoundation.Python.3.13_qbz5n2kfra8p0\\python.exe', // Windows
        '/usr/bin/python3',        // Linux
        '/usr/local/bin/python3',  // Mac
        'python3',                 // System PATH
        'python',                  // Fallback
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    
    return 'python3';  // Ultimate fallback
}
```

---

### Audio Storage Structure
```
storage/app/public/
├── practice_recordings/        # Practice mode audio
│   ├── 1738705921_abc123.webm
│   ├── 1738705922_def456.mp3
│   └── ref_1738705923_xyz789.mp3  # Reference audio (Alafasy)
│
├── submissions/                # Assignment audio
│   ├── 1738706001_submission1.webm
│   └── 1738706002_submission2.webm
│
└── temp_reference_audio/       # Cached reference audios
    └── ref_md5hash.mp3
```

**Public Access URLs:**
- Practice: `https://tajtrainer.com/storage/practice_recordings/1738705921_abc123.webm`
- Assignment: `https://tajtrainer.com/storage/submissions/1738706001_submission1.webm`

---

## Database Schema

### assignment_submissions
```sql
CREATE TABLE assignment_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    audio_file_path VARCHAR(255),         -- Path to audio file
    transcription TEXT,                   -- Whisper transcription
    tajweed_analysis JSON,                -- Full Python analysis result
    text_submission TEXT,                 -- Optional text submission
    status ENUM('submitted', 'graded'),
    submitted_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (assignment_id) REFERENCES assignments(assignment_id),
    FOREIGN KEY (student_id) REFERENCES users(id)
);
```

### scores
```sql
CREATE TABLE scores (
    score_id INT PRIMARY KEY AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    user_id INT NOT NULL,
    score DECIMAL(5,2),                  -- Numeric score (e.g., 85.00)
    feedback TEXT,                       -- AI or teacher feedback
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE KEY (assignment_id, user_id),
    FOREIGN KEY (assignment_id) REFERENCES assignments(assignment_id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### tajweed_error_logs
```sql
CREATE TABLE tajweed_error_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assignment_submission_id INT,
    error_type VARCHAR(50),              -- 'madd', 'idgham_bi_ghunnah', etc.
    rule_name VARCHAR(100),              -- 'Madd Munfasil', 'Idgham Bi Ghunnah'
    timestamp_in_audio DECIMAL(10,2),    -- Time in seconds (e.g., 2.45)
    severity ENUM('minor', 'moderate', 'major'),
    was_correct BOOLEAN DEFAULT 0,
    issue_description TEXT,
    recommendation TEXT,
    created_at TIMESTAMP,
    
    FOREIGN KEY (assignment_submission_id) 
        REFERENCES assignment_submissions(id)
);
```

---

## Python Analyzer Architecture

### Dependencies
**File:** `python/requirements.txt`
```
openai==1.12.0
whisper==1.1.10
librosa==0.10.1
numpy==1.24.3
scipy==1.11.4
praat-parselmouth==0.4.3
scikit-learn==1.3.2
```

### Core Analysis Modules

#### 1. Whisper Transcription
```python
import whisper

def transcribe_audio(audio_path):
    model = whisper.load_model("base")
    result = model.transcribe(audio_path, language='ar')
    return result['text']
```

#### 2. Audio Feature Extraction (Librosa)
```python
import librosa

def extract_features(audio_path):
    y, sr = librosa.load(audio_path, sr=22050)
    
    # MFCCs for timbre analysis
    mfccs = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=13)
    
    # Pitch (F0) for intonation
    f0 = librosa.yin(y, fmin=50, fmax=400)
    
    # Energy (RMS) for emphasis
    rms = librosa.feature.rms(y=y)
    
    # Spectral centroid for brightness
    centroid = librosa.feature.spectral_centroid(y=y, sr=sr)
    
    return {
        'mfccs': mfccs,
        'pitch': f0,
        'energy': rms,
        'centroid': centroid
    }
```

#### 3. Reference Comparison
```python
from sklearn.metrics.pairwise import cosine_similarity

def compare_with_reference(student_mfcc, reference_mfcc):
    similarity = cosine_similarity(student_mfcc.T, reference_mfcc.T)
    avg_similarity = np.mean(similarity)
    return avg_similarity * 100  # Percentage
```

#### 4. Tajweed Rules Detection
```python
def detect_madd(transcription, audio_features):
    """Detect Madd (elongation) rules"""
    madd_patterns = [
        ('مَآ', 'Madd Munfasil'),
        ('قَآلَ', 'Madd Muttasil'),
        ('الْآن', 'Madd Lazim')
    ]
    
    issues = []
    for pattern, rule_type in madd_patterns:
        if pattern in transcription:
            # Check if elongation duration is correct (2-6 counts)
            duration = measure_elongation(audio_features, pattern_position)
            if duration < 2:
                issues.append({
                    'pattern': pattern,
                    'type': rule_type,
                    'issue': f'Elongation too short ({duration} counts, expected 2-6)'
                })
    
    return issues

def detect_noon_sakin(transcription, audio_features):
    """Detect Noon Sakin / Tanween rules"""
    noon_patterns = [
        ('نْ', 'Noon Sakin'),
        ('ٌ', 'Tanween Dammah'),
        ('ٍ', 'Tanween Kasrah'),
        ('ً', 'Tanween Fathah')
    ]
    
    issues = []
    for pattern, type in noon_patterns:
        if pattern in transcription:
            # Check for proper nasalization (ghunnah)
            has_ghunnah = detect_nasal_sound(audio_features, position)
            if not has_ghunnah:
                issues.append({
                    'pattern': pattern,
                    'rule': 'Idgham bi Ghunnah',
                    'issue': 'Missing nasalization (ghunnah)'
                })
    
    return issues
```

#### 5. OpenAI GPT Feedback
```python
import openai

def generate_ai_feedback(transcription, analysis_data):
    prompt = f"""
    You are a Quran Tajweed expert. Analyze this recitation:
    
    Transcription: {transcription}
    Overall Score: {analysis_data['overall_score']}%
    Madd Accuracy: {analysis_data['madd_score']}%
    Noon Sakin Accuracy: {analysis_data['noon_score']}%
    
    Provide specific, constructive feedback in 2-3 sentences.
    """
    
    response = openai.ChatCompletion.create(
        model="gpt-4",
        messages=[
            {"role": "system", "content": "You are a Quran Tajweed expert."},
            {"role": "user", "content": prompt}
        ]
    )
    
    return response.choices[0].message.content
```

---

## Summary Flow Diagrams

### Practice Mode: Synchronous Flow
```
┌────────────┐
│  Browser   │ Record audio with MediaRecorder
└─────┬──────┘
      │ POST /student/practice/submit
      │ (FormData with audio Blob)
      ▼
┌────────────────────┐
│ StudentController  │ Receive & save audio
│  submitPractice()  │ Download reference audio
└─────┬──────────────┘
      │ Execute Python immediately
      ▼
┌────────────────────┐
│ Python Analyzer    │ Whisper transcription
│ tajweed_analyzer.py│ Librosa feature extraction
└─────┬──────────────┘ Reference comparison
      │ Tajweed analysis
      │ OpenAI feedback
      │ Output JSON
      ▼
┌────────────────────┐
│ StudentController  │ Parse JSON
│  submitPractice()  │ Format response
└─────┬──────────────┘
      │ Return JSON response
      ▼
┌────────────┐
│  Browser   │ Display results immediately
└────────────┘
```

### Assignment Mode: Asynchronous Flow
```
┌────────────┐
│  Browser   │ Record/upload audio
└─────┬──────┘
      │ POST /student/assignment/{id}/store
      │ (FormData with audio Blob)
      ▼
┌────────────────────┐
│ StudentController  │ Save audio file
│  storeSubmission() │ Create submission record
└─────┬──────────────┘ status = 'submitted'
      │
      │ Dispatch job to queue
      ▼
┌────────────────────┐
│  Laravel Queue     │ Job queued
│  (Redis/Database)  │
└─────┬──────────────┘
      │
      │ Process job in background
      ▼
┌────────────────────────┐
│ ProcessSubmissionAudio │ Load submission
│        Job             │ Get Quran text from API
└─────┬──────────────────┘ Download reference audio
      │
      │ Execute Python
      ▼
┌────────────────────┐
│ Python Analyzer    │ Whisper transcription
│ tajweed_analyzer.py│ Librosa features
└─────┬──────────────┘ Tajweed analysis
      │ OpenAI feedback
      │ Output JSON
      ▼
┌────────────────────────┐
│ ProcessSubmissionAudio │ Parse JSON
│        Job             │ Save transcription
└─────┬──────────────────┘ Save tajweed_analysis (JSON)
      │ Calculate score
      │ Create Score record
      │ Log errors to tajweed_error_logs
      │ status = 'graded'
      ▼
┌────────────┐
│  Database  │ assignment_submissions updated
│            │ scores created
│            │ tajweed_error_logs populated
└─────┬──────┘
      │
      │ Student views assignment
      ▼
┌────────────────────┐
│ assignment/show    │ Display score
│     (Blade)        │ Show AI feedback
└────────────────────┘ Show audio player
                       Show Tajweed analysis
```

---

## Key Differences: Practice vs Assignment

| Aspect | Practice Mode | Assignment Mode |
|--------|--------------|-----------------|
| **Processing** | Synchronous (immediate) | Asynchronous (background job) |
| **Storage** | Temporary (practice_recordings/) | Permanent (submissions/) |
| **Database** | No database record | assignment_submissions table |
| **Grading** | AI only | AI + Teacher review |
| **Score** | Displayed immediately | Saved to scores table |
| **Status** | N/A | 'submitted' → 'graded' |
| **Teacher Access** | No | Yes (grading interface) |
| **Error Logging** | No | tajweed_error_logs table |

---

## Environment Configuration

### .env Variables
```bash
# Python Executable Path (production)
PYTHON_PATH=/usr/bin/python3

# OpenAI API Key (for GPT feedback)
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxx

# Queue Driver
QUEUE_CONNECTION=database  # or 'redis'

# Storage
FILESYSTEM_DISK=public
```

### Queue Worker (Production)
```bash
# Start queue worker
php artisan queue:work --queue=default --tries=3 --timeout=600

# Or use Supervisor for auto-restart
[program:tajtrainer-worker]
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --timeout=600
autostart=true
autorestart=true
```

---

## Troubleshooting Guide

### Common Issues

#### 1. "Call to undefined relationship [score]"
**Cause:** Using eager loading on custom accessor
**Solution:** Remove `->with('score')` from queries

#### 2. "Python returned empty output"
**Cause:** Python dependencies not installed
**Solution:** 
```bash
cd python
pip install -r requirements.txt
```

#### 3. "AssemblyAI upload failed"
**Cause:** Invalid API key or network issue
**Solution:** This is now obsolete - system uses Whisper instead

#### 4. Audio file not found
**Cause:** Incorrect storage path
**Solution:** Check `storage/app/public` is symlinked:
```bash
php artisan storage:link
```

#### 5. Queue job not processing
**Cause:** Queue worker not running
**Solution:**
```bash
php artisan queue:work
```

---

## Performance Metrics

### Average Processing Times
- **Audio Recording:** 1-5 seconds (depends on duration)
- **File Upload:** 2-10 seconds (depends on file size)
- **Practice Analysis:** 30-60 seconds (synchronous)
- **Assignment Analysis:** 60-120 seconds (background)
  - Whisper transcription: 20-30 seconds
  - Librosa analysis: 10-20 seconds
  - Reference comparison: 15-25 seconds
  - OpenAI GPT feedback: 5-10 seconds

### Resource Usage
- **Python Memory:** 500MB - 1GB (Whisper model)
- **Storage per Recording:** 100KB - 5MB (audio files)
- **Database JSON Storage:** 10KB - 50KB (analysis results)

---

## Future Enhancements

### Potential Improvements
1. **Real-time Feedback:** WebSocket-based live analysis during recording
2. **Video Recording:** Add video for makharij analysis
3. **Batch Processing:** Process multiple submissions in parallel
4. **Caching:** Cache reference audio downloads
5. **Mobile App:** Native iOS/Android with better audio quality
6. **Advanced ML:** Custom-trained model for Arabic Tajweed
7. **Progress Tracking:** Historical accuracy trends over time

---

## Conclusion

This system provides a comprehensive audio processing pipeline that:
- ✅ Handles both live recording and file uploads
- ✅ Uses industry-standard speech recognition (Whisper)
- ✅ Analyzes audio features with advanced signal processing (Librosa)
- ✅ Applies specific Tajweed rule detection
- ✅ Generates AI-powered feedback (OpenAI GPT)
- ✅ Supports both instant practice feedback and graded assignments
- ✅ Provides detailed error logging and teacher review capabilities

The dual-mode approach (synchronous for practice, asynchronous for assignments) ensures optimal user experience while maintaining system performance and reliability.
