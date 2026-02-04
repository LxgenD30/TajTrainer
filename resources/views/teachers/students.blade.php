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
    }
    .student-card:hover {
        border-color: #0a5c36;
        transform: translateY(-5px);
        box-shadow: 5px 5px 0 #2a2a2a;
    }
    .page-header {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);
        color: #fff;
    }
    .page-title {
        font-size: 2rem;
        color: #fff;
        font-weight: 800;
    }
    .page-subtitle {
        font-size: 1.2rem;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 600;
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

    <!-- Page Header -->
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 class="page-title" style="margin: 0 0 10px 0; font-family: 'El Messiri', serif;">
                    <i class="fas fa-user-graduate"></i> My Students
                </h1>
                <p class="page-subtitle" style="margin: 0; font-family: 'Cairo', sans-serif;">
                    <span style="color: #d4af37; font-weight: 700;">{{ $students->count() }}</span> student{{ $students->count() != 1 ? 's' : '' }} enrolled across <span style="color: #d4af37; font-weight: 700;">{{ $classrooms->count() }}</span> classroom{{ $classrooms->count() != 1 ? 's' : '' }}
                </p>
            </div>
            <div style="font-size: 3rem; opacity: 0.1;">👨‍🎓</div>
        </div>
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
                                @foreach($student->classrooms as $classroom)
                                    <span style="display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: rgba(212, 175, 55, 0.15); color: #d4af37; border-radius: 15px; font-size: 1.05rem; font-weight: 700; font-family: 'Cairo', sans-serif;">
                                        <i class="fas fa-chalkboard-teacher"></i> {{ $classroom->class_name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="student-info-text" style="margin: 0; font-style: italic;">Not enrolled in any classes</p>
                        @endif
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('teacher.student.profile', $student->id) }}" 
                       style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 12px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 1.05rem; font-family: 'Cairo', sans-serif; transition: all 0.3s ease; border: 2px solid #2a2a2a;"
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
