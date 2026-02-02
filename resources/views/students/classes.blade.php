@extends('layouts.dashboard')

@section('title', 'My Classes')
@section('user-role', 'Student • My Classes')

@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ route('student.classes') }}" class="nav-item active">
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

@section('content')
<style>
    .modern-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);
        transition: all 0.3s ease;
    }
    
    .modern-card:hover {
        box-shadow: 0 15px 40px rgba(10, 92, 54, 0.15);
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d4f4dd, #a8e6cf);
        border-left: 5px solid #0a5c36;
        color: #064e32;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }
    
    .alert-error {
        background: linear-gradient(135deg, #ffe5e5, #ffcccc);
        border-left: 5px solid #e74c3c;
        color: #c0392b;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 3px solid #f5f5dc;
    }
    
    .icon-badge {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        box-shadow: 0 5px 20px rgba(10, 92, 54, 0.3);
    }
    
    .class-card {
        background: #ffffff;
        border: 2px solid #2a2a2a;
        border-radius: 12px;
        padding: 25px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .class-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(180deg, #d4af37, #0a5c36);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .class-card:hover {
        border-color: #0a5c36;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.3);
    }
    
    .class-card:hover::before {
        opacity: 1;
    }
    
    .btn-view-class {
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
        color: #ffffff;
        padding: 10px 25px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-view-class:hover {
        background: linear-gradient(135deg, #064e32, #0a5c36);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.3);
    }
    
    .enroll-form-input {
        width: 100%;
        padding: 15px 20px;
        background: #ffffff;
        border: 2px solid #e8e8e8;
        border-radius: 10px;
        color: #064e32;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        font-family: 'Courier New', monospace;
        letter-spacing: 4px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 600;
    }
    
    .enroll-form-input:focus {
        border-color: #d4af37;
        outline: none;
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
    }
    
    .btn-enroll {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #d4af37, #c9a961);
        color: #064e32;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .btn-enroll:hover {
        background: linear-gradient(135deg, #c9a961, #d4af37);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
    }
    
    .info-box {
        background: linear-gradient(135deg, #fffef7, #f5f5dc);
        border: 2px solid #d4af37;
        border-radius: 12px;
        padding: 20px;
        margin-top: 25px;
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 40px;
        color: #999;
    }
    
    .empty-state-icon {
        font-size: 5rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
    
    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 15px;
        background: linear-gradient(135deg, #f5f5dc, #e8dcc4);
        border-radius: 50px;
        color: #064e32;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    @media (max-width: 968px) {
        .classes-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div style="padding: 0;">
    @if(session('success'))
        <div class="alert-success">
            <span style="font-size: 1.8rem;">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error">
            <span style="font-size: 1.8rem;">⚠</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 1.8rem;">⚠</span>
                    <span style="font-weight: 700;">Please fix the following errors:</span>
                </div>
                <ul style="margin-left: 40px; list-style: disc;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="classes-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 30px;">
        <!-- My Enrolled Classes Section -->
        <div class="modern-card">
            <div class="section-header">
                <div class="icon-badge">
                    <i class="fas fa-school" style="color: #d4af37;"></i>
                </div>
                <div>
                    <h2 style="color: #0a5c36; font-size: 1.8rem; margin-bottom: 5px; font-weight: 700;">My Enrolled Classes</h2>
                    <p style="color: #666; font-size: 1rem;">{{ $student->classrooms->count() }} {{ $student->classrooms->count() === 1 ? 'Class' : 'Classes' }} Active</p>
                </div>
            </div>

            @if($student->classrooms->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">📚</div>
                    <h3 style="color: #0a5c36; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">No Classes Yet</h3>
                    <p style="color: #666; font-size: 1.1rem; line-height: 1.6;">Use the access code from your teacher to enroll in your first class</p>
                </div>
            @else
                <div style="display: grid; gap: 20px;">
                    @foreach($student->classrooms as $classroom)
                        <div class="class-card">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                                <div style="flex: 1;">
                                    <h3 style="color: #0a5c36; font-size: 1.4rem; margin-bottom: 12px; font-weight: 700;">{{ $classroom->class_name }}</h3>
                                    <p style="color: #666; margin-bottom: 15px; line-height: 1.6; font-size: 1rem;">{{ $classroom->description ?? 'No description available' }}</p>
                                </div>
                                <a href="{{ route('classroom.show', $classroom->id) }}" class="btn-view-class">
                                    View Class <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>

                            <div style="display: flex; flex-wrap: wrap; gap: 12px; padding-top: 15px; border-top: 2px solid #f5f5dc;">
                                <span class="stat-badge">
                                    <i class="fas fa-chalkboard-teacher" style="color: #0a5c36;"></i>
                                    {{ $classroom->teacher->name ?? 'Unknown' }}
                                </span>
                                <span class="stat-badge">
                                    <i class="fas fa-users" style="color: #2e8b57;"></i>
                                    {{ $classroom->students->count() }} Students
                                </span>
                                <span class="stat-badge">
                                    <i class="fas fa-tasks" style="color: #d4af37;"></i>
                                    {{ $classroom->assignments->count() }} Assignments
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Enroll in Class Section -->
        <div class="modern-card" style="height: fit-content;">
            <div class="section-header">
                <div class="icon-badge">
                    <i class="fas fa-plus-circle" style="color: #d4af37;"></i>
                </div>
                <div>
                    <h2 style="color: #0a5c36; font-size: 1.5rem; margin-bottom: 5px; font-weight: 700;">Enroll in Class</h2>
                    <p style="color: #666; font-size: 0.95rem;">Enter access code</p>
                </div>
            </div>

            <form method="POST" action="{{ route('student.enroll') }}">
                @csrf
                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: #0a5c36; font-weight: 600; margin-bottom: 12px; font-size: 1rem;">
                        Access Code <span style="color: #e74c3c;">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="access_code" 
                        value="{{ old('access_code') }}"
                        placeholder="XXXXXX"
                        required
                        maxlength="6"
                        class="enroll-form-input"
                    >
                    <p style="color: #999; font-size: 0.9rem; margin-top: 10px; line-height: 1.5;">
                        <i class="fas fa-info-circle" style="color: #d4af37;"></i> Ask your teacher for the 6-digit access code
                    </p>
                </div>

                <button type="submit" class="btn-enroll">
                    <i class="fas fa-graduation-cap"></i> Join Class
                </button>
            </form>

            <div class="info-box">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                    <i class="fas fa-lightbulb" style="font-size: 1.5rem; color: #d4af37;"></i>
                    <h4 style="color: #0a5c36; font-size: 1.1rem; font-weight: 600;">How to Enroll</h4>
                </div>
                <ol style="color: #064e32; font-size: 0.95rem; margin-left: 25px; line-height: 2; list-style: decimal;">
                    <li>Get the access code from your teacher</li>
                    <li>Enter the code in the field above</li>
                    <li>Click "Join Class" to enroll</li>
                    <li>Start learning immediately!</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome (if not already included in layout) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
