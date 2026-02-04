@extends('layouts.dashboard')

@section('title', 'My Progress')
@section('user-role', 'Student • Learning Analytics')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('content')
<style>
    .modern-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);
        transition: all 0.3s ease;
        border: 3px solid #2a2a2a;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 3px solid #f5f5dc;
    }
    
    .section-title {
        font-size: 1.6rem;
        color: #000 !important;
        font-weight: 800;
        font-family: 'El Messiri', serif;
    }

    .card-description {
        color: #444; 
        font-size: 1.2rem;
        margin: 0;
        font-family: 'Cairo', sans-serif;
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
        font-size: 3.5rem;
        font-weight: 800;
        color: #d4af37;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 1.05rem;
        font-weight: 700;
        opacity: 0.95;
        font-family: 'Cairo', sans-serif;
    }

    .progress-ring {
        width: 200px;
        height: 200px;
        margin: 20px auto;
        position: relative;
    }

    .ring-value {
        font-size: 3.5rem;
        font-weight: bold;
        color: #0a5c36;
    }
    
    .ring-label {
        font-size: 1.2rem;
        color: #444;
        font-weight: 600;
    }
    
    .weakness-name {
        font-size: 1.2rem;
        font-weight: 700;
        color: #e74c3c;
    }
    
    .weakness-count {
        background: #e74c3c;
        color: white;
        padding: 8px 18px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 1.05rem;
    }

    .trend-value {
        font-size: 3rem;
        font-weight: bold;
    }

    .trend-label {
        font-size: 1.2rem;
        color: #444;
        font-weight: 600;
    }

    .icon-badge {
        width: 50px; height: 50px;
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; color: white;
        border: 2px solid #2a2a2a;
    }
    .icon-badge.purple { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
    .icon-badge.red { background: linear-gradient(135deg, #e74c3c, #c0392b); }
    .icon-badge.orange { background: linear-gradient(135deg, #e67e22, #d35400); }
</style>

<div class="modern-card" style="margin-bottom: 30px;">
    <div class="section-header" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
        <div class="icon-badge">
            <i class="fas fa-chart-line"></i>
        </div>
        <div>
            <h1 class="section-title">My Learning Progress</h1>
            <p class="card-description">Track your Tajweed mastery journey with detailed analytics</p>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ number_format($overallProgress['accuracy'] ?? 0, 1) }}%</div>
        <div class="stat-label">Overall Accuracy</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $overallProgress['total_attempts'] ?? 0 }}</div>
        <div class="stat-label">Total Attempts</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $overallProgress['error_count'] ?? 0 }}</div>
        <div class="stat-label">Errors Logged</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $overallProgress['correct_count'] ?? 0 }}</div>
        <div class="stat-label">Correct Rules</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;">
    
    <div class="modern-card">
        <div class="section-header">
            <div class="icon-badge"><i class="fas fa-trophy"></i></div>
            <h3 class="section-title">Performance</h3>
        </div>
        <div class="progress-ring">
            <svg width="200" height="200" viewBox="0 0 200 200">
                <circle style="fill:none; stroke:rgba(10,92,54,0.1); stroke-width:15;" cx="100" cy="100" r="90" />
                <circle id="overall-ring" style="fill:none; stroke:#0a5c36; stroke-width:15; stroke-linecap:round; stroke-dasharray:565; stroke-dashoffset:565; transition: stroke-dashoffset 1.5s ease;" cx="100" cy="100" r="90" />
            </svg>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                <div class="ring-value">{{ number_format($overallProgress['accuracy'] ?? 0, 0) }}%</div>
                <div class="ring-label">Accuracy</div>
            </div>
        </div>
        <p style="text-align: center; color: #666; font-size: 1.1rem; font-weight: 600;">Last 30 Days Activity</p>
    </div>

    <div class="modern-card">
        <div class="section-header">
            <div class="icon-badge purple"><i class="fas fa-trending-up"></i></div>
            <h3 class="section-title">Trend</h3>
        </div>
        @php
            $isImproving = $improvementTrends['is_improving'] ?? false;
            $trendValue = abs($improvementTrends['accuracy_change'] ?? 0);
            $trend = $isImproving ? 'improving' : ($trendValue > 0 ? 'declining' : 'stable');
        @endphp
        <div style="text-align: center; padding: 20px;">
            <div class="trend-arrow" style="font-size: 4rem; color: {{ $isImproving ? '#2ecc71' : '#e74c3c' }};">
                {{ $isImproving ? '↗' : ($trendValue > 0 ? '↘' : '→') }}
            </div>
            <div class="trend-value" style="color: {{ $isImproving ? '#2ecc71' : '#e74c3c' }};">
                {{ number_format($trendValue, 1) }}%
            </div>
            <div class="trend-label">{{ ucfirst($trend) }} this week</div>
            <p style="font-size: 1.1rem; color: #666; margin-top: 15px;">
                Current: <strong>{{ number_format($improvementTrends['current_week_accuracy'] ?? 0, 1) }}%</strong>
            </p>
        </div>
    </div>

    <div class="modern-card">
        <div class="section-header">
            <div class="icon-badge red"><i class="fas fa-exclamation-circle"></i></div>
            <h3 class="section-title">Focus Areas</h3>
        </div>
        
        @if(isset($topWeaknesses) && count($topWeaknesses) > 0)
            @foreach($topWeaknesses as $weakness)
                <div style="background: #fffafa; border: 2px solid #ffebeb; border-radius: 12px; padding: 20px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span class="weakness-name">{{ $weakness->rule_name }}</span>
                        <span class="weakness-count">{{ $weakness->error_count }}</span>
                    </div>
                    <div style="font-size: 1.1rem; color: #666; font-weight: 500;">
                        Type: {{ ucfirst($weakness->error_type) }}
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-smile-beam" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                <p style="font-size: 1.2rem;">No significant weaknesses detected!<br>Keep up the excellent work!</p>
            </div>
        @endif
    </div>
</div>

@if(isset($recurringErrors) && count($recurringErrors) > 0)
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
                    Occurred <strong>{{ $error->occurrences ?? 0 }} times</strong>
                </div>
                <div style="font-size: 0.85rem; color: #999; padding: 10px; background: rgba(0,0,0,0.03); border-radius: 8px;">
                    {{ $error->issue_description ?? 'Practice this rule more' }}
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