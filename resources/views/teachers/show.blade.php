@extends('layouts.dashboard')

@section('title', 'Teacher Profile')
@section('user-role', 'Teacher • Profile')

@section('navigation')
    <a href="{{ route('home') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="{{ route('classroom.index') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <a href="{{ route('students.list') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-user-graduate"></i></div>
        <div class="nav-label">My Students</div>
    </a>
    <a href="{{ route('materials.index') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
        <div class="nav-label">Materials</div>
    </a>
@endsection

@section('content')
<style>
    /* High Contrast Card Styles */
    .profile-banner {
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
    
    .profile-banner:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .profile-header {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
    }
    
    .profile-info-left {
        display: flex;
        align-items: center;
        gap: 30px;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        font-weight: 800;
        color: #0a5c36;
        border: 4px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }
    
    .profile-info h1 {
        font-size: 2rem;
        margin-bottom: 10px;
        font-weight: 700;
        color: #ffffff;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .profile-info p {
        font-size: 1.05rem;
        opacity: 0.95;
        line-height: 1.6;
        margin: 5px 0;
    }
    
    .profile-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 215, 0, 0.2);
        color: #ffd700;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 700;
        border: 2px solid rgba(255, 215, 0, 0.3);
        margin-top: 10px;
    }
    
    .edit-profile-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: white;
        color: #0a5c36;
        padding: 12px 24px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    
    .edit-profile-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .info-card h3 {
        color: #000;
        font-weight: 800;
        margin-bottom: 20px;
        font-size: 1.6rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        background: #f9f9f9;
        border-radius: 12px;
        margin-bottom: 12px;
        border: 2px solid #2a2a2a;
    }
    
    .info-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        border: 2px solid #2a2a2a;
    }
    
    .info-content {
        flex: 1;
    }
    
    .info-label {
        font-size: 1.05rem;
        color: #666;
        margin-bottom: 3px;
        font-weight: 600;
    }
    
    .info-value {
        font-weight: 700;
        color: #000;
        font-size: 1.2rem;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 5px 5px 0 #2a2a2a;
    }
    
    .stat-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        display: block;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: #000;
        line-height: 1;
        margin-bottom: 8px;
    }
    
    .stat-label {
        color: #666;
        font-size: 1.05rem;
        font-weight: 700;
    }
    
    .section-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .section-title {
        font-size: 1.6rem;
        color: #000;
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .class-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: #f9f9f9;
        border-radius: 12px;
        border: 2px solid #2a2a2a;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
    }
    
    .class-item:hover {
        background: #fff;
        transform: translateX(5px);
        box-shadow: 5px 5px 0 #2a2a2a;
    }
    
    .class-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        border: 2px solid #2a2a2a;
    }
    
    .class-details {
        flex: 1;
    }
    
    .class-details h4 {
        color: #000;
        font-weight: 800;
        margin-bottom: 5px;
        font-size: 1.2rem;
    }
    
    .class-details p {
        color: #666;
        font-size: 1.05rem;
        margin: 0;
        font-weight: 600;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #000;
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
        display: block;
    }
    
    .biodata-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .biodata-card h3 {
        color: #000;
        font-weight: 800;
        margin-bottom: 15px;
        font-size: 1.6rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .biodata-card p {
        color: #222;
        font-size: 1.05rem;
        line-height: 1.6;
        font-weight: 600;
        margin: 0;
    }
    
    @if(session('success'))
        .success-message {
            background: #d4edda;
            border: 3px solid #28a745;
            color: #155724;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 700;
            font-size: 1.05rem;
        }
    @endif
</style>

@if(session('success'))
    <div class="success-message">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<!-- Profile Banner -->
<section class="profile-banner">
    <div class="profile-header">
        <div class="profile-info-left">
            <div class="profile-avatar">
                {{ substr($teacher->name, 0, 1) }}
            </div>
            
            <div class="profile-info">
                <h1>{{ $teacher->name }}</h1>
                @if($teacher->title)
                    <p style="font-weight: 700; font-size: 1.1rem;"><i class="fas fa-award"></i> {{ $teacher->title }}</p>
                @endif
                <p><i class="fas fa-envelope"></i> {{ $teacher->user->email }}</p>
                @if($teacher->user->phone)
                    <p><i class="fas fa-phone"></i> {{ $teacher->user->phone }}</p>
                @endif
                <div class="profile-badge">
                    <i class="fas fa-chalkboard-teacher"></i> Teacher
                </div>
            </div>
        </div>
        
        @if(Auth::id() == $teacher->id)
            <a href="{{ route('teachers.edit', $teacher) }}" class="edit-profile-btn">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        @endif
    </div>
</section>

<!-- Personal Information -->
<div class="info-grid">
    <div class="info-card">
        <h3><i class="fas fa-id-card"></i> Personal Information</h3>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-user"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Full Name</div>
                <div class="info-value">{{ $teacher->name }}</div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Email Address</div>
                <div class="info-value">{{ $teacher->user->email }}</div>
            </div>
        </div>
        
        @if($teacher->user->phone)
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-phone"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Phone Number</div>
                <div class="info-value">{{ $teacher->user->phone }}</div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="info-card">
        <h3><i class="fas fa-info-circle"></i> Account Information</h3>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Member Since</div>
                <div class="info-value">{{ $teacher->created_at->format('M d, Y') }}</div>
            </div>
        </div>
        
        @if($teacher->title)
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-award"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Title</div>
                <div class="info-value">{{ $teacher->title }}</div>
            </div>
        </div>
        @endif
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-user-tag"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Role</div>
                <div class="info-value">Teacher</div>
            </div>
        </div>
    </div>
</div>
@endsection
