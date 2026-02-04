@extends('layouts.dashboard')

@section('title', 'Student Dashboard')
@section('user-role', 'Student • Learning Portal')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('content')
<style>
    /* Welcome Banner */
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
        top: 0; left: 0; right: 0; bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .welcome-content { position: relative; z-index: 2; }
    .welcome-content h1 { font-size: 2.5rem; margin-bottom: 10px; font-weight: 700; color: #ffffff; text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3); }
    .welcome-content p { font-size: 1.25rem; opacity: 0.95; line-height: 1.6; }

    /* Stats Grid */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 25px; margin-bottom: 30px; }
    .stat-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1); border: 3px solid #2a2a2a; }
    .stat-icon { font-size: 2.5rem; margin-bottom: 10px; }
    .stat-value { font-size: 2.5rem; font-weight: 800; color: #000; line-height: 1; margin-bottom: 5px; }
    .stat-label { color: #333; font-size: 1.05rem; font-weight: 700; }

    /* Layout Split */
    .dashboard-lower-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px; align-items: start; }
    @media (max-width: 992px) { .dashboard-lower-grid { grid-template-columns: 1fr; } }

    .section-card { background: white; border-radius: 15px; padding: 25px; border: 3px solid #2a2a2a; height: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .section-title { font-size: 1.6rem; color: #000 !important; font-weight: 800; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }

    /* High Contrast Cards */
    .class-item-card {
        display: flex; align-items: center; gap: 15px; padding: 15px; 
        background: #f9f9f9; border-radius: 12px; border: 2px solid #2a2a2a; 
        margin-bottom: 12px; transition: all 0.3s ease; text-decoration: none; color: inherit;
    }
    .class-item-card:hover { background: #fff; transform: translateX(5px); box-shadow: 5px 5px 0 #2a2a2a; }

    .class-icon-card { 
        width: 50px; height: 50px; background: linear-gradient(135deg, #0a5c36, #1abc9c); 
        border-radius: 12px; display: flex; align-items: center; justify-content: center; 
        font-size: 1.5rem; color: white; border: 2px solid #2a2a2a;
    }

    .class-details-card { flex: 1; }
    .class-details-card h4 { color: #000 !important; font-weight: 800; margin-bottom: 5px; font-size: 1.2rem; }
    .class-details-card p { color: #222 !important; font-size: 1.05rem; font-weight: 600; margin: 0; }
    
    .score-badge, .score-value { font-size: 1.8rem; font-weight: 900; }
    .empty-state { text-align: center; padding: 40px; color: #000; font-weight: 700; font-size: 1.1rem; }
    .empty-state i { font-size: 3rem; margin-bottom: 15px; display: block; opacity: 0.5; }
</style>

<section class="welcome-banner">
    <div class="welcome-content">
        <h1>Assalamu'alaikum, <span style="color: #d4af37;">{{ $student->name }}</span>! 👋</h1>
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
        <div class="stat-label">Completed</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">⭐</div>
        <div class="stat-value">{{ number_format($averageScore, 1) }}%</div>
        <div class="stat-label">Average Score</div>
    </div>
</div>

<!-- Dashboard Lower Grid -->
<div class="dashboard-lower-grid">
    <!-- My Enrolled Classes Section -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-users"></i> My Enrolled Classes
        </h2>
        
        @if($student->classrooms->count() > 0)
            @foreach($student->classrooms->sortByDesc('pivot.date_joined')->take(4) as $classroom)
                <a href="{{ route('classroom.show', $classroom->id) }}" class="class-item-card" style="border-left: 8px solid #0a5c36;">
                    <div class="class-icon-card">
                        <i class="fas fa-book-quran"></i>
                    </div>
                    <div class="class-details-card">
                        <h4>{{ $classroom->class_name }}</h4>
                        <p>
                            <i class="fas fa-user"></i> {{ $classroom->teacher->name }} • 
                            <i class="fas fa-tasks"></i> {{ $classroom->assignments->count() }} Tasks
                        </p>
                    </div>
                    <i class="fas fa-arrow-right" style="color: #000;"></i>
                </a>
            @endforeach
            
            @if($student->classrooms->count() > 4)
                <div style="text-align: center; margin-top: 15px;">
                    <a href="{{ route('student.classes') }}" style="color: #0a5c36; font-weight: 800; text-decoration: underline; font-size: 1.05rem;">
                        View All Classes
                    </a>
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No classes yet.</p>
                <a href="{{ route('student.classes') }}" style="color: #0a5c36; font-weight: 700; text-decoration: underline;">
                    Browse Classes
                </a>
            </div>
        @endif
    </div>

    <!-- Recent Grades Section -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-star"></i> Recent Grades
        </h2>

        @if($student->scores->count() > 0)
            @foreach($student->scores->sortByDesc('created_at')->take(4) as $score)
                @php
                    $statusColor = $score->score >= 80 ? '#27ae60' : ($score->score >= 60 ? '#f39c12' : '#e74c3c');
                    $grad = $score->score >= 80 ? '#2ecc71, #27ae60' : ($score->score >= 60 ? '#f39c12, #e67e22' : '#e74c3c, #c0392b');
                    $icon = $score->score >= 80 ? 'trophy' : ($score->score >= 60 ? 'medal' : 'flag');
                @endphp
                <div class="class-item-card" style="border-left: 8px solid {{ $statusColor }};">
                    <div class="class-icon-card" style="background: linear-gradient(135deg, {{ $grad }}); border-color: #2a2a2a;">
                        <i class="fas fa-{{ $icon }}"></i>
                    </div>
                    <div class="class-details-card">
                        <h4>{{ Str::limit($score->assignment->title ?? 'Assignment', 25) }}</h4>
                        <p style="font-size: 1rem;">{{ $score->created_at->format('M d, Y') }} • {{ $score->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="score-value" style="color: {{ $statusColor }};">
                        {{ number_format($score->score, 0) }}%
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-graduation-cap"></i>
                <p>No grades available yet.</p>
            </div>
        @endif
    </div>
</div>

@endsection
