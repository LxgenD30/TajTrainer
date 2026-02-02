@extends('layouts.dashboard')

@section('title', 'Teacher Dashboard')
@section('user-role', 'Teacher • ' . $stats['total_classes'] . ' Classes')

@section('navigation')
    <a href="{{ route('home') }}" class="nav-item active">
        <i class="fas fa-home nav-icon"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    
    <a href="{{ route('classroom.index') }}" class="nav-item">
        <i class="fas fa-chalkboard-teacher nav-icon"></i>
        <span class="nav-label">My Classes</span>
    </a>
    
    <a href="{{ route('materials.index') }}" class="nav-item">
        <i class="fas fa-book-open nav-icon"></i>
        <span class="nav-label">Materials</span>
    </a>
    
    <a href="{{ route('teachers.show', Auth::id()) }}" class="nav-item">
        <i class="fas fa-user-circle nav-icon"></i>
        <span class="nav-label">Profile</span>
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
        font-size: 1.1rem;
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
        text-align: center;
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
    
    .section-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .section-title {
        font-size: 1.5rem;
        color: #0a5c36;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .submission-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        border-left: 4px solid #0a5c36;
        margin-bottom: 12px;
        transition: all 0.3s ease;
    }
    
    .submission-item:hover {
        background: rgba(10, 92, 54, 0.1);
        transform: translateX(5px);
    }
    
    .submission-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .submission-details {
        flex: 1;
    }
    
    .submission-details h4 {
        color: #0a5c36;
        font-weight: 700;
        margin-bottom: 5px;
        font-size: 1.05rem;
    }
    
    .submission-details p {
        color: #666;
        font-size: 0.85rem;
        margin: 0;
    }
    
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 25px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 15px;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .action-btn:hover {
        background: rgba(10, 92, 54, 0.1);
        transform: translateY(-3px);
        border-color: #0a5c36;
    }
    
    .action-btn i {
        font-size: 2.5rem;
        color: #0a5c36;
        margin-bottom: 15px;
    }
    
    .action-btn h4 {
        color: #0a5c36;
        font-weight: 700;
        margin-bottom: 5px;
        font-size: 1.1rem;
    }
    
    .action-btn p {
        color: #666;
        font-size: 0.85rem;
        margin: 0;
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
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    
    .info-box {
        background-color: rgba(10, 92, 54, 0.05);
        padding: 15px;
        border-radius: 10px;
        text-align: center;
    }
    
    .info-box strong {
        color: #0a5c36;
        display: block;
        margin-bottom: 5px;
    }
</style>

<!-- Welcome Banner -->
<section class="welcome-banner">
    <div class="welcome-content">
        <h1>Welcome, {{ explode(' ', Auth::user()->name)[0] }}! 👨‍🏫</h1>
        <p>Manage your Tajweed classes, track student progress, and help shape the next generation of Quran reciters.</p>
    </div>
</section>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🏫</div>
        <div class="stat-value">{{ $stats['total_classes'] }}</div>
        <div class="stat-label">Total Classes</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">👨‍🎓</div>
        <div class="stat-value">{{ $stats['total_students'] }}</div>
        <div class="stat-label">Total Students</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-value">{{ $stats['total_assignments'] }}</div>
        <div class="stat-label">Assignments</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-value">{{ $stats['total_materials'] }}</div>
        <div class="stat-label">Materials</div>
    </div>
</div>

<!-- Grading Queue Section -->
<div class="section-card">
    <h2 class="section-title">
        <i class="fas fa-clipboard-check"></i> Grading Queue
    </h2>
    
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
        <div class="submission-item">
            <div class="submission-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="submission-details">
                <h4>{{ $submission->student->name }}</h4>
                <p>
                    <i class="fas fa-file-alt"></i> {{ Str::limit($submission->assignment->title, 40) }} • 
                    <i class="fas fa-clock"></i> {{ $submission->created_at->diffForHumans() }}
                </p>
            </div>
            <a href="{{ route('teacher.submission.grade', $submission->id) }}" style="display: inline-flex; align-items: center; gap: 8px; background: #0a5c36; color: white; padding: 10px 20px; border-radius: 25px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-check"></i> Grade
            </a>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <p>All caught up with grading! ✨</p>
        </div>
    @endforelse
</div>

<!-- Quick Actions Section -->
<div class="section-card">
    <h2 class="section-title">
        <i class="fas fa-bolt"></i> Quick Actions
    </h2>
    
    <div class="quick-actions-grid">
        <a href="{{ route('classroom.create') }}" class="action-btn">
            <i class="fas fa-plus-circle"></i>
            <h4>New Class</h4>
            <p>Create a new classroom</p>
        </a>
        
        <a href="{{ route('classroom.index') }}" class="action-btn">
            <i class="fas fa-chalkboard-teacher"></i>
            <h4>View Classes</h4>
            <p>Manage your classrooms</p>
        </a>
        
        <a href="{{ route('materials.create') }}" class="action-btn">
            <i class="fas fa-file-upload"></i>
            <h4>Upload Material</h4>
            <p>Add learning resources</p>
        </a>
        
        <a href="{{ route('materials.index') }}" class="action-btn">
            <i class="fas fa-book-reader"></i>
            <h4>View Materials</h4>
            <p>Browse all materials</p>
        </a>
    </div>
</div>

<!-- Tajweed System Info -->
<div class="section-card">
    <h2 class="section-title">
        <i class="fas fa-brain"></i> AI-Powered Tajweed Analysis System
    </h2>
    <p style="font-size: 1.05rem; line-height: 1.8; color: #555;">
        TajTrainer uses advanced AI technology to analyze student recitations for Tajweed accuracy. Our system detects:
    </p>
    <div class="info-grid">
        <div class="info-box">
            <strong>Makharij</strong>
            Pronunciation points
        </div>
        <div class="info-box">
            <strong>Ghunnah</strong>
            Nasal sounds
        </div>
        <div class="info-box">
            <strong>Idgham</strong>
            Letter merging
        </div>
        <div class="info-box">
            <strong>Qalqalah</strong>
            Echo sound
        </div>
        <div class="info-box">
            <strong>Madd</strong>
            Elongation rules
        </div>
        <div class="info-box">
            <strong>Ikhfa</strong>
            Concealment
        </div>
    </div>
    <p style="margin-top: 20px; font-size: 0.95rem; color: #777; font-style: italic;">
        All submissions are automatically analyzed, providing detailed feedback to help students perfect their recitation.
    </p>
</div>
@endsection
