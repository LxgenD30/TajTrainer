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

    // Restore diacritics for display only (does not affect scoring) by aligning
    // the transcription words to the diacritized expected text.
    $recitedTextDisplay = $recitedText;
    $expectedForRestore = $analysisForDetails['expected_text'] ?? '';
    if ($expectedForRestore === '') {
        $expectedForRestore = preg_replace('/<[^>]+>/u', ' ', (string) ($submission->assignment->expected_recitation ?? ''));
    }
    $expectedForRestore = html_entity_decode($expectedForRestore, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $expectedForRestore = preg_replace('/[\x{0660}-\x{0669}0-9]/u', '', $expectedForRestore);
    $expectedForRestore = preg_replace('/\s+/u', ' ', trim($expectedForRestore));

    if ($recitedText !== '' && $expectedForRestore !== '') {
        $normForMatch = static function ($s) {
            $s = html_entity_decode((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $s = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}]/u', '', $s);
            $s = str_replace('ـ', '', $s);
            $s = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $s);
            $s = str_replace(['ى'], 'ي', $s);
            $s = str_replace('ة', 'ه', $s);
            return preg_replace('/[^\p{Arabic}]/u', '', $s);
        };

        $diacritizedByNorm = [];
        foreach (preg_split('/\s+/u', trim($expectedForRestore)) as $ew) {
            if ($ew === '') {
                continue;
            }
            $n = $normForMatch($ew);
            if ($n !== '' && !isset($diacritizedByNorm[$n])) {
                $diacritizedByNorm[$n] = $ew;
            }
        }

        $restored = [];
        foreach (preg_split('/\s+/u', trim($recitedText)) as $tok) {
            if ($tok === '') {
                continue;
            }
            if ($tok === '۝' || str_contains($tok, '۝')) {
                $restored[] = $tok;
                continue;
            }
            $n = $normForMatch($tok);
            if (isset($diacritizedByNorm[$n])) {
                $restored[] = $diacritizedByNorm[$n];
                continue;
            }
            $best = '';
            $bestScore = 0;
            foreach ($diacritizedByNorm as $en => $ew) {
                similar_text($n, $en, $p);
                if ($p > $bestScore && $p >= 60) {
                    $bestScore = $p;
                    $best = $ew;
                }
            }
            $restored[] = $best !== '' ? $best : $tok;
        }
        $recitedTextDisplay = implode(' ', $restored);
    }

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

    $aiFeedback = $analysisForDetails['ai_feedback'] ?? null;
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
        --grade-gold: #d4af37;
        --grade-shadow: 0 12px 28px rgba(14, 28, 18, 0.08);
    }

    .grade-shell {
        max-width: 1600px;
        margin: 0 auto;
        padding: 0;
    }

    /* ============ HERO ============ */
    .grade-hero {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border: 3px solid #0a4a2b;
        border-radius: 20px;
        color: #fff;
        padding: 30px 34px;
        margin-bottom: 28px;
        box-shadow: 0 14px 32px rgba(10, 92, 54, 0.24);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .grade-hero h1 {
        margin: 0 0 6px;
        font-size: 2.2rem;
        line-height: 1.2;
        color: #fff;
    }

    .grade-hero p {
        margin: 0;
        opacity: 0.95;
        font-size: 1.1rem;
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
        padding: 12px 20px;
        white-space: nowrap;
        font-size: 1.05rem;
        transition: all 0.2s ease;
    }

    .hero-back:hover {
        background: linear-gradient(135deg, #f4d03f, #d4af37);
        transform: translateY(-2px);
    }

    /* ============ 2-COLUMN LAYOUT ============ */
    .grade-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 380px);
        gap: 28px;
        align-items: start;
    }

    /* ============ LEFT COLUMN CARDS ============ */
    .left-stack {
        display: grid;
        gap: 24px;
        min-width: 0;
    }

    .grade-card {
        background: var(--grade-card);
        border: 2px solid #2a2a2a;
        border-radius: 18px;
        box-shadow: var(--grade-shadow);
        padding: 28px;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 3px solid #0a5c36;
    }

    .card-header h2,
    .card-header h3 {
        margin: 0;
        color: var(--grade-heading);
        font-family: 'El Messiri', serif;
        font-size: 1.45rem;
        line-height: 1.2;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-subtitle {
        margin: 4px 0 0;
        color: var(--grade-muted);
        font-size: 1rem;
    }

    /* Detail grid */
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .detail-item {
        border: 2px solid #deeadf;
        border-radius: 12px;
        background: #f8fcf8;
        padding: 16px 18px;
    }

    .detail-item.wide {
        grid-column: 1 / -1;
    }

    .detail-label {
        margin: 0 0 8px;
        font-size: 0.92rem;
        color: var(--grade-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .detail-value {
        margin: 0;
        color: var(--grade-text);
        line-height: 1.6;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .detail-value .muted {
        color: var(--grade-muted);
        font-weight: 400;
    }

    /* Status chips */
    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 6px 14px;
    }

    .chip-green { color: #2e7d32; background: #e7f6ea; }
    .chip-red { color: #b3261e; background: #fce8e6; }

    /* Quran verse display */
    .quran-verse-display {
        border: 2px solid #deeadf;
        background: #f6fbf7;
        padding: 22px;
        border-radius: 14px;
        direction: rtl;
        text-align: center;
        font-family: 'Amiri', serif;
        font-size: 2rem;
        line-height: 2.3;
        color: #000;
        overflow-wrap: break-word;
    }

    .quran-verse-display tajweed { display: inline; font-weight: 700; }
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

    /* Recited text */
    .recited-text {
        border: 2px solid #deeadf;
        background: #f8fcf8;
        border-radius: 12px;
        padding: 20px;
    }

    .recited-text p {
        margin: 0;
        direction: rtl;
        text-align: right;
        color: #111827;
        font-family: 'Amiri', 'Traditional Arabic', serif;
        font-size: 1.8rem;
        line-height: 2.1;
        overflow-wrap: break-word;
    }

    .recited-note {
        margin-top: 14px !important;
        color: #0a5c36;
        font-size: 0.95rem !important;
        font-family: 'Cairo', sans-serif !important;
    }

    .no-transcription {
        color: var(--grade-muted);
        font-family: 'Cairo', sans-serif;
        font-size: 1.1rem;
        text-align: center;
        padding: 24px;
        border: 2px dashed #b7c6ba;
        border-radius: 12px;
        background: #f7faf8;
        margin: 0;
    }

    /* Audio box */
    .audio-box {
        border: 2px solid #deeadf;
        border-radius: 12px;
        background: #f8fcf8;
        padding: 20px;
    }

    .audio-row {
        display: flex;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    .download-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #8f6f10;
        background: #f8f1d5;
        border: 1px solid #e5d699;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 0.95rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .download-link:hover {
        background: #f1e3b3;
        transform: translateY(-1px);
    }

    .audio-player {
        width: 100%;
        border-radius: 10px;
    }

    /* ============ ANALYSIS SECTION ============ */
    .analysis-summary {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 20px;
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(26, 188, 156, 0.05));
        border: 2px solid rgba(10, 92, 54, 0.15);
        border-radius: 14px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .big-score {
        font-size: 3.2rem;
        font-weight: 900;
        line-height: 1;
        color: var(--grade-heading);
        min-width: 110px;
        text-align: center;
    }

    .big-score .score-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--grade-muted);
        margin-top: 4px;
    }

    .analysis-legend {
        flex: 1;
        min-width: 240px;
    }

    .legend-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 8px 0;
        border-bottom: 1px solid rgba(10, 92, 54, 0.1);
        font-size: 1.15rem;
    }

    .legend-row:last-child { border-bottom: none; }

    .legend-label {
        color: var(--grade-muted);
        font-weight: 600;
    }

    .legend-value {
        color: var(--grade-heading);
        font-weight: 800;
    }

    /* Rule cards (single column) */
    .rule-card {
        border: 2px solid #dbe8dd;
        border-radius: 14px;
        background: #fdfefe;
        padding: 24px;
        margin-bottom: 18px;
    }

    .rule-card:last-child { margin-bottom: 0; }

    .rule-head {
        display: flex;
        align-items: start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .rule-head h4 {
        margin: 0 0 4px;
        color: var(--grade-heading);
        font-size: 1.45rem;
    }

    .rule-head p {
        margin: 0;
        color: var(--grade-muted);
        font-size: 1.08rem;
    }

    .rule-percent {
        font-size: 2.2rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .rule-bar {
        height: 14px;
        border-radius: 999px;
        background: #e8ece7;
        overflow: hidden;
        margin: 12px 0 14px;
    }

    .rule-bar > span {
        display: block;
        height: 100%;
        border-radius: 999px;
        transition: width 0.8s ease;
    }

    .rule-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .rule-stat {
        border-radius: 10px;
        border: 2px solid #deeadf;
        background: #f8fcf8;
        text-align: center;
        padding: 12px;
    }

    .rule-stat p { margin: 0; }

    .rule-stat .k {
        font-size: 0.95rem;
        color: var(--grade-muted);
        font-weight: 700;
    }

    .rule-stat .v {
        margin-top: 4px;
        font-size: 1.7rem;
        font-weight: 800;
        color: #0a5c36;
    }

    .rule-alert {
        border-radius: 10px;
        border: 2px solid #efc36f;
        background: #fff8ec;
        color: #8a5d11;
        padding: 14px 16px;
        font-size: 1.1rem;
        line-height: 1.7;
    }

    .rule-feedback {
        border-radius: 10px;
        border: 2px solid #a7d7ae;
        background: #edf8ef;
        color: #15803d;
        padding: 12px 16px;
        font-size: 1.15rem;
        font-weight: 700;
        line-height: 1.6;
        margin-bottom: 12px;
    }

    .rule-feedback i { color: #d4af37; margin-right: 6px; }

    .arabic-word {
        font-family: 'Amiri', serif;
        font-size: 1.35rem;
        font-weight: 700;
        color: #0a5c36;
    }

    .rule-alert .issue-item {
        margin-bottom: 8px;
    }

    .rule-alert .issue-item:last-child { margin-bottom: 0; }

    .rule-ok {
        border-radius: 10px;
        border: 2px solid #a7d7ae;
        background: #edf8ef;
        color: #2e7d32;
        padding: 12px 16px;
        font-size: 1.05rem;
    }

    .ai-overall-feedback {
        margin-top: 16px;
        background: #f8f6e8;
        border: 2px solid #e8ddb2;
        border-radius: 12px;
        padding: 16px;
    }

    .ai-overall-feedback .note-title {
        color: #7a6110;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .ai-overall-feedback p {
        color: #333;
        line-height: 1.7;
        margin: 0;
        font-size: 1.05rem;
    }

    .muted {
        color: var(--grade-muted);
        font-size: 0.95rem;
    }

    /* ============ RIGHT SIDEBAR ============ */
    .grade-sidebar {
        position: sticky;
        top: 20px;
        display: grid;
        gap: 22px;
    }

    .grade-form-card {
        background: var(--grade-card);
        border: 2px solid #2a2a2a;
        border-radius: 18px;
        box-shadow: var(--grade-shadow);
        padding: 26px;
    }

    .grade-form-header {
        margin-bottom: 18px;
        padding-bottom: 14px;
        border-bottom: 3px solid #0a5c36;
    }

    .grade-form-header h3 {
        margin: 0;
        color: var(--grade-heading);
        font-family: 'El Messiri', serif;
        font-size: 1.4rem;
    }

    .note-box {
        border-radius: 12px;
        border: 2px solid #dde7de;
        background: #f8fbf8;
        padding: 14px;
    }

    .note-box + .note-box {
        margin-top: 12px;
    }

    .note-title {
        margin: 0 0 6px;
        font-size: 0.9rem;
        color: var(--grade-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .note-value {
        margin: 0;
        color: var(--grade-text);
        font-size: 1.15rem;
        font-weight: 700;
    }

    .form-grid {
        display: grid;
        gap: 18px;
        margin-top: 18px;
    }

    .field-label {
        display: block;
        margin-bottom: 8px;
        font-size: 1rem;
        font-weight: 700;
        color: var(--grade-heading);
    }

    .input-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .grade-input,
    .grade-textarea {
        width: 100%;
        border: 2px solid #cfd9d0;
        border-radius: 10px;
        padding: 12px 14px;
        font-family: 'Cairo', sans-serif;
        color: var(--grade-text);
        background: #fff;
        font-size: 1.1rem;
        transition: border-color 0.2s ease;
    }

    .grade-input:focus,
    .grade-textarea:focus {
        outline: none;
        border-color: #0a5c36;
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.12);
    }

    .grade-input {
        font-size: 1.4rem;
        font-weight: 800;
    }

    .grade-textarea {
        font-size: 1.05rem;
        min-height: 150px;
        line-height: 1.7;
        resize: vertical;
    }

    .grade-submit {
        width: 100%;
        border: 0;
        border-radius: 12px;
        padding: 16px 20px;
        cursor: pointer;
        color: #fff;
        font-weight: 800;
        font-size: 1.15rem;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        box-shadow: 0 10px 18px rgba(10, 92, 54, 0.2);
        font-family: 'El Messiri', sans-serif;
        transition: all 0.2s ease;
    }

    .grade-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 24px rgba(10, 92, 54, 0.3);
    }

    .field-error {
        color: #b3261e;
        font-size: 0.95rem;
        margin: 6px 0 0;
    }

    /* AI Assistant sidebar card */
    .ai-card {
        background: var(--grade-card);
        border: 2px solid #d9def8;
        border-radius: 18px;
        box-shadow: var(--grade-shadow);
        padding: 26px;
    }

    .ai-card-header {
        margin-bottom: 16px;
        padding-bottom: 14px;
        border-bottom: 3px solid #3f51b5;
    }

    .ai-card-header h3 {
        margin: 0;
        color: #3f51b5;
        font-family: 'El Messiri', serif;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ai-block {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #fafbff;
        padding: 16px;
        margin-bottom: 12px;
    }

    .ai-block h4 {
        margin: 0 0 8px;
        color: #3f51b5;
        font-size: 1.15rem;
    }

    .ai-block p,
    .ai-block ul {
        margin: 0;
        color: #374151;
        line-height: 1.7;
        font-size: 1.1rem;
    }

    .ai-block ul {
        padding-left: 20px;
    }

    .ai-empty {
        border: 1px dashed #b7c6ba;
        border-radius: 12px;
        background: #f7faf8;
        color: #5f6f65;
        padding: 20px;
        text-align: center;
        font-size: 1rem;
        line-height: 1.6;
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 1200px) {
        .grade-grid {
            grid-template-columns: 1fr;
        }

        .grade-sidebar {
            position: static;
        }
    }

    @media (max-width: 700px) {
        .grade-hero {
            padding: 22px;
        }

        .grade-hero h1 {
            font-size: 1.6rem;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .detail-item.wide {
            grid-column: 1;
        }

        .analysis-summary {
            flex-direction: column;
            align-items: flex-start;
        }

        .big-score {
            text-align: left;
        }

        .rule-stats {
            grid-template-columns: 1fr;
        }

        .grade-card {
            padding: 20px;
        }
    }
</style>

<!-- ============ HERO ============ -->
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

        <!-- ============ LEFT COLUMN: CONTENT REVIEW ============ -->
        <div class="left-stack">

            <!-- Submission Details -->
            <section class="grade-card">
                <div class="card-header">
                    <h3><i class="fas fa-clipboard-list"></i> Submission Details</h3>
                    @if($submission->created_at->gt($submission->assignment->due_date))
                        <span class="status-chip chip-red"><i class="fas fa-exclamation-triangle"></i> Late</span>
                    @else
                        <span class="status-chip chip-green"><i class="fas fa-check-circle"></i> On Time</span>
                    @endif
                </div>

                <div class="detail-grid">
                    <div class="detail-item">
                        <p class="detail-label">Student</p>
                        <p class="detail-value">{{ $submission->student->name }}</p>
                        <p class="detail-value muted">{{ $submission->student->email }}</p>
                    </div>
                    <div class="detail-item">
                        <p class="detail-label">Submitted</p>
                        <p class="detail-value">{{ $submission->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="detail-item wide">
                        <p class="detail-label">Assignment</p>
                        <p class="detail-value">
                            @if($submission->assignment->surah)
                                📖 {{ $submission->assignment->surah }} ({{ $submission->assignment->start_verse }}@if($submission->assignment->end_verse)-{{ $submission->assignment->end_verse }}@endif)
                            @else
                                {{ $submission->assignment->material ? $submission->assignment->material->title : 'Assignment' }}
                            @endif
                        </p>
                        @if($submission->assignment->instructions)
                            <p class="detail-value muted" style="margin-top: 6px;">{{ $submission->assignment->instructions }}</p>
                        @endif
                        <p class="muted" style="margin-top: 10px;">
                            {{ $submission->assignment->total_marks }} marks • Due {{ $submission->assignment->due_date->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                @if(!empty($expectedRecitationDisplay) || $submission->assignment->expected_recitation)
                    @php
                        $expectedRecitation = $expectedRecitationDisplay ?: $submission->assignment->expected_recitation;
                        $hasTajweedMarkup = str_contains($expectedRecitation, '<tajweed') || str_contains($expectedRecitation, '<span class=end');
                    @endphp
                    <div style="margin-top: 20px;">
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

            <!-- Student Recitation -->
            <section class="grade-card">
                <div class="card-header">
                    <h3><i class="fas fa-microphone-alt"></i> Student Recitation</h3>
                    <span class="status-chip chip-green">Whisper (Tarteel model)</span>
                </div>

                <div class="recited-text">
                    @if($recitedTextDisplay !== '')
                        <p>{{ $recitedTextDisplay }}</p>
                        @if($pauseMarkerCount > 0)
                            <p class="recited-note">Symbol ۝ marks long pauses and does not reduce word-accuracy score.</p>
                        @endif
                    @else
                        <p class="no-transcription">
                            <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                            Recited text is not available yet for this submission.
                        </p>
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

                    <div class="audio-box" style="margin-top: 16px;">
                        <p class="detail-label"><i class="fas fa-volume-up"></i> Voice Recording</p>

                        @if($audioExists)
                            <audio id="submissionAudio" controls preload="auto" controlsList="nodownload" class="audio-player" src="{{ $audioUrl }}">
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

            <!-- Tajweed Analysis -->
            @if($submission->tajweed_analysis)
                <section class="grade-card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Tajweed Analysis</h3>
                        <div style="text-align: right;">
                            <div style="font-size: 1.6rem; font-weight: 900; color: {{ $scoreColor }}; line-height: 1;">
                                {{ $submission->tajweed_score }}%
                            </div>
                            <p class="muted" style="margin: 3px 0 0;">{{ $submission->tajweed_grade }}</p>
                        </div>
                    </div>

                    <!-- Analysis summary -->
                    <div class="analysis-summary">
                        <div class="big-score">
                            {{ $submission->tajweed_score }}%
                            <span class="score-label">Overall</span>
                        </div>
                        <div class="analysis-legend">
                            <div class="legend-row">
                                <span class="legend-label">Tajweed Rules (75%)</span>
                                <span class="legend-value">{{ $tajweedComponent !== null ? number_format($tajweedComponent, 2) . '%' : 'N/A' }}</span>
                            </div>
                            <div class="legend-row">
                                <span class="legend-label">Word Accuracy (20%)</span>
                                <span class="legend-value">{{ $wordComponent !== null ? number_format($wordComponent, 2) . '%' : 'N/A' }}</span>
                            </div>
                            <div class="legend-row">
                                <span class="legend-label">Reference Similarity (5%)</span>
                                <span class="legend-value">{{ $referenceComponent !== null ? number_format($referenceComponent, 2) . '%' : 'N/A' }}</span>
                            </div>
                            <div class="legend-row">
                                <span class="legend-label">Pronunciation</span>
                                <span class="legend-value">{{ $pronunciationComponent !== null ? number_format($pronunciationComponent, 2) . '%' : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rule breakdowns -->
                    @foreach($ruleCards as $ruleCard)
                        @if(isset($analysisForDetails[$ruleCard['key']]))
                            @php
                                $ruleData = $analysisForDetails[$ruleCard['key']];
                                $isApplicable = $ruleData['rule_applicable'] ?? true;
                                $rulePercentage = $ruleData['percentage'] ?? 0;
                                $ruleIssues = $ruleData['issues'] ?? [];
                                $ruleFeedback = $ruleData['rule_feedback'] ?? '';
                                $ruleColor = !$isApplicable ? '#6b7280' : ($rulePercentage >= 90 ? '#2e7d32' : ($rulePercentage >= 70 ? '#558b2f' : '#ef6c00'));
                            @endphp

                            <article class="rule-card">
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

                                    @if(!empty($ruleFeedback))
                                        <div class="rule-feedback">
                                            <i class="fas fa-star"></i> {{ $ruleFeedback }}
                                        </div>
                                    @endif

                                    @if(count($ruleIssues) > 0)
                                        <div class="rule-alert">
                                            @foreach($ruleIssues as $index => $issue)
                                                <div class="issue-item">
                                                    <strong>Occurrence {{ $index + 1 }}:</strong>
                                                    @if(!empty($issue['word']))
                                                        <span class="arabic-word">{{ $issue['word'] }}</span> —
                                                    @endif
                                                    {{ $issue['issue'] ?? 'Not clearly produced in the recitation' }}
                                                    @if(!empty($issue['recommendation']))
                                                        <br><em style="color:#8a5d11;">{{ $issue['recommendation'] }}</em>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
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
                        <div class="ai-overall-feedback">
                            <p class="note-title">AI-Generated Overall Feedback</p>
                            <p>{{ $overall['feedback'] }}</p>
                        </div>
                    @endif

                    <p class="muted" style="margin: 16px 0 0;">
                        Analyzed with audio processing pipeline • {{ $submission->created_at->diffForHumans() }}
                    </p>
                </section>
            @else
                <section class="grade-card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Tajweed Analysis</h3>
                    </div>
                    <div class="no-transcription">
                        <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                        Tajweed analysis is not available for this submission yet.
                    </div>
                </section>
            @endif
        </div>

        <!-- ============ RIGHT COLUMN: GRADING + AI ============ -->
        <aside class="grade-sidebar">

            <!-- Grading Form -->
            <div class="grade-form-card">
                <div class="grade-form-header">
                    <h3><i class="fas fa-edit"></i> {{ $submission->status === 'graded' ? 'Update Grade' : 'Grade Submission' }}</h3>
                </div>

                @if($submission->score)
                    <div class="note-box">
                        <p class="note-title">Previously Graded</p>
                        <p class="note-value" style="color: #2e7d32;">
                            {{ $submission->score->score }}/{{ $submission->assignment->total_marks }}
                            ({{ round(($submission->score->score / $submission->assignment->total_marks) * 100, 1) }}%)
                        </p>
                    </div>
                @endif

                @if($submission->tajweed_score)
                    <div class="note-box">
                        <p class="note-title">AI Suggested Score</p>
                        <p class="note-value" style="color: #0a5c36;">
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
                            <span class="muted" style="font-weight: 700; font-size: 1.1rem;">/ {{ $submission->assignment->total_marks }}</span>
                        </div>
                        @error('score')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="field-label">Feedback *</label>
                        <textarea
                            name="feedback"
                            required
                            rows="7"
                            class="grade-textarea"
                            placeholder="Provide clear feedback for the student..."
                        >{{ $defaultFeedback }}</textarea>
                        @error('feedback')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="grade-submit">
                        <i class="fas fa-check-circle"></i>
                        {{ $submission->status === 'graded' ? 'Update Grade' : 'Submit Grade' }}
                    </button>
                </form>
            </div>

            <!-- AI Assistant -->
            <div class="ai-card">
                <div class="ai-card-header">
                    <h3><i class="fas fa-robot"></i> AI Assistant</h3>
                </div>

                @if(isset($aiFeedback['summary']) || isset($aiFeedback['strengths']) || isset($aiFeedback['improvements']) || isset($aiFeedback['next_steps']))
                    @if(isset($aiFeedback['summary']))
                        <div class="ai-block">
                            <h4>Summary</h4>
                            <p>{{ $aiFeedback['summary'] }}</p>
                        </div>
                    @endif

                    @if(isset($aiFeedback['strengths']) && count($aiFeedback['strengths']) > 0)
                        <div class="ai-block">
                            <h4>Strengths</h4>
                            <ul>
                                @foreach($aiFeedback['strengths'] as $strength)
                                    <li>{{ $strength }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(isset($aiFeedback['improvements']) && count($aiFeedback['improvements']) > 0)
                        <div class="ai-block">
                            <h4>Improvements</h4>
                            <ul>
                                @foreach($aiFeedback['improvements'] as $improvement)
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

                    @if(isset($aiFeedback['next_steps']))
                        <div class="ai-block">
                            <h4>Next Steps</h4>
                            <p>{{ $aiFeedback['next_steps'] }}</p>
                        </div>
                    @endif
                @else
                    <div class="ai-empty">
                        <p style="margin: 0 0 6px; font-weight: 700;">AI feedback not generated</p>
                        <p style="margin: 0; font-size: 0.95rem;">
                            {{ $overall['feedback'] ?? 'No AI feedback found for this submission.' }}
                        </p>
                    </div>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection
