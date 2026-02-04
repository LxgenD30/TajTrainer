@extends('layouts.dashboard')

@section('title', 'My Classrooms')
@section('user-role', 'Teacher • Classroom Management')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
<style>
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
    
    .btn-create {
        position: relative;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: white;
        color: #0a5c36;
        padding: 15px 30px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    
    .btn-create:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }
    
    .classrooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 25px;
    }
    
    .classroom-card {
        background: white;
        border: 2px solid #000000;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
        transition: all 0.3s ease;
    }
    
    .classroom-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.15);
    }
    
    .classroom-header {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .classroom-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
        border: 2px solid #2a2a2a;
    }
    
    .classroom-info {
        flex: 1;
        min-width: 0;
    }
    
    .classroom-title {
        color: #000;
        font-size: 1.2rem;
        margin: 0 0 8px 0;
        font-weight: 800;
    }
    
    .classroom-description {
        color: #666;
        font-size: 1.05rem;
        font-weight: 600;
        line-height: 1.6;
        margin: 0;
        word-wrap: break-word;
        overflow-wrap: break-word;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .access-code-section {
        background: rgba(212, 175, 55, 0.1);
        border: 2px solid #2a2a2a;
        padding: 15px;
        border-radius: 10px;
        margin: 15px 0;
    }
    
    .access-code-label {
        font-size: 1.05rem;
        color: #666;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .access-code-display {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .code-value {
        font-family: 'Courier New', monospace;
        font-size: 1.5rem;
        font-weight: 700;
        color: #d4af37;
        letter-spacing: 4px;
    }
    
    .toggle-code-btn {
        padding: 8px 12px;
        background: #0a5c36;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 1.05rem;
    }
    
    .toggle-code-btn:hover {
        background: #1abc9c;
        transform: scale(1.05);
    }
    
    .classroom-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin: 15px 0;
        padding: 15px 0;
        border-top: 1px solid rgba(10, 92, 54, 0.1);
        border-bottom: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    .stat-box {
        text-align: center;
    }
    
    .stat-box-label {
        font-size: 1.05rem;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 5px;
        font-weight: 700;
    }
    
    .stat-box-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0a5c36;
    }
    
    .stat-box-value.pending {
        color: #ff9800;
    }
    
    .stat-box-value.completed {
        color: #4caf50;
    }
    
    .classroom-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-action {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.05rem;
        transition: all 0.3s ease;
    }
    
    .btn-view {
        background: #0a5c36;
        color: white;
    }
    
    .btn-view:hover {
        background: #1abc9c;
        transform: scale(1.02);
    }
    
    .btn-edit {
        background: transparent;
        color: #0a5c36;
        border: 2px solid #0a5c36;
    }
    
    .btn-edit:hover {
        background: #0a5c36;
        color: white;
    }
    
    .btn-delete {
        background: transparent;
        color: #e74c3c;
        border: 2px solid #e74c3c;
        flex: 0.3;
    }
    
    .btn-delete:hover {
        background: #e74c3c;
        color: white;
    }
    
    .empty-state {
        background: white;
        border-radius: 15px;
        padding: 80px 40px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .empty-state i {
        font-size: 5rem;
        color: rgba(10, 92, 54, 0.2);
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        color: #000;
        font-size: 1.6rem;
        margin-bottom: 10px;
        font-weight: 800;
    }
    
    .empty-state p {
        color: #666;
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 30px;
    }
    
    .filter-controls {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }
    
    .search-input, .filter-select {
        padding: 12px 15px;
        background: #1a1a1a;
        border: 2px solid #2a2a2a;
        border-radius: 12px;
        color: white;
        font-size: 0.95rem;
        outline: none;
        transition: all 0.3s ease;
    }
    
    .search-input {
        padding-left: 40px;
        width: 250px;
    }
    
    .filter-select {
        padding-left: 40px;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        min-width: 180px;
    }
    
    .search-wrapper, .filter-wrapper {
        position: relative;
    }
    
    .search-wrapper i, .filter-wrapper i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #d4af37;
        pointer-events: none;
    }
    
    @media (max-width: 768px) {
        .classrooms-grid {
            grid-template-columns: 1fr;
        }
        
        .search-input, .filter-select {
            width: 100%;
        }
        
        .welcome-banner {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .btn-create {
            width: 100%;
            justify-content: center;
        }
        
        .classroom-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div class="welcome-content">
        <h1><i class="fas fa-chalkboard-teacher"></i> My Classrooms</h1>
        <p>Create and manage virtual classrooms for your Tajweed students</p>
    </div>
    
    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
        @if($classrooms->count() > 0)
            <!-- Search and Filter Controls -->
            <div class="filter-controls" style="margin-bottom: 0;">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input 
                        type="text" 
                        id="classroomSearch" 
                        class="search-input"
                        placeholder="Search classrooms..."
                        onkeyup="filterClassrooms()"
                    >
                </div>
                
                <div class="filter-wrapper">
                    <i class="fas fa-filter"></i>
                    <select id="classroomSort" class="filter-select" onchange="sortClassrooms()">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="students">Most Students</option>
                        <option value="assignments">Most Assignments</option>
                    </select>
                </div>
            </div>
        @endif
        
        <a href="{{ route('classroom.create') }}" class="btn-create">
            <i class="fas fa-plus-circle"></i> Create New Class
        </a>
    </div>
</div>

@if($classrooms->count() > 0)
    <div class="classrooms-grid" id="classroomsContainer">
        @foreach($classrooms as $classroom)
            <div class="classroom-card" 
                 data-name="{{ strtolower($classroom->class_name) }}"
                 data-description="{{ strtolower($classroom->description ?? '') }}"
                 data-created="{{ $classroom->created_at }}"
                 data-students="{{ $classroom->students_count ?? 0 }}"
                 data-assignments="{{ $classroom->assignments_count ?? 0 }}">
                <div class="classroom-header">
                    <div class="classroom-icon">
                        <i class="fas fa-book-quran"></i>
                    </div>
                    <div class="classroom-info">
                        <h3 class="classroom-title">{{ $classroom->class_name }}</h3>
                        <p class="classroom-description">{{ Str::limit($classroom->description ?? 'No description provided', 80) }}</p>
                    </div>
                </div>
                
                <!-- Access Code -->
                <div class="access-code-section">
                    <div class="access-code-label">
                        <i class="fas fa-key"></i> Access Code
                    </div>
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
                        <div class="stat-box-label">Students</div>
                        <div class="stat-box-value">{{ $classroom->students_count ?? 0 }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-label">Assignments</div>
                        <div class="stat-box-value">{{ $classroom->assignments_count ?? 0 }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-label">Pending</div>
                        <div class="stat-box-value pending">{{ $classroom->pending_assignments_count ?? 0 }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-label">Completed</div>
                        <div class="stat-box-value completed">{{ $classroom->completed_assignments_count ?? 0 }}</div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="classroom-actions">
                    <a href="{{ route('classroom.show', $classroom->id) }}" class="btn-action btn-view">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('classroom.edit', $classroom->id) }}" class="btn-action btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('classroom.destroy', $classroom->id) }}" method="POST" style="flex: 0.3; display: contents;" onsubmit="return confirm('Are you sure you want to delete this classroom? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-chalkboard"></i>
        <h3>No Classrooms Yet</h3>
        <p>Create your first classroom to start teaching and managing students</p>
        <a href="{{ route('classroom.create') }}" class="btn-create">
            <i class="fas fa-plus-circle"></i> Create Your First Classroom
        </a>
    </div>
@endif

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
    
    function filterClassrooms() {
        const searchInput = document.getElementById('classroomSearch').value.toLowerCase();
        const cards = document.querySelectorAll('.classroom-card');
        let visibleCount = 0;
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const description = card.getAttribute('data-description');
            
            if (name.includes(searchInput) || description.includes(searchInput)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        document.getElementById('classCount').textContent = visibleCount;
    }
    
    function sortClassrooms() {
        const sortValue = document.getElementById('classroomSort').value;
        const container = document.getElementById('classroomsContainer');
        const cards = Array.from(document.querySelectorAll('.classroom-card'));
        
        cards.sort((a, b) => {
            switch(sortValue) {
                case 'newest':
                    return new Date(b.getAttribute('data-created')) - new Date(a.getAttribute('data-created'));
                case 'oldest':
                    return new Date(a.getAttribute('data-created')) - new Date(b.getAttribute('data-created'));
                case 'students':
                    return parseInt(b.getAttribute('data-students')) - parseInt(a.getAttribute('data-students'));
                case 'assignments':
                    return parseInt(b.getAttribute('data-assignments')) - parseInt(a.getAttribute('data-assignments'));
            }
        });
        
        cards.forEach(card => container.appendChild(card));
    }
</script>
@endsection
