@extends('layouts.dashboard')

@section('title', 'Edit Profile')
@section('user-role', 'Student • Edit Profile')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('content')
<style>
    /* ID Card Layout */
    .profile-card-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .id-card {
        background: linear-gradient(135deg, #0a5c36 0%, #1abc9c 100%);
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 20px 60px rgba(10, 92, 54, 0.3);
        border: 4px solid #fff;
        position: relative;
        overflow: hidden;
    }

    .id-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7z' fill='%23ffffff' fill-opacity='0.15' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.3;
    }

    .id-card-header {
        display: flex;
        align-items: center;
        gap: 30px;
        margin-bottom: 30px;
        position: relative;
        z-index: 2;
    }

    .profile-picture-section {
        position: relative;
    }

    .profile-picture-display {
        width: 180px;
        height: 180px;
        border-radius: 20px;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: #0a5c36;
    }

    .profile-picture-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        text-align: center;
        padding: 10px;
        border-radius: 0 0 15px 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .profile-picture-overlay:hover {
        background: rgba(0, 0, 0, 0.9);
    }

    #profile_picture {
        display: none;
    }

    .id-card-info {
        flex: 1;
        color: #fff;
    }

    .id-card-info h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        color: #fff;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .id-card-role {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
        backdrop-filter: blur(10px);
    }

    .id-card-details {
        display: flex;
        gap: 30px;
        margin-top: 15px;
    }

    .id-detail-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.05rem;
    }

    .id-detail-item i {
        font-size: 1.3rem;
        opacity: 0.9;
    }

    .edit-profile-banner {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 30px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
        border: 3px solid #2a2a2a;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .edit-profile-banner:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .banner-content {
        position: relative;
        z-index: 2;
    }
    
    .banner-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 5px;
        color: #ffffff;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .banner-subtitle {
        font-size: 1.05rem;
        opacity: 0.9;
        color: #ffffff;
    }
    
    .back-to-profile {
        background: white;
        color: #0a5c36;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 2;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }
    
    .back-to-profile:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        color: #0a5c36;
    }
    
    .form-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .form-section {
        background: #f9f9f9;
        padding: 25px;
        border-radius: 12px;
        border: 2px solid #2a2a2a;
        margin-bottom: 25px;
    }
    
    .section-title {
        font-size: 1.6rem;
        color: #000;
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group:last-child {
        margin-bottom: 0;
    }
    
    .form-label {
        display: block;
        color: #2a2a2a;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 1.05rem;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        background: white;
        color: #000;
        border: 2px solid #2a2a2a;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 1.05rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0a5c36;
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.1);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    
    .form-hint {
        display: block;
        font-size: 1.05rem;
        color: #555;
        margin-top: 5px;
        font-weight: 600;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .btn-group {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #2a2a2a;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: white;
        border: none;
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(10, 92, 54, 0.3);
    }
    
    .btn-secondary {
        background: white;
        color: #000;
        border: 2px solid #2a2a2a;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.05rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }
    
    .alert {
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        border: 2px solid;
    }
    
    .alert-error {
        background: rgba(231, 76, 60, 0.1);
        border-color: #e74c3c;
        color: #e74c3c;
    }
    
    .alert-error strong {
        display: block;
        margin-bottom: 8px;
    }
    
    .alert-error ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .alert-error li {
        margin: 5px 0;
    }
</style>

<div class="profile-card-container">
    <!-- Back Button -->
    <a href="{{ route('students.show', $student->id) }}" style="background: rgba(10,92,54,0.1); color: #0a5c36; border: 2px solid #0a5c36; padding: 12px 25px; border-radius: 25px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; margin-bottom: 20px;">
        <i class="fas fa-arrow-left"></i> Back to Profile
    </a>

    <!-- ID Card Style Profile Display -->
    <div class="id-card">
        <div class="id-card-header">
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

            <div class="id-card-info">
                <h1>{{ $student->name }}</h1>
                <div class="id-card-role">
                    <i class="fas fa-user-graduate"></i> Student - {{ $student->current_level ?? 'Beginner' }}
                </div>
                <div class="id-card-details">
                    <div class="id-detail-item">
                        <i class="fas fa-envelope"></i>
                        <span>{{ $student->user->email }}</span>
                    </div>
                    <div class="id-detail-item">
                        <i class="fas fa-phone"></i>
                        <span>{{ $student->user->phone ?? 'Not set' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
<div class="form-card">
    @if($errors->any())
        <div class="alert alert-error">
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

        <!-- Personal Information Section -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-user"></i> Personal Information
            </h3>
            
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $student->name) }}" 
                       class="form-control" required placeholder="Enter your full name">
            </div>

            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" value="{{ old('email', $student->user->email) }}" 
                       class="form-control" required placeholder="your.email@example.com">
                <small class="form-hint">This email will be used for login and notifications</small>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $student->user->phone) }}" 
                       class="form-control" placeholder="+60123456789">
            </div>
        </div>

        <!-- Learning Information Section -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-book-quran"></i> Learning Information
            </h3>
            
            <div class="form-group">
                <label class="form-label">Current Quran Recitation Level</label>
                <select name="current_level" class="form-control">
                    <option value="">Select your level</option>
                    <option value="Beginner" {{ old('current_level', $student->current_level) == 'Beginner' ? 'selected' : '' }}>
                        Beginner - Starting to learn Quranic recitation
                    </option>
                    <option value="Intermediate" {{ old('current_level', $student->current_level) == 'Intermediate' ? 'selected' : '' }}>
                        Intermediate - Can recite with basic tajweed
                    </option>
                    <option value="Advanced" {{ old('current_level', $student->current_level) == 'Advanced' ? 'selected' : '' }}>
                        Advanced - Proficient in tajweed rules
                    </option>
                    <option value="Expert" {{ old('current_level', $student->current_level) == 'Expert' ? 'selected' : '' }}>
                        Expert - Master of tajweed and recitation
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">About Me</label>
                <textarea name="biodata" class="form-control" 
                          placeholder="Tell us about your Quran learning journey...">{{ old('biodata', $student->biodata) }}</textarea>
                <small class="form-hint">Share your goals, experience, and what motivates you to learn</small>
            </div>
        </div>

        <!-- Change Password Section -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-lock"></i> Change Password (Optional)
            </h3>
            <small class="form-hint" style="display: block; margin-bottom: 15px;">
                Leave blank to keep your current password
            </small>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Enter new password" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" 
                           placeholder="Confirm new password" autocomplete="new-password">
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
    </form>
</div>
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
