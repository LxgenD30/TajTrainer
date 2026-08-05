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
    :root {
        --b-bg: #F5F6FC;
        --b-card: #ffffff;
        --b-purple: #4F3FD4;
        --b-purple-dark: #3D2FC0;
        --b-purple-soft: #EDEBFB;
        --b-purple-light: #AEADD0;
        --b-orange: #EC5B29;
        --b-orange-soft: #FDEFEA;
        --b-pink: #D9C6C0;
        --b-muted: #9697AF;
        --b-text: #2D393F;
        --b-tan: #AF9882;
        --b-line: #E6E8F2;
    }

    /* ============ LIGHT PANEL ============ */
    .brite-wrap {
        background: var(--b-bg);
        border-radius: 28px;
        padding: 34px;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.25);
        display: grid;
        gap: 28px;
    }

    /* ============ HEADER ============ */
    .brite-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
    }

    .brite-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 1.4px;
        text-transform: uppercase;
        color: var(--b-purple);
        background: var(--b-purple-soft);
        padding: 6px 14px;
        border-radius: 50px;
        margin-bottom: 12px;
    }

    .brite-head h1 {
        margin: 0;
        font-family: 'El Messiri', serif;
        font-size: 2.6rem;
        font-weight: 800;
        color: var(--b-text);
        line-height: 1.1;
    }

    .brite-head .sub {
        margin: 8px 0 0;
        color: var(--b-muted);
        font-size: 1.1rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .brite-head .sub i { color: var(--b-purple); }

    .brite-actions { display: flex; gap: 10px; flex-wrap: wrap; }

    .brite-btn {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 13px 24px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.05rem;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        font-family: 'Cairo', sans-serif;
        cursor: pointer;
        white-space: nowrap;
        color: var(--b-text);
    }

    .brite-btn.orange {
        background: var(--b-orange);
        color: #fff;
        box-shadow: 0 10px 24px rgba(236, 91, 41, 0.3);
    }
    .brite-btn.orange:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(236, 91, 41, 0.38); }

    .brite-btn.purple {
        background: var(--b-purple);
        color: #fff;
        box-shadow: 0 10px 24px rgba(79, 63, 212, 0.28);
    }
    .brite-btn.purple:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(79, 63, 212, 0.36); }

    .brite-btn.outline {
        background: #fff;
        color: var(--b-purple);
        border-color: var(--b-purple);
    }
    .brite-btn.outline:hover { background: var(--b-purple-soft); transform: translateY(-3px); }

    .brite-btn.ghost {
        background: transparent;
        color: var(--b-muted);
        border-color: var(--b-line);
    }
    .brite-btn.ghost:hover { background: #fff; color: var(--b-text); transform: translateY(-3px); }

    /* ============ STATS ============ */
    .brite-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 18px;
    }

    .brite-stat {
        background: var(--b-card);
        border-radius: 20px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 18px;
        box-shadow: 0 8px 26px rgba(79, 63, 212, 0.08);
        border: 1px solid var(--b-line);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        animation: brIn 0.5s ease both;
    }
    .brite-stat:nth-child(2) { animation-delay: 0.06s; }
    .brite-stat:nth-child(3) { animation-delay: 0.12s; }
    .brite-stat:nth-child(4) { animation-delay: 0.18s; }

    .brite-stat:hover { transform: translateY(-5px); box-shadow: 0 20px 44px rgba(79, 63, 212, 0.16); }

    .brite-stat-chip {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        flex-shrink: 0;
    }
    .brite-stat-chip.purple { background: linear-gradient(135deg, var(--b-purple), var(--b-purple-light)); }
    .brite-stat-chip.orange { background: linear-gradient(135deg, var(--b-orange), #f5855f); }
    .brite-stat-chip.pink { background: linear-gradient(135deg, var(--b-pink), var(--b-tan)); }
    .brite-stat-chip.tan { background: linear-gradient(135deg, var(--b-tan), var(--b-pink)); }

    .brite-stat .num {
        font-size: 2.3rem;
        font-weight: 900;
        color: var(--b-text);
        line-height: 1;
    }
    .brite-stat .lbl {
        margin-top: 6px;
        font-size: 0.78rem;
        font-weight: 800;
        color: var(--b-muted);
        text-transform: uppercase;
        letter-spacing: 0.7px;
    }

    /* ============ GRID ============ */
    .brite-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(280px, 0.85fr);
        gap: 22px;
        align-items: start;
    }
    .brite-grid.student { grid-template-columns: minmax(0, 1.6fr) minmax(300px, 1fr); }

    .brite-card {
        background: var(--b-card);
        border-radius: 22px;
        padding: 26px;
        border: 1px solid var(--b-line);
        box-shadow: 0 8px 26px rgba(79, 63, 212, 0.07);
        animation: brIn 0.5s ease both;
    }

    .brite-card-head {
        display: flex;
        align-items: center;
        gap: 13px;
        margin-bottom: 22px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--b-line);
    }

    .brite-card-head .ch {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #fff;
        flex-shrink: 0;
        background: linear-gradient(135deg, var(--b-purple), var(--b-purple-light));
        box-shadow: 0 6px 16px rgba(79, 63, 212, 0.28);
    }
    .brite-card-head .ch.orange { background: linear-gradient(135deg, var(--b-orange), #f5855f); box-shadow: 0 6px 16px rgba(236, 91, 41, 0.28); }

    .brite-card-head h3 {
        margin: 0;
        font-family: 'El Messiri', serif;
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--b-text);
        flex: 1;
    }

    .brite-pill {
        background: var(--b-purple-soft);
        color: var(--b-purple);
        font-weight: 800;
        font-size: 1rem;
        padding: 6px 16px;
        border-radius: 50px;
        white-space: nowrap;
    }

    /* ============ STUDENTS ============ */
    .brite-students {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 14px;
    }

    .brite-student {
        background: var(--b-bg);
        border: 1px solid var(--b-line);
        border-radius: 16px;
        padding: 18px 14px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 9px;
        transition: all 0.22s ease;
    }
    .brite-student:hover { border-color: var(--b-purple-light); transform: translateY(-4px); box-shadow: 0 14px 30px rgba(79, 63, 212, 0.12); }

    .brite-avatar {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--b-purple), var(--b-purple-light));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.5rem;
        overflow: hidden;
        box-shadow: 0 8px 18px rgba(79, 63, 212, 0.25);
    }
    .brite-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .brite-student-name {
        font-family: 'El Messiri', serif;
        font-size: 1.12rem;
        font-weight: 800;
        color: var(--b-text);
        margin: 0;
        line-height: 1.2;
    }
    .brite-student-email {
        font-size: 0.85rem;
        color: var(--b-muted);
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    .brite-chips { display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; }
    .brite-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.82rem;
        font-weight: 800;
        padding: 5px 11px;
        border-radius: 50px;
    }
    .brite-chip.subs { color: var(--b-purple); background: var(--b-purple-soft); }
    .brite-chip.graded { color: #1f9d55; background: #e5f7ec; }
    .brite-chip.pending { color: var(--b-orange); background: var(--b-orange-soft); }

    .brite-student-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px;
        border-radius: 11px;
        background: var(--b-purple);
        color: #fff;
        text-decoration: none;
        font-weight: 800;
        font-size: 1rem;
        transition: all 0.2s ease;
        font-family: 'Cairo', sans-serif;
        margin-top: 2px;
        box-shadow: 0 6px 14px rgba(79, 63, 212, 0.2);
    }
    .brite-student-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(79, 63, 212, 0.3); }

    /* ============ ASSIGNMENTS ============ */
    .brite-assignments { display: grid; gap: 14px; }

    .brite-assignment {
        background: var(--b-bg);
        border: 1px solid var(--b-line);
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        transition: all 0.22s ease;
        position: relative;
        overflow: hidden;
    }
    .brite-assignment::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(180deg, var(--b-purple), var(--b-orange));
    }
    .brite-assignment:hover { border-color: var(--b-purple-light); transform: translateY(-3px); box-shadow: 0 12px 28px rgba(79, 63, 212, 0.1); }

    .brite-assign-title {
        font-family: 'El Messiri', serif;
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--b-text);
        margin: 0 0 7px;
        display: flex;
        align-items: center;
        gap: 9px;
        flex-wrap: wrap;
    }
    .brite-assign-title i { color: var(--b-purple); }

    .brite-assign-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 18px;
        color: var(--b-muted);
        font-size: 0.98rem;
        font-weight: 600;
    }
    .brite-assign-meta i { color: var(--b-purple); margin-right: 5px; }

    .brite-assign-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

    .brite-pts {
        background: var(--b-pink);
        color: #7a564a;
        font-weight: 800;
        font-size: 0.95rem;
        padding: 7px 15px;
        border-radius: 50px;
        white-space: nowrap;
    }

    .brite-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 0.95rem;
        font-weight: 800;
        padding: 7px 15px;
        border-radius: 50px;
        white-space: nowrap;
    }
    .brite-status.ok { color: var(--b-purple); background: var(--b-purple-soft); }
    .brite-status.wait { color: var(--b-orange); background: var(--b-orange-soft); animation: brPulse 2s infinite; }

    @keyframes brPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(236, 91, 41, 0.25); }
        50% { box-shadow: 0 0 0 6px rgba(236, 91, 41, 0); }
    }

    .brite-a-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 18px;
        border-radius: 11px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1rem;
        transition: all 0.2s ease;
        font-family: 'Cairo', sans-serif;
        white-space: nowrap;
        color: var(--b-text);
    }
    .brite-a-btn.solid { background: var(--b-purple); color: #fff; box-shadow: 0 6px 14px rgba(79, 63, 212, 0.22); }
    .brite-a-btn.solid:hover { transform: translateY(-2px); }
    .brite-a-btn.outline { background: #fff; color: var(--b-purple); border: 2px solid var(--b-purple); }
    .brite-a-btn.outline:hover { background: var(--b-purple-soft); }
    .brite-a-btn.edit { background: #fff; color: var(--b-orange); border: 2px solid var(--b-orange); }
    .brite-a-btn.edit:hover { background: var(--b-orange); color: #fff; }

    /* ============ INFO SIDEBAR ============ */
    .brite-info { display: grid; gap: 22px; }

    .brite-about p {
        margin: 0;
        color: var(--b-text);
        font-size: 1.06rem;
        line-height: 1.7;
    }

    .brite-code {
        background: var(--b-bg);
        border: 1px solid var(--b-line);
        border-radius: 14px;
        padding: 13px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .brite-code-value {
        font-family: 'Courier New', monospace;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: 5px;
        color: var(--b-text);
        flex: 1;
    }
    .brite-code-btn {
        background: var(--b-purple);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 9px 15px;
        font-size: 0.95rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Cairo', sans-serif;
    }
    .brite-code-btn:hover { background: var(--b-purple-dark); transform: scale(1.04); }

    .brite-teacher {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--b-bg);
        border: 1px solid var(--b-line);
        border-radius: 14px;
        padding: 14px 16px;
    }
    .brite-teacher-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--b-orange), #f5855f);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        overflow: hidden;
        flex-shrink: 0;
    }
    .brite-teacher-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .brite-teacher .tt { font-size: 1.05rem; font-weight: 800; color: var(--b-text); margin: 0; }
    .brite-teacher .ts { font-size: 0.88rem; color: var(--b-muted); margin: 0; }

    .brite-mini-h {
        font-family: 'El Messiri', serif;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--b-text);
        margin: 20px 0 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .brite-mini-h:first-of-type { margin-top: 0; }
    .brite-mini-h i { color: var(--b-purple); }

    /* Empty */
    .brite-empty {
        text-align: center;
        padding: 48px 20px;
        color: var(--b-text);
        border: 2px dashed var(--b-purple-light);
        border-radius: 16px;
        background: #fff;
    }
    .brite-empty i { font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 12px; color: var(--b-purple); }
    .brite-empty h3 { color: var(--b-purple); font-size: 1.35rem; margin: 0 0 5px; font-family: 'El Messiri', serif; }
    .brite-empty p { margin: 0; font-size: 1.05rem; color: var(--b-muted); }

    /* Animations */
    @keyframes brIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1200px) {
        .brite-grid, .brite-grid.student { grid-template-columns: 1fr; }
        .brite-info { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 720px) {
        .brite-wrap { padding: 20px; border-radius: 20px; }
        .brite-head h1 { font-size: 2rem; }
        .brite-info { grid-template-columns: 1fr; }
    }
</style>

<div class="brite-wrap">
    <!-- ============ HEADER ============ -->
    <div class="brite-head">
        <div>
            <span class="brite-eyebrow"><i class="fas fa-school"></i> Classroom</span>
            <h1>{{ $classroom->class_name }}</h1>
            <p class="sub">
                <i class="fas fa-user-tie"></i>
                {{ $isTeacher ? 'Your Classroom' : ($classroom->teacher->name ?? 'Teacher') }}
                @if($classroom->description)
                    <span style="opacity: 0.9;">• {{ \Illuminate\Support\Str::limit($classroom->description, 70) }}</span>
                @endif
            </p>
        </div>

        <div class="brite-actions">
            @if($isTeacher)
                <a href="{{ route('classroom.index') }}" class="brite-btn ghost">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('classroom.edit', $classroom->id) }}" class="brite-btn outline">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('assignment.create', $classroom->id) }}" class="brite-btn orange">
                    <i class="fas fa-plus"></i> New Assignment
                </a>
            @else
                <a href="{{ route('student.classes') }}" class="brite-btn purple">
                    <i class="fas fa-arrow-left"></i> My Classes
                </a>
            @endif
        </div>
    </div>

    <!-- ============ STATS ============ -->
    <div class="brite-stats">
        <div class="brite-stat">
            <div class="brite-stat-chip purple"><i class="fas fa-users"></i></div>
            <div>
                <div class="num" data-count="{{ $studentCount }}">0</div>
                <div class="lbl">Students</div>
            </div>
        </div>
        <div class="brite-stat">
            <div class="brite-stat-chip orange"><i class="fas fa-tasks"></i></div>
            <div>
                <div class="num" data-count="{{ $assignmentCount }}">0</div>
                <div class="lbl">Assignments</div>
            </div>
        </div>
        <div class="brite-stat">
            <div class="brite-stat-chip pink"><i class="fas fa-clipboard-check"></i></div>
            <div>
                <div class="num" data-count="{{ $totalSubmitted }}">0</div>
                <div class="lbl">Submitted</div>
            </div>
        </div>
        @if($isTeacher)
            <div class="brite-stat">
                <div class="brite-stat-chip tan"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <div class="num" data-count="{{ $totalPending }}">0</div>
                    <div class="lbl">Awaiting Grade</div>
                </div>
            </div>
        @else
            <div class="brite-stat">
                <div class="brite-stat-chip tan"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <div class="num" data-count="{{ max(0, $assignmentCount - $totalSubmitted) }}">0</div>
                    <div class="lbl">To Complete</div>
                </div>
            </div>
        @endif
    </div>

    <!-- ============ CONTENT ============ -->
    <div class="brite-grid {{ $isTeacher ? '' : 'student' }}">

        @if($isTeacher)
            <!-- ============ STUDENTS ============ -->
            <div class="brite-card">
                <div class="brite-card-head">
                    <div class="ch"><i class="fas fa-users"></i></div>
                    <h3>Students</h3>
                    <span class="brite-pill">{{ $studentCount }}</span>
                </div>

                @if($studentCount > 0)
                    <div class="brite-students">
                        @foreach($students as $studentUser)
                            @php
                                $subCount = $studentUser->total_submissions ?? 0;
                                $gradedCount = $studentUser->graded_submissions ?? 0;
                                $pendingCount = max(0, $subCount - $gradedCount);
                            @endphp
                            <div class="brite-student">
                                <div class="brite-avatar">
                                    @if($studentUser->profile_picture)
                                        <img src="{{ asset('storage/' . $studentUser->profile_picture) }}" alt="Avatar">
                                    @else
                                        {{ strtoupper(substr($studentUser->name, 0, 1)) }}
                                    @endif
                                </div>
                                <p class="brite-student-name">{{ $studentUser->name }}</p>
                                <p class="brite-student-email">{{ $studentUser->email }}</p>
                                <div class="brite-chips">
                                    <span class="brite-chip subs"><i class="fas fa-clipboard-list"></i> {{ $subCount }}</span>
                                    <span class="brite-chip graded"><i class="fas fa-check-circle"></i> {{ $gradedCount }}</span>
                                    @if($pendingCount > 0)
                                        <span class="brite-chip pending"><i class="fas fa-hourglass-half"></i> {{ $pendingCount }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('teacher.student.submissions', ['classroom' => $classroom->id, 'student' => $studentUser->id]) }}" class="brite-student-btn">
                                    <i class="fas fa-clipboard-check"></i> Review
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="brite-empty">
                        <i class="fas fa-user-plus"></i>
                        <h3>No Students Yet</h3>
                        <p>Share the access code to invite students</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- ============ ASSIGNMENTS ============ -->
        <div class="brite-card">
            <div class="brite-card-head">
                <div class="ch orange"><i class="fas fa-tasks"></i></div>
                <h3>Assignments</h3>
                <span class="brite-pill">{{ $assignmentCount }}</span>
            </div>

            @if($assignmentCount > 0)
                <div class="brite-assignments">
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
                        <div class="brite-assignment">
                            <div style="flex: 1; min-width: 200px; padding-left: 8px;">
                                <h4 class="brite-assign-title">
                                    <i class="fas fa-book-quran"></i>
                                    {{ $assignmentTitle }}
                                </h4>
                                <div class="brite-assign-meta">
                                    <span><i class="far fa-calendar"></i> Due {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}</span>
                                    @if($isTeacher)
                                        <span><i class="fas fa-file-alt"></i> {{ $submissionCount }} Submitted</span>
                                    @endif
                                </div>
                            </div>

                            <div class="brite-assign-right">
                                <span class="brite-pts"><i class="fas fa-star"></i> {{ $assignment->total_marks }} pts</span>

                                @if($isTeacher)
                                    <a href="{{ route('assignment.show', $assignment->assignment_id) }}" class="brite-a-btn solid">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('assignment.edit', $assignment->assignment_id) }}" class="brite-a-btn edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @else
                                    @if($studentSubmission)
                                        <span class="brite-status ok"><i class="fas fa-check-circle"></i> Submitted</span>
                                        <a href="{{ route('student.assignment.view', $assignment->assignment_id) }}" class="brite-a-btn outline">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @else
                                        <span class="brite-status wait"><i class="fas fa-hourglass-half"></i> Pending</span>
                                        <a href="{{ route('student.assignment.submit', $assignment->assignment_id) }}" class="brite-a-btn solid">
                                            <i class="fas fa-paper-plane"></i> Submit
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="brite-empty">
                    <i class="fas fa-clipboard"></i>
                    <h3>No Assignments Yet</h3>
                    <p>{{ $isTeacher ? 'Create your first assignment to get started' : 'Your teacher has not created any yet' }}</p>
                </div>
            @endif
        </div>

        <!-- ============ INFO SIDEBAR ============ -->
        <div class="brite-info">
            <div class="brite-card">
                <div class="brite-card-head">
                    <div class="ch"><i class="fas fa-info-circle"></i></div>
                    <h3>Class Info</h3>
                </div>

                <div class="brite-about" style="margin-bottom: 4px;">
                    <p>{{ $classroom->description ?: 'No description provided.' }}</p>
                </div>

                <h4 class="brite-mini-h"><i class="fas fa-key"></i> Access Code</h4>
                <div class="brite-code">
                    <span class="brite-code-value" id="briteCode">••••••</span>
                    <button class="brite-code-btn" id="briteCodeBtn" onclick="toggleCode()">
                        <i class="fas fa-eye"></i> Show
                    </button>
                </div>

                @if($classroom->teacher)
                    <h4 class="brite-mini-h"><i class="fas fa-user-tie"></i> Teacher</h4>
                    <div class="brite-teacher">
                        <div class="brite-teacher-avatar">
                            @if($classroom->teacher->user && $classroom->teacher->user->profile_picture)
                                <img src="{{ asset('storage/' . $classroom->teacher->user->profile_picture) }}" alt="Teacher">
                            @else
                                {{ strtoupper(substr($classroom->teacher->name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <p class="tt">{{ $classroom->teacher->name }}</p>
                            <p class="ts">{{ $classroom->teacher->title ?? 'Teacher' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('extra-scripts')
<script>
    // ===== Count-up animation =====
    document.querySelectorAll('.brite-stat .num').forEach(function (el) {
        const target = parseInt(el.dataset.count, 10) || 0;
        const dur = 900;
        const start = performance.now();

        function tick(now) {
            const p = Math.min((now - start) / dur, 1);
            const eased = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(target * eased);
            if (p < 1) requestAnimationFrame(tick);
            else el.textContent = target;
        }
        requestAnimationFrame(tick);
    });

    // ===== Access code toggle =====
    function toggleCode() {
        const el = document.getElementById('briteCode');
        const btn = document.getElementById('briteCodeBtn');
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
