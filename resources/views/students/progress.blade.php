@extends('layouts.dashboard')

@section('title', 'My Progress')
@section('user-role', 'Student • Learning Analytics')

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
    
    <a href="{{ route('student.progress') }}" class="nav-item active">
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
        border: 3px solid #2a2a2a;
    }
    
    .modern-card:hover {
        box-shadow: 0 15px 40px rgba(10, 92, 54, 0.15);
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
        color: white;
    }
    
    .icon-badge.orange {
        background: linear-gradient(135deg, #e67e22, #d35400);
    }
    
    .icon-badge.blue {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }
    
    .icon-badge.purple {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
    }
    
    .icon-badge.red {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
    }
    
    .section-title {
        font-size: 1.8rem;
        color: #0a5c36;
        font-weight: 700;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
        border-radius: 15px;
        padding: 25px;
        color: white;
        text-align: center;
        border: 3px solid #2a2a2a;
    }
    
    .stat-value {
        font-size: 3rem;
        font-weight: bold;
        color: #d4af37;
    }
    
    .stat-label {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-top: 10px;
    }
    
    .progress-ring {
        width: 200px;
        height: 200px;
        margin: 20px auto;
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
        stroke: #0a5c36;
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
        font-size: 3rem;
        font-weight: bold;
        color: #0a5c36;
    }
    
    .ring-label {
        font-size: 1rem;
        color: #666;
    }
    
    .weakness-item {
        background: linear-gradient(135deg, rgba(231, 76, 60, 0.05), rgba(192, 57, 43, 0.05));
        border: 2px solid rgba(231, 76, 60, 0.2);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
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
    
    .trend-indicator {
        text-align: center;
        padding: 40px;
    }
    
    .trend-arrow {
        font-size: 4rem;
        margin-bottom: 15px;
    }
    
    .trend-value {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .trend-label {
        font-size: 1.1rem;
        color: #666;
    }
</style>

<!-- Progress Header -->
<div class="modern-card" style="margin-bottom: 30px;">
    <div class="section-header">
        <div class="icon-badge">
            <i class="fas fa-chart-line"></i>
        </div>
        <div>
            <h1 class="section-title">My Learning Progress</h1>
            <p style="color: #666; font-size: 1.1rem; margin: 0;">Track your Tajweed mastery journey</p>
        </div>
    </div>
</div>

<!-- Stats Overview -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ number_format($overallProgress['accuracy'] ?? 0, 1) }}%</div>
        <div class="stat-label">Overall Accuracy</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $overallProgress['total_sessions'] ?? 0 }}</div>
        <div class="stat-label">Total Sessions</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $overallProgress['total_errors'] ?? 0 }}</div>
        <div class="stat-label">Errors Logged</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $overallProgress['total_correct'] ?? 0 }}</div>
        <div class="stat-label">Correct Rules</div>
    </div>
</div>

<!-- Progress Details Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;">
    
    <!-- Overall Performance -->
    <div class="modern-card">
        <div class="section-header">
            <div class="icon-badge">
                <i class="fas fa-trophy"></i>
            </div>
            <h3 class="section-title" style="font-size: 1.5rem;">Overall Performance</h3>
        </div>
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
        <p style="text-align: center; color: #666; font-size: 0.95rem;">Based on last 30 days</p>
    </div>
    
    <!-- Improvement Trend -->
    <div class="modern-card">
        <div class="section-header">
            <div class="icon-badge purple">
                <i class="fas fa-trending-up"></i>
            </div>
            <h3 class="section-title" style="font-size: 1.5rem;">Improvement Trend</h3>
        </div>
        @php
            $trend = $improvementTrends['overall_trend'] ?? 'stable';
            $trendValue = $improvementTrends['trend_percentage'] ?? 0;
            $trendIcon = $trend === 'improving' ? '↗' : ($trend === 'declining' ? '↘' : '→');
            $trendColor = $trend === 'improving' ? '#2ecc71' : ($trend === 'declining' ? '#e74c3c' : '#f39c12');
        @endphp
        <div class="trend-indicator">
            <div class="trend-arrow" style="color: {{ $trendColor }};">{{ $trendIcon }}</div>
            <div class="trend-value" style="color: {{ $trendColor }};">{{ abs($trendValue) }}%</div>
            <div class="trend-label">{{ ucfirst($trend) }} over time</div>
        </div>
    </div>
    
    <!-- Top Weaknesses -->
    <div class="modern-card">
        <div class="section-header">
            <div class="icon-badge red">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3 class="section-title" style="font-size: 1.5rem;">Areas to Improve</h3>
        </div>
        @if(count($topWeaknesses) > 0)
            @foreach($topWeaknesses as $weakness)
                <div class="weakness-item">
                    <div class="weakness-header">
                        <div class="weakness-name">{{ $weakness->rule_name ?? 'Unknown Rule' }}</div>
                        <div class="weakness-count">{{ $weakness->error_count ?? 0 }} errors</div>
                    </div>
                    <div style="color: #666; font-size: 0.9rem;">
                        {{ $weakness->error_type === 'madd' ? 'Madd (Elongation) Rule' : 'Noon Sakin/Tanween Rule' }}
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-smile-beam" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                <p>No significant weaknesses detected!<br>Keep up the excellent work!</p>
            </div>
        @endif
    </div>
    
</div>

<!-- Recurring Errors -->
@if(count($recurringErrors) > 0)
<div class="modern-card" style="margin-top: 30px;">
    <div class="section-header">
        <div class="icon-badge orange">
            <i class="fas fa-redo"></i>
        </div>
        <h3 class="section-title">Recurring Errors</h3>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        @foreach($recurringErrors as $error)
            <div style="background: linear-gradient(135deg, rgba(230, 126, 34, 0.05), rgba(211, 84, 0, 0.05)); border: 2px solid rgba(230, 126, 34, 0.2); border-radius: 15px; padding: 25px; text-align: center;">
                <div style="font-size: 2.5rem; color: #e67e22; margin-bottom: 10px;">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div style="font-size: 1.3rem; font-weight: 600; color: #e67e22; margin-bottom: 8px;">
                    {{ $error->rule_name ?? 'Unknown Rule' }}
                </div>
                <div style="font-size: 0.95rem; color: #666; margin-bottom: 12px;">
                    Occurred <strong>{{ $error->occurrence_count ?? 0 }} times</strong>
                </div>
                <div style="font-size: 0.9rem; color: #999;">
                    {{ $error->last_occurrence ?? 'Recently' }}
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
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
