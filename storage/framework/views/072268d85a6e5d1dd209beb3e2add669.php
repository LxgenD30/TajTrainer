<?php $__env->startSection('title', 'My Classrooms'); ?>
<?php $__env->startSection('user-role', 'Teacher • Classroom Management'); ?>

<?php $__env->startSection('navigation'); ?>
    <a href="<?php echo e(route('home')); ?>" class="nav-item">
        <i class="fas fa-home nav-icon"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    
    <a href="<?php echo e(route('classroom.index')); ?>" class="nav-item active">
        <i class="fas fa-chalkboard-teacher nav-icon"></i>
        <span class="nav-label">My Classes</span>
    </a>
    
    <a href="<?php echo e(route('materials.index')); ?>" class="nav-item">
        <i class="fas fa-book-open nav-icon"></i>
        <span class="nav-label">Materials</span>
    </a>
    
    <a href="<?php echo e(route('teachers.show', Auth::id())); ?>" class="nav-item">
        <i class="fas fa-user-circle nav-icon"></i>
        <span class="nav-label">Profile</span>
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
    .welcome-banner {
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
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .welcome-banner:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .welcome-content {
        position: relative;
        z-index: 2;
        flex: 1;
    }
    
    .welcome-content h1 {
        font-size: 2.2rem;
        margin-bottom: 8px;
        font-weight: 700;
        color: #ffffff;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .welcome-content p {
        font-size: 1.05rem;
        opacity: 0.95;
        line-height: 1.6;
        margin: 0;
    }
    
    .btn-create {
        position: relative;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: white;
        color: #0a5c36;
        padding: 15px 30px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    
    .btn-create:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }
    
    .classrooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }
    
    .classroom-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
        transition: all 0.3s ease;
    }
    
    .classroom-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.15);
    }
    
    .classroom-header {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .classroom-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
        flex-shrink: 0;
    }
    
    .classroom-info {
        flex: 1;
        min-width: 0;
    }
    
    .classroom-title {
        color: #0a5c36;
        font-size: 1.4rem;
        margin: 0 0 8px 0;
        font-weight: 700;
    }
    
    .classroom-description {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0;
    }
    
    .access-code-section {
        background: rgba(212, 175, 55, 0.1);
        border-left: 4px solid #d4af37;
        padding: 15px;
        border-radius: 10px;
        margin: 15px 0;
    }
    
    .access-code-label {
        font-size: 0.75rem;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .access-code-display {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .code-value {
        font-family: 'Courier New', monospace;
        font-size: 1.6rem;
        font-weight: 700;
        color: #d4af37;
        letter-spacing: 4px;
    }
    
    .toggle-code-btn {
        padding: 8px 12px;
        background: #0a5c36;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.85rem;
    }
    
    .toggle-code-btn:hover {
        background: #1abc9c;
        transform: scale(1.05);
    }
    
    .classroom-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin: 15px 0;
        padding: 15px 0;
        border-top: 1px solid rgba(10, 92, 54, 0.1);
        border-bottom: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    .stat-box {
        text-align: center;
    }
    
    .stat-box-label {
        font-size: 0.7rem;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 5px;
        font-weight: 600;
    }
    
    .stat-box-value {
        font-size: 1.6rem;
        font-weight: 700;
        color: #0a5c36;
    }
    
    .stat-box-value.pending {
        color: #ff9800;
    }
    
    .stat-box-value.completed {
        color: #4caf50;
    }
    
    .classroom-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-action {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .btn-view {
        background: #0a5c36;
        color: white;
    }
    
    .btn-view:hover {
        background: #1abc9c;
        transform: scale(1.02);
    }
    
    .btn-edit {
        background: transparent;
        color: #0a5c36;
        border: 2px solid #0a5c36;
    }
    
    .btn-edit:hover {
        background: #0a5c36;
        color: white;
    }
    
    .btn-delete {
        background: transparent;
        color: #e74c3c;
        border: 2px solid #e74c3c;
        flex: 0.3;
    }
    
    .btn-delete:hover {
        background: #e74c3c;
        color: white;
    }
    
    .empty-state {
        background: white;
        border-radius: 15px;
        padding: 80px 40px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .empty-state i {
        font-size: 5rem;
        color: rgba(10, 92, 54, 0.2);
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        color: #0a5c36;
        font-size: 1.8rem;
        margin-bottom: 10px;
        font-weight: 700;
    }
    
    .empty-state p {
        color: #666;
        font-size: 1.05rem;
        margin-bottom: 30px;
    }
    
    @media (max-width: 768px) {
        .classrooms-grid {
            grid-template-columns: 1fr;
        }
        
        .welcome-banner {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .btn-create {
            width: 100%;
            justify-content: center;
        }
        
        .classroom-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div class="welcome-content">
        <h1><i class="fas fa-chalkboard-teacher"></i> My Classrooms</h1>
        <p>Create and manage virtual classrooms for your Tajweed students</p>
    </div>
    <a href="<?php echo e(route('classroom.create')); ?>" class="btn-create">
        <i class="fas fa-plus-circle"></i> Create New Class
    </a>
</div>

<!-- Classrooms Grid -->
<?php if($classrooms->count() > 0): ?>
    <div class="classrooms-grid">
        <?php $__currentLoopData = $classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="classroom-card">
                <div class="classroom-header">
                    <div class="classroom-icon">
                        <i class="fas fa-book-quran"></i>
                    </div>
                    <div class="classroom-info">
                        <h3 class="classroom-title"><?php echo e($classroom->class_name); ?></h3>
                        <p class="classroom-description"><?php echo e(Str::limit($classroom->description ?? 'No description provided', 80)); ?></p>
                    </div>
                </div>
                
                <!-- Access Code -->
                <div class="access-code-section">
                    <div class="access-code-label">
                        <i class="fas fa-key"></i> Access Code
                    </div>
                    <div class="access-code-display">
                        <span class="code-value" id="code-<?php echo e($classroom->id); ?>">••••••</span>
                        <span style="display: none;" id="real-code-<?php echo e($classroom->id); ?>"><?php echo e($classroom->access_code); ?></span>
                        <button class="toggle-code-btn" onclick="toggleCode(<?php echo e($classroom->id); ?>)">
                            <i class="fas fa-eye" id="icon-<?php echo e($classroom->id); ?>"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="classroom-stats">
                    <div class="stat-box">
                        <div class="stat-box-label">Students</div>
                        <div class="stat-box-value"><?php echo e($classroom->students_count ?? 0); ?></div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-label">Assignments</div>
                        <div class="stat-box-value"><?php echo e($classroom->assignments_count ?? 0); ?></div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-label">Pending</div>
                        <div class="stat-box-value pending"><?php echo e($classroom->pending_assignments_count ?? 0); ?></div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-label">Completed</div>
                        <div class="stat-box-value completed"><?php echo e($classroom->completed_assignments_count ?? 0); ?></div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="classroom-actions">
                    <a href="<?php echo e(route('classroom.show', $classroom->id)); ?>" class="btn-action btn-view">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="<?php echo e(route('classroom.edit', $classroom->id)); ?>" class="btn-action btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="<?php echo e(route('classroom.destroy', $classroom->id)); ?>" method="POST" style="flex: 0.3; display: contents;" onsubmit="return confirm('Are you sure you want to delete this classroom? This action cannot be undone.');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn-action btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-chalkboard"></i>
        <h3>No Classrooms Yet</h3>
        <p>Create your first classroom to start teaching and managing students</p>
        <a href="<?php echo e(route('classroom.create')); ?>" class="btn-create">
            <i class="fas fa-plus-circle"></i> Create Your First Classroom
        </a>
    </div>
<?php endif; ?>

<script>
    function toggleCode(classroomId) {
        const codeElement = document.getElementById('code-' + classroomId);
        const realCode = document.getElementById('real-code-' + classroomId).textContent;
        const icon = document.getElementById('icon-' + classroomId);
        
        if (codeElement.textContent === '••••••') {
            codeElement.textContent = realCode;
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            codeElement.textContent = '••••••';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tajtrainerV2\resources\views/teachers/classrooms.blade.php ENDPATH**/ ?>