@extends('layouts.dashboard')

@section('title', 'View Submission')
@section('user-role', 'Student • View Submission')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('content')
<style>
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #1a1a1a;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        padding: 12px 24px;
        border-radius: 50px;
        background: #d4af37;
        border: 3px solid #b8860b;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        white-space: nowrap;
    }
    
    .back-button:hover {
        background: #ffcc33;
        transform: translateY(-2px);
    }
    
    .assignment-header {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: start;
        position: relative;
        overflow: visible;
    }
    
    .header-content {
        flex: 1;
    }
    
    .header-content h1 {
        color: white;
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .header-content p {
        opacity: 0.9;
        font-size: 1.05rem;
        margin: 0;
    }
    
    .status-badge {
        padding: 12px 24px;
        border-radius: 15px;
        font-weight: 700;
        font-size: 1.1rem;
        display: inline-block;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .status-graded {
        background: #4caf50;
        color: white;
    }
    
    .status-pending {
        background: #ff9800;
        color: white;
    }
    
    .submitted-date {
        color: white;
        opacity: 0.8;
        font-size: 0.9rem;
        margin-top: 8px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .info-label {
        color: #666;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-value {
        color: #0a5c36;
        font-size: 1.1rem;
        font-weight: 700;
    }
    
    .info-value.score {
        color: #4caf50;
        font-size: 1.5rem;
    }
    
    .content-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .card-title {
        color: #0a5c36;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .text-content {
        color: #333;
        line-height: 1.8;
        white-space: pre-wrap;
    }
    
    .audio-player {
        width: 100%;
        margin-top: 15px;
        outline: none;
        border-radius: 10px;
    }
    
    .audio-info {
        display: flex;
        gap: 15px;
        align-items: center;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .audio-filename {
        font-size: 0.85rem;
        color: #666;
        flex: 1;
    }
    
    .download-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #d4af37;
        color: #0a5c36;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .download-btn:hover {
        background: #c19b2e;
    }
    
    .transcription-box {
        background: rgba(10, 92, 54, 0.05);
        padding: 20px;
        border-radius: 10px;
        border: 2px solid #0a5c36;
        margin-top: 20px;
    }
    
    .transcription-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .transcription-title {
        color: #0a5c36;
        font-weight: 700;
        font-size: 1rem;
    }
    
    .ai-badge {
        background: rgba(212, 175, 55, 0.2);
        color: #d4af37;
        padding: 4px 12px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .transcription-text {
        color: #333;
        line-height: 1.8;
        direction: rtl;
        text-align: right;
    }
    
    .analysis-section {
        background: white;
        padding: 25px;
        border-radius: 15px;
        border: 3px solid #2a2a2a;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.1);
    }
    
    .analysis-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .score-badge {
        font-size: 3rem;
        font-weight: 700;
        color: #0a5c36;
    }
    
    .score-details h3 {
        color: #d4af37;
        font-size: 1.5rem;
        margin-bottom: 5px;
    }
    
    .score-feedback {
        color: #666;
        line-height: 1.6;
    }
    
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    
    .metric-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        border: 2px solid rgba(10, 92, 54, 0.2);
        text-align: center;
    }
    
    .metric-icon {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }
    
    .metric-title {
        color: #0a5c36;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
    
    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        color: #4caf50;
    }
    
    .metric-subtitle {
        color: #666;
        font-size: 0.8rem;
        margin-top: 5px;
    }
    
    .feedback-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        border: 2px solid rgba(26, 188, 156, 0.3);
        margin-bottom: 25px;
    }
    
    .feedback-title {
        color: #1abc9c;
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .feedback-text {
        color: #333;
        line-height: 1.8;
    }
    
    .material-card {
        background: rgba(26, 188, 156, 0.05);
        padding: 20px;
        border-radius: 12px;
        border: 2px solid #1abc9c;
    }
    
    .material-title {
        color: #1abc9c;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .material-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #1abc9c;
        color: white;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .material-link:hover {
        background: #16a085;
    }
</style>

<!-- Assignment Header -->
<div class="assignment-header">
    <a href="{{ route('classroom.show', $assignment->class_id) }}" class="back-button" style="position: absolute; top: 20px; right: 30px; z-index: 10;">
        <i class="fas fa-arrow-left"></i> Back to Classroom
    </a>
    
    <div class="header-content">
        <h1>
            @if($assignment->surah)
                📖 {{ $assignment->surah }} ({{ $assignment->start_verse }}@if($assignment->end_verse)-{{ $assignment->end_verse }}@endif)
            @else
                {{ $assignment->material ? $assignment->material->title : 'Assignment' }}
            @endif
        </h1>
        <p>{{ $assignment->classroom->class_name }}</p>
    </div>
    <div style="text-align: right; margin-top: 50px;">
        @php
            $score = \App\Models\Score::where('assignment_id', $assignment->assignment_id)
                                     ->where('user_id', Auth::id())
                                     ->first();
        @endphp
        @if($score)
            <div class="status-badge status-graded">
                🏆 Your Score: {{ $score->score }}/{{ $assignment->total_marks }}
            </div>
        @else
            <div class="status-badge status-pending">
                ⏳ Awaiting Grading
            </div>
        @endif
        <div class="submitted-date">
            Submitted: {{ $submission->submitted_at->format('M d, Y h:i A') }}
        </div>
    </div>
</div>

<!-- Info Grid -->
<div class="info-grid">
    <div class="info-card">
        <div class="info-label"><i class="fas fa-calendar"></i> Due Date</div>
        <div class="info-value">{{ $assignment->due_date->format('M d, Y h:i A') }}</div>
    </div>
    <div class="info-card">
        <div class="info-label"><i class="fas fa-trophy"></i> Total Marks</div>
        <div class="info-value">{{ $assignment->total_marks }} points</div>
    </div>
</div>

<!-- Text Submission -->
@if($submission->text_submission)
<div class="content-card">
    <h3 class="card-title">📄 Your Written Response</h3>
    <p class="text-content">{{ $submission->text_submission }}</p>
</div>
@endif

<!-- Audio Submission -->
@if($submission->audio_file_path)
<div class="content-card">
    <h3 class="card-title">🎤 Audio Recording</h3>
    @php
        $audioUrl = \Storage::url($submission->audio_file_path);
        $audioExt = pathinfo($submission->audio_file_path, PATHINFO_EXTENSION);
        $mimeTypes = [
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'webm' => 'audio/webm',
            'm4a' => 'audio/mp4',
            'ogg' => 'audio/ogg',
        ];
        $detectedMime = $mimeTypes[strtolower($audioExt)] ?? 'audio/mpeg';
    @endphp
    <audio controls preload="auto" class="audio-player" src="{{ $audioUrl }}">
        <source src="{{ $audioUrl }}" type="{{ $detectedMime }}">
        Your browser does not support the audio element.
    </audio>
    <div class="audio-info">
        <div class="audio-filename">
            📁 {{ basename($submission->audio_file_path) }}
        </div>
        <a href="{{ $audioUrl }}" download="{{ basename($submission->audio_file_path) }}" class="download-btn">
            <i class="fas fa-download"></i> Download
        </a>
    </div>
    
    @if($submission->transcription)
    <div class="transcription-box">
        <div class="transcription-header">
            <span class="transcription-title">📝 AI Transcription</span>
            <span class="ai-badge">Whisper (Tarteel AI model)</span>
        </div>
        <p class="transcription-text">{{ $submission->transcription }}</p>
        @php
            $analysisForTranscription = is_string($submission->tajweed_analysis)
                ? json_decode($submission->tajweed_analysis, true)
                : ($submission->tajweed_analysis ?? []);
            $pauseMarkerCount = $analysisForTranscription['pause_markers']['count'] ?? 0;
        @endphp
        @if($pauseMarkerCount > 0)
            <div style="margin-top: 10px; font-size: 0.85rem; color: #0a5c36;">
                Note: The symbol ۝ marks a pause in your recitation and does not reduce word accuracy.
            </div>
        @endif
    </div>
    @endif
</div>
@endif

<!-- Tajweed Analysis -->
@if($submission->tajweed_analysis)
    @php
        $analysis = is_string($submission->tajweed_analysis) 
            ? json_decode($submission->tajweed_analysis, true) 
            : $submission->tajweed_analysis;
    @endphp
    
    @if(isset($analysis['overall_score']))
    @php
        $overall = $analysis['overall_score'];
        $wordAccuracy = $overall['word_accuracy'] ?? ($analysis['text_accuracy'] ?? null);
        $referenceSimilarity = $overall['reference_similarity'] ?? null;
        $tajweedRulesScore = $overall['tajweed_rules_score'] ?? null;
    @endphp
    <div class="analysis-section">
        <div class="analysis-header">
            <div class="score-badge">{{ $analysis['overall_score']['score'] ?? 0 }}%</div>
            <div class="score-details">
                <h3>✨ Tajweed Analysis Results</h3>
                <p class="score-feedback">{{ $analysis['overall_score']['feedback'] ?? 'Analysis complete' }}</p>
            </div>
        </div>
        
        @if($wordAccuracy !== null || $tajweedRulesScore !== null || $referenceSimilarity !== null || isset($analysis['madd_analysis']) || isset($analysis['idgham_bila_ghunnah_analysis']) || isset($analysis['idgham_bi_ghunnah_analysis']))
        <div class="metrics-grid">
            @if($tajweedRulesScore !== null)
            <div class="metric-card">
                <div class="metric-icon">📘</div>
                <div class="metric-title">Tajweed Rules Score</div>
                <div class="metric-value">{{ number_format($tajweedRulesScore, 2) }}%</div>
                <div class="metric-subtitle">Main grading component (75%)</div>
            </div>
            @endif

            @if($wordAccuracy !== null)
            <div class="metric-card">
                <div class="metric-icon">📖</div>
                <div class="metric-title">Word Accuracy</div>
                <div class="metric-value">{{ number_format($wordAccuracy, 2) }}%</div>
                <div class="metric-subtitle">Word-level precision (20%)</div>
            </div>
            @endif

            @if($referenceSimilarity !== null)
            <div class="metric-card">
                <div class="metric-icon">🎵</div>
                <div class="metric-title">Reference Similarity</div>
                <div class="metric-value">{{ number_format($referenceSimilarity, 2) }}%</div>
                <div class="metric-subtitle">Rhythm/tone alignment (5%)</div>
            </div>
            @endif
            
            @php
                $ruleCards = [
                    [
                        'key' => 'madd_analysis',
                        'icon' => '📏',
                        'title' => 'Madd (Elongation)',
                        'color' => '#d4af37',
                        'correct_key' => 'correct_elongations',
                        'total_key' => 'total_elongations',
                    ],
                    [
                        'key' => 'idgham_bila_ghunnah_analysis',
                        'icon' => '🔡',
                        'title' => 'Idgham Bila Ghunnah',
                        'color' => '#ff9800',
                        'correct_key' => 'correct_pronunciation',
                        'total_key' => 'total_occurrences',
                    ],
                    [
                        'key' => 'idgham_bi_ghunnah_analysis',
                        'icon' => '🌀',
                        'title' => 'Idgham Bi Ghunnah',
                        'color' => '#1abc9c',
                        'correct_key' => 'correct_pronunciation',
                        'total_key' => 'total_occurrences',
                    ],
                ];
            @endphp

            @foreach($ruleCards as $ruleCard)
                @if(isset($analysis[$ruleCard['key']]))
                    @php
                        $ruleData = $analysis[$ruleCard['key']];
                        $isApplicable = $ruleData['rule_applicable'] ?? true;
                    @endphp
                    <div class="metric-card">
                        <div class="metric-icon">{{ $ruleCard['icon'] }}</div>
                        <div class="metric-title">{{ $ruleCard['title'] }}</div>
                        <div class="metric-value" style="color: {{ $ruleCard['color'] }};">{{ $ruleData['percentage'] ?? 0 }}%</div>
                        <div class="metric-subtitle">
                            @if($isApplicable)
                                {{ $ruleData[$ruleCard['correct_key']] ?? 0 }}/{{ $ruleData[$ruleCard['total_key']] ?? 0 }} correct
                            @else
                                Not present in the verse
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        @endif
    </div>
    @endif
@endif

<!-- Teacher Feedback -->
@if($submission->teacher_feedback || (isset($score) && $score && $score->feedback))
<div class="feedback-card">
    <h3 class="feedback-title">👨‍🏫 Teacher Feedback</h3>
    <p class="feedback-text">{{ $submission->teacher_feedback ?? $score->feedback ?? 'No feedback yet.' }}</p>
</div>
@endif

<!-- Assignment Material -->
@if($assignment->material)
<div class="content-card">
    <div class="material-card">
        <h4 class="material-title">📚 Assignment Material: {{ $assignment->material->title }}</h4>
        <a href="{{ route('student.material.show', $assignment->material->material_id) }}" class="material-link">
            View Material <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endif
@endsection
