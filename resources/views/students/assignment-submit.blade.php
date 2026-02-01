@extends('layouts.template')

@section('title', 'Submit Assignment')
@section('page-title', 'Submit Assignment')
@section('page-subtitle', 'Complete and submit your work')

@section('content')
<div style="padding: 0;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('classroom.show', $assignment->class_id) }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--color-gold); text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.color='var(--color-light-green)'" onmouseout="this.style.color='var(--color-gold)'">
            ← Back to Classroom
        </a>
    </div>

    <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
            <div style="width: 60px; height: 60px; background: var(--color-dark-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                📝
            </div>
            <div>
                <h2 style="color: var(--color-gold); font-size: 1.5rem; margin-bottom: 5px;">
                    @if($assignment->surah)
                        📖 {{ $assignment->surah }} ({{ $assignment->start_verse }}@if($assignment->end_verse)-{{ $assignment->end_verse }}@endif)
                    @else
                        {{ $assignment->material ? $assignment->material->title : 'Assignment' }}
                    @endif
                </h2>
                <p style="color: var(--color-light-green); opacity: 0.8; font-size: 0.9rem; margin: 0;">{{ $assignment->classroom->class_name }}</p>
            </div>
        </div>

        <div style="background: rgba(70, 63, 58, 0.4); padding: 20px; border-radius: 12px; border: 2px solid rgba(77, 139, 49, 0.2); margin-bottom: 25px;">
            <h3 style="color: var(--color-gold); font-size: 1.2rem; margin-bottom: 15px;">📋 Instructions</h3>
            <p style="color: var(--color-light-green); line-height: 1.8; margin: 0; white-space: pre-wrap;">{{ $assignment->instructions }}</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 25px;">
            <div style="background: rgba(77, 139, 49, 0.2); padding: 18px; border-radius: 12px; border: 2px solid rgba(77, 139, 49, 0.4);">
                <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                    📅 Due Date
                </div>
                <div style="color: var(--color-light-green); font-size: 1.1rem; font-weight: 600;">{{ $assignment->due_date->format('M d, Y') }}</div>
                <div style="color: var(--color-light-green); font-size: 0.95rem; opacity: 0.8; margin-top: 4px;">{{ $assignment->due_date->format('h:i A') }}</div>
            </div>
            <div style="background: rgba(227, 216, 136, 0.15); padding: 18px; border-radius: 12px; border: 2px solid var(--color-gold);">
                <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                    ✨ Tajweed Focus
                </div>
                <div style="color: var(--color-light-green); font-size: 1.05rem; font-weight: 600;">{{ $assignment->tajweed_rules ?? 'General Tajweed' }}</div>
                <div style="color: var(--color-light-green); font-size: 0.85rem; opacity: 0.8; margin-top: 4px;">🎯 100 points • 🎤 Voice Only</div>
            </div>
        </div>

        @if($assignment->material)
        <div style="background: rgba(227, 216, 136, 0.1); padding: 20px; border-radius: 12px; border: 2px solid rgba(227, 216, 136, 0.3); margin-bottom: 25px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span style="font-size: 1.3rem;">📚</span>
                <h4 style="color: var(--color-gold); font-size: 1rem; font-weight: 600; margin: 0;">Study Material</h4>
            </div>
            <p style="color: var(--color-light-green); margin: 0 0 15px 0; opacity: 0.9;">Review this material before submitting your assignment</p>
            <a href="{{ route('student.material.show', ['id' => $assignment->material->material_id, 'from' => 'assignment', 'assignment' => $assignment->assignment_id]) }}" class="btn-secondary" style="text-decoration: none; display: inline-block;">
                View Material →
            </a>
        </div>
        @endif
    </div>

    <form method="POST" action="{{ route('student.assignment.store', $assignment->assignment_id) }}" enctype="multipart/form-data" id="submissionForm">
        @csrf
        <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
                <div style="width: 50px; height: 50px; background: var(--color-dark-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                    ✍️
                </div>
                <div>
                    <h3 style="color: var(--color-gold); font-size: 1.3rem; margin-bottom: 5px;">Your Submission</h3>
                    <p style="color: var(--color-light-green); opacity: 0.8; font-size: 0.9rem; margin: 0;">Complete the fields below</p>
                </div>
            </div>

            @if($assignment->is_voice_submission)
            <div style="margin-bottom: 25px;">
                @if($assignment->surah && $verses)
                <div style="background: rgba(227, 216, 136, 0.15); padding: 25px; border-radius: 12px; border: 3px solid var(--color-gold); margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 15px;">
                        <span style="font-size: 1.3rem;">📖</span>
                        <h4 style="color: var(--color-gold); font-size: 1.1rem; margin: 0; font-weight: 700;">Verses to Recite</h4>
                    </div>
                    <div style="background: rgba(31, 39, 27, 0.4); padding: 25px; border-radius: 10px; margin-bottom: 12px;">
                        <div style="color: #fff; font-size: 1.8rem; line-height: 2.5; font-family: 'Amiri', 'Arabic Typesetting', serif; direction: rtl; text-align: center; letter-spacing: 1px;">
                            {{ $verses }}
                        </div>
                    </div>
                    <div style="color: var(--color-light-green); font-size: 0.9rem; opacity: 0.9; text-align: center; font-style: italic;">
                        🎯 Recite these verses with proper Tajweed rules
                    </div>
                </div>
                @endif
                
                <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 15px; font-size: 1rem;">
                    🎤 Voice Submission <span style="color: #ff6b6b;">*</span>
                </label>
                
                <div id="submissionTypeSelection" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <button type="button" onclick="selectSubmissionType('live')" class="submission-type-btn" id="liveBtn" style="padding: 30px 20px; background: rgba(70, 63, 58, 0.4); border: 2px solid rgba(77, 139, 49, 0.4); border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 10px;">🎙️</div>
                        <div style="color: var(--color-gold); font-weight: 600; font-size: 1.1rem; margin-bottom: 5px;">Live Recitation</div>
                        <div style="color: var(--color-light-green); opacity: 0.8; font-size: 0.85rem;">Record directly in browser</div>
                    </button>
                    
                    <button type="button" onclick="selectSubmissionType('upload')" class="submission-type-btn" id="uploadBtn" style="padding: 30px 20px; background: rgba(70, 63, 58, 0.4); border: 2px solid rgba(77, 139, 49, 0.4); border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 10px;">📤</div>
                        <div style="color: var(--color-gold); font-weight: 600; font-size: 1.1rem; margin-bottom: 5px;">Upload Recording</div>
                        <div style="color: var(--color-light-green); opacity: 0.8; font-size: 0.85rem;">Upload an audio file</div>
                    </button>
                </div>

                <div id="liveRecordingInterface" style="display: none; background: rgba(70, 63, 58, 0.4); padding: 25px; border-radius: 10px; border: 2px solid rgba(77, 139, 49, 0.4);">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <div style="color: var(--color-gold); font-size: 1.1rem; font-weight: 600; margin-bottom: 10px;">🎙️ Live Recording</div>
                        <div id="recordingStatus" style="color: var(--color-light-green); opacity: 0.8; font-size: 0.9rem; margin-bottom: 15px;">Ready to record</div>
                        <div id="recordingTimer" style="color: var(--color-gold); font-size: 2rem; font-weight: 600; margin-bottom: 20px; font-family: monospace;">00:00</div>
                    </div>
                    
                    <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 20px;">
                        <button type="button" id="startRecordBtn" onclick="startRecording()" style="padding: 12px 30px; background: #4caf50; color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            ▶️ Start Recording
                        </button>
                        <button type="button" id="stopRecordBtn" onclick="stopRecording()" disabled style="padding: 12px 30px; background: #e74c3c; color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: all 0.3s ease; opacity: 0.5;">
                            ⏹️ Stop Recording
                        </button>
                    </div>
                    
                    <div id="audioPlayback" style="display: none; text-align: center; padding: 20px; background: rgba(31, 39, 27, 0.5); border-radius: 10px;">
                        <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 10px;">Your Recording:</div>
                        <audio id="audioPlayer" controls style="width: 100%; margin-bottom: 10px;"></audio>
                        <button type="button" onclick="deleteRecording()" style="padding: 8px 20px; background: #e74c3c; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.9rem;">
                            🗑️ Delete & Re-record
                        </button>
                    </div>
                    
                    <input type="hidden" id="recordedAudio" name="recorded_audio">
                </div>

                <div id="uploadInterface" style="display: none; background: rgba(70, 63, 58, 0.4); padding: 25px; border-radius: 10px; border: 2px dashed rgba(77, 139, 49, 0.4); text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">📤</div>
                    <input 
                        type="file" 
                        name="audio_file" 
                        id="audioFileInput"
                        accept="audio/*"
                        style="width: 100%; padding: 15px; color: var(--color-light-green); background: rgba(31, 39, 27, 0.5); border: 2px solid rgba(77, 139, 49, 0.4); border-radius: 8px; cursor: pointer;"
                        onchange="showFileName(this)"
                    >
                    <p style="color: var(--color-light-green); opacity: 0.7; font-size: 0.85rem; margin: 15px 0 0 0;">
                        💡 Supported formats: MP3, WAV, M4A, OGG (Max: 10MB)
                    </p>
                    <div id="selectedFileName" style="color: var(--color-gold); margin-top: 15px; font-weight: 600;"></div>
                </div>

                <button type="button" onclick="changeSubmissionType()" id="changeTypeBtn" style="display: none; margin-top: 15px; padding: 8px 16px; background: transparent; color: var(--color-light-green); border: 2px solid rgba(77, 139, 49, 0.4); border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s ease;">
                    ← Change Submission Type
                </button>
            </div>
            @endif

            <div style="display: flex; gap: 15px; justify-content: flex-end; padding-top: 20px; border-top: 2px solid rgba(77, 139, 49, 0.3);">
                <a href="{{ route('classroom.show', $assignment->class_id) }}" class="btn-secondary" style="text-decoration: none;">
                    Cancel
                </a>
                <button type="submit" class="btn-primary" id="submitBtn">
                    📤 Submit Assignment
                </button>
            </div>
            
            @if($assignment->is_voice_submission && config('services.assemblyai.api_key'))
            <div style="margin-top: 15px; padding: 12px 20px; background: rgba(227, 216, 136, 0.1); border-radius: 10px; border: 2px solid rgba(227, 216, 136, 0.2); text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; color: var(--color-gold); font-size: 0.9rem;">
                    <span style="font-size: 1.1rem;">🤖</span>
                    <span>Your audio will be automatically transcribed and analyzed for Tajweed after submission</span>
                </div>
            </div>
            @endif
        </div>
    </form>
</div>

<style>
    .submission-type-btn:hover {
        background: rgba(70, 63, 58, 0.6) !important;
        border-color: var(--color-gold) !important;
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(227, 216, 136, 0.3);
    }
    
    .submission-type-selected {
        background: rgba(77, 139, 49, 0.3) !important;
        border-color: var(--color-gold) !important;
        box-shadow: 0 4px 15px rgba(227, 216, 136, 0.3);
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
        background: rgba(31, 39, 27, 0.95);
        border: 2px solid var(--color-gold);
        border-radius: 15px;
        padding: 40px;
        text-align: center;
        max-width: 400px;
    }
    
    .spinner {
        border: 4px solid rgba(227, 216, 136, 0.3);
        border-top: 4px solid var(--color-gold);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="submitting-overlay" id="submittingOverlay">
    <div class="submitting-content">
        <div class="spinner"></div>
        <h3 style="color: var(--color-gold); margin-bottom: 10px;">Submitting Assignment...</h3>
        <p style="color: var(--color-light-green); font-size: 0.9rem; opacity: 0.8;">
            @if($assignment->is_voice_submission && config('services.assemblyai.api_key'))
                Processing and transcribing your audio...
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
            
            // Check if getUserMedia is supported
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Your browser does not support audio recording. Please use HTTPS or a modern browser (Chrome, Firefox, Edge).');
            }
            
            // Request microphone access
            audioStream = await navigator.mediaDevices.getUserMedia({ 
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    sampleRate: 44100
                } 
            });
            console.log('✓ Microphone access granted');
            
            // Determine best MIME type
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
                document.getElementById('audioPlayer').src = audioUrl;
                document.getElementById('audioPlayback').style.display = 'block';
                
                // Convert to base64
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
            document.getElementById('stopRecordBtn').style.opacity = '1';
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
                alert('🎤 Microphone permission denied.\n\nPlease allow microphone access and try again.');
            } else if (error.name === 'NotFoundError') {
                alert('🎤 No microphone detected.\n\nPlease connect a microphone and try again.');
            } else {
                alert('⚠️ Recording failed: ' + error.message + '\n\nPlease use the "Upload Recording" option instead.');
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
        document.getElementById('stopRecordBtn').style.opacity = '0.5';
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
        document.getElementById('recordingStatus').style.color = 'var(--color-light-green)';
        
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

    // Validate form before submission
    document.getElementById('submissionForm')?.addEventListener('submit', function(e) {
        @if($assignment->is_voice_submission)
        const recordedAudio = document.getElementById('recordedAudio')?.value;
        const uploadedFile = document.getElementById('audioFileInput')?.files.length > 0;
        
        if (!recordedAudio && !uploadedFile) {
            e.preventDefault();
            alert('Please either record your recitation or upload an audio file.');
            return false;
        }
        
        console.log('Submitting assignment...');
        console.log('Has recorded audio:', recordedAudio ? 'Yes' : 'No');
        console.log('Has uploaded file:', uploadedFile ? 'Yes' : 'No');
        console.log('Audio will be transcribed on server using AssemblyAI');
        @endif
        
        // Show loading overlay
        document.getElementById('submittingOverlay').style.display = 'flex';
    });

    console.log('✓ Recording interface ready');
</script>
@endsection
