@extends('layouts.dashboard')

@section('title', 'Create Teacher Profile')
@section('user-role', 'Admin • Teacher Management')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">➕ Create Teacher Profile</h3>
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

            <form action="{{ route('teachers.store') }}" method="POST">
                @csrf

                <!-- Account Information -->
                <div style="background: rgba(227, 216, 136, 0.05); padding: 20px; border-radius: 12px; border: 2px solid var(--primary-green); margin-bottom: 20px;">
                    <h4 style="color: var(--gold); margin: 0 0 15px 0; font-size: 1.1rem;">👤 Account Information</h4>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--gold); font-weight: 600; margin-bottom: 8px;">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--primary-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--gold); font-weight: 600; margin-bottom: 8px;">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--primary-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--gold); font-weight: 600; margin-bottom: 8px;">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--primary-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; color: var(--gold); font-weight: 600; margin-bottom: 8px;">Password *</label>
                            <input type="password" name="password" required
                                style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--primary-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                        </div>
                        <div>
                            <label style="display: block; color: var(--gold); font-weight: 600; margin-bottom: 8px;">Confirm Password *</label>
                            <input type="password" name="password_confirmation" required
                                style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--primary-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div style="background: rgba(227, 216, 136, 0.05); padding: 20px; border-radius: 12px; border: 2px solid var(--primary-green); margin-bottom: 20px;">
                    <h4 style="color: var(--gold); margin: 0 0 15px 0; font-size: 1.1rem;">📚 Professional Information</h4>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--gold); font-weight: 600; margin-bottom: 8px;">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g., Ustadh, Ustadzah, Sheikh"
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--primary-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--gold); font-weight: 600; margin-bottom: 8px;">Biodata / About</label>
                        <textarea name="biodata" rows="4"
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--primary-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem; resize: vertical;">{{ old('biodata') }}</textarea>
                    </div>
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <a href="{{ route('teachers.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Create Teacher Profile</button>
                </div>
            </form>
        </div>
    </div>
@endsection
