@extends('layouts.template')

@section('title', 'Student Dashboard')
@section('page-title', 'My Dashboard')
@section('page-subtitle', 'Track your learning progress and stay on top of your assignments')

@section('content')
<style>
    .dashboard-container {
        padding: 25px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: rgba(31, 39, 27, 0.6);
        border: 2px solid var(--color-dark-green);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(227, 216, 136, 0.2);
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 10px;
        display: block;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--color-gold);
        font-family: 'Cairo', sans-serif;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
        font-family: 'Cairo', sans-serif;
    }

    .section-card {
        background: rgba(31, 39, 27, 0.6);
        border: 2px solid var(--color-dark-green);
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 1.3rem;
        color: var(--color-gold);
        font-family: 'Amiri', serif;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(227, 216, 136, 0.2);
    }

    .class-item {
        background: rgba(31, 39, 27, 0.5);
        border: 1px solid rgba(227, 216, 136, 0.2);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 12px;
        transition: all 0.3s ease;
    }

    .class-item:hover {
        background: rgba(227, 216, 136, 0.1);
        border-color: var(--color-gold);
    }

    .class-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .class-name {
        font-size: 1.1rem;
        color: var(--color-gold);
        font-weight: 600;
        font-family: 'Cairo', sans-serif;
    }

    .class-teacher {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
        font-family: 'Cairo', sans-serif;
    }

    .class-info {
        display: flex;
        gap: 15px;
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .class-info span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: rgba(255, 255, 255, 0.5);
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .empty-state-text {
        font-size: 1rem;
        font-family: 'Cairo', sans-serif;
    }

    .btn-enroll {
        margin-top: 15px;
        padding: 10px 20px;
        background: var(--color-dark-green);
        color: var(--color-gold);
        border: 2px solid var(--color-gold);
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-enroll:hover {
        background: var(--color-gold);
        color: var(--color-dark-green);
    }
</style>

<div class="dashboard-container">
    <!-- Statistics Cards -->
    <div class="dashboard-grid">
        <div class="stat-card">
            <span class="stat-icon">🏫</span>
            <div class="stat-value">{{ $enrolledClassesCount }}</div>
            <div class="stat-label">Enrolled Classes</div>
        </div>

        <div class="stat-card">
            <span class="stat-icon">📝</span>
            <div class="stat-value">{{ $pendingAssignments }}</div>
            <div class="stat-label">Pending Assignments</div>
        </div>

        <div class="stat-card">
            <span class="stat-icon">✅</span>
            <div class="stat-value">{{ $completedAssignments }}</div>
            <div class="stat-label">Completed Assignments</div>
        </div>

        <div class="stat-card">
            <span class="stat-icon">⭐</span>
            <div class="stat-value">{{ number_format($averageScore, 1) }}%</div>
            <div class="stat-label">Average Score</div>
        </div>
    </div>

    <!-- My Classes Section -->
    <div class="section-card">
        <h2 class="section-title">My Enrolled Classes</h2>
        
        @if($student->classrooms->count() > 0)
            @foreach($student->classrooms as $classroom)
                <div class="class-item">
                    <div class="class-header">
                        <div class="class-name">{{ $classroom->class_name }}</div>
                    </div>
                    <div class="class-teacher">
                        👨‍🏫 {{ $classroom->teacher->name ?? 'Unknown Teacher' }}
                    </div>
                    <div class="class-info">
                        <span>📚 {{ $classroom->assignments->count() }} Assignments</span>
                        <span>📖 {{ $classroom->description ?? 'No description' }}</span>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📚</div>
                <div class="empty-state-text">You haven't enrolled in any classes yet.</div>
                <a href="{{ route('student.classes') }}" class="btn-enroll">Browse Classes</a>
            </div>
        @endif
    </div>

    <!-- Recent Activity Section (if needed) -->
    @if($student->scores->count() > 0)
        <div class="section-card">
            <h2 class="section-title">Recent Grades</h2>
            
            @foreach($student->scores->sortByDesc('created_at')->take(5) as $score)
                <div class="class-item">
                    <div class="class-header">
                        <div class="class-name">{{ $score->assignment->title ?? 'Assignment' }}</div>
                        <div class="stat-value" style="font-size: 1.2rem;">{{ $score->score }}%</div>
                    </div>
                    <div class="class-teacher">
                        📅 {{ $score->created_at->format('M d, Y') }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection