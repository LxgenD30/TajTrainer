<?php $__env->startSection('title', 'My Profile'); ?>
<?php $__env->startSection('user-role', 'Student • Profile'); ?>

<?php $__env->startSection('navigation'); ?>
    <a href="<?php echo e(route('student.dashboard')); ?>" class="nav-item">
        <i class="fas fa-home nav-icon"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    
    <a href="<?php echo e(route('student.classes')); ?>" class="nav-item">
        <i class="fas fa-users nav-icon"></i>
        <span class="nav-label">My Classes</span>
    </a>
    
    <a href="<?php echo e(route('student.practice')); ?>" class="nav-item">
        <i class="fas fa-microphone-alt nav-icon"></i>
        <span class="nav-label">Practice</span>
    </a>
    
    <a href="<?php echo e(route('student.progress')); ?>" class="nav-item">
        <i class="fas fa-chart-line nav-icon"></i>
        <span class="nav-label">My Progress</span>
    </a>
    
    <a href="<?php echo e(route('student.materials')); ?>" class="nav-item">
        <i class="fas fa-book-open nav-icon"></i>
        <span class="nav-label">Materials</span>
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
    .profile-banner {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 30px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
        border: 3px solid #2a2a2a;
    }
    
    .profile-banner:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .profile-header {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 30px;
    }
    
    .profile-avatar {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .profile-info h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        font-weight: 700;
        color: #ffffff;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .profile-info p {
        font-size: 1.1rem;
        opacity: 0.95;
        line-height: 1.6;
        margin: 5px 0;
    }
    
    .edit-profile-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 12px 24px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        margin-top: 15px;
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .edit-profile-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .info-card h3 {
        color: #0a5c36;
        font-weight: 700;
        margin-bottom: 20px;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 10px;
        margin-bottom: 10px;
    }
    
    .info-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
    }
    
    .info-content {
        flex: 1;
    }
    
    .info-label {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 3px;
    }
    
    .info-value {
        font-weight: 600;
        color: #0a5c36;
        font-size: 1rem;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.15);
    }
    
    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #0a5c36;
        line-height: 1;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #666;
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .section-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .section-title {
        font-size: 1.5rem;
        color: #0a5c36;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .class-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        border-left: 4px solid #0a5c36;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
    }
    
    .class-item:hover {
        background: rgba(10, 92, 54, 0.1);
        transform: translateX(5px);
    }
    
    .class-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .class-details {
        flex: 1;
    }
    
    .class-details h4 {
        color: #0a5c36;
        font-weight: 700;
        margin-bottom: 5px;
        font-size: 1.05rem;
    }
    
    .class-details p {
        color: #666;
        font-size: 0.85rem;
        margin: 0;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #999;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.3;
    }
</style>

<!-- Profile Banner -->
<section class="profile-banner">
    <div class="profile-header">
        <?php if($student->profile_picture): ?>
            <img src="<?php echo e(asset('storage/' . $student->profile_picture)); ?>" alt="Profile Picture" class="profile-avatar">
        <?php else: ?>
            <img src="<?php echo e(asset('images/default-avatar.png')); ?>" alt="Default Avatar" class="profile-avatar">
        <?php endif; ?>
        
        <div class="profile-info">
            <h1><?php echo e($student->name); ?></h1>
            <p><i class="fas fa-envelope"></i> <?php echo e($student->user->email); ?></p>
            <p><i class="fas fa-id-card"></i> Student ID: <?php echo e($student->id); ?></p>
            
            <a href="<?php echo e(route('students.edit', $student->id)); ?>" class="edit-profile-btn">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </div>
    </div>
</section>

<!-- Personal Information -->
<div class="info-grid">
    <div class="info-card">
        <h3><i class="fas fa-user-circle"></i> Personal Information</h3>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-user"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Full Name</div>
                <div class="info-value"><?php echo e($student->name); ?></div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Email Address</div>
                <div class="info-value"><?php echo e($student->user->email); ?></div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-id-badge"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Student ID</div>
                <div class="info-value">#<?php echo e($student->id); ?></div>
            </div>
        </div>
    </div>
    
    <div class="info-card">
        <h3><i class="fas fa-book-quran"></i> Learning Information</h3>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Current Level</div>
                <div class="info-value"><?php echo e($student->current_level ?? 'Not Set'); ?></div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Member Since</div>
                <div class="info-value"><?php echo e($student->created_at->format('M d, Y')); ?></div>
            </div>
        </div>
        
        <?php if($student->biodata): ?>
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="info-content">
                <div class="info-label">About</div>
                <div class="info-value" style="font-weight: 400;"><?php echo e(Str::limit($student->biodata, 80)); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-value"><?php echo e($student->classrooms->count()); ?></div>
        <div class="stat-label">Enrolled Classes</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-value"><?php echo e($student->scores->count()); ?></div>
        <div class="stat-label">Submissions</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">⭐</div>
        <div class="stat-value"><?php echo e($student->scores->count() > 0 ? number_format($student->scores->avg('score'), 1) : '0'); ?>%</div>
        <div class="stat-label">Average Score</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">🏆</div>
        <div class="stat-value"><?php echo e($student->scores->where('score', '>=', 80)->count()); ?></div>
        <div class="stat-label">High Scores (80%+)</div>
    </div>
</div>

<!-- Enrolled Classes -->
<div class="section-card">
    <h2 class="section-title">
        <i class="fas fa-users"></i> Enrolled Classes
    </h2>
    
    <?php if($student->classrooms->count() > 0): ?>
        <?php $__currentLoopData = $student->classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('classroom.show', $classroom->id)); ?>" class="class-item">
                <div class="class-icon">
                    <i class="fas fa-book-quran"></i>
                </div>
                <div class="class-details">
                    <h4><?php echo e($classroom->class_name); ?></h4>
                    <p>
                        <i class="fas fa-user"></i> <?php echo e($classroom->teacher->name); ?> • 
                        <i class="fas fa-calendar"></i> Joined <?php echo e(\Carbon\Carbon::parse($classroom->pivot->date_joined)->format('M d, Y')); ?>

                    </p>
                </div>
                <i class="fas fa-arrow-right" style="color: #0a5c36; font-size: 1.2rem;"></i>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>You haven't enrolled in any classes yet.</p>
            <a href="<?php echo e(route('student.classes')); ?>" style="display: inline-flex; align-items: center; gap: 8px; background: #0a5c36; color: white; padding: 10px 20px; border-radius: 25px; text-decoration: none; font-weight: 600; margin-top: 10px;">
                <i class="fas fa-plus-circle"></i> Browse Classes
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tajtrainerV2\resources\views/students/show.blade.php ENDPATH**/ ?>