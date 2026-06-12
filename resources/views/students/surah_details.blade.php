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
</style>

<div class="surah-header">
    <h1 class="surah-title">{{ $surahData['name'] }}</h1>
    <p class="surah-meta">{{ $surahData['englishName'] }} • {{ $surahData['revelationType'] }} • {{ $surahData['numberOfAyahs'] }} Ayahs</p>
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
});
</script>
@endsection
