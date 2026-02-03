@extends('layouts.dashboard')

@section('title', 'Submit Assignment')
@section('user-role', 'Student • Submit Work')

@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="nav-item">
        <i class="fas fa-home nav-icon"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    
    <a href="{{ route('student.classes') }}" class="nav-item active">
        <i class="fas fa-users nav-icon"></i>
        <span class="nav-label">My Classes</span>
    </a>
    
    <a href="{{ route('student.practice') }}" class="nav-item">
        <i class="fas fa-microphone-alt nav-icon"></i>
        <span class="nav-label">Practice</span>
    </a>
    
    <a href="{{ route('student.progress') }}" class="nav-item">
        <i class="fas fa-chart-line nav-icon"></i>
        <span class="nav-label">My Progress</span>
    </a>
    
    <a href="{{ route('student.materials') }}" class="nav-item">
        <i class="fas fa-book-open nav-icon"></i>
        <span class="nav-label">Materials</span>
    </a>
@endsection

@section('content')
<style>
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #1abc9c;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .back-link:hover {
        color: #0a5c36;
    }
    
    .assignment-header {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.2);
    }
    
    .header-content h1 {
        color: white;
        font-size: 1.8rem;
        margin-bottom: 8px;
    }
    
    .header-content p {
        opacity: 0.9;
        font-size: 1rem;
        margin: 0;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .info-label {
        color: #666;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-value {
        color: #0a5c36;
        font-size: 1.1rem;
        font-weight: 700;
    }
    
    .content-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .card-title {
        color: #0a5c36;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .instructions-box {
        background: rgba(10, 92, 54, 0.05);
        padding: 20px;
        border-radius: 10px;
        border: 2px solid #0a5c36;
        color: #333;
        line-height: 1.8;
        white-space: pre-wrap;
    }
    
    .verses-box {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(212, 175, 55, 0.05));
        border: 3px solid #d4af37;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        text-align: center;
    }
    
    .verses-title {
        color: #d4af37;
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .arabic-text {
        background: rgba(10, 92, 54, 0.05);
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 15px;
    }
    
    .arabic-text div {
        color: #0a5c36;
        font-size: 2rem;
        line-height: 2.8;
        font-family: 'Amiri', 'Arabic Typesetting', serif;
        direction: rtl;
        text-align: center;
        letter-spacing: 2px;
        font-weight: 600;
    }
    
    .material-card {
        background: rgba(26, 188, 156, 0.05);
        padding: 20px;
        border-radius: 12px;
        border: 2px solid #1abc9c;
    }
    
    .material-title {
        color: #1abc9c;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .material-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #1abc9c;
        color: white;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .material-link:hover {
        background: #16a085;
    }
    
    .submission-type-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .submission-type-btn {
        padding: 35px 25px;
        background: white;
        border: 3px solid rgba(10, 92, 54, 0.2);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .submission-type-btn:hover {
        background: rgba(10, 92, 54, 0.05);
        border-color: #d4af37;
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    }
    
    .submission-type-selected {
        background: rgba(10, 92, 54, 0.08);
        border-color: #d4af37;
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.3);
    }
    
    .type-icon {
        font-size: 3.5rem;
        margin-bottom: 12px;
    }
    
    .type-title {
        color: #d4af37;
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 8px;
    }
    
    .type-desc {
        color: #666;
        font-size: 0.95rem;
    }
    
    .recording-interface {
        background: rgba(10, 92, 54, 0.03);
        padding: 30px;
        border-radius: 12px;
        border: 3px solid rgba(10, 92, 54, 0.2);
    }
    
    .recording-status {
        text-align: center;
        margin-bottom: 25px;
    }
    
    .status-text {
        color: #0a5c36;
        font-size: 1.05rem;
        margin-bottom: 18px;
    }
    
    .recording-timer {
        color: #d4af37;
        font-size: 2.5rem;
        font-weight: 700;
        font-family: monospace;
        letter-spacing: 2px;
        margin-bottom: 25px;
    }
    
    .control-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .btn-record, .btn-stop {
        padding: 15px 35px;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        font-size: 1.05rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-record {
        background: linear-gradient(135deg, #4caf50, #45a049);
        color: white;
        box-shadow: 0 4px 20px rgba(76, 175, 80, 0.4);
    }
    
    .btn-record:hover:not(:disabled) {
        transform: translateY(-3px);
        box-shadow: 0 6px 25px rgba(76, 175, 80, 0.5);
    }
    
    .btn-stop {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
    }
    
    .btn-stop:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .btn-stop:hover:not(:disabled) {
        transform: translateY(-3px);
        box-shadow: 0 6px 25px rgba(231, 76, 60, 0.5);
    }
    
    .audio-playback {
        text-align: center;
        padding: 25px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        border: 2px solid #1abc9c;
    }
    
    .playback-title {
        color: #d4af37;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .audio-player {
        width: 100%;
        max-width: 500px;
        margin-bottom: 15px;
        outline: none;
        border-radius: 10px;
    }
    
    .btn-delete {
        padding: 10px 25px;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
    }
    
    .upload-interface {
        background: rgba(10, 92, 54, 0.03);
        padding: 30px;
        border-radius: 12px;
        border: 3px dashed #1abc9c;
        text-align: center;
    }
    
    .upload-icon {
        font-size: 3.5rem;
        margin-bottom: 20px;
    }
    
    .file-input {
        width: 100%;
        padding: 18px;
        color: #333;
        background: white;
        border: 3px solid #1abc9c;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1.05rem;
        font-weight: 600;
    }
    
    .upload-info {
        color: #666;
        font-size: 0.95rem;
        margin: 18px 0 0 0;
        line-height: 1.6;
    }
    
    .selected-file {
        color: #d4af37;
        margin-top: 18px;
        font-weight: 700;
        font-size: 1.05rem;
    }
    
    .btn-change-type {
        margin-top: 20px;
        padding: 10px 20px;
        background: transparent;
        color: #0a5c36;
        border: 2px solid #0a5c36;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-change-type:hover {
        border-color: #d4af37;
        color: #d4af37;
    }
    
    .form-actions {
        display: flex;
        gap: 20px;
        justify-content: flex-end;
        padding-top: 25px;
        border-top: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .btn-cancel, .btn-submit {
        padding: 14px 30px;
        border-radius: 50px;
        font-size: 1.05rem;
        font-weight: 700;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-cancel {
        background: rgba(10, 92, 54, 0.08);
        color: #1a1a1a;
    }
    
    .btn-cancel:hover {
        background: rgba(10, 92, 54, 0.15);
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #d4af37, #c19b2e);
        color: #0a5c36;
        box-shadow: 0 4px 20px rgba(212, 175, 55, 0.4);
    }
    
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 25px rgba(212, 175, 55, 0.5);
    }
    
    .ai-notice {
        margin-top: 20px;
        padding: 15px 25px;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.12), rgba(212, 175, 55, 0.06));
        border-radius: 10px;
        border: 2px solid rgba(212, 175, 55, 0.25);
        text-align: center;
        color: #666;
        font-size: 1rem;
    }
    
    .ai-notice strong {
        color: #d4af37;
    }
    
    .error-alert {
        background: rgba(244, 67, 54, 0.1);
        border: 2px solid #f44336;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }
    
    .error-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        color: #f44336;
        font-size: 1.1rem;
        font-weight: 700;
    }
    
    .error-list {
        color: #333;
        margin: 0;
        padding-left: 25px;
        line-height: 1.8;
    }
    
    .submitting-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.85);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    
    .submitting-content {
        background: white;
        border: 3px solid #d4af37;
        border-radius: 15px;
        padding: 40px;
        text-align: center;
        max-width: 450px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }
    
    .spinner {
        border: 4px solid rgba(212, 175, 55, 0.3);
        border-top: 4px solid #d4af37;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
        margin: 0 auto 25px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @media (max-width: 768px) {
        .submission-type-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Assignment Header -->
<div class="assignment-header">
    <div class="header-content">
        <h1>
            @if($assignment->surah)
                📖 {{ $assignment->surah }} ({{ $assignment->start_verse }}@if($assignment->end_verse)-{{ $assignment->end_verse }}@endif)
            @else
                {{ $assignment->material ? $assignment->material->title : 'Assignment' }}
            @endif
        </h1>
        <p>{{ $assignment->classroom->class_name }}</p>
    </div>
</div>

<!-- Info Grid -->
<div class="info-grid">
    <div class="info-card">
        <div class="info-label"><i class="fas fa-calendar"></i> Due Date</div>
        <div class="info-value">{{ $assignment->due_date->format('M d, Y h:i A') }}</div>
    </div>
    <div class="info-card">
        <div class="info-label"><i class="fas fa-star"></i> Tajweed Focus</div>
        <div class="info-value">
            @if(is_array($assignment->tajweed_rules) && count($assignment->tajweed_rules) > 0)
                {{ $assignment->tajweed_rules[0] }}
            @else
                General Tajweed
            @endif
        </div>
    </div>
</div>

<!-- Instructions -->
<div class="content-card">
    <h3 class="card-title">📋 Instructions</h3>
    <div class="instructions-box">{{ $assignment->instructions }}</div>
</div>

<!-- Assignment Material -->
@if($assignment->material)
<div class="content-card">
    <div class="material-card">
        <h4 class="material-title">📚 Study Material: {{ $assignment->material->title }}</h4>
        <p style="color: #666; margin-bottom: 15px;">Review this material before submitting your assignment</p>
        <a href="{{ route('student.material.show', ['id' => $assignment->material->material_id, 'from' => 'assignment', 'assignment' => $assignment->assignment_id]) }}" class="material-link">
            View Material <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endif

<!-- Submission Form -->
<form method="POST" action="{{ route('student.assignment.store', $assignment->assignment_id) }}" enctype="multipart/form-data" id="submissionForm">
    @csrf
    
    @if($errors->any())
    <div class="error-alert">
        <div class="error-title">
            <span>⚠️</span> Submission Error
        </div>
        <ul class="error-list">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    @if(session('error'))
    <div class="error-alert">
        <div class="error-title">
            <span>⚠️</span> {{ session('error') }}
        </div>
    </div>
    @endif
    
    @if($assignment->is_voice_submission)
    <div class="content-card">
        @if($assignment->surah && $verses)
        <div class="verses-box">
            <div class="verses-title">
                <span>📖</span> Verses to Recite
            </div>
            <div class="arabic-text">
                <div>{{ $verses }}</div>
            </div>
            <p style="color: #666; margin: 0;">🎯 Recite these verses with proper Tajweed rules</p>
        </div>
        @endif
        
        <h3 class="card-title">🎤 Voice Submission <span style="color: #ff6b6b;">*</span></h3>
        
        <div id="submissionTypeSelection" class="submission-type-grid">
            <button type="button" onclick="selectSubmissionType('live')" class="submission-type-btn" id="liveBtn">
                <div class="type-icon">🎙️</div>
                <div class="type-title">Live Recitation</div>
                <div class="type-desc">Record directly in browser</div>
            </button>
            
            <button type="button" onclick="selectSubmissionType('upload')" class="submission-type-btn" id="uploadBtn">
                <div class="type-icon">📤</div>
                <div class="type-title">Upload Recording</div>
                <div class="type-desc">Upload an audio file</div>
            </button>
        </div>

        <div id="liveRecordingInterface" style="display: none;" class="recording-interface">
            <div class="recording-status">
                <div class="status-text" id="recordingStatus">Ready to record</div>
                <div class="recording-timer" id="recordingTimer">00:00</div>
            </div>
            
            <div class="control-buttons">
                <button type="button" id="startRecordBtn" onclick="startRecording()" class="btn-record">
                    ▶️ Start Recording
                </button>
                <button type="button" id="stopRecordBtn" onclick="stopRecording()" disabled class="btn-stop">
                    ⏹️ Stop Recording
                </button>
            </div>
            
            <div id="audioPlayback" style="display: none;" class="audio-playback">
                <div class="playback-title">✅ Your Recording:</div>
                <audio id="audioPlayer" controls preload="auto" class="audio-player">
                    Your browser does not support the audio element.
                </audio>
                <div id="audioError" style="display: none; color: #e74c3c; margin-bottom: 10px; padding: 10px; background: rgba(231, 76, 60, 0.1); border-radius: 6px;">⚠️ Audio playback error. Try download button below.</div>
                <button type="button" onclick="deleteRecording()" class="btn-delete">
                    🗑️ Delete & Re-record
                </button>
            </div>
            
            <input type="hidden" id="recordedAudio" name="recorded_audio">
        </div>

        <div id="uploadInterface" style="display: none;" class="upload-interface">
            <div class="upload-icon">📤</div>
            <input 
                type="file" 
                name="audio_file" 
                id="audioFileInput"
                accept="audio/*,video/*,.mp3,.wav,.m4a,.ogg,.webm,.mp4"
                class="file-input"
                onchange="showFileName(this)"
            >
            <p class="upload-info">
                💡 <strong>Supported formats:</strong> MP3, WAV, M4A, OGG, WEBM, MP4<br>
                <strong>Maximum size:</strong> 10MB
            </p>
            <div id="selectedFileName" class="selected-file"></div>
        </div>

        <button type="button" onclick="changeSubmissionType()" id="changeTypeBtn" style="display: none;" class="btn-change-type">
            ← Change Submission Type
        </button>
    </div>
    @endif

    <div class="content-card">
        <div class="form-actions">
            <a href="{{ route('classroom.show', $assignment->class_id) }}" class="btn-cancel">
                ← Cancel
            </a>
            <button type="submit" class="btn-submit" id="submitBtn">
                📤 Submit Assignment
            </button>
        </div>
        
        @if($assignment->is_voice_submission && config('services.assemblyai.api_key'))
        <div class="ai-notice">
            <span style="font-size: 1.3rem;">🤖</span>
            <strong>AI-Powered Analysis:</strong> Your audio will be automatically transcribed and analyzed for Tajweed accuracy after submission
        </div>
        @endif
    </div>
</form>

<div class="submitting-overlay" id="submittingOverlay">
    <div class="submitting-content">
        <div class="spinner"></div>
        <h3 style="color: #d4af37; margin-bottom: 15px; font-size: 1.4rem; font-weight: 700;">Submitting Assignment...</h3>
        <p style="color: #666; font-size: 1.05rem; line-height: 1.6;">
            @if($assignment->is_voice_submission && config('services.assemblyai.api_key'))
                🎙️ Processing and transcribing your audio...<br>
                <span style="font-size: 0.9rem; opacity: 0.7;">This may take a few moments</span>
            @else
                Please wait while we process your submission...
            @endif
        </p>
    </div>
</div>

<script>
    let mediaRecorder;
    let audioChunks = [];
    let recordingInterval;
    let recordingSeconds = 0;
    let audioBlob = null;
    let audioStream = null;

    function selectSubmissionType(type) {
        document.getElementById('submissionTypeSelection').style.display = 'none';
        document.getElementById('changeTypeBtn').style.display = 'block';
        
        if (type === 'live') {
            document.getElementById('liveRecordingInterface').style.display = 'block';
            document.getElementById('uploadInterface').style.display = 'none';
            document.getElementById('audioFileInput').required = false;
        } else {
            document.getElementById('uploadInterface').style.display = 'block';
            document.getElementById('liveRecordingInterface').style.display = 'none';
            document.getElementById('audioFileInput').required = true;
        }
    }

    function changeSubmissionType() {
        document.getElementById('submissionTypeSelection').style.display = 'grid';
        document.getElementById('liveRecordingInterface').style.display = 'none';
        document.getElementById('uploadInterface').style.display = 'none';
        document.getElementById('changeTypeBtn').style.display = 'none';
        deleteRecording();
    }

    async function startRecording() {
        try {
            console.log('Starting recording...');
            
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Your browser does not support audio recording. Please use HTTPS or a modern browser (Chrome, Firefox, Edge).');
            }
            
            audioStream = await navigator.mediaDevices.getUserMedia({ 
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    sampleRate: 44100
                } 
            });
            console.log('✓ Microphone access granted');
            
            let mimeType = 'audio/webm;codecs=opus';
            if (!MediaRecorder.isTypeSupported(mimeType)) {
                mimeType = 'audio/webm';
            }
            console.log('Using MIME type:', mimeType);

            mediaRecorder = new MediaRecorder(audioStream, { mimeType: mimeType });
            audioChunks = [];
            recordingSeconds = 0;

            mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    audioChunks.push(event.data);
                    console.log('Audio chunk collected:', event.data.size, 'bytes');
                }
            };

            mediaRecorder.onstop = () => {
                console.log('Recording stopped');
                
                if (audioChunks.length === 0) {
                    alert('⚠️ No audio was recorded. Please try again.');
                    return;
                }

                audioBlob = new Blob(audioChunks, { type: mimeType });
                console.log('Audio blob created:', audioBlob.size, 'bytes');
                
                const audioUrl = URL.createObjectURL(audioBlob);
                const audioPlayer = document.getElementById('audioPlayer');
                
                audioPlayer.src = audioUrl;
                audioPlayer.load();
                console.log('✓ Audio ready for playback');
                
                audioPlayer.addEventListener('error', function(e) {
                    console.error('Audio playback error:', e);
                    document.getElementById('audioError').style.display = 'block';
                });
                
                document.getElementById('audioPlayback').style.display = 'block';
                
                const reader = new FileReader();
                reader.onloadend = () => {
                    document.getElementById('recordedAudio').value = reader.result;
                    console.log('✓ Audio ready for submission');
                };
                reader.readAsDataURL(audioBlob);
            };

            mediaRecorder.start(1000);
            console.log('✓ Recording started');
            
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
            
            if (error.name === 'NotAllowedError') {
                alert('🎤 Microphone permission denied.\\n\\nPlease allow microphone access and try again.');
            } else if (error.name === 'NotFoundError') {
                alert('🎤 No microphone detected.\\n\\nPlease connect a microphone and try again.');
            } else {
                alert('⚠️ Recording failed: ' + error.message + '\\n\\nPlease use the "Upload Recording" option instead.');
            }
        }
    }

    function stopRecording() {
        console.log('Stopping recording...');
        
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
        
        console.log('✓ Recording stopped successfully');
    }

    function deleteRecording() {
        console.log('Deleting recording...');
        
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
        
        console.log('✓ Recording deleted');
    }

    function showFileName(input) {
        const fileName = input.files[0]?.name || '';
        if (fileName) {
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
            document.getElementById('selectedFileName').textContent = '✓ Selected: ' + fileName + ' (' + fileSize + ' MB)';
            console.log('File selected:', fileName, fileSize, 'MB');
        }
    }

    document.getElementById('submissionForm')?.addEventListener('submit', function(e) {
        @if($assignment->is_voice_submission)
        const recordedAudio = document.getElementById('recordedAudio')?.value;
        const uploadedFile = document.getElementById('audioFileInput')?.files?.length > 0;
        
        console.log('=== Assignment Submission Check ===');
        console.log('Assignment ID: {{ $assignment->assignment_id }}');
        console.log('Surah: {{ $assignment->surah }} ({{ $assignment->start_verse }}-{{ $assignment->end_verse ?? $assignment->start_verse }})');
        console.log('Has recorded audio:', !!recordedAudio);
        console.log('Has uploaded file:', uploadedFile);
        
        if (!recordedAudio && !uploadedFile) {
            e.preventDefault();
            alert('⚠️ Please either record your recitation or upload an audio file before submitting.');
            console.error('❌ Form validation failed: No audio provided');
            return false;
        }
        
        console.log('✅ Validation passed');
        console.log('📤 Submitting assignment to server...');
        console.log('⏳ Please wait while we process your submission...');
        console.log('🔄 Server will analyze your recitation using Whisper AI + Tajweed rules');
        console.log('📊 This may take 15-30 seconds for first-time analysis (downloading AI model)');
        @endif
        
        document.getElementById('submittingOverlay').style.display = 'flex';
        
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
            submitBtn.style.cursor = 'not-allowed';
        }
        
        console.log('🎯 Form submitted successfully - waiting for server response...');
    });

    console.log('✓ Recording interface ready');
</script>
@endsection
