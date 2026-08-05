@extends('layouts.dashboard')

@section('title', $classroom->class_name)
@section('user-role', (auth()->user()->role_id == 3 ? 'Teacher' : 'Student') . ' • ' . $classroom->class_name)

@section('navigation')
    @if(auth()->user()->role_id == 3)
        @include('partials.teacher-nav')
    @else
        @include('partials.student-nav')
    @endif
@endsection

@section('content')
@php
    $isTeacher = auth()->user()->role_id == 3;
    $isStudent = !$isTeacher;
    $studentCount = $classroom->students->count();
    $assignmentCount = $assignments->count();
    $submissionsByAssignment = $submissions ?? collect();

    // Overview stats
    $totalSubmitted = 0;
    $totalGraded = 0;
    if ($isTeacher) {
        $totalSubmitted = $students->sum('total_submissions');
        $totalGraded = $students->sum('graded_submissions');
    } else {
        $totalSubmitted = $submissionsByAssignment->count();
    }
    $totalPending = max(0, $totalSubmitted - $totalGraded);
@endphp

<style>
    /* ============ HEADER BAR ============ */
    .cd-bar {
        background: linear-gradient(90deg, #0a5c36, #14855a);
        border-radius: 16px;
        padding: 16px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        box-shadow: 0 10px 26px rgba(10, 92, 54, 0.2);
    }

    .cd-bar-left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .cd-bar-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(255,255,255,0.16);
        color: #f4d03f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .cd-bar-title {
        font-family: 'El Messiri', serif;
        font-size: 1.9rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cd-bar-sub {
        color: rgba(255,255,255,0.85);
        font-size: 1.05rem;
        margin: 2px 0 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cd-bar-right {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .cd-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 22px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.05rem;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        font-family: 'Cairo', sans-serif;
        cursor: pointer;
        white-space: nowrap;
        color: #1a1a1a;
    }

    .cd-btn.gold {
        background: linear-gradient(135deg, #f4d03f, #d4af37);
        border-color: #b8860b;
    }
    .cd-btn.gold:hover { transform: translateY(-2px); }

    .cd-btn.ghost {
        background: rgba(255,255,255,0.14);
        color: #fff;
        border-color: rgba(255,255,255,0.35);
    }
    .cd-btn.ghost:hover { background: rgba(255,255,255,0.26); transform: translateY(-2px); }

    /* ============ TABS ============ */
    .cd-tabs {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 26px;
    }

    .cd-tab {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 13px 26px;
        border-radius: 50px;
        border: 2px solid #cfd9d0;
        background: #fff;
        color: #1a1a1a;
        font-weight: 800;
        font-size: 1.15rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Cairo', sans-serif;
    }

    .cd-tab:hover { border-color: #0a5c36; }

    .cd-tab.active {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-color: #0a5c36;
        color: #fff;
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.25);
    }

    .cd-tab .cnt {
        background: rgba(0,0,0,0.1);
        padding: 2px 11px;
        border-radius: 50px;
        font-size: 1rem;
    }
    .cd-tab.active .cnt { background: rgba(255,255,255,0.28); }

    .cd-pane { display: none; animation: cdFade 0.35s ease; }
    .cd-pane.active { display: block; }

    @keyframes cdFade {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ============ OVERVIEW STAT CARDS ============ */
    .cd-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    .cd-stat {
        background: #fff;
        border: 1px solid #e2e8e0;
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 8px 22px rgba(14, 28, 18, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .cd-stat:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(10, 92, 54, 0.12); }

    .cd-stat .n {
        font-size: 2.6rem;
        font-weight: 900;
        color: #0a5c36;
        line-height: 1;
    }
    .cd-stat .n.gold { color: #b8860b; }
    .cd-stat .l {
        margin-top: 8px;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a1a;
    }

    /* ============ INFO CARD ============ */
    .cd-info {
        background: #fff;
        border: 1px solid #e2e8e0;
        border-radius: 18px;
        padding: 26px 28px;
        box-shadow: 0 8px 22px rgba(14, 28, 18, 0.05);
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 22px;
    }

    .cd-info h3 {
        font-family: 'El Messiri', serif;
        font-size: 1.4rem;
        font-weight: 800;
        color: #0a5c36;
        margin: 0 0 10px;
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .cd-info p {
        color: #1a1a1a;
        font-size: 1.15rem;
        line-height: 1.7;
        margin: 0;
    }

    .cd-code-row {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f6f8f5;
        border: 1px solid #d9e2db;
        border-radius: 12px;
        padding: 12px 16px;
        margin-top: 4px;
    }

    .cd-code-value {
        font-family: 'Courier New', monospace;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: 5px;
        color: #1a1a1a;
        flex: 1;
    }

    .cd-code-btn {
        background: #0a5c36;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 1rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Cairo', sans-serif;
    }
    .cd-code-btn:hover { background: #1abc9c; }

    /* ============ STUDENTS GRID ============ */
    .cd-students {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 16px;
    }

    .cd-student {
        background: #fff;
        border: 1px solid #e2e8e0;
        border-radius: 18px;
        padding: 22px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        transition: all 0.25s ease;
    }
    .cd-student:hover { border-color: #0a5c36; transform: translateY(-4px); box-shadow: 0 14px 30px rgba(10, 92, 54, 0.12); }

    .cd-avatar {
        width: 78px;
        height: 78px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.9rem;
        overflow: hidden;
        box-shadow: 0 8px 18px rgba(10, 92, 54, 0.25);
    }
    .cd-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .cd-student-name {
        font-family: 'El Messiri', serif;
        font-size: 1.3rem;
        font-weight: 800;
        color: #111;
        margin: 0;
    }
    .cd-student-email {
        font-size: 1rem;
        color: #333;
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    .cd-metrics {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .cd-metric {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 1rem;
        font-weight: 800;
        padding: 6px 14px;
        border-radius: 50px;
    }
    .cd-metric.subs { color: #1d4ed8; background: #dbeafe; }
    .cd-metric.graded { color: #15803d; background: #dcfce7; }
    .cd-metric.pending { color: #b45309; background: #fef3c7; }

    .cd-student-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 13px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.1rem;
        transition: all 0.22s ease;
        font-family: 'Cairo', sans-serif;
        box-shadow: 0 8px 18px rgba(10, 92, 54, 0.22);
    }
    .cd-student-btn:hover { transform: translateY(-2px); }

    /* ============ ASSIGNMENTS ============ */
    .cd-assignments {
        display: grid;
        gap: 16px;
    }

    .cd-assignment {
        background: #fff;
        border: 1px solid #e2e8e0;
        border-radius: 16px;
        padding: 22px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .cd-assignment::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(180deg, #1abc9c, #0a5c36);
    }
    .cd-assignment:hover { border-color: #0a5c36; transform: translateY(-3px); box-shadow: 0 12px 28px rgba(10, 92, 54, 0.1); }

    .cd-assign-title {
        font-family: 'El Messiri', serif;
        font-size: 1.5rem;
        font-weight: 800;
        color: #0a5c36;
        margin: 0 0 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .cd-assign-title i { color: #b8860b; }

    .cd-assign-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 22px;
        color: #333;
        font-size: 1.1rem;
        font-weight: 600;
    }
    .cd-assign-meta i { color: #0a5c36; margin-right: 6px; }

    .cd-assign-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cd-pts {
        background: #f8f1d5;
        border: 1px solid #e5d699;
        color: #7a4f00;
        font-weight: 800;
        font-size: 1.05rem;
        padding: 8px 18px;
        border-radius: 50px;
        white-space: nowrap;
    }

    .cd-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 1.05rem;
        font-weight: 800;
        padding: 8px 18px;
        border-radius: 50px;
        white-space: nowrap;
    }
    .cd-status.ok { background: #dcfce7; color: #15803d; }
    .cd-status.wait { background: #fef3c7; color: #92400e; }

    .cd-assign-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.08rem;
        transition: all 0.22s ease;
        font-family: 'Cairo', sans-serif;
        white-space: nowrap;
        color: #1a1a1a;
    }
    .cd-assign-btn.solid {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        box-shadow: 0 8px 18px rgba(10, 92, 54, 0.22);
    }
    .cd-assign-btn.solid:hover { transform: translateY(-2px); }
    .cd-assign-btn.outline { background: #fff; color: #0a5c36; border: 2px solid #0a5c36; }
    .cd-assign-btn.outline:hover { background: #0a5c36; color: #fff; }
    .cd-assign-btn.edit { background: #fff; color: #b45309; border: 2px solid #f59e0b; }
    .cd-assign-btn.edit:hover { background: #f59e0b; color: #fff; }

    /* Empty state */
    .cd-empty {
        text-align: center;
        padding: 60px 20px;
        color: #1a1a1a;
        border: 2px dashed #c7d4cc;
        border-radius: 18px;
        background: #fbfdfc;
    }
    .cd-empty i { font-size: 3.6rem; opacity: 0.3; display: block; margin-bottom: 14px; color: #0a5c36; }
    .cd-empty h3 { color: #0a5c36; font-size: 1.5rem; margin: 0 0 6px; font-family: 'El Messiri', serif; }
    .cd-empty p { margin: 0; font-size: 1.15rem; color: #333; }

    @media (max-width: 720px) {
        .cd-bar { padding: 14px 18px; }
        .cd-bar-title { font-size: 1.5rem; }
        .cd-tab { padding: 11px 18px; font-size: 1.05rem; }
    }
</style>

<!-- ============ HEADER BAR ============ -->
<div class="cd-bar">
    <div class="cd-bar-left">
        <div class="cd-bar-icon"><i class="fas fa-school"></i></div>
        <div>
            <h1 class="cd-bar-title">{{ $classroom->class_name }}</h1>
            <p class="cd-bar-sub">
                <i class="fas fa-user-tie"></i>
                {{ $isTeacher ? 'Your Classroom' : ($classroom->teacher->name ?? 'Teacher') }}
            </p>
        </div>
    </div>

    <div class="cd-bar-right">
        @if($isTeacher)
            <a href="{{ route('classroom.edit', $classroom->id) }}" class="cd-btn ghost">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('assignment.create', $classroom->id) }}" class="cd-btn gold">
                <i class="fas fa-plus"></i> New Assignment
            </a>
            <a href="{{ route('classroom.index') }}" class="cd-btn ghost">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        @else
            <a href="{{ route('student.classes') }}" class="cd-btn ghost">
                <i class="fas fa-arrow-left"></i> My Classes
            </a>
        @endif
    </div>
</div>

<!-- ============ TABS ============ -->
<div class="cd-tabs">
    <button class="cd-tab active" data-tab="overview" onclick="showTab('overview')">
        <i class="fas fa-th-large"></i> Overview
    </button>
    @if($isTeacher)
        <button class="cd-tab" data-tab="students" onclick="showTab('students')">
            <i class="fas fa-users"></i> Students <span class="cnt">{{ $studentCount }}</span>
        </button>
    @endif
    <button class="cd-tab" data-tab="assignments" onclick="showTab('assignments')">
        <i class="fas fa-tasks"></i> Assignments <span class="cnt">{{ $assignmentCount }}</span>
    </button>
</div>

<!-- ============ OVERVIEW ============ -->
<div class="cd-pane active" id="pane-overview">
    <div class="cd-stats">
        <div class="cd-stat">
            <div class="n">{{ $studentCount }}</div>
            <div class="l"><i class="fas fa-users"></i> Students</div>
        </div>
        <div class="cd-stat">
            <div class="n gold">{{ $assignmentCount }}</div>
            <div class="l"><i class="fas fa-tasks"></i> Assignments</div>
        </div>
        <div class="cd-stat">
            <div class="n">{{ $totalSubmitted }}</div>
            <div class="l"><i class="fas fa-clipboard-check"></i> Submitted</div>
        </div>
        @if($isTeacher)
            <div class="cd-stat">
                <div class="n gold">{{ $totalPending }}</div>
                <div class="l"><i class="fas fa-hourglass-half"></i> Awaiting Grade</div>
            </div>
        @else
            <div class="cd-stat">
                <div class="n">{{ max(0, $assignmentCount - $totalSubmitted) }}</div>
                <div class="l"><i class="fas fa-hourglass-half"></i> To Complete</div>
            </div>
        @endif
    </div>

    <div class="cd-info">
        <div>
            <h3><i class="fas fa-align-left"></i> About</h3>
            <p>{{ $classroom->description ?: 'No description provided.' }}</p>
        </div>
        <div>
            <h3><i class="fas fa-key"></i> Access Code</h3>
            <div class="cd-code-row">
                <span class="cd-code-value" id="cdCode">••••••</span>
                <button class="cd-code-btn" id="cdCodeBtn" onclick="toggleCode()">
                    <i class="fas fa-eye"></i> Show
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============ STUDENTS (Teacher) ============ -->
@if($isTeacher)
<div class="cd-pane" id="pane-students">
    @if($studentCount > 0)
        <div class="cd-students">
            @foreach($students as $studentUser)
                @php
                    $subCount = $studentUser->total_submissions ?? 0;
                    $gradedCount = $studentUser->graded_submissions ?? 0;
                    $pendingCount = max(0, $subCount - $gradedCount);
                @endphp
                <div class="cd-student">
                    <div class="cd-avatar">
                        @if($studentUser->profile_picture)
                            <img src="{{ asset('storage/' . $studentUser->profile_picture) }}" alt="Avatar">
                        @else
                            {{ strtoupper(substr($studentUser->name, 0, 1)) }}
                        @endif
                    </div>
                    <p class="cd-student-name">{{ $studentUser->name }}</p>
                    <p class="cd-student-email">{{ $studentUser->email }}</p>
                    <div class="cd-metrics">
                        <span class="cd-metric subs"><i class="fas fa-clipboard-list"></i> {{ $subCount }}</span>
                        <span class="cd-metric graded"><i class="fas fa-check-circle"></i> {{ $gradedCount }}</span>
                        @if($pendingCount > 0)
                            <span class="cd-metric pending"><i class="fas fa-hourglass-half"></i> {{ $pendingCount }}</span>
                        @endif
                    </div>
                    <a href="{{ route('teacher.student.submissions', ['classroom' => $classroom->id, 'student' => $studentUser->id]) }}" class="cd-student-btn">
                        <i class="fas fa-clipboard-check"></i> Review
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="cd-empty">
            <i class="fas fa-user-plus"></i>
            <h3>No Students Yet</h3>
            <p>Share the access code to invite students</p>
        </div>
    @endif
</div>
@endif

<!-- ============ ASSIGNMENTS ============ -->
<div class="cd-pane" id="pane-assignments">
    @if($assignmentCount > 0)
        <div class="cd-assignments">
            @foreach($assignments as $assignment)
                @php
                    $assignmentTitle = $assignment->surah
                        ? $assignment->surah . ' ' . $assignment->start_verse . ($assignment->end_verse ? '-' . $assignment->end_verse : '')
                        : ($assignment->material ? $assignment->material->title : 'Assignment');

                    $studentSubmission = $isStudent ? $submissionsByAssignment->get($assignment->assignment_id) : null;

                    $submissionCount = 0;
                    if ($isTeacher) {
                        $submissionCount = \App\Models\AssignmentSubmission::where('assignment_id', $assignment->assignment_id)->count();
                    }
                @endphp
                <div class="cd-assignment">
                    <div style="flex: 1; min-width: 220px; padding-left: 8px;">
                        <h4 class="cd-assign-title">
                            <i class="fas fa-book-quran"></i>
                            {{ $assignmentTitle }}
                        </h4>
                        <div class="cd-assign-meta">
                            <span><i class="far fa-calendar"></i> Due {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}</span>
                            @if($isTeacher)
                                <span><i class="fas fa-file-alt"></i> {{ $submissionCount }} Submitted</span>
                            @endif
                        </div>
                    </div>

                    <div class="cd-assign-right">
                        <span class="cd-pts"><i class="fas fa-star"></i> {{ $assignment->total_marks }} pts</span>

                        @if($isTeacher)
                            <a href="{{ route('assignment.show', $assignment->assignment_id) }}" class="cd-assign-btn solid">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('assignment.edit', $assignment->assignment_id) }}" class="cd-assign-btn edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        @else
                            @if($studentSubmission)
                                <span class="cd-status ok"><i class="fas fa-check-circle"></i> Submitted</span>
                                <a href="{{ route('student.assignment.view', $assignment->assignment_id) }}" class="cd-assign-btn outline">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @else
                                <span class="cd-status wait"><i class="fas fa-hourglass-half"></i> Pending</span>
                                <a href="{{ route('student.assignment.submit', $assignment->assignment_id) }}" class="cd-assign-btn solid">
                                    <i class="fas fa-paper-plane"></i> Submit
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="cd-empty">
            <i class="fas fa-clipboard"></i>
            <h3>No Assignments Yet</h3>
            <p>{{ $isTeacher ? 'Create your first assignment to get started' : 'Your teacher has not created any yet' }}</p>
        </div>
    @endif
</div>

@endsection

@section('extra-scripts')
<script>
    function showTab(name) {
        document.querySelectorAll('.cd-tab').forEach(function (t) {
            t.classList.toggle('active', t.dataset.tab === name);
        });
        document.querySelectorAll('.cd-pane').forEach(function (p) {
            p.classList.toggle('active', p.id === 'pane-' + name);
        });
    }

    function toggleCode() {
        const el = document.getElementById('cdCode');
        const btn = document.getElementById('cdCodeBtn');
        const real = @json($classroom->access_code);

        if (el.textContent.indexOf('•') !== -1) {
            el.textContent = real;
            btn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide';
        } else {
            el.textContent = '••••••';
            btn.innerHTML = '<i class="fas fa-eye"></i> Show';
        }
    }
</script>
@endsection
