@extends('layouts.dashboard')

@section('title', 'Student Profile')
@section('user-role', 'Teacher • Student Profile')

@section('navigation')
    <a href="{{ route('home') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="{{ route('classroom.index') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <a href="{{ route('students.list') }}" class="nav-item active">
        <div class="nav-icon"><i class="fas fa-user-graduate"></i></div>
        <div class="nav-label">My Students</div>
    </a>
    <a href="{{ route('materials.index') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
        <div class="nav-label">Materials</div>
    </a>
@endsection

@section('content')
    <!-- Back Button -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('students.list') }}" 
            style="display: inline-flex; align-items: center; gap: 10px; color: #1a1a1a; text-decoration: none; font-family: 'El Messiri', sans-serif; font-weight: 600; font-size: 1rem; padding: 8px 16px; border-radius: 50px; background: rgba(255, 255, 255, 0.9); border: 3px solid #2a2a2a; transition: all 0.3s ease;"
            onmouseover="this.style.background='rgba(10, 92, 54, 0.1)'; this.style.transform='translateX(-5px)'; this.style.borderColor='#0a5c36'"
            onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.transform='translateX(0)'; this.style.borderColor='#2a2a2a'">
            <i class="fas fa-arrow-left"></i> Back to My Students
        </a>
    </div>

    <!-- Student Profile Card -->
    <div style="background: white; border-radius: 25px; padding: 40px; margin-bottom: 30px; border: 3px solid #2a2a2a; box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);">
        <!-- Student Header -->
        <div style="display: flex; align-items: center; gap: 30px; padding-bottom: 30px; border-bottom: 3px solid #f5f5f5; margin-bottom: 30px;">
            <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #0a5c36, #1abc9c); display: flex; align-items: center; justify-content: center; color: white; font-size: 3.5rem; font-weight: 700; font-family: 'El Messiri', serif; box-shadow: 0 10px 25px rgba(10, 92, 54, 0.3);">
                {{ strtoupper(substr($student->user->name, 0, 1)) }}
            </div>
            <div style="flex: 1;">
                <h1 style="margin: 0 0 10px 0; font-family: 'El Messiri', serif; font-size: 2.5rem; color: #0a5c36; font-weight: 700;">
                    {{ $student->user->name }}
                </h1>
                <p style="margin: 0 0 5px 0; font-size: 1.1rem; color: #666; font-family: 'Cairo', sans-serif;">
                    <i class="fas fa-envelope" style="color: #0a5c36; width: 20px;"></i> {{ $student->user->email }}
                </p>
                @if($student->phone_number)
                <p style="margin: 0; font-size: 1.1rem; color: #666; font-family: 'Cairo', sans-serif;">
                    <i class="fas fa-phone" style="color: #0a5c36; width: 20px;"></i> {{ $student->phone_number }}
                </p>
                @endif
            </div>
            <div style="padding: 15px 25px; background: rgba(26, 188, 156, 0.15); border-radius: 15px; border: 3px solid rgba(26, 188, 156, 0.3);">
                <div style="font-size: 0.9rem; color: #666; font-family: 'Cairo', sans-serif; margin-bottom: 5px;">Skill Level</div>
                <div style="font-size: 1.3rem; color: #1abc9c; font-weight: 700; font-family: 'El Messiri', serif;">
                    {{ $student->level ?? 'Beginner' }}
                </div>
            </div>
        </div>

        <!-- Info Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <!-- Join Date -->
            <div style="padding: 20px; background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(26, 188, 156, 0.05)); border-radius: 15px; border: 3px solid rgba(10, 92, 54, 0.1);">
                <div style="font-size: 0.85rem; color: #999; font-family: 'Cairo', sans-serif; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Member Since</div>
                <div style="font-size: 1.2rem; color: #0a5c36; font-weight: 700; font-family: 'El Messiri', serif;">
                    <i class="fas fa-calendar-alt"></i> {{ $student->user->created_at->format('M d, Y') }}
                </div>
            </div>

            <!-- Total Classes -->
            <div style="padding: 20px; background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), rgba(255, 215, 0, 0.05)); border-radius: 15px; border: 3px solid rgba(212, 175, 55, 0.2);">
                <div style="font-size: 0.85rem; color: #999; font-family: 'Cairo', sans-serif; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Enrolled Classes</div>
                <div style="font-size: 1.2rem; color: #d4af37; font-weight: 700; font-family: 'El Messiri', serif;">
                    <i class="fas fa-chalkboard"></i> {{ $student->classrooms->count() }} Classes
                </div>
            </div>

            <!-- Progress -->
            @if(isset($student->progress))
            <div style="padding: 20px; background: linear-gradient(135deg, rgba(26, 188, 156, 0.05), rgba(52, 211, 153, 0.05)); border-radius: 15px; border: 3px solid rgba(26, 188, 156, 0.2);">
                <div style="font-size: 0.85rem; color: #999; font-family: 'Cairo', sans-serif; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Overall Progress</div>
                <div style="font-size: 1.2rem; color: #1abc9c; font-weight: 700; font-family: 'El Messiri', serif;">
                    <i class="fas fa-chart-line"></i> {{ $student->progress }}%
                </div>
            </div>
            @endif
        </div>

        <!-- Additional Info -->
        @if($student->bio || $student->learning_goals)
        <div style="padding: 25px; background: rgba(10, 92, 54, 0.03); border-radius: 15px; border: 3px solid rgba(10, 92, 54, 0.1); margin-bottom: 30px;">
            @if($student->bio)
            <div style="margin-bottom: 20px;">
                <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: #0a5c36; font-weight: 700; font-family: 'El Messiri', serif;">
                    <i class="fas fa-info-circle"></i> About
                </h3>
                <p style="margin: 0; color: #666; font-family: 'Cairo', sans-serif; line-height: 1.8;">
                    {{ $student->bio }}
                </p>
            </div>
            @endif
            
            @if($student->learning_goals)
            <div>
                <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: #0a5c36; font-weight: 700; font-family: 'El Messiri', serif;">
                    <i class="fas fa-bullseye"></i> Learning Goals
                </h3>
                <p style="margin: 0; color: #666; font-family: 'Cairo', sans-serif; line-height: 1.8;">
                    {{ $student->learning_goals }}
                </p>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Enrolled Classes -->
    @if($student->classrooms->count() > 0)
    <div style="background: white; border-radius: 25px; padding: 30px; margin-bottom: 30px; border: 3px solid #2a2a2a; box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);">
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
                <p style="margin: 0 0 10px 0; color: #666; font-family: 'Cairo', sans-serif; font-size: 0.9rem;">
                    {{ $classroom->description ?? 'No description' }}
                </p>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: #999; font-family: 'Cairo', sans-serif;">
                    <span><i class="fas fa-calendar"></i> Joined: {{ $classroom->pivot->date_joined ?? 'N/A' }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Performance Stats (if available) -->
    @if(isset($submissions) && $submissions->count() > 0)
    <div style="background: white; border-radius: 25px; padding: 30px; border: 3px solid #2a2a2a; box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);">
        <h2 style="margin: 0 0 25px 0; font-family: 'El Messiri', serif; font-size: 1.8rem; color: #0a5c36; font-weight: 700;">
            <i class="fas fa-chart-bar"></i> Recent Submissions
        </h2>
        
        <div style="display: grid; gap: 15px;">
            @foreach($submissions->take(5) as $submission)
            <div style="padding: 20px; background: rgba(10, 92, 54, 0.03); border-radius: 15px; border: 3px solid rgba(10, 92, 54, 0.1); display: flex; align-items: center; justify-content: space-between;">
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 5px 0; font-size: 1.1rem; color: #1a1a1a; font-weight: 700; font-family: 'El Messiri', serif;">
                        {{ $submission->assignment->title ?? 'Assignment' }}
                    </h4>
                    <p style="margin: 0; font-size: 0.9rem; color: #666; font-family: 'Cairo', sans-serif;">
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
                    <div style="font-size: 0.9rem; color: #999; font-weight: 600; font-family: 'Cairo', sans-serif;">
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
