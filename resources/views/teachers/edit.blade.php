@extends('layouts.dashboard')

@section('title', 'Update Profile')
@section('user-role', 'Teacher • Edit Profile')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
<style>
    /* High Contrast Card Styles */
    .edit-banner {
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
    
    .edit-banner:before {
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
    
    .banner-content h1 {
        font-size: 2rem;
        margin-bottom: 8px;
        font-weight: 700;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .banner-content p {
        font-size: 1.05rem;
        opacity: 0.95;
        margin: 0;
    }
    
    .back-btn {
        background: white;
        color: #0a5c36;
        padding: 12px 24px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 2;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    
    .back-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }
    
    .form-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
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
        color: #000;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 1.05rem;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        background: white;
        color: #333;
        border: 2px solid #2a2a2a;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: 1.05rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0a5c36;
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.1);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }
    
    .form-hint {
        display: block;
        font-size: 1.05rem;
        color: #666;
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
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.2);
    }
    
    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(10, 92, 54, 0.3);
    }
    
    .btn-secondary {
        background: white;
        color: #0a5c36;
        border: 2px solid #2a2a2a;
        padding: 10px 30px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 1.05rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-secondary:hover {
        background: #0a5c36;
        color: white;
        transform: translateY(-2px);
    }
    
    .alert-error {
        background: #ffebee;
        border: 3px solid #f44336;
        color: #c62828;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 25px;
        font-weight: 700;
    }
    
    .alert-error strong {
        display: block;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }
    
    .alert-error ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .alert-error li {
        margin: 5px 0;
        font-size: 1.05rem;
    }
</style>

<!-- Banner -->
<div class="edit-banner">
    <div class="banner-content">
        <h1><i class="fas fa-edit"></i> Edit Profile</h1>
        <p>Update your teacher profile information</p>
    </div>
    <a href="{{ route('teachers.show', $teacher) }}" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Profile
    </a>
</div>

<!-- Form Card -->
<div class="form-card">
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

    <form action="{{ route('teachers.update', $teacher) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Account Information -->
        <div class="form-section">
            <h3 class="section-title"><i class="fas fa-user"></i> Account Information</h3>
            
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required class="form-control" placeholder="Enter your full name">
            </div>

            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" value="{{ old('email', $teacher->user->email) }}" required class="form-control" placeholder="your.email@example.com">
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $teacher->user->phone) }}" class="form-control" placeholder="Your phone number">
            </div>
        </div>

        <!-- Professional Information -->
        <div class="form-section">
            <h3 class="section-title"><i class="fas fa-award"></i> Professional Information</h3>
            
            <div class="form-group">
                <label class="form-label">Title</label>
                <input type="text" name="title" value="{{ old('title', $teacher->title) }}" placeholder="e.g., Ustadh, Ustadzah, Sheikh" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Biodata / About</label>
                <textarea name="biodata" class="form-control" placeholder="Tell us about your teaching experience and qualifications...">{{ old('biodata', $teacher->biodata) }}</textarea>
            </div>
        </div>

        <!-- Change Password (Optional) -->
        <div class="form-section">
            <h3 class="section-title"><i class="fas fa-lock"></i> Change Password (Optional)</h3>
            <small class="form-hint" style="display: block; margin-bottom: 15px;">Leave blank to keep your current password</small>
            
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" placeholder="Enter current password">
                <small class="form-hint">Required if you want to change your password</small>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control" placeholder="Confirm new password">
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="btn-group">
            <a href="{{ route('teachers.show', $teacher) }}" class="btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Update Profile
            </button>
        </div>
    </form>
</div>
@endsection
