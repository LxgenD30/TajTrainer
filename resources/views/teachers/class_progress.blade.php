@extends('layouts.dashboard')

@section('title', 'Class Progress Analytics')
@section('user-role', 'Teacher • Performance Overview')

@section('navigation')
    <a href="{{ url('/home') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ url('/classrooms') }}" class="nav-item">
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
    .progress-header {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        color: var(--white);
        box-shadow: 0 8px 20px var(--shadow);
        position: relative;
        overflow: hidden;
    }
    
    .progress-header:before {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    }
    
    .progress-header h2 {
        color: var(--white);
        font-size: 2rem;
        margin-bottom: 10px;
        position: relative;
        z-index: 2;
    }
    
    .progress-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }
    
    .class-selector {
        background: var(--white);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px var(--shadow);
    }
    
    .class-selector select {
        width: 100%;
        padding: 12px 20px;
        border: 2px solid rgba(10, 92, 54, 0.2);
        border-radius: 10px;
        font-family: 'El Messiri', sans-serif;
        font-size: 1rem;
        color: var(--primary-green);
        font-weight: 600;
        background: var(--white);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .class-selector select:focus {
        outline: none;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.1);
    }
    
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: var(--white);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 8px 20px var(--shadow);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 15px;
    }
    
    .stat-icon.blue {
        background: linear-gradient(135deg, rgba(33, 150, 243, 0.1), rgba(33, 150, 243, 0.2));
        color: #2196f3;
    }
    
    .stat-icon.green {
        background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(76, 175, 80, 0.2));
        color: #4caf50;
    }
    
    .stat-icon.orange {
        background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(255, 152, 0, 0.2));
        color: #ff9800;
    }
    
    .stat-icon.gold {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(212, 175, 55, 0.2));
        color: var(--gold);
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-green);
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #666;
        font-size: 0.95rem;
    }
    
    .tajweed-analysis {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 8px 20px var(--shadow);
    }
    
    .tajweed-analysis h3 {
        color: var(--primary-green);
        font-size: 1.5rem;
        margin-bottom: 25px;
    }
    
    .tajweed-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .tajweed-item {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.03), rgba(46, 139, 87, 0.03));
        border-radius: 15px;
        padding: 20px;
        border-left: 4px solid var(--primary-green);
    }
    
    .tajweed-item h4 {
        color: var(--primary-green);
        font-size: 1.1rem;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .tajweed-item h4 i {
        font-size: 1.3rem;
    }
    
    .progress-bar-container {
        background: rgba(10, 92, 54, 0.1);
        border-radius: 50px;
        height: 10px;
        overflow: hidden;
        margin-bottom: 10px;
    }
    
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-green), var(--light-green));
        border-radius: 50px;
        transition: width 1s ease;
    }
    
    .progress-percentage {
        color: var(--primary-green);
        font-weight: 600;
        font-size: 0.95rem;
    }
    
    .students-table {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 20px var(--shadow);
        overflow-x: auto;
    }
    
    .students-table h3 {
        color: var(--primary-green);
        font-size: 1.5rem;
        margin-bottom: 25px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    thead {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(46, 139, 87, 0.05));
    }
    
    th {
        padding: 15px;
        text-align: left;
        color: var(--primary-green);
        font-weight: 600;
        font-family: 'El Messiri', sans-serif;
    }
    
    td {
        padding: 15px;
        border-bottom: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    tbody tr {
        transition: all 0.3s ease;
    }
    
    tbody tr:hover {
        background: rgba(10, 92, 54, 0.03);
    }
    
    .student-name {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .student-avatar-small {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .score-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .score-high {
        background: rgba(76, 175, 80, 0.1);
        color: #4caf50;
    }
    
    .score-medium {
        background: rgba(255, 152, 0, 0.1);
        color: #ff9800;
    }
    
    .score-low {
        background: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
    }
    
    .export-btn {
        margin-top: 20px;
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
    
    .export-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.3);
    }
    
    @media (max-width: 768px) {
        .tajweed-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-overview {
            grid-template-columns: 1fr;
        }
        
        table {
            font-size: 0.9rem;
        }
        
        th, td {
            padding: 10px;
        }
    }
</style>
@endsection

@section('content')
<!-- Progress Header -->
<div class="progress-header">
    <h2><i class="fas fa-chart-line"></i> Class Progress Analytics</h2>
    <p>Track student performance and Tajweed mastery across your classes</p>
</div>

<!-- Class Selector -->
<div class="class-selector">
    <select id="classSelect" onchange="filterByClass()">
        <option value="all">All Classes</option>
        @php
            $classrooms = \App\Models\Classroom::with('teacher')->get();
        @endphp
        @foreach($classrooms as $classroom)
            <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
        @endforeach
    </select>
</div>

@php
    // Get statistics for all classes
    $totalStudents = \App\Models\Student::count();
    $totalAssignments = \App\Models\Assignment::count();
    $totalSubmissions = \App\Models\AssignmentSubmission::whereNotNull('marks')->count();
    $avgClassScore = \App\Models\Score::avg('marks') ?? 0;
    
    // Tajweed analysis (mock data for demonstration)
    $tajweedRules = [
        ['name' => 'Makharij', 'icon' => 'fa-comment-dots', 'accuracy' => 87],
        ['name' => 'Ghunnah', 'icon' => 'fa-volume-up', 'accuracy' => 82],
        ['name' => 'Idgham', 'icon' => 'fa-compress', 'accuracy' => 79],
        ['name' => 'Qalqalah', 'icon' => 'fa-bolt', 'accuracy' => 75],
        ['name' => 'Madd', 'icon' => 'fa-arrows-alt-h', 'accuracy' => 84],
        ['name' => 'Ikhfa', 'icon' => 'fa-eye-slash', 'accuracy' => 78]
    ];
    
    // Get student performance data
    $students = \App\Models\Student::with(['user', 'scores'])->get()->map(function($student) {
        $avgScore = $student->scores->avg('marks') ?? 0;
        return [
            'name' => $student->user->name ?? 'Student',
            'email' => $student->user->email ?? 'N/A',
            'assignments_completed' => $student->scores->count(),
            'avg_score' => round($avgScore, 1),
            'highest_score' => $student->scores->max('marks') ?? 0,
            'attendance' => rand(75, 100) // Mock attendance
        ];
    });
@endphp

<!-- Stats Overview -->
<div class="stats-overview">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-value">{{ $totalStudents }}</div>
        <div class="stat-label">Total Students</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-value">{{ $totalAssignments }}</div>
        <div class="stat-label">Assignments Given</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="stat-value">{{ $totalSubmissions }}</div>
        <div class="stat-label">Submissions Graded</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon gold">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-value">{{ number_format($avgClassScore, 1) }}%</div>
        <div class="stat-label">Class Average</div>
    </div>
</div>

<!-- Tajweed Analysis -->
<div class="tajweed-analysis">
    <h3><i class="fas fa-brain"></i> Tajweed Rules Mastery</h3>
    <div class="tajweed-grid">
        @foreach($tajweedRules as $rule)
            <div class="tajweed-item">
                <h4>
                    <i class="fas {{ $rule['icon'] }}"></i>
                    {{ $rule['name'] }}
                </h4>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: {{ $rule['accuracy'] }}%"></div>
                </div>
                <div class="progress-percentage">Class Accuracy: {{ $rule['accuracy'] }}%</div>
            </div>
        @endforeach
    </div>
</div>

<!-- Students Performance Table -->
<div class="students-table">
    <h3><i class="fas fa-graduation-cap"></i> Student Performance Overview</h3>
    
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Assignments</th>
                <th>Average Score</th>
                <th>Highest Score</th>
                <th>Attendance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>
                        <div class="student-name">
                            <div class="student-avatar-small">
                                {{ strtoupper(substr($student['name'], 0, 1)) }}
                            </div>
                            <div>
                                <strong>{{ $student['name'] }}</strong><br>
                                <small style="color: #999;">{{ $student['email'] }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $student['assignments_completed'] }}</td>
                    <td>
                        @php
                            $scoreClass = 'score-low';
                            if($student['avg_score'] >= 80) $scoreClass = 'score-high';
                            elseif($student['avg_score'] >= 60) $scoreClass = 'score-medium';
                        @endphp
                        <span class="score-badge {{ $scoreClass }}">{{ $student['avg_score'] }}%</span>
                    </td>
                    <td>{{ $student['highest_score'] }}%</td>
                    <td>{{ $student['attendance'] }}%</td>
                    <td>
                        @if($student['avg_score'] >= 80)
                            <i class="fas fa-check-circle" style="color: #4caf50;"></i> Excellent
                        @elseif($student['avg_score'] >= 60)
                            <i class="fas fa-exclamation-circle" style="color: #ff9800;"></i> Good
                        @else
                            <i class="fas fa-times-circle" style="color: #e74c3c;"></i> Needs Support
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <button class="export-btn" onclick="exportToCSV()">
        <i class="fas fa-download"></i> Export to CSV
    </button>
</div>
@endsection

@section('extra-scripts')
<script>
    function filterByClass() {
        const classId = document.getElementById('classSelect').value;
        if(classId === 'all') {
            window.location.href = '{{ url("/teacher/class-progress") }}';
        } else {
            window.location.href = '{{ url("/teacher/class-progress") }}/' + classId;
        }
    }
    
    function exportToCSV() {
        const table = document.querySelector('table');
        let csv = [];
        
        // Get headers
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent);
        csv.push(headers.join(','));
        
        // Get rows
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = Array.from(row.querySelectorAll('td')).map(td => {
                // Clean up the text content
                return td.textContent.replace(/\s+/g, ' ').trim();
            });
            csv.push(cells.join(','));
        });
        
        // Download
        const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'class_progress_' + new Date().toISOString().split('T')[0] + '.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Animate progress bars on load
    document.addEventListener('DOMContentLoaded', function() {
        const progressBars = document.querySelectorAll('.progress-bar-fill');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 500);
        });
    });
</script>
@endsection
