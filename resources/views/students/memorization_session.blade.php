@extends('layouts.dashboard')

@section('title', 'Start Memorizing')
@section('user-role', 'Student • Learning Portal')

@section('content')
<div style="max-width:860px; margin:0 auto; padding:24px 16px;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-family:'Cairo',sans-serif; font-size:2rem; font-weight:900; color:#1a1a1a; margin:0;">
                <i class="fas fa-book-open" style="color:#0a5c36; margin-left:4px;"></i>
                Start Memorizing
            </h1>
            <p style="margin:4px 0 0; color:#6b7280; font-size:0.95rem;">
                Recite freely — your spoken Arabic will appear instantly below.
            </p>
        </div>
        <a href="{{ route('student.memorization') }}"
           style="display:inline-flex; align-items:center; gap:8px; padding:10px 20px; background:#f3f4f6; border:2px solid #d1d5db; border-radius:12px; color:#374151; font-weight:700; text-decoration:none; font-size:0.95rem; transition:all 0.2s;"
           onmouseover="this.style.background='#e5e7eb';" onmouseout="this.style.background='#f3f4f6';">
            <i class="fas fa-arrow-left"></i> Back to Surahs
        </a>
    </div>

    {{-- Transcript area --}}
    <div style="background:#fff; border-radius:20px; border:3px solid #e5e7eb; box-shadow:0 4px 20px rgba(0,0,0,0.06); padding:32px; margin-bottom:24px; min-height:240px; display:flex; flex-direction:column; align-items:center; justify-content:center;">

        <div id="transcript-container"
             style="width:100%; min-height:140px; font-family:'Amiri','Cairo',serif; font-size:1.8rem; line-height:2.4; direction:rtl; text-align:right; color:#1a1a1a; word-break:break-word;">
            <span id="placeholder-text" style="color:#9ca3af; font-size:1.2rem; font-family:'Cairo',sans-serif; font-style:italic;">
                Press <strong>Start</strong> and begin reciting…
            </span>
        </div>

        <div id="interim-container"
             style="width:100%; font-family:'Amiri','Cairo',serif; font-size:1.4rem; direction:rtl; text-align:right; color:#aaa; font-style:italic; margin-top:8px; min-height:2em;">
        </div>
    </div>

    {{-- Controls row --}}
    <div style="display:flex; align-items:center; justify-content:center; gap:20px; flex-wrap:wrap;">

        <button id="recordButton"
                onclick="toggleRecording()"
                style="display:inline-flex; align-items:center; gap:12px; padding:16px 40px; background:linear-gradient(135deg,#0a5c36,#1abc9c); color:white; border:none; border-radius:50px; font-size:1.1rem; font-weight:800; cursor:pointer; box-shadow:0 6px 20px rgba(10,92,54,0.35); transition:all 0.3s; font-family:'Cairo',sans-serif;"
                onmouseover="if(!this.classList.contains('recording')){this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 25px rgba(10,92,54,0.45)';}"
                onmouseout="this.style.transform=''; this.style.boxShadow=this.classList.contains('recording')?'0 6px 20px rgba(220,38,38,0.45)':'0 6px 20px rgba(10,92,54,0.35)';">
            <i class="fas fa-microphone" style="font-size:1.2rem;"></i>
            Start
        </button>

        <button id="clearButton"
                onclick="clearTranscript()"
                style="display:inline-flex; align-items:center; gap:8px; padding:14px 28px; background:#f9fafb; border:2px solid #d1d5db; border-radius:50px; color:#374151; font-size:0.95rem; font-weight:700; cursor:pointer; transition:all 0.2s; font-family:'Cairo',sans-serif;"
                onmouseover="this.style.background='#f3f4f6';" onmouseout="this.style.background='#f9fafb';">
            <i class="fas fa-trash-alt"></i> Clear
        </button>

        <div id="timer"
             style="font-family:'Cairo',monospace; font-size:1.1rem; font-weight:700; color:#6b7280; display:none; background:#f3f4f6; padding:12px 20px; border-radius:50px;">
            <i class="fas fa-clock"></i> <span id="timer-display">0:00</span>
        </div>
    </div>

    {{-- API availability notice --}}
    <div id="api-notice" style="margin-top:16px; text-align:center; font-size:0.85rem; color:#6b7280; display:none;">
        <i class="fas fa-info-circle"></i>
        <span id="api-notice-text"></span>
    </div>

</div>
@endsection

@section('extra-scripts')
<script>
    const transcriptContainer = document.getElementById('transcript-container');
    const interimContainer    = document.getElementById('interim-container');
    const recordButton        = document.getElementById('recordButton');
    const timerWrapper        = document.getElementById('timer');
    const timerDisplay        = document.getElementById('timer-display');
    const apiNotice           = document.getElementById('api-notice');
    const apiNoticeText       = document.getElementById('api-notice-text');

    let isRecording = false, fullTranscript = '', timerInterval, seconds = 0;

    // ── Helpers ──────────────────────────────────────────────────────────
    function normalizeAr(s) {
        return s.replace(/[\u0610-\u061A\u064B-\u065F\u0670\u0671]/g, '').replace(/\s+/g, ' ').trim();
    }
    function deduplicateAppend(existing, newText) {
        const trimmed = newText.trim();
        if (!trimmed) return existing;
        const eWords = existing.trim().split(/\s+/).filter(Boolean);
        const nWords = trimmed.split(/\s+/).filter(Boolean);
        let overlap = 0;
        const maxCheck = Math.min(8, eWords.length, nWords.length);
        for (let len = maxCheck; len >= 1; len--) {
            if (eWords.slice(-len).map(normalizeAr).join(' ') ===
                nWords.slice(0, len).map(normalizeAr).join(' ')) {
                overlap = len; break;
            }
        }
        const unique = nWords.slice(overlap);
        if (!unique.length) return existing;
        return (existing.trimEnd() + ' ' + unique.join(' ') + ' ').trimStart();
    }
    function renderTranscript(final, interim) {
        const placeholder = document.getElementById('placeholder-text');
        if (placeholder) placeholder.remove();
        transcriptContainer.innerHTML = final || '';
        interimContainer.textContent  = interim || '';
    }
    function startTimer() {
        seconds = 0; timerWrapper.style.display = 'flex';
        timerInterval = setInterval(() => {
            seconds++;
            timerDisplay.textContent = Math.floor(seconds/60) + ':' + String(seconds%60).padStart(2,'0');
        }, 1000);
    }
    function stopTimer() {
        clearInterval(timerInterval); timerWrapper.style.display = 'none';
    }

    // ── Primary: Web Speech API ───────────────────────────────────────────
    const SpeechRecognitionAPI = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition = null;

    if (SpeechRecognitionAPI) {
        recognition = new SpeechRecognitionAPI();
        recognition.lang = 'ar-SA';
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.maxAlternatives = 1;

        recognition.onresult = (event) => {
            let interim = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                if (event.results[i].isFinal) {
                    const text = event.results[i][0].transcript.trim();
                    if (text) fullTranscript = deduplicateAppend(fullTranscript, text);
                } else {
                    interim += event.results[i][0].transcript;
                }
            }
            renderTranscript(fullTranscript, interim);
        };
        recognition.onerror = (e) => {
            if (e.error !== 'no-speech' && e.error !== 'aborted')
                console.warn('Speech recognition error:', e.error);
        };
        recognition.onend = () => { if (isRecording) { try { recognition.start(); } catch(e) {} } };
    } else {
        // Show notice once DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            apiNotice.style.display = 'block';
            apiNoticeText.textContent = 'Your browser does not support instant Arabic recognition. Transcription will be sent to server every second.';
        });
    }

    // ── Fallback: WAV → Whisper (Firefox / non-Chrome) ────────────────────
    let audioCtx, micSource, scriptProc, micStream;
    let audioSamples = [], chunkInterval, isChunkInFlight = false;

    function encodeWAV(samples, sampleRate) {
        const buf = new ArrayBuffer(44 + samples.length * 2), view = new DataView(buf);
        const ws = (off, str) => { for (let i = 0; i < str.length; i++) view.setUint8(off + i, str.charCodeAt(i)); };
        ws(0,'RIFF'); view.setUint32(4,36+samples.length*2,true); ws(8,'WAVE'); ws(12,'fmt ');
        view.setUint32(16,16,true); view.setUint16(20,1,true); view.setUint16(22,1,true);
        view.setUint32(24,sampleRate,true); view.setUint32(28,sampleRate*2,true);
        view.setUint16(32,2,true); view.setUint16(34,16,true);
        ws(36,'data'); view.setUint32(40,samples.length*2,true);
        let off=44;
        for (let i=0;i<samples.length;i++){const s=Math.max(-1,Math.min(1,samples[i]));view.setInt16(off,s<0?s*0x8000:s*0x7FFF,true);off+=2;}
        return new Blob([buf],{type:'audio/wav'});
    }
    function getRMS(s){let sum=0;for(let i=0;i<s.length;i++)sum+=s[i]*s[i];return Math.sqrt(sum/s.length);}
    function flushAndSend(){
        if(isChunkInFlight||audioSamples.length===0)return;
        const total=audioSamples.reduce((s,a)=>s+a.length,0);
        const merged=new Float32Array(total);let off=0;
        audioSamples.forEach(a=>{merged.set(a,off);off+=a.length;});audioSamples=[];
        if(getRMS(merged)<0.01)return;
        isChunkInFlight=true;
        const reader=new FileReader();
        reader.readAsDataURL(encodeWAV(merged,audioCtx?audioCtx.sampleRate:16000));
        reader.onloadend=async()=>{
            try{
                const resp=await fetch('{{ route("student.memorization.transcribe") }}',{
                    method:'POST',
                    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
                    body:JSON.stringify({audio_chunk:reader.result,context:fullTranscript.slice(-120)})
                });
                if(resp.ok){const d=await resp.json();if(d.text&&d.text.trim()){fullTranscript=deduplicateAppend(fullTranscript,d.text);renderTranscript(fullTranscript,'');}}
            }catch(e){console.error(e);}
            finally{isChunkInFlight=false;}
        };
    }

    // ── Toggle / Clear ─────────────────────────────────────────────────────
    async function toggleRecording() {
        isRecording ? stopSession() : await startSession();
    }

    async function startSession() {
        fullTranscript = '';
        transcriptContainer.innerHTML = '<span style="color:#9ca3af;font-style:italic;font-family:Cairo,sans-serif;">Listening… speak now.</span>';
        interimContainer.textContent = '';

        if (recognition) {
            try {
                recognition.start();
                isRecording = true;
                recordButton.innerHTML = '<i class="fas fa-stop" style="font-size:1.2rem;"></i> Stop';
                recordButton.style.background = 'linear-gradient(135deg,#dc2626,#f87171)';
                recordButton.style.boxShadow  = '0 6px 20px rgba(220,38,38,0.45)';
                startTimer();
            } catch(err) {
                transcriptContainer.innerHTML = `<p style="color:red;">${err.message}</p>`;
            }
        } else {
            try {
                micStream=await navigator.mediaDevices.getUserMedia({audio:true});
                audioCtx=new (window.AudioContext||window.webkitAudioContext)({sampleRate:16000});
                micSource=audioCtx.createMediaStreamSource(micStream);
                scriptProc=audioCtx.createScriptProcessor(4096,1,1);
                audioSamples=[];
                scriptProc.onaudioprocess=(e)=>{if(isRecording)audioSamples.push(new Float32Array(e.inputBuffer.getChannelData(0)));};
                micSource.connect(scriptProc);scriptProc.connect(audioCtx.destination);
                isRecording=true;
                recordButton.innerHTML='<i class="fas fa-stop" style="font-size:1.2rem;"></i> Stop';
                recordButton.style.background='linear-gradient(135deg,#dc2626,#f87171)';
                recordButton.style.boxShadow='0 6px 20px rgba(220,38,38,0.45)';
                startTimer();
                chunkInterval=setInterval(flushAndSend,1000);
            } catch(err) {
                transcriptContainer.innerHTML=`<p style="color:red;">${err.message}</p>`;
            }
        }
    }

    function stopSession() {
        isRecording=false;
        if(recognition){recognition.stop();}
        else{clearInterval(chunkInterval);flushAndSend();
            if(scriptProc){scriptProc.disconnect();scriptProc=null;}
            if(micSource){micSource.disconnect();micSource=null;}
            if(audioCtx){audioCtx.close();audioCtx=null;}
            if(micStream){micStream.getTracks().forEach(t=>t.stop());micStream=null;}
        }
        recordButton.innerHTML='<i class="fas fa-microphone" style="font-size:1.2rem;"></i> Start';
        recordButton.style.background='linear-gradient(135deg,#0a5c36,#1abc9c)';
        recordButton.style.boxShadow='0 6px 20px rgba(10,92,54,0.35)';
        stopTimer();
    }

    function clearTranscript() {
        fullTranscript='';
        transcriptContainer.innerHTML='<span id="placeholder-text" style="color:#9ca3af;font-size:1.2rem;font-family:Cairo,sans-serif;font-style:italic;">Press <strong>Start</strong> and begin reciting…</span>';
        interimContainer.textContent='';
    }
</script>
@endsection
