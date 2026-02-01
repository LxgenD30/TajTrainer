@extends('layouts.template')

@section('page-title', 'My Progress')
@section('page-subtitle', 'Track your Tajweed learning journey and identify areas for improvement')

@section('content')
<style>
    .progress-container {
        padding: 25px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: rgba(31, 39, 27, 0.6);
        border: 2px solid var(--color-dark-green);
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
        color: var(--color-gold);
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
        border: 2px solid var(--color-dark-green);
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 25px;
    }

    .chart-title {
        font-size: 1.3rem;
        color: var(--color-gold);
        font-family: 'Amiri', serif;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(227, 216, 136, 0.2);
    }

    .weakness-item {
        background: rgba(31, 39, 27, 0.5);
        border: 1px solid rgba(227, 216, 136, 0.2);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .weakness-name {
        font-size: 1rem;
        color: var(--color-gold);
        font-weight: 600;
        font-family: 'Cairo', sans-serif;
    }

    .weakness-stats {
        text-align: right;
    }

    .weakness-rate {
        font-size: 1.2rem;
        color: #ff6b6b;
        font-weight: bold;
    }

    .weakness-count {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
    }

    .improvement-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-left: 10px;
    }

    .badge-positive {
        background: rgba(81, 212, 136, 0.2);
        color: #51d488;
        border: 1px solid #51d488;
    }

    .badge-negative {
        background: rgba(255, 107, 107, 0.2);
        color: #ff6b6b;
        border: 1px solid #ff6b6b;
    }

    .no-data-message {
        text-align: center;
        padding: 40px;
        color: rgba(255, 255, 255, 0.5);
        font-size: 1.1rem;
    }

    .grid-2col {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 25px;
        margin-bottom: 25px;
    }
</style>

<div class="progress-container">
    <!-- Overall Stats -->
    <div class="stats-grid">
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
            <div class="stat-label">Correct Pronunciations</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $overallProgress['error_count'] }}</div>
            <div class="stat-label">Errors Made</div>
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
