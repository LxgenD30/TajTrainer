@extends('layouts.dashboard')

@section('title', 'Assignment Details')
@section('user-role', (auth()->user()->role_id == 3 ? 'Teacher' : 'Student') . ' • Assignment Details')

@section('navigation')
    <a href="{{ route('home') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="{{ route('classroom.index') }}" class="nav-item active">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    @if(auth()->user()->role_id == 3)
    <a href="{{ route('students.list') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-user-graduate"></i></div>
        <div class="nav-label">My Students</div>
    </a>
    @endif
    <a href="{{ route('materials.index') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
        <div class="nav-label">Materials</div>
    </a>
    @if(auth()->user()->role_id == 2)
    <a href="{{ route('student.practice') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-microphone"></i></div>
        <div class="nav-label">Practice</div>
    </a>
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
        gap: 10px;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 2px solid;
    }
    
    .btn-edit {
        background: #d4af37;
        color: #0a5c36;
        border-color: #d4af37;
    }
    
    .btn-edit:hover {
        background: rgba(212, 175, 55, 0.8);
    }
    
    .btn-delete {
        background: #e74c3c;
        color: white;
        border-color: #e74c3c;
    }
    
    .btn-delete:hover {
        background: #c0392b;
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        color: #1abc9c;
        border: 2px solid #1abc9c;
        padding: 12px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-top: 30px;
    }
    
    .btn-back:hover {
        background: rgba(26, 188, 156, 0.1);
    }
    
    .detail-card {
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
        @if(auth()->user()->role_id == 3)
            <a href="{{ route('assignment.edit', $assignment->assignment_id) }}" class="btn btn-edit">
                ✏️ Edit
            </a>
            <form action="{{ route('assignment.destroy', $assignment->assignment_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">
                    🗑️ Delete
                </button>
            </form>
        @else
            @if(isset($submission))
                <span class="btn" style="background: #95a5a6; color: white; border-color: #95a5a6; cursor: default;">
                    ✅ Submitted
                </span>
            @else
                <a href="{{ route('student.assignment.submit', $assignment->assignment_id) }}" class="btn" style="background: #95a5a6; color: white; border-color: #95a5a6;">
                    🎤 Attempt Assignment
                </a>
            @endif
        @endif
    </div>
</div>

<!-- Assignment Details Card -->
<div class="detail-card">
    <!-- Quran Verse Section -->
    <div class="detail-section gold">
        <h4 class="section-title gold">
            <span>📖</span> Assigned Quran Verse
        </h4>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Surah</div>
                <div class="info-value">{{ $assignment->surah }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Ayat From</div>
                <div class="info-value">{{ $assignment->start_verse }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Ayat To</div>
                <div class="info-value">{{ $assignment->end_verse ?? 'N/A' }}</div>
            </div>
        </div>
        
        @if($assignment->tajweed_rules && count($assignment->tajweed_rules) > 0)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid rgba(212, 175, 55, 0.2);">
                <div class="info-label" style="margin-bottom: 10px;">✨ Focus on Tajweed Rules:</div>
                <div class="rules-container">
                    @foreach($assignment->tajweed_rules as $rule)
                        <span class="rule-badge">{{ $rule }}</span>
                    @endforeach
                </div>
            </div>
        @endif
        
        @if($assignment->expected_recitation)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid rgba(212, 175, 55, 0.2);">
                <div class="info-label" style="margin-bottom: 10px;">📝 Expected Arabic Text:</div>
                <div style="background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 10px; direction: rtl; text-align: center; font-size: 1.5rem; font-weight: bold; color: #d4af37;">
                    {{ $assignment->expected_recitation }}
                </div>
            </div>
        @endif
        
        @if($assignment->reference_audio_url)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid rgba(212, 175, 55, 0.2);">
                <div class="info-label" style="margin-bottom: 10px;">🎧 Reference Audio (Sheikh Alafasy):</div>
                <audio controls style="width: 100%; border-radius: 10px;">
                    <source src="{{ $assignment->reference_audio_url }}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
                <p style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.6); margin-top: 10px;">
                    Listen to the correct pronunciation before recording your own recitation.
                </p>
            </div>
        @endif
    </div>

    <!-- Reference Materials -->
    @if($assignment->material)
        <div class="detail-section">
            <h4 class="section-title">
                <span>📚</span> Reference Materials
            </h4>
            <div class="material-info">
                <p class="material-title">{{ $assignment->material->title }}</p>
                
                <div class="material-links">
                    @if($assignment->material->file_path)
                        <a href="{{ Storage::url($assignment->material->file_path) }}" target="_blank" class="material-link">
                            📄 Download PDF
                        </a>
                    @endif
                    
                    @if($assignment->material->video_link)
                        <a href="{{ $assignment->material->video_link }}" target="_blank" class="material-link">
                            🎥 Watch Video
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Student Submission Section -->
    @if(auth()->user()->role_id == 2 && isset($submission))
        <div class="detail-section" style="background: rgba(46, 204, 113, 0.1); border-color: #27ae60;">
            <h4 class="section-title" style="color: #27ae60;">
                <span>✅</span> Your Submission
            </h4>
            
            <div class="info-grid" style="margin-bottom: 20px;">
                <div class="info-item">
                    <div class="info-label">📅 Submitted At</div>
                    <div class="info-value" style="color: #27ae60;">{{ $submission->submitted_at ? $submission->submitted_at->format('F d, Y h:i A') : 'Processing...' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">📊 Status</div>
                    <div class="info-value" style="color: #27ae60;">
                        <span class="submission-badge" style="background: rgba(46, 204, 113, 0.2); border-color: #27ae60; color: #27ae60;">
                            {{ ucfirst($submission->status ?? 'pending') }}
                        </span>
                    </div>
                </div>
                @if($submission->score)
                    <div class="info-item">
                        <div class="info-label">🎯 Score</div>
                        <div class="info-value" style="color: #d4af37;">{{ $submission->score->score ?? 'Not graded' }} / {{ $assignment->total_marks }}</div>
                    </div>
                @endif
            </div>

            @if($submission->audio_file_path)
                <div style="margin-bottom: 20px;">
                    <div class="info-label" style="margin-bottom: 10px;">🎤 Your Recording</div>
                    <audio controls style="width: 100%; border-radius: 10px;">
                        <source src="{{ Storage::url($submission->audio_file_path) }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            @endif

            @if($submission->transcription)
                <div style="margin-bottom: 20px;">
                    <div class="info-label" style="margin-bottom: 10px;">📝 Transcription</div>
                    <div class="instructions-box">
                        <p class="instructions-text">{{ $submission->transcription }}</p>
                    </div>
                </div>
            @endif

            @if($submission->tajweed_analysis && is_array($submission->tajweed_analysis))
                <div>
                    <div class="info-label" style="margin-bottom: 10px;">🎯 Tajweed Analysis</div>
                    <div style="background: white; padding: 20px; border-radius: 10px; border: 2px solid #27ae60;">
                        @foreach($submission->tajweed_analysis as $key => $value)
                            <div style="margin-bottom: 10px;">
                                <strong style="color: #0a5c36;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                <span>{{ is_array($value) ? json_encode($value) : $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Assignment Info -->
    <div class="info-grid" style="margin-bottom: 30px;">
        <div class="info-item">
            <div class="info-label">📅 Due Date</div>
            <div class="info-value">{{ $assignment->due_date->format('F d, Y h:i A') }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">🎯 Total Marks</div>
            <div class="info-value">{{ $assignment->total_marks }} points</div>
        </div>
        <div class="info-item">
            <div class="info-label">🎤 Submission Type</div>
            <div class="info-value">
                <span class="submission-badge">
                    @if($assignment->is_voice_submission)
                        🎤 Voice Recording
                    @else
                        📝 Text Submission
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div>
        <div class="info-label" style="margin-bottom: 10px; font-size: 1rem; font-weight: 600; color: #0a5c36;">📋 Instructions</div>
        <div class="instructions-box">
            <p class="instructions-text">{{ $assignment->instructions }}</p>
        </div>
    </div>

    <!-- Back Button -->
    <a href="{{ route('classroom.show', $classroom->id) }}" class="btn-back">
        ← Back to Classroom
    </a>
</div>
@endsection
