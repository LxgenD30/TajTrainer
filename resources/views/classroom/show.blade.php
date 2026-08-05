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
    /* ============ PAGE SHELL (Brite-style light panel) ============ */
    .bt-shell {
        display: grid;
        gap: 22px;
    }

    /* ============ HEADER CARD ============ */
    .bt-hero {
        background: #fff;
        border-radius: 20px;
        padding: 26px 30px;
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        box-shadow: 0 12px 34px rgba(0, 0, 0, 0.16);
        animation: btIn 0.45s ease both;
    }

    .bt-hero-icon {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.9rem;
        box-shadow: 0 10px 24px rgba(10, 92, 54, 0.3);
        flex-shrink: 0;
    }

    .bt-hero-info { flex: 1; min-width: 220px; }

    .bt-hero h1 {
        margin: 0 0 4px;
        font-family: 'El Messiri', serif;
        font-size: 2rem;
        font-weight: 800;
        color: #1a1a1a;
        line-height: 1.15;
    }

    .bt-hero p {
        margin: 0;
        color: #333;
        font-size: 1.08rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .bt-hero p i { color: #0a5c36; }

    .bt-hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .bt-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
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

    .bt-btn.gold {
        background: linear-gradient(135deg, #f4d03f, #d4af37);
        border-color: #b8860b;
        box-shadow: 0 8px 20px rgba(180, 130, 20, 0.25);
    }
    .bt-btn.gold:hover { transform: translateY(-2px); }

    .bt-btn.green {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.25);
    }
    .bt-btn.green:hover { transform: translateY(-2px); }

    .bt-btn.outline {
        background: #fff;
        color: #0a5c36;
        border-color: #0a5c36;
    }
    .bt-btn.outline:hover { background: #0a5c36; color: #fff; }

    /* ============ STAT CARDS ============ */
    .bt-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 18px;
    }

    .bt-stat {
        background: #fff;
        border-radius: 18px;
        padding: 22px 24px;
        display: flex;
        align-items: center;
        gap: 18px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
        transition: transform 0.22s ease, box-shadow 0.22s ease;
        animation: btIn 0.45s ease both;
    }
    .bt-stat:nth-child(2) { animation-delay: 0.05s; }
    .bt-stat:nth-child(3) { animation-delay: 0.1s; }
    .bt-stat:nth-child(4) { animation-delay: 0.15s; }

    .bt-stat:hover { transform: translateY(-4px); box-shadow: 0 18px 38px rgba(0, 0, 0, 0.16); }

    .bt-stat-chip {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        flex-shrink: 0;
    }
    .bt-stat-chip.green { background: linear-gradient(135deg, #0a5c36, #1abc9c); }
    .bt-stat-chip.gold { background: linear-gradient(135deg, #d4af37, #b8860b); }
    .bt-stat-chip.teal { background: linear-gradient(135deg, #1abc9c, #14855a); }
    .bt-stat-chip.amber { background: linear-gradient(135deg, #f39c12, #e67e22); }

    .bt-stat .n {
        font-size: 2.2rem;
        font-weight: 900;
        color: #1a1a1a;
        line-height: 1;
    }
    .bt-stat .l {
        margin-top: 5px;
        font-size: 0.8rem;
        font-weight: 800;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    /* ============ CONTENT GRID ============ */
    .bt-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(280px, 0.85fr);
        gap: 22px;
        align-items: start;
    }
    .bt-grid.student {
        grid-template-columns: minmax(0, 1.6fr) minmax(300px, 1fr);
    }

    .bt-card {
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.1);
        animation: btIn 0.45s ease both;
    }

    .bt-card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #eef1ee;
    }

    .bt-card-head .ch {
        width: 44px;
        height: 44px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        color: #fff;
        flex-shrink: 0;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
    }
    .bt-card-head .ch.gold { background: linear-gradient(135deg, #d4af37, #b8860b); }

    .bt-card-head h3 {
        margin: 0;
        font-family: 'El Messiri', serif;
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a1a1a;
        flex: 1;
    }

    .bt-count-pill {
        background: #eef6f1;
        color: #0a5c36;
        font-weight: 800;
        font-size: 1rem;
        padding: 6px 16px;
        border-radius: 50px;
        white-space: nowrap;
    }

    /* ============ STUDENTS GRID ============ */
    .bt-students {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 14px;
    }

    .bt-student {
        background: #f8faf9;
        border: 1px solid #e6ece5;
        border-radius: 16px;
        padding: 18px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 9px;
        transition: all 0.22s ease;
    }
    .bt-student:hover { border-color: #0a5c36; transform: translateY(-3px); box-shadow: 0 12px 26px rgba(10, 92, 54, 0.12); }

    .bt-avatar {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.5rem;
        overflow: hidden;
    }
    .bt-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .bt-student-name {
        font-family: 'El Messiri', serif;
        font-size: 1.15rem;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
        line-height: 1.2;
    }
    .bt-student-email {
        font-size: 0.88rem;
        color: #555;
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    .bt-mini-chips { display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; }
    .bt-mini {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.85rem;
        font-weight: 800;
        padding: 5px 11px;
        border-radius: 50px;
    }
    .bt-mini.subs { color: #1d4ed8; background: #dbeafe; }
    .bt-mini.graded { color: #15803d; background: #dcfce7; }
    .bt-mini.pending { color: #b45309; background: #fef3c7; }

    .bt-student-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px;
        border-radius: 11px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        text-decoration: none;
        font-weight: 800;
        font-size: 1rem;
        transition: all 0.2s ease;
        font-family: 'Cairo', sans-serif;
        margin-top: 2px;
    }
    .bt-student-btn:hover { transform: translateY(-2px); }

    /* ============ ASSIGNMENT ROWS ============ */
    .bt-assignments { display: grid; gap: 14px; }

    .bt-assignment {
        background: #f8faf9;
        border: 1px solid #e6ece5;
        border-radius: 15px;
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
    .bt-assignment::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(180deg, #1abc9c, #0a5c36);
    }
    .bt-assignment:hover { border-color: #0a5c36; transform: translateY(-2px); box-shadow: 0 10px 24px rgba(10, 92, 54, 0.1); }

    .bt-assign-title {
        font-family: 'El Messiri', serif;
        font-size: 1.3rem;
        font-weight: 800;
        color: #0a5c36;
        margin: 0 0 7px;
        display: flex;
        align-items: center;
        gap: 9px;
        flex-wrap: wrap;
    }
    .bt-assign-title i { color: #b8860b; }

    .bt-assign-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 18px;
        color: #333;
        font-size: 1rem;
        font-weight: 600;
    }
    .bt-assign-meta i { color: #0a5c36; margin-right: 5px; }

    .bt-assign-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .bt-pts {
        background: #f8f1d5;
        border: 1px solid #e5d699;
        color: #7a4f00;
        font-weight: 800;
        font-size: 0.98rem;
        padding: 7px 15px;
        border-radius: 50px;
        white-space: nowrap;
    }

    .bt-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 0.98rem;
        font-weight: 800;
        padding: 7px 15px;
        border-radius: 50px;
        white-space: nowrap;
    }
    .bt-status.ok { background: #dcfce7; color: #15803d; }
    .bt-status.wait { background: #fef3c7; color: #92400e; }

    .bt-a-btn {
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
        color: #1a1a1a;
    }
    .bt-a-btn.solid { background: linear-gradient(135deg, #0a5c36, #1abc9c); color: #fff; box-shadow: 0 7px 16px rgba(10, 92, 54, 0.2); }
    .bt-a-btn.solid:hover { transform: translateY(-2px); }
    .bt-a-btn.outline { background: #fff; color: #0a5c36; border: 2px solid #0a5c36; }
    .bt-a-btn.outline:hover { background: #0a5c36; color: #fff; }
    .bt-a-btn.edit { background: #fff; color: #b45309; border: 2px solid #f59e0b; }
    .bt-a-btn.edit:hover { background: #f59e0b; color: #fff; }

    /* ============ INFO SIDEBAR ============ */
    .bt-info { display: grid; gap: 22px; }

    .bt-about p {
        margin: 0;
        color: #1a1a1a;
        font-size: 1.08rem;
        line-height: 1.7;
    }

    .bt-code {
        background: #f6f8f5;
        border: 1px solid #d9e2db;
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .bt-code-value {
        font-family: 'Courier New', monospace;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: 5px;
        color: #1a1a1a;
        flex: 1;
    }
    .bt-code-btn {
        background: #0a5c36;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 9px 15px;
        font-size: 0.98rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Cairo', sans-serif;
    }
    .bt-code-btn:hover { background: #1abc9c; }

    .bt-teacher {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8faf9;
        border: 1px solid #e6ece5;
        border-radius: 14px;
        padding: 14px 16px;
    }
    .bt-teacher-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d4af37, #b8860b);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        overflow: hidden;
        flex-shrink: 0;
    }
    .bt-teacher-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .bt-teacher .tt { font-size: 1.05rem; font-weight: 800; color: #1a1a1a; margin: 0; }
    .bt-teacher .ts { font-size: 0.9rem; color: #555; margin: 0; }

    /* Empty state */
    .bt-empty {
        text-align: center;
        padding: 48px 20px;
        color: #1a1a1a;
        border: 2px dashed #c7d4cc;
        border-radius: 16px;
        background: #fbfdfc;
    }
    .bt-empty i { font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 12px; color: #0a5c36; }
    .bt-empty h3 { color: #0a5c36; font-size: 1.35rem; margin: 0 0 5px; font-family: 'El Messiri', serif; }
    .bt-empty p { margin: 0; font-size: 1.05rem; color: #333; }

    @keyframes btIn {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1200px) {
        .bt-grid, .bt-grid.student { grid-template-columns: 1fr; }
        .bt-info { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 720px) {
        .bt-hero { padding: 22px; }
        .bt-info { grid-template-columns: 1fr; }
    }
</style>

<div class="bt-shell">
    <!-- ============ HEADER ============ -->
    <div class="bt-hero">
        <div class="bt-hero-icon"><i class="fas fa-school"></i></div>
        <div class="bt-hero-info">
            <h1>{{ $classroom->class_name }}</h1>
            <p>
                <i class="fas fa-user-tie"></i>
                {{ $isTeacher ? 'Your Classroom' : ($classroom->teacher->name ?? 'Teacher') }}
                @if($classroom->description)
                    <span style="opacity: 0.85;">• {{ \Illuminate\Support\Str::limit($classroom->description, 70) }}</span>
                @endif
            </p>
        </div>
        <div class="bt-hero-actions">
            @if($isTeacher)
                <a href="{{ route('classroom.edit', $classroom->id) }}" class="bt-btn outline">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('assignment.create', $classroom->id) }}" class="bt-btn gold">
                    <i class="fas fa-plus"></i> New Assignment
                </a>
                <a href="{{ route('classroom.index') }}" class="bt-btn green">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            @else
                <a href="{{ route('student.classes') }}" class="bt-btn green">
                    <i class="fas fa-arrow-left"></i> My Classes
                </a>
            @endif
        </div>
    </div>

    <!-- ============ STATS ============ -->
    <div class="bt-stats">
        <div class="bt-stat">
            <div class="bt-stat-chip green"><i class="fas fa-users"></i></div>
            <div>
                <div class="n">{{ $studentCount }}</div>
                <div class="l">Students</div>
            </div>
        </div>
        <div class="bt-stat">
            <div class="bt-stat-chip gold"><i class="fas fa-tasks"></i></div>
            <div>
                <div class="n">{{ $assignmentCount }}</div>
                <div class="l">Assignments</div>
            </div>
        </div>
        <div class="bt-stat">
            <div class="bt-stat-chip teal"><i class="fas fa-clipboard-check"></i></div>
            <div>
                <div class="n">{{ $totalSubmitted }}</div>
                <div class="l">Submitted</div>
            </div>
        </div>
        @if($isTeacher)
            <div class="bt-stat">
                <div class="bt-stat-chip amber"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <div class="n">{{ $totalPending }}</div>
                    <div class="l">Awaiting Grade</div>
                </div>
            </div>
        @else
            <div class="bt-stat">
                <div class="bt-stat-chip amber"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <div class="n">{{ max(0, $assignmentCount - $totalSubmitted) }}</div>
                    <div class="l">To Complete</div>
                </div>
            </div>
        @endif
    </div>

    <!-- ============ CONTENT ============ -->
    <div class="bt-grid {{ $isTeacher ? '' : 'student' }}">

        @if($isTeacher)
            <!-- ============ STUDENTS ============ -->
            <div class="bt-card">
                <div class="bt-card-head">
                    <div class="ch"><i class="fas fa-users"></i></div>
                    <h3>Students</h3>
                    <span class="bt-count-pill">{{ $studentCount }}</span>
                </div>

                @if($studentCount > 0)
                    <div class="bt-students">
                        @foreach($students as $studentUser)
                            @php
                                $subCount = $studentUser->total_submissions ?? 0;
                                $gradedCount = $studentUser->graded_submissions ?? 0;
                                $pendingCount = max(0, $subCount - $gradedCount);
                            @endphp
                            <div class="bt-student">
                                <div class="bt-avatar">
                                    @if($studentUser->profile_picture)
                                        <img src="{{ asset('storage/' . $studentUser->profile_picture) }}" alt="Avatar">
                                    @else
                                        {{ strtoupper(substr($studentUser->name, 0, 1)) }}
                                    @endif
                                </div>
                                <p class="bt-student-name">{{ $studentUser->name }}</p>
                                <p class="bt-student-email">{{ $studentUser->email }}</p>
                                <div class="bt-mini-chips">
                                    <span class="bt-mini subs"><i class="fas fa-clipboard-list"></i> {{ $subCount }}</span>
                                    <span class="bt-mini graded"><i class="fas fa-check-circle"></i> {{ $gradedCount }}</span>
                                    @if($pendingCount > 0)
                                        <span class="bt-mini pending"><i class="fas fa-hourglass-half"></i> {{ $pendingCount }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('teacher.student.submissions', ['classroom' => $classroom->id, 'student' => $studentUser->id]) }}" class="bt-student-btn">
                                    <i class="fas fa-clipboard-check"></i> Review
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bt-empty">
                        <i class="fas fa-user-plus"></i>
                        <h3>No Students Yet</h3>
                        <p>Share the access code to invite students</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- ============ ASSIGNMENTS ============ -->
        <div class="bt-card">
            <div class="bt-card-head">
                <div class="ch gold"><i class="fas fa-tasks"></i></div>
                <h3>Assignments</h3>
                <span class="bt-count-pill">{{ $assignmentCount }}</span>
            </div>

            @if($assignmentCount > 0)
                <div class="bt-assignments">
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
                        <div class="bt-assignment">
                            <div style="flex: 1; min-width: 200px; padding-left: 8px;">
                                <h4 class="bt-assign-title">
                                    <i class="fas fa-book-quran"></i>
                                    {{ $assignmentTitle }}
                                </h4>
                                <div class="bt-assign-meta">
                                    <span><i class="far fa-calendar"></i> Due {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}</span>
                                    @if($isTeacher)
                                        <span><i class="fas fa-file-alt"></i> {{ $submissionCount }} Submitted</span>
                                    @endif
                                </div>
                            </div>

                            <div class="bt-assign-right">
                                <span class="bt-pts"><i class="fas fa-star"></i> {{ $assignment->total_marks }} pts</span>

                                @if($isTeacher)
                                    <a href="{{ route('assignment.show', $assignment->assignment_id) }}" class="bt-a-btn solid">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('assignment.edit', $assignment->assignment_id) }}" class="bt-a-btn edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @else
                                    @if($studentSubmission)
                                        <span class="bt-status ok"><i class="fas fa-check-circle"></i> Submitted</span>
                                        <a href="{{ route('student.assignment.view', $assignment->assignment_id) }}" class="bt-a-btn outline">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @else
                                        <span class="bt-status wait"><i class="fas fa-hourglass-half"></i> Pending</span>
                                        <a href="{{ route('student.assignment.submit', $assignment->assignment_id) }}" class="bt-a-btn solid">
                                            <i class="fas fa-paper-plane"></i> Submit
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bt-empty">
                    <i class="fas fa-clipboard"></i>
                    <h3>No Assignments Yet</h3>
                    <p>{{ $isTeacher ? 'Create your first assignment to get started' : 'Your teacher has not created any yet' }}</p>
                </div>
            @endif
        </div>

        <!-- ============ INFO SIDEBAR ============ -->
        <div class="bt-info">
            <div class="bt-card">
                <div class="bt-card-head">
                    <div class="ch"><i class="fas fa-info-circle"></i></div>
                    <h3>Class Info</h3>
                </div>

                <div class="bt-about" style="margin-bottom: 18px;">
                    <p>{{ $classroom->description ?: 'No description provided.' }}</p>
                </div>

                <h4 style="font-family: 'El Messiri', serif; font-size: 1.15rem; font-weight: 800; color: #1a1a1a; margin: 0 0 10px;">
                    <i class="fas fa-key" style="color: #0a5c36;"></i> Access Code
                </h4>
                <div class="bt-code">
                    <span class="bt-code-value" id="btCode">••••••</span>
                    <button class="bt-code-btn" id="btCodeBtn" onclick="toggleCode()">
                        <i class="fas fa-eye"></i> Show
                    </button>
                </div>

                @if($classroom->teacher)
                    <h4 style="font-family: 'El Messiri', serif; font-size: 1.15rem; font-weight: 800; color: #1a1a1a; margin: 22px 0 10px;">
                        <i class="fas fa-user-tie" style="color: #0a5c36;"></i> Teacher
                    </h4>
                    <div class="bt-teacher">
                        <div class="bt-teacher-avatar">
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
    function toggleCode() {
        const el = document.getElementById('btCode');
        const btn = document.getElementById('btCodeBtn');
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
