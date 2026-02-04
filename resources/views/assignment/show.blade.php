@extends('layouts.dashboard')

@section('title', 'Assignment Details')
@section('user-role', (auth()->user()->role_id == 3 ? 'Teacher' : 'Student') . ' • Assignment Details')

@section('navigation')
    @if(auth()->user()->role_id == 3)
        @include('partials.teacher-nav')
    @else
        @include('partials.student-nav')
    @endif
@endsection

@section('content')
<style>
    .page-header {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-header h1 {
        color: white;
        font-size: 2rem;
        margin: 0;
    }
    
    .header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    
    .btn {
        height: 45px;
        padding: 0 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 3px solid #000000;
        white-space: nowrap;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.95); /* Slight transparency */
        color: #0a5c36;
        border: 3px solid #000000;
    }

    .btn-edit {
        background: #d4af37;
        color: #0a5c36;
        border: 3px solid #000000;
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
        border: 3px solid #000000;
        /* Matches button height for form submission */
        font-family: inherit;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        opacity: 0.9;
    }    .detail-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .detail-section {
        margin-bottom: 30px;
        padding: 25px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        border: 2px solid #0a5c36;
    }
    
    .detail-section.gold {
        background: rgba(212, 175, 55, 0.1);
        border-color: #d4af37;
    }
    
    .section-title {
        color: #0a5c36;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title.gold {
        color: #d4af37;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
    }
    
    .info-label {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }
    
    .info-value {
        color: #0a5c36;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .rules-container {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .rule-badge {
        display: inline-block;
        background: rgba(212, 175, 55, 0.2);
        color: #d4af37;
        padding: 6px 15px;
        border-radius: 15px;
        font-size: 0.9rem;
        font-weight: 600;
        border: 2px solid #d4af37;
    }
    
    .material-info {
        padding: 20px;
        background: rgba(26, 188, 156, 0.1);
        border-radius: 10px;
        border: 2px solid #1abc9c;
    }
    
    .material-title {
        color: #1abc9c;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .material-links {
        display: flex;
        gap: 10px;
    }
    
    .material-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #0a5c36;
        color: #d4af37;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .material-link:hover {
        background: #064e32;
    }
    
    .instructions-box {
        background: rgba(10, 92, 54, 0.1);
        padding: 20px;
        border-radius: 10px;
        border: 2px solid #0a5c36;
    }
    
    .instructions-text {
        color: #333;
        line-height: 1.6;
        white-space: pre-wrap;
    }
    
    .submission-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: rgba(212, 175, 55, 0.15);
        border: 2px solid #d4af37;
        border-radius: 20px;
        color: #d4af37;
        font-weight: 600;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h1>📝 Assignment Details</h1>
    <div class="header-actions">
        @if(auth()->user()->role_id == 2)
            {{-- Student Status --}}
            @if(isset($submission))
                <span class="btn" style="background: #00d4ff; color: #000000; border: 3px solid #000000; cursor: default; font-weight: 700;">
                    <i class="fas fa-check-circle" style="color: #006b00;"></i> Completed
                </span>
            @else
                <span class="btn" style="background: #ffaa00; color: #000000; border: 3px solid #000000; cursor: default; font-weight: 700;">
                    <i class="fas fa-clock" style="color: #8b4500;"></i> Pending
                </span>
            @endif
        @else
            {{-- Teacher Status --}}
            @if($notSubmittedStudents->isEmpty())
                <span class="btn" style="background: #00d4ff; color: #000000; border: 3px solid #000000; cursor: default; font-weight: 700;">
                    <i class="fas fa-check-double" style="color: #006b00;"></i> All Submitted
                </span>
            @else
                <span class="btn" style="background: #ffaa00; color: #000000; border: 3px solid #000000; cursor: default; font-weight: 700;">
                    <i class="fas fa-tasks" style="color: #8b4500;"></i> In Progress
                </span>
            @endif
        @endif

        <a href="{{ route('classroom.show', $classroom->id) }}" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Back
        </a>

        {{-- Teacher Actions --}}
        @if(auth()->user()->role_id == 3)
            <a href="{{ route('assignment.edit', $assignment->assignment_id) }}" class="btn btn-edit">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('assignment.destroy', $assignment->assignment_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Assignment Details Card -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
    
    <!-- LEFT COLUMN: Assignment Details -->
    <div class="detail-card">
    <!-- Quran Verse Section -->
    <div class="detail-section gold">
        <h4 class="section-title gold" style="font-size: 1.4rem;">
            <span>📖</span> Assigned Quran Verse
        </h4>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label" style="font-size: 1rem;">Surah</div>
                <div class="info-value" style="font-size: 1.3rem;">{{ $assignment->surah }}</div>
            </div>
            <div class="info-item">
                <div class="info-label" style="font-size: 1rem;">Ayat From</div>
                <div class="info-value" style="font-size: 1.3rem;">{{ $assignment->start_verse }}</div>
            </div>
            <div class="info-item">
                <div class="info-label" style="font-size: 1rem;">Ayat To</div>
                <div class="info-value" style="font-size: 1.3rem;">{{ $assignment->end_verse ?? 'N/A' }}</div>
            </div>
        </div>
        
        @if($assignment->tajweed_rules && count($assignment->tajweed_rules) > 0)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid rgba(0, 0, 0, 0.2);">
                <div class="info-label" style="margin-bottom: 10px; font-size: 1rem;">✨ Focus on Tajweed Rules:</div>
                <div class="rules-container">
                    @foreach($assignment->tajweed_rules as $rule)
                        <span class="rule-badge" style="font-size: 1rem;">{{ $rule }}</span>
                    @endforeach
                </div>
            </div>
        @endif
        
        @if($assignment->expected_recitation)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid rgba(0, 0, 0, 0.2);">
                <div class="info-label" style="margin-bottom: 10px; font-size: 1rem;">📝 Expected Arabic Text:</div>
                <div style="background: rgba(0, 0, 0, 0.05); padding: 20px; border-radius: 10px; direction: rtl; text-align: center; font-size: 2rem; font-weight: bold; color: #000000; font-family: 'Amiri', serif; line-height: 2.8;">
                    {{ $assignment->expected_recitation }}
                </div>
            </div>
        @endif
        
        @if($assignment->reference_audio_url)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid rgba(212, 175, 55, 0.2);">
                <div class="info-label" style="margin-bottom: 10px; font-size: 1rem;">🎧 Reference Audio (Sheikh Alafasy):</div>
                <div style="margin-bottom: 15px;">
                    @if($assignment->start_verse != $assignment->end_verse)
                        <p style="font-size: 0.9rem; color: #000000; margin-bottom: 5px; font-weight: 600;">
                            Verses {{ $assignment->start_verse }} - {{ $assignment->end_verse }} (Continuous Recitation)
                        </p>
                    @endif
                    <audio controls {{ auth()->user()->role_id != 3 ? 'controlsList="nodownload"' : '' }} style="width: 100%; border-radius: 10px;">
                        @if(str_starts_with($assignment->reference_audio_url, 'references/'))
                            <source src="{{ Storage::url($assignment->reference_audio_url) }}" type="audio/mpeg">
                        @else
                            <source src="{{ $assignment->reference_audio_url }}" type="audio/mpeg">
                        @endif
                        Your browser does not support the audio element.
                    </audio>
                </div>
                <p style="font-size: 0.9rem; color: #000000; margin-top: 10px; opacity: 0.7;">
                    Listen to the correct pronunciation before recording your own recitation.
                </p>
            </div>
        @endif
    </div>

    <!-- Reference Materials -->
    @if($assignment->material)
        <div class="detail-section">
            <h4 class="section-title" style="font-size: 1.4rem;">
                <span>📚</span> Reference Materials
            </h4>
            <div class="material-info">
                <p class="material-title" style="font-size: 1.2rem;">{{ $assignment->material->title }}</p>
                
                <div class="material-links">
                    @if($assignment->material->file_path)
                        <a href="{{ Storage::url($assignment->material->file_path) }}" target="_blank" class="material-link" style="font-size: 1rem;">
                            📄 Download PDF
                        </a>
                    @endif
                    
                    @if($assignment->material->video_link)
                        <a href="{{ $assignment->material->video_link }}" target="_blank" class="material-link" style="font-size: 1rem;">
                            🎥 Watch Video
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Assignment Info -->
    <div class="info-grid" style="margin-top: 30px;">
        <div class="info-item">
            <div class="info-label" style="font-size: 1rem;">📅 Due Date</div>
            <div class="info-value" style="font-size: 1.2rem;">{{ $assignment->due_date->format('F d, Y h:i A') }}</div>
        </div>
        <div class="info-item">
            <div class="info-label" style="font-size: 1rem;">🎯 Total Marks</div>
            <div class="info-value" style="font-size: 1.2rem;">{{ $assignment->total_marks }} points</div>
        </div>
        <div class="info-item">
            <div class="info-label" style="font-size: 1rem;">🎤 Submission Type</div>
            <div class="info-value" style="font-size: 1.2rem;">
                <span class="submission-badge" style="font-size: 1rem;">
                    @if($assignment->is_voice_submission)
                        🎤 Voice Recording
                    @else
                        📝 Text Submission
                    @endif
                </span>
            </div>
        </div>
    </div>
    </div> <!-- End LEFT COLUMN -->
    
    <!-- RIGHT COLUMN: Instructions + Your Submission -->
    <div style="display: flex; flex-direction: column; gap: 30px;">
    
    <!-- Instructions -->
    <div class="detail-card">
        <div class="detail-section">
            <h4 class="section-title" style="font-size: 1.4rem;">
                <span>📋</span> Instructions
            </h4>
            <div class="instructions-box">
                <p class="instructions-text" style="font-size: 1rem; line-height: 1.8;">{{ $assignment->instructions }}</p>
            </div>
        </div>
    </div>
    
    <!-- Your Submission -->
    <div class="detail-card">
    @if(auth()->user()->role_id == 3)
        <!-- Teacher View: Student Submission List -->
        <div class="detail-section">
            <h4 class="section-title" style="font-size: 1.4rem; margin-bottom: 20px;">
                <span>📋</span> Student Submissions Overview
            </h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="background: rgba(46, 204, 113, 0.1); border: 2px solid #27ae60; border-radius: 12px; padding: 20px;">
                    <h5 style="color: #27ae60; font-size: 1.2rem; font-weight: 700; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-check-circle"></i> Submitted ({{ $submissions->count() }})
                    </h5>
                    <div style="max-height: 400px; overflow-y: auto;">
                        @if($submissions->isEmpty())
                            <p style="color: #999; font-size: 1rem; text-align: center; padding: 20px;">No submissions yet</p>
                        @else
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                @foreach($submissions as $submission)
                                    <li style="padding: 12px 15px; margin-bottom: 8px; background: white; border-radius: 8px; border: 1px solid #27ae60; display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-user-check" style="color: #27ae60; font-size: 1.1rem;"></i>
                                        <span style="flex: 1; color: #333; font-size: 1.05rem;">{{ $submission->student->name ?? 'Unknown' }}</span>
                                        @if($submission->score)
                                            <span style="color: #d4af37; font-weight: 600; font-size: 1rem;">{{ $submission->score->score }}/{{ $assignment->total_marks }}</span>
                                        @else
                                            <span style="color: #999; font-size: 0.9rem; font-style: italic;">Not graded</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div style="background: rgba(231, 76, 60, 0.1); border: 2px solid #e74c3c; border-radius: 12px; padding: 20px;">
                    <h5 style="color: #e74c3c; font-size: 1.2rem; font-weight: 700; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-exclamation-circle"></i> Due ({{ $notSubmittedStudents->count() }})
                    </h5>
                    <div style="max-height: 400px; overflow-y: auto;">
                        @if($notSubmittedStudents->isEmpty())
                            <p style="color: #999; font-size: 1rem; text-align: center; padding: 20px;">All students submitted</p>
                        @else
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                @foreach($notSubmittedStudents as $student)
                                    <li style="padding: 12px 15px; margin-bottom: 8px; background: white; border-radius: 8px; border: 1px solid #e74c3c; display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-user-times" style="color: #e74c3c; font-size: 1.1rem;"></i>
                                        <span style="flex: 1; color: #333; font-size: 1.05rem;">{{ $student->name ?? 'Unknown' }}</span>
                                        <span style="color: #e74c3c; font-size: 0.9rem; font-weight: 600;">Pending</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    @elseif(auth()->user()->role_id == 2 && isset($submission))
        <!-- Student View: Their Own Submission -->
        <div class="detail-section" style="background: rgba(46, 204, 113, 0.1); border-color: #27ae60;">
            <h4 class="section-title" style="color: #27ae60; font-size: 1.4rem;">
                <span>✅</span> Your Submission
            </h4>
            
            <div class="info-grid" style="margin-bottom: 25px;">
                <div class="info-item">
                    <div class="info-label" style="font-size: 1rem;">📅 Submitted At</div>
                    <div class="info-value" style="color: #27ae60; font-size: 1.2rem;">{{ $submission->submitted_at ? $submission->submitted_at->format('F d, Y h:i A') : 'Processing...' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label" style="font-size: 1rem;">📊 Status</div>
                    <div class="info-value" style="color: #27ae60; font-size: 1.2rem;">
                        <span class="submission-badge" style="background: rgba(46, 204, 113, 0.2); border-color: #27ae60; color: #27ae60; font-size: 1rem;">
                            {{ ucfirst($submission->status ?? 'pending') }}
                        </span>
                    </div>
                </div>
                @if($submission->score)
                    <div class="info-item">
                        <div class="info-label" style="font-size: 1rem;">🎯 Score</div>
                        <div class="info-value" style="color: #d4af37; font-size: 1.2rem;">{{ $submission->score->score ?? 'Not graded' }} / {{ $assignment->total_marks }}</div>
                    </div>
                @endif
            </div>

            @if($submission->audio_file_path)
                <div style="margin-bottom: 25px;">
                    <div class="info-label" style="margin-bottom: 12px; font-size: 1rem;">🎤 Your Recording</div>
                    <audio controls style="width: 100%; border-radius: 10px;">
                        <source src="{{ Storage::url($submission->audio_file_path) }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            @endif

            @if($submission->transcription)
                <div style="margin-bottom: 25px;">
                    <div class="info-label" style="margin-bottom: 12px; font-size: 1rem;">📝 Transcription</div>
                    <div class="instructions-box">
                        <p class="instructions-text" style="font-size: 1.25rem; font-family: 'Amiri', serif; direction: rtl; text-align: right;">{{ $submission->transcription }}</p>
                    </div>
                </div>
            @endif

            @if($submission->tajweed_analysis && is_array($submission->tajweed_analysis))
                <div>
                    <div class="info-label" style="margin-bottom: 12px; font-size: 1rem;">🎯 Tajweed Analysis</div>
                    <div style="background: white; padding: 25px; border-radius: 10px; border: 2px solid #27ae60;">
                        @foreach($submission->tajweed_analysis as $key => $value)
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #0a5c36; font-size: 1rem;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                <span style="font-size: 1rem;">{{ is_array($value) ? json_encode($value) : $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- No Submission Yet - Placeholder -->
        <div class="detail-section" style="background: rgba(149, 165, 166, 0.1); border-color: #95a5a6; text-align: center; padding: 60px 30px;">
            <div style="font-size: 5rem; margin-bottom: 20px; opacity: 0.4;">📝</div>
            <h4 style="color: #7f8c8d; font-size: 1.3rem; margin-bottom: 15px;">No Submission Yet</h4>
            <p style="color: #95a5a6; font-size: 1rem; line-height: 1.6; margin-bottom: 25px;">
                @if(auth()->user()->role_id == 2)
                    Start recording your recitation to submit this assignment.
                @else
                    Waiting for student submission.
                @endif
            </p>
            @if(auth()->user()->role_id == 2)
                <a href="{{ route('student.assignment.submit', $assignment->assignment_id) }}" 
                   style="display: inline-block; padding: 14px 30px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 1.25rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(10, 92, 54, 0.3);">
                    🎤 Start Recording
                </a>
            @endif
        </div>
    @endif
    </div>
    
</div> 

@endsection
