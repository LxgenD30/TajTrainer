@extends('layouts.dashboard')

@section('title', 'Teacher Dashboard')
@section('user-role', 'Teacher • ' . $stats['total_classes'] . ' Classes')

@section('navigation')
    <a href="{{ route('home') }}" class="nav-item active">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ route('classroom.index') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="nav-label">My Classes</div>
    </a>
    
    <a href="{{ route('materials.index') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="nav-label">Materials</div>
    </a>
    
    <a href="#" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="nav-label">Analytics</div>
    </a>
    
    <a href="{{ route('teachers.show', Auth::id()) }}" class="nav-item">
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
        grid-template-rows: repeat(4, auto);
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
        grid-column: 1 / 4;
        grid-row: 1 / 2;
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(46, 139, 87, 0.05));
    }
    
    .item-2 {
        grid-column: 4 / 7;
        grid-row: 1 / 2;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), rgba(255, 215, 0, 0.05));
    }
    
    .item-3 {
        grid-column: 7 / 10;
        grid-row: 1 / 2;
        background: linear-gradient(135deg, rgba(6, 78, 50, 0.05), rgba(10, 92, 54, 0.05));
    }
    
    .item-4 {
        grid-column: 10 / 13;
        grid-row: 1 / 2;
        background: linear-gradient(135deg, rgba(46, 139, 87, 0.05), rgba(86, 179, 132, 0.05));
    }
    
    .item-5 {
        grid-column: 1 / 9;
        grid-row: 2 / 4;
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.03), rgba(6, 78, 50, 0.03));
    }
    
    .item-6 {
        grid-column: 9 / 13;
        grid-row: 2 / 4;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), rgba(255, 215, 0, 0.05));
    }
    
    .item-7 {
        grid-column: 1 / 13;
        grid-row: 4 / 5;
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(46, 139, 87, 0.05));
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
    
    .stat-card {
        text-align: center;
    }
    
    .stat-value {
        font-size: 3rem;
        font-weight: bold;
        color: var(--primary-green);
        line-height: 1;
        margin-bottom: 10px;
    }
    
    .stat-label {
        font-size: 1rem;
        color: #666;
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
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background-color: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        background-color: rgba(10, 92, 54, 0.1);
        transform: translateX(5px);
    }
    
    .action-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-green);
        color: var(--white);
        font-size: 1.3rem;
    }
    
    .action-details h4 {
        font-size: 1.1rem;
        margin-bottom: 5px;
        color: var(--dark-green);
    }
    
    .action-details p {
        font-size: 0.85rem;
        color: #666;
    }
    
    @media (max-width: 1200px) {
        .dashboard-grid {
            grid-template-columns: repeat(6, 1fr);
        }
        
        .item-1, .item-2 { grid-column: span 3; }
        .item-3, .item-4 { grid-column: span 3; }
        .item-5 { grid-column: 1 / 7; }
        .item-6 { grid-column: 1 / 7; }
        .item-7 { grid-column: 1 / 7; }
    }
    
    @media (max-width: 768px) {
        .welcome-content h1 {
            font-size: 2rem;
        }
        
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        
        .item-1, .item-2, .item-3, .item-4, .item-5, .item-6, .item-7 {
            grid-column: 1;
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Welcome Section -->
<section class="welcome-section">
    <div class="welcome-content">
        <h1>Welcome, {{ explode(' ', Auth::user()->name)[0] }}!</h1>
        <p>Manage your Tajweed classes, track student progress, and help shape the next generation of Quran reciters.</p>
        <div class="streak-counter">
            <i class="fas fa-chalkboard-teacher streak-icon"></i>
            <span>{{ $stats['total_classes'] }} Active {{ Str::plural('Class', $stats['total_classes']) }}</span>
        </div>
    </div>
</section>

<!-- Dashboard Grid -->
<div class="dashboard-grid">
    <!-- Total Classes -->
    <div class="dashboard-item item-1">
        <div class="stat-card">
            <div class="item-icon" style="margin: 0 auto 15px;">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div class="stat-value">{{ $stats['total_classes'] }}</div>
            <div class="stat-label">Total Classes</div>
        </div>
    </div>

    <!-- Total Students -->
    <div class="dashboard-item item-2">
        <div class="stat-card">
            <div class="item-icon" style="margin: 0 auto 15px;">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-value">{{ $stats['total_students'] }}</div>
            <div class="stat-label">Total Students</div>
        </div>
    </div>

    <!-- Total Assignments -->
    <div class="dashboard-item item-3">
        <div class="stat-card">
            <div class="item-icon" style="margin: 0 auto 15px;">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-value">{{ $stats['total_assignments'] }}</div>
            <div class="stat-label">Assignments</div>
        </div>
    </div>

    <!-- Total Materials -->
    <div class="dashboard-item item-4">
        <div class="stat-card">
            <div class="item-icon" style="margin: 0 auto 15px;">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-value">{{ $stats['total_materials'] }}</div>
            <div class="stat-label">Materials</div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="dashboard-item item-5">
        <div class="item-header">
            <h3>Grading Queue</h3>
            <div class="item-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
        </div>
        <ul class="activity-list">
            @php
                $recentSubmissions = \App\Models\AssignmentSubmission::whereHas('assignment.classroom', function($q) {
                    $q->where('teacher_id', Auth::id());
                })
                ->where('status', 'submitted')
                ->with('student', 'assignment')
                ->latest()
                ->take(5)
                ->get();
            @endphp
            
            @forelse($recentSubmissions as $submission)
            <li class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="activity-details">
                    <h4>{{ $submission->student->name }}</h4>
                    <p>{{ Str::limit($submission->assignment->title, 40) }}</p>
                </div>
            </li>
            @empty
            <li style="text-align: center; padding: 20px; color: #666;">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--light-green); margin-bottom: 10px; display: block;"></i>
                All caught up with grading!
            </li>
            @endforelse
        </ul>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-item item-6">
        <div class="item-header">
            <h3>Quick Actions</h3>
            <div class="item-icon">
                <i class="fas fa-bolt"></i>
            </div>
        </div>
        <div class="quick-actions">
            <a href="{{ route('classroom.create') }}" class="action-btn">
                <div class="action-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="action-details">
                    <h4>New Class</h4>
                    <p>Create a new classroom</p>
                </div>
            </a>
            <a href="{{ route('classroom.index') }}" class="action-btn">
                <div class="action-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="action-details">
                    <h4>View Classes</h4>
                    <p>Manage your classrooms</p>
                </div>
            </a>
            <a href="{{ route('materials.create') }}" class="action-btn">
                <div class="action-icon">
                    <i class="fas fa-file-upload"></i>
                </div>
                <div class="action-details">
                    <h4>Upload Material</h4>
                    <p>Add learning resources</p>
                </div>
            </a>
            <a href="{{ route('classroom.index') }}" class="action-btn">
                <div class="action-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="action-details">
                    <h4>View Analytics</h4>
                    <p>Track student progress</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Tajweed System Info -->
    <div class="dashboard-item item-7">
        <div class="item-header">
            <h3><i class="fas fa-brain"></i> AI-Powered Tajweed Analysis System</h3>
            <div class="item-icon">
                <i class="fas fa-microphone"></i>
            </div>
        </div>
        <p style="font-size: 1.05rem; line-height: 1.8; color: #555;">
            TajTrainer uses advanced AI technology to analyze student recitations for Tajweed accuracy. Our system detects:
        </p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
            <div style="background-color: rgba(10, 92, 54, 0.05); padding: 15px; border-radius: 10px;">
                <strong style="color: var(--primary-green);">Makharij</strong> - Pronunciation points
            </div>
            <div style="background-color: rgba(10, 92, 54, 0.05); padding: 15px; border-radius: 10px;">
                <strong style="color: var(--primary-green);">Ghunnah</strong> - Nasal sounds
            </div>
            <div style="background-color: rgba(10, 92, 54, 0.05); padding: 15px; border-radius: 10px;">
                <strong style="color: var(--primary-green);">Idgham</strong> - Letter merging
            </div>
            <div style="background-color: rgba(10, 92, 54, 0.05); padding: 15px; border-radius: 10px;">
                <strong style="color: var(--primary-green);">Qalqalah</strong> - Echo sound
            </div>
            <div style="background-color: rgba(10, 92, 54, 0.05); padding: 15px; border-radius: 10px;">
                <strong style="color: var(--primary-green);">Madd</strong> - Elongation rules
            </div>
            <div style="background-color: rgba(10, 92, 54, 0.05); padding: 15px; border-radius: 10px;">
                <strong style="color: var(--primary-green);">Ikhfa</strong> - Concealment
            </div>
        </div>
        <p style="margin-top: 20px; font-size: 0.95rem; color: #777; font-style: italic;">
            All submissions are automatically analyzed, providing detailed feedback to help students perfect their recitation.
        </p>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    // Any additional teacher-specific scripts
</script>
@endsection
