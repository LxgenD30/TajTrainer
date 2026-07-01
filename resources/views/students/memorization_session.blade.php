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
    .error-header-icon { font-size: 3rem; color: #ef4444; }
    .error-title { font-size: 1.1rem; font-weight: 800; color: #1a1a1a; margin: 0 0 4px; }
    /* Full ayah display with mistake highlighted */
    .err-ayah-display {
        width: 100%;
        font-family: 'Amiri', serif;
        font-size: 1.75rem;
        line-height: 2.6;
        direction: rtl;
        text-align: right;
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px 18px;
        word-break: break-word;
        margin: 6px 0;
        box-shadow: inset 0 1px 4px rgba(0,0,0,0.04);
    }
    .err-w-ok  { color: #16a34a; }
    .err-w-rem { color: #9ca3af; }
    .err-w-bad {
        display: inline-block;
        background: #fef2f2;
        color: #dc2626;
        border: 2px solid #fca5a5;
        border-radius: 8px;
        padding: 0 7px;
        text-decoration: underline wavy #dc2626;
    }
    .err-detail {
        font-size: 0.88rem;
        color: #4b5563;
        text-align: center;
        margin: 2px 0;
        width: 100%;
    }
    .err-detail .said-hl { color: #dc2626; font-weight: 800; font-family: 'Amiri', serif; font-size: 1.1em; }
    .err-detail .want-hl { color: #16a34a; font-weight: 800; font-family: 'Amiri', serif; font-size: 1.1em; }
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
    <div class="ms-screen" id="scr-error" style="display:none; align-items:flex-start;">
        <div style="width:100%; text-align:center;">
            <div class="error-header-icon"><i class="fas fa-times-circle"></i></div>
            <p class="error-title">Mistake — Ayah <span id="err-ayah-num"></span></p>
        </div>
        {{-- Full ayah: green = correct words, red = mistake position, gray = remaining --}}
        <div class="err-ayah-display" id="err-ayah-display" dir="rtl"></div>
        <p class="err-detail" id="err-detail"></p>
        <div class="err-actions" style="width:100%; justify-content:center; margin-top:10px;">
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

// ── Muqatta'at letter names ──────────────────────────────────────────────────
const LETTER_NAMES = {
    'ا':'الف','ل':'لام','م':'ميم','ح':'حا','ي':'يا',
    'ط':'طا','س':'سين','ك':'كاف','ه':'ها','ع':'عين',
    'ر':'را','ص':'صاد','ق':'قاف','ن':'نون',
};
// Expand Muqatta'at: U+0653 (Arabic Maddah Above) marks each letter in text_uthmani Muqatta'at
// Returns {disp, comp} pairs: disp = letter char to display, comp = letter name user should say
// e.g. الٓمٓ → [{ا,'الف'},{ل,'لام'},{م,'ميم'}]
function expandIfMuqattaat(word) {
    if (!word.includes('\u0653')) return [{ disp: word, comp: word }];
    const base = word
        .replace(/[\u064B-\u065F\u0610-\u061A\u0640\u0653\u0670]/g, '')
        .replace(/[\u0671أإآٱ]/g, 'ا');
    const names = [...base].map(c => LETTER_NAMES[c]);
    if (names.length > 0 && names.every(n => n !== undefined)) {
        return [...base].map((c, i) => ({ disp: c, comp: names[i] }));
    }
    return [{ disp: word, comp: word }];
}

// Pre-process each verse: expand Muqatta'at, build display + comparison + human-readable arrays
const processed = VERSES.map(v => {
    const words     = [];  // display: individual letter chars for Muqattaat, original otherwise
    const compWords = [];  // what user should say: letter names for Muqattaat, original otherwise
    v.text.trim().split(/\s+/).filter(w => w.length > 0).forEach(w => {
            expandIfMuqattaat(w).forEach(({ disp, comp }) => {
                if (!normalizeAr(comp)) return; // skip stop marks / annotation-only tokens (e.g. \u06db \u06da)
                words.push(disp);
                compWords.push(comp);
            });
        });
    const normWords = compWords.map(normalizeAr); // compare against spoken letter names
    return { number: v.number, words, normWords, compWords };
});

// ── Arabic normalization (diacritics stripped for comparison) ───────────────
function normalizeAr(s) {
    return s
        .replace(/[\u064B-\u065F\u0610-\u061A]/g, '')  // strip tashkeel (NOT \u0671)
        .replace(/\u0640/g, '')              // tatweel
        .replace(/\u0670/g, '')              // strip dagger alef (long vowel handled by medial-alef strip below)
        .replace(/[\u0671أإآٱ]/g, 'ا')      // alef wasla + variants → plain alef
        .replace(/ة/g,  'ه')                // ta marbuta
        .replace(/ى/g,  'ي')                // alef maqsura
        .replace(/\u06E5/g, 'و')            // ۥ Small Waw → و
        .replace(/\u06E6/g, 'ي')            // ۦ Small Ya  → ي
        .replace(/[\u06D6-\u06E4\u06E7-\u06FF]/g, '') // strip Quranic annotation marks
        .replace(/([؀-ۿ])ا([؀-ۿ])/g, '$1$2') // strip medial alef (ذالك→ذلك, عالمين→علمين, كتاب→كتب)
        .replace(/^([وبفلك])ال(?=[تثدذرزسشصضطظن])/, '$1') // strip ال after connector before sun letter
        .replace(/ط/g, 'ت')                 // ط often transcribed as ت
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
                console.log('[Recitation Debug] Chosen alt:', JSON.stringify(best), '| score:', bestScore, '| alts:', [...alts].map(a => a.transcript));
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

    // ── Debug: open browser console (F12) to see full recitation vs expected ──
    console.group('[Recitation Debug] ' + new Date().toLocaleTimeString());
    console.log('Spoken raw   :', JSON.stringify(text));
    console.log('Spoken words :', spoken);
    console.log('Normalized   :', spoken.map(normalizeAr));
    const _v = processed[st.ayahIdx];
    if (_v) {
        const _wi = st.wordIdx;
        console.log('Expected norm:', _v.normWords.slice(_wi, _wi + spoken.length + 3));
        console.log('Expected comp:', _v.compWords.slice(_wi, _wi + spoken.length + 3));
        console.log('Ayah', _v.number, '| word pos', _wi + 1, '/', _v.words.length);
    }
    console.groupEnd();
    // ── End Debug ────────────────────────────────────────────────────────────
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
                want:    verse.compWords[st.wordIdx], // letter name for Muqattaat, original word otherwise
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
    const e     = st.errInfo;
    const verse = processed[st.ayahIdx];

    // Build full ayah: green = correctly said, red = mistake word (shows expected), gray = remaining
    const html = verse.words.map((w, i) => {
        if (i < st.wordIdx)   return `<span class="err-w-ok">${w}</span>`;
        if (i === st.wordIdx) return `<span class="err-w-bad" title="${e.said}">${w}</span>`;
        return `<span class="err-w-rem">${w}</span>`;
    }).join(' ');

    document.getElementById('err-ayah-display').innerHTML = html;
    document.getElementById('err-detail').innerHTML =
        `Word ${e.wordPos} of ${e.total} &mdash; ` +
        `You said <span class="said-hl">${e.said}</span>, ` +
        `expected <span class="want-hl">${e.want}</span>`;
    document.getElementById('err-ayah-num').textContent = e.ayahNum;
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
