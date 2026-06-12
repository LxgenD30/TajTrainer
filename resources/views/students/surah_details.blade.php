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
    }
    #recordButton {
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 10px;
        border: 2px solid #2a2a2a;
        cursor: pointer;
        font-weight: bold;
        background-color: #e74c3c;
        color: white;
    }
    #recordButton.recording {
        background-color: #c0392b;
    }
    #recording-output {
        margin-top: 20px;
        padding: 20px;
        background-color: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        min-height: 100px;
        display: none; /* Hidden by default */
    }
</style>

<div class="surah-header">
    <h1 class="surah-title">{{ $surahData['name'] }}</h1>
    <p class="surah-meta">{{ $surahData['englishName'] }} • {{ $surahData['revelationType'] }} • {{ $surahData['numberOfAyahs'] }} Ayahs</p>
    <div class="record-section">
        <button id="recordButton"><i class="fas fa-microphone"></i> Start Recording</button>
    </div>
</div>

<div id="recording-output">
    <p>Your recorded text will appear here...</p>
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
    let isRecording = false;
    let mediaRecorder;
    let chunks = [];

    recordButton.addEventListener('click', async () => {
        if (!isRecording) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                
                mediaRecorder.onstart = () => {
                    isRecording = true;
                    recordButton.innerHTML = '<i class="fas fa-stop"></i> Stop Recording';
                    recordButton.classList.add('recording');
                    recordingOutput.style.display = 'block';
                    recordingOutput.innerHTML = '<p>Recording...</p>';
                    chunks = [];
                };

                mediaRecorder.ondataavailable = (e) => {
                    chunks.push(e.data);
                };

                mediaRecorder.onstop = () => {
                    isRecording = false;
                    recordButton.innerHTML = '<i class="fas fa-microphone"></i> Start Recording';
                    recordButton.classList.remove('recording');
                    
                    const blob = new Blob(chunks, { 'type' : 'audio/webm' });
                    const audioURL = window.URL.createObjectURL(blob);
                    recordingOutput.innerHTML = `<p>Recording finished.</p><audio controls src="${audioURL}"></audio>`;
                    
                    // Stop the microphone track
                    stream.getTracks().forEach(track => track.stop());
                };

                mediaRecorder.start();

            } catch (err) {
                console.error('Error accessing microphone:', err);
                recordingOutput.style.display = 'block';
                recordingOutput.innerHTML = `<p class="text-danger">Error: Could not access microphone. Please grant permission and try again.</p>`;
            }
        } else {
            mediaRecorder.stop();
        }
    });
});
</script>
@endsection
