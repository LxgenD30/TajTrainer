@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #1a2f1a 0%, #2d4a2d 100%); padding: 40px 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="background: linear-gradient(135deg, rgba(77, 139, 49, 0.2), rgba(46, 64, 38, 0.3)); backdrop-filter: blur(10px); border-radius: 20px; padding: 40px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); border: 1px solid rgba(211, 255, 177, 0.1);">
            <h1 style="color: var(--color-gold); font-size: 2rem; font-weight: 700; margin-bottom: 10px;">✨ Create Student Profile</h1>
            <p style="color: rgba(211, 255, 177, 0.7); margin-bottom: 30px;">Fill in your information to get started</p>

            @if($errors->any())
            <div style="background: rgba(244, 67, 54, 0.2); border-left: 4px solid #f44336; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px;">
                <div style="color: #f44336; font-weight: 600; margin-bottom: 10px;">Please fix the following errors:</div>
                <ul style="color: #f44336; margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('students.store') }}" method="POST">
                @csrf

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: rgba(211, 255, 177, 0.9); font-weight: 600; margin-bottom: 8px;">
                        Full Name <span style="color: #f44336;">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        style="width: 100%; padding: 14px 18px; background: rgba(31, 39, 27, 0.5); border: 2px solid rgba(211, 255, 177, 0.2); border-radius: 12px; color: var(--color-light-green); font-size: 1rem; outline: none; transition: all 0.3s;"
                        placeholder="Enter your full name">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: rgba(211, 255, 177, 0.9); font-weight: 600; margin-bottom: 8px;">
                        Email Address <span style="color: #f44336;">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        style="width: 100%; padding: 14px 18px; background: rgba(31, 39, 27, 0.5); border: 2px solid rgba(211, 255, 177, 0.2); border-radius: 12px; color: var(--color-light-green); font-size: 1rem; outline: none; transition: all 0.3s;"
                        placeholder="your.email@example.com">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: rgba(211, 255, 177, 0.9); font-weight: 600; margin-bottom: 8px;">
                        Password <span style="color: #f44336;">*</span>
                    </label>
                    <input type="password" name="password" required
                        style="width: 100%; padding: 14px 18px; background: rgba(31, 39, 27, 0.5); border: 2px solid rgba(211, 255, 177, 0.2); border-radius: 12px; color: var(--color-light-green); font-size: 1rem; outline: none; transition: all 0.3s;"
                        placeholder="Minimum 8 characters">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: rgba(211, 255, 177, 0.9); font-weight: 600; margin-bottom: 8px;">
                        Confirm Password <span style="color: #f44336;">*</span>
                    </label>
                    <input type="password" name="password_confirmation" required
                        style="width: 100%; padding: 14px 18px; background: rgba(31, 39, 27, 0.5); border: 2px solid rgba(211, 255, 177, 0.2); border-radius: 12px; color: var(--color-light-green); font-size: 1rem; outline: none; transition: all 0.3s;"
                        placeholder="Re-enter your password">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: rgba(211, 255, 177, 0.9); font-weight: 600; margin-bottom: 8px;">
                        Current Quranic Level
                    </label>
                    <select name="current_level"
                        style="width: 100%; padding: 14px 18px; background: rgba(31, 39, 27, 0.5); border: 2px solid rgba(211, 255, 177, 0.2); border-radius: 12px; color: var(--color-light-green); font-size: 1rem; outline: none; transition: all 0.3s;">
                        <option value="">Select your level</option>
                        <option value="Beginner" {{ old('current_level') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="Intermediate" {{ old('current_level') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="Advanced" {{ old('current_level') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                        <option value="Expert" {{ old('current_level') == 'Expert' ? 'selected' : '' }}>Expert</option>
                    </select>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; color: rgba(211, 255, 177, 0.9); font-weight: 600; margin-bottom: 8px;">
                        About Me
                    </label>
                    <textarea name="biodata" rows="4"
                        style="width: 100%; padding: 14px 18px; background: rgba(31, 39, 27, 0.5); border: 2px solid rgba(211, 255, 177, 0.2); border-radius: 12px; color: var(--color-light-green); font-size: 1rem; outline: none; resize: vertical; transition: all 0.3s; font-family: inherit;"
                        placeholder="Tell us about your Quran learning journey...">{{ old('biodata') }}</textarea>
                </div>

                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <a href="{{ route('students.index') }}" 
                        style="background: rgba(77, 139, 49, 0.3); color: var(--color-light-green); padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; border: 2px solid rgba(211, 255, 177, 0.2); transition: all 0.3s;">
                        Cancel
                    </a>
                    <button type="submit" 
                        style="background: linear-gradient(135deg, var(--color-gold), #d4a574); color: #1f271b; padding: 14px 32px; border-radius: 12px; border: none; font-weight: 600; cursor: pointer; box-shadow: 0 4px 15px rgba(199, 151, 95, 0.3); transition: all 0.3s;">
                        ✓ Create Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
