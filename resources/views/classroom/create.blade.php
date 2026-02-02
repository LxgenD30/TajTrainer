@extends('layouts.dashboard')

@section('title', 'Create New Classroom')
@section('user-role', 'Teacher • Create Classroom')

@section('navigation')
    <a href="{{ route('home') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="{{ route('classroom.index') }}" class="nav-item active">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
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
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">📚 Create New Classroom</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('classroom.store') }}" method="POST">
                @csrf
                
                <!-- Class Name -->
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-gold);">
                        Class Name <span style="color: #e74c3c;">*</span>
                    </label>
                    <input type="text" name="class_name" value="{{ old('class_name') }}" required
                        style="width: 100%; padding: 12px 15px; background: rgba(31, 39, 27, 0.5); border: 2px solid var(--color-dark-green); border-radius: 8px; color: var(--color-light-green); font-size: 1rem; font-family: 'Cairo', sans-serif;"
                        placeholder="e.g., Tajweed Level 1">
                    @error('class_name')
                        <span style="color: #e74c3c; font-size: 0.9rem; display: block; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-gold);">
                        Description
                    </label>
                    <textarea name="description" rows="5"
                        style="width: 100%; padding: 12px 15px; background: rgba(31, 39, 27, 0.5); border: 2px solid var(--color-dark-green); border-radius: 8px; color: var(--color-light-green); font-size: 1rem; font-family: 'Cairo', sans-serif; resize: vertical;"
                        placeholder="Describe the classroom objectives and content...">{{ old('description') }}</textarea>
                    @error('description')
                        <span style="color: #e74c3c; font-size: 0.9rem; display: block; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Info Box -->
                <div style="background: rgba(227, 216, 136, 0.1); border: 2px solid var(--color-gold); border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 2rem;">🔑</div>
                        <div>
                            <h4 style="color: var(--color-gold); margin: 0 0 8px 0; font-family: 'Amiri', serif;">Access Code</h4>
                            <p style="margin: 0; opacity: 0.9; line-height: 1.6;">
                                A unique 6-digit access code will be automatically generated for this classroom. 
                                Share this code with your students so they can join the class.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <a href="{{ route('classroom.index') }}" 
                        style="padding: 12px 30px; background: transparent; color: var(--color-light-green); border: 2px solid var(--color-light-green); border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-block;"
                        onmouseover="this.style.background='rgba(226, 241, 175, 0.1)'"
                        onmouseout="this.style.background='transparent'">
                        Cancel
                    </a>
                    <button type="submit"
                        style="padding: 12px 30px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif;"
                        onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                        onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                        Create Classroom
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
