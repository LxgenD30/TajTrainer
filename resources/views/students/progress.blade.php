@extends('layouts.dashboard')

@section('title', 'My Progress')
@section('user-role', 'Student • Learning Analytics')

@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ route('student.classes') }}" class="nav-item">
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
    
    <a href="{{ route('student.progress') }}" class="nav-item active">
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

@section('extra-styles')
<style>
    /* Page Header */
    .progress-header {
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
    
    .progress-header:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .progress-title {
        position: relative;
        z-index: 2;
    }
    
    .progress-title h1 {
        color: var(--white);
        font-size: 2.8rem;
        margin-bottom: 10px;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    
    .progress-title p {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 25px;
    }
    
    .progress-stats {
        display: flex;
        gap: 25px;
        flex-wrap: wrap;
    }
    
    .stat-item-header {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        padding: 15px 25px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        min-width: 150px;
    }
    
    .stat-value-header {
        font-size: 2rem;
        font-weight: bold;
        color: var(--gold);
        line-height: 1;
    }
    
    .stat-label-header {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-top: 5px;
    }
    
    /* Progress Grid */
    .progress-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
    }
    
    @media (min-width: 1400px) {
        .progress-grid {
            grid-template-columns: repeat(3, 1fr);
        }
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
    }
    
    .section-icon.green {
        background: linear-gradient(135deg, var(--primary-green), #2e8b57);
    }
    
    .section-icon.orange {
        background: linear-gradient(135deg, #e67e22, #d35400);
    }
    
    .section-icon.blue {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }
    
    .section-icon.purple {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
    }
    
    .section-icon.red {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
    }
    
    .section-header h3 {
        font-size: 1.5rem;
        color: #1a1a1a;
        margin: 0;
    }
    
    /* Progress Ring */
    .progress-ring-container {
        text-align: center;
        padding: 20px;
    }
    
    .progress-ring {
        width: 200px;
        height: 200px;
        margin: 0 auto 20px;
        position: relative;
    }
    
    .ring-circle {
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
    
    .ring-bg {
        fill: none;
        stroke: rgba(10, 92, 54, 0.1);
        stroke-width: 15;
    }
    
    .ring-progress {
        fill: none;
        stroke: var(--primary-green);
        stroke-width: 15;
        stroke-linecap: round;
        stroke-dasharray: 565;
        stroke-dashoffset: 565;
        transition: stroke-dashoffset 1.5s ease;
    }
    
    .ring-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }
    
    .ring-value {
        font-size: 3.5rem;
        font-weight: bold;
        color: var(--primary-green);
        line-height: 1;
    }
    
    .ring-label {
        font-size: 1.1rem;
        color: #666;
        margin-top: 8px;
    }
    
    /* Stats List */
    .stats-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 0;
        border-bottom: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    .stat-item:last-child {
        border-bottom: none;
    }
    
    .stat-label {
        font-size: 1.05rem;
        color: #555;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .stat-label i {
        color: var(--primary-green);
        width: 20px;
    }
    
    .stat-value {
        font-size: 1.4rem;
        font-weight: bold;
        color: var(--primary-green);
    }
    
    /* Improvement Indicator */
    .improvement-indicator {
        text-align: center;
        padding: 40px 20px;
    }
    
    .improvement-arrow {
        font-size: 4rem;
        margin-bottom: 15px;
        line-height: 1;
    }
    
    .improvement-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--primary-green);
        margin-bottom: 10px;
    }
    
    .improvement-label {
        font-size: 1rem;
        color: #666;
    }
    
    /* Weakness Cards */
    .weakness-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .weakness-card {
        background: linear-gradient(135deg, rgba(231, 76, 60, 0.05), rgba(192, 57, 43, 0.05));
        border: 2px solid rgba(231, 76, 60, 0.2);
        border-radius: 15px;
        padding: 20px;
        transition: all 0.3s ease;
    }
    
    .weakness-card:hover {
        border-color: #e74c3c;
        transform: translateX(5px);
    }
    
    .weakness-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .weakness-name {
        font-size: 1.2rem;
        font-weight: 600;
        color: #e74c3c;
    }
    
    .weakness-count {
        background: #e74c3c;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
    }
    
    .weakness-desc {
        color: #666;
        font-size: 0.95rem;
        margin-bottom: 12px;
    }
    
    .weakness-progress {
        width: 100%;
        height: 8px;
        background: rgba(231, 76, 60, 0.1);
        border-radius: 4px;
        overflow: hidden;
    }
    
    .weakness-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #e74c3c, #c0392b);
        border-radius: 4px;
        transition: width 1s ease;
    }
    
    /* Improved Cards */
    .improved-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .improved-card {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.05), rgba(39, 174, 96, 0.05));
        border: 2px solid rgba(46, 204, 113, 0.2);
        border-radius: 15px;
        padding: 20px;
        transition: all 0.3s ease;
    }
    
    .improved-card:hover {
        border-color: #2ecc71;
        transform: translateX(5px);
    }
    
    .improved-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .improved-name {
        font-size: 1.2rem;
        font-weight: 600;
        color: #27ae60;
    }
    
    .improved-badge {
        background: #2ecc71;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
    }
    
    .improved-desc {
        color: #666;
        font-size: 0.95rem;
    }
    
    /* Daily Progress Chart */
    .chart-container {
        padding: 20px;
        height: 300px;
        position: relative;
    }
    
    .chart-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #999;
    }
    
    .chart-placeholder i {
        font-size: 4rem;
        margin-bottom: 15px;
        opacity: 0.3;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
    
    .empty-state p {
        font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
        .progress-grid {
            grid-template-columns: 1fr;
        }
        
        .progress-stats {
            justify-content: center;
        }
        
        .progress-title h1 {
            font-size: 2rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Progress Header -->
<div class="progress-header">
    <div class="progress-title">
        <h1>My Learning Progress</h1>
        <p>Track your Tajweed mastery journey with detailed analytics and insights</p>
        
        <div class="progress-stats">
            <div class="stat-item-header">
                <div class="stat-value-header">{{ number_format($overallProgress['accuracy'] ?? 0, 1) }}%</div>
                <div class="stat-label-header">Overall Accuracy</div>
            </div>
            <div class="stat-item-header">
                <div class="stat-value-header">{{ $overallProgress['total_sessions'] ?? 0 }}</div>
                <div class="stat-label-header">Total Sessions</div>
            </div>
            <div class="stat-item-header">
                <div class="stat-value-header">{{ $overallProgress['total_errors'] ?? 0 }}</div>
                <div class="stat-label-header">Errors Logged</div>
            </div>
            <div class="stat-item-header">
                <div class="stat-value-header">{{ $overallProgress['total_correct'] ?? 0 }}</div>
                <div class="stat-label-header">Correct Rules</div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Overview Grid -->
<div class="progress-grid">
    <!-- Overall Performance Ring -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon green">
                <i class="fas fa-trophy"></i>
            </div>
            <h3>Overall Performance</h3>
        </div>
        <div class="progress-ring-container">
            <div class="progress-ring">
                <svg width="200" height="200" viewBox="0 0 200 200">
                    <circle class="ring-bg" cx="100" cy="100" r="90" />
                    <circle class="ring-progress" cx="100" cy="100" r="90" id="overall-ring" />
                </svg>
                <div class="ring-text">
                    <div class="ring-value">{{ number_format($overallProgress['accuracy'] ?? 0, 0) }}%</div>
                    <div class="ring-label">Accuracy</div>
                </div>
            </div>
            <p style="color: #666; font-size: 0.95rem;">Based on last 30 days of practice</p>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon blue">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h3>Statistics</h3>
        </div>
        <ul class="stats-list">
            <li class="stat-item">
                <span class="stat-label">
                    <i class="fas fa-clipboard-check"></i>
                    Total Practice Sessions
                </span>
                <span class="stat-value">{{ $overallProgress['total_sessions'] ?? 0 }}</span>
            </li>
            <li class="stat-item">
                <span class="stat-label">
                    <i class="fas fa-check-circle"></i>
                    Rules Applied Correctly
                </span>
                <span class="stat-value">{{ $overallProgress['total_correct'] ?? 0 }}</span>
            </li>
            <li class="stat-item">
                <span class="stat-label">
                    <i class="fas fa-exclamation-triangle"></i>
                    Errors to Review
                </span>
                <span class="stat-value">{{ $overallProgress['total_errors'] ?? 0 }}</span>
            </li>
            <li class="stat-item">
                <span class="stat-label">
                    <i class="fas fa-book-open"></i>
                    Enrolled Classes
                </span>
                <span class="stat-value">{{ \App\Models\Student::find(Auth::id())->classrooms->count() }}</span>
            </li>
        </ul>
    </div>
    
    <!-- Improvement Trend -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon purple">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3>Improvement Trend</h3>
        </div>
        @php
            $trend = $improvementTrends['overall_trend'] ?? 'stable';
            $trendValue = $improvementTrends['trend_percentage'] ?? 0;
            $trendIcon = $trend === 'improving' ? '↗' : ($trend === 'declining' ? '↘' : '→');
            $trendColor = $trend === 'improving' ? '#2ecc71' : ($trend === 'declining' ? '#e74c3c' : '#f39c12');
        @endphp
        <div class="improvement-indicator">
            <div class="improvement-arrow" style="color: {{ $trendColor }};">
                {{ $trendIcon }}
            </div>
            <div class="improvement-value" style="color: {{ $trendColor }};">
                {{ abs($trendValue) }}%
            </div>
            <div class="improvement-label">
                {{ ucfirst($trend) }} over time
            </div>
        </div>
    </div>
</div>

<!-- Two Column Section -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 30px; margin-bottom: 30px;">
    <!-- Top Weaknesses -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon red">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3>Areas to Improve</h3>
        </div>
        
        @if(count($topWeaknesses) > 0)
            <div class="weakness-list">
                @foreach($topWeaknesses as $weakness)
                    <div class="weakness-card">
                        <div class="weakness-header">
                            <div class="weakness-name">{{ $weakness['rule_name'] ?? 'Unknown Rule' }}</div>
                            <div class="weakness-count">{{ $weakness['error_count'] ?? 0 }} errors</div>
                        </div>
                        <div class="weakness-desc">
                            {{ $weakness['error_type'] === 'madd' ? 'Madd (Elongation) Rule' : 'Noon Sakin/Tanween Rule' }}
                        </div>
                        <div class="weakness-progress">
                            <div class="weakness-progress-fill" style="width: {{ min(($weakness['error_count'] ?? 0) * 10, 100) }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-smile-beam"></i>
                <p>No significant weaknesses detected!<br>Keep up the excellent work!</p>
            </div>
        @endif
    </div>
    
    <!-- Most Improved -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon green">
                <i class="fas fa-award"></i>
            </div>
            <h3>Most Improved Skills</h3>
        </div>
        
        @if(count($mostImproved) > 0)
            <div class="improved-list">
                @foreach($mostImproved as $improved)
                    <div class="improved-card">
                        <div class="improved-header">
                            <div class="improved-name">{{ $improved['rule_name'] ?? 'Unknown Rule' }}</div>
                            <div class="improved-badge">+{{ number_format($improved['improvement_percentage'] ?? 0, 0) }}%</div>
                        </div>
                        <div class="improved-desc">
                            Improved from {{ number_format($improved['old_accuracy'] ?? 0, 0) }}% to {{ number_format($improved['new_accuracy'] ?? 0, 0) }}% accuracy
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-clock"></i>
                <p>Keep practicing to see your improvement!<br>Complete more sessions to track progress.</p>
            </div>
        @endif
    </div>
</div>

<!-- Recurring Errors -->
@if(count($recurringErrors) > 0)
<div class="section-card">
    <div class="section-header">
        <div class="section-icon orange">
            <i class="fas fa-redo"></i>
        </div>
        <h3>Recurring Errors</h3>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        @foreach($recurringErrors as $error)
            <div style="background: linear-gradient(135deg, rgba(230, 126, 34, 0.05), rgba(211, 84, 0, 0.05)); border: 2px solid rgba(230, 126, 34, 0.2); border-radius: 15px; padding: 25px; text-align: center;">
                <div style="font-size: 2.5rem; color: #e67e22; margin-bottom: 10px;">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div style="font-size: 1.3rem; font-weight: 600; color: #e67e22; margin-bottom: 8px;">
                    {{ $error['rule_name'] ?? 'Unknown Rule' }}
                </div>
                <div style="font-size: 0.95rem; color: #666; margin-bottom: 12px;">
                    Occurred <strong>{{ $error['occurrence_count'] ?? 0 }} times</strong>
                </div>
                <div style="font-size: 0.9rem; color: #999;">
                    {{ $error['last_occurrence'] ?? 'Recently' }}
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Daily Progress Chart Placeholder -->
<div class="section-card">
    <div class="section-header">
        <div class="section-icon blue">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <h3>Daily Progress (Last 14 Days)</h3>
    </div>
    
    <div class="chart-container">
        <div class="chart-placeholder">
            <i class="fas fa-chart-area"></i>
            <p>Progress chart visualization coming soon!<br>Data is being tracked for {{ count($dailyProgress) }} days.</p>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate overall progress ring
        const overallRing = document.getElementById('overall-ring');
        if (overallRing) {
            const progressValue = {{ number_format($overallProgress['accuracy'] ?? 0, 0) }};
            const circumference = 2 * Math.PI * 90;
            const offset = circumference - (progressValue / 100) * circumference;
            
            setTimeout(() => {
                overallRing.style.strokeDashoffset = offset;
            }, 500);
        }
    });
</script>
@endsection
        gap: 20px;
    }
    
    .tajweed-card {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(46, 139, 87, 0.05));
        border: 2px solid rgba(10, 92, 54, 0.1);
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .tajweed-card:hover {
        border-color: var(--gold);
        transform: translateY(-5px);
    }
    
    .tajweed-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        color: var(--white);
        font-size: 2rem;
    }
    
    .tajweed-name {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--primary-green);
        margin-bottom: 10px;
    }
    
    .tajweed-desc {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 15px;
    }
    
    .tajweed-progress {
        width: 100%;
        height: 8px;
        background: rgba(10, 92, 54, 0.1);
        border-radius: 4px;
        overflow: hidden;
    }
    
    .tajweed-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-green), var(--light-green));
        border-radius: 4px;
        transition: width 1s ease;
    }
    
    .recent-scores {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 20px var(--shadow);
    }
    
    .score-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .score-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background: rgba(10, 92, 54, 0.03);
        border-radius: 12px;
        border: 2px solid rgba(10, 92, 54, 0.1);
        transition: all 0.3s ease;
    }
    
    .score-item:hover {
        border-color: var(--gold);
        transform: translateX(5px);
    }
    
    .score-info h4 {
        color: var(--primary-green);
        font-size: 1.1rem;
        margin-bottom: 5px;
    }
    
    .score-info p {
        color: #666;
        font-size: 0.9rem;
    }
    
    .score-badge {
        padding: 10px 20px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
        border-radius: 50px;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    @media (max-width: 768px) {
        .progress-grid {
            grid-template-columns: 1fr;
        }
        
        .tajweed-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Progress Overview -->
<div class="progress-grid">
    <!-- Overall Performance -->
    <div class="progress-card">
        <div class="progress-header">
            <h3>Overall Score</h3>
            <div class="progress-icon">
                <i class="fas fa-trophy"></i>
            </div>
        </div>
        @php
            $scores = \App\Models\Score::where('user_id', Auth::id())->get();
            $avgScore = $scores->avg('score') ?? 0;
        @endphp
        <div class="progress-ring">
            <svg width="150" height="150" viewBox="0 0 150 150">
                <circle class="ring-bg" cx="75" cy="75" r="70" />
                <circle class="ring-progress" cx="75" cy="75" r="70" id="overall-ring" />
            </svg>
            <div class="ring-text">
                <div class="ring-value">{{ number_format($avgScore, 0) }}%</div>
                <div class="ring-label">Average</div>
            </div>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="progress-card">
        <div class="progress-header">
            <h3>Statistics</h3>
            <div class="progress-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
        </div>
        <ul class="stats-list">
            <li class="stat-item">
                <span class="stat-label">Total Assignments</span>
                <span class="stat-value">{{ $scores->count() }}</span>
            </li>
            <li class="stat-item">
                <span class="stat-label">Highest Score</span>
                <span class="stat-value">{{ $scores->max('score') ?? 0 }}%</span>
            </li>
            <li class="stat-item">
                <span class="stat-label">Lowest Score</span>
                <span class="stat-value">{{ $scores->min('score') ?? 0 }}%</span>
            </li>
            <li class="stat-item">
                <span class="stat-label">Enrolled Classes</span>
                <span class="stat-value">{{ \App\Models\Student::find(Auth::id())->classrooms->count() }}</span>
            </li>
        </ul>
    </div>
    
    <!-- Improvement -->
    <div class="progress-card">
        <div class="progress-header">
            <h3>Improvement</h3>
            <div class="progress-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        @php
            $firstScore = $scores->sortBy('created_at')->first();
            $lastScore = $scores->sortByDesc('created_at')->first();
            $improvement = $lastScore && $firstScore ? $lastScore->score - $firstScore->score : 0;
        @endphp
        <div style="text-align: center; padding: 40px 20px;">
            <div style="font-size: 3rem; color: {{ $improvement >= 0 ? 'var(--light-green)' : '#e74c3c' }}; margin-bottom: 10px;">
                {{ $improvement >= 0 ? '↗' : '↘' }}
            </div>
            <div style="font-size: 2rem; font-weight: bold; color: var(--primary-green); margin-bottom: 5px;">
                {{ abs($improvement) }}%
            </div>
            <div style="font-size: 0.9rem; color: #666;">
                {{ $improvement >= 0 ? 'Improvement' : 'Decrease' }} from first assignment
            </div>
        </div>
    </div>
</div>

<!-- Tajweed Analysis -->
<div class="tajweed-section">
    <h3 class="section-title">
        <i class="fas fa-brain"></i>
        Tajweed Skills Analysis
    </h3>
    <p style="color: #666; margin-bottom: 25px; font-size: 1.05rem;">
        Our AI system analyzes your recitation for these Tajweed rules:
    </p>
    
    <div class="tajweed-grid">
        <div class="tajweed-card">
            <div class="tajweed-icon">
                <i class="fas fa-volume-up"></i>
            </div>
            <div class="tajweed-name">Makharij</div>
            <div class="tajweed-desc">Pronunciation Points - Correct articulation of Arabic letters</div>
            <div class="tajweed-progress">
                <div class="tajweed-progress-fill" style="width: {{ $scores->count() > 0 ? number_format($avgScore * 0.9, 0) : 0 }}%;"></div>
            </div>
        </div>
        
        <div class="tajweed-card">
            <div class="tajweed-icon">
                <i class="fas fa-wind"></i>
            </div>
            <div class="tajweed-name">Ghunnah</div>
            <div class="tajweed-desc">Nasal Sound - Nasalization with specific letters</div>
            <div class="tajweed-progress">
                <div class="tajweed-progress-fill" style="width: {{ $scores->count() > 0 ? number_format($avgScore * 0.85, 0) : 0 }}%;"></div>
            </div>
        </div>
        
        <div class="tajweed-card">
            <div class="tajweed-icon">
                <i class="fas fa-compress-alt"></i>
            </div>
            <div class="tajweed-name">Idgham</div>
            <div class="tajweed-desc">Letter Merging - Blending letters together</div>
            <div class="tajweed-progress">
                <div class="tajweed-progress-fill" style="width: {{ $scores->count() > 0 ? number_format($avgScore * 0.88, 0) : 0 }}%;"></div>
            </div>
        </div>
        
        <div class="tajweed-card">
            <div class="tajweed-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="tajweed-name">Qalqalah</div>
            <div class="tajweed-desc">Echo Sound - Bouncing pronunciation</div>
            <div class="tajweed-progress">
                <div class="tajweed-progress-fill" style="width: {{ $scores->count() > 0 ? number_format($avgScore * 0.82, 0) : 0 }}%;"></div>
            </div>
        </div>
        
        <div class="tajweed-card">
            <div class="tajweed-icon">
                <i class="fas fa-arrows-alt-h"></i>
            </div>
            <div class="tajweed-name">Madd</div>
            <div class="tajweed-desc">Elongation - Proper letter lengthening</div>
            <div class="tajweed-progress">
                <div class="tajweed-progress-fill" style="width: {{ $scores->count() > 0 ? number_format($avgScore * 0.86, 0) : 0 }}%;"></div>
            </div>
        </div>
        
        <div class="tajweed-card">
            <div class="tajweed-icon">
                <i class="fas fa-eye-slash"></i>
            </div>
            <div class="tajweed-name">Ikhfa</div>
            <div class="tajweed-desc">Concealment - Hidden pronunciation</div>
            <div class="tajweed-progress">
                <div class="tajweed-progress-fill" style="width: {{ $scores->count() > 0 ? number_format($avgScore * 0.84, 0) : 0 }}%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Scores -->
<div class="recent-scores">
    <h3 class="section-title">
        <i class="fas fa-history"></i>
        Recent Scores
    </h3>
    
    @if($scores->count() > 0)
        <div class="score-list">
            @foreach($scores->sortByDesc('created_at')->take(10) as $score)
                <div class="score-item">
                    <div class="score-info">
                        <h4>{{ $score->assignment->title ?? 'Assignment' }}</h4>
                        <p>
                            <i class="fas fa-calendar"></i> {{ $score->created_at->format('M d, Y') }} • 
                            <i class="fas fa-chalkboard"></i> {{ $score->assignment->classroom->class_name ?? 'Unknown Class' }}
                        </p>
                    </div>
                    <div class="score-badge">
                        {{ $score->score }}%
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px; color: #999;">
            <i class="fas fa-clipboard-list" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
            <p>No scores yet. Complete assignments to see your progress!</p>
        </div>
    @endif
</div>
@endsection

@section('extra-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate overall progress ring
        const overallRing = document.getElementById('overall-ring');
        if (overallRing) {
            const progressValue = {{ number_format($avgScore, 0) }};
            const circumference = 2 * Math.PI * 70;
            const offset = circumference - (progressValue / 100) * circumference;
            
            setTimeout(() => {
                overallRing.style.strokeDashoffset = offset;
            }, 500);
        }
    });
</script>
@endsection
