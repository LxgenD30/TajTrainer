@extends('layouts.dashboard')

@section('title', 'Edit Profile')
@section('user-role', 'Student • Edit Profile')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('content')
<style>
    .profile-edit-container {
        max-width: 1100px;
        margin: 0 auto;
    }

    .back-btn {
        background: rgba(10,92,54,0.1);
        color: #0a5c36;
        border: 2px solid #0a5c36;
        padding: 12px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        margin-bottom: 25px;
    }

    .back-btn:hover {
        background: #0a5c36;
        color: #fff;
        transform: translateY(-2px);
    }

    /* ID Card Horizontal Layout */
    .id-card-form {
        background: linear-gradient(135deg, #0a5c36 0%, #1abc9c 100%);
        border-radius: 25px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(10, 92, 54, 0.3);
        border: 4px solid #fff;
        position: relative;
        overflow: hidden;
    }

    .id-card-form::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7z' fill='%23ffffff' fill-opacity='0.1'/%3E%3C/svg%3E");
        opacity: 0.2;
    }

    .id-card-layout {
        display: flex;
        gap: 40px;
        position: relative;
        z-index: 2;
    }

    /* Left Side - Profile Picture */
    .id-card-left {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .profile-picture-section {
        position: relative;
    }

    .profile-picture-display {
        width: 220px;
        height: 280px;
        border-radius: 20px;
        object-fit: cover;
        border: 6px solid #fff;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5rem;
        color: #0a5c36;
    }

    .profile-picture-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.8);
        color: #fff;
        text-align: center;
        padding: 12px;
        border-radius: 0 0 14px 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
        font-weight: 600;
    }

    .profile-picture-overlay:hover {
        background: rgba(0, 0, 0, 0.95);
    }

    #profile_picture {
        display: none;
    }

    .id-badge {
        background: rgba(255, 255, 255, 0.95);
        padding: 15px 25px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .id-badge-title {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .id-badge-value {
        font-size: 1.3rem;
        color: #0a5c36;
        font-weight: 800;
    }

    /* Right Side - Form Fields */
    .id-card-right {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .id-card-header {
        color: #fff;
        border-bottom: 3px solid rgba(255, 255, 255, 0.3);
        padding-bottom: 20px;
    }

    .id-card-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        color: #fff;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .id-card-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    /* Form Grid */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        color: #fff;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 1rem;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .form-control {
        width: 100%;
        padding: 14px 18px;
        background: rgba(255, 255, 255, 0.95);
        color: #2a2a2a;
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        background: #fff;
        border-color: #ffd700;
        box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.2);
    }

    select.form-control {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230a5c36' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        padding-right: 40px;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
        font-family: 'Cairo', sans-serif;
    }

    .form-hint {
        display: block;
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 5px;
        font-style: italic;
    }

    /* Password Section */
    .password-section {
        background: rgba(0, 0, 0, 0.2);
        padding: 20px;
        border-radius: 15px;
        margin-top: 10px;
    }

    .password-section h3 {
        color: #fff;
        font-size: 1.2rem;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Buttons */
    .btn-group {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 25px;
    }

    .btn-primary {
        background: #ffd700;
        color: #0a5c36;
        border: none;
        padding: 14px 35px;
        border-radius: 25px;
        font-weight: 800;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 8px 20px rgba(255, 215, 0, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(255, 215, 0, 0.6);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 2px solid #fff;
        padding: 12px 35px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 1.1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        backdrop-filter: blur(10px);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    .alert-error {
        background: rgba(231, 76, 60, 0.95);
        border: 3px solid #c0392b;
        color: #fff;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 25px;
        font-weight: 600;
    }

    .alert-error strong {
        display: block;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }

    .alert-error ul {
        margin: 10px 0 0 0;
        padding-left: 20px;
    }

    @media (max-width: 968px) {
        .id-card-layout {
            flex-direction: column;
            align-items: center;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-edit-container">
    <a href="{{ route('students.show', $student->id) }}" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Profile
    </a>

    @if($errors->any())
        <div class="alert-error">
            <strong><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

        <div class="id-card-form">
            <div class="id-card-layout">
                <!-- Left: Profile Picture -->
                <div class="id-card-left">
                    <div class="profile-picture-section">
                        @if($student->user->profile_picture)
                            <img src="{{ asset('storage/' . $student->user->profile_picture) }}" alt="Profile" class="profile-picture-display" id="profilePreview">
                        @else
                            <div class="profile-picture-display" id="profilePreview">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        @endif
                        <label for="profile_picture" class="profile-picture-overlay">
                            <i class="fas fa-camera"></i> Change Photo
                        </label>
                    </div>

                    <div class="id-badge">
                        <div class="id-badge-title">Current Level</div>
                        <div class="id-badge-value">{{ $student->current_level ?? 'Beginner' }}</div>
                    </div>
                </div>

                <!-- Right: Form Fields -->
                <div class="id-card-right">
                    <div class="id-card-header">
                        <h1><i class="fas fa-id-card"></i> Edit Profile</h1>
                        <p class="id-card-subtitle">Update your student profile information</p>
                    </div>

                    <!-- Personal Information -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name', $student->name) }}" required class="form-control" placeholder="Enter your full name">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Current Level</label>
                            <select name="current_level" class="form-control">
                                <option value="">Select your level</option>
                                <option value="Beginner" {{ old('current_level', $student->current_level) == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="Intermediate" {{ old('current_level', $student->current_level) == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="Advanced" {{ old('current_level', $student->current_level) == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                <option value="Expert" {{ old('current_level', $student->current_level) == 'Expert' ? 'selected' : '' }}>Expert</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email', $student->user->email) }}" required class="form-control" placeholder="your.email@example.com">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $student->user->phone) }}" class="form-control" placeholder="+60123456789">
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">About Me</label>
                            <textarea name="biodata" class="form-control" placeholder="Tell us about your Quran learning journey...">{{ old('biodata', $student->biodata) }}</textarea>
                            <small class="form-hint">Share your goals, experience, and what motivates you to learn</small>
                        </div>
                    </div>

                    <!-- Password Change Section -->
                    <div class="password-section">
                        <h3><i class="fas fa-lock"></i> Change Password (Optional)</h3>
                        <small class="form-hint" style="display: block; margin-bottom: 15px;">Leave blank to keep your current password</small>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter new password">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="btn-group">
                        <a href="{{ route('students.show', $student->id) }}" class="btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Profile picture preview
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profilePreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                preview.innerHTML = `<img src="${e.target.result}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 20px;">`;
            }
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
