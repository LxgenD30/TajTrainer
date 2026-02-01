@extends('layouts.template')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back to your Islamic learning journey')

@section('content')
    <!-- Welcome Card -->
    <div class="content-card welcome-card">
        <div class="welcome-icon">☪</div>
        <h2 class="welcome-title">As-salamu Alaykum!</h2>
        <p class="welcome-message">
            Welcome to TajTrainer, your comprehensive Islamic learning platform. 
            May your journey in learning the Quran and deepening your understanding of Islam 
            be blessed and fruitful. Start exploring your courses and continue your spiritual growth.
        </p>
    </div>
    
    <!-- Quick Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="content-card" style="text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 15px;">📖</div>
            <h3 style="color: var(--color-gold); font-family: 'Amiri', serif; font-size: 1.5rem; margin-bottom: 10px;">Active Courses</h3>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-light-green);">0</p>
        </div>
        
        <div class="content-card" style="text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 15px;">⏱️</div>
            <h3 style="color: var(--color-gold); font-family: 'Amiri', serif; font-size: 1.5rem; margin-bottom: 10px;">Study Hours</h3>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-light-green);">0h</p>
        </div>
        
        <div class="content-card" style="text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 15px;">🎯</div>
            <h3 style="color: var(--color-gold); font-family: 'Amiri', serif; font-size: 1.5rem; margin-bottom: 10px;">Achievements</h3>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-light-green);">0</p>
        </div>
    </div>
    
    <!-- Recent Activity Card -->
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">Recent Activity</h3>
        </div>
        <div class="card-body">
            <p style="text-align: center; opacity: 0.7; padding: 40px 0;">No recent activity yet. Start learning to see your progress here!</p>
        </div>
    </div>
@endsection
