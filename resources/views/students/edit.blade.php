@extends('layouts.dashboard')

@section('title', 'Edit Profile')
@section('user-role', 'Student • Edit Profile')

@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="nav-item">
        <i class="fas fa-home nav-icon"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    
    <a href="{{ route('student.classes') }}" class="nav-item">
        <i class="fas fa-users nav-icon"></i>
        <span class="nav-label">My Classes</span>
    </a>
    
    <a href="{{ route('student.practice') }}" class="nav-item">
        <i class="fas fa-microphone-alt nav-icon"></i>
        <span class="nav-label">Practice</span>
    </a>
    
    <a href="{{ route('student.progress') }}" class="nav-item">
        <i class="fas fa-chart-line nav-icon"></i>
        <span class="nav-label">My Progress</span>
    </a>
    
    <a href="{{ route('student.materials') }}" class="nav-item">
        <i class="fas fa-book-open nav-icon"></i>
        <span class="nav-label">Materials</span>
    </a>
@endsection

@section('extra-styles')
<style>
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
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 5px;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .banner-subtitle {
        font-size: 0.95rem;
        opacity: 0.9;
    }
    
    .back-to-profile {
        background: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: #0a5c36;
        padding: 12px 24px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 2;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    
    .back-to-profile:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
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
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.03), rgba(26, 188, 156, 0.03));
        padding: 25px;
        border-radius: 15px;
        border: 2px solid rgba(10, 92, 54, 0.1);
        margin-bottom: 25px;
    }
    
    .section-title {
        font-size: 1.2rem;
        color: #0a5c36;
        font-weight: 700;
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
        color: #0a5c36;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        background: white;
        color: #333;
        border: 2px solid rgba(10, 92, 54, 0.2);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 1rem;
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
        font-size: 0.85rem;
        color: #666;
        margin-top: 5px;
        font-style: italic;
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
        border-top: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.3);
    }
    
    .btn-secondary {
        background: white;
        color: #0a5c36;
        border: 2px solid #0a5c36;
        padding: 10px 30px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-secondary:hover {
        background: #0a5c36;
        color: white;
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
@endsection

@section('content')

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

    <form action="{{ route('students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')

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
@endsection
