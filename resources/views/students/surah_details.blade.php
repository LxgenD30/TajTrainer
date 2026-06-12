@extends('layouts.dashboard')

@section('title', 'Surah Details')
@section('user-role', 'Student • Memorization')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('extra-styles')
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

    .btn {
        padding: 10px 20px;
        border-radius: 20px;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-record-main {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 15px 30px;
        border-radius: 25px;
        font-size: 1.2rem;
    }
    .btn-record-main.recording {
        background: linear-gradient(135deg, #27ae60, #229954);
    }
    .btn-play {
        background: #3498db;
        color: white;
    }
    .btn-play.playing {
        background: #f39c12;
    }
    .btn-primary-small {
        background: #1abc9c;
        color: white;
    }
    .btn-secondary-small {
        background: #bdc3c7;
        color: #333;
    }
</style>
@endsection

@section('content')
<div class="surah-header">
    <h1 class="surah-title">{{ $surahData['name'] }}</h1>
    <p class="surah-meta">{{ $surahData['englishName'] }} • {{ $surahData['revelationType'] }} • {{ $surahData['numberOfAyahs'] }} Ayahs</p>
    
    <!-- Recording Controls -->
    <div style="margin-top: 20px; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 15px;">
        <div id="recordingStatus" style="font-size: 1.2rem; margin-bottom: 10px;">Ready to Record</div>
        <div id="recordingTimer" style="font-size: 2rem; font-weight: bold; margin-bottom: 15px;">00:00</div>
        <button id="recordBtn" class="btn btn-record-main">
            <i id="recordIcon" class="fas fa-microphone"></i>
            <span id="recordBtnText">Start Recording</span>
        </button>
        <div id="audioPlayback" style="display: none; margin-top: 20px;">
            <h4 style="margin-bottom: 10px;">Your Recitation</h4>
            <audio id="audioPlayer" controls style="width: 100%;"></audio>
            <div style="margin-top: 10px;">
                <button onclick="deleteRecording()" class="btn btn-secondary-small">Re-record</button>
                <button onclick="submitMemorization()" class="btn btn-primary-small">Submit</button>
            </div>
        </div>
    </div>
</div>

<div class="ayah-grid">
    @foreach ($surahData['ayahs'] as $ayah)
        <div class="ayah-card" id="ayah-{{ $ayah['numberInSurah'] }}">
            <p class="ayah-text">{{ $ayah['text'] }} <span class="ayah-number">{{ $ayah['numberInSurah'] }}</span></p>
            <div class="ayah-actions">
                <button class="btn btn-play" onclick="playAyah('{{ $ayah['audio'] }}', this)">
                    <i class="fas fa-play"></i> Play
                </button>
                <button class="status-toggle status-not-memorized">Not Memorized</button>
                <button class="status-toggle status-in-progress">In Progress</button>
                <button class="status-toggle status-memorized">Memorized</button>
            </div>
            <audio class="ayah-audio" src="{{ $ayah['audio'] ?? '#' }}" preload="none"></audio>
        </div>
    @endforeach
</div>

@endsection

@section('extra-scripts')
<script>
(function() {
    'use strict';

    let mediaRecorder;
    let audioChunks = [];
    let recordingTimer;
    let recordingSeconds = 0;
    let recordedBlob = null;
    let currentPlayingAudio = null;

    const recordBtn = document.getElementById('recordBtn');
    const recordIcon = document.getElementById('recordIcon');
    const recordBtnText = document.getElementById('recordBtnText');
    const recordingStatus = document.getElementById('recordingStatus');
    const recordingTimerEl = document.getElementById('recordingTimer');
    const audioPlayback = document.getElementById('audioPlayback');
    const audioPlayer = document.getElementById('audioPlayer');

    function toggleRecording() {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            stopRecording();
        } else {
            startRecording();
        }
    }

    function startRecording() {
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                recordingSeconds = 0;

                mediaRecorder.ondataavailable = event => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = () => {
                    recordedBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(recordedBlob);
                    audioPlayer.src = audioUrl;
                    audioPlayback.style.display = 'block';
                    stream.getTracks().forEach(track => track.stop());
                };

                mediaRecorder.start();
                recordBtn.classList.add('recording');
                recordIcon.className = 'fas fa-stop';
                recordBtnText.textContent = 'Stop Recording';
                recordingStatus.textContent = 'Recording...';
                
                recordingTimer = setInterval(() => {
                    recordingSeconds++;
                    const minutes = String(Math.floor(recordingSeconds / 60)).padStart(2, '0');
                    const seconds = String(recordingSeconds % 60).padStart(2, '0');
                    recordingTimerEl.textContent = `${minutes}:${seconds}`;
                }, 1000);
            })
            .catch(err => {
                console.error("Error starting recording:", err);
                alert("Could not access microphone. Please check permissions.");
            });
    }

    function stopRecording() {
        if (mediaRecorder) {
            mediaRecorder.stop();
            clearInterval(recordingTimer);
            recordBtn.classList.remove('recording');
            recordIcon.className = 'fas fa-microphone';
            recordBtnText.textContent = 'Start Recording';
            recordingStatus.textContent = 'Recording Finished';
        }
    }

    window.deleteRecording = function() {
        recordedBlob = null;
        audioChunks = [];
        recordingSeconds = 0;
        recordingTimerEl.textContent = '00:00';
        recordingStatus.textContent = 'Ready to Record';
        audioPlayback.style.display = 'none';
        audioPlayer.src = '';
    }

    window.submitMemorization = function() {
        if (!recordedBlob) {
            alert("Please record your recitation first.");
            return;
        }
        // Here you would handle the form submission with the recordedBlob
        alert("Submitting memorization... (functionality to be implemented)");
    }
    
    window.playAyah = function(audioSrc, button) {
        const ayahCard = button.closest('.ayah-card');
        const audio = ayahCard.querySelector('.ayah-audio');
        const playIcon = button.querySelector('i');

        if (currentPlayingAudio && currentPlayingAudio !== audio) {
            const otherButton = document.querySelector('.btn-play.playing');
            if(otherButton) {
                otherButton.querySelector('i').className = 'fas fa-play';
                otherButton.innerHTML = otherButton.innerHTML.replace('Pause', 'Play');
                otherButton.classList.remove('playing');
            }
            currentPlayingAudio.pause();
            currentPlayingAudio.currentTime = 0;
        }

        if (audio.paused) {
            audio.play();
            playIcon.className = 'fas fa-pause';
            button.innerHTML = button.innerHTML.replace('Play', 'Pause');
            button.classList.add('playing');
            currentPlayingAudio = audio;
        } else {
            audio.pause();
            playIcon.className = 'fas fa-play';
            button.innerHTML = button.innerHTML.replace('Pause', 'Play');
            button.classList.remove('playing');
            currentPlayingAudio = null;
        }

        audio.onended = () => {
            playIcon.className = 'fas fa-play';
            button.innerHTML = button.innerHTML.replace('Pause', 'Play');
            button.classList.remove('playing');
            currentPlayingAudio = null;
        };
    }

    recordBtn.addEventListener('click', toggleRecording);

})();
