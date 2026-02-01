@extends('layouts.template')

@section('page-title', 'My Classrooms')
@section('page-subtitle', 'Manage your classrooms and students')

@section('content')
    <!-- Success Message -->
    @if(session('success'))
        <div style="background: rgba(46, 125, 50, 0.2); border: 2px solid #4caf50; color: #a5d6a7; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header with Create Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="color: var(--color-gold); font-family: 'Amiri', serif; font-size: 2rem; margin: 0;">📚 Your Classrooms</h2>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">Create and manage virtual classrooms for your students</p>
        </div>
        <a href="{{ route('classroom.create') }}"
            style="padding: 12px 30px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;"
            onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
            onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
            <span style="font-size: 1.2rem;">+</span> Create Classroom
        </a>
    </div>

    <!-- Classrooms Grid -->
    @if($classrooms->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
            @foreach($classrooms as $classroom)
                <div class="content-card" style="background: linear-gradient(135deg, rgba(77, 139, 49, 0.15), rgba(31, 39, 27, 0.3)); transition: transform 0.3s ease, box-shadow 0.3s ease;"
                    onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 24px rgba(197, 160, 89, 0.3)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    
                    <!-- Classroom Header -->
                    <div style="border-bottom: 1px solid rgba(197, 160, 89, 0.2); padding-bottom: 15px; margin-bottom: 15px;">
                        <h3 style="color: var(--color-gold); margin: 0 0 10px 0; font-size: 1.4rem; font-family: 'Amiri', serif;">
                            {{ $classroom->class_name }}
                        </h3>
                        <p style="margin: 0; opacity: 0.85; font-size: 0.95rem; line-height: 1.6;">
                            {{ Str::limit($classroom->description ?? 'No description provided', 80) }}
                        </p>
                    </div>

                    <!-- Access Code Display -->
                    <div style="background: rgba(227, 216, 136, 0.15); border-left: 4px solid var(--color-gold); padding: 12px 15px; margin-bottom: 15px; border-radius: 8px;">
                        <div style="font-size: 0.85rem; color: var(--color-gold); font-weight: 600; margin-bottom: 5px;">🔑 Access Code</div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="access-code-display" data-code="{{ $classroom->access_code }}" style="font-family: 'JetBrains Mono', monospace; font-size: 1.8rem; font-weight: 700; color: var(--color-gold); letter-spacing: 4px;">
                                ••••••
                            </div>
                            <button onclick="toggleCode(this)" 
                                style="padding: 5px 10px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; font-size: 0.8rem;"
                                onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                                onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                                👁️
                            </button>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div style="display: flex; gap: 15px; margin-bottom: 15px; padding: 10px 0; border-bottom: 1px solid rgba(197, 160, 89, 0.1); flex-wrap: wrap;">
                        <div>
                            <div style="font-size: 0.85rem; opacity: 0.7;">Students</div>
                            <div style="font-size: 1.3rem; font-weight: 600; color: var(--color-gold);">{{ $classroom->students_count ?? 0 }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; opacity: 0.7;">Assignments</div>
                            <div style="font-size: 1.3rem; font-weight: 600; color: var(--color-gold);">{{ $classroom->assignments_count ?? 0 }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; opacity: 0.7;">Pending</div>
                            <div style="font-size: 1.3rem; font-weight: 600; color: #ff9800;">{{ $classroom->pending_assignments ?? 0 }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; opacity: 0.7;">Completed</div>
                            <div style="font-size: 1.3rem; font-weight: 600; color: #4caf50;">{{ $classroom->completed_assignments ?? 0 }}</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 10px;">
                        <a href="{{ route('classroom.show', $classroom->id) }}"
                            style="flex: 1; padding: 10px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 20px; text-decoration: none; text-align: center; font-weight: 600; transition: all 0.3s ease;"
                            onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                            onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                            View Details
                        </a>
                        <a href="{{ route('classroom.edit', $classroom->id) }}"
                            style="padding: 10px 15px; background: transparent; color: var(--color-light-green); border: 2px solid var(--color-light-green); border-radius: 20px; text-decoration: none; text-align: center; transition: all 0.3s ease;"
                            onmouseover="this.style.background='rgba(226, 241, 175, 0.1)'"
                            onmouseout="this.style.background='transparent'">
                            ✏️
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="content-card" style="text-align: center; padding: 60px 40px;">
            <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.6;">📚</div>
            <h3 style="color: var(--color-gold); margin: 0 0 10px 0; font-size: 1.5rem;">No Classrooms Yet</h3>
            <p style="margin: 0 0 25px 0; opacity: 0.8;">Create your first classroom to start teaching students</p>
            <a href="{{ route('classroom.create') }}"
                style="padding: 12px 30px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;"
                onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                <span style="font-size: 1.2rem;">+</span> Create Your First Classroom
            </a>
        </div>
    @endif

    <script>
        function toggleCode(button) {
            const codeDisplay = button.parentElement.querySelector('.access-code-display');
            const code = codeDisplay.dataset.code;
            
            if (codeDisplay.textContent.includes('•')) {
                codeDisplay.textContent = code;
                button.textContent = '🙈';
            } else {
                codeDisplay.textContent = '••••••';
                button.textContent = '👁️';
            }
        }
    </script>
@endsection
