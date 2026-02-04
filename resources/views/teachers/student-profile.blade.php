@extends('layouts.dashboard')

@section('title', 'Student Profile')
@section('user-role', 'Teacher • Student Profile')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
<style>
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
        top: 0; left: 0; right: 0; bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .profile-header {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 30px;
        justify-content: space-between;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3.5rem;
        font-weight: 700;
        font-family: 'El Messiri', serif;
        border: 4px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }
    
    .profile-info h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        font-weight: 700;
        color: #ffffff;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        font-family: 'El Messiri', serif;
    }
    
    .profile-info p {
        font-size: 1.25rem;
        opacity: 0.95;
        line-height: 1.6;
        margin: 5px 0;
        color: #ffffff;
        font-family: 'Cairo', sans-serif;
    }
    
    .back-btn {
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
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        color: #0a5c36;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);
    }
    
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        padding: 20px;
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(26, 188, 156, 0.05));
        border-radius: 15px;
        border: 3px solid rgba(10, 92, 54, 0.1);
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #666;
        font-family: 'Cairo', sans-serif;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-value {
        font-size: 1.2rem;
        color: #0a5c36;
        font-weight: 700;
        font-family: 'El Messiri', serif;
    }
</style>

<!-- Profile Banner -->
<div class="profile-banner">
    <div class="profile-header">
        <div style="display: flex; align-items: center; gap: 30px;">
            <div class="profile-avatar">
                {{ strtoupper(substr($student->user->name, 0, 1)) }}
            </div>
            <div class="profile-info">
                <h1>{{ $student->user->name }}</h1>
                <p><i class="fas fa-envelope"></i> {{ $student->user->email }}</p>
                @if($student->phone_number)
                <p><i class="fas fa-phone"></i> {{ $student->phone_number }}</p>
                @endif
            </div>
        </div>
        <a href="{{ route('students.list') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Students
        </a>
    </div>
</div>

<!-- Info Cards -->
<div class="info-card">
    <!-- Stats Grid -->
    <div class="stat-grid">
        <!-- Join Date -->
        <div class="stat-card">
            <div class="stat-label">Member Since</div>
            <div class="stat-value">
                <i class="fas fa-calendar-alt"></i> {{ $student->user->created_at->format('M d, Y') }}
            </div>
        </div>

        <!-- Total Classes -->
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), rgba(255, 215, 0, 0.05)); border-color: rgba(212, 175, 55, 0.2);">
            <div class="stat-label">Enrolled Classes</div>
            <div class="stat-value" style="color: #d4af37;">
                <i class="fas fa-chalkboard"></i> {{ $student->classrooms->count() }} Classes
            </div>
        </div>

        <!-- Progress -->
        @if(isset($student->progress))
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(26, 188, 156, 0.05), rgba(52, 211, 153, 0.05)); border-color: rgba(26, 188, 156, 0.2);">
            <div class="stat-label">Overall Progress</div>
            <div class="stat-value" style="color: #1abc9c;">
                <i class="fas fa-chart-line"></i> {{ $student->progress }}%
            </div>
        </div>
        @endif
        
        <!-- Skill Level -->
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(26, 188, 156, 0.05), rgba(52, 211, 153, 0.05)); border-color: rgba(26, 188, 156, 0.2);">
            <div class="stat-label">Skill Level</div>
            <div class="stat-value" style="color: #1abc9c;">
                <i class="fas fa-chart-line"></i> {{ $student->level ?? 'Beginner' }}
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    @if($student->bio || $student->learning_goals)
    <div style="padding: 25px; background: rgba(10, 92, 54, 0.03); border-radius: 15px; border: 3px solid rgba(10, 92, 54, 0.1);">
        @if($student->bio)
        <div style="margin-bottom: 20px;">
            <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: #0a5c36; font-weight: 700; font-family: 'El Messiri', serif;">
                <i class="fas fa-info-circle"></i> About
            </h3>
            <p style="margin: 0; color: #333; font-family: 'Cairo', sans-serif; line-height: 1.8;">
                {{ $student->bio }}
            </p>
        </div>
        @endif
        
        @if($student->learning_goals)
        <div>
            <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: #0a5c36; font-weight: 700; font-family: 'El Messiri', serif;">
                <i class="fas fa-bullseye"></i> Learning Goals
            </h3>
            <p style="margin: 0; color: #333; font-family: 'Cairo', sans-serif; line-height: 1.8;">
                {{ $student->learning_goals }}
            </p>
        </div>
        @endif
    </div>
    @endif
</div>

<!-- Enrolled Classes -->
@if($student->classrooms->count() > 0)
<div class="info-card">
    <h2 style="margin: 0 0 25px 0; font-family: 'El Messiri', serif; font-size: 1.8rem; color: #0a5c36; font-weight: 700;">
        <i class="fas fa-chalkboard-teacher"></i> Enrolled Classes
    </h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        @foreach($student->classrooms as $classroom)
        <a href="{{ route('classroom.show', $classroom->id) }}" 
           style="display: block; padding: 20px; background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(26, 188, 156, 0.05)); border-radius: 15px; border: 3px solid rgba(10, 92, 54, 0.1); text-decoration: none; transition: all 0.3s ease;"
           onmouseover="this.style.borderColor='#0a5c36'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 25px rgba(10, 92, 54, 0.15)'"
           onmouseout="this.style.borderColor='rgba(10, 92, 54, 0.1)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
            <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: #0a5c36; font-weight: 700; font-family: 'El Messiri', serif;">
                {{ $classroom->class_name }}
            </h3>
            <p style="margin: 0 0 10px 0; color: #333; font-family: 'Cairo', sans-serif; font-size: 1.05rem;">
                {{ $classroom->description ?? 'No description' }}
            </p>
            <div style="display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: #666; font-family: 'Cairo', sans-serif;">
                <span><i class="fas fa-calendar"></i> Joined: {{ $classroom->pivot->date_joined ?? 'N/A' }}</span>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

<!-- Performance Stats (if available) -->
@if(isset($submissions) && $submissions->count() > 0)
<div class="info-card">
    <h2 style="margin: 0 0 25px 0; font-family: 'El Messiri', serif; font-size: 1.8rem; color: #0a5c36; font-weight: 700;">
        <i class="fas fa-chart-bar"></i> Recent Submissions
    </h2>
    
    <div style="display: grid; gap: 15px;">
        @foreach($submissions->take(5) as $submission)
        <div style="padding: 20px; background: rgba(10, 92, 54, 0.03); border-radius: 15px; border: 3px solid rgba(10, 92, 54, 0.1); display: flex; align-items: center; justify-content: space-between;">
            <div style="flex: 1;">
                <h4 style="margin: 0 0 5px 0; font-size: 1.25rem; color: #1a1a1a; font-weight: 700; font-family: 'El Messiri', serif;">
                    {{ $submission->assignment->title ?? 'Assignment' }}
                </h4>
                <p style="margin: 0; font-size: 1.05rem; color: #555; font-family: 'Cairo', sans-serif;">
                    <i class="fas fa-clock"></i> {{ $submission->created_at->format('M d, Y g:i A') }}
                </p>
                </div>
                @if($submission->grade)
                <div style="padding: 10px 20px; background: {{ $submission->grade >= 80 ? 'rgba(26, 188, 156, 0.15)' : 'rgba(255, 193, 7, 0.15)' }}; border-radius: 12px; border: 3px solid {{ $submission->grade >= 80 ? 'rgba(26, 188, 156, 0.3)' : 'rgba(255, 193, 7, 0.3)' }};">
                    <div style="font-size: 1.5rem; color: {{ $submission->grade >= 80 ? '#1abc9c' : '#ffc107' }}; font-weight: 700; font-family: 'El Messiri', serif;">
                        {{ $submission->grade }}%
                    </div>
                </div>
                @else
                <div style="padding: 10px 20px; background: rgba(158, 158, 158, 0.15); border-radius: 12px; border: 3px solid rgba(158, 158, 158, 0.3);">
                    <div style="font-size: 1.05rem; color: #666; font-weight: 600; font-family: 'Cairo', sans-serif;">
                        Pending
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection
