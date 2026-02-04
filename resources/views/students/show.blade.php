@extends('layouts.dashboard')

@section('title', 'My Profile')
@section('user-role', 'Student • Profile')

@section('navigation')
    @include('partials.student-nav')
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
        gap: 30px;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
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
        margin-top: 15px;
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.3);
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
        border: 2px solid #2a2a2a;
        margin-bottom: 12px;
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
</style>

<!-- Profile Banner -->
<section class="profile-banner">
    <div class="profile-header">
        @if($student->user->profile_picture)
            <img src="{{ asset('storage/' . $student->user->profile_picture) }}" alt="Profile Picture" class="profile-avatar">
        @else
            <div class="profile-avatar" style="background: linear-gradient(135deg, #ffd700, #ffed4e); display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 800; color: #0a5c36;">
                {{ substr($student->name, 0, 1) }}
            </div>
        @endif
        
        <div class="profile-info">
            <h1>{{ $student->name }}</h1>
            <p><i class="fas fa-envelope"></i> {{ $student->user->email }}</p>
            <p><i class="fas fa-id-card"></i> Student ID: {{ $student->id }}</p>
            
            <a href="{{ route('students.edit', $student->id) }}" class="edit-profile-btn">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </div>
    </div>
</section>

<!-- Personal Information -->
<div class="info-grid">
    <div class="info-card">
        <h3><i class="fas fa-user-circle"></i> Personal Information</h3>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-user"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Full Name</div>
                <div class="info-value">{{ $student->name }}</div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Email Address</div>
                <div class="info-value">{{ $student->user->email }}</div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-id-badge"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Student ID</div>
                <div class="info-value">#{{ $student->id }}</div>
            </div>
        </div>
    </div>
    
    <div class="info-card">
        <h3><i class="fas fa-book-quran"></i> Learning Information</h3>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Current Level</div>
                <div class="info-value">{{ $student->current_level ?? 'Not Set' }}</div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Member Since</div>
                <div class="info-value">{{ $student->created_at->format('M d, Y') }}</div>
            </div>
        </div>
        
        @if($student->biodata)
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="info-content">
                <div class="info-label">About</div>
                <div class="info-value" style="font-weight: 400;">{{ Str::limit($student->biodata, 80) }}</div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
