@extends('layouts.dashboard')

@section('title', 'Grade Submission')
@section('user-role', 'Teacher • Grade Assignment')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
<style>
    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 30px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
        border: 3px solid #2a2a2a;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .welcome-banner:before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .welcome-content { position: relative; z-index: 2; }
    .welcome-content h1 { font-size: 2.2rem; margin-bottom: 10px; font-weight: 700; color: #ffffff; text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3); }
    .welcome-content p { font-size: 1.1rem; opacity: 0.95; line-height: 1.6; margin: 0; }
    
    .btn-back-gold {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #d4af37, #f4d03f);
        color: #000000;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        border-radius: 12px;
        border: 3px solid #2a2a2a;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }
    
    .btn-back-gold:hover {
        background: linear-gradient(135deg, #f4d03f, #d4af37);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.6);
    }
</style>

<section class="welcome-banner">
    <div class="welcome-content">
        <h1>Grading Submission by <span style="color: #d4af37;">{{ $submission->student->name }}</span> 📝</h1>
        <p>Evaluate and provide feedback on the student's Tajweed recitation</p>
    </div>
    <a href="{{ route('teacher.student.submissions', ['classroom' => $submission->assignment->class_id, 'student' => $submission->student_id]) }}" 
       class="btn-back-gold">
        <i class="fas fa-arrow-left"></i> Back to Submissions
    </a>
</section>

<div style="padding: 0; max-width: 1600px; margin: 0 auto;">

    <div style="display: grid; grid-template-columns: 320px 1fr 1fr 350px; gap: 25px; max-width: 1800px; margin: 0 auto;">
        <!-- Column 1: Grading Form (320px fixed width) -->
        <div style="background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 3px solid #2a2a2a; height: fit-content; position: sticky; top: 20px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid rgba(10, 92, 54, 0.2);">
                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #0a5c36, #1abc9c); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; box-shadow: 0 4px 15px rgba(10, 92, 54, 0.3);">
                    ✏️
                </div>
                <div>
                    <h3 style="color: #0a5c36; font-size: 1.2rem; margin-bottom: 3px; font-family: 'El Messiri', serif;">
                        {{ $submission->status === 'graded' ? 'Update Grade' : 'Grade' }}
                    </h3>
                    <p style="color: #666; font-size: 0.85rem; margin: 0;">Evaluate performance</p>
                </div>
            </div>

            @if($submission->score)
            <div style="background: rgba(76, 175, 80, 0.1); border: 2px solid #4caf50; border-radius: 12px; padding: 15px; margin-bottom: 15px;">
                <div style="color: #4caf50; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle"></i> Previously Graded</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <div style="font-size: 0.75rem; color: #666; margin-bottom: 4px;">Score</div>
                        <div style="font-size: 1.3rem; color: #4caf50; font-weight: 700;">{{ $submission->score->score }}/{{ $submission->assignment->total_marks }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: #666; margin-bottom: 4px;">Percentage</div>
                        <div style="font-size: 1.3rem; color: #4caf50; font-weight: 700;">{{ round(($submission->score->score / $submission->assignment->total_marks) * 100, 1) }}%</div>
                    </div>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('teacher.submission.update.grade', $submission->id) }}">
                @csrf
                
                @php
                    // Calculate suggested score from Tajweed analysis
                    $suggestedScore = null;
                    if($submission->tajweed_score) {
                        $suggestedScore = round(($submission->tajweed_score / 100) * $submission->assignment->total_marks, 1);
                    }
                    $defaultScore = old('score', $submission->score->score ?? $suggestedScore ?? '');
                @endphp

                @if($submission->tajweed_score)
                <div style="background: rgba(26, 188, 156, 0.08); border: 2px solid rgba(26, 188, 156, 0.25); border-radius: 10px; padding: 15px; margin-bottom: 15px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                        <div>
                            <div style="color: #0a5c36; font-weight: 700; font-size: 0.95rem;">🤖 AI Score</div>
                            <div style="font-size: 0.75rem; color: #666;">Auto-calculated</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 1.6rem; color: #0a5c36; font-weight: 800; line-height: 1;">{{ $submission->tajweed_score }}%</div>
                            <div style="font-size: 0.8rem; color: #666; font-weight: 600;">{{ $submission->tajweed_grade }}</div>
                        </div>
                    </div>
                    <div style="background: white; border-radius: 8px; padding: 12px;">
                        <div style="font-size: 0.85rem; color: #666; margin-bottom: 3px;">Suggested Points:</div>
                        <div style="font-size: 1.3rem; color: #0a5c36; font-weight: 700;">{{ $suggestedScore }} <span style="font-size: 0.9rem; opacity: 0.7;">/ {{ $submission->assignment->total_marks }}</span></div>
                    </div>
                </div>
                @endif

                <div style="margin-bottom: 18px;">
                    <label style="display: block; color: #0a5c36; font-weight: 600; margin-bottom: 8px; font-size: 0.95rem;">
                        🎯 Points Earned <span style="color: #ff6b6b;">*</span>
                    </label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input 
                            type="number" 
                            name="score" 
                            min="0" 
                            max="{{ $submission->assignment->total_marks }}"
                            step="0.5"
                            value="{{ $defaultScore }}"
                            required
                            style="flex: 1; padding: 12px; background: white; border: 2px solid {{ $suggestedScore && !$submission->score ? '#1abc9c' : 'rgba(10, 92, 54, 0.3)' }}; border-radius: 8px; color: #1a1a1a; font-size: 1.2rem; font-weight: 700;"
                        >
                        <span style="color: #666; font-size: 1rem; font-weight: 600;">/ {{ $submission->assignment->total_marks }}</span>
                    </div>
                    @error('score')
                        <p style="color: #ff6b6b; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                @php
                    $defaultFeedback = old('feedback', $submission->score->feedback ?? '');
                    if(!$defaultFeedback && $submission->tajweed_analysis) {
                        $analysis = json_decode($submission->tajweed_analysis, true);
                        if(isset($analysis['overall_score']['feedback'])) {
                            $defaultFeedback = $analysis['overall_score']['feedback'];
                        }
                    }
                @endphp

                <div style="margin-bottom: 18px;">
                    <label style="display: block; color: #0a5c36; font-weight: 600; margin-bottom: 8px; font-size: 0.95rem;">
                        💬 Feedback <span style="color: #ff6b6b;">*</span>
                    </label>
                    <textarea 
                        name="feedback" 
                        rows="6" 
                        required
                        placeholder="Provide feedback on recitation..."
                        style="width: 100%; padding: 12px; background: white; border: 2px solid {{ $defaultFeedback && !$submission->score ? '#1abc9c' : 'rgba(10, 92, 54, 0.3)' }}; border-radius: 8px; color: #1a1a1a; font-size: 0.9rem; line-height: 1.5; resize: vertical; font-family: 'Cairo', sans-serif;"
                    >{{ $defaultFeedback }}</textarea>
                    @error('feedback')
                        <p style="color: #ff6b6b; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                    style="width: 100%; padding: 12px 20px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(10, 92, 54, 0.3);">
                    {{ $submission->status === 'graded' ? '📝 Update Grade' : '✓ Submit Grade' }}
                </button>
            </form>
        </div>

        <!-- Column 2: Submission Details (1fr flexible) -->
        <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 3px solid #2a2a2a;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(10, 92, 54, 0.2);">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #0a5c36, #1abc9c); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(10, 92, 54, 0.3);">
                    📝
                </div>
                <div>
                    <h3 style="color: #0a5c36; font-size: 1.3rem; margin-bottom: 5px; font-family: 'El Messiri', serif;">Submission Details</h3>
                    <p style="color: #666; font-size: 0.9rem; margin: 0;">Review student work</p>
                </div>
            </div>

            <!-- Student Info -->
            <div style="background: rgba(10, 92, 54, 0.05); padding: 18px; border-radius: 12px; margin-bottom: 20px; border: 2px solid rgba(10, 92, 54, 0.1);">
                <div style="color: #0a5c36; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;"><i class="fas fa-user-circle"></i> Student</div>
                <div style="color: #1a1a1a; font-size: 1.1rem; margin-bottom: 5px; font-weight: 600;">{{ $submission->student->name }}</div>
                <div style="color: #666; font-size: 0.9rem;">{{ $submission->student->email }}</div>
            </div>

            <!-- Assignment Info -->
            <div style="background: rgba(10, 92, 54, 0.05); padding: 18px; border-radius: 12px; margin-bottom: 20px; border: 2px solid rgba(10, 92, 54, 0.1);">
                <div style="color: #0a5c36; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;"><i class="fas fa-book-quran"></i> Assignment</div>
                <h4 style="color: #1a1a1a; margin: 0 0 10px 0; font-size: 1.1rem; font-weight: 600;">
                    @if($submission->assignment->surah)
                        📖 {{ $submission->assignment->surah }} 
                        ({{ $submission->assignment->start_verse }}@if($submission->assignment->end_verse)-{{ $submission->assignment->end_verse }}@endif)
                    @else
                        {{ $submission->assignment->material ? $submission->assignment->material->title : 'Assignment' }}
                    @endif
                </h4>
                <p style="color: #666; margin: 0 0 10px 0; font-size: 0.9rem; line-height: 1.6;">{{ $submission->assignment->instructions }}</p>
                <div style="display: flex; gap: 15px; font-size: 0.85rem; color: #666;">
                    <span><i class="fas fa-bullseye"></i> {{ $submission->assignment->total_marks }} marks</span>
                    <span><i class="far fa-calendar"></i> Due {{ $submission->assignment->due_date->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- Submission Time -->
            <div style="background: rgba(10, 92, 54, 0.05); padding: 18px; border-radius: 12px; margin-bottom: 20px; border: 2px solid rgba(10, 92, 54, 0.1);">
                <div style="color: #0a5c36; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;"><i class="far fa-clock"></i> Submission Time</div>
                <div style="color: #1a1a1a; font-size: 1rem; font-weight: 600;">
                    {{ $submission->created_at->format('M d, Y h:i A') }}
                    @if($submission->created_at->gt($submission->assignment->due_date))
                        <span style="color: #e74c3c; font-weight: 600; margin-left: 10px; padding: 4px 10px; background: rgba(231, 76, 60, 0.1); border-radius: 6px; font-size: 0.85rem;"><i class="fas fa-exclamation-triangle"></i> Late</span>
                    @else
                        <span style="color: #4caf50; font-weight: 600; margin-left: 10px; padding: 4px 10px; background: rgba(76, 175, 80, 0.1); border-radius: 6px; font-size: 0.85rem;"><i class="fas fa-check-circle"></i> On Time</span>
                    @endif
                </div>
            </div>

            @if($submission->audio_file_path)
            <!-- Audio Submission -->
            <div style="background: rgba(10, 92, 54, 0.05); padding: 18px; border-radius: 12px; margin-bottom: 20px; border: 2px solid rgba(10, 92, 54, 0.1);">
                <div style="color: #0a5c36; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;"><i class="fas fa-microphone"></i> Voice Recording</div>
                @php
                    $audioExt = pathinfo($submission->audio_file_path, PATHINFO_EXTENSION);
                    $mimeTypes = [
                        'mp3' => 'audio/mpeg',
                        'wav' => 'audio/wav',
                        'webm' => 'audio/webm',
                        'm4a' => 'audio/mp4',
                        'ogg' => 'audio/ogg',
                        'oga' => 'audio/ogg',
                    ];
                    $detectedMime = $mimeTypes[strtolower($audioExt)] ?? 'audio/mpeg';
                    // Use Storage::url() for proper public URL generation
                    $audioUrl = \Storage::url($submission->audio_file_path);
                    // Check if file actually exists
                    $audioExists = \Storage::disk('public')->exists($submission->audio_file_path);
                @endphp
                @if($audioExists)
                <audio id="submissionAudio" controls preload="auto" style="width: 100%; margin-bottom: 10px; outline: none;" controlsList="nodownload" src="{{ $audioUrl }}">
                    <source src="{{ $audioUrl }}" type="{{ $detectedMime }}">
                    <source src="{{ $audioUrl }}" type="audio/webm">
                    <source src="{{ $audioUrl }}" type="audio/mpeg">
                    <source src="{{ $audioUrl }}" type="audio/wav">
                    <source src="{{ $audioUrl }}" type="audio/ogg">
                    <source src="{{ $audioUrl }}" type="audio/mp4">
                    <source src="{{ $audioUrl }}" type="audio/x-m4a">
                    Your browser does not support the audio element.
                </audio>
                @else
                <div style="padding: 20px; background: rgba(231, 76, 60, 0.1); border: 2px solid #e74c3c; border-radius: 8px; color: #e74c3c;">
                    ⚠️ Audio file not found: {{ $submission->audio_file_path }}<br>
                    <small style="opacity: 0.8;">File may have been deleted or path is incorrect</small>
                </div>
                @endif
                <div style="display: flex; gap: 10px; align-items: center; margin-top: 8px;">
                    <div style="font-size: 0.75rem; color: var(--light-green); opacity: 0.6; flex: 1;">
                        📁 File: {{ basename($submission->audio_file_path) }}
                    </div>
                    <a href="{{ $audioUrl }}" download="{{ basename($submission->audio_file_path) }}" 
                       style="padding: 6px 12px; background: rgba(227, 216, 136, 0.2); color: var(--gold); border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; transition: all 0.3s ease; border: 1px solid rgba(227, 216, 136, 0.3);"
                       onmouseover="this.style.background='var(--gold)'; this.style.color='var(--dark-green)'"
                       onmouseout="this.style.background='rgba(227, 216, 136, 0.2)'; this.style.color='var(--gold)'">
                        ⬇️ Download
                    </a>
                </div>
                <script>
                    // Audio debugging
                    const audio = document.getElementById('submissionAudio');
                    if (audio) {
                        audio.addEventListener('error', function(e) {
                            console.error('Audio loading error:', e);
                            console.log('Attempted URL:', '{{ $audioUrl }}');
                            console.log('MIME type:', '{{ $detectedMime }}');
                            const errorDiv = document.createElement('div');
                            errorDiv.style.cssText = 'background: rgba(255,107,107,0.2); padding: 10px; border-radius: 6px; margin-top: 8px; border-left: 3px solid #ff6b6b; color: #ff6b6b; font-size: 0.85rem;';
                            errorDiv.innerHTML = '⚠️ Audio playback error. Please use the download button to listen offline.';
                            audio.parentNode.insertBefore(errorDiv, audio.nextSibling);
                        });
                        audio.addEventListener('loadedmetadata', function() {
                            console.log('✅ Audio loaded successfully');
                        });
                    }
                </script>
            </div>
            @endif

            @if($submission->transcription)
            <!-- AI Transcription -->
            <div style="background: rgba(26, 188, 156, 0.1); padding: 18px; border-radius: 12px; margin-bottom: 20px; border: 2px solid rgba(26, 188, 156, 0.3);">
                <div style="display: flex; align-items: center; gap: 8px; color: #0a5c36; font-weight: 600; margin-bottom: 12px; font-size: 0.9rem;">
                    <i class="fas fa-robot"></i>
                    <span>AI Transcription</span>
                    <span style="font-size: 0.75rem; padding: 3px 8px; background: rgba(26, 188, 156, 0.2); border-radius: 12px; font-weight: 500; color: #0a5c36;">AssemblyAI</span>
                </div>
                <div style="background: rgba(10, 92, 54, 0.05); padding: 18px; border-radius: 8px; max-height: 200px; overflow-y: auto; border: 1px solid rgba(10, 92, 54, 0.1);">
                    <p style="color: #1a1a1a; margin: 0; line-height: 2.2; direction: rtl; text-align: right; font-size: 1.8rem; font-family: 'Amiri', 'Traditional Arabic', serif; letter-spacing: 0.5px;">
                        {{ $submission->transcription }}
                    </p>
                </div>
            </div>
            @endif
        </div>

        <!-- Column 3: Tajweed Analysis (1fr flexible) -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            @if($submission->tajweed_analysis)
            <!-- Tajweed Analysis -->
            <div style="background: white; border: 3px solid #0a5c36; padding: 30px; border-radius: 20px; box-shadow: 0 8px 25px rgba(10, 92, 54, 0.15); margin-bottom: 25px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 3px solid rgba(10, 92, 54, 0.15);">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 55px; height: 55px; background: linear-gradient(135deg, #0a5c36, #1abc9c); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 6px 20px rgba(10, 92, 54, 0.3);">📖</div>
                        <div>
                            <div style="color: #0a5c36; font-weight: 800; font-size: 1.4rem; font-family: 'El Messiri', serif; margin-bottom: 5px;">Tajweed Analysis</div>
                            <div style="font-size: 0.85rem; padding: 5px 12px; background: rgba(26, 188, 156, 0.15); border-radius: 15px; font-weight: 600; color: #0a5c36; display: inline-block;">🤖 AI-Powered Analysis</div>
                        </div>
                    </div>
                    @php
                        $scoreColor = $submission->tajweed_score >= 90 ? '#4caf50' : ($submission->tajweed_score >= 70 ? '#8bc34a' : ($submission->tajweed_score >= 60 ? '#ff9800' : '#f44336'));
                    @endphp
                    <div style="padding: 18px 28px; background: white; border: 3px solid {{ $scoreColor }}; border-radius: 15px; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.2);">
                        <div style="text-align: center;">
                            <div style="color: {{ $scoreColor }}; font-weight: 900; font-size: 2.5rem; line-height: 1;">{{ $submission->tajweed_score }}%</div>
                            <div style="color: #666; font-size: 1rem; font-weight: 700; margin-top: 5px;">{{ $submission->tajweed_grade }}</div>
                        </div>
                    </div>
                </div>

                @php
                    $analysis = is_array($submission->tajweed_analysis) ? $submission->tajweed_analysis : json_decode($submission->tajweed_analysis, true);
                @endphp

                <!-- Madd Analysis -->
                @if(isset($analysis['madd_analysis']))
                <div style="background: rgba(10, 92, 54, 0.05); border: 2px solid rgba(10, 92, 54, 0.2); padding: 25px; border-radius: 15px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h5 style="color: #0a5c36; margin: 0 0 8px 0; font-size: 1.3rem; font-weight: 800;">مد (Madd - Elongation)</h5>
                            <p style="color: #666; font-size: 0.95rem; margin: 0; line-height: 1.5;">Proper elongation of vowel sounds (2-6 counts)</p>
                        </div>
                        @php
                            $maddColor = $analysis['madd_analysis']['percentage'] >= 90 ? '#4caf50' : ($analysis['madd_analysis']['percentage'] >= 70 ? '#8bc34a' : '#ff9800');
                        @endphp
                        <div style="padding: 12px 20px; background: white; border: 3px solid {{ $maddColor }}; border-radius: 12px; box-shadow: 0 4px 12px {{ $maddColor }}33;">
                            <span style="color: {{ $maddColor }}; font-weight: 800; font-size: 1.8rem;">{{ $analysis['madd_analysis']['percentage'] }}%</span>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div style="width: 100%; height: 12px; background: rgba(0, 0, 0, 0.08); border-radius: 10px; overflow: hidden; margin-bottom: 20px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                        <div style="width: {{ $analysis['madd_analysis']['percentage'] }}%; height: 100%; background: linear-gradient(90deg, {{ $maddColor }}, {{ $maddColor }}cc); transition: width 0.5s ease;"></div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px;">
                        <div style="background: white; padding: 18px; border-radius: 12px; text-align: center; border: 2px solid #e0e0e0;">
                            <div style="color: #666; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Total Found</div>
                            <div style="color: #0a5c36; font-size: 2rem; font-weight: 800;">{{ $analysis['madd_analysis']['total_elongations'] }}</div>
                        </div>
                        <div style="background: white; padding: 18px; border-radius: 12px; text-align: center; border: 2px solid #4caf50;">
                            <div style="color: #4caf50; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Correct</div>
                            <div style="color: #4caf50; font-size: 2rem; font-weight: 800;">{{ $analysis['madd_analysis']['correct_elongations'] }}</div>
                        </div>
                        <div style="background: white; padding: 18px; border-radius: 12px; text-align: center; border: 2px solid #f44336;">
                            <div style="color: #f44336; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Issues</div>
                            <div style="color: #f44336; font-size: 2rem; font-weight: 800;">{{ count($analysis['madd_analysis']['issues']) }}</div>
                        </div>
                    </div>

                    @if(count($analysis['madd_analysis']['issues']) > 0)
                    <div style="background: rgba(255, 152, 0, 0.08); border-left: 4px solid #ff9800; padding: 12px 15px; border-radius: 8px;">
                        <div style="color: #ff9800; font-size: 0.9rem; font-weight: 700; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                            <span>⚠️</span>
                            <span>Issues Detected ({{ count($analysis['madd_analysis']['issues']) }})</span>
                        </div>
                        <div style="max-height: 200px; overflow-y: auto;">
                            @foreach($analysis['madd_analysis']['issues'] as $index => $issue)
                            <div style="background: rgba(31, 39, 27, 0.5); padding: 10px 12px; border-radius: 6px; margin-bottom: 8px; font-size: 0.85rem;">
                                <div style="display: flex; align-items: start; gap: 10px; margin-bottom: 6px;">
                                    <span style="background: #ff9800; color: #1f271b; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; min-width: 50px; text-align: center;">Word {{ $issue['position'] ?? $index + 1 }}</span>
                                    <span style="color: var(--light-green); flex: 1; line-height: 1.5; font-weight: 600;">{{ $issue['word'] ?? '' }}</span>
                                </div>
                                <div style="color: #ffa726; font-size: 0.8rem; margin-bottom: 4px;">{{ $issue['note'] ?? $issue['issue'] ?? 'Issue detected' }}</div>
                                <div style="color: rgba(211, 255, 177, 0.7); font-size: 0.75rem;">💡 {{ $issue['recommendation'] ?? '' }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div style="background: rgba(76, 175, 80, 0.1); border-left: 4px solid #4caf50; padding: 12px 15px; border-radius: 8px;">
                        <div style="color: #4caf50; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                            <span>✓</span>
                            <span>Excellent! No issues detected in Madd elongations.</span>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Noon Sakin Analysis -->
                @if(isset($analysis['noon_sakin_analysis']))
                <div style="background: rgba(31, 39, 27, 0.6); border: 1px solid rgba(77, 139, 49, 0.3); padding: 18px; border-radius: 10px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <div>
                            <h5 style="color: var(--gold); margin: 0 0 5px 0; font-size: 1.1rem; font-weight: 700;">نون ساكن (Noon Sakin & Tanween)</h5>
                            <p style="color: var(--light-green); opacity: 0.8; font-size: 0.85rem; margin: 0;">Proper nasalization and pronunciation rules</p>
                        </div>
                        @php
                            $noonColor = $analysis['noon_sakin_analysis']['percentage'] >= 90 ? '#4caf50' : ($analysis['noon_sakin_analysis']['percentage'] >= 70 ? '#8bc34a' : '#ff9800');
                        @endphp
                        <div style="padding: 8px 15px; background: rgba(77, 139, 49, 0.2); border: 2px solid {{ $noonColor }}; border-radius: 10px;">
                            <span style="color: {{ $noonColor }}; font-weight: 700; font-size: 1.3rem;">{{ $analysis['noon_sakin_analysis']['percentage'] }}%</span>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div style="width: 100%; height: 8px; background: rgba(70, 63, 58, 0.5); border-radius: 10px; overflow: hidden; margin-bottom: 15px;">
                        <div style="width: {{ $analysis['noon_sakin_analysis']['percentage'] }}%; height: 100%; background: linear-gradient(90deg, {{ $noonColor }}, {{ $noonColor }}dd); transition: width 0.5s ease;"></div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 15px;">
                        <div style="background: rgba(77, 139, 49, 0.15); padding: 12px; border-radius: 8px; text-align: center;">
                            <div style="color: var(--light-green); font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px;">Total Found</div>
                            <div style="color: var(--gold); font-size: 1.5rem; font-weight: 700; margin-top: 5px;">{{ $analysis['noon_sakin_analysis']['total_occurrences'] }}</div>
                        </div>
                        <div style="background: rgba(76, 175, 80, 0.15); padding: 12px; border-radius: 8px; text-align: center;">
                            <div style="color: var(--light-green); font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px;">Correct</div>
                            <div style="color: #4caf50; font-size: 1.5rem; font-weight: 700; margin-top: 5px;">{{ $analysis['noon_sakin_analysis']['correct_pronunciation'] }}</div>
                        </div>
                        <div style="background: rgba(244, 67, 54, 0.15); padding: 12px; border-radius: 8px; text-align: center;">
                            <div style="color: var(--light-green); font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px;">Issues</div>
                            <div style="color: #f44336; font-size: 1.5rem; font-weight: 700; margin-top: 5px;">{{ count($analysis['noon_sakin_analysis']['issues']) }}</div>
                        </div>
                    </div>

                    @if(count($analysis['noon_sakin_analysis']['issues']) > 0)
                    <div style="background: rgba(255, 152, 0, 0.08); border-left: 4px solid #ff9800; padding: 12px 15px; border-radius: 8px;">
                        <div style="color: #ff9800; font-size: 0.9rem; font-weight: 700; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                            <span>⚠️</span>
                            <span>Issues Detected ({{ count($analysis['noon_sakin_analysis']['issues']) }})</span>
                        </div>
                        <div style="max-height: 200px; overflow-y: auto;">
                            @foreach($analysis['noon_sakin_analysis']['issues'] as $index => $issue)
                            <div style="background: rgba(31, 39, 27, 0.5); padding: 10px 12px; border-radius: 6px; margin-bottom: 8px; font-size: 0.85rem;">
                                <div style="display: flex; align-items: start; gap: 10px; margin-bottom: 6px;">
                                    <span style="background: #ff9800; color: #1f271b; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; min-width: 50px; text-align: center;">Word {{ $issue['position'] ?? $index + 1 }}</span>
                                    <span style="color: var(--light-green); flex: 1; line-height: 1.5; font-weight: 600;">{{ $issue['word'] ?? '' }}</span>
                                </div>
                                <div style="color: #ffa726; font-size: 0.8rem; margin-bottom: 4px;">{{ $issue['issue'] }}</div>
                                <div style="color: rgba(211, 255, 177, 0.7); font-size: 0.75rem;">💡 {{ $issue['recommendation'] ?? '' }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div style="background: rgba(76, 175, 80, 0.1); border-left: 4px solid #4caf50; padding: 12px 15px; border-radius: 8px;">
                        <div style="color: #4caf50; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                            <span>✓</span>
                            <span>Excellent! No issues detected in Noon Sakin pronunciation.</span>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Overall AI Feedback -->
                @if(isset($analysis['overall_score']['feedback']))
                <div style="background: linear-gradient(135deg, rgba(227, 216, 136, 0.15) 0%, rgba(227, 216, 136, 0.05) 100%); border: 2px solid rgba(227, 216, 136, 0.3); padding: 18px; border-radius: 10px; box-shadow: 0 4px 15px rgba(227, 216, 136, 0.1);">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                        <div style="width: 35px; height: 35px; background: var(--gold); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: 0 4px 10px rgba(227, 216, 136, 0.3);">💬</div>
                        <div style="color: var(--gold); font-weight: 700; font-size: 1rem;">AI-Generated Feedback</div>
                    </div>
                    <p style="color: var(--light-green); margin: 0; font-size: 0.95rem; line-height: 1.8; padding-left: 45px;">
                        {{ $analysis['overall_score']['feedback'] }}
                    </p>
                </div>
                @endif

                <!-- Analysis Info Footer -->
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(77, 139, 49, 0.3); display: flex; align-items: center; justify-content: space-between;">
                    <div style="font-size: 0.75rem; color: var(--light-green); opacity: 0.6;">
                        <span>🤖 Analyzed using advanced audio processing (librosa, scipy)</span>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--light-green); opacity: 0.6;">
                        <span>⏱️ {{ $submission->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div> <!-- End Column 3: Tajweed Analysis -->
            
        <!-- Column 4: AI Teaching Assistant (350px fixed width) -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            @if($submission->tajweed_analysis)
            @php
                $analysis = is_array($submission->tajweed_analysis) ? $submission->tajweed_analysis : json_decode($submission->tajweed_analysis, true);
            @endphp
            {{-- OpenAI Intelligent Feedback for Teacher Reference --}}
            {{-- Always show AI feedback box, with message if not available yet --}}
            <div style="background: white; border: 3px solid #667eea; padding: 25px; border-radius: 20px; box-shadow: 0 8px 30px rgba(102, 126, 234, 0.2); height: fit-content; position: sticky; top: 20px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid rgba(102, 126, 234, 0.15);">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);">🤖</div>
                    <div>
                        <div style="color: #667eea; font-weight: 700; font-size: 1.2rem; margin-bottom: 3px;">AI Assistant</div>
                        <div style="font-size: 0.75rem; padding: 4px 10px; background: rgba(102, 126, 234, 0.1); border-radius: 10px; font-weight: 600; color: #667eea; display: inline-block;">Reference Only</div>
                    </div>
                </div>

                @if(isset($analysis['ai_feedback']))
                    @if(isset($analysis['ai_feedback']['summary']))
                    <div style="background: rgba(102, 126, 234, 0.08); padding: 20px; border-radius: 12px; margin-bottom: 15px; border-left: 4px solid #667eea;">
                        <h4 style="color: #667eea; font-size: 1rem; font-weight: 700; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 1.1rem;">📊</span> <span>Summary</span>
                        </h4>
                        <p style="color: #1a1a1a; line-height: 1.7; margin: 0; font-size: 0.95rem;">{{ $analysis['ai_feedback']['summary'] }}</p>
                    </div>
                    @endif

                    <div style="display: grid; grid-template-columns: 1fr; gap: 15px; margin-bottom: 15px;">
                        @if(isset($analysis['ai_feedback']['strengths']) && count($analysis['ai_feedback']['strengths']) > 0)
                        <div style="background: rgba(76, 175, 80, 0.08); padding: 18px; border-radius: 12px; border-left: 4px solid #4caf50;">
                            <h4 style="color: #4caf50; font-size: 1rem; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 1.1rem;">💪</span> <span>Strengths</span>
                            </h4>
                            <ul style="margin: 0; padding-left: 18px; color: #1a1a1a; line-height: 1.6; font-size: 0.9rem;">
                                @foreach($analysis['ai_feedback']['strengths'] as $strength)
                                <li style="margin-bottom: 8px;">{{ $strength }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(isset($analysis['ai_feedback']['improvements']) && count($analysis['ai_feedback']['improvements']) > 0)
                        <div style="background: rgba(255, 152, 0, 0.08); padding: 18px; border-radius: 12px; border-left: 4px solid #ff9800;">
                            <h4 style="color: #ff9800; font-size: 1rem; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 1.1rem;">🎯</span> <span>Improvements</span>
                            </h4>
                            <ul style="margin: 0; padding-left: 18px; color: #1a1a1a; line-height: 1.6; font-size: 0.9rem;">
                                @foreach($analysis['ai_feedback']['improvements'] as $improvement)
                                <li style="margin-bottom: 8px;">
                                    @if(is_array($improvement))
                                        {{ $improvement['issue'] ?? '' }}
                                    @else
                                        {{ $improvement }}
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>

                    @if(isset($analysis['ai_feedback']['next_steps']))
                    <div style="background: rgba(102, 126, 234, 0.08); padding: 18px; border-radius: 12px; border-left: 4px solid #667eea;">
                        <h4 style="color: #667eea; font-size: 1rem; font-weight: 700; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 1.1rem;">🚀</span> <span>Next Steps</span>
                        </h4>
                        <p style="color: #1a1a1a; line-height: 1.6; margin: 0; font-size: 0.9rem;">{{ $analysis['ai_feedback']['next_steps'] }}</p>
                    </div>
                    @endif
                @else
                    {{-- No AI feedback available --}}
                    <div style="background: rgba(255, 107, 107, 0.08); padding: 20px; border-radius: 12px; border-left: 4px solid #ff6b6b; text-align: center;">
                        <div style="font-size: 2.5rem; margin-bottom: 10px; opacity: 0.6;">⚠️</div>
                        <h4 style="color: #ff6b6b; font-size: 1rem; font-weight: 700; margin-bottom: 8px;">Not Generated</h4>
                        <p style="color: #666; margin: 0; line-height: 1.6; font-size: 0.85rem;">
                            {{ $analysis['overall_score']['feedback'] ?? 'Failed to generate AI feedback. Check logs.' }}
                        </p>
                    </div>
                @endif
            </div>
            @else
            <!-- No Tajweed Analysis - Placeholder -->
            <div style="background: rgba(149, 165, 166, 0.1); border: 2px dashed #95a5a6; border-radius: 12px; padding: 40px; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 15px; opacity: 0.4;">📊</div>
                <h4 style="color: #7f8c8d; font-size: 1.1rem; margin-bottom: 10px;">No AI Feedback</h4>
                <p style="color: #95a5a6; font-size: 0.9rem; margin: 0;">Analysis not yet available</p>
            </div>
            @endif
        </div> <!-- End Column 4: AI Teaching Assistant -->
    </div> <!-- Close 4-column grid -->
</div>
@endsection