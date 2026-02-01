@extends('layouts.template')

@section('page-title', $classroom->class_name)
@section('page-subtitle', 'Classroom Details and Management')

@section('content')
    <!-- Compact Classroom Info Bar -->
    <div class="content-card" style="padding: 15px 20px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
            <!-- Class Info -->
            <div style="flex: 1; min-width: 200px;">
                <h2 style="color: var(--color-gold); font-size: 1.5rem; margin: 0 0 5px 0;">{{ $classroom->class_name }}</h2>
                <p style="margin: 0; opacity: 0.8; font-size: 0.9rem;">{{ Str::limit($classroom->description ?? 'No description', 60) }}</p>
            </div>

            <!-- Access Code -->
            <div style="display: flex; align-items: center; gap: 10px; background: rgba(227, 216, 136, 0.15); padding: 10px 15px; border-radius: 8px; border: 1px solid var(--color-gold);">
                <div style="font-size: 1.2rem;">🔑</div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--color-gold); margin-bottom: 2px;">Access Code</div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span id="accessCode" style="font-family: 'JetBrains Mono', monospace; font-size: 1.3rem; font-weight: 700; color: var(--color-gold); letter-spacing: 3px;">••••••</span>
                        <span id="accessCodeHidden" style="display: none;">{{ $classroom->access_code }}</span>
                        <button onclick="toggleAccessCode(this)" 
                            style="padding: 5px 10px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 6px; cursor: pointer; font-size: 0.75rem; transition: all 0.3s ease;"
                            onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                            onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                            <span id="toggleIcon">👁️</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('teacher.class.progress', $classroom->id) }}"
                    style="padding: 8px 20px; background: rgba(81, 212, 136, 0.2); color: #51d488; border: 2px solid #51d488; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;"
                    onmouseover="this.style.background='rgba(81, 212, 136, 0.3)'"
                    onmouseout="this.style.background='rgba(81, 212, 136, 0.2)'">
                    📊 Progress
                </a>
                <a href="{{ route('classroom.edit', $classroom->id) }}"
                    style="padding: 8px 20px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;"
                    onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                    onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                    ✏️ Edit
                </a>
                <a href="{{ route('classroom.index') }}"
                    style="padding: 8px 20px; background: transparent; color: var(--color-light-green); border: 2px solid var(--color-light-green); border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;"
                    onmouseover="this.style.background='rgba(226, 241, 175, 0.1)'"
                    onmouseout="this.style.background='transparent'">
                    ← Back
                </a>
            </div>
        </div>
    </div>

    <script>
        function toggleAccessCode(btn) {
            const codeElement = document.getElementById('accessCode');
            const hiddenCode = document.getElementById('accessCodeHidden').textContent.trim();
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (codeElement.textContent.includes('•')) {
                codeElement.textContent = hiddenCode;
                toggleIcon.textContent = '🙈';
            } else {
                codeElement.textContent = '••••••';
                toggleIcon.textContent = '👁️';
            }
        }
    </script>

    <!-- Students and Assignments Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
        <!-- Students Section -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">👥 Enrolled Students</h3>
            </div>
            <div class="card-body">
                @if($students->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($students as $student)
                            <div style="background: rgba(77, 139, 49, 0.15); border-left: 4px solid var(--color-light-green); padding: 12px 15px; border-radius: 8px; transition: all 0.3s ease;" 
                                onmouseover="this.style.background='rgba(77, 139, 49, 0.25)'"
                                onmouseout="this.style.background='rgba(77, 139, 49, 0.15)'">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                    <div>
                                        <h4 style="color: var(--color-gold); margin: 0 0 5px 0; font-size: 1rem;">{{ $student->name }}</h4>
                                        <p style="margin: 0; font-size: 0.85rem; opacity: 0.8;">
                                            📧 {{ $student->email }}
                                        </p>
                                    </div>
                                    <a href="{{ route('teacher.student.submissions', ['classroom' => $classroom->id, 'student' => $student->id]) }}" 
                                        style="padding: 5px 12px; background: var(--color-gold); color: var(--color-dark); border-radius: 15px; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: all 0.3s ease; white-space: nowrap;"
                                        onmouseover="this.style.opacity='0.8'"
                                        onmouseout="this.style.opacity='1'">
                                        View Work
                                    </a>
                                </div>
                                <div style="display: flex; gap: 15px; font-size: 0.85rem; margin-top: 8px;">
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <span>📝</span>
                                        <span style="color: var(--color-light-green);">{{ $student->total_submissions ?? 0 }} submissions</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <span>✅</span>
                                        <span style="color: var(--color-gold);">{{ $student->graded_submissions ?? 0 }} graded</span>
                                    </div>
                                    @if($student->student && $student->student->classrooms->first())
                                        <div style="display: flex; align-items: center; gap: 5px; opacity: 0.7;">
                                            <span>📅</span>
                                            <span>Joined {{ $student->student->classrooms->first()->pivot->date_joined ? \Carbon\Carbon::parse($student->student->classrooms->first()->pivot->date_joined)->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="text-align: center; padding: 40px 0; opacity: 0.7;">
                        No students enrolled yet. Students can join using the access code above.
                    </p>
                @endif
            </div>
        </div>

        <!-- Assignments Section -->
        <div class="content-card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">📝 Assignments</h3>
                <button onclick="window.location.href='{{ route('assignment.create', $classroom->id) }}'" 
                    style="padding: 8px 18px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif; font-size: 0.9rem;"
                    onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                    onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                    + Create
                </button>
            </div>
            <div class="card-body">
                @if($assignments->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($assignments as $assignment)
                            <div style="background: rgba(77, 139, 49, 0.15); border-left: 4px solid var(--color-gold); padding: 12px 15px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                    <h4 style="color: var(--color-gold); margin: 0; font-size: 1rem;">
                                        @if($assignment->surah)
                                            📖 {{ $assignment->surah }} 
                                            ({{ $assignment->start_verse }}@if($assignment->end_verse)-{{ $assignment->end_verse }}@endif)
                                        @else
                                            {{ $assignment->material ? $assignment->material->title : 'Assignment' }}
                                        @endif
                                    </h4>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span style="font-size: 0.85rem; color: var(--color-light-green); white-space: nowrap;">{{ $assignment->total_marks }} pts</span>
                                        <a href="{{ route('assignment.show', $assignment->assignment_id) }}" 
                                            style="padding: 5px 12px; background: var(--color-gold); color: var(--color-dark); border-radius: 15px; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: all 0.3s ease;"
                                            onmouseover="this.style.opacity='0.8'"
                                            onmouseout="this.style.opacity='1'">
                                            View
                                        </a>
                                    </div>
                                </div>
                                <p style="margin: 0 0 8px 0; font-size: 0.9rem; opacity: 0.9;">{{ Str::limit($assignment->instructions, 80) }}</p>
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; opacity: 0.8;">
                                    <span>📅 Due: {{ $assignment->due_date->format('M d, Y h:i A') }}</span>
                                    @if($assignment->is_voice_submission)
                                        <span style="color: var(--color-gold);">🎤 Voice</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="text-align: center; padding: 40px 0; opacity: 0.7;">
                        No assignments yet. Create assignments to give tasks to your students.
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
