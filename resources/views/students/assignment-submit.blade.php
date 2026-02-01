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

    <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 35px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 25px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
            <div style="width: 65px; height: 65px; background: linear-gradient(135deg, var(--color-dark-green), rgba(77, 139, 49, 0.8)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; box-shadow: 0 4px 20px rgba(77, 139, 49, 0.5);">
                📝
            </div>
            <div style="flex: 1;">
                <h2 style="color: var(--color-gold); font-size: 1.6rem; margin-bottom: 8px; font-weight: 700;">
                    @if($assignment->surah)
                        📖 {{ $assignment->surah }}
                        <span style="font-size: 1.2rem; opacity: 0.9;">(Ayah {{ $assignment->start_verse }}@if($assignment->end_verse && $assignment->end_verse != $assignment->start_verse)-{{ $assignment->end_verse }}@endif)</span>
                    @else
                        {{ $assignment->material ? $assignment->material->title : 'Assignment' }}
                    @endif
                </h2>
                <p style="color: var(--color-light-green); opacity: 0.9; font-size: 1rem; margin: 0;">{{ $assignment->classroom->class_name }}</p>
            </div>
        </div>

        <div style="background: rgba(70, 63, 58, 0.4); padding: 25px; border-radius: 12px; border: 2px solid rgba(77, 139, 49, 0.2); margin-bottom: 25px;">
            <h3 style="color: var(--color-gold); font-size: 1.3rem; margin-bottom: 15px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                <span>📋</span> Instructions
            </h3>
            <p style="color: var(--color-light-green); line-height: 1.9; margin: 0; white-space: pre-wrap; font-size: 1.05rem;">{{ $assignment->instructions }}</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 25px;">
            <div style="background: rgba(77, 139, 49, 0.2); padding: 20px; border-radius: 12px; border: 2px solid rgba(77, 139, 49, 0.4);">
                <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 10px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                    📅 Due Date
                </div>
                <div style="color: var(--color-light-green); font-size: 1.2rem; font-weight: 700;">{{ $assignment->due_date->format('M d, Y') }}</div>
                <div style="color: var(--color-light-green); font-size: 1rem; opacity: 0.9; margin-top: 4px;">{{ $assignment->due_date->format('h:i A') }}</div>
            </div>
            <div style="background: rgba(227, 216, 136, 0.15); padding: 20px; border-radius: 12px; border: 2px solid var(--color-gold);">
                <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 10px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                    ✨ Tajweed Focus
                </div>
                <div style="color: var(--color-light-green); font-size: 1.1rem; font-weight: 700;">
                    @if(is_array($assignment->tajweed_rules) && count($assignment->tajweed_rules) > 0)
                        {{ $assignment->tajweed_rules[0] }}
                    @else
                        General Tajweed
                    @endif
                </div>
                <div style="color: var(--color-light-green); font-size: 0.9rem; opacity: 0.8; margin-top: 6px;">🎯 100 points • 🎤 Voice Only</div>
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
        
        @if($errors->any())
        <div style="background: rgba(244, 67, 54, 0.15); border: 2px solid #f44336; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <span style="font-size: 1.5rem;">⚠️</span>
                <h4 style="color: #f44336; font-size: 1.1rem; font-weight: 700; margin: 0;">Submission Error</h4>
            </div>
            <ul style="color: var(--color-light); margin: 0; padding-left: 25px; line-height: 1.8;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        @if(session('error'))
        <div style="background: rgba(244, 67, 54, 0.15); border: 2px solid #f44336; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.5rem;">⚠️</span>
                <p style="color: #f44336; font-size: 1.05rem; font-weight: 600; margin: 0;">{{ session('error') }}</p>
            </div>
        </div>
        @endif
        
        <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 35px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px; padding-bottom: 25px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
                <div style="width: 55px; height: 55px; background: linear-gradient(135deg, var(--color-dark-green), rgba(77, 139, 49, 0.8)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 4px 20px rgba(77, 139, 49, 0.5);">
                    ✍️
                </div>
                <div>
                    <h3 style="color: var(--color-gold); font-size: 1.5rem; margin-bottom: 8px; font-weight: 700;">Your Submission</h3>
                    <p style="color: var(--color-light-green); opacity: 0.9; font-size: 1rem; margin: 0;">Complete the fields below to submit your work</p>
                </div>
            </div>

            @if($assignment->is_voice_submission)
            <div style="margin-bottom: 25px;">
                @if($assignment->surah && $verses)
                <div style="background: linear-gradient(135deg, rgba(227, 216, 136, 0.15), rgba(227, 216, 136, 0.08)); padding: 30px; border-radius: 12px; border: 3px solid var(--color-gold); margin-bottom: 25px; box-shadow: 0 4px 20px rgba(227, 216, 136, 0.2);">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 20px;">
                        <span style="font-size: 1.5rem;">📖</span>
                        <h4 style="color: var(--color-gold); font-size: 1.3rem; margin: 0; font-weight: 700;">Verses to Recite</h4>
                    </div>
                    <div style="background: rgba(31, 39, 27, 0.5); padding: 30px; border-radius: 10px; margin-bottom: 15px; box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.3);">
                        <div style="color: #fff; font-size: 2rem; line-height: 2.8; font-family: 'Amiri', 'Arabic Typesetting', serif; direction: rtl; text-align: center; letter-spacing: 2px;">
                            {{ $verses }}
                        </div>
                    </div>
                    <div style="color: var(--color-light-green); font-size: 1rem; opacity: 0.95; text-align: center; font-style: italic;">
                        🎯 Recite these verses with proper Tajweed rules
                    </div>
                </div>
                @endif
                
                <label style="display: block; color: var(--color-gold); font-weight: 700; margin-bottom: 20px; font-size: 1.2rem; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 1.4rem;">🎤</span> Voice Submission <span style="color: #ff6b6b; font-size: 1.3rem;">*</span>
                </label>
                
                <div id="submissionTypeSelection" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <button type="button" onclick="selectSubmissionType('live')" class="submission-type-btn" id="liveBtn" style="padding: 35px 25px; background: rgba(70, 63, 58, 0.4); border: 3px solid rgba(77, 139, 49, 0.4); border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-align: center;">
                        <div style="font-size: 3.5rem; margin-bottom: 12px;">🎙️</div>
                        <div style="color: var(--color-gold); font-weight: 700; font-size: 1.2rem; margin-bottom: 8px;">Live Recitation</div>
                        <div style="color: var(--color-light-green); opacity: 0.85; font-size: 0.95rem;">Record directly in browser</div>
                    </button>
                    
                    <button type="button" onclick="selectSubmissionType('upload')" class="submission-type-btn" id="uploadBtn" style="padding: 35px 25px; background: rgba(70, 63, 58, 0.4); border: 3px solid rgba(77, 139, 49, 0.4); border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-align: center;">
                        <div style="font-size: 3.5rem; margin-bottom: 12px;">📤</div>
                        <div style="color: var(--color-gold); font-weight: 700; font-size: 1.2rem; margin-bottom: 8px;">Upload Recording</div>
                        <div style="color: var(--color-light-green); opacity: 0.85; font-size: 0.95rem;">Upload an audio file</div>
                    </button>
                </div>

                <div id="liveRecordingInterface" style="display: none; background: rgba(70, 63, 58, 0.5); padding: 30px; border-radius: 12px; border: 3px solid rgba(77, 139, 49, 0.4);">
                    <div style="text-align: center; margin-bottom: 25px;">
                        <div style="color: var(--color-gold); font-size: 1.3rem; font-weight: 700; margin-bottom: 12px;">🎙️ Live Recording</div>
                        <div id="recordingStatus" style="color: var(--color-light-green); opacity: 0.9; font-size: 1.05rem; margin-bottom: 18px;">Ready to record</div>
                        <div id="recordingTimer" style="color: var(--color-gold); font-size: 2.5rem; font-weight: 700; margin-bottom: 25px; font-family: monospace; letter-spacing: 2px;">00:00</div>
                    </div>
                    
                    <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 25px;">
                        <button type="button" id="startRecordBtn" onclick="startRecording()" style="padding: 15px 35px; background: linear-gradient(135deg, #4caf50, #45a049); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 1.05rem; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(76, 175, 80, 0.4);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 25px rgba(76, 175, 80, 0.5)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(76, 175, 80, 0.4)'">
                            ▶️ Start Recording
                        </button>
                        <button type="button" id="stopRecordBtn" onclick="stopRecording()" disabled style="padding: 15px 35px; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: not-allowed; font-size: 1.05rem; transition: all 0.3s ease; opacity: 0.5;">
                            ⏹️ Stop Recording
                        </button>
                    </div>
                    
                    <div id="audioPlayback" style="display: none; text-align: center; padding: 25px; background: rgba(31, 39, 27, 0.6); border-radius: 12px; border: 2px solid rgba(77, 139, 49, 0.4);">
                        <div style="color: var(--color-gold); font-weight: 700; margin-bottom: 15px; font-size: 1.1rem;">✅ Your Recording:</div>
                        <audio id="audioPlayer" controls style="width: 100%; max-width: 500px; margin-bottom: 15px;"></audio>
                        <button type="button" onclick="deleteRecording()" style="padding: 10px 25px; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.95rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 3px 15px rgba(231, 76, 60, 0.3);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            🗑️ Delete & Re-record
                        </button>
                    </div>
                    
                    <input type="hidden" id="recordedAudio" name="recorded_audio">
                </div>

                <div id="uploadInterface" style="display: none; background: rgba(70, 63, 58, 0.5); padding: 30px; border-radius: 12px; border: 3px dashed rgba(77, 139, 49, 0.5); text-align: center;">
                    <div style="font-size: 3.5rem; margin-bottom: 20px;">📤</div>
                    <input 
                        type="file" 
                        name="audio_file" 
                        id="audioFileInput"
                        accept="audio/*"
                        style="width: 100%; padding: 18px; color: var(--color-light-green); background: rgba(31, 39, 27, 0.5); border: 3px solid rgba(77, 139, 49, 0.5); border-radius: 10px; cursor: pointer; font-size: 1.05rem; font-weight: 600;"
                        onchange="showFileName(this)"
                    >
                    <p style="color: var(--color-light-green); opacity: 0.8; font-size: 0.95rem; margin: 18px 0 0 0; line-height: 1.6;">
                        💡 <strong>Supported formats:</strong> MP3, WAV, M4A, OGG<br>
                        <strong>Maximum size:</strong> 10MB
                    </p>
                    <div id="selectedFileName" style="color: var(--color-gold); margin-top: 18px; font-weight: 700; font-size: 1.05rem;"></div>
                </div>

                <button type="button" onclick="changeSubmissionType()" id="changeTypeBtn" style="display: none; margin-top: 20px; padding: 10px 20px; background: transparent; color: var(--color-light-green); border: 2px solid rgba(77, 139, 49, 0.5); border-radius: 10px; cursor: pointer; font-size: 1rem; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.borderColor='var(--color-gold)'; this.style.color='var(--color-gold)'" onmouseout="this.style.borderColor='rgba(77, 139, 49, 0.5)'; this.style.color='var(--color-light-green)'">
                    ← Change Submission Type
                </button>
            </div>
            @endif

            <div style="display: flex; gap: 20px; justify-content: flex-end; padding-top: 25px; border-top: 2px solid rgba(77, 139, 49, 0.3);">
                <a href="{{ route('classroom.show', $assignment->class_id) }}" class="btn-secondary" style="text-decoration: none; padding: 14px 30px; font-size: 1.05rem; font-weight: 600;">
                    ← Cancel
                </a>
                <button type="submit" class="btn-primary" id="submitBtn" style="padding: 14px 35px; font-size: 1.05rem; font-weight: 700; box-shadow: 0 4px 20px rgba(227, 216, 136, 0.4); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 25px rgba(227, 216, 136, 0.5)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(227, 216, 136, 0.4)'">
                    📤 Submit Assignment
                </button>
            </div>
            
            @if($assignment->is_voice_submission && config('services.assemblyai.api_key'))
            <div style="margin-top: 20px; padding: 15px 25px; background: linear-gradient(135deg, rgba(227, 216, 136, 0.12), rgba(227, 216, 136, 0.06)); border-radius: 10px; border: 2px solid rgba(227, 216, 136, 0.25); text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 12px; color: var(--color-gold); font-size: 1rem;">
                    <span style="font-size: 1.3rem;">🤖</span>
                    <span><strong>AI-Powered Analysis:</strong> Your audio will be automatically transcribed and analyzed for Tajweed accuracy after submission</span>
                </div>
            </div>
            @endif
        </div>
    </form>
</div>

<style>
    .submission-type-btn:hover {
        background: rgba(70, 63, 58, 0.7) !important;
        border-color: var(--color-gold) !important;
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(227, 216, 136, 0.4);
    }
    
    .submission-type-selected {
        background: rgba(77, 139, 49, 0.35) !important;
        border-color: var(--color-gold) !important;
        box-shadow: 0 6px 20px rgba(227, 216, 136, 0.4);
        transform: scale(1.02);
    }
    
    #stopRecordBtn:not([disabled]) {
        cursor: pointer;
        opacity: 1;
    }
    
    #stopRecordBtn:not([disabled]):hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 25px rgba(231, 76, 60, 0.5);
    }
    
    @media (max-width: 768px) {
        #submissionTypeSelection {
            grid-template-columns: 1fr !important;
        }
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
        background: rgba(31, 39, 27, 0.95);
        border: 3px solid var(--color-gold);
        border-radius: 15px;
        padding: 40px;
        text-align: center;
        max-width: 450px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }
    
    .spinner {
        border: 4px solid rgba(227, 216, 136, 0.3);
        border-top: 4px solid var(--color-gold);
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
</style>

<div class="submitting-overlay" id="submittingOverlay">
    <div class="submitting-content">
        <div class="spinner"></div>
        <h3 style="color: var(--color-gold); margin-bottom: 15px; font-size: 1.4rem; font-weight: 700;">Submitting Assignment...</h3>
        <p style="color: var(--color-light-green); font-size: 1.05rem; opacity: 0.9; line-height: 1.6;">
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
        const uploadedFile = document.getElementById('audioFileInput')?.files?.length > 0;
        
        console.log('=== Form Submission Check ===');
        console.log('Has recorded audio:', !!recordedAudio);
        console.log('Has uploaded file:', uploadedFile);
        console.log('Recorded audio length:', recordedAudio ? recordedAudio.length : 0);
        console.log('Upload file name:', uploadedFile ? document.getElementById('audioFileInput').files[0].name : 'None');
        console.log('Upload file size:', uploadedFile ? document.getElementById('audioFileInput').files[0].size : 0);
        
        if (!recordedAudio && !uploadedFile) {
            e.preventDefault();
            alert('⚠️ Please either record your recitation or upload an audio file before submitting.');
            console.error('❌ Form validation failed: No audio provided');
            return false;
        }
        
        // Log form data
        const formData = new FormData(e.target);
        console.log('=== Form Data Being Submitted ===');
        for (let [key, value] of formData.entries()) {
            if (key === 'audio_file') {
                console.log('audio_file:', value.name, value.size + ' bytes', value.type);
            } else if (key === 'recorded_audio') {
                console.log('recorded_audio: [base64 data ' + (value.length / 1024).toFixed(2) + ' KB]');
            } else {
                console.log(key + ':', value);
            }
        }
        
        console.log('📤 Submitting assignment...');
        console.log('Has recorded audio:', recordedAudio ? 'Yes (' + (recordedAudio.length / 1024).toFixed(2) + ' KB)' : 'No');
        console.log('Has uploaded file:', uploadedFile ? 'Yes (' + document.getElementById('audioFileInput').files[0].name + ')' : 'No');
        console.log('🤖 Audio will be transcribed on server using AssemblyAI');
        console.log('🎯 Tajweed analysis will be performed using Python analyzer');
        @endif
        
        // Show loading overlay
        document.getElementById('submittingOverlay').style.display = 'flex';
        
        // Disable submit button to prevent double submission
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
            submitBtn.style.cursor = 'not-allowed';
        }
    });

    console.log('✓ Recording interface ready');
</script>
@endsection
