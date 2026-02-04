@extends('layouts.dashboard')

@section('title', 'My Classes')
@section('user-role', 'Student • My Classes')

@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="nav-item">
        <i class="fas fa-home nav-icon"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    
    <a href="{{ route('student.classes') }}" class="nav-item active">
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
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #f5f5dc; padding-bottom: 20px; margin-bottom: 25px;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="icon-badge">
                        <i class="fas fa-school" style="color: #d4af37;"></i>
                    </div>
                    <div>
                        <h2 style="color: #0a5c36; font-size: 1.8rem; margin: 0; font-weight: 700;">My Enrolled Classes</h2>
                        <p id="classCountLabel" style="color: #666; font-size: 1rem; margin: 0;">{{ $student->classrooms->count() }} Active</p>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <div style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #d4af37; z-index: 10;"></i>
                        <input 
                            type="text" 
                            id="classSearch" 
                            placeholder="Search..." 
                            onkeyup="filterClasses()"
                            style="padding: 12px 15px 12px 40px; background: #1a1a1a; border: 2px solid #2a2a2a; border-radius: 12px; color: white; font-size: 0.95rem; width: 200px; outline: none; transition: all 0.3s ease;"
                        >
                    </div>

                    <div style="position: relative;">
                        <i class="fas fa-filter" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #d4af37; z-index: 10; pointer-events: none;"></i>
                        <select 
                            id="classSort" 
                            onchange="sortClasses()"
                            style="padding: 12px 15px 12px 40px; background: #1a1a1a; border: 2px solid #2a2a2a; border-radius: 12px; color: white; font-size: 0.95rem; cursor: pointer; appearance: none; -webkit-appearance: none; min-width: 160px; outline: none;"
                        >
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                        </select>
                    </div>
                </div>
            </div>

            @if($student->classrooms->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">📚</div>
                    <h3 style="color: #0a5c36; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">No Classes Yet</h3>
                    <p style="color: #666; font-size: 1.25rem; line-height: 1.6;">Use the access code from your teacher to enroll in your first class</p>
                </div>
            @else
                <div style="display: grid; gap: 20px;" id="classesContainer">
                    @foreach($student->classrooms as $classroom)
                        <div class="class-card" data-classroom-name="{{ strtolower($classroom->class_name) }}" data-teacher-name="{{ strtolower($classroom->teacher->name ?? '') }}" data-enrolled-date="{{ $classroom->pivot->created_at ?? now() }}">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                                <div style="flex: 1;">
                                    <h3 style="color: #0a5c36; font-size: 1.4rem; margin-bottom: 12px; font-weight: 700;">{{ $classroom->class_name }}</h3>
                                    <p style="color: #666; margin-bottom: 15px; line-height: 1.6; font-size: 1.15rem;">{{ $classroom->description ?? 'No description available' }}</p>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <a href="{{ route('classroom.show', $classroom->id) }}" class="btn-view-class">
                                        View Class <i class="fas fa-arrow-right"></i>
                                    </a>
                                    <form method="POST" action="{{ route('classroom.leave', $classroom->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to leave {{ $classroom->class_name }}? You will need a new access code to rejoin.');">
                                        @csrf
                                        <button type="submit" style="padding: 12px 24px; background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 1rem; box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(231, 76, 60, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(231, 76, 60, 0.3)';">
                                            <i class="fas fa-sign-out-alt"></i> Leave Class
                                        </button>
                                    </form>
                                </div>
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
                    <p style="color: #999; font-size: 1.05rem; margin-top: 10px; line-height: 1.5;">
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
                <ol style="color: #064e32; font-size: 1.1rem; margin-left: 25px; line-height: 2; list-style: decimal;">
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

<script>
function filterClasses() {
    // 1. Get the search value
    const searchInput = document.getElementById('classSearch').value.toLowerCase().trim();
    
    // 2. Select all cards specifically inside the classesContainer
    const container = document.getElementById('classesContainer');
    if (!container) return;
    
    const classCards = container.querySelectorAll('.class-card');
    let visibleCount = 0;
    
    classCards.forEach(card => {
        // Use getAttribute or search the text content directly for better reliability
        const className = card.getAttribute('data-classroom-name') || '';
        const teacherName = card.getAttribute('data-teacher-name') || '';
        
        if (className.includes(searchInput) || teacherName.includes(searchInput)) {
            card.style.display = 'block'; // Or '' to restore original
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // 3. Update the count label
    const countLabel = document.getElementById('classCountLabel');
    if (countLabel) {
        if (searchInput === "") {
            countLabel.textContent = `${classCards.length} Active`;
            countLabel.style.color = "#666";
        } else {
            countLabel.textContent = `Found ${visibleCount} matches`;
            countLabel.style.color = "#d4af37";
        }
    }
}

function sortClasses() {
    const sortValue = document.getElementById('classSort').value;
    const container = document.getElementById('classesContainer');
    if (!container) return;

    const cards = Array.from(container.querySelectorAll('.class-card'));
    
    cards.sort((a, b) => {
        const dateA = new Date(a.getAttribute('data-enrolled-date'));
        const dateB = new Date(b.getAttribute('data-enrolled-date'));
        
        return sortValue === 'newest' ? dateB - dateA : dateA - dateB;
    });
    
    // Re-append to DOM
    container.innerHTML = '';
    cards.forEach(card => container.appendChild(card));
}

// Load sort preference on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedSort = localStorage.getItem('classSort');
    if (savedSort) {
        document.getElementById('classSort').value = savedSort;
        sortClasses();
    }
    
    // Add focus effect to search input
    const searchInput = document.getElementById('classSearch');
    if (searchInput) {
        searchInput.addEventListener('focus', function() {
            this.style.borderColor = '#d4af37';
            this.style.boxShadow = '0 4px 12px rgba(212, 175, 55, 0.3)';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.borderColor = '#0a5c36';
            this.style.boxShadow = '0 2px 8px rgba(10, 92, 54, 0.1)';
        });
    }
    
    // Add hover effect to sort dropdown
    const sortSelect = document.getElementById('classSort');
    if (sortSelect) {
        sortSelect.addEventListener('focus', function() {
            this.style.borderColor = '#d4af37';
            this.style.boxShadow = '0 4px 12px rgba(212, 175, 55, 0.3)';
        });
        
        sortSelect.addEventListener('blur', function() {
            this.style.borderColor = '#0a5c36';
            this.style.boxShadow = '0 2px 8px rgba(10, 92, 54, 0.1)';
        });
    }
});
</script>
@endsection
