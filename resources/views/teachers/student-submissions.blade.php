@extends('layouts.dashboard')

@section('title', 'Student Submissions')
@section('user-role', 'Teacher • Grading Queue')

@section('navigation')
    <a href="{{ route('home') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ route('classroom.index') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="nav-label">My Classes</div>
    </a>
    
    <a href="{{ route('students.list') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="nav-label">My Students</div>
    </a>
    
    <a href="{{ route('materials.index') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="nav-label">Materials</div>
    </a>
@endsection

@section('extra-styles')
<style>
    .submissions-header {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        color: var(--white);
        box-shadow: 0 8px 20px var(--shadow);
        position: relative;
        overflow: hidden;
    }
    
    .submissions-header:before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    }
    
    .submissions-header h2 {
        color: var(--white);
        font-size: 2rem;
        margin-bottom: 10px;
        position: relative;
        z-index: 2;
    }
    
    .submissions-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }
    
    .submission-card {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 8px 20px var(--shadow);
        transition: all 0.3s ease;
    }
    
    .submission-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(10, 92, 54, 0.2);
    }
    
    .submission-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .student-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .student-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 1.3rem;
        font-weight: 600;
    }
    
    .student-details h3 {
        color: var(--primary-green);
        font-size: 1.3rem;
        margin-bottom: 5px;
    }
    
    .student-details p {
        color: #666;
        font-size: 0.9rem;
    }
    
    .submission-status {
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        font-family: 'El Messiri', sans-serif;
    }
    
    .status-pending {
        background: rgba(255, 152, 0, 0.1);
        color: #ff9800;
    }
    
    .status-graded {
        background: rgba(76, 175, 80, 0.1);
        color: #4caf50;
    }
    
    .assignment-info {
        background: rgba(10, 92, 54, 0.05);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .assignment-info h4 {
        color: var(--primary-green);
        font-size: 1.2rem;
        margin-bottom: 10px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #666;
        font-size: 0.95rem;
    }
    
    .info-item i {
        color: var(--primary-green);
        font-size: 1.1rem;
    }
    
    .audio-player {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(46, 139, 87, 0.05));
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .audio-player h5 {
        color: var(--primary-green);
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    
    .audio-controls {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .play-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
        border: none;
        font-size: 1.3rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .play-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.3);
    }
    
    .audio-timeline {
        flex: 1;
        height: 8px;
        background: rgba(10, 92, 54, 0.1);
        border-radius: 10px;
        position: relative;
        cursor: pointer;
    }
    
    .audio-progress {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-green), var(--light-green));
        border-radius: 10px;
        width: 0%;
        transition: width 0.1s linear;
    }
    
    .tajweed-errors {
        margin-bottom: 20px;
    }
    
    .tajweed-errors h5 {
        color: var(--primary-green);
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    
    .error-list {
        display: grid;
        gap: 12px;
    }
    
    .error-item {
        background: rgba(231, 76, 60, 0.05);
        border-left: 4px solid #e74c3c;
        border-radius: 10px;
        padding: 15px;
    }
    
    .error-item h6 {
        color: #e74c3c;
        font-size: 1rem;
        margin-bottom: 5px;
    }
    
    .error-item p {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .grading-form {
        background: rgba(212, 175, 55, 0.05);
        border-radius: 15px;
        padding: 25px;
        margin-top: 20px;
    }
    
    .grading-form h5 {
        color: var(--primary-green);
        margin-bottom: 20px;
        font-size: 1.1rem;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        color: var(--primary-green);
        font-weight: 600;
        margin-bottom: 8px;
        font-family: 'El Messiri', sans-serif;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid rgba(10, 92, 54, 0.2);
        border-radius: 10px;
        font-family: 'Amiri', serif;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.1);
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .btn-submit-grade {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        font-family: 'El Messiri', sans-serif;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-submit-grade:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.3);
    }
    
    .empty-submissions {
        background: var(--white);
        border-radius: 20px;
        padding: 80px 40px;
        text-align: center;
        box-shadow: 0 8px 20px var(--shadow);
    }
    
    .empty-submissions i {
        font-size: 6rem;
        color: rgba(10, 92, 54, 0.2);
        margin-bottom: 20px;
    }
    
    .empty-submissions h3 {
        color: var(--primary-green);
        font-size: 1.8rem;
        margin-bottom: 10px;
    }
    
    .empty-submissions p {
        color: #666;
        font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
        .submission-header {
            flex-direction: column;
            gap: 15px;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Submissions Header with Welcome Banner Style -->
<div style="background: linear-gradient(135deg, #0a5c36, #1abc9c); border-radius: 25px; padding: 40px; margin-bottom: 30px; color: #ffffff; position: relative; overflow: hidden; box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25); border: 3px solid #2a2a2a;">
    <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url(\"data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E\"); opacity: 0.4;"></div>
    <div style="position: relative; z-index: 2;">
        <h1 style="font-size: 2.5rem; margin-bottom: 10px; font-weight: 700; color: #ffffff; text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);">
            <i class="fas fa-clipboard-check"></i> {{ $student->name }}'s Submissions
        </h1>
        <p style="font-size: 1.1rem; opacity: 0.95; line-height: 1.6; margin: 0;">
            {{ $classroom->name }} • Review and grade student work
        </p>
    </div>
    <a href="{{ route('classroom.show', $classroom) }}" style="position: absolute; top: 30px; right: 30px; background: rgba(255,255,255,0.25); padding: 12px 25px; border-radius: 15px; color: white; text-decoration: none; font-weight: 600; border: 2px solid rgba(255,255,255,0.3); transition: all 0.3s ease; backdrop-filter: blur(10px);" onmouseover="this.style.background='rgba(255,255,255,0.35)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(0)'">
        <i class="fas fa-arrow-left"></i> Back to Class
    </a>
</div>

@if($submissions->isEmpty())
    <div class="empty-submissions">
        <i class="fas fa-check-double"></i>
        <h3>No Submissions Yet</h3>
        <p>{{ $student->name }} hasn't submitted any assignments yet.</p>
    </div>
@else
    <!-- Pending Review Section -->
    @php
        $pendingSubmissions = $submissions->filter(function($sub) {
            return $sub->status !== 'graded';
        });
        $gradedSubmissions = $submissions->filter(function($sub) {
            return $sub->status === 'graded';
        });
    @endphp

    @if($pendingSubmissions->isNotEmpty())
    <div style="background: rgba(255, 193, 7, 0.1); border: 2px solid #ffc107; border-radius: 15px; padding: 25px; margin-bottom: 30px;">
        <h3 style="color: #f57c00; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-clock"></i> Pending Review ({{ $pendingSubmissions->count() }})
        </h3>
        <div style="display: grid; gap: 15px;">
            @foreach($pendingSubmissions as $submission)
                <div style="background: white; border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'">
                    <div style="flex: 1;">
                        <h4 style="color: #0a5c36; margin-bottom: 8px; font-size: 1.1rem;">
                            📖 {{ $submission->assignment->surah ?? 'Assignment' }} 
                            @if($submission->assignment->start_verse)
                                ({{ $submission->assignment->start_verse }}@if($submission->assignment->end_verse)-{{ $submission->assignment->end_verse }}@endif)
                            @endif
                        </h4>
                        <div style="display: flex; gap: 20px; color: #666; font-size: 0.9rem;">
                            <span><i class="fas fa-calendar"></i> Submitted {{ $submission->created_at->format('M d, Y') }}</span>
                            <span><i class="fas fa-clock"></i> {{ $submission->created_at->diffForHumans() }}</span>
                            @if($submission->audio_file_path)
                                <span><i class="fas fa-microphone"></i> Voice Recording</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('teacher.submission.grade', $submission->id) }}" 
                        style="padding: 12px 25px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 10px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(10, 92, 54, 0.3);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(10, 92, 54, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(10, 92, 54, 0.3)'">
                        <i class="fas fa-eye"></i> View Details & Grade
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Graded Submissions Section -->
    @if($gradedSubmissions->isNotEmpty())
    <div style="background: rgba(76, 175, 80, 0.1); border: 2px solid #4caf50; border-radius: 15px; padding: 25px;">
        <h3 style="color: #2e7d32; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle"></i> Graded Submissions ({{ $gradedSubmissions->count() }})
        </h3>
        <div style="display: grid; gap: 15px;">
            @foreach($gradedSubmissions as $submission)
                @php
                    $score = \App\Models\Score::where('user_id', $submission->student_id)
                        ->where('assignment_id', $submission->assignment_id)
                        ->first();
                @endphp
                <div style="background: white; border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'">
                    <div style="flex: 1;">
                        <h4 style="color: #0a5c36; margin-bottom: 8px; font-size: 1.1rem;">
                            📖 {{ $submission->assignment->surah ?? 'Assignment' }} 
                            @if($submission->assignment->start_verse)
                                ({{ $submission->assignment->start_verse }}@if($submission->assignment->end_verse)-{{ $submission->assignment->end_verse }}@endif)
                            @endif
                        </h4>
                        <div style="display: flex; gap: 20px; color: #666; font-size: 0.9rem;">
                            <span><i class="fas fa-calendar"></i> Submitted {{ $submission->created_at->format('M d, Y') }}</span>
                            @if($score)
                                <span style="color: #4caf50; font-weight: 600;">
                                    <i class="fas fa-star"></i> Score: {{ $score->score }}/{{ $submission->assignment->total_marks }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('teacher.submission.grade', $submission->id) }}" 
                        style="padding: 12px 25px; background: white; color: #0a5c36; border: 2px solid #0a5c36; border-radius: 10px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease;"
                        onmouseover="this.style.background='#0a5c36'; this.style.color='white'"
                        onmouseout="this.style.background='white'; this.style.color='#0a5c36'">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
@endif
@endsection

@section('extra-scripts')
<script>
    console.log('=== STUDENT SUBMISSIONS PAGE DEBUG ===');
    console.log('✓ Student submissions page loaded');
    console.log('Classroom: {{ $classroom->name }}');
    console.log('Student: {{ $student->name }}');
    console.log('Total submissions: {{ $submissions->count() }}');
    
    @php
        try {
            $pendingCount = $pendingSubmissions->count() ?? 0;
            $gradedCount = $gradedSubmissions->count() ?? 0;
            echo "console.log('Pending review: {$pendingCount}');";
            echo "console.log('Graded: {$gradedCount}');";
        } catch (\Exception $e) {
            echo "console.error('Error counting submissions: " . addslashes($e->getMessage()) . "');";
        }
    @endphp
    
    console.log('Testing score accessor on each submission...');
    @foreach($submissions as $index => $submission)
        try {
            console.log('Submission {{ $index }}: ID={{ $submission->id }}, Assignment={{ $submission->assignment_id }}');
            @php
                try {
                    $testScore = $submission->score;
                    $scoreInfo = $testScore ? "Has score: {$testScore->score}" : "No score";
                    echo "console.log('  ✓ Score accessor works: {$scoreInfo}');";
                } catch (\Exception $e) {
                    echo "console.error('  ✗ Score accessor failed: " . addslashes($e->getMessage()) . "');";
                }
            @endphp
        } catch(e) {
            console.error('  ✗ JavaScript error:', e);
        }
    @endforeach
    
    console.log('=== DEBUG END ===');
</script>
@endsection
