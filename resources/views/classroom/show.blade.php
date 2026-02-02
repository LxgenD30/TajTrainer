@extends('layouts.dashboard')

@section('title', $classroom->class_name)
@section('user-role', 'Teacher • ' . $classroom->class_name)

@section('navigation')
    <a href="{{ route('home') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="{{ route('classroom.index') }}" class="nav-item active">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <a href="{{ route('teachers.show', Auth::id()) }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-user-circle"></i></div>
        <div class="nav-label">Profile</div>
    </a>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;" class="nav-item">
        @csrf
        <button type="submit" style="all: unset; width: 100%; cursor: pointer;">
            <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
            <div class="nav-label">Logout</div>
        </button>
    </form>
@endsection

@section('content')
    <!-- Success Message -->
    @if(session('success'))
        <div style="background: rgba(46, 125, 50, 0.2); border: 3px solid #4caf50; color: #2e7d32; padding: 15px 20px; border-radius: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">✓</span>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Back Button -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('classroom.index') }}" 
            style="display: inline-flex; align-items: center; gap: 10px; color: #1a1a1a; text-decoration: none; font-family: 'El Messiri', sans-serif; font-weight: 600; font-size: 1rem; padding: 8px 16px; border-radius: 50px; background: rgba(255, 255, 255, 0.9); border: 3px solid #2a2a2a; transition: all 0.3s ease;"
            onmouseover="this.style.background='rgba(10, 92, 54, 0.1)'; this.style.transform='translateX(-5px)'; this.style.borderColor='#0a5c36'"
            onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.transform='translateX(0)'; this.style.borderColor='#2a2a2a'">
            <i class="fas fa-arrow-left"></i> Back to Classes
        </a>
    </div>

    <!-- Classroom Header -->
    <div style="background: linear-gradient(135deg, #0a5c36, #1abc9c); border-radius: 25px; padding: 40px; margin-bottom: 30px; color: white; position: relative; overflow: hidden; box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25); border: 3px solid #2a2a2a;">
        <div style="position: relative; z-index: 1;">
            <h1 style="margin: 0 0 15px 0; font-family: 'El Messiri', serif; font-size: 2.5rem; font-weight: 700;">
                <i class="fas fa-chalkboard-teacher"></i> {{ $classroom->class_name }}
            </h1>
            <p style="margin: 0 0 25px 0; font-size: 1.1rem; opacity: 0.95; font-weight: 500; font-family: 'Cairo', sans-serif;">
                {{ $classroom->description ?? 'Manage your classroom students and assignments' }}
            </p>
            
            <!-- Stats Grid -->
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <div style="background: rgba(255, 255, 255, 0.2); border-radius: 15px; padding: 20px 30px; backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.3);">
                    <div style="font-size: 2.2rem; font-weight: 700; line-height: 1;">{{ $classroom->students->count() }}</div>
                    <div style="font-size: 0.95rem; opacity: 0.9; margin-top: 5px;">Students</div>
                </div>
                <div style="background: rgba(255, 255, 255, 0.2); border-radius: 15px; padding: 20px 30px; backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.3);">
                    <div style="font-size: 2.2rem; font-weight: 700; line-height: 1; color: #d4af37;">{{ $classroom->assignments->count() }}</div>
                    <div style="font-size: 0.95rem; opacity: 0.9; margin-top: 5px;">Assignments</div>
                </div>
                <div style="background: rgba(255, 255, 255, 0.2); border-radius: 15px; padding: 20px 30px; backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.3); font-family: 'JetBrains Mono', monospace;">
                    <div style="font-size: 1.8rem; font-weight: 700; line-height: 1; letter-spacing: 3px;">{{ $classroom->access_code }}</div>
                    <div style="font-size: 0.95rem; opacity: 0.9; margin-top: 5px;">Access Code</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
        <!-- Students Section -->
        <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 3px solid #2a2a2a;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #e0e0e0;">
                <h3 style="margin: 0; font-family: 'El Messiri', serif; font-size: 1.5rem; color: #1a1a1a;">
                    <i class="fas fa-users"></i> Students ({{ $classroom->students->count() }})
                </h3>
            </div>
            
            @if($classroom->students->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    @foreach($classroom->students as $student)
                        <div style="background: rgba(10, 92, 54, 0.05); border-radius: 12px; padding: 15px 20px; border: 2px solid rgba(10, 92, 54, 0.1); transition: all 0.3s ease;"
                            onmouseover="this.style.background='rgba(10, 92, 54, 0.1)'; this.style.borderColor='#0a5c36'"
                            onmouseout="this.style.background='rgba(10, 92, 54, 0.05)'; this.style.borderColor='rgba(10, 92, 54, 0.1)'">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #0a5c36, #1abc9c); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem;">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #1a1a1a; font-family: 'Cairo', sans-serif;">{{ $student->name }}</div>
                                        <div style="font-size: 0.85rem; color: #666;">{{ $student->email }}</div>
                                    </div>
                                </div>
                                <span style="padding: 6px 15px; background: rgba(76, 175, 80, 0.1); color: #4caf50; border-radius: 50px; font-size: 0.85rem; font-weight: 600;">
                                    Active
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 50px 20px; color: #999;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">👥</div>
                    <p style="margin: 0; font-size: 1.1rem; color: #666;">No students enrolled yet</p>
                    <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #999;">Share the access code to invite students</p>
                </div>
            @endif
        </div>

        <!-- Assignments Section -->
        <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 3px solid #2a2a2a;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #e0e0e0;">
                <h3 style="margin: 0; font-family: 'El Messiri', serif; font-size: 1.5rem; color: #1a1a1a;">
                    <i class="fas fa-tasks"></i> Assignments ({{ $classroom->assignments->count() }})
                </h3>
                <a href="#" style="padding: 8px 20px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(10, 92, 54, 0.3)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <i class="fas fa-plus"></i> New
                </a>
            </div>
            
            @if($classroom->assignments->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    @foreach($classroom->assignments as $assignment)
                        <div style="background: rgba(10, 92, 54, 0.05); border-left: 4px solid #0a5c36; border-radius: 12px; padding: 20px; transition: all 0.3s ease;"
                            onmouseover="this.style.background='rgba(10, 92, 54, 0.1)'; this.style.transform='translateX(5px)'"
                            onmouseout="this.style.background='rgba(10, 92, 54, 0.05)'; this.style.transform='translateX(0)'">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <h4 style="margin: 0; font-family: 'Cairo', sans-serif; font-size: 1.1rem; color: #1a1a1a;">
                                    {{ $assignment->title ?? 'Assignment' }}
                                </h4>
                                <span style="padding: 5px 12px; background: rgba(212, 175, 55, 0.1); color: #d4af37; border-radius: 50px; font-size: 0.8rem; font-weight: 600;">
                                    {{ $assignment->total_marks }} pts
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 15px; font-size: 0.85rem; color: #666; margin-bottom: 15px;">
                                <span><i class="far fa-calendar"></i> Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}</span>
                                @php
                                    $submissionCount = \App\Models\AssignmentSubmission::where('assignment_id', $assignment->id)->count();
                                @endphp
                                <span><i class="fas fa-file-alt"></i> {{ $submissionCount }} submissions</span>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <a href="#" style="flex: 1; padding: 8px; background: white; color: #0a5c36; border: 2px solid #0a5c36; border-radius: 8px; text-align: center; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;"
                                    onmouseover="this.style.background='#0a5c36'; this.style.color='white'"
                                    onmouseout="this.style.background='white'; this.style.color='#0a5c36'">
                                    View
                                </a>
                                <a href="#" style="flex: 1; padding: 8px; background: white; color: #ff9800; border: 2px solid #ff9800; border-radius: 8px; text-align: center; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;"
                                    onmouseover="this.style.background='#ff9800'; this.style.color='white'"
                                    onmouseout="this.style.background='white'; this.style.color='#ff9800'">
                                    Edit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 50px 20px; color: #999;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">📋</div>
                    <p style="margin: 0; font-size: 1.1rem; color: #666;">No assignments yet</p>
                    <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #999;">Create your first assignment to get started</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Classroom Actions -->
    <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 3px solid #2a2a2a; margin-bottom: 30px;">
        <h3 style="margin: 0 0 20px 0; font-family: 'El Messiri', serif; font-size: 1.5rem; color: #1a1a1a;">
            <i class="fas fa-cog"></i> Classroom Actions
        </h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="{{ route('classroom.edit', $classroom->id) }}" 
                style="padding: 12px 25px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 10px;"
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(10, 92, 54, 0.3)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <i class="fas fa-edit"></i> Edit Classroom
            </a>
            <form action="{{ route('classroom.destroy', $classroom->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Are you sure you want to delete this classroom? This action cannot be undone.')"
                    style="padding: 12px 25px; background: transparent; color: #e74c3c; border: 2px solid #e74c3c; border-radius: 50px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 10px;"
                    onmouseover="this.style.background='rgba(231, 76, 60, 0.1)'"
                    onmouseout="this.style.background='transparent'">
                    <i class="fas fa-trash-alt"></i> Delete Classroom
                </button>
            </form>
        </div>
    </div>
@endsection

@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="nav-item">
        <i class="fas fa-home nav-icon"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    
    <a href="{{ route('student.classes') }}" class="nav-item">
        <i class="fas fa-users nav-icon"></i>
        <span class="nav-label">My Classes</span>
    </a>
    
    <a href="{{ route('student.practice') }}" class="nav-item">
        <i class="fas fa-microphone-alt nav-icon"></i>
        <span class="nav-label">Practice</span>
    </a>
    
    <a href="{{ route('student.progress') }}" class="nav-item">
        <i class="fas fa-chart-line nav-icon"></i>
        <span class="nav-label">My Progress</span>
    </a>
    
    <a href="{{ route('student.materials') }}" class="nav-item">
        <i class="fas fa-book-open nav-icon"></i>
        <span class="nav-label">Materials</span>
    </a>
@endsection

@section('extra-styles')
<style>
    /* Spinner Animation */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .spinner {
        border: 3px solid rgba(10, 92, 54, 0.1);
        border-top: 3px solid var(--primary-green);
        border-radius: 50%;
        width: 16px;
        height: 16px;
        animation: spin 0.8s linear infinite;
        flex-shrink: 0;
    }
    
    /* Back Navigation */
    .back-nav {
        padding: 25px 0 15px;
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #1a1a1a;
        text-decoration: none;
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        font-size: 1rem;
        padding: 8px 16px;
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid #2a2a2a;
        transition: all 0.3s ease;
    }
    
    .back-link:hover {
        background: rgba(10, 92, 54, 0.1);
        transform: translateX(-5px);
        border-color: var(--primary-green);
    }
    
    /* Classroom Header */
    .classroom-header {
        background: linear-gradient(135deg, var(--primary-green), #1abc9c);
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 30px;
        color: var(--white);
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
        border: 3px solid #2a2a2a;
    }
    
    .classroom-header:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .classroom-title {
        position: relative;
        z-index: 2;
    }
    
    .classroom-title h1 {
        color: var(--white);
        font-size: 2.8rem;
        margin-bottom: 10px;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    
    .classroom-title p {
        font-size: 1.2rem;
        opacity: 0.9;
        max-width: 800px;
        margin-bottom: 25px;
    }
    
    .classroom-stats {
        display: flex;
        gap: 25px;
        flex-wrap: wrap;
    }
    
    .stat-item {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        padding: 15px 25px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        min-width: 150px;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--gold);
        line-height: 1;
    }
    
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-top: 5px;
    }
    
    /* Section Card */
    .section-card {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
        transition: all 0.3s ease;
    }
    
    .section-card:hover {
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.15);
    }
    
    .section-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .section-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: var(--white);
        background: linear-gradient(135deg, #e74c3c, #e67e22);
    }
    
    .section-header h3 {
        font-size: 1.5rem;
        color: #1a1a1a;
        margin: 0;
    }
    
    /* Assignments List */
    .assignments-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .assignment-card {
        background: rgba(10, 92, 54, 0.03);
        border-left: 4px solid var(--primary-green);
        border-radius: 12px;
        padding: 25px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .assignment-card:hover {
        transform: translateX(5px);
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.1);
    }
    
    .assignment-card.graded { border-left-color: #4caf50; }
    .assignment-card.submitted { border-left-color: #ff9800; }
    .assignment-card.overdue { border-left-color: #e74c3c; }
    .assignment-card.pending { border-left-color: var(--primary-green); }
    
    .assignment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }
    
    .assignment-title h4 {
        font-size: 1.2rem;
        color: #1a1a1a;
        margin-bottom: 8px;
    }
    
    .assignment-title p {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    .assignment-status {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        white-space: nowrap;
    }
    
    .status-graded { background: rgba(76, 175, 80, 0.1); color: #4caf50; }
    .status-submitted { background: rgba(255, 152, 0, 0.1); color: #ff9800; }
    .status-overdue { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }
    .status-pending { background: rgba(10, 92, 54, 0.1); color: var(--primary-green); }
    
    .assignment-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    .assignment-meta {
        display: flex;
        gap: 20px;
        font-size: 0.9rem;
        color: #666;
    }
    
    .assignment-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .assignment-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-primary, .btn-secondary {
        padding: 8px 20px;
        border-radius: 50px;
        border: none;
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-green), #2e8b57);
        color: var(--white);
    }
    
    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.3);
    }
    
    .btn-secondary {
        background: rgba(10, 92, 54, 0.08);
        color: #1a1a1a;
    }
    
    .btn-secondary:hover {
        background: rgba(10, 92, 54, 0.15);
    }
    
    /* Feedback Section */
    .feedback-section {
        margin-top: 20px;
        padding: 15px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 10px;
        border-left: 4px solid var(--gold);
    }
    
    .feedback-section h5 {
        color: #1a1a1a;
        font-size: 1rem;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .feedback-content {
        color: #666;
        line-height: 1.6;
        font-size: 0.95rem;
    }
    
    /* No Content State */
    .no-content {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    
    .no-content-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
    
    .no-content h3 {
        font-size: 1.3rem;
        margin-bottom: 10px;
        color: #666;
    }
    
    .no-content p {
        font-size: 0.95rem;
        max-width: 400px;
        margin: 0 auto;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .classroom-header {
            padding: 30px;
        }
        
        .classroom-title h1 {
            font-size: 2.2rem;
        }
    }
    
    @media (max-width: 768px) {
        .assignment-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .assignment-details {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .assignment-actions {
            width: 100%;
        }
        
        .btn-primary, .btn-secondary {
            flex: 1;
            justify-content: center;
        }
    }
    
    @media (max-width: 576px) {
        .section-card {
            padding: 20px;
        }
    }
</style>
@endsection

@section('content')
<!-- Classroom Header -->
<div class="classroom-header">
    <div class="classroom-title">
        <h1>{{ $classroom->class_name }}</h1>
        <p>{{ $classroom->description ?? 'Master the rules of Quranic recitation through detailed analysis and practice.' }}</p>
        
        <div class="classroom-stats">
            <div class="stat-item">
                <div class="stat-value">{{ $assignments->count() }}</div>
                <div class="stat-label">Assignments</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $classroom->students->count() }}</div>
                <div class="stat-label">Students</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $classroom->teacher->name ?? 'Unknown' }}</div>
                <div class="stat-label">Teacher</div>
            </div>
        </div>
        <div style="position: absolute; top: 30px; right: 40px; z-index: 10;">
            <a href="{{ route('student.classes') }}" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to My Classes
            </a>
        </div>
    </div>
</div>

<!-- Assignments Section -->
<div class="section-card">
    <div class="section-header">
        <div class="section-icon">
            <i class="fas fa-tasks"></i>
        </div>
        <h3>Class Assignments</h3>
    </div>
    
    @if($assignments->count() > 0)
        <div class="assignments-list">
            @foreach($assignments as $assignment)
                @php
                    $submission = $submissions->get($assignment->assignment_id);
                    $score = \App\Models\Score::where('assignment_id', $assignment->assignment_id)
                                               ->where('user_id', Auth::id())
                                               ->first();
                    
                    $isGraded = $score !== null;
                    $isSubmitted = $submission && $submission->status === 'submitted' && !$isGraded;
                    $isOverdue = $assignment->due_date < now() && !$isSubmitted && !$isGraded;
                    $isPending = !$submission || ($submission->status === 'pending' && !$isGraded);
                @endphp
                
                <div class="assignment-card {{ $isGraded ? 'graded' : ($isSubmitted ? 'submitted' : ($isOverdue ? 'overdue' : 'pending')) }}">
                    <div class="assignment-header">
                        <div class="assignment-title">
                            <h4>
                                @if($assignment->surah)
                                    <i class="fas fa-book-quran"></i> {{ $assignment->surah }} 
                                    ({{ $assignment->start_verse }}@if($assignment->end_verse)-{{ $assignment->end_verse }}@endif)
                                @else
                                    <i class="fas fa-file-alt"></i> {{ $assignment->title ?? ($assignment->material ? $assignment->material->title : 'Assignment') }}
                                @endif
                            </h4>
                            <p>{{ Str::limit($assignment->instructions, 150) }}</p>
                        </div>
                        
                        <div class="assignment-status 
                            {{ $isGraded ? 'status-graded' : ($isSubmitted ? 'status-submitted' : ($isOverdue ? 'status-overdue' : 'status-pending')) }}">
                            @if($isGraded)
                                <i class="fas fa-check-circle"></i>
                                Graded: {{ $score->score }}/{{ $assignment->total_marks }}
                            @elseif($isSubmitted)
                                <div class="spinner"></div>
                                Analyzing...
                            @elseif($isOverdue)
                                <i class="fas fa-exclamation-triangle"></i>
                                Overdue
                            @else
                                <i class="fas fa-clock"></i>
                                Pending
                            @endif
                        </div>
                    </div>
                    
                    <div class="assignment-details">
                        <div class="assignment-meta">
                            @if($assignment->due_date)
                            <span>
                                <i class="far fa-calendar"></i>
                                Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y g:i A') }}
                            </span>
                            @endif
                            <span>
                                <i class="fas fa-star"></i>
                                {{ $assignment->total_marks }} points
                            </span>
                            @if($assignment->tajweed_rules)
                            <span style="color: var(--primary-green);">
                                <i class="fas fa-microphone"></i>
                                Voice Submission
                            </span>
                            @endif
                        </div>
                        
                        <div class="assignment-actions">
                            @if($isGraded || $isSubmitted)
                                <a href="{{ route('student.assignment.view', $assignment->assignment_id) }}" class="btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @else
                                <a href="{{ route('student.assignment.submit', $assignment->assignment_id) }}" class="btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    @if($isGraded && $score->feedback)
                        <div class="feedback-section">
                            <h5><i class="fas fa-comment-dots"></i> Teacher Feedback</h5>
                            <div class="feedback-content">{{ $score->feedback }}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="no-content">
            <div class="no-content-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3>No Assignments Yet</h3>
            <p>Your teacher hasn't posted any assignments for this class.</p>
        </div>
    @endif
</div>
@endsection
