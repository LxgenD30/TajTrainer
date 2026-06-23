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
    .status-buttons {
        display: flex;
        gap: 6px;
        margin-left: auto;
        flex-wrap: wrap;
    }
    .status-toggle {
        padding: 6px 12px;
        border-radius: 8px;
        border: 2px solid #2a2a2a;
        cursor: pointer;
        font-weight: bold;
        font-size: 0.8rem;
        transition: all 0.3s ease;
        opacity: 0.35;
    }
    .status-toggle.active-status {
        opacity: 1;
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    }
    .status-not-memorized { background-color: #e0e0e0; color: #555; }
    .status-in-progress { background-color: #f1c40f; color: #7a6000; }
    .status-memorized { background-color: #2ecc71; color: #fff; }
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
    <div id="processing-indicator" style="display:none; color:#888; font-style:italic; font-size:0.9rem; text-align:center; margin-top:8px;">
        <i class="fas fa-spinner fa-spin"></i> Processing audio chunk...
    </div>
</div>

<div class="ayah-grid">
    @foreach ($surahData['ayahs'] as $ayah)
        @php $currentStatus = $statuses[$ayah['numberInSurah']] ?? 'not_memorized'; @endphp
        <div class="ayah-card" data-ayah="{{ $ayah['numberInSurah'] }}">
            <p class="ayah-text">{{ $ayah['text'] }} <span class="ayah-number">{{ $ayah['numberInSurah'] }}</span></p>
            <div class="ayah-actions">
                <button class="play-btn" data-audio-src="{{ $ayah['audio'] ?? '#' }}" {{ !isset($ayah['audio']) ? 'disabled' : '' }}>
                    <i class="fas fa-play"></i>
                    <span>Play</span>
                </button>
                <div class="status-buttons">
                    <button class="status-toggle status-not-memorized {{ $currentStatus === 'not_memorized' ? 'active-status' : '' }}"
                        data-ayah="{{ $ayah['numberInSurah'] }}" data-status="not_memorized">✗ Not Memorized</button>
                    <button class="status-toggle status-in-progress {{ $currentStatus === 'in_progress' ? 'active-status' : '' }}"
                        data-ayah="{{ $ayah['numberInSurah'] }}" data-status="in_progress">⟳ In Progress</button>
                    <button class="status-toggle status-memorized {{ $currentStatus === 'memorized' ? 'active-status' : '' }}"
                        data-ayah="{{ $ayah['numberInSurah'] }}" data-status="memorized">✓ Memorized</button>
                </div>
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
    
    // ── WAV encoder (no ffmpeg needed on server) ────────────────────────────
    function encodeWAV(samples, sampleRate) {
        const buf  = new ArrayBuffer(44 + samples.length * 2);
        const view = new DataView(buf);
        const ws   = (off, str) => { for (let i = 0; i < str.length; i++) view.setUint8(off + i, str.charCodeAt(i)); };
        ws(0, 'RIFF'); view.setUint32(4, 36 + samples.length * 2, true);
        ws(8, 'WAVE'); ws(12, 'fmt ');
        view.setUint32(16, 16, true);  view.setUint16(20, 1, true);   // PCM
        view.setUint16(22, 1, true);   view.setUint32(24, sampleRate, true);
        view.setUint32(28, sampleRate * 2, true); view.setUint16(32, 2, true);
        view.setUint16(34, 16, true);
        ws(36, 'data'); view.setUint32(40, samples.length * 2, true);
        let off = 44;
        for (let i = 0; i < samples.length; i++) {
            const s = Math.max(-1, Math.min(1, samples[i]));
            view.setInt16(off, s < 0 ? s * 0x8000 : s * 0x7FFF, true);
            off += 2;
        }
        return new Blob([buf], { type: 'audio/wav' });
    }

    let isRecording  = false;
    let audioCtx, micSource, scriptProc, micStream;
    let audioSamples = [];
    let chunkInterval;
    let fullTranscript = '';
    let timerInterval;
    let seconds       = 0;
    let pendingChunks = 0;

    function setProcessing(delta) {
        pendingChunks = Math.max(0, pendingChunks + delta);
        const el = document.getElementById('processing-indicator');
        if (el) el.style.display = pendingChunks > 0 ? 'block' : 'none';
    }

    function flushAndSend() {
        if (audioSamples.length === 0) return;
        const total  = audioSamples.reduce((s, a) => s + a.length, 0);
        const merged = new Float32Array(total);
        let off = 0;
        audioSamples.forEach(a => { merged.set(a, off); off += a.length; });
        audioSamples = [];
        sendWavChunk(encodeWAV(merged, audioCtx ? audioCtx.sampleRate : 16000));
    }

    const sendWavChunk = (wavBlob) => {
        setProcessing(+1);
        const reader = new FileReader();
        reader.readAsDataURL(wavBlob); // → data:audio/wav;base64,...
        reader.onloadend = async () => {
            try {
                const response = await fetch('{{ route('student.memorization.transcribe') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ audio_chunk: reader.result })
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.text && data.text.trim()) {
                        fullTranscript += data.text.trim() + ' ';
                        transcriptContainer.innerHTML = `<span class="final-transcript">${fullTranscript}</span>`;
                    }
                }
            } catch (err) {
                console.error('Transcription error:', err);
            } finally {
                setProcessing(-1);
            }
        };
    };

    const startRecording = async () => {
        if (isRecording) return;
        fullTranscript = '';
        transcriptContainer.innerHTML = '<span class="placeholder">Connecting to microphone...</span>';
        recordingOutput.style.display = 'block';
        try {
            micStream  = await navigator.mediaDevices.getUserMedia({ audio: true });
            audioCtx   = new (window.AudioContext || window.webkitAudioContext)({ sampleRate: 16000 });
            micSource  = audioCtx.createMediaStreamSource(micStream);
            scriptProc = audioCtx.createScriptProcessor(4096, 1, 1);
            audioSamples = [];

            scriptProc.onaudioprocess = (e) => {
                if (isRecording) audioSamples.push(new Float32Array(e.inputBuffer.getChannelData(0)));
            };
            micSource.connect(scriptProc);
            scriptProc.connect(audioCtx.destination);

            isRecording = true;
            recordButton.innerHTML = '<i class="fas fa-stop"></i> Stop Recording';
            recordButton.classList.add('recording');
            transcriptContainer.innerHTML = '<span class="placeholder">Listening… transcription will appear below.</span>';
            startTimer();

            // Send a WAV chunk every 2 seconds for rolling live transcription
            chunkInterval = setInterval(flushAndSend, 2000);
        } catch (err) {
            transcriptContainer.innerHTML = `<p class="text-danger"><strong>Error:</strong> ${err.message}</p>`;
        }
    };

    const stopRecording = () => {
        if (!isRecording) return;
        isRecording = false;
        clearInterval(chunkInterval);
        flushAndSend(); // send any remaining audio before stopping
        if (scriptProc) { scriptProc.disconnect(); scriptProc = null; }
        if (micSource)  { micSource.disconnect();  micSource  = null; }
        if (audioCtx)   { audioCtx.close();         audioCtx   = null; }
        if (micStream)  { micStream.getTracks().forEach(t => t.stop()); micStream = null; }
        recordButton.innerHTML = '<i class="fas fa-microphone"></i> Start Recording';
        recordButton.classList.remove('recording');
        stopTimer();
    };

    function formatTime(sec) {
        const m = Math.floor(sec / 60), s = sec % 60;
        return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }
    function startTimer() {
        seconds = 0;
        timerDisplay.textContent = formatTime(0);
        timerInterval = setInterval(() => { seconds++; timerDisplay.textContent = formatTime(seconds); }, 1000);
    }
    function stopTimer() { clearInterval(timerInterval); }

    recordButton.addEventListener('click', () => isRecording ? stopRecording() : startRecording());

    // Memorization status toggle
    const surahNumber = {{ $surahData['number'] }};
    document.querySelectorAll('.status-toggle').forEach(btn => {
        btn.addEventListener('click', async function () {
            const ayahNumber = this.dataset.ayah;
            const status = this.dataset.status;
            const card = this.closest('.ayah-card');

            // Update UI optimistically
            card.querySelectorAll('.status-toggle').forEach(b => b.classList.remove('active-status'));
            this.classList.add('active-status');

            try {
                await fetch('{{ route('student.memorization.status') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        surah_number: surahNumber,
                        ayah_number: parseInt(ayahNumber),
                        status: status,
                    })
                });
            } catch (error) {
                console.error('Failed to save memorization status:', error);
            }
        });
    });
});
</script>
@endsection
