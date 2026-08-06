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
        color: #000;
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
        color: #000;
        font-weight: 600;
    }
    
    .weakness-name {
        font-size: 1.2rem;
        font-weight: 800;
        color: #000;
    }
    
    .weakness-count {
        background: #e74c3c;
        color: white;
        padding: 8px 18px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 1.05rem;
        border: 2px solid #2a2a2a;
    }

    .trend-value {
        font-size: 3rem;
        font-weight: bold;
    }

    .trend-label {
        font-size: 1.2rem;
        color: #000;
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

    /* Welcome Banner (consistent student theme) */
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
    }
    .welcome-banner:before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    .welcome-content { position: relative; z-index: 2; }
    .welcome-content h1 { font-size: 2.5rem; margin-bottom: 10px; font-weight: 700; color: #ffffff; text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3); display: flex; align-items: center; gap: 12px; }
    .welcome-content p { font-size: 1.25rem; opacity: 0.95; line-height: 1.6; }

    /* Progress 3-column layout */
    .progress-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        align-items: start;
    }
    .progress-col {
        display: flex;
        flex-direction: column;
        gap: 30px;
        min-width: 0;
    }
    @media (max-width: 1100px) {
        .progress-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="welcome-banner">
    <div class="welcome-content">
        <h1><i class="fas fa-chart-line"></i> My Learning Progress</h1>
        <p>Track your Tajweed mastery journey with detailed analytics</p>
    </div>
</section>

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

<div class="progress-grid">
    
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
        <p style="text-align: center; color: #000; font-size: 1.1rem; font-weight: 600;">Last 30 Days Activity</p>
    </div>

    <div class="progress-col">

    <div class="modern-card">
        <div class="section-header">
            <div class="icon-badge purple"><i class="fas fa-trending-up"></i></div>
            <h3 class="section-title">Trend</h3>
        </div>
        @php
            $isImproving = $improvementTrends['is_improving'] ?? false;
            $hasTrendData = $improvementTrends['has_data'] ?? false;
            $trendDirection = $improvementTrends['trend_direction'] ?? ($isImproving ? 'improving' : 'stable');
            $trendValue = abs($improvementTrends['accuracy_change'] ?? 0);
            $trend = $hasTrendData ? $trendDirection : 'stable';
            $trendColor = $trend === 'improving' ? '#2ecc71' : ($trend === 'declining' ? '#e74c3c' : '#7f8c8d');
            $trendArrow = $trend === 'improving' ? '↗' : ($trend === 'declining' ? '↘' : '→');
        @endphp
        <div style="text-align: center; padding: 20px;">
            <div class="trend-arrow" style="font-size: 4rem; color: {{ $trendColor }};">
                {{ $trendArrow }}
            </div>
            <div class="trend-value" style="color: {{ $trendColor }};">
                {{ number_format($trendValue, 1) }}%
            </div>
            <div class="trend-label">{{ $hasTrendData ? ucfirst($trend) . ' this week' : 'Not enough data yet' }}</div>
            <p style="font-size: 1.1rem; color: #000; margin-top: 15px;">
                Current: <strong>{{ number_format($improvementTrends['current_week_accuracy'] ?? 0, 1) }}%</strong>
            </p>
        </div>
    </div>

    @if(isset($recurringErrors) && count($recurringErrors) > 0)
    <div class="modern-card">
        <div class="section-header">
            <div class="icon-badge orange">
                <i class="fas fa-redo"></i>
            </div>
            <h3 class="section-title">Recurring Errors</h3>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 16px;">
            @foreach($recurringErrors as $error)
                <div style="background: #fffbf0; border: 2px solid #2a2a2a; border-radius: 12px; padding: 18px; width: 100%; box-sizing: border-box;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <i class="fas fa-sync-alt" style="color: #e67e22; font-size: 1.3rem;"></i>
                        <div style="font-size: 1.15rem; font-weight: 700; color: #000;">
                            {{ $error->rule_name ?? 'Unknown Rule' }}
                        </div>
                    </div>
                    <div style="font-size: 0.95rem; color: #000; margin-bottom: 12px; font-weight: 700;">
                        Occurred <strong style="color: #e67e22;">{{ $error->occurrences ?? 0 }}</strong> times
                    </div>
                    <div style="font-size: 0.9rem; color: #000; padding: 10px; background: rgba(0,0,0,0.04); border: 1px solid #2a2a2a; border-radius: 8px; line-height: 1.6;">
                        {{ $error->issue_description ?? 'Practice this rule more' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    </div>

    <div class="modern-card">
        <div class="section-header">
            <div class="icon-badge red"><i class="fas fa-exclamation-circle"></i></div>
            <h3 class="section-title">Focus Areas</h3>
        </div>
        
        @if(isset($topWeaknesses) && count($topWeaknesses) > 0)
            <div style="max-height: 640px; overflow-y: auto; padding-right: 6px;">
            @foreach($topWeaknesses as $weakness)
                @php
                    $attempts = (int) ($weakness->total_attempts ?? 0);
                    $failRate = (float) ($weakness->fail_rate ?? 0);
                    $score = $attempts > 0 ? round(100 - $failRate, 1) : 0;
                    $scoreColor = $score >= 70 ? '#2e7d32' : ($score >= 50 ? '#ef6c00' : '#c62828');
                @endphp
                <div style="background: #fffafa; border: 2px solid #2a2a2a; border-radius: 12px; padding: 16px 18px; margin-bottom: 15px; width: 100%; box-sizing: border-box;">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 6px;">
                        <span class="weakness-name">{{ $weakness->rule_name }}</span>
                        <span style="font-size: 1.5rem; font-weight: 800; line-height: 1; color: {{ $scoreColor }};">{{ $score }}%</span>
                    </div>
                    <div style="font-size: 1rem; color: #000; font-weight: 600; margin-bottom: 12px;">
                        Type: {{ ucfirst($weakness->error_type ?? '—') }}
                    </div>
                    <div style="height: 12px; border-radius: 999px; background: #f0f0f0; border: 1px solid #2a2a2a; overflow: hidden;">
                        <div style="height: 100%; width: {{ max(0, min(100, $score)) }}%; background: {{ $scoreColor }}; border-radius: 999px;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; gap: 20px; margin-top: 8px; font-size: 0.92rem; color: #000; font-weight: 700;">
                        <span><i class="fas fa-tasks"></i> {{ $attempts }} attempts</span>
                        <span><i class="fas fa-times-circle"></i> {{ round($failRate, 1) }}% error rate</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #000;">
                <i class="fas fa-smile-beam" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                <p style="font-size: 1.2rem;">No significant weaknesses detected!<br>Keep up the excellent work!</p>
            </div>
        @endif
    </div>
</div>

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