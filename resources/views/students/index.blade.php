@extends('layouts.dashboard')

@section('title', 'Student Dashboard')
@section('user-role', 'Student • Learning Portal')

@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="nav-item active">
        <i class="fas fa-home nav-icon"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    
    <a href="{{ route('student.classes') }}" class="nav-item">
        <i class="fas fa-users nav-icon"></i>
        <span class="nav-label">My Classes</span>
    </a>
    
    <a href="{{ route('student.practice') }}" class="nav-item">
        <i class="fas fa-microphone-alt nav-icon"></i>
        <span class="nav-label">Practice</span>
    </a>
    
    <a href="{{ route('student.progress') }}" class="nav-item">
        <i class="fas fa-chart-line nav-icon"></i>
        <span class="nav-label">My Progress</span>
    </a>
    
    <a href="{{ route('student.materials') }}" class="nav-item">
        <i class="fas fa-book-open nav-icon"></i>
        <span class="nav-label">Materials</span>
    </a>
@endsection

@section('content')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 30px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
        border: 3px solid #2a2a2a;
    }
    
    .welcome-banner:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .welcome-content {
        position: relative;
        z-index: 2;
    }
    
    .welcome-content h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        font-weight: 700;
        color: #ffffff;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .welcome-content p {
        font-size: 1.25rem;
        opacity: 0.95;
        line-height: 1.6;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.15);
    }
    
    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #0a5c36;
        line-height: 1;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #666;
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .dashboard-lower-grid {
        display: grid;
        grid-template-columns: 1fr 1fr; /* Two equal columns */
        gap: 25px;
        margin-bottom: 30px;
    }

    /* Target smaller screens: Stack columns on mobile */
    @media (max-width: 992px) {
        .dashboard-lower-grid {
            grid-template-columns: 1fr;
        }
    }

    .section-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        height: 100%; /* Ensures both boxes match height */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 3px solid #2a2a2a;
    }

    /* High Contrast Typography */
    .section-title {
        font-size: 1.6rem; /* Bigger title */
        color: #000000 !important; /* Pure black */
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .class-details-card h4 {
        color: #000000 !important; /* Pure black */
        font-weight: 700;
        margin-bottom: 4px;
        font-size: 1.2rem; /* Increased from 1.05rem */
    }

    .class-details-card p {
        color: #222222 !important; /* Darker grey/black for readability */
        font-size: 1rem; /* Increased from 0.85rem */
        font-weight: 500;
    }

    .score-value {
        font-size: 2rem; /* Large score */
        font-weight: 800;
    }

    /* Item Card Adjustments */
    .class-item-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 18px;
        background: #f9f9f9;
        border-radius: 12px;
        border: 2px solid #eeeeee;
        border-left: 6px solid #0a5c36;
        margin-bottom: 15px;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
    }

    .class-item-card:hover {
        border-color: #0a5c36;
        background: #ffffff;
        transform: translateX(5px);
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #999;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.3;
    }
</style>

<!-- Welcome Banner -->
<section class="welcome-banner">
    <div class="welcome-content">
        <h1>Assalamu'alaikum, {{ $student->name }}! 👋</h1>
        <p>Welcome back to your learning dashboard. Continue your journey in mastering Tajweed.</p>
    </div>
</section>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-value">{{ $enrolledClassesCount }}</div>
        <div class="stat-label">Enrolled Classes</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-value">{{ $pendingAssignments }}</div>
        <div class="stat-label">Pending Assignments</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-value">{{ $completedAssignments }}</div>
        <div class="stat-label">Assignments Completed</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">⭐</div>
        <div class="stat-value">{{ number_format($averageScore, 1) }}%</div>
        <div class="stat-label">Average Score</div>
    </div>
</div>

<!-- My Classes Section -->
<div class="dashboard-lower-grid">
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-users"></i> My Classes
        </h2>
        
        @if($student->classrooms->count() > 0)
            @foreach($student->classrooms->sortByDesc('pivot.date_joined')->take(4) as $classroom)
                <a href="{{ route('classroom.show', $classroom->id) }}" class="class-item-card">
                    <div class="class-details-card">
                        <h4>{{ $classroom->class_name }}</h4>
                        <p>
                            <i class="fas fa-user-tie"></i> {{ $classroom->teacher->name }}
                        </p>
                    </div>
                    <i class="fas fa-chevron-right" style="color: #000; opacity: 0.3;"></i>
                </a>
            @endforeach
            
            <a href="{{ route('student.classes') }}" style="display: block; text-align: center; color: #000; font-weight: 700; text-decoration: underline; margin-top: 10px; font-size: 1rem;">
                View All Classes
            </a>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p style="color:#000; font-weight:600;">No classes enrolled yet.</p>
            </div>
    </div>

    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-star"></i> Recent Grades
        </h2>
        
        @if($student->scores->count() > 0)
            @foreach($student->scores->sortByDesc('created_at')->take(4) as $score)
                @php
                    $statusColor = $score->score >= 80 ? '#27ae60' : ($score->score >= 60 ? '#f39c12' : '#e74c3c');
                @endphp
                <div class="class-item-card" style="border-left-color: {{ $statusColor }};">
                    <div class="class-details-card">
                        <h4>{{ Str::limit($score->assignment->title ?? 'Assignment', 25) }}</h4>
                        <p style="font-size: 0.9rem;">{{ $score->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="score-value" style="color: {{ $statusColor }};">
                        {{ number_format($score->score, 0) }}%
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-graduation-cap"></i>
                <p style="color:#000; font-weight:600;">No grades available yet.</p>
            </div>
    </div>
</div>
@endsection

