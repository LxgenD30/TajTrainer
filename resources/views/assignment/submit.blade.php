@extends('layouts.dashboard')

@section('title', 'Submit Assignment')
@section('user-role', 'Student • Submit Work')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('content')
<style>
    .container-grid {
        display: grid;
        grid-template-columns: 1fr 500px;
        gap: 25px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    @media (max-width: 1200px) {
        .container-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Left Column - Assignment Info */
    .assignment-info-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #0a5c36;
        text-decoration: none;
        font-weight: 700;
        padding: 10px 16px;
        background: linear-gradient(135deg, #d4af37, #f1c40f);
        border-radius: 10px;
        transition: all 0.3s ease;
        width: fit-content;
        border: 2px solid rgba(0, 0, 0, 0.1);
        box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
    }
    
    .back-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(212, 175, 55, 0.4);
    }
    
    .assignment-header-card {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 15px;
        padding: 25px;
        color: white;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.2);
    }
    
    .assignment-header-card h1 {
        color: white;
        font-size: 1.8rem;
        margin: 0 0 10px 0;
        font-weight: 800;
    }
    
    .assignment-meta {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        font-size: 0.95rem;
        opacity: 0.95;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        border: 3px solid #2a2a2a;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .info-card-title {
        color: #0a5c36;
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .instructions-box {
        background: rgba(10, 92, 54, 0.05);
        padding: 15px;
        border-radius: 10px;
        border-left: 4px solid #1abc9c;
        color: #333;
        line-height: 1.6;
    }
    
    .verses-display {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(26, 188, 156, 0.05));
        padding: 20px;
        border-radius: 12px;
        border: 2px solid #1abc9c;
        text-align: right;
        direction: rtl;
        font-family: 'Amiri', serif;
        font-size: 1.8rem;
        line-height: 2.2;
        color: #0a5c36;
    }
    
    .tajweed-rules-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        border: 3px solid #2a2a2a;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .rule-badge {
        display: inline-block;
        background: linear-gradient(135deg, #d4af37, #f1c40f);
        color: #0a5c36;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.95rem;
        border: 2px solid #b38f2d;
    }
    
    /* Right Column - Submission Form */
    .submission-column {
        position: sticky;
        top: 20px;
        height: fit-content;
    }
    
    .submission-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .submission-card-title {
        color: #0a5c36;
        font-size: 1.4rem;
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .recording-interface {
        background: rgba(10, 92, 54, 0.03);
        padding: 20px;
        border-radius: 12px;
        border: 2px solid rgba(10, 92, 54, 0.2);
        margin-bottom: 20px;
    }
    
    .recording-status {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .status-text {
        color: #0a5c36;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .recording-timer {
        color: #d4af37;
        font-size: 2rem;
        font-weight: 700;
        font-family: monospace;
        letter-spacing: 2px;
    }
    
    .control-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .btn-record, .btn-stop {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    
    .btn-record {
        background: linear-gradient(135deg, #4caf50, #45a049);
        color: white;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }
    
    .btn-record:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }
    
    .btn-stop {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
    }
    
    .btn-stop:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .audio-playback {
        text-align: center;
        padding: 15px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 10px;
        border: 2px solid #1abc9c;
        margin-top: 15px;
    }
    
    .playback-title {
        color: #d4af37;
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }
    
    .audio-player {
        width: 100%;
        outline: none;
        border-radius: 8px;
    }
    
    .btn-delete {
        padding: 8px 20px;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-top: 10px;
    }
    
    .upload-interface {
        background: rgba(10, 92, 54, 0.03);
        padding: 20px;
        border-radius: 12px;
        border: 2px dashed #1abc9c;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .file-input {
        width: 100%;
        padding: 12px;
        color: #333;
        background: white;
        border: 2px solid #1abc9c;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
    }
    
    .upload-info {
        color: #666;
        font-size: 0.85rem;
        margin-top: 10px;
        line-height: 1.5;
    }
    
    .selected-file {
        color: #d4af37;
        margin-top: 12px;
        font-weight: 700;
        font-size: 0.95rem;
    }
    
    .form-actions {
        display: flex;
        gap: 15px;
        padding-top: 20px;
        border-top: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .btn-submit {
        flex: 1;
        padding: 15px;
        background: linear-gradient(135deg, #d4af37, #f1c40f);
        color: #0a5c36;
        border: 2px solid #b38f2d;
        border-radius: 10px;
        font-weight: 800;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(212, 175, 55, 0.3);
    }
    
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    }
    
    .submitting-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    
    .submitting-content {
        background: white;
        padding: 40px;
        border-radius: 20px;
        text-align: center;
        max-width: 400px;
    }
    
    .spinner {
        border: 4px solid rgba(10, 92, 54, 0.1);
        border-left-color: #1abc9c;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="container-grid">
    <!-- LEFT COLUMN - Assignment Info -->
    <div class="assignment-info-column">
        <div class="assignment-header-card">
            <a href="{{ url()->previous() }}" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Classroom
            </a>
            
            <h1 style="margin-top: 15px;">{{ $assignment->surah ? $assignment->surah . ' ' . $assignment->start_verse . '-' . ($assignment->end_verse ?? $assignment->start_verse) : 'Assignment' }}</h1>
            <div class="assignment-meta">
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y g:i A') }}
                </div>
                <div class="meta-item">
                    <i class="fas fa-star"></i>
                    {{ $assignment->total_marks }} Points
                </div>
            </div>
        </div>
        
        @if($assignment->instructions)
        <div class="info-card">
            <div class="info-card-title">
                <i class="fas fa-info-circle"></i>
                Instructions
            </div>
            <div class="instructions-box">
                {{ $assignment->instructions }}
            </div>
        </div>
        @endif
        
        @if($verses)
        <div class="info-card">
            <div class="info-card-title">
                <i class="fas fa-book-quran"></i>
                Verses to Recite
            </div>
            <div class="verses-display">
                {!! nl2br(e($verses)) !!}
            </div>
        </div>
        @endif
        
        @if($assignment->tajweed_rules)
        <div class="tajweed-rules-card">
            <div class="info-card-title">
                <i class="fas fa-star"></i>
                Focus on These Tajweed Rules
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                @foreach((array)$assignment->tajweed_rules as $rule)
                    <span class="rule-badge">{{ $rule }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    
    <!-- RIGHT COLUMN - Submission Form -->
    <div class="submission-column">
        <div class="submission-card">
            <div class="submission-card-title">
                <i class="fas fa-microphone"></i>
                Submit Your Work
            </div>
            
            <form id="submissionForm" action="{{ route('student.assignment.store', $assignment->assignment_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if($assignment->is_voice_submission)
                <!-- Submission Method Choice -->
                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: #0a5c36; font-weight: 700; margin-bottom: 15px; font-size: 1.05rem;">
                        Choose Submission Method
                    </label>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <label style="display: flex; align-items: center; gap: 10px; padding: 15px; background: white; border: 2px solid #1abc9c; border-radius: 10px; cursor: pointer; transition: all 0.3s ease;" onclick="showRecordingInterface()">
                            <input type="radio" name="submission_method" value="record" checked style="width: 18px; height: 18px; cursor: pointer; accent-color: #1abc9c;">
                            <div>
                                <div style="color: #0a5c36; font-weight: 700; font-size: 1rem;">🎤 Record Now</div>
                                <div style="color: #666; font-size: 0.85rem;">Record your recitation directly in the browser</div>
                            </div>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 10px; padding: 15px; background: white; border: 2px solid #1abc9c; border-radius: 10px; cursor: pointer; transition: all 0.3s ease;" onclick="showUploadInterface()">
                            <input type="radio" name="submission_method" value="upload" style="width: 18px; height: 18px; cursor: pointer; accent-color: #1abc9c;">
                            <div>
                                <div style="color: #0a5c36; font-weight: 700; font-size: 1rem;">📤 Upload File</div>
                                <div style="color: #666; font-size: 0.85rem;">Upload a pre-recorded audio file</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Recording Interface -->
                <div class="recording-interface" id="recordingInterface">
                    <div class="recording-status">
                        <div class="status-text" id="recordingStatus">Ready to record</div>
                        <div class="recording-timer" id="recordingTimer">00:00</div>
                    </div>
                    
                    <div class="control-buttons">
                        <button type="button" class="btn-record" id="startRecordBtn" onclick="startRecording()">
                            <i class="fas fa-circle"></i> Start Recording
                        </button>
                        <button type="button" class="btn-stop" id="stopRecordBtn" onclick="stopRecording()" disabled>
                            <i class="fas fa-stop"></i> Stop
                        </button>
                    </div>
                    
                    <div id="audioPlayback" class="audio-playback" style="display: none;">
                        <div class="playback-title">🎵 Preview Your Recording</div>
                        <audio id="audioPlayer" class="audio-player" controls></audio>
                        <button type="button" class="btn-delete" onclick="deleteRecording()">
                            <i class="fas fa-trash"></i> Delete & Re-record
                        </button>
                    </div>
                    
                    <input type="hidden" name="recorded_audio" id="recordedAudio">
                    <input type="hidden" name="transcription" id="transcriptionInput">
                </div>
                
                <!-- Upload Interface -->
                <div class="upload-interface" id="uploadInterface" style="display: none;">
                    <div style="font-size: 2.5rem; margin-bottom: 12px;">📤</div>
                    <input type="file" name="audio_file" id="audioFileInput" class="file-input" accept="audio/*,video/webm,video/mp4" onchange="showFileName(this)">
                    <div class="upload-info">
                        Supported: MP3, WAV, M4A, WEBM, MP4<br>
                        Maximum size: 10MB
                    </div>
                    <div id="selectedFileName" class="selected-file"></div>
                </div>
                @else
                <!-- Text Submission -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: #0a5c36; font-weight: 700; margin-bottom: 10px;">
                        Your Response
                    </label>
                    <textarea name="text_submission" rows="8" style="width: 100%; padding: 12px; border: 2px solid #1abc9c; border-radius: 8px; font-family: inherit; font-size: 1rem;" required>{{ old('text_submission') }}</textarea>
                </div>
                @endif
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Submit Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Submitting Overlay -->
<div id="submittingOverlay" class="submitting-overlay">
    <div class="submitting-content">
        <div class="spinner"></div>
        <h3 style="color: #0a5c36; margin-bottom: 10px;">Processing Your Submission...</h3>
        <p style="color: #666;">Analyzing your recitation with AI<br>This may take 15-30 seconds</p>
    </div>
</div>

<script>
    let mediaRecorder;
    let audioChunks = [];
    let audioBlob;
    let audioStream;
    let recordingInterval;
    let recordingSeconds = 0;

    async function startRecording() {
        try {
            audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(audioStream);
            audioChunks = [];
            
            mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    audioChunks.push(event.data);
                }
            };

            mediaRecorder.onstop = () => {
                audioBlob = new Blob(audioChunks, { type: 'audio/webm;codecs=opus' });
                const audioUrl = URL.createObjectURL(audioBlob);
                
                document.getElementById('audioPlayer').src = audioUrl;
                document.getElementById('audioPlayback').style.display = 'block';
                
                const reader = new FileReader();
                reader.readAsDataURL(audioBlob);
                reader.onloadend = () => {
                    document.getElementById('recordedAudio').value = reader.result;
                };
            };

            mediaRecorder.start(1000);
            
            document.getElementById('startRecordBtn').disabled = true;
            document.getElementById('stopRecordBtn').disabled = false;
            document.getElementById('recordingStatus').textContent = '🔴 Recording in progress...';
            document.getElementById('recordingStatus').style.color = '#e74c3c';
            
            recordingInterval = setInterval(() => {
                recordingSeconds++;
                const minutes = Math.floor(recordingSeconds / 60).toString().padStart(2, '0');
                const seconds = (recordingSeconds % 60).toString().padStart(2, '0');
                document.getElementById('recordingTimer').textContent = `${minutes}:${seconds}`;
            }, 1000);
            
        } catch (error) {
            console.error('Recording error:', error);
            alert('🎤 Microphone access denied. Please allow microphone access and try again.');
        }
    }

    function stopRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
        
        if (audioStream) {
            audioStream.getTracks().forEach(track => track.stop());
            audioStream = null;
        }
        
        if (recordingInterval) {
            clearInterval(recordingInterval);
        }
        
        document.getElementById('startRecordBtn').disabled = false;
        document.getElementById('stopRecordBtn').disabled = true;
        document.getElementById('recordingStatus').textContent = '✅ Recording completed';
        document.getElementById('recordingStatus').style.color = '#4caf50';
    }

    function deleteRecording() {
        if (audioStream) {
            audioStream.getTracks().forEach(track => track.stop());
            audioStream = null;
        }
        
        audioBlob = null;
        audioChunks = [];
        recordingSeconds = 0;
        
        document.getElementById('recordingTimer').textContent = '00:00';
        document.getElementById('audioPlayback').style.display = 'none';
        document.getElementById('audioPlayer').src = '';
        document.getElementById('recordedAudio').value = '';
        document.getElementById('recordingStatus').textContent = 'Ready to record';
        document.getElementById('recordingStatus').style.color = '#0a5c36';
        
        if (recordingInterval) {
            clearInterval(recordingInterval);
        }
    }

    function showFileName(input) {
        const fileName = input.files[0]?.name || '';
        if (fileName) {
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
            document.getElementById('selectedFileName').textContent = '✓ Selected: ' + fileName + ' (' + fileSize + ' MB)';
        }
    }

    function showRecordingInterface() {
        document.getElementById('recordingInterface').style.display = 'block';
        document.getElementById('uploadInterface').style.display = 'none';
        document.getElementById('audioFileInput').value = '';
        document.getElementById('selectedFileName').textContent = '';
    }

    function showUploadInterface() {
        document.getElementById('recordingInterface').style.display = 'none';
        document.getElementById('uploadInterface').style.display = 'block';
        deleteRecording();
    }

    document.getElementById('submissionForm')?.addEventListener('submit', function(e) {
        @if($assignment->is_voice_submission)
        const submissionMethod = document.querySelector('input[name="submission_method"]:checked')?.value;
        const recordedAudio = document.getElementById('recordedAudio')?.value;
        const uploadedFile = document.getElementById('audioFileInput')?.files?.length > 0;
        
        if (submissionMethod === 'record' && !recordedAudio) {
            e.preventDefault();
            alert('⚠️ Please record your audio before submitting.');
            return false;
        }
        
        if (submissionMethod === 'upload' && !uploadedFile) {
            e.preventDefault();
            alert('⚠️ Please either record your recitation or upload an audio file before submitting.');
            return false;
        }
        @endif
        
        document.getElementById('submittingOverlay').style.display = 'flex';
        
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
            submitBtn.style.cursor = 'not-allowed';
        }
    });
</script>
@endsection
