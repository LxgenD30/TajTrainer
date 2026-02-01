@extends('layouts.template')

@section('page-title', 'Assignment Details')
@section('page-subtitle', 'View assignment in ' . $classroom->class_name)

@section('content')
    <div class="content-card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="card-title">📝 Assignment Details</h3>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('assignment.edit', $assignment->assignment_id) }}" 
                    style="padding: 8px 20px; background: var(--color-gold); color: var(--color-dark); border: none; border-radius: 20px; text-decoration: none; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif;"
                    onmouseover="this.style.opacity='0.8'"
                    onmouseout="this.style.opacity='1'">
                    ✏️ Edit
                </a>
                <form action="{{ route('assignment.destroy', $assignment->assignment_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        style="padding: 8px 20px; background: #e74c3c; color: white; border: none; border-radius: 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif;"
                        onmouseover="this.style.background='#c0392b'"
                        onmouseout="this.style.background='#e74c3c'">
                        🗑️ Delete
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Quran Verse Assignment -->
            <div style="margin-bottom: 25px; background: rgba(227, 216, 136, 0.1); padding: 20px; border-radius: 12px; border: 2px solid var(--color-gold);">
                <h4 style="color: var(--color-gold); margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 1.5rem;">📖</span> Assigned Quran Verse
                </h4>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div>
                        <label style="display: block; color: var(--color-gold); font-size: 0.9rem; margin-bottom: 5px; opacity: 0.8;">Surah</label>
                        <p style="color: var(--color-light); font-size: 1.1rem; margin: 0; font-weight: 600;">{{ $assignment->surah }}</p>
                    </div>
                    <div>
                        <label style="display: block; color: var(--color-gold); font-size: 0.9rem; margin-bottom: 5px; opacity: 0.8;">Ayat From</label>
                        <p style="color: var(--color-light); font-size: 1.1rem; margin: 0; font-weight: 600;">{{ $assignment->start_verse }}</p>
                    </div>
                    <div>
                        <label style="display: block; color: var(--color-gold); font-size: 0.9rem; margin-bottom: 5px; opacity: 0.8;">Ayat To</label>
                        <p style="color: var(--color-light); font-size: 1.1rem; margin: 0; font-weight: 600;">{{ $assignment->end_verse ?? 'N/A' }}</p>
                    </div>
                </div>
                
                @if($assignment->tajweed_rules && count($assignment->tajweed_rules) > 0)
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid rgba(227, 216, 136, 0.2);">
                        <label style="display: block; color: var(--color-gold); font-size: 0.9rem; margin-bottom: 8px; opacity: 0.8;">✨ Focus on Tajweed Rules:</label>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            @foreach($assignment->tajweed_rules as $rule)
                                <span style="display: inline-block; background: rgba(227, 216, 136, 0.2); color: var(--color-gold); padding: 6px 15px; border-radius: 15px; font-size: 0.9rem; font-weight: 600; border: 2px solid var(--color-gold);">
                                    {{ $rule }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Reference Materials -->
            @if($assignment->material)
                <div style="margin-bottom: 25px; background: rgba(77, 139, 49, 0.1); padding: 20px; border-radius: 12px; border: 2px solid var(--color-light-green);">
                    <h4 style="color: var(--color-light-green); margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.5rem;">📚</span> Reference Materials
                    </h4>
                    <p style="color: var(--color-light); font-size: 1.1rem; margin: 0 0 15px 0; font-weight: 600;">{{ $assignment->material->title }}</p>
                    
                    @if($assignment->material->file_path)
                        <a href="{{ Storage::url($assignment->material->file_path) }}" target="_blank"
                            style="display: inline-block; padding: 10px 20px; background: var(--color-dark-green); color: var(--color-gold); border-radius: 20px; text-decoration: none; font-weight: 600; margin-right: 10px;">
                            📄 Download PDF
                        </a>
                    @endif
                    
                    @if($assignment->material->video_link)
                        <a href="{{ $assignment->material->video_link }}" target="_blank"
                            style="display: inline-block; padding: 10px 20px; background: var(--color-dark-green); color: var(--color-gold); border-radius: 20px; text-decoration: none; font-weight: 600;">
                            🎥 Watch Video
                        </a>
                    @endif
                </div>
            @endif

            <!-- Assignment Details -->
            <div style="margin-bottom: 25px;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">📅 Due Date</label>
                        <p style="color: var(--color-light); font-size: 1.1rem; margin: 0;">{{ $assignment->due_date->format('F d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">🎯 Total Marks</label>
                        <p style="color: var(--color-light); font-size: 1.1rem; margin: 0;">{{ $assignment->total_marks }} points</p>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">📋 Instructions</label>
                    <div style="background: rgba(31, 39, 27, 0.5); padding: 15px; border-radius: 8px; border: 2px solid var(--color-dark-green);">
                        <p style="color: var(--color-light); margin: 0; line-height: 1.6; white-space: pre-wrap;">{{ $assignment->instructions }}</p>
                    </div>
                </div>

                <div>
                    <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">🎤 Submission Type</label>
                    <div style="display: inline-block; padding: 10px 20px; background: rgba(227, 216, 136, 0.15); border: 2px solid var(--color-gold); border-radius: 20px;">
                        <span style="color: var(--color-gold); font-weight: 600;">
                            @if($assignment->is_voice_submission)
                                🎤 Voice Submission Required
                            @else
                                📝 Text Submission
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div style="margin-top: 30px;">
                <a href="{{ route('classroom.show', $classroom->id) }}" 
                    style="display: inline-block; padding: 12px 30px; background: transparent; color: var(--color-light-green); border: 2px solid var(--color-light-green); border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;"
                    onmouseover="this.style.background='rgba(226, 241, 175, 0.1)'"
                    onmouseout="this.style.background='transparent'">
                    ← Back to Classroom
                </a>
            </div>
        </div>
    </div>
@endsection
