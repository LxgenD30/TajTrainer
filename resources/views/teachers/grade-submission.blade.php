@extends('layouts.dashboard')

@section('title', 'Grade Submission')
@section('user-role', 'Teacher • Grade Assignment')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
@php
    $analysisForDetails = is_array($submission->tajweed_analysis)
        ? $submission->tajweed_analysis
        : json_decode($submission->tajweed_analysis ?? '[]', true);

    if (!is_array($analysisForDetails)) {
        $analysisForDetails = [];
    }

    $recitedText = trim((string) (
        $submission->transcription
        ?: ($analysisForDetails['whisper_transcription'] ?? '')
        ?: ($analysisForDetails['transcribed_text'] ?? '')
        ?: ($analysisForDetails['whisper_transcription_raw'] ?? '')
    ));

    $pauseMarkerCount = $analysisForDetails['pause_markers']['count'] ?? 0;
    $overall = $analysisForDetails['overall_score'] ?? [];

    $suggestedScore = null;
    if ($submission->tajweed_score) {
        $suggestedScore = round(($submission->tajweed_score / 100) * $submission->assignment->total_marks, 1);
    }

    $defaultScore = old('score', $submission->score->score ?? $suggestedScore ?? '');

    $defaultFeedback = old('feedback', $submission->score->feedback ?? '');
    if (!$defaultFeedback && isset($overall['feedback'])) {
        $defaultFeedback = $overall['feedback'];
    }

    $tajweedComponent = $overall['tajweed_rules_score'] ?? null;
    $wordComponent = $overall['word_accuracy'] ?? ($analysisForDetails['text_accuracy'] ?? null);
    $referenceComponent = $overall['reference_similarity'] ?? null;
    $pronunciationComponent = $overall['pronunciation_accuracy'] ?? null;

    $ruleCards = [
        [
            'key' => 'madd_analysis',
            'title' => 'مد (Madd - Elongation)',
            'description' => 'Proper elongation of vowel sounds (2-6 counts)',
            'total_key' => 'total_elongations',
            'correct_key' => 'correct_elongations',
            'success_message' => 'Excellent! No issues detected in Madd elongations.',
        ],
        [
            'key' => 'idgham_bila_ghunnah_analysis',
            'title' => 'ادغام بلا غنة (Idgham Bila Ghunnah)',
            'description' => 'Merging without nasalization (letters ر and ل)',
            'total_key' => 'total_occurrences',
            'correct_key' => 'correct_pronunciation',
            'success_message' => 'Excellent! No issues detected in Idgham Bila Ghunnah.',
        ],
        [
            'key' => 'idgham_bi_ghunnah_analysis',
            'title' => 'ادغام بغنة (Idgham Bi Ghunnah)',
            'description' => 'Merging with nasalization (letters و م ن ي)',
            'total_key' => 'total_occurrences',
            'correct_key' => 'correct_pronunciation',
            'success_message' => 'Excellent! No issues detected in Idgham Bi Ghunnah.',
        ],
    ];

    $scoreColor = $submission->tajweed_score >= 90
        ? '#2e7d32'
        : ($submission->tajweed_score >= 70 ? '#558b2f' : ($submission->tajweed_score >= 60 ? '#ef6c00' : '#c62828'));
@endphp

<style>
    :root {
        --grade-bg: #f6f8f5;
        --grade-card: #ffffff;
        --grade-border: #e2e8e0;
        --grade-heading: #0a5c36;
        --grade-text: #1f2937;
        --grade-muted: #5f6f65;
        --grade-accent: #1abc9c;
        --grade-shadow: 0 12px 28px rgba(14, 28, 18, 0.08);
    }

    .grade-shell {
        max-width: 1600px;
        margin: 0 auto;
        padding: 0;
    }

    .grade-hero {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border: 2px solid #0a4a2b;
        border-radius: 20px;
        color: #fff;
        padding: 28px 30px;
        margin-bottom: 24px;
        box-shadow: 0 14px 32px rgba(10, 92, 54, 0.24);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .grade-hero h1 {
        margin: 0 0 8px;
        font-size: 2rem;
        line-height: 1.2;
    }

    .grade-hero p {
        margin: 0;
        opacity: 0.95;
        font-size: 1.02rem;
    }

    .hero-highlight {
        color: #f4d03f;
    }

    .hero-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        border: 2px solid #3d3520;
        border-radius: 12px;
        background: linear-gradient(135deg, #d4af37, #f4d03f);
        color: #111827;
        font-weight: 700;
        padding: 10px 16px;
        white-space: nowrap;
    }

    .grade-grid {
        display: grid;
        grid-template-columns: minmax(280px, 330px) minmax(0, 1fr) minmax(300px, 360px);
        gap: 20px;
        align-items: start;
    }

    .grade-card {
        background: var(--grade-card);
        border: 1px solid var(--grade-border);
        border-radius: 16px;
        box-shadow: var(--grade-shadow);
        padding: 20px;
    }

    .grade-sticky {
        position: sticky;
        top: 18px;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e6ece5;
    }

    .card-header h2,
    .card-header h3 {
        margin: 0;
        color: var(--grade-heading);
        font-family: 'El Messiri', serif;
        line-height: 1.2;
    }

    .card-subtitle {
        margin: 4px 0 0;
        color: var(--grade-muted);
        font-size: 0.9rem;
    }

    .main-stack {
        display: grid;
        gap: 20px;
    }

    .form-grid {
        display: grid;
        gap: 14px;
    }

    .field-label {
        display: block;
        margin-bottom: 6px;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--grade-heading);
    }

    .input-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .grade-input,
    .grade-textarea {
        width: 100%;
        border: 1px solid #cfd9d0;
        border-radius: 10px;
        padding: 10px 12px;
        font-family: 'Cairo', sans-serif;
        color: var(--grade-text);
        background: #fff;
    }

    .grade-input {
        font-size: 1.1rem;
        font-weight: 700;
    }

    .grade-textarea {
        font-size: 0.93rem;
        min-height: 140px;
        line-height: 1.6;
        resize: vertical;
    }

    .grade-submit {
        width: 100%;
        border: 0;
        border-radius: 10px;
        padding: 11px 16px;
        cursor: pointer;
        color: #fff;
        font-weight: 700;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        box-shadow: 0 10px 18px rgba(10, 92, 54, 0.2);
    }

    .note-box {
        border-radius: 12px;
        border: 1px solid #dde7de;
        background: #f8fbf8;
        padding: 12px 14px;
    }

    .note-box + .note-box {
        margin-top: 12px;
    }

    .note-title {
        margin: 0 0 6px;
        font-size: 0.85rem;
        color: var(--grade-muted);
        font-weight: 700;
    }

    .note-value {
        margin: 0;
        color: var(--grade-text);
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 4px 10px;
    }

    .chip-green {
        color: #2e7d32;
        background: #e7f6ea;
    }

    .chip-red {
        color: #b3261e;
        background: #fce8e6;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .detail-item {
        border: 1px solid #deeadf;
        border-radius: 12px;
        background: #f8fcf8;
        padding: 12px 14px;
    }

    .detail-label {
        margin: 0 0 6px;
        font-size: 0.8rem;
        color: var(--grade-muted);
        font-weight: 700;
    }

    .detail-value {
        margin: 0;
        color: var(--grade-text);
        line-height: 1.5;
    }

    .quran-verse-display {
        border: 1px solid #deeadf;
        background: #f6fbf7;
        padding: 16px;
        border-radius: 12px;
        direction: rtl;
        text-align: center;
        font-family: 'Amiri', serif;
        font-size: 1.55rem;
        line-height: 2.2;
        color: #000;
    }

    .quran-verse-display tajweed {
        display: inline;
        font-weight: 700;
    }

    .quran-verse-display tajweed.ham_wasl { color: #2e7d32; }
    .quran-verse-display tajweed.laam_shamsiyah { color: #ef6c00; }
    .quran-verse-display tajweed.ikhafa { color: #1565c0; }
    .quran-verse-display tajweed.idgham_ghunnah { color: #7b1fa2; }
    .quran-verse-display tajweed.idgham_shafawi { color: #00897b; }
    .quran-verse-display tajweed.gunnah { color: #2e7d32; }
    .quran-verse-display tajweed.madda_obligatory,
    .quran-verse-display tajweed.madda_normal,
    .quran-verse-display tajweed.madda_permissible { color: #c62828; }
    .quran-verse-display tajweed.slnt { color: #6d4c41; }
    .quran-verse-display tajweed.end { color: #555; }
    .quran-verse-display span.end { display: none; }

    .recited-text {
        border: 1px solid #deeadf;
        background: #f8fcf8;
        border-radius: 12px;
        padding: 14px;
    }

    .recited-text p {
        margin: 0;
        direction: rtl;
        text-align: right;
        color: #111827;
        font-family: 'Amiri', 'Traditional Arabic', serif;
        font-size: 1.5rem;
        line-height: 2;
    }

    .recited-note {
        margin-top: 10px;
        color: #0a5c36;
        font-size: 0.82rem;
    }

    .audio-box {
        border: 1px solid #deeadf;
        border-radius: 12px;
        background: #f8fcf8;
        padding: 14px;
    }

    .audio-row {
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: space-between;
        margin-top: 8px;
    }

    .download-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        color: #8f6f10;
        background: #f8f1d5;
        border: 1px solid #e5d699;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .breakdown-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .breakdown-item {
        border: 1px solid #deeadf;
        border-radius: 10px;
        background: #f8fcf8;
        padding: 10px;
    }

    .breakdown-item p {
        margin: 0;
    }

    .breakdown-item .k {
        font-size: 0.75rem;
        color: var(--grade-muted);
    }

    .breakdown-item .v {
        margin-top: 4px;
        font-size: 1.08rem;
        font-weight: 800;
        color: var(--grade-heading);
    }

    .rule-card {
        border: 1px solid #dbe8dd;
        border-radius: 12px;
        background: #fdfefe;
        padding: 14px;
    }

    .rule-head {
        display: flex;
        align-items: start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .rule-head h4 {
        margin: 0 0 6px;
        color: var(--grade-heading);
        font-size: 1.03rem;
    }

    .rule-head p {
        margin: 0;
        color: var(--grade-muted);
        font-size: 0.88rem;
    }

    .rule-percent {
        font-size: 1.25rem;
        font-weight: 800;
    }

    .rule-bar {
        height: 9px;
        border-radius: 999px;
        background: #e8ece7;
        overflow: hidden;
        margin: 8px 0 10px;
    }

    .rule-bar > span {
        display: block;
        height: 100%;
    }

    .rule-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 10px;
    }

    .rule-stat {
        border-radius: 8px;
        border: 1px solid #deeadf;
        background: #f8fcf8;
        text-align: center;
        padding: 8px;
    }

    .rule-stat p {
        margin: 0;
    }

    .rule-stat .k {
        font-size: 0.72rem;
        color: var(--grade-muted);
    }

    .rule-stat .v {
        margin-top: 4px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #0a5c36;
    }

    .rule-alert {
        border-radius: 8px;
        border: 1px solid #efc36f;
        background: #fff8ec;
        color: #8a5d11;
        padding: 10px;
        font-size: 0.85rem;
        line-height: 1.5;
    }

    .rule-ok {
        border-radius: 8px;
        border: 1px solid #a7d7ae;
        background: #edf8ef;
        color: #2e7d32;
        padding: 10px;
        font-size: 0.86rem;
    }

    .ai-card {
        border-color: #d9def8;
    }

    .ai-block {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #fafbff;
        padding: 12px;
        margin-bottom: 10px;
    }

    .ai-block h4 {
        margin: 0 0 8px;
        color: #3f51b5;
        font-size: 0.95rem;
    }

    .ai-block p,
    .ai-block ul {
        margin: 0;
        color: #374151;
        line-height: 1.6;
        font-size: 0.88rem;
    }

    .ai-block ul {
        padding-left: 18px;
    }

    .empty-state {
        border: 1px dashed #b7c6ba;
        border-radius: 12px;
        background: #f7faf8;
        color: #5f6f65;
        padding: 18px;
        text-align: center;
    }

    .muted {
        color: var(--grade-muted);
        font-size: 0.82rem;
    }

    @media (max-width: 1280px) {
        .grade-grid {
            grid-template-columns: minmax(280px, 330px) minmax(0, 1fr);
        }

        .grade-grid .ai-panel {
            grid-column: 1 / -1;
        }

        .grade-grid .ai-panel .grade-sticky {
            position: static;
        }
    }

    @media (max-width: 900px) {
        .grade-hero {
            padding: 20px;
            flex-direction: column;
            align-items: flex-start;
        }

        .grade-hero h1 {
            font-size: 1.65rem;
        }

        .grade-grid {
            grid-template-columns: 1fr;
        }

        .grade-sticky {
            position: static;
        }

        .detail-grid,
        .breakdown-grid,
        .rule-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="grade-hero">
    <div>
        <h1>Grading Submission by <span class="hero-highlight">{{ $submission->student->name }}</span></h1>
        <p>Evaluate tajweed performance with clear evidence, structure, and feedback.</p>
    </div>
    <a href="{{ route('teacher.student.submissions', ['classroom' => $submission->assignment->class_id, 'student' => $submission->student_id]) }}" class="hero-back">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Submissions</span>
    </a>
</section>

<div class="grade-shell">
    <div class="grade-grid">
        <aside class="grade-panel">
            <div class="grade-card grade-sticky">
                <div class="card-header">
                    <div>
                        <h2>{{ $submission->status === 'graded' ? 'Update Grade' : 'Grade Submission' }}</h2>
                        <p class="card-subtitle">Apply points and written feedback.</p>
                    </div>
                </div>

                @if($submission->score)
                    <div class="note-box">
                        <p class="note-title">Previously Graded</p>
                        <p class="note-value" style="font-weight: 800; color: #2e7d32;">
                            {{ $submission->score->score }}/{{ $submission->assignment->total_marks }}
                            ({{ round(($submission->score->score / $submission->assignment->total_marks) * 100, 1) }}%)
                        </p>
                    </div>
                @endif

                @if($submission->tajweed_score)
                    <div class="note-box">
                        <p class="note-title">AI Suggested Score</p>
                        <p class="note-value" style="font-weight: 800; color: #0a5c36;">
                            {{ $submission->tajweed_score }}% ({{ $submission->tajweed_grade }})
                        </p>
                        <p class="muted" style="margin-top: 4px;">
                            Suggested points: {{ $suggestedScore }}/{{ $submission->assignment->total_marks }}
                        </p>
                    </div>
                @endif

                <form method="POST" action="{{ route('teacher.submission.update.grade', $submission->id) }}" class="form-grid">
                    @csrf

                    <div>
                        <label class="field-label">Points Earned *</label>
                        <div class="input-row">
                            <input
                                type="number"
                                name="score"
                                min="0"
                                max="{{ $submission->assignment->total_marks }}"
                                step="0.5"
                                value="{{ $defaultScore }}"
                                required
                                class="grade-input"
                            >
                            <span class="muted" style="font-weight: 700;">/ {{ $submission->assignment->total_marks }}</span>
                        </div>
                        @error('score')
                            <p style="color: #b3261e; font-size: 0.8rem; margin: 6px 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label">Feedback *</label>
                        <textarea
                            name="feedback"
                            required
                            rows="6"
                            class="grade-textarea"
                            placeholder="Provide clear feedback for the student..."
                        >{{ $defaultFeedback }}</textarea>
                        @error('feedback')
                            <p style="color: #b3261e; font-size: 0.8rem; margin: 6px 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="grade-submit">
                        {{ $submission->status === 'graded' ? 'Update Grade' : 'Submit Grade' }}
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-stack">
            <section class="grade-card">
                <div class="card-header">
                    <div>
                        <h3>Submission Details</h3>
                        <p class="card-subtitle">Assignment, student and timeline information.</p>
                    </div>
                </div>

                <div class="detail-grid">
                    <div class="detail-item">
                        <p class="detail-label">Student</p>
                        <p class="detail-value" style="font-weight: 700;">{{ $submission->student->name }}</p>
                        <p class="detail-value">{{ $submission->student->email }}</p>
                    </div>
                    <div class="detail-item">
                        <p class="detail-label">Submission Time</p>
                        <p class="detail-value" style="font-weight: 700;">{{ $submission->created_at->format('M d, Y h:i A') }}</p>
                        @if($submission->created_at->gt($submission->assignment->due_date))
                            <span class="status-chip chip-red"><i class="fas fa-exclamation-triangle"></i> Late</span>
                        @else
                            <span class="status-chip chip-green"><i class="fas fa-check-circle"></i> On Time</span>
                        @endif
                    </div>
                </div>

                <div class="detail-item" style="margin-top: 12px;">
                    <p class="detail-label">Assignment</p>
                    <p class="detail-value" style="font-weight: 700;">
                        @if($submission->assignment->surah)
                            {{ $submission->assignment->surah }} ({{ $submission->assignment->start_verse }}@if($submission->assignment->end_verse)-{{ $submission->assignment->end_verse }}@endif)
                        @else
                            {{ $submission->assignment->material ? $submission->assignment->material->title : 'Assignment' }}
                        @endif
                    </p>
                    <p class="detail-value" style="margin-top: 6px;">{{ $submission->assignment->instructions }}</p>
                    <p class="muted" style="margin-top: 8px;">
                        {{ $submission->assignment->total_marks }} marks • Due {{ $submission->assignment->due_date->format('M d, Y') }}
                    </p>
                </div>

                @if(!empty($expectedRecitationDisplay) || $submission->assignment->expected_recitation)
                    @php
                        $expectedRecitation = $expectedRecitationDisplay ?: $submission->assignment->expected_recitation;
                        $hasTajweedMarkup = str_contains($expectedRecitation, '<tajweed') || str_contains($expectedRecitation, '<span class=end');
                    @endphp
                    <div style="margin-top: 12px;">
                        <p class="detail-label">Expected Recitation</p>
                        <div class="quran-verse-display">
                            @if($hasTajweedMarkup)
                                {!! $expectedRecitation !!}
                            @else
                                {!! nl2br(e($expectedRecitation)) !!}
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            <section class="grade-card">
                <div class="card-header">
                    <div>
                        <h3>Student Recitation</h3>
                        <p class="card-subtitle">Transcribed text and uploaded audio.</p>
                    </div>
                    <span class="status-chip chip-green">Whisper (Tarteel model)</span>
                </div>

                <div class="recited-text">
                    @if($recitedText !== '')
                        <p>{{ $recitedText }}</p>
                        @if($pauseMarkerCount > 0)
                            <p class="recited-note">Symbol ۝ marks long pauses and does not reduce word-accuracy score.</p>
                        @endif
                    @else
                        <p class="detail-value">Recited text is not available yet for this submission.</p>
                    @endif
                </div>

                @if($submission->audio_file_path)
                    @php
                        $audioExt = pathinfo($submission->audio_file_path, PATHINFO_EXTENSION);
                        $mimeTypes = [
                            'mp3' => 'audio/mpeg',
                            'wav' => 'audio/wav',
                            'webm' => 'audio/webm',
                            'm4a' => 'audio/mp4',
                            'ogg' => 'audio/ogg',
                            'oga' => 'audio/ogg',
                        ];
                        $detectedMime = $mimeTypes[strtolower($audioExt)] ?? 'audio/mpeg';
                        $audioUrl = \Storage::url($submission->audio_file_path);
                        $audioExists = \Storage::disk('public')->exists($submission->audio_file_path);
                    @endphp

                    <div class="audio-box" style="margin-top: 12px;">
                        <p class="detail-label">Voice Recording</p>

                        @if($audioExists)
                            <audio id="submissionAudio" controls preload="auto" controlsList="nodownload" style="width: 100%;" src="{{ $audioUrl }}">
                                <source src="{{ $audioUrl }}" type="{{ $detectedMime }}">
                                <source src="{{ $audioUrl }}" type="audio/webm">
                                <source src="{{ $audioUrl }}" type="audio/mpeg">
                                <source src="{{ $audioUrl }}" type="audio/wav">
                                <source src="{{ $audioUrl }}" type="audio/ogg">
                                <source src="{{ $audioUrl }}" type="audio/mp4">
                                <source src="{{ $audioUrl }}" type="audio/x-m4a">
                                Your browser does not support the audio element.
                            </audio>
                        @else
                            <div class="rule-alert">Audio file not found: {{ $submission->audio_file_path }}</div>
                        @endif

                        <div class="audio-row">
                            <span class="muted">File: {{ basename($submission->audio_file_path) }}</span>
                            <a href="{{ $audioUrl }}" download="{{ basename($submission->audio_file_path) }}" class="download-link">
                                <i class="fas fa-download"></i>
                                <span>Download</span>
                            </a>
                        </div>
                    </div>

                    <script>
                        const submissionAudio = document.getElementById('submissionAudio');
                        if (submissionAudio) {
                            submissionAudio.addEventListener('error', function () {
                                const error = document.createElement('div');
                                error.className = 'rule-alert';
                                error.style.marginTop = '10px';
                                error.textContent = 'Audio playback failed in browser. Download the file to listen offline.';
                                submissionAudio.insertAdjacentElement('afterend', error);
                            }, { once: true });
                        }
                    </script>
                @endif
            </section>

            @if($submission->tajweed_analysis)
                <section class="grade-card">
                    <div class="card-header">
                        <div>
                            <h3>Tajweed Analysis</h3>
                            <p class="card-subtitle">Rule-level scoring and detected issues.</p>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 1.75rem; font-weight: 900; color: {{ $scoreColor }}; line-height: 1;">
                                {{ $submission->tajweed_score }}%
                            </div>
                            <p class="muted" style="margin: 3px 0 0;">{{ $submission->tajweed_grade }}</p>
                        </div>
                    </div>

                    <div class="breakdown-grid" style="margin-bottom: 12px;">
                        <div class="breakdown-item">
                            <p class="k">Tajweed Rules (75%)</p>
                            <p class="v">{{ $tajweedComponent !== null ? number_format($tajweedComponent, 2) . '%' : 'N/A' }}</p>
                        </div>
                        <div class="breakdown-item">
                            <p class="k">Word Accuracy (20%)</p>
                            <p class="v">{{ $wordComponent !== null ? number_format($wordComponent, 2) . '%' : 'N/A' }}</p>
                        </div>
                        <div class="breakdown-item">
                            <p class="k">Reference Similarity (5%)</p>
                            <p class="v">{{ $referenceComponent !== null ? number_format($referenceComponent, 2) . '%' : 'N/A' }}</p>
                        </div>
                        <div class="breakdown-item">
                            <p class="k">Pronunciation</p>
                            <p class="v">{{ $pronunciationComponent !== null ? number_format($pronunciationComponent, 2) . '%' : 'N/A' }}</p>
                        </div>
                    </div>

                    @foreach($ruleCards as $ruleCard)
                        @if(isset($analysisForDetails[$ruleCard['key']]))
                            @php
                                $ruleData = $analysisForDetails[$ruleCard['key']];
                                $isApplicable = $ruleData['rule_applicable'] ?? true;
                                $rulePercentage = $ruleData['percentage'] ?? 0;
                                $ruleIssues = $ruleData['issues'] ?? [];
                                $ruleColor = !$isApplicable ? '#6b7280' : ($rulePercentage >= 90 ? '#2e7d32' : ($rulePercentage >= 70 ? '#558b2f' : '#ef6c00'));
                            @endphp

                            <article class="rule-card" style="margin-top: 10px;">
                                <div class="rule-head">
                                    <div>
                                        <h4>{{ $ruleCard['title'] }}</h4>
                                        <p>{{ $ruleCard['description'] }}</p>
                                    </div>
                                    <div class="rule-percent" style="color: {{ $ruleColor }};">{{ $rulePercentage }}%</div>
                                </div>

                                @if($isApplicable)
                                    <div class="rule-bar">
                                        <span style="width: {{ $rulePercentage }}%; background: linear-gradient(90deg, {{ $ruleColor }}, {{ $ruleColor }}bb);"></span>
                                    </div>

                                    <div class="rule-stats">
                                        <div class="rule-stat">
                                            <p class="k">Total</p>
                                            <p class="v">{{ $ruleData[$ruleCard['total_key']] ?? 0 }}</p>
                                        </div>
                                        <div class="rule-stat">
                                            <p class="k">Correct</p>
                                            <p class="v">{{ $ruleData[$ruleCard['correct_key']] ?? 0 }}</p>
                                        </div>
                                        <div class="rule-stat">
                                            <p class="k">Issues</p>
                                            <p class="v">{{ count($ruleIssues) }}</p>
                                        </div>
                                    </div>

                                    @if(count($ruleIssues) > 0)
                                        <div class="rule-alert">
                                            @foreach($ruleIssues as $index => $issue)
                                                <div style="margin-bottom: 7px;">
                                                    <strong>Issue {{ $index + 1 }}:</strong>
                                                    {{ $issue['note'] ?? $issue['issue'] ?? 'Issue detected' }}
                                                    @if(!empty($issue['recommendation']))
                                                        - {{ $issue['recommendation'] }}
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="rule-ok">{{ $ruleCard['success_message'] }}</div>
                                    @endif
                                @else
                                    <div class="rule-ok" style="background: #f4f5f7; border-color: #d1d5db; color: #4b5563;">
                                        Not present in this verse. No rule-specific feedback is generated.
                                    </div>
                                @endif
                            </article>
                        @endif
                    @endforeach

                    @if(isset($overall['feedback']))
                        <div class="note-box" style="margin-top: 12px; background: #f8f6e8; border-color: #e8ddb2;">
                            <p class="note-title" style="color: #7a6110;">AI-Generated Overall Feedback</p>
                            <p class="note-value" style="line-height: 1.7;">{{ $overall['feedback'] }}</p>
                        </div>
                    @endif

                    <p class="muted" style="margin: 12px 0 0;">
                        Analyzed with audio processing pipeline • {{ $submission->created_at->diffForHumans() }}
                    </p>
                </section>
            @else
                <section class="grade-card empty-state">
                    Tajweed analysis is not available for this submission yet.
                </section>
            @endif
        </main>

        <aside class="ai-panel">
            <div class="grade-card ai-card grade-sticky">
                <div class="card-header">
                    <div>
                        <h3 style="color: #3f51b5;">AI Assistant</h3>
                        <p class="card-subtitle">Reference guidance for teacher grading.</p>
                    </div>
                </div>

                @if(isset($analysisForDetails['ai_feedback']))
                    @if(isset($analysisForDetails['ai_feedback']['summary']))
                        <div class="ai-block">
                            <h4>Summary</h4>
                            <p>{{ $analysisForDetails['ai_feedback']['summary'] }}</p>
                        </div>
                    @endif

                    @if(isset($analysisForDetails['ai_feedback']['strengths']) && count($analysisForDetails['ai_feedback']['strengths']) > 0)
                        <div class="ai-block">
                            <h4>Strengths</h4>
                            <ul>
                                @foreach($analysisForDetails['ai_feedback']['strengths'] as $strength)
                                    <li>{{ $strength }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(isset($analysisForDetails['ai_feedback']['improvements']) && count($analysisForDetails['ai_feedback']['improvements']) > 0)
                        <div class="ai-block">
                            <h4>Improvements</h4>
                            <ul>
                                @foreach($analysisForDetails['ai_feedback']['improvements'] as $improvement)
                                    <li>
                                        @if(is_array($improvement))
                                            {{ $improvement['issue'] ?? '' }}
                                        @else
                                            {{ $improvement }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(isset($analysisForDetails['ai_feedback']['next_steps']))
                        <div class="ai-block">
                            <h4>Next Steps</h4>
                            <p>{{ $analysisForDetails['ai_feedback']['next_steps'] }}</p>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <p style="margin: 0 0 6px; font-weight: 700;">AI feedback not generated</p>
                        <p style="margin: 0; font-size: 0.85rem;">
                            {{ $overall['feedback'] ?? 'No AI feedback found for this submission.' }}
                        </p>
                    </div>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection