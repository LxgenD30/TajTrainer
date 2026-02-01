@extends('layouts.template')

@section('title', 'Student Submissions')
@section('page-title', $student->name . ' - Submissions')
@section('page-subtitle', $classroom->class_name)

@push('styles')
<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .spinner {
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        width: 14px;
        height: 14px;
        animation: spin 0.8s linear infinite;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div style="padding: 0;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('classroom.show', $classroom->id) }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--color-gold); text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.color='var(--color-light-green)'" onmouseout="this.style.color='var(--color-gold)'">
            ← Back to Classroom
        </a>
    </div>

    <!-- Student Info Card -->
    <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 25px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 60px; height: 60px; background: var(--color-dark-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                👤
            </div>
            <div style="flex: 1;">
                <h2 style="color: var(--color-gold); font-size: 1.5rem; margin-bottom: 5px;">{{ $student->name }}</h2>
                <p style="color: var(--color-light-green); opacity: 0.8; font-size: 0.9rem; margin: 0;">📧 {{ $student->email }}</p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.85rem; color: var(--color-light-green); margin-bottom: 5px;">Total Submissions</div>
                <div style="font-size: 1.8rem; font-weight: 700; color: var(--color-gold);">{{ $submissions->count() }}</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.85rem; color: var(--color-light-green); margin-bottom: 5px;">Graded</div>
                <div style="font-size: 1.8rem; font-weight: 700; color: #4caf50;">{{ $submissions->where('status', 'graded')->count() }}</div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div style="background: rgba(76, 175, 80, 0.2); border: 2px solid #4caf50; border-radius: 10px; padding: 15px; margin-bottom: 20px; color: #4caf50; font-weight: 600;">
        ✓ {{ session('success') }}
    </div>
    @endif

    <!-- Submissions List -->
    <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
            <div style="width: 50px; height: 50px; background: var(--color-dark-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                📝
            </div>
            <div>
                <h3 style="color: var(--color-gold); font-size: 1.3rem; margin-bottom: 5px;">Assignment Submissions</h3>
                <p style="color: var(--color-light-green); opacity: 0.8; font-size: 0.9rem; margin: 0;">Review and grade student work</p>
            </div>
        </div>

        @if($submissions->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 15px;">
                @foreach($submissions as $submission)
                    <div style="background: rgba(70, 63, 58, 0.4); border-left: 4px solid {{ $submission->status === 'graded' ? '#4caf50' : 'var(--color-gold)' }}; padding: 20px; border-radius: 10px; transition: all 0.3s ease;"
                        onmouseover="this.style.background='rgba(70, 63, 58, 0.6)'"
                        onmouseout="this.style.background='rgba(70, 63, 58, 0.4)'">
                        
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                            <div style="flex: 1;">
                                <h4 style="color: var(--color-gold); font-size: 1.1rem; margin: 0 0 10px 0;">
                                    @if($submission->assignment->surah)
                                        📖 {{ $submission->assignment->surah }} 
                                        ({{ $submission->assignment->start_verse }}@if($submission->assignment->end_verse)-{{ $submission->assignment->end_verse }}@endif)
                                    @else
                                        {{ $submission->assignment->material ? $submission->assignment->material->title : 'Assignment' }}
                                    @endif
                                </h4>
                                <div style="display: flex; gap: 20px; font-size: 0.9rem; color: var(--color-light-green); opacity: 0.9;">
                                    <span>📅 Submitted: {{ $submission->created_at->format('M d, Y h:i A') }}</span>
                                    <span>🎯 Total Marks: {{ $submission->assignment->total_marks }}</span>
                                    @if($submission->assignment->is_voice_submission)
                                        <span style="color: var(--color-gold);">🎤 Voice Recording</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 10px; align-items: center;">
                                @if($submission->status === 'graded' && $submission->score)
                                    <div style="text-align: center; padding: 10px 15px; background: rgba(76, 175, 80, 0.2); border: 2px solid #4caf50; border-radius: 10px;">
                                        <div style="font-size: 0.75rem; color: #4caf50; margin-bottom: 3px;">Score</div>
                                        <div style="font-size: 1.3rem; font-weight: 700; color: #4caf50;">{{ $submission->score->score }}/{{ $submission->assignment->total_marks }}</div>
                                    </div>
                                @elseif($submission->status === 'submitted' && !$submission->score)
                                    <div style="padding: 8px 15px; background: rgba(33, 150, 243, 0.2); border: 2px solid #2196f3; border-radius: 10px; color: #2196f3; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                        <div class="spinner" style="border-color: rgba(33, 150, 243, 0.3); border-top-color: #2196f3;"></div>
                                        Processing...
                                    </div>
                                @else
                                    <div style="padding: 8px 15px; background: rgba(227, 216, 136, 0.2); border: 2px solid var(--color-gold); border-radius: 10px; color: var(--color-gold); font-size: 0.85rem; font-weight: 600;">
                                        ⏳ Pending Review
                                    </div>
                                @endif
                                
                                <a href="{{ route('teacher.submission.grade', $submission->id) }}" 
                                    style="padding: 10px 20px; background: var(--color-gold); color: var(--color-dark); border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; font-size: 0.9rem;"
                                    onmouseover="this.style.opacity='0.8'"
                                    onmouseout="this.style.opacity='1'">
                                    {{ $submission->status === 'graded' ? '👁️ View' : '✏️ Grade' }}
                                </a>
                            </div>
                        </div>

                        @if($submission->status === 'graded' && $submission->score && $submission->score->feedback)
                            <div style="background: rgba(31, 39, 27, 0.5); padding: 15px; border-radius: 8px; margin-top: 10px;">
                                <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">💬 Teacher Feedback:</div>
                                <p style="color: var(--color-light-green); margin: 0; line-height: 1.6; font-size: 0.9rem;">{{ Str::limit($submission->score->feedback, 150) }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 4rem; margin-bottom: 15px; opacity: 0.5;">📭</div>
                <h3 style="color: var(--color-gold); margin-bottom: 10px;">No Submissions Yet</h3>
                <p style="color: var(--color-light-green); opacity: 0.7;">This student hasn't submitted any assignments in this classroom.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh if any submission is still being processed
    @php
        $hasProcessing = $submissions->contains(function($submission) {
            return $submission->status === 'submitted' && !$submission->score;
        });
    @endphp
    
    @if($hasProcessing)
        console.log('Audio processing in progress... Page will auto-refresh in 5 seconds');
        setTimeout(function() {
            location.reload();
        }, 5000);
    @endif
</script>
@endpush
@endsection
