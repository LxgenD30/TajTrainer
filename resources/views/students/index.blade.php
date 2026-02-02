@extends('layouts.dashboard')

@section('title', 'Student Dashboard')
@section('user-role', 'Student • ' . number_format($averageScore, 0) . '% Avg Score')

@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="nav-item active">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ route('student.classes') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="nav-label">My Classes</div>
    </a>
    
    <a href="{{ route('student.practice') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-microphone-alt"></i>
        </div>
        <div class="nav-label">Practice</div>
    </a>
    
    <a href="{{ route('student.progress') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="nav-label">My Progress</div>
    </a>
    
    <a href="{{ route('student.materials') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="nav-label">Materials</div>
    </a>
@endsection

@section('extra-styles')
<style>
    :root {
        --turquoise: #1abc9c;
        --coral: #e74c3c;
        --purple: #9b59b6;
        --sky-blue: #3498db;
        --sunflower: #f1c40f;
        --orange: #e67e22;
    }
    
    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--turquoise) 100%);
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 40px;
        color: var(--white);
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
    }
    
    .welcome-banner:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.3;
    }
    
    .welcome-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
    }
    
    .welcome-content h1 {
        color: var(--white) !important;
        font-size: 2.5rem;
        margin-bottom: 15px;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    
    .welcome-content p {
        font-size: 1.2rem;
        opacity: 0.95;
        margin-bottom: 20px;
    }
    
    .stats-container {
        display: flex;
        gap: 20px;
        margin-top: 30px;
        flex-wrap: wrap;
    }
    
    .stat-item-banner {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        padding: 15px 20px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        min-width: 150px;
    }
    
    .stat-value-banner {
        font-size: 2rem;
        font-weight: bold;
        color: var(--gold);
        line-height: 1;
    }
    
    .stat-label-banner {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-top: 5px;
    }
    
    /* Dashboard Grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }
    
    .dashboard-card {
        border-radius: 20px;
        padding: 30px;
        background-color: var(--white);
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    .dashboard-card:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 20px 40px rgba(10, 92, 54, 0.2);
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        position: relative;
        z-index: 2;
    }
    
    .card-header h3 {
        font-size: 1.4rem;
        color: var(--dark-green);
    }
    
    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: var(--white);
    }
    
    /* Color variations for cards */
    .card-1 .card-icon { background: linear-gradient(135deg, var(--primary-green), var(--turquoise)); }
    .card-2 .card-icon { background: linear-gradient(135deg, var(--sky-blue), var(--purple)); }
    .card-3 .card-icon { background: linear-gradient(135deg, var(--coral), var(--orange)); }
    .card-4 .card-icon { background: linear-gradient(135deg, var(--sunflower), var(--gold)); }
    .card-5 .card-icon { background: linear-gradient(135deg, var(--purple), #8e44ad); }
    .card-6 .card-icon { background: linear-gradient(135deg, var(--turquoise), #16a085); }
    
    /* Progress Chart */
    .progress-chart {
        height: 120px;
        margin: 25px 0;
        position: relative;
    }
    
    .chart-bar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        height: 100%;
        gap: 10px;
    }
    
    .bar-item {
        flex: 1;
        background: linear-gradient(to top, var(--primary-green), var(--light-green));
        border-radius: 8px 8px 0 0;
        position: relative;
        transition: all 0.3s ease;
        min-height: 10px;
    }
    
    .bar-item:hover {
        opacity: 0.8;
        transform: scaleY(1.05);
    }
    
    .bar-label {
        position: absolute;
        bottom: -25px;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 0.8rem;
        color: #666;
    }
    
    .bar-value {
        position: absolute;
        top: -25px;
        left: 0;
        right: 0;
        text-align: center;
        font-weight: bold;
        color: var(--primary-green);
    }
    
    /* Activity Feed */
    .activity-feed {
        list-style: none;
    }
    
    .activity-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid rgba(10, 92, 54, 0.1);
        position: relative;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: var(--white);
        font-size: 1.2rem;
    }
    
    .activity-item:nth-child(1) .activity-icon { background-color: var(--turquoise); }
    .activity-item:nth-child(2) .activity-icon { background-color: var(--sky-blue); }
    .activity-item:nth-child(3) .activity-icon { background-color: var(--coral); }
    .activity-item:nth-child(4) .activity-icon { background-color: var(--purple); }
    
    .activity-details h4 {
        font-size: 1rem;
        margin-bottom: 5px;
        color: var(--dark-green);
    }
    
    .activity-details p {
        font-size: 0.9rem;
        color: #666;
    }
    
    .activity-time {
        position: absolute;
        right: 0;
        top: 15px;
        font-size: 0.8rem;
        color: #999;
    }
    
    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 20px;
    }
    
    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px 10px;
        border-radius: 15px;
        background: rgba(10, 92, 54, 0.05);
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
        text-decoration: none;
    }
    
    .action-btn:hover {
        background: rgba(10, 92, 54, 0.1);
        border-color: var(--primary-green);
        transform: translateY(-5px);
    }
    
    .action-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--white);
        margin-bottom: 10px;
    }
    
    .action-1 .action-icon { background: linear-gradient(135deg, var(--primary-green), var(--light-green)); }
    .action-2 .action-icon { background: linear-gradient(135deg, var(--sky-blue), #2980b9); }
    .action-3 .action-icon { background: linear-gradient(135deg, var(--coral), #c0392b); }
    .action-4 .action-icon { background: linear-gradient(135deg, var(--purple), #8e44ad); }
    
    .action-label {
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--dark-green);
        text-align: center;
    }
    
    /* Class List in Card */
    .class-list-card {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .class-item-card {
        display: flex;
        align-items: center;
        padding: 15px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        border-left: 4px solid var(--primary-green);
    }
    
    .class-item-card:hover {
        background: rgba(10, 92, 54, 0.1);
        transform: translateX(5px);
    }
    
    .class-icon-card {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 1.5rem;
        margin-right: 15px;
    }
    
    .class-details-card h4 {
        font-size: 1.1rem;
        margin-bottom: 5px;
        color: var(--dark-green);
    }
    
    .class-details-card p {
        font-size: 0.9rem;
        color: #666;
    }
    
    /* Materials Grid */
    .materials-grid-dash {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 20px;
    }
    
    .material-item-dash {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
    }
    
    .material-item-dash:hover {
        background: rgba(10, 92, 54, 0.1);
        transform: translateY(-3px);
    }
    
    .material-icon-dash {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: var(--white);
    }
    
    .material-icon-dash.pdf { background: linear-gradient(135deg, #e74c3c, #c0392b); }
    .material-icon-dash.audio { background: linear-gradient(135deg, #3498db, #2980b9); }
    .material-icon-dash.video { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
    .material-icon-dash.ai { background: linear-gradient(135deg, #1abc9c, #16a085); }
    
    .material-details-dash h4 {
        font-size: 0.95rem;
        color: var(--dark-green);
        margin-bottom: 3px;
    }
    
    .material-details-dash p {
        font-size: 0.8rem;
        color: #666;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .welcome-content h1 {
            font-size: 2rem;
        }
        
        .stats-container {
            justify-content: center;
        }
        
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
        
        .materials-grid-dash {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Welcome Banner -->
<section class="welcome-banner">
    <div class="welcome-content">
        <h1>Welcome back, {{ Auth::user()->name }}!</h1>
        <p>Your dedication to perfecting Quranic recitation is inspiring. Continue your journey with today's lessons.</p>
        
        <div class="stats-container">
            <div class="stat-item-banner">
                <div class="stat-value-banner">{{ $enrolledClassesCount }}</div>
                <div class="stat-label-banner">Enrolled Classes</div>
            </div>
            <div class="stat-item-banner">
                <div class="stat-value-banner">{{ number_format($averageScore, 0) }}%</div>
                <div class="stat-label-banner">Avg Score</div>
            </div>
            <div class="stat-item-banner">
                <div class="stat-value-banner">{{ $completedAssignments }}</div>
                <div class="stat-label-banner">Completed</div>
            </div>
            <div class="stat-item-banner">
                <div class="stat-value-banner">{{ $pendingAssignments }}</div>
                <div class="stat-label-banner">Pending Work</div>
            </div>
        </div>
    </div>
</section>

<!-- Dashboard Grid -->
<div class="dashboard-grid">
    <!-- Progress Overview -->
    <div class="dashboard-card card-1">
        <div class="card-header">
            <h3>Progress Overview</h3>
            <div class="card-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        
        <div class="progress-chart">
            <div class="chart-bar">
                <div class="bar-item" style="height: {{ min(85, $averageScore + 10) }}%;">
                    <div class="bar-value">{{ min(85, $averageScore + 10) }}%</div>
                    <div class="bar-label">Makharij</div>
                </div>
                <div class="bar-item" style="height: {{ min(78, $averageScore + 5) }}%;">
                    <div class="bar-value">{{ min(78, $averageScore + 5) }}%</div>
                    <div class="bar-label">Ghunnah</div>
                </div>
                <div class="bar-item" style="height: {{ min($averageScore, 100) }}%;">
                    <div class="bar-value">{{ min($averageScore, 100) }}%</div>
                    <div class="bar-label">Idgham</div>
                </div>
                <div class="bar-item" style="height: {{ max(60, $averageScore - 15) }}%;">
                    <div class="bar-value">{{ max(60, $averageScore - 15) }}%</div>
                    <div class="bar-label">Qalqalah</div>
                </div>
                <div class="bar-item" style="height: {{ max(70, $averageScore - 10) }}%;">
                    <div class="bar-value">{{ max(70, $averageScore - 10) }}%</div>
                    <div class="bar-label">Madd</div>
                </div>
            </div>
        </div>
        
        <p style="font-size: 0.9rem; color: #666; text-align: center;">Focus on <strong>Qalqalah</strong> to improve your overall score. Complete 3 practice sessions this week.</p>
    </div>

    <!-- Recent Activity -->
    <div class="dashboard-card card-2">
        <div class="card-header">
            <h3>Recent Activity</h3>
            <div class="card-icon">
                <i class="fas fa-history"></i>
            </div>
        </div>
        
        <ul class="activity-feed">
            @if($completedAssignments > 0)
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="activity-details">
                        <h4>Assignment Completed</h4>
                        <p>You've completed {{ $completedAssignments }} {{ Str::plural('assignment', $completedAssignments) }}</p>
                    </div>
                    <div class="activity-time">Recent</div>
                </li>
            @endif
            
            @if($enrolledClassesCount > 0)
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="activity-details">
                        <h4>Classes Enrolled</h4>
                        <p>{{ $enrolledClassesCount }} active {{ Str::plural('class', $enrolledClassesCount) }} with teachers</p>
                    </div>
                    <div class="activity-time">Active</div>
                </li>
            @endif
            
            <li class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-microphone-alt"></i>
                </div>
                <div class="activity-details">
                    <h4>Practice Available</h4>
                    <p>AI-powered Tajweed analysis ready</p>
                </div>
                <div class="activity-time">Now</div>
            </li>
            
            <li class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="activity-details">
                    <h4>Learning Materials</h4>
                    <p>Access guides, audio, and video tutorials</p>
                </div>
                <div class="activity-time">Available</div>
            </li>
        </ul>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-card card-3">
        <div class="card-header">
            <h3>Quick Actions</h3>
            <div class="card-icon">
                <i class="fas fa-bolt"></i>
            </div>
        </div>
        
        <div class="quick-actions">
            <a href="{{ route('student.practice') }}" class="action-btn action-1">
                <div class="action-icon">
                    <i class="fas fa-microphone-alt"></i>
                </div>
                <div class="action-label">Practice Now</div>
            </a>
            <a href="{{ route('student.classes') }}" class="action-btn action-2">
                <div class="action-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="action-label">My Classes</div>
            </a>
            <a href="{{ route('student.progress') }}" class="action-btn action-3">
                <div class="action-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="action-label">View Progress</div>
            </a>
            <a href="{{ route('student.materials') }}" class="action-btn action-4">
                <div class="action-icon">
                    <i class="fas fa-download"></i>
                </div>
                <div class="action-label">Materials</div>
            </a>
        </div>
        
        <p style="margin-top: 25px; font-size: 0.9rem; color: #666; text-align: center;">
            Complete 2 actions today to improve your Tajweed skills
        </p>
    </div>

    <!-- My Enrolled Classes -->
    <div class="dashboard-card card-4">
        <div class="card-header">
            <h3>My Classes</h3>
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        
        <div class="class-list-card">
            @forelse($student->classrooms->sortByDesc('pivot.date_joined')->take(3) as $classroom)
                <a href="{{ url('/student/classroom/' . $classroom->id) }}" class="class-item-card">
                    <div class="class-icon-card">
                        <i class="fas fa-book-quran"></i>
                    </div>
                    <div class="class-details-card">
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
                    <a href="{{ route('student.classes') }}" class="action-btn action-1" style="margin-top: 15px; display: inline-flex;">
                        <div class="action-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="action-label">Enroll Now</div>
                    </a>
                </div>
            @endforelse
            
            @if($student->classrooms->count() > 3)
                <a href="{{ route('student.classes') }}" style="text-align: center; padding: 15px; color: var(--primary-green); text-decoration: none; font-weight: 600;">
                    View All {{ $student->classrooms->count() }} Classes <i class="fas fa-arrow-right"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- Learning Materials -->
    <div class="dashboard-card card-5">
        <div class="card-header">
            <h3>Learning Materials</h3>
            <div class="card-icon">
                <i class="fas fa-book-open"></i>
            </div>
        </div>
        
        <div class="materials-grid-dash">
            <a href="{{ route('student.materials') }}" class="material-item-dash">
                <div class="material-icon-dash pdf">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="material-details-dash">
                    <h4>Tajweed Guide</h4>
                    <p>Complete reference</p>
                </div>
            </a>
            <a href="{{ route('student.materials') }}" class="material-item-dash">
                <div class="material-icon-dash audio">
                    <i class="fas fa-headphones"></i>
                </div>
                <div class="material-details-dash">
                    <h4>Audio Examples</h4>
                    <p>Recitations by Qaris</p>
                </div>
            </a>
            <a href="{{ route('student.materials') }}" class="material-item-dash">
                <div class="material-icon-dash video">
                    <i class="fas fa-video"></i>
                </div>
                <div class="material-details-dash">
                    <h4>Video Tutorials</h4>
                    <p>Makharij demos</p>
                </div>
            </a>
            <a href="{{ route('student.materials') }}" class="material-item-dash">
                <div class="material-icon-dash ai">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="material-details-dash">
                    <h4>AI Analysis</h4>
                    <p>Tajweed detection</p>
                </div>
            </a>
        </div>
        
        <p style="margin-top: 20px; font-size: 0.9rem; color: #666; text-align: center;">
            Access comprehensive learning resources to master Tajweed
        </p>
    </div>

    <!-- Pending Assignments -->
    <div class="dashboard-card card-6">
        <div class="card-header">
            <h3>Today's Focus</h3>
            <div class="card-icon">
                <i class="fas fa-trophy"></i>
            </div>
        </div>
        
        <div style="text-align: center; padding: 20px 0;">
            <div style="font-size: 3rem; color: var(--gold); margin-bottom: 10px;">
                <i class="fas fa-star"></i>
            </div>
            <h3 style="margin-bottom: 10px;">Continue Your Journey</h3>
            <p style="color: #666; margin-bottom: 20px;">
                @if($pendingAssignments > 0)
                    You have {{ $pendingAssignments }} pending {{ Str::plural('assignment', $pendingAssignments) }} to complete
                @else
                    Practice your Tajweed skills today
                @endif
            </p>
            
            <div style="background: rgba(10, 92, 54, 0.05); border-radius: 15px; padding: 15px; margin: 20px 0;">
                <div style="font-family: 'Amiri', serif; font-size: 1.5rem; text-align: right; color: var(--primary-green); margin-bottom: 10px;">
                    بِسْمِ ٱللَّهِ ٱلرَّحْمَـٰنِ ٱلرَّحِيمِ
                </div>
                <p style="font-size: 0.9rem; color: #666;">
                    @if($averageScore >= 80)
                        Excellent progress! Keep up the great work.
                    @elseif($averageScore >= 60)
                        Good work! Continue practicing to improve.
                    @else
                        Start your practice journey today!
                    @endif
                </p>
            </div>
            
            <a href="{{ route('student.practice') }}" class="action-btn action-1" style="width: 100%; max-width: 300px; margin: 0 auto; display: inline-flex;">
                <div class="action-icon">
                    <i class="fas fa-microphone-alt"></i>
                </div>
                <div class="action-label">Start Practice Session</div>
            </a>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    // Animate progress bars on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Animate chart bars
        const bars = document.querySelectorAll('.bar-item');
        bars.forEach((bar, index) => {
            // Set initial height to 0
            const originalHeight = bar.style.height;
            bar.style.height = '0%';
            
            // Animate after a delay
            setTimeout(() => {
                bar.style.transition = 'height 1.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
                bar.style.height = originalHeight;
            }, 300 + (index * 200));
        });
    });
</script>
@endsection
