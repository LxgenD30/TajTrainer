@extends('layouts.dashboard')

@section('title', 'My Students')
@section('user-role', 'Teacher • Student Management')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
    <style>
    .student-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        border: 3px solid #2a2a2a;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
    }
    .student-card:hover {
        border-color: #0a5c36;
        transform: translateY(-5px);
        box-shadow: 5px 5px 0 #2a2a2a;
    }
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
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
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
        flex: 1;
    }
    .welcome-content h1 {
        font-size: 2rem;
        margin-bottom: 8px;
        font-weight: 700;
        color: #ffffff;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    .welcome-content p {
        font-size: 1.05rem;
        opacity: 0.95;
        line-height: 1.6;
        margin: 0;
    }
    .student-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        font-family: 'El Messiri', serif;
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.3);
        border: 2px solid #2a2a2a;
    }
    .student-name {
        font-size: 1.2rem;
        color: #000;
        font-weight: 800;
        font-family: 'El Messiri', serif;
    }
    .student-info-text {
        font-size: 1.05rem;
        color: #333;
        font-weight: 600;
        font-family: 'Cairo', sans-serif;
    }
    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
    .empty-state-title {
        font-size: 1.6rem;
        color: #000;
        font-weight: 800;
        font-family: 'El Messiri', serif;
    }
    .empty-state-text {
        font-size: 1.2rem;
        color: #555;
        font-weight: 600;
        font-family: 'Cairo', sans-serif;
    }
    </style>
    <!-- Success Message -->
    @if(session('success'))
        <div style="background: rgba(46, 125, 50, 0.2); border: 3px solid #4caf50; color: #2e7d32; padding: 15px 20px; border-radius: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">✓</span>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="welcome-content">
            <h1><i class="fas fa-user-graduate"></i> My Students</h1>
            <p>
                <span style="color: #d4af37; font-weight: 700;">{{ $students->count() }}</span> student{{ $students->count() != 1 ? 's' : '' }} enrolled across <span style="color: #d4af37; font-weight: 700;">{{ $classrooms->count() }}</span> classroom{{ $classrooms->count() != 1 ? 's' : '' }}
            </p>
        </div>
        <div style="font-size: 3rem; opacity: 0.15; position: relative; z-index: 2; line-height: 1;">👨‍🎓</div>
    </div>

    @if($students->count() > 0)
        <!-- Students Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            @foreach($students as $student)
                <div class="student-card">
                    
                    <!-- Student Header -->
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #f5f5f5;">
                        @if($student->user->profile_picture)
                            <img src="{{ Storage::url($student->user->profile_picture) }}" 
                                 alt="{{ $student->user->name }}"
                                 style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; box-shadow: 0 5px 15px rgba(10, 92, 54, 0.3); border: 2px solid #2a2a2a;">
                        @else
                            <div class="student-avatar">
                                {{ strtoupper(substr($student->user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div style="flex: 1; min-width: 0;">
                            <h3 class="student-name" style="margin: 0 0 5px 0;">
                                {{ $student->user->name }}
                            </h3>
                            <p class="student-info-text" style="margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <i class="fas fa-envelope" style="color: #0a5c36;"></i> {{ $student->user->email }}
                            </p>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-phone" style="color: #0a5c36; width: 20px; font-size: 1.05rem;"></i>
                                <span class="student-info-text">{{ $student->user->phone ?? 'No phone number' }}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-layer-group" style="color: #0a5c36; width: 20px; font-size: 1.05rem;"></i>
                                <span style="padding: 4px 12px; background: rgba(26, 188, 156, 0.15); color: #1abc9c; border-radius: 12px; font-size: 1.05rem; font-weight: 700; font-family: 'Cairo', sans-serif;">
                                    {{ ucfirst($student->current_level ?? 'Beginner') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Classes -->
                    <div style="margin-bottom: 20px;">
                        <p style="margin: 0 0 10px 0; font-size: 1.05rem; color: #666; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Cairo', sans-serif;">
                            Enrolled Classes
                        </p>
                        @if($student->classrooms->count() > 0)
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                @foreach($student->classrooms->take(4) as $classroom)
                                    <span style="display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: rgba(212, 175, 55, 0.15); color: #d4af37; border-radius: 15px; font-size: 1.05rem; font-weight: 700; font-family: 'Cairo', sans-serif;">
                                        <i class="fas fa-chalkboard-teacher"></i> {{ $classroom->class_name }}
                                    </span>
                                @endforeach
                                @if($student->classrooms->count() > 4)
                                    <span style="display: inline-flex; align-items: center; padding: 6px 12px; background: rgba(10, 92, 54, 0.1); color: #0a5c36; border-radius: 15px; font-size: 1.05rem; font-weight: 700; font-family: 'Cairo', sans-serif;">
                                        +{{ $student->classrooms->count() - 4 }} more
                                    </span>
                                @endif
                            </div>
                        @else
                            <p class="student-info-text" style="margin: 0; font-style: italic;">Not enrolled in any classes</p>
                        @endif
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('teacher.student.profile', $student->id) }}" 
                       style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 12px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 1.05rem; font-family: 'Cairo', sans-serif; transition: all 0.3s ease; border: 2px solid #2a2a2a; margin-top: auto;"
                       onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 5px 20px rgba(10, 92, 54, 0.4)'"
                       onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'">
                        <i class="fas fa-user-circle"></i> View Profile
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div style="background: white; border-radius: 15px; padding: 60px 40px; text-align: center; border: 3px solid #2a2a2a;">
            <div class="empty-state-icon">👥</div>
            <h3 class="empty-state-title" style="margin: 0 0 10px 0;">No Students Yet</h3>
            <p class="empty-state-text" style="margin: 0;">
                Students will appear here once they enroll in your classrooms.
            </p>
        </div>
    @endif
@endsection
