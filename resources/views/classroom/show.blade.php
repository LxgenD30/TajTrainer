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
@endphp

<style>
    /* ============ HERO ============ */
    .cd-hero {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border: 3px solid #0a4a2b;
        border-radius: 20px;
        color: #fff;
        padding: 28px 34px;
        margin-bottom: 26px;
        box-shadow: 0 14px 32px rgba(10, 92, 54, 0.24);
        position: relative;
        overflow: hidden;
    }

    .cd-hero:before {
        content: '';
        position: absolute;
        top: -100px;
        right: -80px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
        pointer-events: none;
    }

    .cd-hero-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
        margin-bottom: 22px;
    }

    .cd-hero-title-wrap {
        flex: 1;
        min-width: 240px;
    }

    .cd-hero h1 {
        margin: 0 0 6px;
        font-size: 2.2rem;
        line-height: 1.2;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 12px;
        font-family: 'El Messiri', serif;
    }

    .cd-hero p {
        margin: 0;
        opacity: 0.95;
        font-size: 1.05rem;
        line-height: 1.6;
    }

    .cd-hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .cd-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 20px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 800;
        font-size: 0.98rem;
        border: 2px solid transparent;
        transition: all 0.22s ease;
        font-family: 'Cairo', sans-serif;
        white-space: nowrap;
        cursor: pointer;
    }

    .cd-btn.gold {
        background: linear-gradient(135deg, #d4af37, #f4d03f);
        color: #111827;
        border-color: #3d3520;
        box-shadow: 0 6px 16px rgba(0,0,0,0.18);
    }

    .cd-btn.gold:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,0.24); }
    .cd-btn.gold.danger { background: #fff; color: #e74c3c; border-color: #e74c3c; box-shadow: none; }
    .cd-btn.gold.danger:hover { background: #e74c3c; color: #fff; }

    .cd-btn.white {
        background: #fff;
        color: #0a5c36;
        border-color: #0a5c36;
    }

    .cd-btn.white:hover { background: #0a5c36; color: #fff; }

    /* Hero stats */
    .cd-hero-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        position: relative;
        z-index: 2;
    }

    .cd-hero-stat {
        background: rgba(255, 255, 255, 0.14);
        border: 2px solid rgba(255, 255, 255, 0.28);
        border-radius: 16px;
        padding: 18px 20px;
        text-align: center;
        backdrop-filter: blur(8px);
        transition: all 0.22s ease;
    }

    .cd-hero-stat:hover { background: rgba(255, 255, 255, 0.22); transform: translateY(-2px); }

    .cd-hero-stat .v {
        font-size: 2.1rem;
        font-weight: 800;
        line-height: 1;
        color: #fff;
    }

    .cd-hero-stat .v.gold { color: #f4d03f; }
    .cd-hero-stat .k {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-top: 7px;
        font-weight: 600;
    }

    .cd-hero-stat .code {
        font-family: 'Courier New', monospace;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: 5px;
        color: #f4d03f;
    }

    /* ============ CONTENT GRID ============ */
    .cd-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr);
        gap: 26px;
        align-items: start;
    }

    .cd-grid.single {
        grid-template-columns: 1fr;
    }

    .cd-card {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(14, 28, 18, 0.07);
        padding: 24px;
        animation: cdFadeUp 0.45s ease both;
    }

    .cd-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
        padding-bottom: 14px;
        border-bottom: 3px solid #0a5c36;
    }

    .cd-card-header h3 {
        margin: 0;
        color: #0a5c36;
        font-family: 'El Messiri', serif;
        font-size: 1.35rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cd-badge-count {
        background: #0a5c36;
        color: #fff;
        padding: 3px 12px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 800;
    }

    /* ============ STUDENT ROWS (Teacher view) ============ */
    .cd-student-list {
        display: grid;
        gap: 14px;
    }

    .cd-student {
        background: #f8fcf8;
        border: 2px solid #deeadf;
        border-radius: 14px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        transition: all 0.22s ease;
    }

    .cd-student:hover {
        background: #edf8ef;
        border-color: #0a5c36;
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(10, 92, 54, 0.12);
    }

    .cd-student-info {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
        min-width: 220px;
    }

    .cd-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        flex-shrink: 0;
        overflow: hidden;
    }

    .cd-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cd-student-name {
        font-weight: 800;
        color: #1a1a1a;
        font-size: 1.05rem;
        font-family: 'Cairo', sans-serif;
        margin: 0 0 2px;
    }

    .cd-student-email {
        color: #5f6f65;
        font-size: 0.9rem;
        margin: 0;
    }

    .cd-student-stats {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .cd-mini-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 800;
        padding: 6px 14px;
        border-radius: 50px;
    }

    .cd-mini-badge.subs { color: #1d4ed8; background: #dbeafe; }
    .cd-mini-badge.graded { color: #15803d; background: #dcfce7; }
    .cd-mini-badge.pending { color: #b45309; background: #fef3c7; }

    .cd-student-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 18px;
        border-radius: 11px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        text-decoration: none;
        font-weight: 800;
        font-size: 0.92rem;
        transition: all 0.22s ease;
        font-family: 'Cairo', sans-serif;
        box-shadow: 0 6px 14px rgba(10, 92, 54, 0.22);
        white-space: nowrap;
    }

    .cd-student-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(10, 92, 54, 0.3); }

    /* ============ ASSIGNMENT ROWS ============ */
    .cd-assignment-list {
        display: grid;
        gap: 14px;
    }

    .cd-assignment {
        border: 2px solid #deeadf;
        border-radius: 14px;
        background: #f8fcf8;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        transition: all 0.22s ease;
        position: relative;
        overflow: hidden;
    }

    .cd-assignment::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(180deg, #1abc9c, #0a5c36);
    }

    .cd-assignment:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(10, 92, 54, 0.12);
        border-color: #0a5c36;
        background: #fff;
    }

    .cd-assignment-main {
        flex: 1;
        min-width: 220px;
        padding-left: 6px;
    }

    .cd-assignment-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0a5c36;
        margin: 0 0 6px;
        font-family: 'El Messiri', serif;
        display: flex;
        align-items: center;
        gap: 9px;
        flex-wrap: wrap;
    }

    .cd-assignment-title i { color: #d4af37; }

    .cd-assignment-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 18px;
        color: #5f6f65;
        font-size: 0.92rem;
        font-weight: 600;
    }

    .cd-assignment-meta i { color: #1abc9c; margin-right: 5px; }

    .cd-assignment-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cd-pts {
        background: #f8f1d5;
        border: 2px solid #e5d699;
        color: #8a6d0b;
        font-weight: 800;
        font-size: 0.9rem;
        padding: 6px 14px;
        border-radius: 50px;
        white-space: nowrap;
    }

    .cd-status-submitted {
        background: #dcfce7;
        color: #15803d;
        font-weight: 800;
        font-size: 0.82rem;
        padding: 6px 14px;
        border-radius: 50px;
        white-space: nowrap;
    }

    .cd-status-notsubmitted {
        background: #fef3c7;
        color: #b45309;
        font-weight: 800;
        font-size: 0.82rem;
        padding: 6px 14px;
        border-radius: 50px;
        white-space: nowrap;
    }

    .cd-assignment-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 18px;
        border-radius: 11px;
        text-decoration: none;
        font-weight: 800;
        font-size: 0.92rem;
        transition: all 0.22s ease;
        font-family: 'Cairo', sans-serif;
        white-space: nowrap;
    }

    .cd-assignment-btn.solid {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        box-shadow: 0 6px 14px rgba(10, 92, 54, 0.22);
    }

    .cd-assignment-btn.solid:hover { transform: translateY(-2px); }

    .cd-assignment-btn.outline {
        background: #fff;
        color: #0a5c36;
        border: 2px solid #0a5c36;
    }

    .cd-assignment-btn.outline:hover { background: #0a5c36; color: #fff; }

    .cd-assignment-btn.edit {
        background: #fff;
        color: #f39c12;
        border: 2px solid #f39c12;
    }

    .cd-assignment-btn.edit:hover { background: #f39c12; color: #fff; }

    /* Empty state */
    .cd-empty {
        text-align: center;
        padding: 44px 20px;
        color: #5f6f65;
        border: 2px dashed #b7c6ba;
        border-radius: 14px;
        background: #f7faf8;
    }

    .cd-empty i {
        font-size: 3rem;
        opacity: 0.35;
        margin-bottom: 10px;
        display: block;
        color: #0a5c36;
    }

    .cd-empty h4 {
        color: #0a5c36;
        font-size: 1.25rem;
        margin: 0 0 4px;
        font-family: 'El Messiri', serif;
    }

    .cd-empty p { margin: 0; font-size: 0.98rem; }

    @keyframes cdFadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1100px) {
        .cd-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 720px) {
        .cd-hero { padding: 22px; }
        .cd-hero-top { flex-direction: column; }
    }
</style>

<!-- ============ HERO ============ -->
<div class="cd-hero">
    <div class="cd-hero-top">
        <div class="cd-hero-title-wrap">
            <h1><i class="fas fa-chalkboard-teacher"></i> {{ $classroom->class_name }}</h1>
            <p>{{ $classroom->description ?: ($isTeacher ? 'Manage your classroom students and assignments' : 'Your Tajweed learning classroom') }}</p>
            @if($classroom->teacher)
                <p style="margin-top: 8px;">
                    <i class="fas fa-user-tie"></i>
                    {{ $isTeacher ? 'Your Classroom' : 'Teacher: ' . $classroom->teacher->name }}
                </p>
            @endif
        </div>

        <div class="cd-hero-actions">
            @if($isTeacher)
                <a href="{{ route('classroom.index') }}" class="cd-btn gold">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('classroom.edit', $classroom->id) }}" class="cd-btn gold">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('assignment.create', $classroom->id) }}" class="cd-btn gold">
                    <i class="fas fa-plus"></i> New Assignment
                </a>
            @else
                <a href="{{ route('student.classes') }}" class="cd-btn gold">
                    <i class="fas fa-arrow-left"></i> Back to My Classes
                </a>
            @endif
        </div>
    </div>

    <!-- Hero Stats -->
    <div class="cd-hero-stats">
        <div class="cd-hero-stat">
            <div class="v">{{ $studentCount }}</div>
            <div class="k"><i class="fas fa-users"></i> Students</div>
        </div>
        <div class="cd-hero-stat">
            <div class="v gold">{{ $assignmentCount }}</div>
            <div class="k"><i class="fas fa-tasks"></i> Assignments</div>
        </div>
        <div class="cd-hero-stat">
            <div class="code" id="cdAccessCode">••••••</div>
            <div class="k" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fas fa-key"></i> Access Code
                <button onclick="toggleAccessCode()" class="cd-btn gold" style="padding: 3px 10px; font-size: 0.8rem; border-radius: 8px;">
                    <i class="fas fa-eye" id="cdCodeIcon"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="cd-grid {{ $isTeacher ? '' : 'single' }}">
    @if($isTeacher)
        <!-- ============ STUDENTS SECTION (Teacher only) ============ -->
        <div class="cd-card">
            <div class="cd-card-header">
                <h3><i class="fas fa-users"></i> Students</h3>
                <span class="cd-badge-count">{{ $studentCount }}</span>
            </div>

            @if($studentCount > 0)
                <div class="cd-student-list">
                    @foreach($students as $studentUser)
                        @php
                            $subCount = $studentUser->total_submissions ?? 0;
                            $gradedCount = $studentUser->graded_submissions ?? 0;
                            $pendingCount = max(0, $subCount - $gradedCount);
                        @endphp
                        <div class="cd-student">
                            <div class="cd-student-info">
                                <div class="cd-avatar">
                                    @if($studentUser->profile_picture)
                                        <img src="{{ asset('storage/' . $studentUser->profile_picture) }}" alt="Avatar">
                                    @else
                                        {{ strtoupper(substr($studentUser->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="cd-student-name">{{ $studentUser->name }}</p>
                                    <p class="cd-student-email">{{ $studentUser->email }}</p>
                                </div>
                            </div>

                            <div class="cd-student-stats">
                                <span class="cd-mini-badge subs"><i class="fas fa-clipboard-list"></i> {{ $subCount }} Submitted</span>
                                <span class="cd-mini-badge graded"><i class="fas fa-check-circle"></i> {{ $gradedCount }} Graded</span>
                                @if($pendingCount > 0)
                                    <span class="cd-mini-badge pending"><i class="fas fa-hourglass-half"></i> {{ $pendingCount }} Pending</span>
                                @endif
                            </div>

                            <a href="{{ route('teacher.student.submissions', ['classroom' => $classroom->id, 'student' => $studentUser->id]) }}" class="cd-student-btn">
                                <i class="fas fa-clipboard-check"></i> Review & Grade
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="cd-empty">
                    <i class="fas fa-user-plus"></i>
                    <h4>No Students Enrolled Yet</h4>
                    <p>Share the access code to invite students to this class</p>
                </div>
            @endif
        </div>
    @endif

    <!-- ============ ASSIGNMENTS SECTION ============ -->
    <div class="cd-card">
        <div class="cd-card-header">
            <h3><i class="fas fa-tasks"></i> Assignments</h3>
            <span class="cd-badge-count">{{ $assignmentCount }}</span>
        </div>

        @if($assignmentCount > 0)
            <div class="cd-assignment-list">
                @foreach($assignments as $assignment)
                    @php
                        $assignmentTitle = $assignment->surah
                            ? $assignment->surah . ' (' . $assignment->start_verse . ($assignment->end_verse ? '-' . $assignment->end_verse : '') . ')'
                            : ($assignment->material ? $assignment->material->title : 'Assignment');

                        $studentSubmission = $isStudent ? $submissionsByAssignment->get($assignment->assignment_id) : null;

                        $submissionCount = 0;
                        if ($isTeacher) {
                            $submissionCount = \App\Models\AssignmentSubmission::where('assignment_id', $assignment->assignment_id)->count();
                        }
                    @endphp
                    <div class="cd-assignment">
                        <div class="cd-assignment-main">
                            <h4 class="cd-assignment-title">
                                <i class="fas fa-book-quran"></i>
                                {{ $assignmentTitle }}
                            </h4>
                            <div class="cd-assignment-meta">
                                <span><i class="far fa-calendar"></i> Due {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y g:i A') }}</span>
                                @if($isTeacher)
                                    <span><i class="fas fa-file-alt"></i> {{ $submissionCount }} submission{{ $submissionCount != 1 ? 's' : '' }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="cd-assignment-right">
                            <span class="cd-pts"><i class="fas fa-star"></i> {{ $assignment->total_marks }} pts</span>

                            @if($isTeacher)
                                <a href="{{ route('assignment.show', $assignment->assignment_id) }}" class="cd-assignment-btn solid">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('assignment.edit', $assignment->assignment_id) }}" class="cd-assignment-btn edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @else
                                @if($studentSubmission)
                                    <span class="cd-status-submitted"><i class="fas fa-check-circle"></i> Submitted</span>
                                    <a href="{{ route('student.assignment.view', $assignment->assignment_id) }}" class="cd-assignment-btn outline">
                                        <i class="fas fa-eye"></i> View Submission
                                    </a>
                                @else
                                    <span class="cd-status-notsubmitted"><i class="fas fa-hourglass-half"></i> Not Submitted</span>
                                    <a href="{{ route('student.assignment.submit', $assignment->assignment_id) }}" class="cd-assignment-btn solid">
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
                <h4>No Assignments Yet</h4>
                <p>{{ $isTeacher ? 'Create your first assignment to get started' : 'Your teacher has not created any assignments yet' }}</p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('extra-scripts')
<script>
    function toggleAccessCode() {
        const el = document.getElementById('cdAccessCode');
        const icon = document.getElementById('cdCodeIcon');
        const realCode = @json($classroom->access_code);

        if (el.textContent.indexOf('•') !== -1) {
            el.textContent = realCode;
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            el.textContent = '••••••';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection
