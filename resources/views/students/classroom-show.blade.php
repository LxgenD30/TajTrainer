@extends('layouts.dashboard')

@section('title', $classroom->class_name)
@section('user-role', 'Student • ' . $classroom->class_name)

@section('navigation')
    <a href="{{ url('/student/classes') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ url('/student/classes') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="nav-label">My Classes</div>
    </a>
    
    <a href="{{ url('/student/practice') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-microphone-alt"></i>
        </div>
        <div class="nav-label">Practice</div>
    </a>
    
    <a href="{{ url('/student/progress') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="nav-label">My Progress</div>
    </a>
    
    <a href="{{ url('/student/materials') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="nav-label">Materials</div>
    </a>
    
    <a href="{{ route('students.show', Auth::id()) }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="nav-label">Profile</div>
    </a>
    
    <form action="{{ route('logout') }}" method="POST" style="display: inline;" class="nav-item">
        @csrf
        <button type="submit" style="all: unset; width: 100%; cursor: pointer;">
            <div class="nav-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="nav-label">Logout</div>
        </button>
    </form>
@endsection

@section('extra-styles')
<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .spinner {
        border: 2px solid rgba(10, 92, 54, 0.3);
        border-top: 2px solid var(--primary-green);
        border-radius: 50%;
        width: 14px;
        height: 14px;
        animation: spin 0.6s linear infinite;
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary-green);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        font-family: 'El Messiri', sans-serif;
    }
    
    .back-link:hover {
        color: var(--dark-green);
        transform: translateX(-5px);
    }
    
    .classroom-header {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: var(--white);
        box-shadow: 0 8px 20px var(--shadow);
    }
    
    .classroom-title {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .classroom-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }
    
    .classroom-title h2 {
        color: var(--white);
        font-size: 2rem;
        margin: 0;
    }
    
    .classroom-meta {
        display: flex;
        gap: 25px;
        font-size: 1rem;
        flex-wrap: wrap;
    }
    
    .classroom-meta span {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .assignments-section {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 20px var(--shadow);
    }
    
    .section-title {
        font-size: 1.6rem;
        color: var(--primary-green);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .assignments-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .assignment-card {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.03), rgba(46, 139, 87, 0.03));
        border: 2px solid rgba(10, 92, 54, 0.15);
        border-left: 5px solid var(--primary-green);
        border-radius: 15px;
        padding: 25px;
        transition: all 0.3s ease;
    }
    
    .assignment-card:hover {
        transform: translateX(8px);
        border-color: var(--gold);
        border-left-color: var(--primary-green);
        box-shadow: 0 6px 20px rgba(10, 92, 54, 0.15);
    }
    
    .assignment-card.graded {
        border-left-color: #4caf50;
    }
    
    .assignment-card.submitted {
        border-left-color: #2196f3;
    }
    
    .assignment-card.overdue {
        border-left-color: #e74c3c;
    }
    
    .assignment-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        gap: 20px;
        margin-bottom: 15px;
    }
    
    .assignment-title h4 {
        color: var(--primary-green);
        font-size: 1.3rem;
        margin: 0 0 8px 0;
    }
    
    .assignment-desc {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        white-space: nowrap;
        font-family: 'El Messiri', sans-serif;
    }
    
    .status-graded {
        background: #4caf50;
        color: white;
    }
    
    .status-submitted {
        background: #2196f3;
        color: white;
    }
    
    .status-pending {
        background: #ff9800;
        color: white;
    }
    
    .status-overdue {
        background: #e74c3c;
        color: white;
    }
    
    .assignment-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(10, 92, 54, 0.1);
        flex-wrap: wrap;
    }
    
    .assignment-info {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        font-size: 0.9rem;
        color: #666;
    }
    
    .assignment-info span {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-action {
        padding: 10px 20px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
        border: none;
        border-radius: 50px;
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.3);
    }
    
    .btn-view {
        background: linear-gradient(135deg, var(--gold), #e6c34a);
        color: var(--dark-green);
    }
    
    .empty-assignments {
        text-align: center;
        padding: 80px 20px;
        color: #999;
    }
    
    .empty-assignments i {
        font-size: 5rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
    
    .empty-assignments h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: var(--primary-green);
    }
    
    @media (max-width: 768px) {
        .classroom-title {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .assignment-header {
            flex-direction: column;
        }
        
        .assignment-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('content')
<a href="{{ route('student.classes') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to My Classes
</a>

<!-- Classroom Header -->
<div class="classroom-header">
    <div class="classroom-title">
        <div class="classroom-icon">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <h2>{{ $classroom->class_name }}</h2>
    </div>
    <div class="classroom-meta">
        <span><i class="fas fa-user"></i> {{ $classroom->teacher->name ?? 'Unknown Teacher' }}</span>
        <span><i class="fas fa-users"></i> {{ $classroom->students->count() }} students</span>
        <span><i class="fas fa-tasks"></i> {{ $assignments->count() }} assignments</span>
        @if($classroom->description)
        <span><i class="fas fa-info-circle"></i> {{ $classroom->description }}</span>
        @endif
    </div>
</div>

<!-- Assignments Section -->
<div class="assignments-section">
    <h3 class="section-title">
        <i class="fas fa-clipboard-list"></i>
        Assignments
    </h3>
    
    @if($assignments->count() > 0)
        <div class="assignments-list">
            @foreach($assignments as $assignment)
                @php
                    $submission = $submissions->get($assignment->assignment_id);
                    $score = \App\Models\Score::where('assignment_id', $assignment->assignment_id)
                                               ->where('user_id', Auth::id())
                                               ->first();
                    
                    $isGraded = $score !== null;
                    $isSubmitted = $submission && $submission->status === 'submitted' && !$isGraded;
                    $isOverdue = $assignment->due_date < now() && !$isSubmitted && !$isGraded;
                    $isPending = !$submission || ($submission->status === 'pending' && !$isGraded);
                @endphp
                
                <div class="assignment-card {{ $isGraded ? 'graded' : ($isSubmitted ? 'submitted' : ($isOverdue ? 'overdue' : '')) }}">
                    <div class="assignment-header">
                        <div class="assignment-title">
                            <h4>
                                @if($assignment->surah)
                                    <i class="fas fa-book-quran"></i> {{ $assignment->surah }} 
                                    ({{ $assignment->start_verse }}@if($assignment->end_verse)-{{ $assignment->end_verse }}@endif)
                                @else
                                    {{ $assignment->title ?? ($assignment->material ? $assignment->material->title : 'Assignment') }}
                                @endif
                            </h4>
                            <p class="assignment-desc">{{ Str::limit($assignment->instructions, 150) }}</p>
                        </div>
                        
                        <div>
                            @if($isGraded)
                                <div class="status-badge status-graded">
                                    <i class="fas fa-check-circle"></i>
                                    {{ $score->score }}/{{ $assignment->total_marks }}
                                </div>
                            @elseif($isSubmitted)
                                <div class="status-badge status-submitted">
                                    <div class="spinner"></div>
                                    Analyzing
                                </div>
                            @elseif($isOverdue)
                                <div class="status-badge status-overdue">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Overdue
                                </div>
                            @else
                                <div class="status-badge status-pending">
                                    <i class="fas fa-clock"></i>
                                    Pending
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="assignment-footer">
                        <div class="assignment-info">
                            @if($assignment->due_date)
                            <span>
                                <i class="fas fa-calendar"></i>
                                Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}
                            </span>
                            @endif
                            <span>
                                <i class="fas fa-star"></i>
                                {{ $assignment->total_marks }} marks
                            </span>
                            @if($assignment->tajweed_rules)
                            <span>
                                <i class="fas fa-brain"></i>
                                Tajweed: {{ implode(', ', json_decode($assignment->tajweed_rules, true) ?? []) }}
                            </span>
                            @endif
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            @if($isGraded || $isSubmitted)
                                <a href="{{ route('assignment.view', $assignment->assignment_id) }}" class="btn-action btn-view">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            @else
                                <a href="{{ route('assignment.submit', $assignment->assignment_id) }}" class="btn-action">
                                    <i class="fas fa-upload"></i> Submit Assignment
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-assignments">
            <i class="fas fa-clipboard-list"></i>
            <h3>No Assignments Yet</h3>
            <p>Your teacher hasn't posted any assignments for this class.</p>
        </div>
    @endif
</div>
@endsection
