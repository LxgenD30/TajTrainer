@extends('layouts.template')

@section('page-title', 'My Profile')
@section('page-subtitle', 'View and manage your profile information')

@section('content')
<div style="background: rgba(31, 39, 27, 0.7); border: 2px solid var(--color-dark-green); border-radius: 25px; padding: 25px; font-family: 'Cairo', sans-serif; box-shadow: 0 15px 35px rgba(0,0,0,0.4);">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid rgba(227, 216, 136, 0.2);">
        <h3 style="color: var(--color-gold); font-size: 1.5rem; margin: 0; display: flex; align-items: center; gap: 12px;">
            <span style="color: #8e44ad; filter: drop-shadow(0 0 5px rgba(142, 68, 173, 0.5));">👤</span> Student Profile
        </h3>
        <a href="{{ route('students.edit', $student->id) }}" style="background: linear-gradient(135deg, #e3d888, #c5a059); color: #1f271b; padding: 10px 22px; border-radius: 12px; text-decoration: none; font-weight: 800; font-size: 0.85rem; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(227, 216, 136, 0.2);">
            ✏️ Edit Profile
        </a>
    </div>

    <div class="profile-container" style="display: grid; grid-template-columns: 280px 1fr; gap: 20px; align-items: start;">
        
        <div style="background: rgba(227, 216, 136, 0.03); border: 1px solid var(--color-dark-green); border-radius: 20px; padding: 30px 15px; text-align: center;">
            <div style="width: 150px; height: 150px; background: linear-gradient(135deg, #e3d888, #c5a059); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 4rem; font-weight: bold; color: #1f271b; border: 4px solid rgba(31, 39, 27, 0.8); box-shadow: 0 8px 25px rgba(0,0,0,0.3);">
                {{ substr($student->name, 0, 1) }}
            </div>

            <h2 style="color: #fff; margin: 0 0 5px 0; font-size: 1.4rem; letter-spacing: 0.5px;">{{ $student->name }}</h2>
            
            <div style="display: inline-block; background: rgba(227, 216, 136, 0.1); border: 1px solid var(--color-gold); color: var(--color-gold); padding: 4px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; margin-bottom: 25px;">
                📚 Student
            </div>

            <div style="text-align: left; border-top: 1px solid rgba(227, 216, 136, 0.1); padding-top: 20px; display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label style="display: block; color: var(--color-gold); font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; margin-bottom: 2px;">Email</label>
                    <div style="color: var(--color-light); font-size: 0.85rem; word-break: break-all;">{{ $student->user->email }}</div>
                </div>

                <div>
                    <label style="display: block; color: var(--color-gold); font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; margin-bottom: 2px;">Level</label>
                    <div style="color: var(--color-light); font-size: 0.85rem; text-transform: capitalize;">{{ $student->current_level ?? 'Advanced' }}</div>
                </div>

                <div>
                    <label style="display: block; color: var(--color-gold); font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; margin-bottom: 2px;">Member Since</label>
                    <div style="color: var(--color-light); font-size: 0.85rem;">{{ $student->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            
            <div style="background: rgba(31, 39, 27, 0.4); border: 1px solid var(--color-dark-green); border-radius: 15px; padding: 20px;">
                <h4 style="color: var(--color-gold); margin: 0 0 10px 0; font-size: 1rem; display: flex; align-items: center; gap: 8px;">📝 About Me</h4>
                <p style="color: var(--color-light); opacity: 0.85; line-height: 1.5; margin: 0; font-size: 0.9rem;">
                    {{ $student->biodata ?? 'This is a test student account for development.' }}
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                <div style="background: rgba(31, 39, 27, 0.4); border: 1px solid var(--color-dark-green); border-radius: 15px; padding: 15px; text-align: center;">
                    <div style="font-size: 1.8rem; margin-bottom: 5px;">📚</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #fff; line-height: 1;">{{ $student->classrooms->count() }}</div>
                    <div style="color: var(--color-gold); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-top: 5px;">Classes</div>
                </div>

                <div style="background: rgba(31, 39, 27, 0.4); border: 1px solid var(--color-dark-green); border-radius: 15px; padding: 15px; text-align: center;">
                    <div style="font-size: 1.8rem; margin-bottom: 5px;">✅</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #fff; line-height: 1;">{{ $student->scores->count() }}</div>
                    <div style="color: var(--color-gold); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-top: 5px;">Graded</div>
                </div>

                <div style="background: rgba(31, 39, 27, 0.4); border: 1px solid var(--color-dark-green); border-radius: 15px; padding: 15px; text-align: center;">
                    <div style="font-size: 1.8rem; margin-bottom: 5px;">⭐</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #fff; line-height: 1;">{{ $student->scores->count() > 0 ? number_format($student->scores->avg('score'), 1) : 'N/A' }}</div>
                    <div style="color: var(--color-gold); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-top: 5px;">Avg Score</div>
                </div>
            </div>

            <div style="background: rgba(31, 39, 27, 0.4); border: 1px solid var(--color-dark-green); border-radius: 15px; padding: 20px;">
                <h4 style="color: var(--color-gold); margin: 0 0 15px 0; font-size: 1rem;">🎓 Enrolled Classes</h4>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @forelse($student->classrooms as $classroom)
                    <div style="background: rgba(227, 216, 136, 0.05); border: 1px solid rgba(227, 216, 136, 0.1); border-radius: 10px; padding: 12px 18px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span style="font-size: 1.1rem;">🏛️</span>
                            <span style="color: #fff; font-weight: 600; font-size: 0.9rem;">{{ $classroom->class_name }}</span>
                        </div>
                        <a href="{{ route('classroom.show', $classroom->id) }}" style="color: var(--color-gold); text-decoration: none; font-size: 0.8rem; font-weight: 700; padding: 5px 12px; background: rgba(227, 216, 136, 0.1); border-radius: 6px; border: 1px solid var(--color-gold);">
                            View →
                        </a>
                    </div>
                    @empty
                    <p style="color: var(--color-light); opacity: 0.5; text-align: center; margin: 0; font-size: 0.85rem;">No classes enrolled yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection