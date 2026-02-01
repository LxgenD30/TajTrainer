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
        background: rgba(255, 255, 255, 0.95);
        border: 2px solid var(--color-dark-green);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(77, 139, 49, 0.3);
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 10px;
        display: block;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--color-dark-green);
        font-family: 'Cairo', sans-serif;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 1.1rem;
        color: #555;
        font-family: 'Cairo', sans-serif;
        font-weight: 500;
    }

    .section-card {
        background: rgba(255, 255, 255, 0.95);
        border: 2px solid var(--color-dark-green);
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 1.6rem;
        color: var(--color-dark-green);
        font-family: 'Amiri', serif;
        font-weight: 700;
        margin: 0;
    }

    .view-toggle {
        display: flex;
        gap: 10px;
        background: #f5f5f5;
        padding: 5px;
        border-radius: 8px;
    }

    .view-toggle button {
        padding: 8px 16px;
        border: none;
        background: transparent;
        color: #666;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-family: 'Cairo', sans-serif;
    }

    .view-toggle button.active {
        background: var(--color-dark-green);
        color: white;
    }

    .view-toggle button:hover:not(.active) {
        background: #e0e0e0;
    }

    .classes-container.list-view {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .classes-container.grid-view {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .class-item {
        background: #fafafa;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .class-item:hover {
        border-color: var(--color-dark-green);
        box-shadow: 0 4px 12px rgba(77, 139, 49, 0.2);
        transform: translateY(-2px);
    }

    .class-name {
        font-size: 1.4rem;
        color: var(--color-dark-green);
        font-weight: 700;
        font-family: 'Cairo', sans-serif;
        margin-bottom: 10px;
    }

    .class-teacher {
        font-size: 1.1rem;
        color: #666;
        font-family: 'Cairo', sans-serif;
        margin-bottom: 12px;
        font-weight: 500;
    }

    .class-info {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 1rem;
        color: #555;
    }

    .class-info span {
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 500;
    }

    .class-date {
        font-size: 0.95rem;
        color: #999;
        margin-top: 8px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .empty-state-text {
        font-size: 1.2rem;
        font-family: 'Cairo', sans-serif;
    }

    .btn-enroll {
        margin-top: 15px;
        padding: 12px 24px;
        background: var(--color-dark-green);
        color: white;
        border: none;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-enroll:hover {
        background: #3a6b25;
        transform: scale(1.05);
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
        <div class="section-header">
            <h2 class="section-title">My Enrolled Classes</h2>
            <div class="view-toggle">
                <button onclick="setView('list')" id="listBtn" class="active">
                    📋 List
                </button>
                <button onclick="setView('grid')" id="gridBtn">
                    ▦ Grid
                </button>
            </div>
        </div>
        
        @if($student->classrooms->count() > 0)
            <div class="classes-container list-view" id="classesContainer">
                @foreach($student->classrooms->sortByDesc('pivot.date_joined') as $classroom)
                    <a href="{{ route('classroom.show', $classroom->id) }}" class="class-item">
                        <div class="class-name">{{ $classroom->class_name }}</div>
                        <div class="class-teacher">
                            👨‍🏫 {{ $classroom->teacher->name ?? 'Unknown Teacher' }}
                        </div>
                        <div class="class-info">
                            <span>📚 {{ $classroom->assignments->count() }} Assignments</span>
                            <span>📖 {{ $classroom->description ?? 'No description' }}</span>
                        </div>
                        <div class="class-date">
                            Enrolled: {{ \Carbon\Carbon::parse($classroom->pivot->date_joined)->format('M d, Y') }}
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📚</div>
                <div class="empty-state-text">You haven't enrolled in any classes yet.</div>
                <a href="{{ route('student.classes') }}" class="btn-enroll">Browse Classes</a>
            </div>
        @endif
    </div>

    <!-- Recent Activity Section -->
    @if($student->scores->count() > 0)
        <div class="section-card">
            <h2 class="section-title">Recent Grades</h2>
            
            @foreach($student->scores->sortByDesc('created_at')->take(5) as $score)
                <div class="class-item" style="margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="class-name">{{ $score->assignment->title ?? 'Assignment' }}</div>
                        <div class="stat-value" style="font-size: 1.4rem;">{{ $score->score }}%</div>
                    </div>
                    <div class="class-teacher">
                        📅 {{ $score->created_at->format('M d, Y') }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    function setView(view) {
        const container = document.getElementById('classesContainer');
        const listBtn = document.getElementById('listBtn');
        const gridBtn = document.getElementById('gridBtn');
        
        if (view === 'list') {
            container.className = 'classes-container list-view';
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
            localStorage.setItem('classroomView', 'list');
        } else {
            container.className = 'classes-container grid-view';
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            localStorage.setItem('classroomView', 'grid');
        }
    }
    
    // Load saved preference
    window.addEventListener('DOMContentLoaded', function() {
        const savedView = localStorage.getItem('classroomView') || 'list';
        setView(savedView);
    });
</script>
@endsection