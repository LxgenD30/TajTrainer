@extends('layouts.dashboard')

@section('title', 'Memorizing ' . $surahData['englishName'])
@section('user-role', 'Student • Memorization Test')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('content')
<style>
    .ms-wrap {
        max-width: 780px;
        margin: 0 auto;
        padding: 0 12px 40px;
        font-family: 'Cairo', sans-serif;
    }

    /* ── Header ─────────────────────────────────────────────────────────── */
    .ms-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 0 14px;
        gap: 12px;
        flex-wrap: wrap;
    }
    .ms-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #374151;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 8px 16px;
        border-radius: 50px;
        border: 2px solid #e5e7eb;
        background: #f9fafb;
        transition: all 0.2s;
    }
    .ms-back:hover { background: #e5e7eb; }
    .ms-surah-info { text-align: center; flex: 1; }
    .ms-surah-ar   { font-family: 'Amiri', serif; font-size: 2rem; font-weight: 900; color: #1a1a1a; line-height: 1; }
    .ms-surah-en   { font-size: 0.9rem; color: #6b7280; font-weight: 600; margin-top: 2px; }
    .ms-ayah-badge {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: white;
        padding: 8px 18px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    /* ── Progress dots ───────────────────────────────────────────────────── */
    .ms-dots {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: center;
        margin: 8px 0 4px;
    }
    .ms-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #e5e7eb;
        border: 2px solid #d1d5db;
        transition: all 0.3s;
        cursor: default;
    }
    .ms-dot.active   { background: #f59e0b; border-color: #d97706; transform: scale(1.3); }
    .ms-dot.ok       { background: #22c55e; border-color: #16a34a; }
    .ms-dot.error    { background: #ef4444; border-color: #dc2626; }

    /* ── Progress bar ────────────────────────────────────────────────────── */
    .ms-progress-bar {
        height: 6px;
        background: #e5e7eb;
        border-radius: 99px;
        margin: 10px 0 18px;
        overflow: hidden;
    }
    .ms-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #0a5c36, #1abc9c);
        border-radius: 99px;
        transition: width 0.6s ease;
        width: 0%;
    }

    /* ── Screens ─────────────────────────────────────────────────────────── */
    .ms-screen {
        background: white;
        border-radius: 20px;
        border: 3px solid #e5e7eb;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        padding: 36px 28px;
        text-align: center;
        min-height: 260px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
    }

    /* Idle */
    .idle-icon { font-size: 4rem; color: #0a5c36; margin-bottom: 4px; }
    .idle-hint { font-size: 0.9rem; color: #9ca3af; max-width: 380px; line-height: 1.6; }
    .btn-begin {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 16px 40px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(10,92,54,0.3);
        transition: all 0.3s;
        font-family: 'Cairo', sans-serif;
    }
    .btn-begin:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(10,92,54,0.4); }
    .btn-begin .mic-icon { font-size: 1.3rem; }

    /* Active */
    .listening-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #22c55e;
        margin-bottom: 8px;
    }
    .pulse-ring {
        width: 14px; height: 14px;
        border-radius: 50%;
        background: #22c55e;
        animation: pulseRing 1.2s ease-in-out infinite;
    }
    @keyframes pulseRing {
        0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,.6); }
        50%      { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
    }
    .transcript-box {
        width: 100%;
        min-height: 120px;
        font-family: 'Amiri', serif;
        font-size: 1.9rem;
        line-height: 2.4;
        direction: rtl;
        text-align: right;
        word-break: break-word;
        color: #1a1a1a;
    }
    .word-ok    { color: #16a34a; }
    .word-bad   { color: #dc2626; text-decoration: line-through; }
    .word-sep   { color: #9ca3af; font-size: 1.4rem; padding: 0 6px; }
    .interim-box {
        width: 100%;
        direction: rtl;
        text-align: right;
        font-family: 'Amiri', serif;
        font-size: 1.5rem;
        color: #9ca3af;
        font-style: italic;
        min-height: 2rem;
    }
    .ayah-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #6b7280;
        background: #f3f4f6;
        padding: 4px 14px;
        border-radius: 50px;
    }

    /* Error */
    .error-header-icon { font-size: 3.5rem; color: #ef4444; }
    .error-title { font-size: 1.2rem; font-weight: 800; color: #1a1a1a; margin: 0; }
    .error-cmp {
        display: flex;
        align-items: center;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        width: 100%;
        margin: 8px 0;
    }
    .err-col { display: flex; flex-direction: column; align-items: center; gap: 6px; }
    .err-label { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #9ca3af; letter-spacing: .05em; }
    .err-word {
        font-family: 'Amiri', serif;
        font-size: 2rem;
        font-weight: 900;
        padding: 8px 20px;
        border-radius: 12px;
        direction: rtl;
    }
    .err-word.said { background: #fef2f2; color: #dc2626; border: 2px solid #fca5a5; }
    .err-word.want { background: #f0fdf4; color: #16a34a; border: 2px solid #86efac; }
    .err-divider { font-size: 1.4rem; color: #d1d5db; }
    .err-pos { font-size: 0.85rem; color: #6b7280; }
    .btn-retry, .btn-restart {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 26px;
        border-radius: 50px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid;
        font-family: 'Cairo', sans-serif;
    }
    .btn-retry   { background: #16a34a; color: white; border-color: #15803d; }
    .btn-retry:hover { background: #15803d; }
    .btn-restart { background: #f3f4f6; color: #374151; border-color: #d1d5db; }
    .btn-restart:hover { background: #e5e7eb; }
    .err-actions { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }

    /* Success */
    .success-icon { font-size: 4.5rem; color: #22c55e; }
    .success-title { font-size: 1.5rem; font-weight: 900; color: #1a1a1a; margin: 0; }
    .success-sub { color: #6b7280; font-size: 0.95rem; }
    .btn-back-surah {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: white;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.2s;
    }
    .btn-back-surah:hover { opacity: 0.9; transform: translateY(-1px); }

    /* Stop button */
    .ms-stop-bar {
        display: flex;
        justify-content: center;
        margin-top: 18px;
    }
    .btn-stop {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 26px;
        background: #fef2f2;
        color: #dc2626;
        border: 2px solid #fca5a5;
        border-radius: 50px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
    }
    .btn-stop:hover { background: #fee2e2; }

    /* Browser warning */
    .no-sr-warn {
        background: #fffbeb;
        border: 2px solid #fcd34d;
        border-radius: 12px;
        padding: 16px 20px;
        text-align: center;
        font-size: 0.9rem;
        color: #92400e;
        margin-top: 16px;
    }
</style>

<div class="ms-wrap">

    {{-- Header --}}
    <div class="ms-header">
        <a href="{{ route('student.memorization.surah', $surahData['number']) }}" class="ms-back">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <div class="ms-surah-info">
            <div class="ms-surah-ar">{{ $surahData['name'] }}</div>
            <div class="ms-surah-en">{{ $surahData['englishName'] }}</div>
        </div>
        <div class="ms-ayah-badge">
            Ayah <span id="hdr-ayah">1</span> / {{ $surahData['totalAyahs'] }}
        </div>
    </div>

    {{-- Ayah dots --}}
    <div class="ms-dots" id="ms-dots">
        @foreach ($ayahs as $i => $ayah)
            <div class="ms-dot" id="dot-{{ $i }}" title="Ayah {{ $ayah['number'] }}"></div>
        @endforeach
    </div>

    {{-- Progress bar --}}
    <div class="ms-progress-bar">
        <div class="ms-progress-fill" id="ms-prog"></div>
    </div>

    {{-- ── Idle screen ─────────────────────────────────────────────────── --}}
    <div class="ms-screen" id="scr-idle">
        <div class="idle-icon"><i class="fas fa-mosque"></i></div>
        <h2 style="margin:0; font-size:1.3rem; font-weight:900; color:#1a1a1a;">
            Ready to Memorize?
        </h2>
        <p style="margin:0; color:#4b5563; font-size:0.95rem; max-width:360px;">
            Recite <strong>{{ $surahData['englishName'] }}</strong> from memory —
            the text is hidden. Speak clearly and the app will follow along.
        </p>
        <p class="idle-hint">
            <i class="fas fa-info-circle"></i>
            If you say a wrong word, the session stops instantly and shows your mistake.
        </p>
        <button class="btn-begin" onclick="startSession()">
            <i class="fas fa-microphone mic-icon"></i> Begin Recitation
        </button>
    </div>

    {{-- ── Active screen ───────────────────────────────────────────────── --}}
    <div class="ms-screen" id="scr-active" style="display:none; align-items:flex-start; text-align:right;">
        <div style="display:flex; align-items:center; justify-content:space-between; width:100%;">
            <div class="listening-bar">
                <div class="pulse-ring"></div> Listening
            </div>
            <div class="ayah-label">Ayah <span id="act-ayah">1</span></div>
        </div>
        <div class="transcript-box" id="transcript-box"></div>
        <div class="interim-box" id="interim-box"></div>
    </div>

    {{-- ── Error screen ────────────────────────────────────────────────── --}}
    <div class="ms-screen" id="scr-error" style="display:none;">
        <div class="error-header-icon"><i class="fas fa-times-circle"></i></div>
        <p class="error-title">Mistake — Ayah <span id="err-ayah-num"></span></p>
        <div class="error-cmp">
            <div class="err-col">
                <div class="err-label">You said</div>
                <div class="err-word said" id="err-said"></div>
            </div>
            <div class="err-divider"><i class="fas fa-exchange-alt"></i></div>
            <div class="err-col">
                <div class="err-label">Expected</div>
                <div class="err-word want" id="err-want"></div>
            </div>
        </div>
        <p class="err-pos" id="err-pos"></p>
        <div class="err-actions">
            <button class="btn-retry" onclick="retryWord()">
                <i class="fas fa-redo"></i> Try Again
            </button>
            <button class="btn-retry" onclick="retryAyah()" style="background:#f59e0b; border-color:#d97706;">
                <i class="fas fa-step-backward"></i> Retry Ayah
            </button>
            <button class="btn-restart" onclick="restartSurah()">
                <i class="fas fa-sync"></i> Restart Surah
            </button>
        </div>
    </div>

    {{-- ── Success screen ──────────────────────────────────────────────── --}}
    <div class="ms-screen" id="scr-success" style="display:none;">
        <div class="success-icon"><i class="fas fa-check-circle"></i></div>
        <h2 class="success-title">Ma Sha Allah! 🌿</h2>
        <p class="success-sub">
            You've completed <strong>{{ $surahData['englishName'] }}</strong> —
            all {{ $surahData['totalAyahs'] }} ayahs recited correctly.
        </p>
        <a href="{{ route('student.memorization.surah', $surahData['number']) }}" class="btn-back-surah">
            <i class="fas fa-arrow-left"></i> Back to Surah
        </a>
    </div>

    {{-- Stop button (visible while active) --}}
    <div class="ms-stop-bar" id="stop-bar" style="display:none;">
        <button class="btn-stop" onclick="stopSession()">
            <i class="fas fa-stop-circle"></i> Stop
        </button>
    </div>

    {{-- Browser warning shown if no SR support --}}
    <div id="no-sr" style="display:none;" class="no-sr-warn">
        <i class="fas fa-exclamation-triangle"></i>
        Arabic speech recognition is not supported in this browser.
        Please use <strong>Chrome</strong> or <strong>Edge</strong> for the memorization feature.
    </div>
</div>

{{-- Hidden verse data for JS comparison (NOT shown to user) --}}
<script id="verse-data" type="application/json">@json($ayahs)</script>

@endsection

@section('extra-scripts')
<script>
// ── Verse data ──────────────────────────────────────────────────────────────
const VERSES = JSON.parse(document.getElementById('verse-data').textContent);

// Pre-process each verse: build word list with diacritics + normalized form
const processed = VERSES.map(v => {
    const words     = v.text.trim().split(/\s+/).filter(w => w.length > 0);
    const normWords = words.map(normalizeAr);
    return { number: v.number, words, normWords };
});

// ── Arabic normalization (diacritics stripped for comparison) ───────────────
function normalizeAr(s) {
    return s
        .replace(/[\u064B-\u065F\u0610-\u061A\u0670]/g, '') // tashkeel (NOT \u0671 — handled below)
        .replace(/\u0640/g, '')              // tatweel
        .replace(/[\u0671أإآٱ]/g, 'ا')      // alef wasla + alef variants → plain alef
        .replace(/ة/g,  'ه')                // ta marbuta
        .replace(/ى/g,  'ي')                // alef maqsura
        .replace(/[^\u0600-\u06FF]/g, '')   // keep Arabic only
        .trim();
}

// ── State ───────────────────────────────────────────────────────────────────
let st = {
    phase:      'idle',   // idle | active | error | done
    ayahIdx:    0,
    wordIdx:    0,
    display:    [],       // [{t: word, ok: bool}]
    errInfo:    null,
};

// ── UI helpers ──────────────────────────────────────────────────────────────
function show(id) {
    ['scr-idle','scr-active','scr-error','scr-success'].forEach(s => {
        document.getElementById(s).style.display = (s === id) ? 'flex' : 'none';
    });
}

function updateProgress() {
    const pct = (st.ayahIdx / processed.length) * 100;
    document.getElementById('ms-prog').style.width = pct + '%';
    const num = processed[Math.min(st.ayahIdx, processed.length - 1)].number;
    document.getElementById('hdr-ayah').textContent  = num;
    document.getElementById('act-ayah').textContent  = num;
}

function setDot(idx, cls) {
    const d = document.getElementById('dot-' + idx);
    if (d) d.className = 'ms-dot ' + cls;
}

function renderTranscript() {
    const box = document.getElementById('transcript-box');
    box.innerHTML = st.display.map(w => {
        if (w.sep) return `<span class="word-sep"> ۝ </span>`;
        return `<span class="${w.ok ? 'word-ok' : 'word-bad'}">${w.t}</span>`;
    }).join(' ');
    box.scrollTop = box.scrollHeight;
}

// ── Speech Recognition ──────────────────────────────────────────────────────
const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
let recog = null;

if (!SR) {
    document.getElementById('no-sr').style.display = 'block';
} else {
    recog = new SR();
    recog.lang            = 'ar-SA';
    recog.continuous      = true;
    recog.interimResults  = true;
    recog.maxAlternatives = 3;

    recog.onresult = evt => {
        for (let i = evt.resultIndex; i < evt.results.length; i++) {
            if (evt.results[i].isFinal) {
                // Pick the alternative that matches the most upcoming expected words
                const alts = evt.results[i];
                let best = alts[0].transcript.trim();
                let bestScore = -1;
                const curVerse = processed[st.ayahIdx];
                if (curVerse) {
                    for (let a = 0; a < alts.length; a++) {
                        const altWords = alts[a].transcript.trim().split(/\s+/).filter(Boolean);
                        let score = 0;
                        for (let w = 0; w < altWords.length; w++) {
                            const nw = normalizeAr(altWords[w]);
                            if (nw && curVerse.normWords[st.wordIdx + w] === nw) score++;
                            else break;
                        }
                        if (score > bestScore) { bestScore = score; best = alts[a].transcript.trim(); }
                    }
                }
                processFinal(best);
                document.getElementById('interim-box').textContent = '';
            } else {
                document.getElementById('interim-box').textContent = evt.results[i][0].transcript;
            }
        }
    };

    recog.onerror = e => {
        if (e.error === 'no-speech' || e.error === 'aborted') return;
        console.warn('SR error:', e.error);
    };

    recog.onend = () => {
        if (st.phase === 'active') { try { recog.start(); } catch(e) {} }
    };
}

// ── Core comparison ─────────────────────────────────────────────────────────
function processFinal(text) {
    if (st.phase !== 'active') return;

    const spoken = text.trim().split(/\s+/).filter(Boolean);

    for (const word of spoken) {
        if (st.phase !== 'active') return;

        const normWord = normalizeAr(word);
        if (!normWord) continue;

        const verse    = processed[st.ayahIdx];
        const expected = verse.normWords[st.wordIdx];

        if (normWord === expected) {
            // ✓ Correct word — display the diacritized expected form, not the plain spoken word
            st.display.push({ t: verse.words[st.wordIdx], ok: true });
            st.wordIdx++;

            if (st.wordIdx >= verse.words.length) {
                // Ayah finished
                setDot(st.ayahIdx, 'ok');
                st.display.push({ sep: true });
                st.ayahIdx++;
                st.wordIdx = 0;

                if (st.ayahIdx >= processed.length) {
                    // Surah complete
                    st.phase = 'done';
                    if (recog) recog.stop();
                    renderTranscript();
                    document.getElementById('ms-prog').style.width = '100%';
                    document.getElementById('stop-bar').style.display = 'none';
                    document.querySelectorAll('.ms-dot').forEach(d => d.className = 'ms-dot ok');
                    show('scr-success');
                    return;
                }

                updateProgress();
                setDot(st.ayahIdx, 'active');
            }

            renderTranscript();
        } else {
            // ✗ Wrong word — stop immediately
            st.display.push({ t: word, ok: false });
            st.phase = 'error';
            st.errInfo = {
                said:    word,
                want:    verse.words[st.wordIdx],
                ayahNum: verse.number,
                wordPos: st.wordIdx + 1,
                total:   verse.words.length,
            };

            if (recog) recog.stop();
            renderTranscript();
            setDot(st.ayahIdx, 'error');
            showError();
            return;
        }
    }

    document.getElementById('interim-box').textContent = '';
}

function showError() {
    const e = st.errInfo;
    document.getElementById('err-ayah-num').textContent = e.ayahNum;
    document.getElementById('err-said').textContent     = e.said;
    document.getElementById('err-want').textContent     = e.want;
    document.getElementById('err-pos').textContent      = `Word ${e.wordPos} of ${e.total}`;
    document.getElementById('stop-bar').style.display   = 'none';
    show('scr-error');
}

// ── Controls ────────────────────────────────────────────────────────────────
function startSession() {
    if (!recog) {
        document.getElementById('no-sr').style.display = 'block';
        return;
    }
    st = { phase: 'active', ayahIdx: 0, wordIdx: 0, display: [], errInfo: null };
    document.getElementById('transcript-box').innerHTML = '';
    document.getElementById('interim-box').textContent  = '';
    document.querySelectorAll('.ms-dot').forEach(d => d.className = 'ms-dot');
    updateProgress();
    setDot(0, 'active');
    show('scr-active');
    document.getElementById('stop-bar').style.display = 'flex';
    try { recog.start(); }
    catch(e) { alert('Could not start mic: ' + e.message); st.phase = 'idle'; show('scr-idle'); }
}

function stopSession() {
    st.phase = 'idle';
    if (recog) { try { recog.stop(); } catch(e) {} }
    document.getElementById('stop-bar').style.display  = 'none';
    document.getElementById('interim-box').textContent = '';
    show('scr-idle');
}

function retryWord() {
    // Remove the wrong display word, stay at same position
    st.display = st.display.filter(w => !w.ok === false || w.ok); // keep only ok words
    st.display = st.display.filter(w => w.ok || w.sep);           // remove wrong word
    st.phase   = 'active';
    setDot(st.ayahIdx, 'active');
    document.getElementById('interim-box').textContent = '';
    show('scr-active');
    document.getElementById('stop-bar').style.display = 'flex';
    renderTranscript();
    if (recog) { try { recog.start(); } catch(e) {} }
}

function retryAyah() {
    // Go back to the beginning of the current ayah
    // Remove all display words from current ayah onward
    // Count how many words belong to completed ayahs
    let keepCount = 0;
    for (let i = 0; i < st.ayahIdx; i++) {
        keepCount += processed[i].words.length + 1; // +1 for separator
    }
    st.display = st.display.slice(0, keepCount);
    st.wordIdx = 0;
    st.phase   = 'active';
    setDot(st.ayahIdx, 'active');
    document.getElementById('interim-box').textContent = '';
    show('scr-active');
    document.getElementById('stop-bar').style.display = 'flex';
    renderTranscript();
    if (recog) { try { recog.start(); } catch(e) {} }
}

function restartSurah() {
    st = { phase: 'idle', ayahIdx: 0, wordIdx: 0, display: [], errInfo: null };
    document.getElementById('transcript-box').innerHTML = '';
    document.getElementById('interim-box').textContent  = '';
    document.getElementById('ms-prog').style.width      = '0%';
    document.getElementById('hdr-ayah').textContent     = '1';
    document.querySelectorAll('.ms-dot').forEach(d => d.className = 'ms-dot');
    show('scr-idle');
}
</script>
@endsection


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
