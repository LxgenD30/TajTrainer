@extends('layouts.dashboard')

@section('title', 'Quran Practice')
@section('user-role', 'Student • Practice Mode')

@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="{{ route('student.classes') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-users"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <a href="{{ route('student.practice') }}" class="nav-item active">
        <div class="nav-icon"><i class="fas fa-microphone-alt"></i></div>
        <div class="nav-label">Practice</div>
    </a>
    <a href="{{ route('student.progress') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-chart-line"></i></div>
        <div class="nav-label">My Progress</div>
    </a>
    <a href="{{ route('student.materials') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
        <div class="nav-label">Materials</div>
    </a>
@endsection

@section('extra-styles')
<style>
    .practice-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .practice-header {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 30px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
        border: 3px solid #2a2a2a;
        text-align: center;
    }
    
    .practice-header:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }

    .practice-header h1 {
        font-size: 2.5rem;
        color: #ffffff;
        margin-bottom: 10px;
        font-weight: 700;
        position: relative;
        z-index: 2;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }

    .practice-header p {
        font-size: 1.1rem;
        color: #ffffff;
        opacity: 0.95;
        line-height: 1.6;
        position: relative;
        z-index: 2;
    }

    .practice-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    @media (max-width: 1200px) {
        .practice-grid { grid-template-columns: 1fr; }
    }

    .card {
        background: white;
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);
        border: 2px solid rgba(10, 92, 54, 0.1);
    }

    .card h3 {
        color: var(--primary-green);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .verse-arabic {
        font-size: 3rem;
        text-align: center;
        direction: rtl;
        line-height: 2.2;
        color: var(--primary-green);
        margin: 30px 0;
        padding: 25px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 15px;
        min-height: 120px;
    }

    .verse-info {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin: 20px 0;
    }

    .verse-info-item {
        text-align: center;
    }

    .verse-info-label {
        font-size: 1rem;
        color: #666;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .verse-info-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--primary-green);
    }

    .verse-translation {
        font-size: 1.2rem;
        color: #333;
        line-height: 1.9;
        padding: 25px;
        background: rgba(212, 175, 55, 0.05);
        border-radius: 10px;
        border-left: 4px solid var(--gold);
    }

    .btn {
        padding: 15px 30px;
        border-radius: 25px;
        border: none;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.3);
    }

    .btn-secondary {
        background: var(--gold);
        color: var(--primary-green);
    }

    .btn-secondary:hover {
        background: #c19b2e;
    }

    .btn-record {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        font-size: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 30px auto;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 5px solid white;
        box-shadow: 0 10px 30px rgba(231, 76, 60, 0.3);
    }

    .btn-record:hover {
        transform: scale(1.05);
    }

    .btn-record.recording {
        background: linear-gradient(135deg, #27ae60, #229954);
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .recording-status {
        text-align: center;
        margin: 20px 0;
        font-size: 1.3rem;
        color: #666;
        font-weight: 500;
    }

    .recording-status.active {
        color: #e74c3c;
        font-weight: 700;
    }

    .recording-timer {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-green);
        text-align: center;
        margin: 15px 0;
    }

    .audio-player {
        width: 100%;
        margin: 20px 0;
    }

    .reference-section {
        display: none;
        margin-top: 20px;
        padding: 20px;
        background: rgba(26, 188, 156, 0.05);
        border-radius: 15px;
        border: 2px solid rgba(26, 188, 156, 0.2);
    }

    .reference-section.show {
        display: block;
    }

    .tajweed-colors {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-top: 15px;
    }

    .tajweed-color-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }

    .color-box {
        width: 20px;
        height: 20px;
        border-radius: 4px;
    }

    .loading {
        text-align: center;
        color: #999;
        font-style: italic;
    }

    .error {
        background: rgba(231, 76, 60, 0.1);
        border: 2px solid #e74c3c;
        border-radius: 10px;
        padding: 15px;
        color: #c0392b;
        margin: 20px 0;
    }

    .analyzing-overlay {
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

    .analyzing-overlay.show {
        display: flex;
    }

    .analyzing-content {
        background: white;
        padding: 40px;
        border-radius: 20px;
        text-align: center;
    }

    .analyzing-spinner {
        width: 60px;
        height: 60px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid var(--primary-green);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .control-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin: 20px 0;
    }
</style>
@endsection

@section('content')
<div class="practice-container">
    <!-- Header -->
    <div class="practice-header">
        <h1>🕌 Quran Practice</h1>
        <p>Practice your Quranic recitation with real-time AI feedback</p>
    </div>

    <!-- Practice Grid -->
    <div class="practice-grid">
        <!-- Verse Card -->
        <div class="card">
            <h3><i class="fas fa-book-quran"></i> Practice Verse</h3>
            
            <div class="verse-arabic" id="ayahArabic">
                <div class="loading">Loading verse...</div>
            </div>

            <div class="verse-info">
                <div class="verse-info-item">
                    <div class="verse-info-label">Surah</div>
                    <div class="verse-info-value" id="surahInfo">---</div>
                </div>
                <div class="verse-info-item">
                    <div class="verse-info-label">Ayah</div>
                    <div class="verse-info-value" id="ayahInfo">---</div>
                </div>
            </div>

            <div class="control-buttons">
                <button class="btn btn-primary" onclick="loadNewVerse()">
                    <i class="fas fa-sync-alt"></i> New Verse
                </button>
                <button class="btn btn-secondary" onclick="toggleReference()">
                    <i class="fas fa-headphones"></i> <span id="refBtnText">Show Reference</span>
                </button>
            </div>

            <!-- Reference Section -->
            <div class="reference-section" id="referenceSection">
                <h4 style="color: var(--primary-green); margin-bottom: 15px;">
                    <i class="fas fa-volume-up"></i> Reference Audio
                </h4>
                <p style="color: #666; margin-bottom: 10px;">Sheikh Mishary Alafasy</p>
                <audio id="referenceAudio" controls class="audio-player"></audio>
                
                <h4 style="color: var(--primary-green); margin: 20px 0 10px;">
                    <i class="fas fa-palette"></i> Tajweed Colors
                </h4>
                <div class="tajweed-colors">
                    <div class="tajweed-color-item">
                        <div class="color-box" style="background: #4CAF50;"></div>
                        <span>Idgham</span>
                    </div>
                    <div class="tajweed-color-item">
                        <div class="color-box" style="background: #2196F3;"></div>
                        <span>Ikhfa</span>
                    </div>
                    <div class="tajweed-color-item">
                        <div class="color-box" style="background: #F44336;"></div>
                        <span>Madd</span>
                    </div>
                    <div class="tajweed-color-item">
                        <div class="color-box" style="background: #FF9800;"></div>
                        <span>Qalqalah</span>
                    </div>
                </div>
            </div>

            <h4 style="color: var(--primary-green); margin: 20px 0 10px;">
                <i class="fas fa-language"></i> Translation
            </h4>
            <div class="verse-translation" id="ayahTranslation">
                <div class="loading">Loading translation...</div>
            </div>
        </div>

        <!-- Recording Card -->
        <div class="card">
            <h3><i class="fas fa-microphone"></i> Record Your Recitation</h3>
            
            <div class="recording-status" id="recordingStatus">
                Ready to record
            </div>

            <div class="recording-timer" id="recordingTimer">00:00</div>

            <button class="btn-record" id="recordBtn" onclick="toggleRecording()">
                <i class="fas fa-microphone" id="recordIcon"></i>
            </button>

            <div id="audioPlayback" style="display: none;">
                <h4 style="color: var(--primary-green); text-align: center; margin-bottom: 15px;">
                    <i class="fas fa-check-circle"></i> Your Recording
                </h4>
                <audio id="audioPlayer" controls class="audio-player"></audio>
                <div class="control-buttons">
                    <button class="btn btn-secondary" onclick="deleteRecording()">
                        <i class="fas fa-trash"></i> Delete & Re-record
                    </button>
                    <button class="btn btn-primary" onclick="analyzeRecording()">
                        <i class="fas fa-brain"></i> Analyze with AI
                    </button>
                </div>
            </div>

            <div id="analysisResults" style="display: none; margin-top: 20px;">
                <!-- Analysis results will be displayed here -->
            </div>
        </div>
    </div>
</div>

<!-- Analyzing Overlay -->
<div class="analyzing-overlay" id="analyzingOverlay">
    <div class="analyzing-content">
        <div class="analyzing-spinner"></div>
        <h3 style="color: var(--primary-green);">Analyzing Your Recitation</h3>
        <p style="color: #666;">Please wait while our AI evaluates your Tajweed...</p>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
(function() {
    'use strict';
    
    console.log('=== Practice Page Initializing ===');

    // Global variables
    var currentSurah = null;
    var currentAyah = null;
    var currentAudioUrl = null;
    var mediaRecorder = null;
    var audioChunks = [];
    var recordingTimer = null;
    var recordingSeconds = 0;
    var recordedBlob = null;

    // Load random verse on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded');
        console.log('Starting to load verse...');
        loadNewVerse();
    });

    function loadNewVerse() {
        console.log('=== loadNewVerse() called ===');
        
        console.log('Step 1: Show loading state');
        document.getElementById('ayahArabic').innerHTML = '<div class="loading">Loading verse...</div>';
        document.getElementById('ayahTranslation').innerHTML = '<div class="loading">Loading translation...</div>';
        document.getElementById('surahInfo').textContent = '---';
        document.getElementById('ayahInfo').textContent = '---';
        
        console.log('Step 2: Generate random surah');
        currentSurah = Math.floor(Math.random() * 114) + 1;
        console.log('Selected Surah:', currentSurah);
            
            var surahUrl = 'https://api.alquran.cloud/v1/surah/' + currentSurah;
            console.log('Step 3: Fetching surah info from:', surahUrl);
            
            fetch(surahUrl)
                .then(function(response) {
                    console.log('Step 4: Surah response received, status:', response.status);
                    if (!response.ok) {
                        throw new Error('Surah API failed: ' + response.status);
                    }
                    return response.json();
                })
                .then(function(surahData) {
                    console.log('Step 5: Surah data parsed:', surahData);
                    
                    if (surahData.code !== 200 || surahData.status !== 'OK') {
                        throw new Error('Surah API returned error status');
                    }
                    
                    var totalAyahs = surahData.data.numberOfAyahs;
                    currentAyah = Math.floor(Math.random() * totalAyahs) + 1;
                    console.log('Selected Ayah', currentAyah, 'of', totalAyahs);
                    
                    var ayahUrl = 'https://api.alquran.cloud/v1/ayah/' + currentSurah + ':' + currentAyah + '/editions/quran-uthmani,ar.alafasy,en.asad';
                    console.log('Step 6: Fetching ayah from:', ayahUrl);
                    
                    return fetch(ayahUrl);
                })
                .then(function(response) {
                    console.log('Step 7: Ayah response received, status:', response.status);
                    if (!response.ok) {
                        throw new Error('Ayah API failed: ' + response.status);
                    }
                    return response.json();
                })
                .then(function(ayahData) {
                    console.log('Step 8: Ayah data parsed:', ayahData);
                    
                    if (ayahData.code !== 200 || ayahData.status !== 'OK' || !ayahData.data) {
                        throw new Error('Ayah API returned error');
                    }
                    
                    console.log('Step 9: Extracting data from response');
                    var arabicText = ayahData.data[0].text;
                    var audioData = ayahData.data[1];
                    var translationText = ayahData.data[2].text;
                    
                    console.log('Step 10: Updating display');
                    document.getElementById('ayahArabic').textContent = arabicText;
                    document.getElementById('surahInfo').textContent = audioData.surah.englishName;
                    document.getElementById('ayahInfo').textContent = 'Ayah ' + currentAyah;
                    document.getElementById('ayahTranslation').textContent = translationText;
                    
                    if (audioData.audio) {
                        currentAudioUrl = audioData.audio;
                        document.getElementById('referenceAudio').src = currentAudioUrl;
                        console.log('Step 11: Audio URL set:', currentAudioUrl);
                    } else {
                        console.warn('No audio available for this ayah');
                    }
                    
                    console.log('=== Verse loaded successfully! ===');
                })
                .catch(function(error) {
                    console.error('ERROR in loadNewVerse:', error);
                    console.error('Error message:', error.message);
                    console.error('Error stack:', error.stack);
                    
                    var errorDiv = document.createElement('div');
                    errorDiv.className = 'error';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Failed to load verse. Please try again.<br><small>' + error.message + '</small>';
                    
                    document.getElementById('ayahArabic').innerHTML = '';
                    document.getElementById('ayahArabic').appendChild(errorDiv);
                    document.getElementById('ayahTranslation').innerHTML = '<div class="error">Failed to load translation</div>';
                });
    }

    window.loadNewVerse = loadNewVerse;

    function toggleReference() {
        console.log('toggleReference() called');
        var section = document.getElementById('referenceSection');
        var btnText = document.getElementById('refBtnText');
        
        if (section.classList.contains('show')) {
            section.classList.remove('show');
            btnText.textContent = 'Show Reference';
        } else {
            section.classList.add('show');
            btnText.textContent = 'Hide Reference';
        }
    }

    window.toggleReference = toggleReference;

    function toggleRecording() {
        console.log('toggleRecording() called');
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            stopRecording();
        } else {
            navigator.mediaDevices.getUserMedia({ 
            audio: {
                echoCancellation: true,
                noiseSuppression: true,
                sampleRate: 44100
            }
        })
        .then(function(stream) {
            console.log('Microphone access granted');
            
            var mimeType = 'audio/webm;codecs=opus';
            if (!MediaRecorder.isTypeSupported(mimeType)) {
                mimeType = 'audio/webm';
                console.log('Using fallback MIME type:', mimeType);
            }
            
            mediaRecorder = new MediaRecorder(stream, { mimeType: mimeType });
            audioChunks = [];
            recordingSeconds = 0;
            
            mediaRecorder.ondataavailable = function(event) {
                if (event.data.size > 0) {
                    audioChunks.push(event.data);
                }
            };
            
            mediaRecorder.onstop = function() {
                console.log('Recording stopped');
                var audioBlob = new Blob(audioChunks, { type: mimeType });
                recordedBlob = audioBlob;
                
                var audioUrl = URL.createObjectURL(audioBlob);
                var audioPlayer = document.getElementById('audioPlayer');
                audioPlayer.src = audioUrl;
                
                document.getElementById('audioPlayback').style.display = 'block';
                
                stream.getTracks().forEach(function(track) {
                    track.stop();
                });
            };
            
            mediaRecorder.start(1000);
            
            document.getElementById('recordBtn').classList.add('recording');
            document.getElementById('recordIcon').className = 'fas fa-stop';
            document.getElementById('recordingStatus').textContent = 'Recording in progress...';
            document.getElementById('recordingStatus').classList.add('active');
            
            recordingTimer = setInterval(function() {
                recordingSeconds++;
                var minutes = Math.floor(recordingSeconds / 60).toString();
                var seconds = (recordingSeconds % 60).toString();
                if (minutes.length < 2) minutes = '0' + minutes;
                if (seconds.length < 2) seconds = '0' + seconds;
                document.getElementById('recordingTimer').textContent = minutes + ':' + seconds;
            }, 1000);
            
            console.log('Recording started');
        })
        .catch(function(error) {
            console.error('Error starting recording:', error);
            alert('Could not access microphone. Please check permissions.');
        });
        }
    }

    window.toggleRecording = toggleRecording;

    function stopRecording() {
        console.log('stopRecording() called');
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
            clearInterval(recordingTimer);
            
            document.getElementById('recordBtn').classList.remove('recording');
            document.getElementById('recordIcon').className = 'fas fa-microphone';
            document.getElementById('recordingStatus').textContent = 'Recording complete';
            document.getElementById('recordingStatus').classList.remove('active');
        }
    }

    window.stopRecording = stopRecording;

    function deleteRecording() {
        console.log('deleteRecording() called');
        document.getElementById('audioPlayback').style.display = 'none';
        document.getElementById('analysisResults').style.display = 'none';
        document.getElementById('recordingTimer').textContent = '00:00';
        document.getElementById('recordingStatus').textContent = 'Ready to record';
        recordedBlob = null;
        audioChunks = [];
    }

    window.deleteRecording = deleteRecording;

    function analyzeRecording() {
        console.log('=== ANALYSIS STARTED ===');
        console.log('analyzeRecording() called');
        
        if (!recordedBlob) {
            alert('No recording to analyze');
            return;
        }
        
        console.log('✓ Recording blob validated');
        console.log('Blob size:', recordedBlob.size, 'bytes');
        console.log('Blob type:', recordedBlob.type);
        
        console.log('Starting AI analysis...');
        document.getElementById('analyzingOverlay').classList.add('show');
        
        console.log('Building request data:');
        console.log('  - Surah:', currentSurah);
        console.log('  - Ayah:', currentAyah);
        console.log('  - Expected text:', document.getElementById('ayahArabic').textContent.substring(0, 50) + '...');
        console.log('  - Reference audio:', currentAudioUrl);
        
        var formData = new FormData();
        formData.append('audio_file', recordedBlob, 'recording.webm');
        formData.append('surah_number', currentSurah);
        formData.append('ayah_number', currentAyah);
        formData.append('expected_text', document.getElementById('ayahArabic').textContent);
        formData.append('reference_audio_url', currentAudioUrl);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        console.log('✓ FormData prepared, sending to server...');
        
        fetch('{{ route("student.practice.submit") }}', {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            console.log('=== SERVER RESPONSE RECEIVED ===');
            console.log('Response status:', response.status);
            console.log('Response OK:', response.ok);
            return response.json();
        })
        .then(function(result) {
            console.log('=== ANALYSIS COMPLETE ===');
            console.log('Full result:', result);
            console.log('Success:', result.success);
            console.log('Audio saved to:', result.audio_path);
            
            document.getElementById('analyzingOverlay').classList.remove('show');
            
            if (result.success) {
                console.log('✓ Analysis successful!');
                console.log('Accuracy score:', result.analysis.accuracy_score + '%');
                if (result.analysis.python_analysis && result.analysis.python_analysis.whisper_transcription) {
                    console.log('Transcribed text:', result.analysis.python_analysis.whisper_transcription);
                }
                displayAnalysisResults(result.analysis);
            } else {
                console.error('✗ Analysis failed:', result.message);
                alert('Analysis failed: ' + (result.message || 'Unknown error'));
            }
        })
        .catch(function(error) {
            console.error('=== ANALYSIS ERROR ===');
            console.error('Error:', error);
            console.error('Error message:', error.message);
            document.getElementById('analyzingOverlay').classList.remove('show');
            alert('Analysis failed. Please try again.');
        });
    }

    window.analyzeRecording = analyzeRecording;

    function displayAnalysisResults(analysis) {
        console.log('=== DISPLAYING RESULTS ===');
        console.log('displayAnalysisResults() called with:', analysis);
        
        var resultsDiv = document.getElementById('analysisResults');
        var accuracyScore = analysis.accuracy_score || (analysis.overall_score && analysis.overall_score.score) || 0;
        
        // Prioritize AI feedback over generic feedback
        var feedback = 'Analysis complete';
        var aiFeedback = null;
        
        if (analysis.python_analysis && analysis.python_analysis.ai_feedback) {
            aiFeedback = analysis.python_analysis.ai_feedback;
            feedback = aiFeedback.summary || feedback;
        } else if (analysis.feedback) {
            feedback = analysis.feedback;
        } else if (analysis.overall_score && analysis.overall_score.feedback) {
            feedback = analysis.overall_score.feedback;
        }
        
        var html = '<div style="background: rgba(26, 188, 156, 0.05); padding: 25px; border-radius: 15px; border: 2px solid rgba(26, 188, 156, 0.2);">';
        
        // Overall Score
        html += '<h4 style="color: var(--primary-green); margin-bottom: 20px; font-size: 1.4rem; font-weight: 700;">';
        html += '<i class="fas fa-chart-line"></i> Tajweed Analysis Results';
        html += '</h4>';
        html += '<div style="font-size: 3.5rem; font-weight: 700; color: var(--primary-green); text-align: center; margin: 25px 0;">';
        html += accuracyScore + '%';
        html += '</div>';
        html += '<p style="text-align: center; color: #333; font-size: 1.15rem; margin-bottom: 25px; line-height: 1.6;">';
        html += feedback;
        html += '</p>';
        
        // Display AI Feedback sections if available
        if (aiFeedback) {
            // Strengths
            if (aiFeedback.strengths && aiFeedback.strengths.length > 0) {
                html += '<div style="background: rgba(39, 174, 96, 0.1); padding: 20px; border-radius: 10px; margin: 15px 0;">';
                html += '<h5 style="color: #27ae60; margin-bottom: 12px; font-size: 1.2rem; font-weight: 700;"><i class="fas fa-check-circle"></i> Strengths:</h5>';
                html += '<ul style="margin: 0; padding-left: 25px; font-size: 1.05rem; line-height: 1.8;">';
                aiFeedback.strengths.forEach(function(strength) {
                    html += '<li style="color: #333; margin: 5px 0;">' + strength + '</li>';
                });
                html += '</ul></div>';
            }
            
            // Improvements
            if (aiFeedback.improvements && aiFeedback.improvements.length > 0) {
                html += '<div style="background: rgba(231, 76, 60, 0.1); padding: 20px; border-radius: 10px; margin: 15px 0;">';
                html += '<h5 style="color: #e74c3c; margin-bottom: 12px; font-size: 1.2rem; font-weight: 700;"><i class="fas fa-exclamation-triangle"></i> Areas for Improvement:</h5>';
                aiFeedback.improvements.forEach(function(improvement) {
                    html += '<div style="background: white; padding: 15px; border-radius: 8px; margin: 12px 0; border-left: 4px solid #e74c3c;">';
                    html += '<strong style="color: #e74c3c; font-size: 1.05rem;">Issue:</strong> <span style="font-size: 1.05rem;">' + improvement.issue + '</span><br><br>';
                    html += '<strong style="color: #3498db; font-size: 1.05rem;">Suggestion:</strong> <span style="font-size: 1.05rem;">' + improvement.suggestion + '</span>';
                    html += '</div>';
                });
                html += '</div>';
            }
            
            // Next Steps
            if (aiFeedback.next_steps) {
                html += '<div style="background: rgba(52, 152, 219, 0.1); padding: 20px; border-radius: 10px; margin: 15px 0;">';
                html += '<h5 style="color: #3498db; margin-bottom: 12px; font-size: 1.2rem; font-weight: 700;"><i class="fas fa-forward"></i> Next Steps:</h5>';
                html += '<p style="color: #333; margin: 0; font-size: 1.05rem; line-height: 1.7;">' + aiFeedback.next_steps + '</p>';
                html += '</div>';
            }
        }
        
        // Transcribed Text (if available)
        if (analysis.python_analysis && analysis.python_analysis.whisper_transcription) {
            var transcription = analysis.python_analysis.whisper_transcription;
            // Remove special tokens like <|ar|><|transcribe|><|notimestamps|>
            transcription = transcription.replace(/<\|[^|]+\|>/g, '').trim();
            if (transcription) {
                html += '<div style="background: rgba(212, 175, 55, 0.1); padding: 20px; border-radius: 10px; margin: 15px 0; direction: rtl;">';
                html += '<h5 style="color: #d4af37; margin-bottom: 12px; font-size: 1.2rem; font-weight: 700;"><i class="fas fa-microphone"></i> Your Recitation (Transcribed):</h5>';
                html += '<p style="font-size: 2rem; color: #333; text-align: center; line-height: 1.8;">' + transcription + '</p>';
                html += '</div>';
            }
        }
        
        // Detailed Breakdown (if available)
        if (analysis.details) {
            html += '<div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid rgba(26, 188, 156, 0.2);">';
            html += '<h5 style="color: var(--primary-green); margin-bottom: 15px; font-size: 1.2rem; font-weight: 700;"><i class="fas fa-list-check"></i> Detailed Breakdown:</h5>';
            html += '<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">';
            
            for (var key in analysis.details) {
                var label = key.replace(/_/g, ' ').replace(/\b\w/g, function(l){ return l.toUpperCase() });
                var value = Math.round(analysis.details[key]);
                html += '<div style="background: rgba(255, 255, 255, 0.5); padding: 20px; border-radius: 8px; text-align: center;">';
                html += '<div style="font-size: 1rem; color: #666; margin-bottom: 8px; font-weight: 600;">' + label + '</div>';
                html += '<div style="font-size: 2rem; font-weight: 700; color: var(--primary-green);">' + value + '%</div>';
                html += '</div>';
            }
            
            html += '</div></div>';
        }
        
        html += '</div>';
        
        resultsDiv.innerHTML = html;
        resultsDiv.style.display = 'block';
        
        console.log('✓ Results displayed successfully');
        console.log('=== ANALYSIS PRESENTATION COMPLETE ===');
    }

    window.displayAnalysisResults = displayAnalysisResults;

    console.log('=== Practice page initialization complete ===');
    console.log('Available functions:', Object.keys(window).filter(function(key) {
        return typeof window[key] === 'function' && (
            key === 'loadNewVerse' || 
            key === 'toggleReference' || 
            key === 'toggleRecording' ||
            key === 'deleteRecording' ||
            key === 'analyzeRecording'
        );
    }));

})();
</script>
@endsection
