@extends('layouts.dashboard')

@section('title', 'Grade Submission')
@section('user-role', 'Teacher • Grade Assignment')

@section('navigation')
    <a href="{{ route('home') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="{{ route('classroom.index') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <a href="{{ route('materials.index') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
        <div class="nav-label">Materials</div>
    </a>
    <a href="{{ route('teachers.show', Auth::id()) }}" class="nav-item">
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
        <a href="{{ route('teacher.student.submissions', ['classroom' => $submission->assignment->class_id, 'student' => $submission->student_id]) }}" 
            style="display: inline-flex; align-items: center; gap: 8px; color: var(--gold); text-decoration: none; font-weight: 600; transition: all 0.3s ease;" 
            onmouseover="this.style.color='var(--light-green)'" 
            onmouseout="this.style.color='var(--gold)'">
            ← Back to Student Submissions
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
        <!-- Submission Details -->
        <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
                <div style="width: 50px; height: 50px; background: var(--primary-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                    📝
                </div>
                <div>
                    <h3 style="color: var(--gold); font-size: 1.3rem; margin-bottom: 5px;">Submission Details</h3>
                    <p style="color: var(--light-green); opacity: 0.8; font-size: 0.9rem; margin: 0;">Review student work</p>
                </div>
            </div>

            <!-- Student Info -->
            <div style="background: rgba(70, 63, 58, 0.4); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <div style="color: var(--gold); font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">👤 Student</div>
                <div style="color: var(--light-green); font-size: 1rem; margin-bottom: 5px;">{{ $submission->student->name }}</div>
                <div style="color: var(--light-green); font-size: 0.85rem; opacity: 0.8;">{{ $submission->student->email }}</div>
            </div>

            <!-- Assignment Info -->
            <div style="background: rgba(70, 63, 58, 0.4); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <div style="color: var(--gold); font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">📋 Assignment</div>
                <h4 style="color: var(--light-green); margin: 0 0 10px 0; font-size: 1rem;">
                    @if($submission->assignment->surah)
                        📖 {{ $submission->assignment->surah }} 
                        ({{ $submission->assignment->start_verse }}@if($submission->assignment->end_verse)-{{ $submission->assignment->end_verse }}@endif)
                    @else
                        {{ $submission->assignment->material ? $submission->assignment->material->title : 'Assignment' }}
                    @endif
                </h4>
                <p style="color: var(--light-green); opacity: 0.9; margin: 0 0 10px 0; font-size: 0.9rem; line-height: 1.6;">{{ $submission->assignment->instructions }}</p>
                <div style="display: flex; gap: 15px; font-size: 0.85rem; color: var(--light-green); opacity: 0.8;">
                    <span>🎯 Total Marks: {{ $submission->assignment->total_marks }}</span>
                    <span>📅 Due: {{ $submission->assignment->due_date->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- Submission Time -->
            <div style="background: rgba(70, 63, 58, 0.4); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <div style="color: var(--gold); font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">⏰ Submission Time</div>
                <div style="color: var(--light-green); font-size: 1rem;">
                    {{ $submission->created_at->format('M d, Y h:i A') }}
                    @if($submission->created_at->gt($submission->assignment->due_date))
                        <span style="color: #e74c3c; font-weight: 600; margin-left: 10px;">⚠️ Late Submission</span>
                    @else
                        <span style="color: #4caf50; font-weight: 600; margin-left: 10px;">✓ On Time</span>
                    @endif
                </div>
            </div>

            @if($submission->audio_file_path)
            <!-- Audio Submission -->
            <div style="background: rgba(70, 63, 58, 0.4); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <div style="color: var(--gold); font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">🎤 Voice Recording</div>
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

            @if($submission->transcription)
            <!-- AI Transcription -->
            <div style="background: rgba(70, 63, 58, 0.4); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 8px; color: var(--gold); font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">
                    <span>🤖 AI Transcription</span>
                    <span style="font-size: 0.75rem; padding: 3px 8px; background: rgba(227, 216, 136, 0.2); border-radius: 12px; font-weight: 500;">Powered by AssemblyAI</span>
                </div>
                <div style="background: rgba(31, 39, 27, 0.5); padding: 15px; border-radius: 8px; max-height: 200px; overflow-y: auto;">
                    <p style="color: var(--light-green); margin: 0; line-height: 2.2; direction: rtl; text-align: right; font-size: 1.8rem; font-family: 'Amiri', 'Traditional Arabic', serif; letter-spacing: 0.5px;">
                        {{ $submission->transcription }}
                    </p>
                </div>
            </div>
            @endif

            @if($submission->tajweed_analysis)
            <!-- Tajweed Analysis -->
            <div style="background: linear-gradient(135deg, rgba(77, 139, 49, 0.15) 0%, rgba(31, 39, 27, 0.6) 100%); border: 2px solid rgba(77, 139, 49, 0.4); padding: 20px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; background: var(--primary-green); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">📖</div>
                        <div>
                            <div style="color: var(--gold); font-weight: 700; font-size: 1.1rem;">Tajweed Analysis</div>
                            <div style="font-size: 0.75rem; padding: 3px 10px; background: rgba(227, 216, 136, 0.2); border-radius: 12px; font-weight: 500; color: var(--gold); display: inline-block; margin-top: 3px;">🤖 AI-Powered</div>
                        </div>
                    </div>
                    @php
                        $scoreColor = $submission->tajweed_score >= 90 ? '#4caf50' : ($submission->tajweed_score >= 70 ? '#8bc34a' : ($submission->tajweed_score >= 60 ? '#ff9800' : '#f44336'));
                    @endphp
                    <div style="padding: 12px 20px; background: rgba(76, 175, 80, 0.15); border: 2px solid {{ $scoreColor }}; border-radius: 12px; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);">
                        <div style="text-align: center;">
                            <div style="color: {{ $scoreColor }}; font-weight: 800; font-size: 1.8rem; line-height: 1;">{{ $submission->tajweed_score }}%</div>
                            <div style="color: var(--light-green); font-size: 0.9rem; font-weight: 600; margin-top: 2px;">{{ $submission->tajweed_grade }}</div>
                        </div>
                    </div>
                </div>

                @php
                    $analysis = is_array($submission->tajweed_analysis) ? $submission->tajweed_analysis : json_decode($submission->tajweed_analysis, true);
                @endphp

                <!-- Madd Analysis -->
                @if(isset($analysis['madd_analysis']))
                <div style="background: rgba(31, 39, 27, 0.6); border: 1px solid rgba(77, 139, 49, 0.3); padding: 18px; border-radius: 10px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <div>
                            <h5 style="color: var(--gold); margin: 0 0 5px 0; font-size: 1.1rem; font-weight: 700;">مد (Madd - Elongation)</h5>
                            <p style="color: var(--light-green); opacity: 0.8; font-size: 0.85rem; margin: 0;">Proper elongation of vowel sounds (2-6 counts)</p>
                        </div>
                        @php
                            $maddColor = $analysis['madd_analysis']['percentage'] >= 90 ? '#4caf50' : ($analysis['madd_analysis']['percentage'] >= 70 ? '#8bc34a' : '#ff9800');
                        @endphp
                        <div style="padding: 8px 15px; background: rgba(77, 139, 49, 0.2); border: 2px solid {{ $maddColor }}; border-radius: 10px;">
                            <span style="color: {{ $maddColor }}; font-weight: 700; font-size: 1.3rem;">{{ $analysis['madd_analysis']['percentage'] }}%</span>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div style="width: 100%; height: 8px; background: rgba(70, 63, 58, 0.5); border-radius: 10px; overflow: hidden; margin-bottom: 15px;">
                        <div style="width: {{ $analysis['madd_analysis']['percentage'] }}%; height: 100%; background: linear-gradient(90deg, {{ $maddColor }}, {{ $maddColor }}dd); transition: width 0.5s ease;"></div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 15px;">
                        <div style="background: rgba(77, 139, 49, 0.15); padding: 12px; border-radius: 8px; text-align: center;">
                            <div style="color: var(--light-green); font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px;">Total Found</div>
                            <div style="color: var(--gold); font-size: 1.5rem; font-weight: 700; margin-top: 5px;">{{ $analysis['madd_analysis']['total_elongations'] }}</div>
                        </div>
                        <div style="background: rgba(76, 175, 80, 0.15); padding: 12px; border-radius: 8px; text-align: center;">
                            <div style="color: var(--light-green); font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px;">Correct</div>
                            <div style="color: #4caf50; font-size: 1.5rem; font-weight: 700; margin-top: 5px;">{{ $analysis['madd_analysis']['correct_elongations'] }}</div>
                        </div>
                        <div style="background: rgba(244, 67, 54, 0.15); padding: 12px; border-radius: 8px; text-align: center;">
                            <div style="color: var(--light-green); font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px;">Issues</div>
                            <div style="color: #f44336; font-size: 1.5rem; font-weight: 700; margin-top: 5px;">{{ count($analysis['madd_analysis']['issues']) }}</div>
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
            
            {{-- OpenAI Intelligent Feedback for Teacher Reference --}}
            {{-- Always show AI feedback box, with message if not available yet --}}
            <div style="background: linear-gradient(135deg, rgba(227, 216, 136, 0.15) 0%, rgba(31, 39, 27, 0.8) 100%); border: 2px solid rgba(227, 216, 136, 0.4); padding: 25px; border-radius: 15px; margin-top: 20px; box-shadow: 0 6px 25px rgba(0, 0, 0, 0.3);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid rgba(227, 216, 136, 0.3);">
                    <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">🤖</div>
                    <div>
                        <div style="color: var(--gold); font-weight: 700; font-size: 1.2rem;">AI Teaching Assistant</div>
                        <div style="font-size: 0.8rem; padding: 4px 12px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.3), rgba(118, 75, 162, 0.3)); border-radius: 15px; font-weight: 600; color: #b8a3ff; display: inline-block; margin-top: 4px;">✨ Reference Only - Use Your Professional Judgment</div>
                    </div>
                </div>

                @if(isset($analysis['ai_feedback']))
                    @if(isset($analysis['ai_feedback']['summary']))
                    <div style="background: rgba(31, 39, 27, 0.6); padding: 20px; border-radius: 12px; margin-bottom: 15px; border-left: 4px solid #667eea;">
                        <h4 style="color: #b8a3ff; font-size: 1rem; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <span>📊</span> AI Performance Summary
                        </h4>
                        <p style="color: var(--light-green); line-height: 1.8; margin: 0;">{{ $analysis['ai_feedback']['summary'] }}</p>
                    </div>
                    @endif

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        @if(isset($analysis['ai_feedback']['strengths']) && count($analysis['ai_feedback']['strengths']) > 0)
                        <div style="background: rgba(76, 175, 80, 0.15); padding: 18px; border-radius: 12px; border-left: 4px solid #4caf50;">
                            <h4 style="color: #4caf50; font-size: 0.95rem; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                                <span>💪</span> Identified Strengths
                            </h4>
                            <ul style="margin: 0; padding-left: 20px; color: var(--light-green); line-height: 1.8; font-size: 0.9rem;">
                                @foreach($analysis['ai_feedback']['strengths'] as $strength)
                                <li style="margin-bottom: 8px;">{{ $strength }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(isset($analysis['ai_feedback']['improvements']) && count($analysis['ai_feedback']['improvements']) > 0)
                        <div style="background: rgba(255, 152, 0, 0.15); padding: 18px; border-radius: 12px; border-left: 4px solid #ff9800;">
                            <h4 style="color: #ff9800; font-size: 0.95rem; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                                <span>🎯</span> Suggested Improvements
                            </h4>
                            <ul style="margin: 0; padding-left: 20px; color: var(--light-green); line-height: 1.8; font-size: 0.9rem;">
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
                    <div style="background: rgba(102, 126, 234, 0.15); padding: 18px; border-radius: 12px; border-left: 4px solid #667eea;">
                        <h4 style="color: #b8a3ff; font-size: 0.95rem; font-weight: 600; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                            <span>🚀</span> Recommended Next Steps
                        </h4>
                        <p style="color: var(--light-green); line-height: 1.8; margin: 0; font-size: 0.9rem;">{{ $analysis['ai_feedback']['next_steps'] }}</p>
                    </div>
                    @endif
                @else
                    {{-- No AI feedback available --}}
                    <div style="background: rgba(255, 107, 107, 0.15); padding: 20px; border-radius: 12px; border-left: 4px solid #ff6b6b; text-align: center;">
                        <div style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.6;">⚠️</div>
                        <h4 style="color: #ff6b6b; font-size: 1rem; font-weight: 600; margin-bottom: 8px;">AI Feedback Not Generated</h4>
                        <p style="color: var(--light-green); opacity: 0.8; margin: 0; line-height: 1.6; font-size: 0.9rem;">
                            {{ $analysis['overall_score']['feedback'] ?? 'The Python audio analyzer failed to generate AI feedback. Check server logs for details about missing dependencies or execution errors.' }}
                        </p>
                    </div>
                @endif
            </div>
            @endif
            @endif
        </div>

        <!-- Grading Form -->
        <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
                <div style="width: 50px; height: 50px; background: var(--primary-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                    ✏️
                </div>
                <div>
                    <h3 style="color: var(--gold); font-size: 1.3rem; margin-bottom: 5px;">
                        {{ $submission->status === 'graded' ? 'Update Grade' : 'Provide Grade' }}
                    </h3>
                    <p style="color: var(--light-green); opacity: 0.8; font-size: 0.9rem; margin: 0;">Evaluate student performance</p>
                </div>
            </div>

            @if($submission->score)
            <div style="background: rgba(76, 175, 80, 0.2); border: 2px solid #4caf50; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                <div style="color: #4caf50; font-weight: 600; margin-bottom: 10px; font-size: 1rem;">✓ Previously Graded</div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                    <div>
                        <div style="font-size: 0.8rem; color: var(--light-green); opacity: 0.8; margin-bottom: 5px;">Score</div>
                        <div style="font-size: 1.3rem; color: #4caf50; font-weight: 700;">{{ $submission->score->score }}/{{ $submission->assignment->total_marks }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.8rem; color: var(--light-green); opacity: 0.8; margin-bottom: 5px;">Percentage</div>
                        <div style="font-size: 1.3rem; color: #4caf50; font-weight: 700;">{{ round(($submission->score->score / $submission->assignment->total_marks) * 100, 1) }}%</div>
                    </div>
                </div>
                @if($submission->score->feedback)
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(76, 175, 80, 0.3);">
                    <div style="font-size: 0.8rem; color: var(--light-green); opacity: 0.8; margin-bottom: 5px;">Previous Feedback</div>
                    <div style="font-size: 0.9rem; color: var(--light-green); line-height: 1.6;">{{ Str::limit($submission->score->feedback, 100) }}</div>
                </div>
                @endif
            </div>
            @endif

            <form method="POST" action="{{ route('teacher.submission.update.grade', $submission->id) }}">
                @csrf
                
                @php
                    // Calculate suggested score from Tajweed analysis
                    $suggestedScore = null;
                    if($submission->tajweed_score) {
                        // Convert percentage to actual points (e.g., 85% of 100 marks = 85 points)
                        $suggestedScore = round(($submission->tajweed_score / 100) * $submission->assignment->total_marks, 1);
                    }
                    $defaultScore = old('score', $submission->score->score ?? $suggestedScore ?? '');
                @endphp

                <!-- Tajweed AI Score Display (Always show if available) -->
                @if($submission->tajweed_score)
                <div style="background: linear-gradient(135deg, rgba(227, 216, 136, 0.2) 0%, rgba(77, 139, 49, 0.15) 100%); border: 2px solid rgba(227, 216, 136, 0.5); border-radius: 12px; padding: 18px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(227, 216, 136, 0.2);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 45px; height: 45px; background: var(--gold); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; box-shadow: 0 4px 12px rgba(227, 216, 136, 0.4);">🤖</div>
                            <div>
                                <div style="color: var(--gold); font-weight: 700; font-size: 1.1rem;">AI Tajweed Analysis Score</div>
                                <div style="font-size: 0.8rem; color: var(--light-green); opacity: 0.8;">Based on Madd & Noon Sakin rules</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 2rem; color: var(--gold); font-weight: 800; line-height: 1;">{{ $submission->tajweed_score }}%</div>
                            <div style="font-size: 0.85rem; color: var(--light-green); font-weight: 600;">{{ $submission->tajweed_grade }}</div>
                        </div>
                    </div>
                    <div style="background: rgba(31, 39, 27, 0.5); border-radius: 8px; padding: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 0.9rem; color: var(--light-green);">
                                <strong>Suggested Points:</strong>
                            </div>
                            <div style="font-size: 1.4rem; color: var(--gold); font-weight: 700;">
                                {{ $suggestedScore }}<span style="font-size: 1rem; opacity: 0.8;"> / {{ $submission->assignment->total_marks }}</span>
                            </div>
                        </div>
                        @if(!$submission->score)
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(77, 139, 49, 0.3);">
                            <p style="color: var(--light-green); font-size: 0.85rem; margin: 0; opacity: 0.9;">
                                💡 This score has been automatically filled in the Points Earned field below. You can adjust it based on your evaluation.
                            </p>
                        </div>
                        @else
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(77, 139, 49, 0.3);">
                            <p style="color: var(--light-green); font-size: 0.85rem; margin: 0; opacity: 0.9;">
                                📊 AI Score: <strong>{{ $suggestedScore }}</strong> | Your Score: <strong>{{ $submission->score->score }}</strong>
                                @if(abs($suggestedScore - $submission->score->score) > ($submission->assignment->total_marks * 0.1))
                                    <span style="color: #ff9800;">⚠️ Significant difference</span>
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: var(--gold); font-weight: 600; margin-bottom: 10px; font-size: 1rem;">
                        🎯 Points Earned <span style="color: #ff6b6b;">*</span>
                        @if($suggestedScore && !$submission->score)
                        <span style="font-size: 0.85rem; color: var(--gold); font-weight: 500; margin-left: 8px;">(Pre-filled from AI Analysis)</span>
                        @endif
                    </label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input 
                            type="number" 
                            name="score" 
                            min="0" 
                            max="{{ $submission->assignment->total_marks }}"
                            step="0.5"
                            value="{{ $defaultScore }}"
                            required
                            style="flex: 1; padding: 15px; background: rgba(70, 63, 58, 0.4); border: 2px solid {{ $suggestedScore && !$submission->score ? 'var(--gold)' : 'rgba(77, 139, 49, 0.4)' }}; border-radius: 10px; color: var(--light-green); font-size: 1.3rem; font-weight: 700; transition: all 0.3s ease; {{ $suggestedScore && !$submission->score ? 'box-shadow: 0 0 0 3px rgba(227, 216, 136, 0.2);' : '' }}"
                            onfocus="this.style.borderColor='var(--gold)'"
                            onblur="this.style.borderColor='rgba(77, 139, 49, 0.4)'"
                        >
                        <span style="color: var(--light-green); font-size: 1.1rem; font-weight: 600;">/ {{ $submission->assignment->total_marks }}</span>
                    </div>
                    @error('score')
                        <p style="color: #ff6b6b; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: var(--gold); font-weight: 600; margin-bottom: 10px; font-size: 1rem;">
                        💬 Feedback <span style="color: #ff6b6b;">*</span>
                    </label>
                    <textarea 
                        name="feedback" 
                        rows="8" 
                        required
                        placeholder="Provide detailed feedback on the student's recitation, highlighting strengths and areas for improvement..."
                        style="width: 100%; padding: 15px; background: rgba(70, 63, 58, 0.4); border: 2px solid rgba(77, 139, 49, 0.4); border-radius: 10px; color: var(--light-green); font-size: 0.95rem; line-height: 1.6; resize: vertical; transition: all 0.3s ease; font-family: 'Cairo', sans-serif;"
                        onfocus="this.style.borderColor='var(--gold)'"
                        onblur="this.style.borderColor='rgba(77, 139, 49, 0.4)'"
                    >{{ old('feedback', $submission->score->feedback ?? '') }}</textarea>
                    @error('feedback')
                        <p style="color: #ff6b6b; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                    <p style="color: var(--light-green); opacity: 0.7; font-size: 0.85rem; margin-top: 8px;">
                        💡 Tip: Include specific feedback on Tajweed rules, pronunciation, and recitation quality.
                    </p>
                </div>

                <div style="display: flex; gap: 15px; padding-top: 20px; border-top: 2px solid rgba(77, 139, 49, 0.3);">
                    <a href="{{ route('teacher.student.submissions', ['classroom' => $submission->assignment->class_id, 'student' => $submission->student_id]) }}" 
                        class="btn-secondary" 
                        style="text-decoration: none; flex: 1; text-align: center;">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary" style="flex: 2;">
                        {{ $submission->status === 'graded' ? '📝 Update Grade' : '✓ Submit Grade' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
