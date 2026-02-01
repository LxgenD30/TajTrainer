@extends('layouts.template')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your teaching activities')

@section('content')
    <!-- Welcome Card -->
    <div class="content-card welcome-card">
        <div class="welcome-icon">☪</div>
        <h2 class="welcome-title">Assalamualaikum, {{ Auth::user()->name }}!</h2>
        <p class="welcome-message">
            Welcome to TajTrainer Teacher Dashboard. Here you can manage your classes, 
            track student progress, and create engaging Tajweed assignments. 
            May Allah bless your efforts in teaching His words.
        </p>
    </div>
    
    <!-- Quick Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="content-card" style="text-align: center; transition: transform 0.3s ease;"
            onmouseover="this.style.transform='translateY(-5px)'"
            onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 3rem; margin-bottom: 15px;">🏫</div>
            <h3 style="color: var(--color-gold); font-family: 'Amiri', serif; font-size: 1.5rem; margin-bottom: 10px;">Total Classes</h3>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-light-green);">{{ $stats['total_classes'] ?? 0 }}</p>
            <a href="{{ route('classroom.index') }}" style="color: var(--color-light); opacity: 0.7; font-size: 0.9rem; text-decoration: none;">View All Classes →</a>
        </div>
        
        <div class="content-card" style="text-align: center; transition: transform 0.3s ease;"
            onmouseover="this.style.transform='translateY(-5px)'"
            onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 3rem; margin-bottom: 15px;">👥</div>
            <h3 style="color: var(--color-gold); font-family: 'Amiri', serif; font-size: 1.5rem; margin-bottom: 10px;">Total Students</h3>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-light-green);">{{ $stats['total_students'] ?? 0 }}</p>
            <a href="{{ route('students.list') }}" style="color: var(--color-light); opacity: 0.7; font-size: 0.9rem; text-decoration: none;">View All Students →</a>
        </div>
        
        <div class="content-card" style="text-align: center; transition: transform 0.3s ease;"
            onmouseover="this.style.transform='translateY(-5px)'"
            onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 3rem; margin-bottom: 15px;">📝</div>
            <h3 style="color: var(--color-gold); font-family: 'Amiri', serif; font-size: 1.5rem; margin-bottom: 10px;">Assignments</h3>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-light-green);">{{ $stats['total_assignments'] ?? 0 }}</p>
            <span style="color: var(--color-light); opacity: 0.7; font-size: 0.9rem;">Active Assignments</span>
        </div>
        
        <div class="content-card" style="text-align: center; transition: transform 0.3s ease;"
            onmouseover="this.style.transform='translateY(-5px)'"
            onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 3rem; margin-bottom: 15px;">📚</div>
            <h3 style="color: var(--color-gold); font-family: 'Amiri', serif; font-size: 1.5rem; margin-bottom: 10px;">Materials</h3>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-light-green);">{{ $stats['total_materials'] ?? 0 }}</p>
            <a href="{{ route('materials.index') }}" style="color: var(--color-light); opacity: 0.7; font-size: 0.9rem; text-decoration: none;">View Materials →</a>
        </div>
    </div>
@endsection
