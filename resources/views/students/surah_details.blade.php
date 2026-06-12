@extends('layouts.dashboard')

@section('title', 'Surah Details')
@section('user-role', 'Student • Memorization')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('content')
<style>
    .surah-header {
        text-align: center;
        margin-bottom: 30px;
        padding: 20px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: white;
        border-radius: 15px;
        border: 3px solid #2a2a2a;
    }
    .surah-title {
        font-size: 3rem;
        font-weight: 800;
        font-family: 'Amiri', serif; /* A nice font for Arabic */
    }
    .surah-meta {
        font-size: 1.2rem;
    }
    .ayah-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
    }
    .ayah-card {
        background: #fff;
        border-radius: 15px;
        padding: 20px;
        border: 3px solid #2a2a2a;
        box-shadow: 0 8px 15px rgba(0,0,0,0.07);
    }
    .ayah-text {
        font-size: 1.8rem;
        font-family: 'Amiri', serif;
        text-align: right;
        line-height: 2.5;
        margin-bottom: 15px;
    }
    .ayah-number {
        display: inline-block;
        width: 30px;
        height: 30px;
        line-height: 30px;
        text-align: center;
        border-radius: 50%;
        background-color: #1abc9c;
        color: white;
        font-weight: bold;
        margin-left: 10px;
    }
    .ayah-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-top: 15px;
        border-top: 2px solid #f0f0f0;
        padding-top: 15px;
    }
    .status-toggle {
        padding: 8px 15px;
        border-radius: 10px;
        border: 2px solid #2a2a2a;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    .status-not-memorized { background-color: #e0e0e0; color: #333; }
    .status-in-progress { background-color: #f1c40f; color: #fff; }
    .status-memorized { background-color: #2ecc71; color: #fff; }

    .play-btn {
        padding: 8px 15px;
        font-size: 0.9rem;
        border-radius: 10px;
        border: 2px solid #2a2a2a;
        cursor: pointer;
        font-weight: bold;
        background-color: #3498db;
        color: white;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .play-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .play-btn.playing {
        background-color: #f1c40f;
    }
    .play-btn:disabled {
        background-color: #bdc3c7;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .record-section {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    #recordButton {
        padding: 12px 25px;
        font-size: 1.1rem;
        border-radius: 12px;
        border: 3px solid #2a2a2a;
        cursor: pointer;
        font-weight: bold;
        background-color: #1abc9c;
        color: white;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    #recordButton:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    #recordButton.recording {
        background-color: #e74c3c;
        animation: pulse 1.5s infinite;
    }
    #timer {
        font-weight: bold;
        font-size: 1.1rem;
        color: #333;
        background-color: #fff;
        padding: 8px 15px;
        border-radius: 8px;
        border: 3px solid #2a2a2a;
        min-width: 80px;
        text-align: center;
    }
    #recording-output {
        margin-top: 20px;
        padding: 25px;
        background: #fff;
        border-radius: 15px;
        border: 3px solid #2a2a2a;
        box-shadow: 0 8px 15px rgba(0,0,0,0.07);
        display: none; /* Hidden by default */
        font-family: 'Amiri', serif;
        font-size: 1.5rem;
        text-align: right;
        line-height: 2;
        min-height: 150px;
    }
    #recording-output .placeholder {
        color: #999;
        font-style: italic;
        font-size: 1.2rem;
        text-align: center;
    }
    #recording-output .final-transcript {
        color: #000;
    }
    #recording-output .partial-transcript {
        color: #777;
    }
    #recording-output audio {
        width: 100%;
        margin-top: 10px;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7); }
        70% { box-shadow: 0 0 0 15px rgba(231, 76, 60, 0); }
        100% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0); }
    }
</style>

<div class="surah-header">
    <h1 class="surah-title">{{ $surahData['name'] }}</h1>
    <p class="surah-meta">{{ $surahData['englishName'] }} • {{ $surahData['revelationType'] }} • {{ $surahData['numberOfAyahs'] }} Ayahs</p>
    <div class="record-section">
        <button id="recordButton"><i class="fas fa-microphone"></i> Start Recording</button>
        <div id="timer">00:00</div>
    </div>
</div>

<div id="recording-output" class="section-card" style="display: none;">
    <div id="transcript-container">
        <span class="placeholder">Live transcription will appear here...</span>
    </div>
</div>

<div class="ayah-grid">
    @foreach ($surahData['ayahs'] as $ayah)
        <div class="ayah-card">
            <p class="ayah-text">{{ $ayah['text'] }} <span class="ayah-number">{{ $ayah['numberInSurah'] }}</span></p>
            <div class="ayah-actions">
    <button class="play-btn" data-audio-src="{{ $ayah['audio'] ?? '#' }}" {{ !isset($ayah['audio']) ? 'disabled' : '' }}>
        <i class="fas fa-play"></i>
        <span>Play</span>
    </button>
</div>
        </div>
    @endforeach
</div>

@endsection

@section('extra-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const audio = new Audio();
    let currentPlayingButton = null;

    document.querySelectorAll('.play-btn').forEach(button => {
        button.addEventListener('click', function () {
            const audioSrc = this.dataset.audioSrc;
            if (!audioSrc || audioSrc === '#') {
                console.error('Audio source is not available.');
                return;
            }

            // If this button is already playing, pause it
            if (this === currentPlayingButton && !audio.paused) {
                audio.pause();
                this.innerHTML = '<i class="fas fa-play"></i> <span>Play</span>';
                this.classList.remove('playing');
                currentPlayingButton = null;
            } else {
                // If another button is playing, stop it first
                if (currentPlayingButton) {
                    currentPlayingButton.innerHTML = '<i class="fas fa-play"></i> <span>Play</span>';
                    currentPlayingButton.classList.remove('playing');
                }
                
                // Play the new audio
                audio.src = audioSrc;
                audio.play();
                this.innerHTML = '<i class="fas fa-pause"></i> <span>Pause</span>';
                this.classList.add('playing');
                currentPlayingButton = this;
            }
        });
    });

    audio.addEventListener('ended', function() {
        if (currentPlayingButton) {
            currentPlayingButton.innerHTML = '<i class="fas fa-play"></i> <span>Play</span>';
            currentPlayingButton.classList.remove('playing');
            currentPlayingButton = null;
        }
    });

    const recordButton = document.getElementById('recordButton');
    const recordingOutput = document.getElementById('recording-output');
    const transcriptContainer = document.getElementById('transcript-container');
    const timerDisplay = document.getElementById('timer');
    
    let isRecording = false;
    let mediaRecorder;
    let socket;
    let timerInterval;
    let seconds = 0;

    const setupWebSocket = (token) => {
        return new Promise((resolve, reject) => {
            const url = `wss://api.assemblyai.com/v2/realtime/ws?sample_rate=16000&token=${token}`;
            socket = new WebSocket(url);

            socket.onopen = () => {
                console.log('WebSocket connected');
                transcriptContainer.innerHTML = '<span class="placeholder">Start speaking...</span>';
                resolve();
            };

            socket.onmessage = (message) => {
                const result = JSON.parse(message.data);
                if (result.text) {
                    if (result.message_type === 'FinalTranscript') {
                        transcriptContainer.innerHTML = `<span class="final-transcript">${result.text}</span>`;
                    } else {
                        transcriptContainer.innerHTML = `<span class="final-transcript">${transcriptContainer.querySelector('.final-transcript')?.textContent || ''}</span> <span class="partial-transcript">${result.text}</span>`;
                    }
                }
            };

            socket.onerror = (event) => {
                console.error('WebSocket error:', event);
                reject(new Error('WebSocket error.'));
            };

            socket.onclose = (event) => {
                console.log('WebSocket closed:', event);
                socket = null;
            };
        });
    };

    const startRecording = async () => {
        if (isRecording) return;

        transcriptContainer.innerHTML = '<span class="placeholder">Connecting...</span>';
        recordingOutput.style.display = 'block';

        try {
            // 1. Get AssemblyAI token
            const response = await fetch('{{ route('api.assemblyai.token') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to get AssemblyAI token.');
            }
            const data = await response.json();
            
            // 2. Setup WebSocket
            await setupWebSocket(data.token);

            // 3. Start microphone stream
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });

            mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0 && socket && socket.readyState === WebSocket.OPEN) {
                    socket.send(JSON.stringify({ audio_data: event.data.toString('base64') }));
                }
            };
            
            mediaRecorder.onstart = () => {
                isRecording = true;
                recordButton.innerHTML = '<i class="fas fa-stop"></i> Stop Recording';
                recordButton.classList.add('recording');
                startTimer();
            };

            mediaRecorder.onstop = () => {
                if (socket && socket.readyState === WebSocket.OPEN) {
                    socket.send(JSON.stringify({ terminate_session: true }));
                }
                stream.getTracks().forEach(track => track.stop());
                isRecording = false;
                recordButton.innerHTML = '<i class="fas fa-microphone"></i> Start Recording';
                recordButton.classList.remove('recording');
                stopTimer();
            };

            mediaRecorder.start(1000); // Send data every 1000ms

        } catch (error) {
            console.error('Recording failed:', error);
            transcriptContainer.innerHTML = `<p class="text-danger"><strong>Error:</strong> ${error.message}</p>`;
        }
    };

    const stopRecording = () => {
        if (mediaRecorder && isRecording) {
            mediaRecorder.stop();
        }
    };

    function formatTime(sec) {
        const minutes = Math.floor(sec / 60);
        const seconds = sec % 60;
        return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }

    function startTimer() {
        seconds = 0;
        timerDisplay.textContent = formatTime(seconds);
        timerInterval = setInterval(() => {
            seconds++;
            timerDisplay.textContent = formatTime(seconds);
        }, 1000);
    }

    function stopTimer() {
        clearInterval(timerInterval);
    }

    recordButton.addEventListener('click', () => {
        if (isRecording) {
            stopRecording();
        } else {
            startRecording();
        }
    });
});
</script>
@endsection
