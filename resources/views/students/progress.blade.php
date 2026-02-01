@extends('layouts.template')

@section('page-title', 'My Progress')
@section('page-subtitle', 'Track your Tajweed learning journey')

@section('content')
<style>
    .progress-container {
        padding: 25px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 35px;
    }

    .stat-card {
        background: linear-gradient(135deg, rgba(31, 39, 27, 0.8) 0%, rgba(31, 39, 27, 0.5) 100%);
        border: 2px solid var(--color-dark-green);
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--color-dark-green), var(--color-gold));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(227, 216, 136, 0.3);
        border-color: var(--color-gold);
    }

    .stat-card:hover::before {
        transform: scaleX(1);
    }

    .stat-value {
        font-size: 3rem;
        font-weight: 700;
        color: var(--color-gold);
        font-family: 'Cairo', sans-serif;
        margin-bottom: 8px;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.95rem;
        color: var(--color-light-green);
        font-family: 'Cairo', sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .performance-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 35px;
    }

    .performance-card {
        background: linear-gradient(135deg, rgba(31, 39, 27, 0.8) 0%, rgba(31, 39, 27, 0.5) 100%);
        border: 2px solid var(--color-dark-green);
        border-radius: 15px;
        padding: 30px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .performance-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(227, 216, 136, 0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .performance-card:hover::after {
        opacity: 1;
    }

    .performance-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid rgba(227, 216, 136, 0.2);
    }

    .card-title {
        font-size: 1.4rem;
        color: var(--color-gold);
        font-family: 'Amiri', serif;
        font-weight: 600;
        position: relative;
        z-index: 1;
    }

    .card-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        position: relative;
        z-index: 1;
    }

    .badge-assignment {
        background: rgba(81, 212, 136, 0.15);
        color: #51d488;
        border: 1px solid #51d488;
    }

    .badge-practice {
        background: rgba(227, 216, 136, 0.15);
        color: var(--color-gold);
        border: 1px solid var(--color-gold);
    }

    .performance-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .mini-stat {
        background: rgba(31, 39, 27, 0.5);
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        border: 1px solid rgba(227, 216, 136, 0.1);
        transition: all 0.2s ease;
        position: relative;
        z-index: 1;
    }

    .mini-stat:hover {
        border-color: var(--color-gold);
        background: rgba(31, 39, 27, 0.7);
    }

    .mini-stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-light-green);
        margin-bottom: 5px;
    }

    .mini-stat-label {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .insights-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 35px;
    }

    .insight-card {
        background: linear-gradient(135deg, rgba(31, 39, 27, 0.8) 0%, rgba(31, 39, 27, 0.5) 100%);
        border: 2px solid var(--color-dark-green);
        border-radius: 15px;
        padding: 25px;
        transition: all 0.3s ease;
    }

    .insight-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
    }

    .insight-item {
        background: rgba(31, 39, 27, 0.6);
        border: 1px solid rgba(227, 216, 136, 0.15);
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .insight-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--color-gold);
        transform: scaleY(0);
        transition: transform 0.2s ease;
    }

    .insight-item:hover {
        border-color: var(--color-gold);
        background: rgba(31, 39, 27, 0.8);
        transform: translateX(5px);
    }

    .insight-item:hover::before {
        transform: scaleY(1);
    }

    .insight-content {
        flex: 1;
    }

    .insight-title {
        font-size: 1.05rem;
        color: var(--color-light-green);
        font-weight: 600;
        margin-bottom: 5px;
    }

    .insight-meta {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.5);
    }

    .insight-metric {
        text-align: right;
    }

    .metric-value {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .metric-label {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.5);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .error-rate {
        color: #ff6b6b;
    }

    .improvement-rate {
        color: #51d488;
    }

    .no-data {
        text-align: center;
        padding: 60px 20px;
        color: rgba(255, 255, 255, 0.4);
    }

    .no-data-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .no-data-text {
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .recurring-section {
        background: linear-gradient(135deg, rgba(31, 39, 27, 0.8) 0%, rgba(31, 39, 27, 0.5) 100%);
        border: 2px solid var(--color-dark-green);
        border-radius: 15px;
        padding: 25px;
    }

    @media (max-width: 768px) {
        .performance-grid,
        .insights-section {
            grid-template-columns: 1fr;
        }

        .stats-overview {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }

        .performance-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="progress-container">
    @if($overallProgress['total_attempts'] > 0)
        <!-- Overall Statistics -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-value">{{ number_format($overallProgress['accuracy'], 1) }}%</div>
                <div class="stat-label">Overall Accuracy</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $overallProgress['total_attempts'] }}</div>
                <div class="stat-label">Total Attempts</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $overallProgress['correct_count'] }}</div>
                <div class="stat-label">Correct</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $overallProgress['error_count'] }}</div>
                <div class="stat-label">Errors</div>
            </div>
        </div>

        <!-- Performance Comparison -->
        <div class="performance-grid">
            <div class="performance-card">
                <div class="card-header">
                    <div class="card-title">Assignments</div>
                    <div class="card-badge badge-assignment">Graded Work</div>
                </div>
                <div class="performance-stats">
                    <div class="mini-stat">
                        <div class="mini-stat-value">{{ number_format($overallProgress['assignment_accuracy'], 1) }}%</div>
                        <div class="mini-stat-label">Accuracy</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-value">{{ $overallProgress['assignment_attempts'] }}</div>
                        <div class="mini-stat-label">Attempts</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-value">{{ $overallProgress['assignment_correct'] }}</div>
                        <div class="mini-stat-label">Correct</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-value">{{ $overallProgress['assignment_errors'] }}</div>
                        <div class="mini-stat-label">Errors</div>
                    </div>
                </div>
            </div>

            <div class="performance-card">
                <div class="card-header">
                    <div class="card-title">Practice Sessions</div>
                    <div class="card-badge badge-practice">Self Study</div>
                </div>
                <div class="performance-stats">
                    <div class="mini-stat">
                        <div class="mini-stat-value">{{ number_format($overallProgress['practice_accuracy'], 1) }}%</div>
                        <div class="mini-stat-label">Accuracy</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-value">{{ $overallProgress['practice_attempts'] }}</div>
                        <div class="mini-stat-label">Attempts</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-value">{{ $overallProgress['practice_correct'] }}</div>
                        <div class="mini-stat-label">Correct</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-value">{{ $overallProgress['practice_errors'] }}</div>
                        <div class="mini-stat-label">Errors</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights Section -->
        <div class="insights-section">
            <!-- Areas for Improvement -->
            <div class="insight-card">
                <div class="card-header">
                    <div class="card-title">Areas for Improvement</div>
                </div>
                @if(count($topWeaknesses) > 0)
                    @foreach($topWeaknesses as $weakness)
                        <div class="insight-item">
                            <div class="insight-content">
                                <div class="insight-title">{{ $weakness->error_type }} - {{ $weakness->rule_name }}</div>
                                <div class="insight-meta">{{ $weakness->error_count }} errors in {{ $weakness->total_attempts }} attempts</div>
                            </div>
                            <div class="insight-metric">
                                <div class="metric-value error-rate">{{ number_format($weakness->fail_rate, 1) }}%</div>
                                <div class="metric-label">Error Rate</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="no-data">
                        <div class="no-data-text">No recurring errors found. Keep practicing!</div>
                    </div>
                @endif
            </div>

            <!-- Most Improved -->
            <div class="insight-card">
                <div class="card-header">
                    <div class="card-title">Most Improved</div>
                </div>
                @if(count($mostImproved) > 0)
                    @foreach($mostImproved as $improved)
                        <div class="insight-item">
                            <div class="insight-content">
                                <div class="insight-title">{{ $improved->error_type }} - {{ $improved->rule_name }}</div>
                                <div class="insight-meta">
                                    {{ number_format($improved->old_accuracy, 1) }}% → {{ number_format($improved->new_accuracy, 1) }}%
                                </div>
                            </div>
                            <div class="insight-metric">
                                <div class="metric-value improvement-rate">+{{ number_format($improved->improvement, 1) }}%</div>
                                <div class="metric-label">Improvement</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="no-data">
                        <div class="no-data-text">Keep practicing to see your improvements!</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recurring Errors -->
        @if(count($recurringErrors) > 0)
            <div class="recurring-section">
                <div class="card-header">
                    <div class="card-title">Recurring Errors - Need More Focus</div>
                </div>
                @foreach($recurringErrors as $error)
                    <div class="insight-item">
                        <div class="insight-content">
                            <div class="insight-title">{{ $error->error_type }} - {{ $error->rule_name }}</div>
                            <div class="insight-meta">Appeared {{ $error->occurrences }} times recently</div>
                        </div>
                        <div class="insight-metric">
                            <div class="metric-value error-rate">{{ $error->occurrences }}</div>
                            <div class="metric-label">Occurrences</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <!-- No Data State -->
        <div class="insight-card">
            <div class="no-data">
                <div class="no-data-icon">📚</div>
                <div class="no-data-text">
                    No practice data available yet.<br>
                    Complete assignments or practice sessions to track your progress.
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
        </div>
    </div>

    @if($overallProgress['total_attempts'] > 0)
        <!-- Assignment vs Practice Comparison -->
        <div class="chart-card" style="margin-bottom: 25px;">
            <div class="chart-title">📊 Assignment vs Practice Performance</div>
            <div class="stats-grid">
                <div class="stat-card" style="background: rgba(81, 212, 136, 0.1); border-color: #51d488;">
                    <div style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin-bottom: 10px;">📝 Assignments</div>
                    <div class="stat-value" style="color: #51d488;">{{ number_format($overallProgress['assignment_accuracy'], 1) }}%</div>
                    <div class="stat-label">{{ $overallProgress['assignment_attempts'] }} attempts</div>
                </div>
                <div class="stat-card" style="background: rgba(227, 216, 136, 0.1); border-color: var(--color-gold);">
                    <div style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin-bottom: 10px;">🎯 Practice Sessions</div>
                    <div class="stat-value" style="color: var(--color-gold);">{{ number_format($overallProgress['practice_accuracy'], 1) }}%</div>
                    <div class="stat-label">{{ $overallProgress['practice_attempts'] }} attempts</div>
                </div>
            </div>
        </div>

        <!-- Daily Progress Chart -->
        <div class="chart-card">
            <div class="chart-title">📈 Daily Progress (Last 14 Days)</div>
            <canvas id="dailyProgressChart" style="max-height: 300px;"></canvas>
        </div>

        <div class="grid-2col">
            <!-- Top Weaknesses -->
            <div class="chart-card">
                <div class="chart-title">⚠️ Top Areas for Improvement</div>
                @if(count($topWeaknesses) > 0)
                    @foreach($topWeaknesses as $weakness)
                        <div class="weakness-item">
                            <div>
                                <div class="weakness-name">{{ $weakness->error_type }} - {{ $weakness->rule_name }}</div>
                                <div class="weakness-count">{{ $weakness->error_count }} errors in {{ $weakness->total_attempts }} attempts</div>
                            </div>
                            <div class="weakness-stats">
                                <div class="weakness-rate">{{ number_format($weakness->fail_rate, 1) }}%</div>
                                <div class="weakness-count">fail rate</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="no-data-message">No recurring errors found. Keep practicing!</div>
                @endif
            </div>

            <!-- Most Improved -->
            <div class="chart-card">
                <div class="chart-title">🌟 Most Improved Rules</div>
                @if(count($mostImproved) > 0)
                    @foreach($mostImproved as $improved)
                        <div class="weakness-item">
                            <div>
                                <div class="weakness-name">{{ $improved->error_type }} - {{ $improved->rule_name }}</div>
                                <div class="weakness-count">
                                    Old: {{ number_format($improved->old_accuracy, 1) }}% → 
                                    New: {{ number_format($improved->new_accuracy, 1) }}%
                                </div>
                            </div>
                            <div class="weakness-stats">
                                <span class="improvement-badge badge-positive">
                                    +{{ number_format($improved->improvement, 1) }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="no-data-message">Keep practicing to see your improvements!</div>
                @endif
            </div>
        </div>

        <!-- Improvement Trends -->
        <div class="chart-card">
            <div class="chart-title">📊 Weekly Improvement Trends</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($improvementTrends['current_week_accuracy'], 1) }}%</div>
                    <div class="stat-label">This Week</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($improvementTrends['previous_week_accuracy'], 1) }}%</div>
                    <div class="stat-label">Last Week</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">
                        {{ $improvementTrends['accuracy_change'] > 0 ? '+' : '' }}{{ number_format($improvementTrends['accuracy_change'], 1) }}%
                    </div>
                    <div class="stat-label">Change</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">
                        @if($improvementTrends['accuracy_change'] > 0)
                            📈 Improving
                        @elseif($improvementTrends['accuracy_change'] < 0)
                            📉 Declining
                        @else
                            ➡️ Stable
                        @endif
                    </div>
                    <div class="stat-label">Trend</div>
                </div>
            </div>
        </div>

        <!-- Recurring Errors -->
        @if(count($recurringErrors) > 0)
            <div class="chart-card">
                <div class="chart-title">🔄 Recurring Errors (Need More Practice)</div>
                @foreach($recurringErrors as $error)
                    <div class="weakness-item">
                        <div>
                            <div class="weakness-name">{{ $error->error_type }} - {{ $error->rule_name }}</div>
                            <div class="weakness-count">Appeared {{ $error->occurrences }} times</div>
                        </div>
                        <div class="weakness-stats">
                            <span class="improvement-badge badge-negative">
                                Needs Focus
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="chart-card">
            <div class="no-data-message">
                <p style="font-size: 3rem; margin-bottom: 15px;">📚</p>
                <p>No practice data yet. Complete some assignments or practice sessions to see your progress!</p>
            </div>
        </div>
    @endif
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if($overallProgress['total_attempts'] > 0 && count($dailyProgress) > 0)
<script>
    // Daily Progress Chart
    const ctx = document.getElementById('dailyProgressChart').getContext('2d');
    
    const dailyData = @json($dailyProgress);
    const labels = dailyData.map(d => d.date);
    const accuracyData = dailyData.map(d => d.accuracy);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Accuracy %',
                data: accuracyData,
                borderColor: '#E3D888',
                backgroundColor: 'rgba(227, 216, 136, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#E3D888',
                pointBorderColor: '#1F271B',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(31, 39, 27, 0.9)',
                    titleColor: '#E3D888',
                    bodyColor: '#fff',
                    borderColor: '#E3D888',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Accuracy: ' + context.parsed.y.toFixed(1) + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)',
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: 'rgba(227, 216, 136, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    },
                    grid: {
                        color: 'rgba(227, 216, 136, 0.1)'
                    }
                }
            }
        }
    });
</script>
@endif

@endsection
