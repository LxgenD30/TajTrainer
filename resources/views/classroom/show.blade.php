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
    .cv-hero {
        background: linear-gradient(135deg, #0a5c36 0%, #14855a 60%, #1abc9c 100%);
        border-radius: 24px;
        padding: 36px 40px;
        margin-bottom: 30px;
        color: #fff;
        box-shadow: 0 20px 45px rgba(10, 92, 54, 0.28);
        position: relative;
        overflow: hidden;
    }

    .cv-hero::after {
        content: '';
        position: absolute;
        top: -140px;
        right: -100px;
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(255,255,255,0.14) 0%, transparent 70%);
        pointer-events: none;
    }

    .cv-hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
        margin-bottom: 30px;
    }

    .cv-hero h1 {
        margin: 0;
        font-size: 2.9rem;
        font-weight: 800;
        color: #fff;
        font-family: 'El Messiri', serif;
        line-height: 1.15;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .cv-hero h1 i { color: #f4d03f; }

    .cv-hero-sub {
        margin: 10px 0 0;
        font-size: 1.25rem;
        font-weight: 600;
        opacity: 0.92;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .cv-hero-sub i { color: #f4d03f; }

    .cv-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cv-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 26px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.1rem;
        border: 2px solid transparent;
        transition: all 0.22s ease;
        font-family: 'Cairo', sans-serif;
        white-space: nowrap;
        cursor: pointer;
    }

    .cv-btn.gold {
        background: linear-gradient(135deg, #f4d03f, #d4af37);
        color: #1a1a1a;
        border-color: #b8860b;
        box-shadow: 0 8px 22px rgba(0,0,0,0.22);
    }

    .cv-btn.gold:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(0,0,0,0.3); }
    .cv-btn.ghost {
        background: rgba(255,255,255,0.12);
        color: #fff;
        border-color: rgba(255,255,255,0.35);
        backdrop-filter: blur(6px);
    }
    .cv-btn.ghost:hover { background: rgba(255,255,255,0.22); transform: translateY(-3px); }

    /* Hero stat tiles */
    .cv-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        position: relative;
        z-index: 2;
    }

    .cv-stat {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 18px;
        padding: 22px;
        text-align: center;
        backdrop-filter: blur(10px);
        transition: all 0.22s ease;
    }

    .cv-stat:hover { background: rgba(255,255,255,0.22); transform: translateY(-3px); }

    .cv-stat .num {
        font-size: 3rem;
        font-weight: 900;
        line-height: 1;
        color: #fff;
    }

    .cv-stat .num.gold { color: #f4d03f; }

    .cv-stat .lbl {
        margin-top: 10px;
        font-size: 1.15rem;
        font-weight: 700;
        opacity: 0.92;
    }

    .cv-stat.code-stat {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex-direction: row;
    }

    .cv-code {
        font-family: 'Courier New', monospace;
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: 6px;
        color: #f4d03f;
    }

    .cv-eye {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.4);
        color: #fff;
        border-radius: 12px;
        width: 48px;
        height: 48px;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cv-eye:hover { background: rgba(255,255,255,0.35); transform: scale(1.08); }

    /* ============ BODY ============ */
    .cv-body {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr);
        gap: 30px;
        align-items: start;
    }

    .cv-body.single { grid-template-columns: 1fr; }

    .cv-card {
        background: #fff;
        border: 1px solid #e6ece5;
        border-radius: 22px;
        box-shadow: 0 10px 30px rgba(14, 28, 18, 0.06);
        padding: 30px;
        animation: cvIn 0.5s ease both;
    }

    .cv-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 24px;
    }

    .cv-card-header h2 {
        margin: 0;
        color: #0a5c36;
        font-family: 'El Messiri', serif;
        font-size: 1.8rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cv-card-header h2 .ico {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        box-shadow: 0 6px 16px rgba(10, 92, 54, 0.25);
    }

    .cv-count {
        background: #eef6f1;
        color: #0a5c36;
        font-weight: 800;
        font-size: 1.05rem;
        padding: 8px 20px;
        border-radius: 50px;
    }

    /* ============ STUDENTS GRID (Teacher) ============ */
    .cv-students {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 16px;
    }

    .cv-student {
        background: #f8fcf8;
        border: 1px solid #deeadf;
        border-radius: 18px;
        padding: 22px;
        text-align: center;
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .cv-student:hover {
        background: #fff;
        border-color: #1abc9c;
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(10, 92, 54, 0.14);
    }

    .cv-avatar {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.8rem;
        overflow: hidden;
        box-shadow: 0 8px 18px rgba(10, 92, 54, 0.25);
        flex-shrink: 0;
    }

    .cv-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .cv-student-name {
        font-size: 1.35rem;
        font-weight: 800;
        color: #1a1a1a;
        font-family: 'El Messiri', serif;
        margin: 0;
        line-height: 1.25;
    }

    .cv-student-email {
        font-size: 1rem;
        color: #7a8a80;
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    .cv-student-metrics {
        display: flex;
        gap: 10px;
        margin-top: 4px;
    }

    .cv-metric {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 1.05rem;
        font-weight: 800;
        padding: 6px 14px;
        border-radius: 50px;
    }

    .cv-metric.subs { color: #1d4ed8; background: #dbeafe; }
    .cv-metric.graded { color: #15803d; background: #dcfce7; }
    .cv-metric.pending { color: #b45309; background: #fef3c7; }

    .cv-student-btn {
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
        font-size: 1.08rem;
        transition: all 0.22s ease;
        font-family: 'Cairo', sans-serif;
        margin-top: 4px;
        box-shadow: 0 8px 18px rgba(10, 92, 54, 0.22);
    }

    .cv-student-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(10, 92, 54, 0.3); }

    /* ============ ASSIGNMENTS ============ */
    .cv-assignments {
        display: grid;
        gap: 16px;
    }

    .cv-assignment {
        background: #f8fcf8;
        border: 1px solid #deeadf;
        border-radius: 18px;
        padding: 24px 26px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .cv-assignment::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(180deg, #1abc9c, #0a5c36);
    }

    .cv-assignment:hover {
        background: #fff;
        border-color: #1abc9c;
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(10, 92, 54, 0.12);
    }

    .cv-assign-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0a5c36;
        margin: 0 0 8px;
        font-family: 'El Messiri', serif;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cv-assign-title i { color: #d4af37; }

    .cv-assign-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 22px;
        color: #5f6f65;
        font-size: 1.12rem;
        font-weight: 600;
    }

    .cv-assign-meta i { color: #1abc9c; margin-right: 6px; }

    .cv-assign-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cv-pts {
        background: #f8f1d5;
        border: 1px solid #e5d699;
        color: #8a6d0b;
        font-weight: 800;
        font-size: 1.05rem;
        padding: 8px 18px;
        border-radius: 50px;
        white-space: nowrap;
    }

    .cv-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 1.05rem;
        font-weight: 800;
        padding: 8px 18px;
        border-radius: 50px;
        white-space: nowrap;
    }

    .cv-status.ok { background: #dcfce7; color: #15803d; }
    .cv-status.wait { background: #fef3c7; color: #b45309; }

    .cv-assign-btn {
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
    }

    .cv-assign-btn.solid {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        box-shadow: 0 8px 18px rgba(10, 92, 54, 0.22);
    }
    .cv-assign-btn.solid:hover { transform: translateY(-2px); }

    .cv-assign-btn.outline {
        background: #fff;
        color: #0a5c36;
        border: 2px solid #0a5c36;
    }
    .cv-assign-btn.outline:hover { background: #0a5c36; color: #fff; }

    .cv-assign-btn.edit {
        background: #fff;
        color: #f39c12;
        border: 2px solid #f39c12;
    }
    .cv-assign-btn.edit:hover { background: #f39c12; color: #fff; }

    /* Empty */
    .cv-empty {
        text-align: center;
        padding: 60px 20px;
        color: #5f6f65;
        border: 2px dashed #c7d4cc;
        border-radius: 18px;
        background: #fbfdfc;
    }

    .cv-empty i { font-size: 3.6rem; opacity: 0.3; display: block; margin-bottom: 14px; color: #0a5c36; }
    .cv-empty h3 { color: #0a5c36; font-size: 1.5rem; margin: 0 0 6px; font-family: 'El Messiri', serif; }
    .cv-empty p { margin: 0; font-size: 1.15rem; }

    @keyframes cvIn {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1100px) {
        .cv-body { grid-template-columns: 1fr; }
        .cv-stats { grid-template-columns: 1fr; }
        .cv-hero h1 { font-size: 2.3rem; }
    }

    @media (max-width: 720px) {
        .cv-hero { padding: 26px 22px; }
        .cv-code { font-size: 1.6rem; }
    }
</style>

<!-- ============ HERO ============ -->
<div class="cv-hero">
    <div class="cv-hero-top">
        <div>
            <h1><i class="fas fa-school"></i> {{ $classroom->class_name }}</h1>
            <p class="cv-hero-sub">
                @if($classroom->teacher)
                    <i class="fas fa-user-tie"></i>
                    {{ $isTeacher ? 'Your Classroom' : $classroom->teacher->name }}
                @endif
            </p>
        </div>

        <div class="cv-actions">
            @if($isTeacher)
                <a href="{{ route('classroom.edit', $classroom->id) }}" class="cv-btn ghost">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('assignment.create', $classroom->id) }}" class="cv-btn gold">
                    <i class="fas fa-plus"></i> New Assignment
                </a>
                <a href="{{ route('classroom.index') }}" class="cv-btn ghost">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            @else
                <a href="{{ route('student.classes') }}" class="cv-btn ghost">
                    <i class="fas fa-arrow-left"></i> My Classes
                </a>
            @endif
        </div>
    </div>

    <div class="cv-stats">
        <div class="cv-stat">
            <div class="num">{{ $studentCount }}</div>
            <div class="lbl"><i class="fas fa-users"></i> Students</div>
        </div>
        <div class="cv-stat">
            <div class="num gold">{{ $assignmentCount }}</div>
            <div class="lbl"><i class="fas fa-tasks"></i> Assignments</div>
        </div>
        <div class="cv-stat code-stat">
            <div>
                <div class="cv-code" id="cvCode">••••••</div>
                <div class="lbl"><i class="fas fa-key"></i> Access Code</div>
            </div>
            <button class="cv-eye" onclick="toggleCode()" title="Show / hide access code">
                <i class="fas fa-eye" id="cvEye"></i>
            </button>
        </div>
    </div>
</div>

<div class="cv-body {{ $isTeacher ? '' : 'single' }}">
    @if($isTeacher)
        <!-- ============ STUDENTS ============ -->
        <div class="cv-card">
            <div class="cv-card-header">
                <h2><span class="ico"><i class="fas fa-users"></i></span> Students</h2>
                <span class="cv-count">{{ $studentCount }}</span>
            </div>

            @if($studentCount > 0)
                <div class="cv-students">
                    @foreach($students as $studentUser)
                        @php
                            $subCount = $studentUser->total_submissions ?? 0;
                            $gradedCount = $studentUser->graded_submissions ?? 0;
                            $pendingCount = max(0, $subCount - $gradedCount);
                        @endphp
                        <div class="cv-student">
                            <div class="cv-avatar">
                                @if($studentUser->profile_picture)
                                    <img src="{{ asset('storage/' . $studentUser->profile_picture) }}" alt="Avatar">
                                @else
                                    {{ strtoupper(substr($studentUser->name, 0, 1)) }}
                                @endif
                            </div>
                            <p class="cv-student-name">{{ $studentUser->name }}</p>
                            <p class="cv-student-email">{{ $studentUser->email }}</p>
                            <div class="cv-student-metrics">
                                <span class="cv-metric subs"><i class="fas fa-clipboard-list"></i> {{ $subCount }}</span>
                                <span class="cv-metric graded"><i class="fas fa-check-circle"></i> {{ $gradedCount }}</span>
                                @if($pendingCount > 0)
                                    <span class="cv-metric pending"><i class="fas fa-hourglass-half"></i> {{ $pendingCount }}</span>
                                @endif
                            </div>
                            <a href="{{ route('teacher.student.submissions', ['classroom' => $classroom->id, 'student' => $studentUser->id]) }}" class="cv-student-btn">
                                <i class="fas fa-clipboard-check"></i> Review
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="cv-empty">
                    <i class="fas fa-user-plus"></i>
                    <h3>No Students Yet</h3>
                    <p>Share the access code to invite students</p>
                </div>
            @endif
        </div>
    @endif

    <!-- ============ ASSIGNMENTS ============ -->
    <div class="cv-card">
        <div class="cv-card-header">
            <h2><span class="ico"><i class="fas fa-tasks"></i></span> Assignments</h2>
            <span class="cv-count">{{ $assignmentCount }}</span>
        </div>

        @if($assignmentCount > 0)
            <div class="cv-assignments">
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
                    <div class="cv-assignment">
                        <div style="flex: 1; min-width: 220px; padding-left: 8px;">
                            <h4 class="cv-assign-title">
                                <i class="fas fa-book-quran"></i>
                                {{ $assignmentTitle }}
                            </h4>
                            <div class="cv-assign-meta">
                                <span><i class="far fa-calendar"></i> Due {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d') }}</span>
                                @if($isTeacher)
                                    <span><i class="fas fa-file-alt"></i> {{ $submissionCount }} Submitted</span>
                                @endif
                            </div>
                        </div>

                        <div class="cv-assign-right">
                            <span class="cv-pts"><i class="fas fa-star"></i> {{ $assignment->total_marks }} pts</span>

                            @if($isTeacher)
                                <a href="{{ route('assignment.show', $assignment->assignment_id) }}" class="cv-assign-btn solid">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('assignment.edit', $assignment->assignment_id) }}" class="cv-assign-btn edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @else
                                @if($studentSubmission)
                                    <span class="cv-status ok"><i class="fas fa-check-circle"></i> Submitted</span>
                                    <a href="{{ route('student.assignment.view', $assignment->assignment_id) }}" class="cv-assign-btn outline">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                @else
                                    <span class="cv-status wait"><i class="fas fa-hourglass-half"></i> Pending</span>
                                    <a href="{{ route('student.assignment.submit', $assignment->assignment_id) }}" class="cv-assign-btn solid">
                                        <i class="fas fa-paper-plane"></i> Submit
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="cv-empty">
                <i class="fas fa-clipboard"></i>
                <h3>No Assignments Yet</h3>
                <p>{{ $isTeacher ? 'Create your first assignment to get started' : 'Your teacher has not created any yet' }}</p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('extra-scripts')
<script>
    function toggleCode() {
        const el = document.getElementById('cvCode');
        const eye = document.getElementById('cvEye');
        const real = @json($classroom->access_code);

        if (el.textContent.indexOf('•') !== -1) {
            el.textContent = real;
            eye.classList.remove('fa-eye');
            eye.classList.add('fa-eye-slash');
        } else {
            el.textContent = '••••••';
            eye.classList.remove('fa-eye-slash');
            eye.classList.add('fa-eye');
        }
    }
</script>
@endsection
