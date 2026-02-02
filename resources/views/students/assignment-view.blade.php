@extends('layouts.dashboard')

@section('title', 'View Submission')
@section('user-role', 'Student • View Submission')

@section('navigation')
    <a href="{{ url('/student/classes') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="{{ url('/student/classes') }}" class="nav-item active">
        <div class="nav-icon"><i class="fas fa-users"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <a href="{{ url('/student/practice') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-microphone-alt"></i></div>
        <div class="nav-label">Practice</div>
    </a>
    <a href="{{ url('/student/progress') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-chart-line"></i></div>
        <div class="nav-label">My Progress</div>
    </a>
    <a href="{{ url('/student/materials') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
        <div class="nav-label">Materials</div>
    </a>
    <a href="{{ route('students.show', Auth::id()) }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-user-circle"></i></div>
        <div class="nav-label">Profile</div>
    </a>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;" class="nav-item">
        @csrf
        <button type="submit" style="all: unset; width: 100%; cursor: pointer;">
            <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
            <div class="nav-label">Logout</div>
        </button>
    </form>
@endsection

@section('content')
<div style="padding: 0;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('classroom.show', $assignment->class_id) }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--color-gold); text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.color='var(--color-light-green)'" onmouseout="this.style.color='var(--color-gold)'">
            ← Back to Classroom
        </a>
    </div>

    <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); margin-bottom: 25px;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                    <div style="width: 60px; height: 60px; background: var(--color-dark-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                        📝
                    </div>
                    <div>
                        <h2 style="color: var(--color-gold); font-size: 1.5rem; margin-bottom: 5px;">
                            @if($assignment->surah)
                                📖 {{ $assignment->surah }} ({{ $assignment->start_verse }}@if($assignment->end_verse)-{{ $assignment->end_verse }}@endif)
                            @else
                                {{ $assignment->material ? $assignment->material->title : 'Assignment' }}
                            @endif
                        </h2>
                        <p style="color: var(--color-light-green); opacity: 0.8; font-size: 0.9rem; margin: 0;">{{ $assignment->classroom->class_name }}</p>
                    </div>
                </div>
            </div>
            <div style="text-align: right;">
                @php
                    $score = \App\Models\Score::where('assignment_id', $assignment->assignment_id)
                                             ->where('user_id', Auth::id())
                                             ->first();
                @endphp
                @if($score)
                    <div style="background: #4caf50; color: white; padding: 12px 20px; border-radius: 10px; font-weight: 600; font-size: 1.1rem; margin-bottom: 8px; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);">
                        ✓ Graded: {{ $score->score }}/{{ $assignment->total_marks }}
                    </div>
                @else
                    <div style="background: #ff9800; color: white; padding: 12px 20px; border-radius: 10px; font-weight: 600; font-size: 1.1rem; margin-bottom: 8px; box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);">
                        ⏳ Awaiting Grading
                    </div>
                @endif
                <div style="color: var(--color-light-green); font-size: 0.85rem; opacity: 0.8;">
                    Submitted: {{ $submission->submitted_at->format('M d, Y h:i A') }}
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
            <div style="background: rgba(77, 139, 49, 0.2); padding: 15px; border-radius: 10px; border: 1px solid rgba(77, 139, 49, 0.4);">
                <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 5px; font-size: 0.85rem;">📅 Due Date</div>
                <div style="color: var(--color-light-green); font-size: 1rem;">{{ $assignment->due_date->format('M d, Y h:i A') }}</div>
            </div>
            <div style="background: rgba(77, 139, 49, 0.2); padding: 15px; border-radius: 10px; border: 1px solid rgba(77, 139, 49, 0.4);">
                <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 5px; font-size: 0.85rem;">🎯 Total Marks</div>
                <div style="color: var(--color-light-green); font-size: 1rem;">{{ $assignment->total_marks }} points</div>
            </div>
            @if(isset($score) && $score)
            <div style="background: rgba(76, 175, 80, 0.2); padding: 15px; border-radius: 10px; border: 1px solid rgba(76, 175, 80, 0.4);">
                <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 5px; font-size: 0.85rem;">📊 Your Score</div>
                <div style="color: #4caf50; font-size: 1rem; font-weight: 600;">{{ $score->score }}/{{ $assignment->total_marks }}</div>
            </div>
            @endif
        </div>

        @if($submission->text_submission)
        <div style="background: rgba(70, 63, 58, 0.4); padding: 20px; border-radius: 12px; border: 2px solid rgba(77, 139, 49, 0.2); margin-bottom: 25px;">
            <h3 style="color: var(--color-gold); font-size: 1.2rem; margin-bottom: 15px;">📄 Your Written Response</h3>
            <p style="color: var(--color-light-green); line-height: 1.8; margin: 0; white-space: pre-wrap;">{{ $submission->text_submission }}</p>
        </div>
        @endif

        @if($submission->audio_file_path)
        <div style="background: rgba(70, 63, 58, 0.4); padding: 20px; border-radius: 12px; border: 2px solid rgba(77, 139, 49, 0.2); margin-bottom: 25px;">
            <h3 style="color: var(--color-gold); font-size: 1.2rem; margin-bottom: 15px;">🎤 Audio Recording</h3>
            @php
                $audioPath = storage_path('app/public/' . $submission->audio_file_path);
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
            @endphp
            <audio controls preload="auto" style="width: 100%; margin-top: 10px; outline: none;" src="{{ $audioUrl }}">
                <source src="{{ $audioUrl }}" type="{{ $detectedMime }}">
                <source src="{{ $audioUrl }}" type="audio/webm">
                <source src="{{ $audioUrl }}" type="audio/mpeg">
                <source src="{{ $audioUrl }}" type="audio/wav">
                <source src="{{ $audioUrl }}" type="audio/ogg">
                <source src="{{ $audioUrl }}" type="audio/mp4">
                <source src="{{ $audioUrl }}" type="audio/x-m4a">
                Your browser does not support the audio element.
            </audio>
            <div style="display: flex; gap: 10px; align-items: center; margin-top: 12px;">
                <div style="font-size: 0.75rem; color: var(--color-light-green); opacity: 0.6; flex: 1;">
                    📁 {{ basename($submission->audio_file_path) }}
                </div>
                <a href="{{ $audioUrl }}" download="{{ basename($submission->audio_file_path) }}" 
                   style="padding: 8px 16px; background: rgba(227, 216, 136, 0.2); color: var(--color-gold); border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: all 0.3s ease; border: 1px solid rgba(227, 216, 136, 0.3);"
                   onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                   onmouseout="this.style.background='rgba(227, 216, 136, 0.2)'; this.style.color='var(--color-gold)'">
                    ⬇️ Download Audio
                </a>
            </div>
            
            @if($submission->transcription)
            <div style="margin-top: 20px; padding: 20px; background: rgba(31, 39, 27, 0.5); border-radius: 10px; border: 2px solid rgba(227, 216, 136, 0.3);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <span style="font-size: 1.2rem;">📝</span>
                    <h4 style="color: var(--color-gold); font-size: 1rem; font-weight: 600; margin: 0;">AI Transcription</h4>
                    <span style="background: rgba(227, 216, 136, 0.2); color: var(--color-gold); padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">Powered by AssemblyAI</span>
                </div>
                <p style="color: var(--color-light-green); line-height: 2.2; margin: 0; white-space: pre-wrap; direction: rtl; text-align: right; font-size: 1.8rem; font-family: 'Amiri', 'Traditional Arabic', serif; letter-spacing: 0.5px;">{{ $submission->transcription }}</p>
            </div>
            @endif

            @if($submission->tajweed_analysis)
                @php
                    $analysis = json_decode($submission->tajweed_analysis, true);
                    $score = \App\Models\Score::where('assignment_id', $assignment->assignment_id)
                                             ->where('user_id', Auth::id())
                                             ->first();
                @endphp

                <!-- Tajweed Analysis Results -->
                <div style="margin-top: 25px; padding: 25px; background: linear-gradient(135deg, rgba(77, 139, 49, 0.15) 0%, rgba(31, 39, 27, 0.6) 100%); border-radius: 15px; border: 2px solid rgba(77, 139, 49, 0.4); box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid rgba(227, 216, 136, 0.3);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span style="font-size: 1.8rem;">🎯</span>
                            <h4 style="color: var(--color-gold); font-size: 1.3rem; font-weight: 700; margin: 0;">Tajweed Analysis Results</h4>
                        </div>
                        @if($score)
                        <div style="background: rgba(227, 216, 136, 0.2); padding: 10px 20px; border-radius: 10px; border: 2px solid var(--color-gold);">
                            <div style="color: var(--color-light); font-size: 0.85rem; opacity: 0.8; margin-bottom: 2px;">Your Score</div>
                            <div style="color: var(--color-gold); font-size: 1.5rem; font-weight: 700;">{{ $score->score }}/{{ $assignment->total_marks }}</div>
                        </div>
                        @endif
                    </div>

                    <!-- Overall Score Card -->
                    <div style="background: rgba(227, 216, 136, 0.1); padding: 20px; border-radius: 12px; border: 2px solid rgba(227, 216, 136, 0.3); margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                            <div>
                                <div style="color: var(--color-light); font-size: 0.9rem; opacity: 0.8; margin-bottom: 5px;">Overall Performance</div>
                                <div style="color: var(--color-gold); font-size: 2rem; font-weight: 700;">{{ $analysis['overall_score']['score'] ?? 0 }}%</div>
                                <div style="color: var(--color-light-green); font-size: 1.1rem; font-weight: 600; margin-top: 5px;">{{ $analysis['overall_score']['grade'] ?? 'N/A' }}</div>
                            </div>
                            <div style="flex: 1; min-width: 250px;">
                                <div style="color: var(--color-light); font-size: 0.85rem; margin-bottom: 8px; opacity: 0.8;">Performance Breakdown</div>
                                <div style="background: rgba(31, 39, 27, 0.5); border-radius: 8px; height: 12px; overflow: hidden;">
                                    <div style="background: linear-gradient(90deg, #4caf50 0%, var(--color-gold) 50%, #ff9800 100%); height: 100%; width: {{ $analysis['overall_score']['score'] ?? 0 }}%; transition: width 0.5s ease;"></div>
                                </div>
                            </div>
                        </div>
                        @if(isset($analysis['overall_score']['feedback']))
                        <div style="margin-top: 15px; padding: 15px; background: rgba(31, 39, 27, 0.4); border-radius: 8px; border-left: 4px solid var(--color-gold);">
                            <p style="color: var(--color-light-green); line-height: 1.6; margin: 0;">{{ $analysis['overall_score']['feedback'] }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Detailed Metrics -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 20px;">
                        <!-- Text Accuracy -->
                        <div style="background: rgba(31, 39, 27, 0.6); padding: 20px; border-radius: 12px; border: 2px solid rgba(76, 175, 80, 0.3);">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <span style="font-size: 1.3rem;">📖</span>
                                <h5 style="color: var(--color-light-green); font-size: 1rem; font-weight: 600; margin: 0;">Text Accuracy</h5>
                            </div>
                            <div style="color: #4caf50; font-size: 2rem; font-weight: 700; margin-bottom: 5px;">{{ $analysis['text_accuracy'] ?? 0 }}%</div>
                            <div style="color: var(--color-light); font-size: 0.85rem; opacity: 0.8;">Word-level precision</div>
                        </div>

                        <!-- Madd Analysis -->
                        @if(isset($analysis['madd_analysis']))
                        <div style="background: rgba(31, 39, 27, 0.6); padding: 20px; border-radius: 12px; border: 2px solid rgba(227, 216, 136, 0.3);">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <span style="font-size: 1.3rem;">📏</span>
                                <h5 style="color: var(--color-gold); font-size: 1rem; font-weight: 600; margin: 0;">Madd (Elongation)</h5>
                            </div>
                            <div style="color: var(--color-gold); font-size: 2rem; font-weight: 700; margin-bottom: 5px;">{{ $analysis['madd_analysis']['percentage'] ?? 0 }}%</div>
                            <div style="color: var(--color-light); font-size: 0.85rem; opacity: 0.8;">{{ $analysis['madd_analysis']['correct_elongations'] ?? 0 }}/{{ $analysis['madd_analysis']['total_elongations'] ?? 0 }} correct</div>
                        </div>
                        @endif

                        <!-- Noon Sakin Analysis -->
                        @if(isset($analysis['noon_sakin_analysis']))
                        <div style="background: rgba(31, 39, 27, 0.6); padding: 20px; border-radius: 12px; border: 2px solid rgba(255, 152, 0, 0.3);">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <span style="font-size: 1.3rem;">🔤</span>
                                <h5 style="color: #ff9800; font-size: 1rem; font-weight: 600; margin: 0;">Noon Sakin/Tanween</h5>
                            </div>
                            <div style="color: #ff9800; font-size: 2rem; font-weight: 700; margin-bottom: 5px;">{{ $analysis['noon_sakin_analysis']['percentage'] ?? 0 }}%</div>
                            <div style="color: var(--color-light); font-size: 0.85rem; opacity: 0.8;">{{ $analysis['noon_sakin_analysis']['correct_pronunciation'] ?? 0 }}/{{ $analysis['noon_sakin_analysis']['total_occurrences'] ?? 0 }} correct</div>
                        </div>
                        @endif
                    </div>

                    <!-- Word Errors -->
                    @if(isset($analysis['word_errors']) && count($analysis['word_errors']) > 0)
                    <div style="background: rgba(244, 67, 54, 0.1); padding: 20px; border-radius: 12px; border: 2px solid rgba(244, 67, 54, 0.3); margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                            <span style="font-size: 1.3rem;">⚠️</span>
                            <h5 style="color: #f44336; font-size: 1rem; font-weight: 600; margin: 0;">Areas for Improvement</h5>
                        </div>
                        <div style="max-height: 200px; overflow-y: auto;">
                            @foreach(array_slice($analysis['word_errors'], 0, 5) as $error)
                            <div style="padding: 10px; background: rgba(31, 39, 27, 0.4); border-radius: 8px; margin-bottom: 10px; border-left: 3px solid #f44336;">
                                <div style="color: var(--color-light); font-size: 0.85rem; opacity: 0.8; margin-bottom: 3px;">Word {{ $error['position'] }}</div>
                                <div style="display: flex; gap: 15px; align-items: center;">
                                    <div>
                                        <span style="color: var(--color-light); font-size: 0.8rem; opacity: 0.7;">Expected:</span>
                                        <span style="color: var(--color-gold); font-size: 1rem; font-weight: 600; direction: rtl;">{{ $error['expected'] }}</span>
                                    </div>
                                    <span style="color: var(--color-light); opacity: 0.5;">→</span>
                                    <div>
                                        <span style="color: var(--color-light); font-size: 0.8rem; opacity: 0.7;">Actual:</span>
                                        <span style="color: #f44336; font-size: 1rem; font-weight: 600; direction: rtl;">{{ $error['actual'] ?: '(missing)' }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if(count($analysis['word_errors']) > 5)
                        <div style="color: var(--color-light); font-size: 0.85rem; opacity: 0.7; margin-top: 10px; text-align: center;">
                            And {{ count($analysis['word_errors']) - 5 }} more errors...
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Tajweed Rule Issues -->
                    @if((isset($analysis['madd_analysis']['issues']) && count($analysis['madd_analysis']['issues']) > 0) || 
                        (isset($analysis['noon_sakin_analysis']['issues']) && count($analysis['noon_sakin_analysis']['issues']) > 0))
                    <div style="background: rgba(255, 152, 0, 0.1); padding: 20px; border-radius: 12px; border: 2px solid rgba(255, 152, 0, 0.3);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                            <span style="font-size: 1.3rem;">💡</span>
                            <h5 style="color: #ff9800; font-size: 1rem; font-weight: 600; margin: 0;">Tajweed Recommendations</h5>
                        </div>

                        @if(isset($analysis['madd_analysis']['issues']) && count($analysis['madd_analysis']['issues']) > 0)
                        <div style="margin-bottom: 15px;">
                            <h6 style="color: var(--color-gold); font-size: 0.9rem; font-weight: 600; margin-bottom: 10px;">Madd (Elongation) Issues:</h6>
                            @foreach(array_slice($analysis['madd_analysis']['issues'], 0, 3) as $issue)
                            <div style="padding: 12px; background: rgba(31, 39, 27, 0.4); border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--color-gold);">
                                <div style="color: var(--color-light-green); font-size: 0.9rem; direction: rtl; margin-bottom: 5px;">{{ $issue['word'] ?? 'N/A' }}</div>
                                <div style="color: var(--color-light); font-size: 0.85rem; opacity: 0.8;">{{ $issue['recommendation'] ?? $issue['issue'] ?? 'Review Madd rules' }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($analysis['noon_sakin_analysis']['issues']) && count($analysis['noon_sakin_analysis']['issues']) > 0)
                        <div>
                            <h6 style="color: #ff9800; font-size: 0.9rem; font-weight: 600; margin-bottom: 10px;">Noon Sakin/Tanween Issues:</h6>
                            @foreach(array_slice($analysis['noon_sakin_analysis']['issues'], 0, 3) as $issue)
                            <div style="padding: 12px; background: rgba(31, 39, 27, 0.4); border-radius: 8px; margin-bottom: 8px; border-left: 3px solid #ff9800;">
                                <div style="color: var(--color-light-green); font-size: 0.9rem; direction: rtl; margin-bottom: 5px;">{{ $issue['word'] ?? 'N/A' }}</div>
                                <div style="color: var(--color-light); font-size: 0.85rem; opacity: 0.8;">{{ $issue['recommendation'] ?? $issue['issue'] ?? 'Review Noon Sakin rules' }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            @endif
            
            {{-- OpenAI Intelligent Feedback --}}
            {{-- Always show AI feedback box, with message if not available yet --}}
            <div style="background: linear-gradient(135deg, rgba(227, 216, 136, 0.15) 0%, rgba(31, 39, 27, 0.8) 100%); border: 2px solid rgba(227, 216, 136, 0.4); padding: 25px; border-radius: 15px; margin-top: 20px; box-shadow: 0 6px 25px rgba(0, 0, 0, 0.3);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid rgba(227, 216, 136, 0.3);">
                    <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">🤖</div>
                    <div>
                        <div style="color: var(--color-gold); font-weight: 700; font-size: 1.2rem;">AI-Powered Recitation Coach</div>
                        <div style="font-size: 0.8rem; padding: 4px 12px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.3), rgba(118, 75, 162, 0.3)); border-radius: 15px; font-weight: 600; color: #b8a3ff; display: inline-block; margin-top: 4px;">✨ Personalized Feedback by OpenAI GPT-4</div>
                    </div>
                </div>

                @if(isset($analysis['ai_feedback']))
                    @if(isset($analysis['ai_feedback']['summary']))
                    <div style="background: rgba(31, 39, 27, 0.6); padding: 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #667eea;">
                        <h4 style="color: #b8a3ff; font-size: 1rem; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <span>📊</span> Performance Summary
                        </h4>
                        <p style="color: var(--color-light-green); line-height: 1.8; margin: 0; white-space: pre-wrap;">{{ $analysis['ai_feedback']['summary'] }}</p>
                    </div>
                    @endif

                    @if(isset($analysis['ai_feedback']['strengths']) && count($analysis['ai_feedback']['strengths']) > 0)
                    <div style="background: rgba(76, 175, 80, 0.15); padding: 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #4caf50;">
                        <h4 style="color: #4caf50; font-size: 1rem; font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                            <span>💪</span> Your Strengths
                        </h4>
                        <ul style="margin: 0; padding-left: 20px; color: var(--color-light-green); line-height: 2;">
                            @foreach($analysis['ai_feedback']['strengths'] as $strength)
                            <li style="margin-bottom: 8px;">{{ $strength }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($analysis['ai_feedback']['improvements']) && count($analysis['ai_feedback']['improvements']) > 0)
                    <div style="background: rgba(255, 152, 0, 0.15); padding: 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #ff9800;">
                        <h4 style="color: #ff9800; font-size: 1rem; font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                            <span>🎯</span> Areas for Improvement
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            @foreach($analysis['ai_feedback']['improvements'] as $improvement)
                            <div style="background: rgba(31, 39, 27, 0.5); padding: 15px; border-radius: 10px; border-left: 3px solid #ff9800;">
                                <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 8px; font-size: 0.95rem;">{{ $improvement['issue'] ?? $improvement }}</div>
                                @if(isset($improvement['suggestion']))
                                <div style="color: var(--color-light-green); opacity: 0.9; font-size: 0.9rem; line-height: 1.6;">💡 {{ $improvement['suggestion'] }}</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($analysis['ai_feedback']['next_steps']))
                    <div style="background: rgba(102, 126, 234, 0.15); padding: 20px; border-radius: 12px; border-left: 4px solid #667eea;">
                        <h4 style="color: #b8a3ff; font-size: 1rem; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <span>🚀</span> Next Steps for Mastery
                        </h4>
                        <p style="color: var(--color-light-green); line-height: 1.8; margin: 0; white-space: pre-wrap;">{{ $analysis['ai_feedback']['next_steps'] }}</p>
                    </div>
                    @endif
                @else
                    {{-- No AI feedback available --}}
                    <div style="background: rgba(255, 107, 107, 0.15); padding: 20px; border-radius: 12px; border-left: 4px solid #ff6b6b; text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 15px; opacity: 0.6;">⚠️</div>
                        <h4 style="color: #ff6b6b; font-size: 1.1rem; font-weight: 600; margin-bottom: 10px;">AI Feedback Not Available</h4>
                        <p style="color: var(--color-light-green); opacity: 0.8; margin: 0; line-height: 1.6;">
                            {{ $analysis['overall_score']['feedback'] ?? 'The AI audio analyzer is not generating feedback. This could be due to missing Python dependencies (parselmouth, fastdtw) or configuration issues. Please check the server logs for detailed error messages.' }}
                        </p>
                        @if(isset($analysis['overall_score']['feedback']) && str_contains($analysis['overall_score']['feedback'], 'dependencies'))
                        <div style="margin-top: 15px; padding: 15px; background: rgba(31, 39, 27, 0.6); border-radius: 8px; text-align: left;">
                            <div style="color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">🔧 Required Python packages:</div>
                            <code style="display: block; color: #b8a3ff; font-size: 0.85rem; line-height: 1.8;">
                                pip install praat-parselmouth fastdtw librosa openai
                            </code>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endif

        @if($submission->teacher_feedback || (isset($score) && $score->feedback))
        <div style="background: rgba(227, 216, 136, 0.1); padding: 20px; border-radius: 12px; border: 2px solid rgba(227, 216, 136, 0.3); margin-bottom: 25px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <span style="font-size: 1.5rem;">👨‍🏫</span>
                <h3 style="color: var(--color-gold); font-size: 1.2rem; margin: 0;">Teacher Feedback</h3>
            </div>
            <p style="color: var(--color-light-green); line-height: 1.8; margin: 0; white-space: pre-wrap;">{{ $submission->teacher_feedback ?? $score->feedback ?? 'No feedback yet.' }}</p>
        </div>
        @endif

        @if($assignment->material)
        <div style="background: rgba(77, 139, 49, 0.1); padding: 20px; border-radius: 12px; border: 2px solid rgba(77, 139, 49, 0.3);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span style="font-size: 1.3rem;">📚</span>
                <h4 style="color: var(--color-gold); font-size: 1rem; font-weight: 600; margin: 0;">Assignment Material</h4>
            </div>
            <p style="color: var(--color-light-green); margin: 0 0 15px 0; opacity: 0.9;">{{ $assignment->material->title }}</p>
            <a href="{{ route('student.material.show', $assignment->material->material_id) }}" class="btn-secondary" style="text-decoration: none; display: inline-block;">
                View Material →
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
