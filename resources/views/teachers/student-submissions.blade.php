@extends('layouts.dashboard')

@section('title', 'Student Submissions')
@section('user-role', 'Teacher • Grading Queue')

@section('navigation')
    <a href="{{ route('home') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="{{ route('classroom.index') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <a href="{{ route('students.list') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-user-graduate"></i></div>
        <div class="nav-label">My Students</div>
    </a>
    <a href="{{ route('materials.index') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
        <div class="nav-label">Materials</div>
    </a>
@endsection

@section('content')
<!-- Page Header -->
<div style="background: linear-gradient(135deg, #0a5c36, #1abc9c); border-radius: 25px; padding: 40px 50px; margin-bottom: 30px; color: white; position: relative; box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25); border: 3px solid #2a2a2a;">
    <h1 style="font-size: 2.5rem; margin: 0 0 10px 0; font-weight: 700; color: white;">
        <i class="fas fa-clipboard-check"></i> {{ $student->name }}'s Submissions
    </h1>
    <p style="font-size: 1.25rem; margin: 0; opacity: 0.95;">
        {{ $classroom->name }} • Review and grade student work
    </p>
    
    <!-- Back Button -->
    <a href="{{ route('classroom.show', $classroom->id) }}" 
       style="position: absolute; top: 35px; right: 50px; background: white; padding: 12px 25px; border-radius: 12px; color: #0a5c36; text-decoration: none; font-weight: 700; font-size: 1rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2); border: 3px solid #d4af37;"
       onmouseover="this.style.background='#d4af37'; this.style.color='white'; this.style.transform='translateY(-2px)'"
       onmouseout="this.style.background='white'; this.style.color='#0a5c36'; this.style.transform='translateY(0)'">
        <i class="fas fa-arrow-left"></i> Back to Class
    </a>
</div>

@if($submissions->isEmpty())
    <!-- Empty State -->
    <div style="background: white; border-radius: 20px; padding: 80px 40px; text-align: center; box-shadow: 0 8px 20px rgba(0,0,0,0.1); border: 3px solid #e0e0e0;">
        <i class="fas fa-check-double" style="font-size: 5rem; color: rgba(10, 92, 54, 0.2); margin-bottom: 20px;"></i>
        <h3 style="color: #0a5c36; font-size: 1.8rem; margin-bottom: 10px;">No Submissions Yet</h3>
        <p style="color: #666; font-size: 1.25rem; margin: 0;">{{ $student->name }} hasn't submitted any assignments yet.</p>
    </div>
@else
    @php
        // Filter submissions
        $pendingSubmissions = $submissions->filter(function($sub) {
            return in_array($sub->status, ['submitted', 'pending_review']);
        });
        $gradedSubmissions = $submissions->filter(function($sub) {
            return $sub->status === 'graded';
        });
    @endphp

    <!-- 2-Column Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
        
        <!-- LEFT COLUMN: Pending Review -->
        <div>
            <h3 style="color: #f57c00; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; font-size: 1.6rem; font-weight: 700;">
                <i class="fas fa-clock"></i> Pending Review ({{ $pendingSubmissions->count() }})
            </h3>
            
            @if($pendingSubmissions->isNotEmpty())
                <div style="display: grid; gap: 20px;">
                    @foreach($pendingSubmissions as $submission)
                        <div style="background: white; border: 3px solid #ffc107; border-radius: 20px; padding: 30px; box-shadow: 0 8px 25px rgba(255,193,7,0.2); transition: all 0.3s ease;"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 35px rgba(255,193,7,0.3)'"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(255,193,7,0.2)'">
                            
                            <h4 style="color: #0a5c36; margin: 0 0 15px 0; font-size: 1.4rem; font-weight: 700;">
                                📖 {{ $submission->assignment->surah ?? 'Assignment' }}
                                @if($submission->assignment->start_verse)
                                    ({{ $submission->assignment->start_verse }}@if($submission->assignment->end_verse)-{{ $submission->assignment->end_verse }}@endif)
                                @endif
                            </h4>
                            
                            <div style="margin-bottom: 20px; display: grid; gap: 10px;">
                                <div style="color: #666; font-size: 1rem;">
                                    <i class="fas fa-calendar" style="color: #0a5c36; margin-right: 8px;"></i>
                                    Submitted {{ $submission->created_at->format('M d, Y') }}
                                </div>
                                <div style="color: #666; font-size: 1rem;">
                                    <i class="fas fa-clock" style="color: #0a5c36; margin-right: 8px;"></i>
                                    {{ $submission->created_at->diffForHumans() }}
                                </div>
                                @if($submission->audio_file_path)
                                <div style="color: #1abc9c; font-size: 1rem; font-weight: 600;">
                                    <i class="fas fa-microphone" style="margin-right: 8px;"></i>
                                    Voice Recording Available
                                </div>
                                @endif
                            </div>
                            
                            <a href="{{ route('teacher.submission.grade', $submission->id) }}"
                               style="display: block; width: 100%; padding: 16px 30px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 15px; text-decoration: none; font-weight: 700; font-size: 1.15rem; text-align: center; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(10, 92, 54, 0.3); border: none;"
                               onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 30px rgba(10, 92, 54, 0.5)'"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(10, 92, 54, 0.3)'">
                                <i class="fas fa-eye"></i> View Details & Grade
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="background: rgba(255, 193, 7, 0.1); border: 2px dashed #ffc107; border-radius: 20px; padding: 50px; text-align: center;">
                    <i class="fas fa-inbox" style="font-size: 3.5rem; color: #ffc107; opacity: 0.5; margin-bottom: 15px;"></i>
                    <h4 style="color: #f57c00; font-size: 1.3rem; margin-bottom: 8px;">No Pending Submissions</h4>
                    <p style="color: #666; font-size: 1rem; margin: 0;">All submissions have been graded</p>
                </div>
            @endif
        </div>

        <!-- RIGHT COLUMN: Graded Submissions -->
        <div>
            <h3 style="color: #2e7d32; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; font-size: 1.6rem; font-weight: 700;">
                <i class="fas fa-check-circle"></i> Graded Submissions ({{ $gradedSubmissions->count() }})
            </h3>
            
            @if($gradedSubmissions->isNotEmpty())
                <div style="display: grid; gap: 20px;">
                    @foreach($gradedSubmissions as $submission)
                        @php
                            $score = \App\Models\Score::where('user_id', $submission->student_id)
                                ->where('assignment_id', $submission->assignment_id)
                                ->first();
                        @endphp
                        <div style="background: white; border: 3px solid #4caf50; border-radius: 20px; padding: 30px; box-shadow: 0 8px 25px rgba(76,175,80,0.2); transition: all 0.3s ease;"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 35px rgba(76,175,80,0.3)'"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(76,175,80,0.2)'">
                            
                            <h4 style="color: #0a5c36; margin: 0 0 15px 0; font-size: 1.4rem; font-weight: 700;">
                                📖 {{ $submission->assignment->surah ?? 'Assignment' }}
                                @if($submission->assignment->start_verse)
                                    ({{ $submission->assignment->start_verse }}@if($submission->assignment->end_verse)-{{ $submission->assignment->end_verse }}@endif)
                                @endif
                            </h4>
                            
                            <div style="margin-bottom: 20px; display: grid; gap: 10px;">
                                <div style="color: #666; font-size: 1rem;">
                                    <i class="fas fa-calendar" style="color: #0a5c36; margin-right: 8px;"></i>
                                    Submitted {{ $submission->created_at->format('M d, Y') }}
                                </div>
                                @if($score)
                                <div style="background: rgba(76, 175, 80, 0.15); border: 2px solid #4caf50; border-radius: 12px; padding: 15px; margin-top: 5px;">
                                    <div style="color: #2e7d32; font-weight: 700; font-size: 1.3rem;">
                                        <i class="fas fa-star" style="color: #d4af37;"></i> 
                                        Score: {{ $score->score }}/{{ $submission->assignment->total_marks }}
                                        <span style="font-size: 1rem; opacity: 0.8; margin-left: 8px;">
                                            ({{ round(($score->score / $submission->assignment->total_marks) * 100, 1) }}%)
                                        </span>
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <a href="{{ route('teacher.submission.grade', $submission->id) }}"
                               style="display: block; width: 100%; padding: 16px 30px; background: white; color: #0a5c36; border: 3px solid #0a5c36; border-radius: 15px; text-decoration: none; font-weight: 700; font-size: 1.15rem; text-align: center; transition: all 0.3s ease;"
                               onmouseover="this.style.background='#0a5c36'; this.style.color='white'; this.style.transform='translateY(-3px)'"
                               onmouseout="this.style.background='white'; this.style.color='#0a5c36'; this.style.transform='translateY(0)'">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="background: rgba(76, 175, 80, 0.1); border: 2px dashed #4caf50; border-radius: 20px; padding: 50px; text-align: center;">
                    <i class="fas fa-clipboard-check" style="font-size: 3.5rem; color: #4caf50; opacity: 0.5; margin-bottom: 15px;"></i>
                    <h4 style="color: #2e7d32; font-size: 1.3rem; margin-bottom: 8px;">No Graded Submissions</h4>
                    <p style="color: #666; font-size: 1rem; margin: 0;">Graded submissions will appear here</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        @media (max-width: 1200px) {
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endif
@endsection

@section('extra-scripts')
<script>
    console.log('Student Submissions Page Loaded');
    console.log('Student: {{ $student->name }}');
    console.log('Classroom: {{ $classroom->name }}');
    console.log('Classroom ID: {{ $classroom->id }}');
    console.log('Total Submissions: {{ $submissions->count() }}');
    
    // Test Back Button URL
    const backButtonUrl = '{{ url("/teacher/classroom/" . $classroom->id) }}';
    console.log('Back button URL:', backButtonUrl);
</script>
@endsection
