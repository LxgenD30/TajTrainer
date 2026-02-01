@extends('layouts.template')

@section('page-title', 'Update Profile')
@section('page-subtitle', 'Edit your information')

@section('content')
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">✏️ Update Profile</h3>
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

            <form action="{{ route('teachers.update', $teacher) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Account Information -->
                <div style="background: rgba(227, 216, 136, 0.05); padding: 20px; border-radius: 12px; border: 2px solid var(--color-dark-green); margin-bottom: 20px;">
                    <h4 style="color: var(--color-gold); margin: 0 0 15px 0; font-size: 1.1rem;">👤 Account Information</h4>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $teacher->user->email) }}" required
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $teacher->user->phone) }}"
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    </div>
                </div>

                <!-- Professional Information -->
                <div style="background: rgba(227, 216, 136, 0.05); padding: 20px; border-radius: 12px; border: 2px solid var(--color-dark-green); margin-bottom: 20px;">
                    <h4 style="color: var(--color-gold); margin: 0 0 15px 0; font-size: 1.1rem;">📚 Professional Information</h4>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Title</label>
                        <input type="text" name="title" value="{{ old('title', $teacher->title) }}" placeholder="e.g., Ustadh, Ustadzah, Sheikh"
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Biodata / About</label>
                        <textarea name="biodata" rows="4"
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem; resize: vertical;">{{ old('biodata', $teacher->biodata) }}</textarea>
                    </div>
                </div>

                <!-- Change Password (Optional) -->
                <div style="background: rgba(227, 216, 136, 0.05); padding: 20px; border-radius: 12px; border: 2px solid var(--color-dark-green); margin-bottom: 20px;">
                    <h4 style="color: var(--color-gold); margin: 0 0 15px 0; font-size: 1.1rem;">🔒 Change Password (Optional)</h4>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Current Password</label>
                        <input type="password" name="current_password"
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                        <small style="color: var(--color-light); opacity: 0.7; display: block; margin-top: 5px;">Required if you want to change your password</small>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">New Password</label>
                            <input type="password" name="new_password"
                                style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                        </div>
                        <div>
                            <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation"
                                style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <a href="{{ route('teachers.show', $teacher) }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">💾 Update Profile</button>
                </div>
            </form>
        </div>
    </div>
@endsection
