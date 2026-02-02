@extends('layouts.dashboard')

@section('title', 'My Classrooms')
@section('user-role', 'Teacher • Classroom Management')

@section('navigation')
    <a href="{{ url('/home') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ url('/classrooms') }}" class="nav-item active">
        <div class="nav-icon">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="nav-label">My Classes</div>
    </a>
    
    <a href="{{ url('/assignments') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="nav-label">Assignments</div>
    </a>
    
    <a href="{{ url('/teacher/submissions') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="nav-label">Submissions</div>
    </a>
    
    <a href="{{ url('/materials') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="nav-label">Materials</div>
    </a>
    
    <a href="{{ route('teachers.show', Auth::id()) }}" class="nav-item">
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
    .classrooms-header {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        color: var(--white);
        box-shadow: 0 8px 20px var(--shadow);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .header-content h2 {
        color: var(--white);
        font-size: 2rem;
        margin: 0 0 10px 0;
    }
    
    .header-content p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }
    
    .btn-create-classroom {
        padding: 15px 30px;
        background: var(--white);
        color: var(--primary-green);
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        font-family: 'El Messiri', sans-serif;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .btn-create-classroom:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(255, 255, 255, 0.3);
    }
    
    .classrooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }
    
    .classroom-card {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 20px var(--shadow);
        transition: all 0.3s ease;
    }
    
    .classroom-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
    }
    
    .classroom-title {
        color: var(--primary-green);
        font-size: 1.5rem;
        margin: 0 0 10px 0;
        font-weight: 600;
    }
    
    .classroom-description {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 20px;
        min-height: 48px;
    }
    
    .access-code-section {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(46, 139, 87, 0.05));
        border-left: 4px solid var(--gold);
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .access-code-label {
        font-size: 0.85rem;
        color: var(--primary-green);
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .access-code-display {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .code-value {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gold);
        letter-spacing: 3px;
    }
    
    .toggle-code-btn {
        padding: 6px 12px;
        background: var(--primary-green);
        color: var(--white);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.85rem;
    }
    
    .toggle-code-btn:hover {
        background: var(--dark-green);
        transform: scale(1.05);
    }
    
    .classroom-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px 0;
        border-top: 1px solid rgba(10, 92, 54, 0.1);
        border-bottom: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    .stat-box {
        text-align: center;
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 5px;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-green);
    }
    
    .stat-value.pending {
        color: #ff9800;
    }
    
    .stat-value.completed {
        color: #4caf50;
    }
    
    .classroom-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-action {
        flex: 1;
        padding: 12px;
        border-radius: 50px;
        text-decoration: none;
        text-align: center;
        font-weight: 600;
        font-family: 'El Messiri', sans-serif;
        transition: all 0.3s ease;
    }
    
    .btn-view {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
    }
    
    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.3);
    }
    
    .btn-edit {
        background: transparent;
        color: var(--primary-green);
        border: 2px solid var(--primary-green);
    }
    
    .btn-edit:hover {
        background: var(--primary-green);
        color: var(--white);
    }
    
    .empty-state {
        background: var(--white);
        border-radius: 20px;
        padding: 80px 40px;
        text-align: center;
        box-shadow: 0 8px 20px var(--shadow);
    }
    
    .empty-state i {
        font-size: 6rem;
        color: rgba(10, 92, 54, 0.2);
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        color: var(--primary-green);
        font-size: 1.8rem;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #666;
        font-size: 1.1rem;
        margin-bottom: 30px;
    }
    
    @media (max-width: 768px) {
        .classrooms-grid {
            grid-template-columns: 1fr;
        }
        
        .classrooms-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .btn-create-classroom {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<!-- Classrooms Header -->
<div class="classrooms-header">
    <div class="header-content">
        <h2><i class="fas fa-chalkboard-teacher"></i> My Classrooms</h2>
        <p>Create and manage virtual classrooms for your students</p>
    </div>
    <a href="{{ route('classroom.create') }}" class="btn-create-classroom">
        <i class="fas fa-plus-circle"></i> Create Classroom
    </a>
</div>

<!-- Classrooms Grid -->
@if($classrooms->count() > 0)
    <div class="classrooms-grid">
        @foreach($classrooms as $classroom)
            <div class="classroom-card">
                <h3 class="classroom-title">{{ $classroom->class_name }}</h3>
                <p class="classroom-description">{{ Str::limit($classroom->description ?? 'No description provided', 100) }}</p>
                
                <!-- Access Code -->
                <div class="access-code-section">
                    <div class="access-code-label"><i class="fas fa-key"></i> Access Code</div>
                    <div class="access-code-display">
                        <span class="code-value" id="code-{{ $classroom->id }}">••••••</span>
                        <span style="display: none;" id="real-code-{{ $classroom->id }}">{{ $classroom->access_code }}</span>
                        <button class="toggle-code-btn" onclick="toggleCode({{ $classroom->id }})">
                            <i class="fas fa-eye" id="icon-{{ $classroom->id }}"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="classroom-stats">
                    <div class="stat-box">
                        <div class="stat-label">Students</div>
                        <div class="stat-value">{{ $classroom->students_count ?? 0 }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Assignments</div>
                        <div class="stat-value">{{ $classroom->assignments_count ?? 0 }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Pending</div>
                        <div class="stat-value pending">{{ $classroom->pending_assignments ?? 0 }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Completed</div>
                        <div class="stat-value completed">{{ $classroom->completed_assignments ?? 0 }}</div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="classroom-actions">
                    <a href="{{ route('classroom.show', $classroom->id) }}" class="btn-action btn-view">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    <a href="{{ route('classroom.edit', $classroom->id) }}" class="btn-action btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-chalkboard"></i>
        <h3>No Classrooms Yet</h3>
        <p>Create your first classroom to start teaching and managing students</p>
        <a href="{{ route('classroom.create') }}" class="btn-create-classroom">
            <i class="fas fa-plus-circle"></i> Create Your First Classroom
        </a>
    </div>
@endif
@endsection

@section('extra-scripts')
<script>
    function toggleCode(classroomId) {
        const codeElement = document.getElementById('code-' + classroomId);
        const realCode = document.getElementById('real-code-' + classroomId).textContent;
        const icon = document.getElementById('icon-' + classroomId);
        
        if (codeElement.textContent === '••••••') {
            codeElement.textContent = realCode;
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            codeElement.textContent = '••••••';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection
