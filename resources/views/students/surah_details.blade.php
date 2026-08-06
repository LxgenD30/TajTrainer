@extends('layouts.dashboard')

@section('title', 'Surah Details')
@section('user-role', 'Student • Memorization')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('content')
<style>
    :root {
        --memorization-arabic-size: 2.6rem;
    }

    /* ── Card Heading ──────────────────────────────────────────────────── */
    .surah-header {
        background: #fff;
        border: 3px solid #2a2a2a;
        border-radius: 18px;
        padding: 26px 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }
    .sd-header-main {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
        padding-bottom: 18px;
        border-bottom: 3px solid #f5f5dc;
    }
    .sd-header-icon {
        width: 62px;
        height: 62px;
        border-radius: 16px;
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        box-shadow: 0 8px 18px rgba(10, 92, 54, 0.28);
        flex-shrink: 0;
    }
    .sd-header-text {
        flex: 1;
        min-width: 220px;
    }
    .surah-title {
        font-size: 2.6rem;
        font-weight: 800;
        font-family: 'Amiri', serif; /* A nice font for Arabic */
        color: #0a5c36;
        margin: 0;
        line-height: 1.2;
        text-align: left;
    }
    .surah-meta {
        font-size: 1.1rem;
        color: #333;
        margin: 6px 0 0;
        font-weight: 600;
    }
    .sd-start-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 26px;
        background: linear-gradient(135deg, #f4d03f, #d4af37);
        color: #1a1a1a;
        border: 2px solid #b8860b;
        border-radius: 50px;
        font-weight: 800;
        font-size: 1.05rem;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(180, 130, 20, 0.25);
        transition: all 0.22s ease;
        font-family: 'Cairo', sans-serif;
        white-space: nowrap;
    }
    .sd-start-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(180, 130, 20, 0.35);
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
        font-size: var(--memorization-arabic-size);
        font-family: 'Amiri', serif;
        text-align: right;
        line-height: 2.5;
        margin-bottom: 15px;
        color: #000000;
    }

    .arabic-size-control {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 16px;
        padding: 12px 16px;
        border-radius: 12px;
        border: 2px solid #deeadf;
        background: #f8fcf8;
        flex-wrap: wrap;
    }
    .arabic-size-control label {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0a5c36;
    }
    .arabic-size-control input[type="range"] {
        width: 200px;
        accent-color: #0a5c36;
    }
    .arabic-size-value {
        min-width: 52px;
        text-align: right;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0a5c36;
    }

    /* ── Tajweed colour key (requested scheme) ───────────────────────────── */
    .ayah-text tajweed,
    .ayah-text [class] {
        /* default: inherit (unstyled rules remain readable) */
    }

    /* Grey: silent letters / not pronounced in continuity */
    .ayah-text tajweed.ham_wasl,
    .ayah-text tajweed.laam_shamsiyah,
    .ayah-text tajweed.lam_shamsiyah,
    .ayah-text tajweed.silent,
    .ayah-text tajweed.slnt {
        color: #9e9e9e;
    }

    /* Olive / yellow-green: normal madd (2) */
    .ayah-text tajweed.madda_normal {
        color: #9cad32;
    }

    /* Orange: separated madd (2/4/6) */
    .ayah-text tajweed.madda_permissible {
        color: #f39c12;
    }

    /* Red: connected madd (4/5) */
    .ayah-text tajweed.madda_obligatory,
    .ayah-text tajweed.madda_prolonged {
        color: #e53935;
    }

    /* Dark red / maroon: necessary madd (6) */
    .ayah-text tajweed.madda_necessary {
        color: #7b1f1f;
    }

    /* Green: ghunnah / ikhfa' */
    .ayah-text tajweed.ghunnah,
    .ayah-text tajweed.ghn,
    .ayah-text tajweed.ikhfa,
    .ayah-text tajweed.ikhfa_shafawi,
    .ayah-text tajweed.idgham_ghunnah,
    .ayah-text tajweed.idgham_bi_ghunnah,
    .ayah-text tajweed.iqlab {
        color: #1f8f45;
    }

    /* Light blue: qalqalah (echo) */
    .ayah-text tajweed.qalaqah,
    .ayah-text tajweed.qalqalah {
        color: #5bc0eb;
    }

    /* Dark blue: tafkhim (heavy letters) */
    .ayah-text tajweed.tafkheem,
    .ayah-text tajweed.tafkhim,
    .ayah-text tajweed.heavy,
    .ayah-text tajweed.mufakham,
    .ayah-text tajweed.full_mouth,
    .ayah-text tajweed.isti_la {
        color: #0f3d91;
    }

    /* Additional rule colors kept for readability */
    .ayah-text tajweed.idgham_no_ghunnah,
    .ayah-text tajweed.idgham_shafawi {
        color: #8b6b00;
    }
    /* Verse-end circle already styled via .ayah-number; hide duplicate span */
    .ayah-text span.end       { display: none; }
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
        margin-top: 16px;
        flex-wrap: wrap;
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
    <div class="sd-header-main">
        <div class="sd-header-icon"><i class="fas fa-book-quran"></i></div>
        <div class="sd-header-text">
            <h1 class="surah-title">{{ $surahData['name'] }}</h1>
            <p class="surah-meta">{{ $surahData['englishName'] }} • {{ $surahData['revelationType'] }} • {{ $surahData['numberOfAyahs'] }} Ayahs</p>
        </div>
        <a href="{{ route('student.memorization.start', $surahData['number']) }}" class="sd-start-btn">
            <i class="fas fa-play-circle"></i> Start Memorizing
        </a>
    </div>
    <div class="arabic-size-control">
        <label for="memorizationArabicSize"><i class="fas fa-text-height"></i> Arabic Text Size</label>
        <input type="range" id="memorizationArabicSize" min="2.0" max="4.5" step="0.1" value="2.6" aria-label="Adjust Arabic text size">
        <span class="arabic-size-value" id="memorizationArabicSizeValue">2.6rem</span>
    </div>
</div>

<div class="ayah-grid">
    @foreach ($surahData['ayahs'] as $ayah)
        @php $currentStatus = $statuses[$ayah['numberInSurah']] ?? 'not_memorized'; @endphp
        <div class="ayah-card" data-ayah="{{ $ayah['numberInSurah'] }}">
            <p class="ayah-text">{!! $ayah['text'] !!} <span class="ayah-number">{{ $ayah['numberInSurah'] }}</span></p>
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
    const sizeSlider = document.getElementById('memorizationArabicSize');
    const sizeValue = document.getElementById('memorizationArabicSizeValue');
    if (sizeSlider && sizeValue) {
        const saved = localStorage.getItem('memorizationArabicSizeRem');
        const initial = saved ? parseFloat(saved) : parseFloat(sizeSlider.value);
        if (!isNaN(initial)) {
            sizeSlider.value = initial.toFixed(1);
            document.documentElement.style.setProperty('--memorization-arabic-size', initial.toFixed(1) + 'rem');
            sizeValue.textContent = initial.toFixed(1) + 'rem';
        }
        sizeSlider.addEventListener('input', function() {
            const next = parseFloat(this.value);
            if (isNaN(next)) return;
            document.documentElement.style.setProperty('--memorization-arabic-size', next.toFixed(1) + 'rem');
            sizeValue.textContent = next.toFixed(1) + 'rem';
            localStorage.setItem('memorizationArabicSizeRem', next.toFixed(1));
        });
    }

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
