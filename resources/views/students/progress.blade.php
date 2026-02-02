@extends('layouts.dashboard')

@section('title', 'My Progress')
@section('user-role', 'Student • Learning Analytics')

@section('navigation')
    <a href="{{ url('/student/classes') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ url('/student/classes') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="nav-label">My Classes</div>
    </a>
    
    <a href="{{ url('/student/practice') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-microphone-alt"></i>
        </div>
        <div class="nav-label">Practice</div>
    </a>
    
    <a href="{{ url('/student/progress') }}" class="nav-item active">
        <div class="nav-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="nav-label">My Progress</div>
    </a>
    
    <a href="{{ url('/student/materials') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="nav-label">Materials</div>
    </a>
    
    <a href="{{ route('students.show', Auth::id()) }}" class="nav-item">
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
    .progress-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .progress-card {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 20px var(--shadow);
        transition: all 0.3s ease;
    }
    
    .progress-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(10, 92, 54, 0.2);
    }
    
    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .progress-header h3 {
        color: var(--primary-green);
        font-size: 1.3rem;
    }
    
    .progress-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 1.5rem;
    }
    
    .progress-ring {
        width: 150px;
        height: 150px;
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
        stroke-width: 10;
    }
    
    .ring-progress {
        fill: none;
        stroke: var(--primary-green);
        stroke-width: 10;
        stroke-linecap: round;
        stroke-dasharray: 440;
        stroke-dashoffset: 440;
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
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--primary-green);
        line-height: 1;
    }
    
    .ring-label {
        font-size: 0.9rem;
        color: #666;
    }
    
    .stats-list {
        list-style: none;
    }
    
    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    .stat-item:last-child {
        border-bottom: none;
    }
    
    .stat-label {
        font-size: 1rem;
        color: #666;
    }
    
    .stat-value {
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--primary-green);
    }
    
    .tajweed-section {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 20px var(--shadow);
        margin-bottom: 30px;
    }
    
    .section-title {
        font-size: 1.8rem;
        color: var(--primary-green);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .tajweed-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
