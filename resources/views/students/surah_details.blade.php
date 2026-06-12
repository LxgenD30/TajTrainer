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
    }
    #recording-output p {
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 15px;
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
    <p>Your recorded audio:</p>
    <div id="audio-player-container"></div>
</div>

<div class="ayah-grid">
    @foreach ($surahData['ayahs'] as $ayah)
        <div class="ayah-card">
            <p class="ayah-text">{{ $ayah['text'] }} <span class="ayah-number">{{ $ayah['numberInSurah'] }}</span></p>
            <div class="ayah-actions">
    <button class="btn btn-sm btn-outline-secondary play-btn" data-audio-src="{{ $ayah['audio'] ?? '#' }}" {{ !isset($ayah['audio']) ? 'disabled' : '' }}>
        <i class="fas fa-play"></i> Play
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

            if (audio.src === audioSrc && !audio.paused) {
                audio.pause();
                this.innerHTML = '<i class="fas fa-play"></i> Play';
                currentPlayingButton = null;
            } else {
                if (currentPlayingButton) {
                    currentPlayingButton.innerHTML = '<i class="fas fa-play"></i> Play';
                }
                audio.src = audioSrc;
                audio.play();
                this.innerHTML = '<i class="fas fa-pause"></i> Pause';
                currentPlayingButton = this;
            }
        });
    });

    audio.addEventListener('ended', function() {
        if (currentPlayingButton) {
            currentPlayingButton.innerHTML = '<i class="fas fa-play"></i> Play';
            currentPlayingButton = null;
        }
    });

    const recordButton = document.getElementById('recordButton');
    const recordingOutput = document.getElementById('recording-output');
    const audioPlayerContainer = document.getElementById('audio-player-container');
    const timerDisplay = document.getElementById('timer');
    let isRecording = false;
    let mediaRecorder;
    let chunks = [];
    let timerInterval;
    let seconds = 0;

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

    recordButton.addEventListener('click', async () => {
        if (!isRecording) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
                
                mediaRecorder.onstart = () => {
                    isRecording = true;
                    recordButton.innerHTML = '<i class="fas fa-stop"></i> Stop Recording';
                    recordButton.classList.add('recording');
                    recordingOutput.style.display = 'none';
                    audioPlayerContainer.innerHTML = '';
                    chunks = [];
                    startTimer();
                };

                mediaRecorder.ondataavailable = (e) => {
                    chunks.push(e.data);
                };

                mediaRecorder.onstop = () => {
                    isRecording = false;
                    recordButton.innerHTML = '<i class="fas fa-microphone"></i> Start Recording';
                    recordButton.classList.remove('recording');
                    stopTimer();
                    
                    const blob = new Blob(chunks, { 'type' : 'audio/webm' });
                    const audioURL = window.URL.createObjectURL(blob);
                    
                    const audioPlayer = new Audio(audioURL);
                    audioPlayer.controls = true;
                    
                    recordingOutput.style.display = 'block';
                    audioPlayerContainer.appendChild(audioPlayer);
                    
                    // Stop the microphone track
                    stream.getTracks().forEach(track => track.stop());
                };

                mediaRecorder.start();

            } catch (err) {
                console.error('Error accessing microphone:', err);
                recordingOutput.style.display = 'block';
                audioPlayerContainer.innerHTML = `<p class="text-danger"><strong>Error:</strong> Could not access microphone. Please grant permission and try again.</p>`;
            }
        } else {
            mediaRecorder.stop();
        }
    });
});
</script>
@endsection
