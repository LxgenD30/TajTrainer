@extends('layouts.template')

@section('page-title', 'Class Progress - ' . $classroom->class_name)
@section('page-subtitle', 'Monitor student performance and identify common challenges')

@section('content')
<style>
    .progress-container {
        padding: 25px;
    }

    .back-link {
        display: inline-block;
        color: var(--gold);
        text-decoration: none;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: rgba(31, 39, 27, 0.6);
        border: 2px solid var(--primary-green);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(227, 216, 136, 0.2);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--gold);
        font-family: 'Cairo', sans-serif;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
        font-family: 'Cairo', sans-serif;
    }

    .chart-card {
        background: rgba(31, 39, 27, 0.6);
        border: 2px solid var(--primary-green);
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 25px;
    }

    .chart-title {
        font-size: 1.3rem;
        color: var(--gold);
        font-family: 'Amiri', serif;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(227, 216, 136, 0.2);
    }

    .student-card {
        background: rgba(31, 39, 27, 0.5);
        border: 1px solid rgba(227, 216, 136, 0.2);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
    }

    .student-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(227, 216, 136, 0.1);
    }

    .student-name {
        font-size: 1.2rem;
        color: var(--gold);
        font-weight: 600;
        font-family: 'Cairo', sans-serif;
    }

    .student-accuracy {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--gold);
    }

    .weakness-mini-list {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .weakness-tag {
        background: rgba(255, 107, 107, 0.2);
        color: #ff6b6b;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        border: 1px solid #ff6b6b;
    }

    .common-error-item {
        background: rgba(31, 39, 27, 0.5);
        border: 1px solid rgba(227, 216, 136, 0.2);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .error-name {
        font-size: 1rem;
        color: var(--gold);
        font-weight: 600;
        font-family: 'Cairo', sans-serif;
    }

    .error-count {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
    }

    .error-rate {
        font-size: 1.2rem;
        color: #ff6b6b;
        font-weight: bold;
        text-align: right;
    }

    .no-data-message {
        text-align: center;
        padding: 40px;
        color: rgba(255, 255, 255, 0.5);
        font-size: 1.1rem;
    }
</style>

<div class="progress-container">
    <a href="{{ route('classroom.show', $classroom->id) }}" class="back-link">← Back to Classroom</a>

    <!-- Class-wide Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ number_format($classStats['class_average_accuracy'], 1) }}%</div>
            <div class="stat-label">Class Average Accuracy</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $classStats['total_students'] }}</div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $classStats['active_students'] }}</div>
            <div class="stat-label">Active Students (30 days)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $classStats['total_practice_sessions'] }}</div>
            <div class="stat-label">Total Practice Sessions</div>
        </div>
    </div>

    @if($classStats['total_practice_sessions'] > 0)
        <!-- Common Class Errors -->
        <div class="chart-card">
            <div class="chart-title">🎯 Most Common Class Errors</div>
            @if(count($classStats['common_errors']) > 0)
                @foreach($classStats['common_errors'] as $error)
                    <div class="common-error-item">
                        <div>
                            <div class="error-name">{{ $error->error_type }} - {{ $error->rule_name }}</div>
                            <div class="error-count">{{ $error->student_count }} students struggling with this</div>
                        </div>
                        <div class="error-rate">
                            {{ $error->total_errors }}<br>
                            <span style="font-size: 0.7rem; color: rgba(255, 255, 255, 0.6);">errors</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-data-message">No common errors found. The class is doing great!</div>
            @endif
        </div>

        <!-- Individual Student Progress -->
        <div class="chart-card">
            <div class="chart-title">👥 Individual Student Progress</div>
            @foreach($studentsProgress as $studentData)
                <div class="student-card">
                    <div class="student-header">
                        <div class="student-name">{{ $studentData['student']->name }}</div>
                        <div class="student-accuracy">{{ number_format($studentData['progress']['accuracy'], 1) }}%</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <div>
                            <span style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem;">
                                {{ $studentData['progress']['total_attempts'] }} attempts
                            </span>
                            <span style="color: rgba(255, 255, 255, 0.5); margin: 0 8px;">|</span>
                            <span style="color: #51d488; font-size: 0.9rem;">
                                {{ $studentData['progress']['correct_count'] }} correct
                            </span>
                            <span style="color: rgba(255, 255, 255, 0.5); margin: 0 8px;">|</span>
                            <span style="color: #ff6b6b; font-size: 0.9rem;">
                                {{ $studentData['progress']['error_count'] }} errors
                            </span>
                        </div>
                    </div>
                    @if(count($studentData['top_weaknesses']) > 0)
                        <div style="margin-top: 10px;">
                            <span style="color: rgba(255, 255, 255, 0.6); font-size: 0.85rem; margin-bottom: 8px; display: block;">
                                Areas for improvement:
                            </span>
                            <div class="weakness-mini-list">
                                @foreach($studentData['top_weaknesses'] as $weakness)
                                    <div class="weakness-tag">
                                        {{ $weakness->error_type }}: {{ number_format($weakness->fail_rate, 0) }}%
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="chart-card">
            <div class="no-data-message">
                <p style="font-size: 3rem; margin-bottom: 15px;">📚</p>
                <p>No practice data yet. Students need to complete assignments or practice sessions to see class progress!</p>
            </div>
        </div>
    @endif
</div>

@endsection
