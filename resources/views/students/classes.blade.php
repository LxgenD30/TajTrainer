@extends('layouts.template')

@section('title', 'My Classes')
@section('page-title', 'My Classes')
@section('page-subtitle', 'View your enrolled classes and join new ones')

@section('content')
<div style="padding: 0;">
    @if(session('success'))
        <div style="background: rgba(77, 139, 49, 0.2); border: 2px solid var(--color-dark-green); color: var(--color-light-green); padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: rgba(197, 48, 48, 0.2); border: 2px solid #c53030; color: #ffcccc; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">⚠</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="background: rgba(197, 48, 48, 0.2); border: 2px solid #c53030; color: #ffcccc; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px;">
            <div style="font-weight: 600; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.5rem;">⚠</span>
                <span>Please fix the following errors:</span>
            </div>
            <ul style="margin-left: 40px; list-style: disc;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 30px;">
        <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
                <div style="width: 50px; height: 50px; background: var(--color-dark-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                    🏫
                </div>
                <div>
                    <h2 style="color: var(--color-gold); font-size: 1.5rem; margin-bottom: 5px;">My Enrolled Classes</h2>
                    <p style="color: var(--color-light-green); opacity: 0.8; font-size: 0.9rem;">{{ $student->classrooms->count() }} {{ $student->classrooms->count() === 1 ? 'Class' : 'Classes' }}</p>
                </div>
            </div>

            @if($student->classrooms->isEmpty())
                <div style="text-align: center; padding: 60px 20px; color: var(--color-light-green); opacity: 0.6;">
                    <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;">📚</div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 10px; color: var(--color-gold);">No Classes Yet</h3>
                    <p style="font-size: 1rem;">Use the access code from your teacher to enroll in a class</p>
                </div>
            @else
                <div style="display: grid; gap: 20px;">
                    @foreach($student->classrooms as $classroom)
                        <div style="background: rgba(70, 63, 58, 0.4); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.borderColor='var(--color-gold)'; this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='rgba(77, 139, 49, 0.3)'; this.style.transform='translateY(0)'">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                <div style="flex: 1;">
                                    <h3 style="color: var(--color-gold); font-size: 1.3rem; margin-bottom: 8px;">{{ $classroom->class_name }}</h3>
                                    <p style="color: var(--color-light-green); opacity: 0.8; margin-bottom: 10px; line-height: 1.5;">{{ $classroom->description ?? 'No description available' }}</p>
                                </div>
                                <a href="{{ route('classroom.show', $classroom->id) }}" class="btn-primary" style="text-decoration: none; white-space: nowrap;">
                                    View Class →
                                </a>
                            </div>

                            <div style="display: flex; gap: 20px; padding-top: 15px; border-top: 1px solid rgba(77, 139, 49, 0.2);">
                                <div style="display: flex; align-items: center; gap: 8px; color: var(--color-light-green); opacity: 0.9;">
                                    <span style="font-size: 1.2rem;">👨‍🏫</span>
                                    <span style="font-size: 0.9rem;">{{ $classroom->teacher->name ?? 'Unknown' }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; color: var(--color-light-green); opacity: 0.9;">
                                    <span style="font-size: 1.2rem;">👥</span>
                                    <span style="font-size: 0.9rem;">{{ $classroom->students->count() }} Students</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; color: var(--color-light-green); opacity: 0.9;">
                                    <span style="font-size: 1.2rem;">📝</span>
                                    <span style="font-size: 0.9rem;">{{ $classroom->assignments->count() }} Assignments</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); height: fit-content;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
                <div style="width: 50px; height: 50px; background: var(--color-dark-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                    ➕
                </div>
                <div>
                    <h2 style="color: var(--color-gold); font-size: 1.3rem; margin-bottom: 5px;">Enroll in Class</h2>
                    <p style="color: var(--color-light-green); opacity: 0.8; font-size: 0.85rem;">Enter access code</p>
                </div>
            </div>

            <form method="POST" action="{{ route('student.enroll') }}">
                @csrf
                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 10px; font-size: 0.95rem;">
                        Access Code <span style="color: #ff6b6b;">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="access_code" 
                        value="{{ old('access_code') }}"
                        placeholder="Enter the 6-digit code from your teacher"
                        required
                        style="width: 100%; padding: 14px 18px; background: rgba(31, 39, 27, 0.5); border: 2px solid rgba(77, 139, 49, 0.4); border-radius: 10px; color: var(--color-light-green); font-size: 1rem; transition: all 0.3s ease; font-family: 'Courier New', monospace; letter-spacing: 3px; text-align: center; text-transform: uppercase;"
                        onfocus="this.style.borderColor='var(--color-gold)'; this.style.background='rgba(31, 39, 27, 0.7)'"
                        onblur="this.style.borderColor='rgba(77, 139, 49, 0.4)'; this.style.background='rgba(31, 39, 27, 0.5)'"
                    >
                    <p style="color: var(--color-light-green); opacity: 0.7; font-size: 0.85rem; margin-top: 8px; line-height: 1.5;">
                        💡 Ask your teacher for the access code
                    </p>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; font-size: 1rem;">
                    🎓 Join Class
                </button>
            </form>

            <div style="margin-top: 25px; padding: 20px; background: rgba(227, 216, 136, 0.1); border: 2px solid rgba(227, 216, 136, 0.3); border-radius: 10px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <span style="font-size: 1.3rem;">ℹ️</span>
                    <h4 style="color: var(--color-gold); font-size: 1rem; font-weight: 600;">How to Enroll</h4>
                </div>
                <ol style="color: var(--color-light-green); opacity: 0.9; font-size: 0.85rem; margin-left: 20px; line-height: 1.8; list-style: decimal;">
                    <li>Get the access code from your teacher</li>
                    <li>Enter the code in the field above</li>
                    <li>Click "Join Class" to enroll</li>
                    <li>Start learning immediately!</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
