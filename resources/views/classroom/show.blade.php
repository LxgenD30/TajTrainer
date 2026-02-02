@extends('layouts.dashboard')

@section('title', $classroom->class_name)
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
    .classroom-info-bar {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        border-radius: 20px;
        padding: 25px 30px;
        margin-bottom: 30px;
        color: var(--white);
        box-shadow: 0 8px 20px var(--shadow);
    }
    
    .info-bar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .class-info h2 {
        color: var(--white);
        font-size: 1.8rem;
        margin: 0 0 8px 0;
    }
    
    .class-info p {
        margin: 0;
        opacity: 0.9;
        font-size: 1rem;
    }
    
    .access-code-box {
        display: flex;
        align-items: center;
        gap: 15px;
        background: rgba(255, 255, 255, 0.15);
        padding: 15px 20px;
        border-radius: 15px;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .access-code-label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 5px;
    }
    
    .access-code-value {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 4px;
        color: var(--white);
    }
    
    .toggle-btn {
        padding: 8px 15px;
        background: rgba(255, 255, 255, 0.2);
        color: var(--white);
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        font-weight: 600;
    }
    
    .toggle-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .btn-action {
        padding: 10px 20px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        font-family: 'El Messiri', sans-serif;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-progress {
        background: rgba(255, 255, 255, 0.2);
        color: var(--white);
        border: 2px solid rgba(255, 255, 255, 0.4);
    }
    
    .btn-progress:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }
    
    .btn-edit {
        background: var(--gold);
        color: var(--dark-green);
        border: none;
    }
    
    .btn-edit:hover {
        background: #c49b2f;
        transform: translateY(-2px);
    }
    
    .btn-back {
        background: transparent;
        color: var(--white);
        border: 2px solid rgba(255, 255, 255, 0.4);
    }
    
    .btn-back:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }
    
    .content-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 25px;
    }
    
    .content-card {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 20px var(--shadow);
        transition: all 0.3s ease;
    }
    
    .content-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(10, 92, 54, 0.2);
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .card-title {
        color: var(--primary-green);
        font-size: 1.4rem;
        margin: 0;
    }
    
    .btn-create {
        padding: 10px 20px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
        border: none;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'El Messiri', sans-serif;
        font-size: 0.95rem;
    }
    
    .btn-create:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.3);
    }
    
    .item-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .item-card {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(46, 139, 87, 0.05));
        border-left: 4px solid var(--primary-green);
        padding: 18px 20px;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    
    .item-card:hover {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.1), rgba(46, 139, 87, 0.1));
        transform: translateX(5px);
    }
    
    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 12px;
    }
    
    .item-title {
        color: var(--primary-green);
        font-size: 1.1rem;
        margin: 0 0 8px 0;
        font-weight: 600;
    }
    
    .item-text {
        color: #666;
        font-size: 0.9rem;
        margin: 0;
    }
    
    .item-meta {
        display: flex;
        gap: 20px;
        font-size: 0.9rem;
        color: #666;
        margin-top: 10px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-view {
        padding: 8px 16px;
        background: var(--gold);
        color: var(--dark-green);
        border-radius: 50px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s ease;
        white-space: nowrap;
    }
    
    .btn-view:hover {
        background: #c49b2f;
        transform: scale(1.05);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: rgba(10, 92, 54, 0.2);
        margin-bottom: 15px;
    }
    
    .empty-state p {
        font-size: 1rem;
        margin: 0;
    }
    
    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .info-bar-content {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .action-buttons {
            width: 100%;
        }
        
        .btn-action {
            flex: 1;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
    <!-- Classroom Info Bar -->
    <div class="classroom-info-bar">
        <div class="info-bar-content">
            <!-- Class Info -->
            <div class="class-info">
                <h2>{{ $classroom->class_name }}</h2>
                <p>{{ $classroom->description ?? 'No description provided' }}</p>
            </div>

            <!-- Access Code -->
            <div class="access-code-box">
                <div style="font-size: 1.5rem;">🔑</div>
                <div>
                    <div class="access-code-label">Access Code</div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span id="accessCode" class="access-code-value">••••••</span>
                        <span id="accessCodeHidden" style="display: none;">{{ $classroom->access_code }}</span>
                        <button onclick="toggleAccessCode(this)" class="toggle-btn">
                            <span id="toggleIcon">👁️</span> <span id="toggleText">Show</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('teacher.class.progress', $classroom->id) }}" class="btn-action btn-progress">
                    <i class="fas fa-chart-line"></i> Progress
                </a>
                <a href="{{ route('classroom.edit', $classroom->id) }}" class="btn-action btn-edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('classroom.index') }}" class="btn-action btn-back">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <script>
        function toggleAccessCode(btn) {
            const codeElement = document.getElementById('accessCode');
            const hiddenCode = document.getElementById('accessCodeHidden').textContent.trim();
            const toggleIcon = document.getElementById('toggleIcon');
            const toggleText = document.getElementById('toggleText');
            
            if (codeElement.textContent.includes('•')) {
                codeElement.textContent = hiddenCode;
                toggleIcon.textContent = '🙈';
                toggleText.textContent = 'Hide';
            } else {
                codeElement.textContent = '••••••';
                toggleIcon.textContent = '👁️';
                toggleText.textContent = 'Show';
            }
        }
    </script>

    <!-- Students and Assignments Grid -->
    <div class="content-grid">
        <!-- Students Section -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users"></i> Enrolled Students</h3>
            </div>
            <div class="card-body">
                @if($students->count() > 0)
                    <div class="item-list">
                        @foreach($students as $student)
                            <div class="item-card">
                                <div class="item-header">
                                    <div>
                                        <h4 class="item-title">{{ $student->name }}</h4>
                                        <p class="item-text"><i class="fas fa-envelope"></i> {{ $student->email }}</p>
                                    </div>
                                    <a href="{{ route('teacher.student.submissions', ['classroom' => $classroom->id, 'student' => $student->id]) }}" 
                                        class="btn-view">
                                        <i class="fas fa-eye"></i> View Work
                                    </a>
                                </div>
                                <div class="item-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-clipboard-list" style="color: var(--primary-green);"></i>
                                        <span>{{ $student->total_submissions ?? 0 }} submissions</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-check-circle" style="color: var(--gold);"></i>
                                        <span>{{ $student->graded_submissions ?? 0 }} graded</span>
                                    </div>
                                    @if($student->student && $student->student->classrooms->first())
                                        <div class="meta-item">
                                            <i class="fas fa-calendar" style="color: #999;"></i>
                                            <span>{{ $student->student->classrooms->first()->pivot->date_joined ? \Carbon\Carbon::parse($student->student->classrooms->first()->pivot->date_joined)->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-user-slash"></i>
                        <p>No students enrolled yet. Students can join using the access code above.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Assignments Section -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tasks"></i> Assignments</h3>
                <button onclick="window.location.href='{{ route('assignment.create', $classroom->id) }}'" class="btn-create">
                    <i class="fas fa-plus"></i> Create
                </button>
            </div>
            <div class="card-body">
                @if($assignments->count() > 0)
                    <div class="item-list">
                        @foreach($assignments as $assignment)
                            <div class="item-card" style="border-left-color: var(--gold);">
                                <div class="item-header">
                                    <div>
                                        <h4 class="item-title">
                                            @if($assignment->surah)
                                                <i class="fas fa-book-quran"></i> {{ $assignment->surah }} 
                                                ({{ $assignment->start_verse }}@if($assignment->end_verse)-{{ $assignment->end_verse }}@endif)
                                            @else
                                                {{ $assignment->material ? $assignment->material->title : 'Assignment' }}
                                            @endif
                                        </h4>
                                        <p class="item-text">{{ Str::limit($assignment->instructions, 100) }}</p>
                                    </div>
                                    <div style="display: flex; gap: 10px; align-items: center; flex-shrink: 0;">
                                        <span style="font-size: 0.9rem; color: var(--primary-green); font-weight: 600; white-space: nowrap;">
                                            <i class="fas fa-star"></i> {{ $assignment->total_marks }} pts
                                        </span>
                                        <a href="{{ route('assignment.show', $assignment->assignment_id) }}" class="btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                                <div class="item-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-calendar-alt" style="color: var(--primary-green);"></i>
                                        <span>Due: {{ $assignment->due_date->format('M d, Y h:i A') }}</span>
                                    </div>
                                    @if($assignment->is_voice_submission)
                                        <div class="meta-item">
                                            <i class="fas fa-microphone" style="color: var(--gold);"></i>
                                            <span>Voice Submission</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-clipboard"></i>
                        <p>No assignments yet. Create assignments to give tasks to your students.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
