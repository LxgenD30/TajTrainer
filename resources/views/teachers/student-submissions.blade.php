@extends('layouts.dashboard')

@section('title', 'Student Submissions')
@section('user-role', 'Teacher • Grading Queue')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
@php
    // Bucket submissions by status
    $pendingSubmissions = $submissions->filter(function ($sub) {
        return !in_array($sub->status, ['graded']);
    });
    $gradedSubmissions = $submissions->filter(function ($sub) {
        return $sub->status === 'graded';
    });

    // Average of teacher-given scores (graded only)
    $gradedScores = $gradedSubmissions->map(function ($sub) {
        return $sub->score ? $sub->score->score : null;
    })->filter();
    $averageScore = $gradedScores->isNotEmpty()
        ? round($gradedScores->avg(), 1)
        : null;

    $totalSubmissions = $submissions->count();
@endphp

<style>
    .ss-hero {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border: 3px solid #0a4a2b;
        border-radius: 20px;
        color: #fff;
        padding: 28px 34px;
        margin-bottom: 26px;
        box-shadow: 0 14px 32px rgba(10, 92, 54, 0.24);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
    }

    .ss-hero:before {
        content: '';
        position: absolute;
        top: -80px;
        right: -80px;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .ss-hero-left {
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        z-index: 2;
    }

    .ss-avatar {
        width: 74px;
        height: 74px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f4d03f, #d4af37);
        color: #0a5c36;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 800;
        font-family: 'El Messiri', serif;
        border: 3px solid rgba(255,255,255,0.4);
        box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        flex-shrink: 0;
    }

    .ss-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .ss-hero h1 {
        margin: 0 0 4px;
        font-size: 2rem;
        line-height: 1.2;
        color: #fff;
    }

    .ss-hero .ss-sub {
        margin: 0;
        opacity: 0.95;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .ss-hero .ss-sub i {
        margin-right: 6px;
    }

    .ss-back {
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
        font-size: 1.02rem;
        transition: all 0.2s ease;
        position: relative;
        z-index: 2;
    }

    .ss-back:hover {
        background: linear-gradient(135deg, #f4d03f, #d4af37);
        transform: translateY(-2px);
    }

    /* ============ STATS STRIP ============ */
    .ss-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }

    .ss-stat {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 8px 20px rgba(14, 28, 18, 0.06);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.25s ease;
    }

    .ss-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 26px rgba(10, 92, 54, 0.12);
    }

    .ss-stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #fff;
        flex-shrink: 0;
    }

    .ss-stat-icon.blue { background: linear-gradient(135deg, #3498db, #2980b9); }
    .ss-stat-icon.amber { background: linear-gradient(135deg, #f39c12, #e67e22); }
    .ss-stat-icon.green { background: linear-gradient(135deg, #2ecc71, #27ae60); }
    .ss-stat-icon.gold { background: linear-gradient(135deg, #d4af37, #b8860b); }

    .ss-stat-value {
        font-size: 1.9rem;
        font-weight: 800;
        color: #0a5c36;
        line-height: 1;
    }

    .ss-stat-label {
        color: #666;
        font-size: 0.95rem;
        font-weight: 600;
        margin-top: 4px;
    }

    /* ============ TOOLBAR (Tabs + Search) ============ */
    .ss-toolbar {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 26px;
        box-shadow: 0 8px 20px rgba(14, 28, 18, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .ss-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .ss-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 50px;
        border: 2px solid #cfd9d0;
        background: #f8fcf8;
        color: #4b5563;
        font-weight: 700;
        font-size: 0.98rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Cairo', sans-serif;
    }

    .ss-tab:hover {
        border-color: #0a5c36;
        color: #0a5c36;
    }

    .ss-tab.active {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-color: #0a5c36;
        color: #fff;
        box-shadow: 0 6px 16px rgba(10, 92, 54, 0.25);
    }

    .ss-tab-badge {
        background: rgba(0,0,0,0.12);
        padding: 2px 9px;
        border-radius: 50px;
        font-size: 0.82rem;
    }

    .ss-tab.active .ss-tab-badge {
        background: rgba(255,255,255,0.25);
    }

    .ss-search {
        position: relative;
        min-width: 240px;
        flex: 0 1 300px;
    }

    .ss-search i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #0a5c36;
        font-size: 0.9rem;
    }

    .ss-search input {
        width: 100%;
        padding: 11px 16px 11px 38px;
        border: 2px solid #cfd9d0;
        border-radius: 50px;
        font-size: 0.98rem;
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        color: #1f2937;
        transition: all 0.2s ease;
    }

    .ss-search input:focus {
        outline: none;
        border-color: #0a5c36;
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.12);
    }

    /* ============ SUBMISSION CARDS ============ */
    .ss-list {
        display: grid;
        gap: 20px;
    }

    .ss-card {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(14, 28, 18, 0.07);
        padding: 26px 28px;
        display: flex;
        justify-content: space-between;
        gap: 24px;
        align-items: center;
        flex-wrap: wrap;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .ss-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        border-radius: 18px 0 0 18px;
    }

    .ss-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 34px rgba(10, 92, 54, 0.14);
    }

    .ss-card.pending { border-color: #2a2a2a; }
    .ss-card.pending::before { background: linear-gradient(180deg, #f39c12, #e67e22); }
    .ss-card.graded { border-color: #2a2a2a; }
    .ss-card.graded::before { background: linear-gradient(180deg, #2ecc71, #27ae60); }
    .ss-card.late::before { background: linear-gradient(180deg, #e74c3c, #c0392b); }

    .ss-card-body {
        flex: 1;
        min-width: 260px;
    }

    .ss-card-top {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .ss-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 800;
        padding: 5px 14px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .ss-status.pending { color: #b45309; background: #fef3c7; }
    .ss-status.pending_review { color: #1d4ed8; background: #dbeafe; }
    .ss-status.graded { color: #15803d; background: #dcfce7; }
    .ss-status.late { color: #b91c1c; background: #fee2e2; }

    .ss-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0a5c36;
        margin: 0;
        font-family: 'El Messiri', serif;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ss-title i {
        color: #d4af37;
    }

    .ss-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 22px;
        margin-top: 10px;
    }

    .ss-meta-item {
        color: #5f6f65;
        font-size: 0.98rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .ss-meta-item i {
        color: #0a5c36;
        width: 16px;
        text-align: center;
    }

    .ss-meta-item.has-audio i {
        color: #1abc9c;
    }

    .ss-meta-item .late-chip {
        color: #b91c1c;
        font-weight: 800;
        font-size: 0.85rem;
        background: #fee2e2;
        padding: 2px 10px;
        border-radius: 50px;
    }

    .ss-card-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 14px;
        min-width: 210px;
    }

    .ss-score {
        text-align: center;
        padding: 12px 18px;
        border-radius: 14px;
        min-width: 140px;
        border: 2px solid;
    }

    .ss-score .ss-score-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #5f6f65;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 4px;
    }

    .ss-score .ss-score-value {
        font-size: 1.7rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .ss-score.graded { background: #edf8ef; border-color: #a7d7ae; }
    .ss-score.graded .ss-score-value { color: #15803d; }
    .ss-score.ai { background: #eef2ff; border-color: #c7d2fe; }
    .ss-score.ai .ss-score-value { color: #1d4ed8; }
    .ss-score.none { background: #f8fafc; border-color: #e2e8f0; }
    .ss-score.none .ss-score-value { color: #94a3b8; }

    .ss-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 26px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.02rem;
        width: 100%;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        font-family: 'Cairo', sans-serif;
    }

    .ss-btn.primary {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        box-shadow: 0 6px 16px rgba(10, 92, 54, 0.25);
    }

    .ss-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(10, 92, 54, 0.32);
    }

    .ss-btn.outline {
        background: #fff;
        color: #0a5c36;
        border-color: #0a5c36;
    }

    .ss-btn.outline:hover {
        background: #0a5c36;
        color: #fff;
    }

    /* Empty state */
    .ss-empty {
        background: #fff;
        border: 2px dashed #b7c6ba;
        border-radius: 18px;
        padding: 60px 30px;
        text-align: center;
        color: #5f6f65;
    }

    .ss-empty i {
        font-size: 3.5rem;
        opacity: 0.35;
        margin-bottom: 14px;
        display: block;
    }

    .ss-empty h4 {
        color: #0a5c36;
        font-size: 1.4rem;
        margin: 0 0 6px;
        font-family: 'El Messiri', serif;
    }

    .ss-empty p {
        margin: 0;
        font-size: 1.05rem;
    }

    .ss-hidden { display: none !important; }

    @media (max-width: 720px) {
        .ss-hero { padding: 22px; }
        .ss-hero-left { flex-direction: column; align-items: flex-start; }
        .ss-card-right { align-items: stretch; width: 100%; }
        .ss-score { width: 100%; }
    }
</style>

<!-- ============ HERO ============ -->
<div class="ss-hero">
    <div class="ss-hero-left">
        <div class="ss-avatar">
            @if($student->profile_picture)
                <img src="{{ asset('storage/' . $student->profile_picture) }}" alt="Profile">
            @else
                {{ strtoupper(substr($student->name, 0, 1)) }}
            @endif
        </div>
        <div>
            <h1>{{ $student->name }}'s Submissions</h1>
            <p class="ss-sub">
                <span><i class="fas fa-envelope"></i>{{ $student->email }}</span>
                <span><i class="fas fa-chalkboard-teacher"></i>{{ $classroom->name }}</span>
                <span><i class="fas fa-clipboard-list"></i>{{ $totalSubmissions }} submission{{ $totalSubmissions != 1 ? 's' : '' }}</span>
            </p>
        </div>
    </div>
    <a href="{{ route('classroom.show', $classroom->id) }}" class="ss-back">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Class</span>
    </a>
</div>

@if($submissions->isEmpty())
    <!-- ============ EMPTY STATE ============ -->
    <div class="ss-empty">
        <i class="fas fa-check-double"></i>
        <h4>No Submissions Yet</h4>
        <p>{{ $student->name }} hasn't submitted any assignments for this class yet.</p>
    </div>
@else
    <!-- ============ STATS STRIP ============ -->
    <div class="ss-stats">
        <div class="ss-stat">
            <div class="ss-stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <div class="ss-stat-value">{{ $totalSubmissions }}</div>
                <div class="ss-stat-label">Total Submissions</div>
            </div>
        </div>
        <div class="ss-stat">
            <div class="ss-stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <div class="ss-stat-value">{{ $pendingSubmissions->count() }}</div>
                <div class="ss-stat-label">Pending Review</div>
            </div>
        </div>
        <div class="ss-stat">
            <div class="ss-stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="ss-stat-value">{{ $gradedSubmissions->count() }}</div>
                <div class="ss-stat-label">Graded</div>
            </div>
        </div>
        <div class="ss-stat">
            <div class="ss-stat-icon gold"><i class="fas fa-star"></i></div>
            <div>
                <div class="ss-stat-value">{{ $averageScore !== null ? number_format($averageScore, 1) . '%' : '—' }}</div>
                <div class="ss-stat-label">Avg Score</div>
            </div>
        </div>
    </div>

    <!-- ============ TOOLBAR ============ -->
    <div class="ss-toolbar">
        <div class="ss-tabs">
            <button class="ss-tab active" data-filter="all" onclick="filterSubmissions('all')">
                <i class="fas fa-layer-group"></i> All
                <span class="ss-tab-badge">{{ $totalSubmissions }}</span>
            </button>
            <button class="ss-tab" data-filter="pending" onclick="filterSubmissions('pending')">
                <i class="fas fa-hourglass-half"></i> Pending
                <span class="ss-tab-badge">{{ $pendingSubmissions->count() }}</span>
            </button>
            <button class="ss-tab" data-filter="graded" onclick="filterSubmissions('graded')">
                <i class="fas fa-check-circle"></i> Graded
                <span class="ss-tab-badge">{{ $gradedSubmissions->count() }}</span>
            </button>
        </div>
        <div class="ss-search">
            <i class="fas fa-search"></i>
            <input type="text" id="ssSearch" placeholder="Search by surah or verse..." onkeyup="searchSubmissions()">
        </div>
    </div>

    <!-- ============ SUBMISSION CARDS ============ -->
    <div class="ss-list">
        @foreach($submissions as $submission)
            @php
                $isGraded = $submission->status === 'graded';
                $isPending = in_array($submission->status, ['submitted', 'pending_review', 'late', 'pending']);
                $dueDate = $submission->assignment->due_date ? \Carbon\Carbon::parse($submission->assignment->due_date) : null;
                $isLate = $dueDate && $submission->created_at->gt($dueDate);

                $surahName = $submission->assignment->surah ?? ($submission->assignment->material ? $submission->assignment->material->title : 'Assignment');
                $verseRange = '';
                if ($submission->assignment->start_verse) {
                    $verseRange = $submission->assignment->start_verse;
                    if ($submission->assignment->end_verse) {
                        $verseRange .= '-' . $submission->assignment->end_verse;
                    }
                    $verseRange = '(' . $verseRange . ')';
                }

                $aiScore = $submission->tajweed_score;
                $teacherScore = $submission->score;
                $scorePercent = $teacherScore && $submission->assignment->total_marks > 0
                    ? round(($teacherScore->score / $submission->assignment->total_marks) * 100, 1)
                    : null;
            @endphp

            <div class="ss-card {{ $isPending ? 'pending' : 'graded' }} {{ $isLate ? 'late' : '' }}"
                 data-filter-type="{{ $isGraded ? 'graded' : 'pending' }}"
                 data-search="{{ strtolower($surahName . ' ' . $verseRange) }}">

                <div class="ss-card-body">
                    <div class="ss-card-top">
                        @php
                            $statusLabel = match($submission->status) {
                                'graded' => 'Graded',
                                'pending_review' => 'AI Analyzed',
                                'submitted', 'pending' => 'Pending Review',
                                'late' => 'Late',
                                default => ucfirst($submission->status),
                            };
                            $statusClass = match($submission->status) {
                                'graded' => 'graded',
                                'pending_review' => 'pending_review',
                                'late' => 'late',
                                default => 'pending',
                            };
                        @endphp
                        <span class="ss-status {{ $statusClass }}">
                            <i class="fas {{ $isGraded ? 'fa-check-circle' : ($submission->status === 'pending_review' ? 'fa-robot' : 'fa-hourglass-half') }}"></i>
                            {{ $statusLabel }}
                        </span>
                        @if($isLate)
                            <span class="ss-status late"><i class="fas fa-exclamation-triangle"></i> Late</span>
                        @endif
                    </div>

                    <h3 class="ss-title">
                        <i class="fas fa-book-quran"></i>
                        {{ $surahName }} @if($verseRange) <span style="font-size: 1.1rem; color: #d4af37;">{{ $verseRange }}</span> @endif
                    </h3>

                    <div class="ss-meta">
                        <span class="ss-meta-item">
                            <i class="fas fa-calendar-check"></i>
                            Submitted {{ $submission->created_at->format('M d, Y g:i A') }}
                        </span>
                        <span class="ss-meta-item">
                            <i class="fas fa-clock"></i>
                            {{ $submission->created_at->diffForHumans() }}
                        </span>
                        <span class="ss-meta-item">
                            <i class="fas fa-flag"></i>
                            Due {{ \Carbon\Carbon::parse($submission->assignment->due_date)->format('M d, Y') }}
                        </span>
                        @if($submission->audio_file_path)
                            <span class="ss-meta-item has-audio">
                                <i class="fas fa-microphone-alt"></i>
                                Voice Recording
                            </span>
                        @elseif($submission->text_submission)
                            <span class="ss-meta-item">
                                <i class="fas fa-file-alt"></i>
                                Text Submission
                            </span>
                        @endif
                        <span class="ss-meta-item">
                            <i class="fas fa-tasks"></i>
                            {{ $submission->assignment->total_marks }} pts
                        </span>
                    </div>
                </div>

                <div class="ss-card-right">
                    @if($isGraded && $teacherScore)
                        <div class="ss-score graded">
                            <div class="ss-score-label">Grade</div>
                            <div class="ss-score-value">
                                {{ $teacherScore->score }}/{{ $submission->assignment->total_marks }}
                            </div>
                            @if($scorePercent !== null)
                                <div style="font-size: 0.85rem; color: #15803d; font-weight: 700;">{{ $scorePercent }}%</div>
                            @endif
                        </div>
                    @elseif($aiScore !== null)
                        <div class="ss-score ai">
                            <div class="ss-score-label">AI Score</div>
                            <div class="ss-score-value">{{ round($aiScore) }}%</div>
                            <div style="font-size: 0.82rem; color: #5f6f65; font-weight: 600;">{{ $submission->tajweed_grade }}</div>
                        </div>
                    @else
                        <div class="ss-score none">
                            <div class="ss-score-label">Not Analyzed</div>
                            <div class="ss-score-value">—</div>
                        </div>
                    @endif

                    <a href="{{ route('teacher.submission.grade', $submission->id) }}"
                       class="ss-btn {{ $isGraded ? 'outline' : 'primary' }}">
                        @if($isGraded)
                            <i class="fas fa-eye"></i> View Details
                        @else
                            <i class="fas fa-check-double"></i> Review & Grade
                        @endif
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

@section('extra-scripts')
<script>
    function filterSubmissions(type) {
        document.querySelectorAll('.ss-tab').forEach(function (tab) {
            tab.classList.toggle('active', tab.dataset.filter === type);
        });
        applyFilters();
    }

    function searchSubmissions() {
        applyFilters();
    }

    function applyFilters() {
        var activeTab = document.querySelector('.ss-tab.active');
        var type = activeTab ? activeTab.dataset.filter : 'all';
        var query = (document.getElementById('ssSearch').value || '').toLowerCase().trim();

        document.querySelectorAll('.ss-card').forEach(function (card) {
            var typeMatch = type === 'all' || card.dataset.filterType === type;
            var searchMatch = query === '' || card.dataset.search.includes(query);
            card.classList.toggle('ss-hidden', !(typeMatch && searchMatch));
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        applyFilters();
    });
</script>
@endsection
