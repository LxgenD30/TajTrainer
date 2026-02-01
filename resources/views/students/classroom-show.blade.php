@extends('layouts.template')

@section('title', $classroom->class_name)
@section('page-title', $classroom->class_name)
@section('page-subtitle', 'Classroom Details')

@push('styles')
<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .spinner {
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-top: 3px solid white;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        animation: spin 0.8s linear infinite;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div style="padding: 0;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('student.classes') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--color-gold); text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.color='var(--color-light-green)'" onmouseout="this.style.color='var(--color-gold)'">
            ← Back to My Classes
        </a>
    </div>

    <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); margin-bottom: 25px;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                    <div style="width: 50px; height: 50px; background: var(--color-dark-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                        🏫
                    </div>
                    <div>
                        <h2 style="color: var(--color-gold); font-size: 1.8rem; margin-bottom: 5px;">{{ $classroom->class_name }}</h2>
                        <p style="color: var(--color-light-green); opacity: 0.8; margin: 0;">{{ $classroom->description ?? 'No description' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; color: var(--color-light-green); opacity: 0.9;">
                <span style="font-size: 1.2rem;">👨‍🏫</span>
                <span>Teacher: <strong style="color: var(--color-gold);">{{ $classroom->teacher->name ?? 'Unknown' }}</strong></span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; color: var(--color-light-green); opacity: 0.9;">
                <span style="font-size: 1.2rem;">👥</span>
                <span><strong style="color: var(--color-gold);">{{ $classroom->students->count() }}</strong> Students</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; color: var(--color-light-green); opacity: 0.9;">
                <span style="font-size: 1.2rem;">📝</span>
                <span><strong style="color: var(--color-gold);">{{ $assignments->count() }}</strong> Assignments</span>
            </div>
        </div>
    </div>

    <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
            <div style="width: 50px; height: 50px; background: var(--color-dark-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                📝
            </div>
            <div>
                <h3 style="color: var(--color-gold); font-size: 1.3rem; margin-bottom: 5px;">Assignments</h3>
                <p style="color: var(--color-light-green); opacity: 0.8; font-size: 0.9rem; margin: 0;">Complete your assignments on time</p>
            </div>
        </div>

        @if($assignments->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 15px;">
                @foreach($assignments as $assignment)
                    @php
                        $submission = $submissions->get($assignment->assignment_id);
                        $isOverdue = $assignment->due_date < now() && (!$submission || $submission->status === 'pending');
                        $isPending = !$submission || $submission->status === 'pending';
                        $isSubmitted = $submission && $submission->status === 'submitted';
                        $isGraded = $submission && $submission->status === 'graded';
                    @endphp
                    
                    <div style="background: rgba(70, 63, 58, 0.4); border-left: 4px solid {{ $isGraded ? '#4caf50' : ($isSubmitted ? '#ff9800' : ($isOverdue ? '#e74c3c' : 'var(--color-gold)')) }}; padding: 20px; border-radius: 12px; transition: all 0.3s ease;" onmouseover="this.style.transform='translateX(5px)'" onmouseout="this.style.transform='translateX(0)'">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                            <div style="flex: 1;">
                                <h4 style="color: var(--color-gold); margin: 0 0 8px 0; font-size: 1.2rem;">
                                    @if($assignment->surah)
                                        📖 {{ $assignment->surah }} ({{ $assignment->start_verse }}@if($assignment->end_verse)-{{ $assignment->end_verse }}@endif)
                                    @else
                                        {{ $assignment->material ? $assignment->material->title : 'Assignment' }}
                                    @endif
                                </h4>
                                <p style="color: var(--color-light-green); opacity: 0.9; margin: 0 0 10px 0; line-height: 1.6;">{{ $assignment->instructions }}</p>
                            </div>
                            <div style="text-align: right; margin-left: 20px;">
                                @if($isGraded)
                                    @php
                                        $score = \App\Models\Score::where('assignment_id', $assignment->assignment_id)
                                                                   ->where('user_id', Auth::id())
                                                                   ->first();
                                    @endphp
                                    <div style="background: #4caf50; color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; margin-bottom: 8px;">
                                        ✓ Graded: {{ $score ? $score->score : $submission->marks_obtained }}/{{ $assignment->total_marks }}
                                    </div>
                                @elseif($isSubmitted)
                                    @php
                                        // Check if score exists - if yes, it's graded, if no, still processing
                                        $score = \App\Models\Score::where('assignment_id', $assignment->assignment_id)
                                                                   ->where('user_id', Auth::id())
                                                                   ->first();
                                    @endphp
                                    @if($score)
                                        <div style="background: #4caf50; color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; margin-bottom: 8px;">
                                            ✓ Graded: {{ $score->score }}/{{ $assignment->total_marks }}
                                        </div>
                                    @else
                                        <div style="background: #2196f3; color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                            <div class="spinner"></div>
                                            <span>Analyzing...</span>
                                        </div>
                                    @endif
                                @elseif($isOverdue)
                                    <div style="background: #e74c3c; color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; margin-bottom: 8px;">
                                        ⚠ Overdue
                                    </div>
                                @else
                                    <div style="background: rgba(77, 139, 49, 0.3); color: var(--color-gold); padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; margin-bottom: 8px; border: 1px solid rgba(77, 139, 49, 0.5);">
                                        📌 Pending
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid rgba(77, 139, 49, 0.2);">
                            <div style="display: flex; gap: 20px; font-size: 0.9rem; color: var(--color-light-green); opacity: 0.9;">
                                <span>📅 Due: {{ $assignment->due_date->format('M d, Y h:i A') }}</span>
                                <span>🎯 {{ $assignment->total_marks }} points</span>
                                @if($assignment->is_voice_submission)
                                    <span style="color: var(--color-gold);">🎤 Voice Submission</span>
                                @endif
                            </div>
                            
                            <div style="display: flex; gap: 10px;">
                                @if($assignment->material)
                                    <a href="{{ route('student.material.show', ['id' => $assignment->material->material_id, 'from' => 'classroom', 'class' => $classroom->id]) }}" class="btn-secondary" style="text-decoration: none; padding: 8px 16px; font-size: 0.9rem;">
                                        📚 Material
                                    </a>
                                @endif
                                
                                @if($isPending)
                                    <a href="{{ route('student.assignment.submit', $assignment->assignment_id) }}" class="btn-primary" style="text-decoration: none; padding: 8px 16px; font-size: 0.9rem;">
                                        📝 Submit
                                    </a>
                                @elseif($isSubmitted || $isGraded)
                                    <a href="{{ route('student.assignment.view', $assignment->assignment_id) }}" class="btn-secondary" style="text-decoration: none; padding: 8px 16px; font-size: 0.9rem;">
                                        👁️ View
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if($isGraded && $submission->teacher_feedback)
                            <div style="margin-top: 15px; padding: 15px; background: rgba(77, 139, 49, 0.1); border-radius: 8px; border: 1px solid rgba(77, 139, 49, 0.3);">
                                <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">📋 Teacher Feedback:</div>
                                <div style="color: var(--color-light-green); line-height: 1.6; font-size: 0.9rem;">{{ $submission->teacher_feedback }}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 60px 20px; color: var(--color-light-green); opacity: 0.6;">
                <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;">📝</div>
                <h3 style="font-size: 1.3rem; margin-bottom: 10px; color: var(--color-gold);">No Assignments Yet</h3>
                <p style="font-size: 1rem;">Your teacher hasn't created any assignments for this class</p>
            </div>
        @endif
    </div>

    @if($assignments->where('material')->isNotEmpty())
    <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
            <div style="width: 50px; height: 50px; background: var(--color-dark-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                📚
            </div>
            <div>
                <h3 style="color: var(--color-gold); font-size: 1.3rem; margin-bottom: 5px;">Learning Materials</h3>
                <p style="color: var(--color-light-green); opacity: 0.8; font-size: 0.9rem; margin: 0;">Materials assigned for this class</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
            @foreach($assignments->where('material')->unique('material.material_id') as $assignment)
                @if($assignment->material)
                    <a href="{{ route('student.material.show', ['id' => $assignment->material->material_id, 'from' => 'classroom', 'class' => $classroom->id]) }}" style="background: rgba(70, 63, 58, 0.4); padding: 20px; border-radius: 12px; border-left: 4px solid var(--color-gold); text-decoration: none; display: block; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(70, 63, 58, 0.6)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(227, 216, 136, 0.3)'" onmouseout="this.style.background='rgba(70, 63, 58, 0.4)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <div style="font-weight: 600; color: var(--color-gold); margin-bottom: 8px; font-size: 1.1rem;">{{ $assignment->material->title }}</div>
                        <p style="color: var(--color-light-green); font-size: 0.9rem; margin: 0; opacity: 0.85; line-height: 1.5;">{{ Str::limit($assignment->material->content, 80) }}</p>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Auto-refresh page if any submission is still being processed
    @php
        $hasProcessing = false;
        foreach($submissions as $submission) {
            if ($submission && $submission->status === 'submitted') {
                // Check if score exists for this submission
                $score = \App\Models\Score::where('assignment_id', $submission->assignment_id)
                                          ->where('user_id', Auth::id())
                                          ->first();
                if (!$score) {
                    $hasProcessing = true;
                    break;
                }
            }
        }
    @endphp
    
    @if($hasProcessing)
        // Refresh every 5 seconds if audio is being processed
        console.log('Audio processing in progress... Page will auto-refresh in 5 seconds');
        setTimeout(function() {
            location.reload();
        }, 5000);
    @endif
</script>
@endpush
@endsection
