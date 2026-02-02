@extends('layouts.dashboard')

@section('title', 'My Classes')
@section('user-role', 'Student • ' . $student->classrooms->count() . ' Enrolled Classes')

@section('navigation')
    <a href="{{ url('/student/classes') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ url('/student/classes') }}" class="nav-item active">
        <div class="nav-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="nav-label">My Classes</div>
    </a>
    
    <a href="{{ url('/student/practice') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-microphone-alt"></i>
        </div>
        <div class="nav-label">Practice</div>
    </a>
    
    <a href="{{ url('/student/progress') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="nav-label">My Progress</div>
    </a>
    
    <a href="{{ url('/student/materials') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="nav-label">Materials</div>
    </a>
    
    <a href="{{ route('students.show', Auth::id()) }}" class="nav-item">
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
    .classes-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }
    
    .classes-main {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 20px var(--shadow);
    }
    
    .section-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .section-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--white);
    }
    
    .section-title h2 {
        color: var(--primary-green);
        font-size: 1.5rem;
        margin-bottom: 5px;
    }
    
    .section-title p {
        color: #666;
        font-size: 0.9rem;
    }
    
    .view-toggle {
        display: flex;
        gap: 10px;
        background: rgba(10, 92, 54, 0.05);
        padding: 5px;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    
    .view-toggle button {
        flex: 1;
        padding: 10px 20px;
        border: none;
        background: transparent;
        color: #666;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .view-toggle button.active {
        background: var(--primary-green);
        color: var(--white);
    }
    
    .view-toggle button:hover:not(.active) {
        background: rgba(10, 92, 54, 0.1);
    }
    
    .classes-container {
        display: grid;
        gap: 20px;
    }
    
    .classes-container.grid-view {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
    
    .classes-container.list-view {
        grid-template-columns: 1fr;
    }
    
    .class-card {
        background: rgba(10, 92, 54, 0.03);
        border: 2px solid rgba(10, 92, 54, 0.1);
        border-radius: 15px;
        padding: 25px;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .class-card:hover {
        border-color: var(--gold);
        transform: translateY(-5px);
        box-shadow: 0 10px 25px var(--shadow);
    }
    
    .class-card h3 {
        color: var(--primary-green);
        font-size: 1.3rem;
        margin-bottom: 10px;
    }
    
    .class-card .description {
        color: #666;
        font-size: 0.95rem;
        margin-bottom: 15px;
        line-height: 1.6;
    }
    
    .class-meta {
        display: flex;
        gap: 20px;
        padding-top: 15px;
        border-top: 1px solid rgba(10, 92, 54, 0.1);
        flex-wrap: wrap;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #666;
        font-size: 0.9rem;
    }
    
    .meta-item i {
        color: var(--primary-green);
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #999;
    }
    
    .empty-state-icon {
        font-size: 5rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: var(--primary-green);
    }
    
    .empty-state p {
        font-size: 1rem;
        color: #666;
    }
    
    .enroll-card {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 20px var(--shadow);
        height: fit-content;
        position: sticky;
        top: 120px;
    }
    
    .enroll-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .form-group label {
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        color: var(--dark-green);
        font-size: 0.95rem;
    }
    
    .form-group input {
        padding: 12px 15px;
        border: 2px solid rgba(10, 92, 54, 0.2);
        border-radius: 10px;
        font-size: 1rem;
        font-family: 'Amiri', serif;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.1);
    }
    
    .btn-enroll {
        padding: 12px 25px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
        border: none;
        border-radius: 50px;
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-enroll:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.3);
    }
    
    .info-box {
        background: rgba(212, 175, 55, 0.1);
        border: 1px solid rgba(212, 175, 55, 0.3);
        border-radius: 10px;
        padding: 15px;
        margin-top: 20px;
    }
    
    .info-box p {
        font-size: 0.85rem;
        color: #666;
        line-height: 1.6;
        margin: 0;
    }
    
    @media (max-width: 1024px) {
        .classes-grid {
            grid-template-columns: 1fr;
        }
        
        .enroll-card {
            position: static;
        }
    }
    
    @media (max-width: 768px) {
        .classes-container.grid-view {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="classes-grid">
    <!-- Main Classes Section -->
    <div class="classes-main">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="section-title">
                <h2>My Enrolled Classes</h2>
                <p>{{ $student->classrooms->count() }} {{ Str::plural('class', $student->classrooms->count()) }} enrolled</p>
            </div>
        </div>
        
        @if($student->classrooms->count() > 0)
            <div class="view-toggle">
                <button onclick="setView('list')" id="listBtn" class="active">
                    <i class="fas fa-list"></i> List View
                </button>
                <button onclick="setView('grid')" id="gridBtn">
                    <i class="fas fa-th"></i> Grid View
                </button>
            </div>
            
            <div class="classes-container list-view" id="classesContainer">
                @foreach($student->classrooms->sortByDesc('pivot.date_joined') as $classroom)
                    <a href="{{ route('classroom.show', $classroom->id) }}" class="class-card">
                        <h3>{{ $classroom->class_name }}</h3>
                        <p class="description">{{ $classroom->description ?? 'No description available' }}</p>
                        <div class="class-meta">
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                <span>{{ $classroom->teacher->name ?? 'Unknown Teacher' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $classroom->students->count() }} students</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-tasks"></i>
                                <span>{{ $classroom->assignments->count() }} assignments</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>Joined {{ \Carbon\Carbon::parse($classroom->pivot->date_joined)->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                <h3>No Classes Yet</h3>
                <p>Enter an access code from your teacher to enroll in your first class</p>
            </div>
        @endif
    </div>
    
    <!-- Enroll Section -->
    <div class="enroll-card">
        <div class="section-header" style="border-bottom: 2px solid rgba(10, 92, 54, 0.1); margin-bottom: 25px; padding-bottom: 20px;">
            <div class="section-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="section-title">
                <h2>Enroll in Class</h2>
                <p>Enter access code</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('student.enroll') }}" class="enroll-form">
            @csrf
            <div class="form-group">
                <label for="access_code">
                    <i class="fas fa-key"></i> Class Access Code
                </label>
                <input 
                    type="text" 
                    id="access_code" 
                    name="access_code" 
                    placeholder="Enter 6-digit code" 
                    required
                    maxlength="6"
                    value="{{ old('access_code') }}"
                >
                @error('access_code')
                    <span style="color: #dc3545; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="btn-enroll">
                <i class="fas fa-check-circle"></i> Enroll Now
            </button>
        </form>
        
        <div class="info-box">
            <p><strong><i class="fas fa-info-circle"></i> How to enroll:</strong><br>
            Ask your teacher for a class access code. Each class has a unique 6-character code that allows you to join.</p>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    function setView(view) {
        const container = document.getElementById('classesContainer');
        const listBtn = document.getElementById('listBtn');
        const gridBtn = document.getElementById('gridBtn');
        
        if (view === 'list') {
            container.className = 'classes-container list-view';
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
            localStorage.setItem('classesView', 'list');
        } else {
            container.className = 'classes-container grid-view';
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            localStorage.setItem('classesView', 'grid');
        }
    }
    
    // Load saved preference
    window.addEventListener('DOMContentLoaded', function() {
        const savedView = localStorage.getItem('classesView') || 'list';
        setView(savedView);
    });
</script>
@endsection
