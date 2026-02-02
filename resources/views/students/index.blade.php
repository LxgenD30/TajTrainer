@extends('layouts.dashboard')

@section('title', 'Student Dashboard')
@section('user-role', 'Student • ' . number_format($averageScore, 0) . '% Avg Score')

@section('navigation')
    <a href="{{ url('/student/classes') }}" class="nav-item active">
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
    /* Welcome Section */
    .welcome-section {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 40px;
        color: var(--white);
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px var(--shadow);
    }
    
    .welcome-section:before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    
    .welcome-section:after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 200px;
        height: 200px;
        background-color: rgba(212, 175, 55, 0.1);
        border-radius: 50%;
    }
    
    .welcome-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
    }
    
    .welcome-content h1 {
        color: var(--white);
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    
    .welcome-content p {
        font-size: 1.2rem;
        opacity: 0.95;
        margin-bottom: 20px;
    }
    
    .streak-counter {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background-color: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: bold;
    }
    
    .streak-icon {
        color: var(--gold);
        font-size: 1.5rem;
    }
    
    /* Dashboard Grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        grid-template-rows: repeat(6, auto);
        gap: 25px;
        margin-top: 40px;
    }
    
    .dashboard-item {
        border-radius: 20px;
        padding: 25px;
        background-color: var(--white);
        box-shadow: 0 8px 20px var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(10, 92, 54, 0.2);
    }
    
    .dashboard-item:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(to right, var(--primary-green), var(--light-green));
    }
    
    .item-1 {
        grid-column: 1 / 7;
        grid-row: 1 / 3;
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(46, 139, 87, 0.05));
    }
    
    .item-2 {
        grid-column: 7 / 13;
        grid-row: 1 / 3;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), rgba(255, 215, 0, 0.05));
    }
    
    .item-3 {
        grid-column: 1 / 5;
        grid-row: 3 / 5;
        background: linear-gradient(135deg, rgba(6, 78, 50, 0.05), rgba(10, 92, 54, 0.05));
    }
    
    .item-4 {
        grid-column: 5 / 9;
        grid-row: 3 / 5;
        background: linear-gradient(135deg, rgba(46, 139, 87, 0.05), rgba(86, 179, 132, 0.05));
    }
    
    .item-5 {
        grid-column: 9 / 13;
        grid-row: 3 / 7;
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.08), rgba(6, 78, 50, 0.08));
    }
    
    .item-6 {
        grid-column: 1 / 9;
        grid-row: 5 / 7;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.08), rgba(255, 215, 0, 0.08));
    }
    
    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .item-header h3 {
        font-size: 1.4rem;
        color: var(--primary-green);
    }
    
    .item-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--white);
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
    }
    
    .progress-ring {
        width: 120px;
        height: 120px;
        margin: 20px auto;
        position: relative;
    }
    
    .ring-circle {
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
    
    .ring-bg {
        fill: none;
        stroke: rgba(10, 92, 54, 0.1);
        stroke-width: 8;
    }
    
    .ring-progress {
        fill: none;
        stroke: var(--primary-green);
        stroke-width: 8;
        stroke-linecap: round;
        stroke-dasharray: 314;
        stroke-dashoffset: 314;
        transition: stroke-dashoffset 1.5s ease;
    }
    
    .ring-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        font-family: 'El Messiri', sans-serif;
        font-weight: bold;
        color: var(--dark-green);
    }
    
    .ring-value {
        font-size: 2rem;
        line-height: 1;
    }
    
    .ring-label {
        font-size: 0.8rem;
        opacity: 0.8;
    }
    
    .activity-list {
        list-style: none;
    }
    
    .activity-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        background-color: rgba(10, 92, 54, 0.08);
        color: var(--primary-green);
    }
    
    .activity-details h4 {
        font-size: 1rem;
        margin-bottom: 5px;
    }
    
    .activity-details p {
        font-size: 0.9rem;
        color: #666;
    }
    
    .class-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .class-item {
        display: flex;
        align-items: center;
        padding: 20px;
        background-color: rgba(10, 92, 54, 0.05);
        border-radius: 15px;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
    }
    
    .class-item:hover {
        background-color: rgba(10, 92, 54, 0.1);
        transform: translateX(10px);
    }
    
    .class-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
        font-size: 1.8rem;
    }
    
    .class-details {
        flex: 1;
    }
    
    .class-details h4 {
        font-size: 1.2rem;
        margin-bottom: 5px;
        color: var(--dark-green);
    }
    
    .class-details p {
        font-size: 0.9rem;
        color: #666;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .practice-card {
        text-align: center;
        padding: 30px 20px;
    }
    
    .practice-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: var(--white);
        background: linear-gradient(135deg, var(--gold), #e6c34a);
    }
    
    .practice-card h3 {
        margin-bottom: 15px;
    }
    
    .practice-card p {
        margin-bottom: 20px;
        color: #666;
    }
    
    .start-btn {
        display: inline-block;
        background-color: var(--gold);
        color: var(--dark-green);
        padding: 12px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .start-btn:hover {
        background-color: #e6c34a;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    }
    
    .stats-grid {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    
    .stat-item {
        text-align: center;
        flex: 1;
    }
    
    .stat-item-value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-green);
    }
    
    .stat-item-label {
        font-size: 0.9rem;
        color: #666;
    }
    
    .goal-card {
        margin-top: 25px;
        background-color: rgba(10, 92, 54, 0.05);
        padding: 15px;
        border-radius: 12px;
    }
    
    .goal-card h4 {
        margin-bottom: 10px;
        font-size: 1.1rem;
    }
    
    .goal-card p {
        font-size: 0.9rem;
        color: #666;
    }
    
    .progress-bar {
        height: 8px;
        background-color: rgba(10, 92, 54, 0.1);
        border-radius: 4px;
        margin-top: 10px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background-color: var(--primary-green);
        border-radius: 4px;
        transition: width 1s ease;
    }
    
    .materials-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .material-item {
        background-color: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        padding: 15px;
        display: flex;
        align-items: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
    }
    
    .material-item:hover {
        background-color: rgba(10, 92, 54, 0.1);
        transform: translateY(-5px);
    }
    
    .material-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        background-color: var(--primary-green);
        color: var(--white);
    }
    
    .material-details h4 {
        font-size: 0.9rem;
        margin-bottom: 5px;
    }
    
    .material-details p {
        font-size: 0.8rem;
        color: #666;
    }
    
    @media (max-width: 1200px) {
        .dashboard-grid {
            grid-template-columns: repeat(6, 1fr);
            grid-template-rows: repeat(12, auto);
        }
        
        .item-1 { grid-column: 1 / 7; grid-row: 1 / 3; }
        .item-2 { grid-column: 1 / 7; grid-row: 3 / 5; }
        .item-3 { grid-column: 1 / 4; grid-row: 5 / 7; }
        .item-4 { grid-column: 4 / 7; grid-row: 5 / 7; }
        .item-5 { grid-column: 1 / 7; grid-row: 7 / 10; }
        .item-6 { grid-column: 1 / 7; grid-row: 10 / 13; }
    }
    
    @media (max-width: 768px) {
        .welcome-content h1 {
            font-size: 2rem;
        }
        
        .materials-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .stat-item {
            flex-basis: calc(50% - 8px);
        }
    }
    
    @media (max-width: 576px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
            grid-template-rows: repeat(6, auto);
        }
        
        .item-1, .item-2, .item-3, .item-4, .item-5, .item-6 {
            grid-column: 1;
        }
        
        .item-1 { grid-row: 1; }
        .item-2 { grid-row: 2; }
        .item-3 { grid-row: 3; }
        .item-4 { grid-row: 4; }
        .item-5 { grid-row: 5; }
        .item-6 { grid-row: 6; }
    }
</style>
@endsection

@section('content')
<!-- Welcome Section -->
<section class="welcome-section">
    <div class="welcome-content">
        <h1>Welcome back, {{ explode(' ', $student->name)[0] }}!</h1>
        <p>Continue your journey to perfect Quranic recitation. You're making excellent progress in mastering Tajweed rules.</p>
        <div class="streak-counter">
            <i class="fas fa-fire streak-icon"></i>
            <span>{{ $enrolledClassesCount }} Active {{ Str::plural('Class', $enrolledClassesCount) }}</span>
        </div>
    </div>
</section>

<!-- Creative Dashboard Grid -->
<div class="dashboard-grid">
    <!-- Today's Progress -->
    <div class="dashboard-item item-1">
        <div class="item-header">
            <h3>Your Performance</h3>
            <div class="item-icon">
                <i class="fas fa-trophy"></i>
            </div>
        </div>
        <div class="progress-ring">
            <svg class="ring-circle" width="120" height="120" viewBox="0 0 120 120">
                <circle class="ring-bg" cx="60" cy="60" r="50" />
                <circle class="ring-progress" cx="60" cy="60" r="50" id="main-progress-ring" />
            </svg>
            <div class="ring-text">
                <div class="ring-value">{{ number_format($averageScore, 0) }}%</div>
                <div class="ring-label">Avg Score</div>
            </div>
        </div>
        <p style="text-align: center; margin-top: 15px;">You've completed {{ $completedAssignments }} {{ Str::plural('assignment', $completedAssignments) }}</p>
    </div>

    <!-- My Classes -->
    <div class="dashboard-item item-2">
        <div class="item-header">
            <h3>My Enrolled Classes</h3>
            <div class="item-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="class-list">
            @forelse($student->classrooms->sortByDesc('pivot.date_joined')->take(3) as $classroom)
            <a href="{{ url('/student/classroom/' . $classroom->id) }}" class="class-item">
                <div class="class-icon">
                    <i class="fas fa-book-quran"></i>
                </div>
                <div class="class-details">
                    <h4>{{ $classroom->class_name }}</h4>
                    <p>
                        <i class="fas fa-user"></i> {{ $classroom->teacher->name }} • 
                        <i class="fas fa-tasks"></i> {{ $classroom->assignments->count() }} {{ Str::plural('Assignment', $classroom->assignments->count()) }}
                    </p>
                </div>
            </a>
            @empty
            <div style="text-align: center; padding: 30px; color: #666;">
                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                <p>No classes enrolled yet.</p>
                <a href="{{ url('/student/classes') }}" class="start-btn" style="margin-top: 15px; display: inline-block;">Enroll Now</a>
            </div>
            @endforelse
            
            @if($student->classrooms->count() > 3)
            <a href="{{ url('/student/classes') }}" style="text-align: center; padding: 15px; color: var(--primary-green); text-decoration: none; font-weight: 600;">
                View All {{ $student->classrooms->count() }} Classes <i class="fas fa-arrow-right"></i>
            </a>
            @endif
        </div>
    </div>

    <!-- Upcoming Assignments -->
    <div class="dashboard-item item-3">
        <div class="item-header">
            <h3>Pending Work</h3>
            <div class="item-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
        <ul class="activity-list">
            @php
                $recentAssignments = $student->classrooms->flatMap->assignments->sortByDesc('created_at')->take(4);
            @endphp
            @forelse($recentAssignments as $assignment)
            <li class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="activity-details">
                    <h4>{{ Str::limit($assignment->title, 30) }}</h4>
                    <p>{{ $assignment->classroom->class_name }}</p>
                </div>
            </li>
            @empty
            <li style="text-align: center; padding: 20px; color: #666;">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--light-green); margin-bottom: 10px; display: block;"></i>
                All caught up!
            </li>
            @endforelse
        </ul>
    </div>

    <!-- Practice Now -->
    <div class="dashboard-item item-4">
        <div class="practice-card">
            <div class="practice-icon">
                <i class="fas fa-microphone-alt"></i>
            </div>
            <h3>Ready to Practice?</h3>
            <p>Start a practice session with AI-powered Tajweed analysis to improve your recitation.</p>
            <a href="{{ url('/student/practice') }}" class="start-btn">Start Practice</a>
        </div>
    </div>

    <!-- Learning Materials -->
    <div class="dashboard-item item-5">
        <div class="item-header">
            <h3>Learning Materials</h3>
            <div class="item-icon">
                <i class="fas fa-book-open"></i>
            </div>
        </div>
        <div class="materials-grid">
            <a href="{{ url('/student/materials') }}" class="material-item">
                <div class="material-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="material-details">
                    <h4>Tajweed Rules Guide</h4>
                    <p>Complete reference</p>
                </div>
            </a>
            <a href="{{ url('/student/materials') }}" class="material-item">
                <div class="material-icon">
                    <i class="fas fa-headphones"></i>
                </div>
                <div class="material-details">
                    <h4>Audio Examples</h4>
                    <p>Recitations by Qaris</p>
                </div>
            </a>
            <a href="{{ url('/student/materials') }}" class="material-item">
                <div class="material-icon">
                    <i class="fas fa-video"></i>
                </div>
                <div class="material-details">
                    <h4>Video Tutorials</h4>
                    <p>Makharij demos</p>
                </div>
            </a>
            <a href="{{ url('/student/materials') }}" class="material-item">
                <div class="material-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="material-details">
                    <h4>AI Analysis</h4>
                    <p>Tajweed detection</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="dashboard-item item-6">
        <div class="item-header">
            <h3>Your Statistics</h3>
            <div class="item-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
        </div>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-item-value">{{ $enrolledClassesCount }}</div>
                <div class="stat-item-label">Enrolled Classes</div>
            </div>
            <div class="stat-item">
                <div class="stat-item-value">{{ $completedAssignments }}</div>
                <div class="stat-item-label">Completed</div>
            </div>
            <div class="stat-item">
                <div class="stat-item-value">{{ $pendingAssignments }}</div>
                <div class="stat-item-label">Pending</div>
            </div>
            <div class="stat-item">
                <div class="stat-item-value">{{ number_format($averageScore, 0) }}%</div>
                <div class="stat-item-label">Avg Score</div>
            </div>
        </div>
        <div class="goal-card">
            <h4><i class="fas fa-bullseye"></i> Tajweed Mastery System</h4>
            <p>Our AI analyzes your recitation for Makharij (Pronunciation Points), Ghunnah (Nasal Sound), Idgham (Merging), Qalqalah (Echo Sound), Madd (Elongation), and other Tajweed rules using advanced speech recognition.</p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ min($averageScore, 100) }}%;"></div>
            </div>
            <p style="margin-top: 10px; text-align: center; font-weight: 600; color: var(--primary-green);">
                Keep practicing to improve your Tajweed accuracy!
            </p>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    // Animate progress ring on page load
    document.addEventListener('DOMContentLoaded', function() {
        const progressRing = document.getElementById('main-progress-ring');
        if (progressRing) {
            const progressValue = {{ number_format($averageScore, 0) }};
            const circumference = 2 * Math.PI * 50;
            const offset = circumference - (progressValue / 100) * circumference;
            
            setTimeout(() => {
                progressRing.style.strokeDashoffset = offset;
            }, 500);
        }
    });
</script>
@endsection
