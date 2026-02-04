@extends('layouts.auth-modal')

@section('title', 'Register')

@section('modal-content')
<h2 style="text-align: center; margin-bottom: 30px; color: var(--primary-green); font-family: 'Reem Kufi Fun', sans-serif; font-size: 2rem;">Join TajTrainer</h2>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <!-- Role Selection - At Top -->
    <div class="form-group">
        <label for="user_type">I am a *</label>
        <select id="user_type" name="role_id" required onchange="toggleRoleFields()">
            <option value="">Select your role</option>
            <option value="2" {{ old('role_id') == '2' ? 'selected' : '' }}>Student</option>
            <option value="3" {{ old('role_id') == '3' ? 'selected' : '' }}>Teacher</option>
        </select>
        @error('role_id')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="name">Full Name *</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
        @error('name')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email Address *</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>


    <!-- Student-specific field -->
    <div class="form-group" id="student_level_field" style="display: none;">
        <label for="current_level">Current Level *</label>
        <select id="current_level" name="current_level">
            <option value="">Select your level</option>
            <option value="Beginner" {{ old('current_level') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
            <option value="Intermediate" {{ old('current_level') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
            <option value="Advanced" {{ old('current_level') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
            <option value="Expert" {{ old('current_level') == 'Expert' ? 'selected' : '' }}>Expert</option>
        </select>
        @error('current_level')
            <div class="error-message">{{ $message }}</div>
        @enderror
        <div style="height:8px"></div>
        <label for="phone_student">Phone Number *</label>
        <input id="phone_student" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+60123456789">
        @error('phone')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <!-- Teacher-specific field -->
    <div class="form-group" id="teacher_title_field" style="display: none;">
        <label for="title">Title *</label>
        <select id="title" name="title">
            <option value="">Select your title</option>
            <option value="Ustaz" {{ old('title') == 'Ustaz' ? 'selected' : '' }}>Ustaz</option>
            <option value="Sheikh" {{ old('title') == 'Sheikh' ? 'selected' : '' }}>Sheikh</option>
            <option value="Ustazah" {{ old('title') == 'Ustazah' ? 'selected' : '' }}>Ustazah</option>
        </select>
        @error('title')
            <div class="error-message">{{ $message }}</div>
        @enderror
        <div style="height:8px"></div>
        <label for="phone_teacher">Phone Number *</label>
        <input id="phone_teacher" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+60123456789">
        @error('phone')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Password *</label>
        <input id="password" type="password" name="password" required autocomplete="new-password">
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password-confirm">Confirm Password *</label>
        <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
    </div>

    <button type="submit" class="submit-btn">Create Account</button>
</form>

<div class="form-footer">
    <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
</div>

<script>
function toggleRoleFields() {
    const roleSelect = document.getElementById('user_type');
    const selectedRole = roleSelect.value;
    
    const studentLevelField = document.getElementById('student_level_field');
    const teacherTitleField = document.getElementById('teacher_title_field');
    const currentLevelSelect = document.getElementById('current_level');
    const titleSelect = document.getElementById('title');
    const phoneStudent = document.getElementById('phone_student');
    const phoneTeacher = document.getElementById('phone_teacher');
    
    // Hide both fields initially
    studentLevelField.style.display = 'none';
    teacherTitleField.style.display = 'none';
    
    // Remove required attribute from both
    currentLevelSelect.removeAttribute('required');
    titleSelect.removeAttribute('required');
    if (phoneStudent) phoneStudent.removeAttribute('required');
    if (phoneTeacher) phoneTeacher.removeAttribute('required');
    
    // Show and make required based on role
    if (selectedRole === '2') { // Student
        studentLevelField.style.display = 'block';
        currentLevelSelect.setAttribute('required', 'required');
        if (phoneStudent) phoneStudent.setAttribute('required', 'required');
    } else if (selectedRole === '3') { // Teacher
        teacherTitleField.style.display = 'block';
        titleSelect.setAttribute('required', 'required');
        if (phoneTeacher) phoneTeacher.setAttribute('required', 'required');
    }
}

// Run on page load to handle old values
document.addEventListener('DOMContentLoaded', function() {
    toggleRoleFields();
});
</script>
@endsection
