@extends('layouts.template')

@section('page-title', 'Edit Profile')
@section('page-subtitle', 'Update your personal information')

@section('content')
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">✏️ Edit Profile</h3>
        </div>

        <div class="card-body">
            @if($errors->any())
                <div style="background: rgba(231, 76, 60, 0.1); border: 2px solid #e74c3c; color: #e74c3c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Personal Information -->
                <div style="background: rgba(227, 216, 136, 0.05); padding: 20px; border-radius: 12px; border: 2px solid var(--color-dark-green); margin-bottom: 20px;">
                    <h4 style="color: var(--color-gold); margin: 0 0 15px 0; font-size: 1.1rem;">👤 Personal Information</h4>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $student->name) }}" required
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', $student->user->email) }}" required
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    </div>
                </div>

                <!-- Learning Information -->
                <div style="background: rgba(227, 216, 136, 0.05); padding: 20px; border-radius: 12px; border: 2px solid var(--color-dark-green); margin-bottom: 20px;">
                    <h4 style="color: var(--color-gold); margin: 0 0 15px 0; font-size: 1.1rem;">📚 Learning Information</h4>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Current Quran Recitation Level</label>
                        <select name="current_level"
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                            <option value="">Select your level</option>
                            <option value="Beginner" {{ old('current_level', $student->current_level) == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="Intermediate" {{ old('current_level', $student->current_level) == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="Advanced" {{ old('current_level', $student->current_level) == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                            <option value="Expert" {{ old('current_level', $student->current_level) == 'Expert' ? 'selected' : '' }}>Expert</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">About Me</label>
                        <textarea name="biodata" rows="4"
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem; resize: vertical;">{{ old('biodata', $student->biodata) }}</textarea>
                    </div>
                </div>

                <!-- Change Password (Optional) -->
                <div style="background: rgba(227, 216, 136, 0.05); padding: 20px; border-radius: 12px; border: 2px solid var(--color-dark-green); margin-bottom: 20px;">
                    <h4 style="color: var(--color-gold); margin: 0 0 15px 0; font-size: 1.1rem;">🔒 Change Password (Optional)</h4>
                    <small style="color: var(--color-light); opacity: 0.7; display: block; margin-bottom: 15px;">Leave blank to keep current password</small>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">New Password</label>
                            <input type="password" name="password"
                                style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                        </div>
                        <div>
                            <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Confirm New Password</label>
                            <input type="password" name="password_confirmation"
                                style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <a href="{{ route('students.show', $student->id) }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">💾 Update Profile</button>
                </div>
            </form>
        </div>
    </div>
@endsection
