<?php $__env->startSection('title', 'Teacher Profile'); ?>
<?php $__env->startSection('user-role', 'Teacher • Profile'); ?>

<?php $__env->startSection('navigation'); ?>
    <a href="<?php echo e(route('home')); ?>" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="<?php echo e(route('classroom.index')); ?>" class="nav-item">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <a href="<?php echo e(route('materials.index')); ?>" class="nav-item">
        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
        <div class="nav-label">Materials</div>
    </a>
    <a href="<?php echo e(route('teachers.show', Auth::id())); ?>" class="nav-item active">
        <div class="nav-icon"><i class="fas fa-user-circle"></i></div>
        <div class="nav-label">Profile</div>
    </a>
    <form action="<?php echo e(route('logout')); ?>" method="POST" style="display: inline;" class="nav-item">
        <?php echo csrf_field(); ?>
        <button type="submit" style="all: unset; width: 100%; cursor: pointer;">
            <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
            <div class="nav-label">Logout</div>
        </button>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div style="background: rgba(31, 39, 27, 0.7); border: 2px solid var(--primary-green); border-radius: 25px; padding: 25px; font-family: 'Cairo', sans-serif; box-shadow: 0 15px 35px rgba(0,0,0,0.4);">

    <?php if(session('success')): ?>
        <div style="background: rgba(46, 204, 113, 0.1); border: 2px solid #2ecc71; color: #2ecc71; padding: 12px; border-radius: 8px; margin-bottom: 15px;">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid rgba(227, 216, 136, 0.2);">
        <h3 style="color: var(--gold); font-size: 1.5rem; margin: 0; display: flex; align-items: center; gap: 12px;">
            <span style="color: #e67e22; filter: drop-shadow(0 0 5px rgba(230, 126, 34, 0.5));">👨‍🏫</span> Teacher Profile
        </h3>
        <?php if(Auth::id() == $teacher->id): ?>
            <a href="<?php echo e(route('teachers.edit', $teacher)); ?>" style="background: linear-gradient(135deg, #e3d888, #c5a059); color: #1f271b; padding: 10px 22px; border-radius: 12px; text-decoration: none; font-weight: 800; font-size: 0.85rem; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(227, 216, 136, 0.2);">
                ✏️ Edit Profile
            </a>
        <?php endif; ?>
    </div>

    <div class="profile-container" style="display: grid; grid-template-columns: 280px 1fr; gap: 20px; align-items: start;">
        
        <div style="background: rgba(227, 216, 136, 0.03); border: 1px solid var(--primary-green); border-radius: 20px; padding: 30px 15px; text-align: center;">
            <div style="width: 150px; height: 150px; background: linear-gradient(135deg, #e3d888, #c5a059); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 4rem; font-weight: bold; color: #1f271b; border: 4px solid rgba(31, 39, 27, 0.8); box-shadow: 0 8px 25px rgba(0,0,0,0.3);">
                <?php echo e(substr($teacher->name, 0, 1)); ?>

            </div>

            <h2 style="color: #fff; margin: 0 0 5px 0; font-size: 1.4rem; letter-spacing: 0.5px;"><?php echo e($teacher->name); ?></h2>
            
            <?php if($teacher->title): ?>
            <div style="color: var(--gold); font-size: 0.9rem; margin-bottom: 15px; font-weight: 600;"><?php echo e($teacher->title); ?></div>
            <?php endif; ?>
            
            <div style="display: inline-block; background: rgba(227, 216, 136, 0.1); border: 1px solid var(--gold); color: var(--gold); padding: 4px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; margin-bottom: 25px;">
                👨‍🏫 Teacher
            </div>

            <div style="text-align: left; border-top: 1px solid rgba(227, 216, 136, 0.1); padding-top: 20px; display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label style="display: block; color: var(--gold); font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; margin-bottom: 2px;">Email</label>
                    <div style="color: var(--color-light); font-size: 0.85rem; word-break: break-all;"><?php echo e($teacher->user->email); ?></div>
                </div>

                <?php if($teacher->user->phone): ?>
                <div>
                    <label style="display: block; color: var(--gold); font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; margin-bottom: 2px;">Phone</label>
                    <div style="color: var(--color-light); font-size: 0.85rem;"><?php echo e($teacher->user->phone); ?></div>
                </div>
                <?php endif; ?>

                <div>
                    <label style="display: block; color: var(--gold); font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; margin-bottom: 2px;">Member Since</label>
                    <div style="color: var(--color-light); font-size: 0.85rem;"><?php echo e($teacher->created_at->format('M d, Y')); ?></div>
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            
            <?php if($teacher->biodata): ?>
            <div style="background: rgba(31, 39, 27, 0.4); border: 1px solid var(--primary-green); border-radius: 15px; padding: 20px;">
                <h4 style="color: var(--gold); margin: 0 0 10px 0; font-size: 1rem; display: flex; align-items: center; gap: 8px;">📝 About</h4>
                <p style="color: var(--color-light); opacity: 0.85; line-height: 1.5; margin: 0; font-size: 0.9rem;">
                    <?php echo e($teacher->biodata); ?>

                </p>
            </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                <div style="background: rgba(31, 39, 27, 0.4); border: 1px solid var(--primary-green); border-radius: 15px; padding: 20px; text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 5px;">🏫</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #fff; line-height: 1;"><?php echo e($teacher->classrooms->count()); ?></div>
                    <div style="color: var(--gold); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-top: 5px;">Total Classes</div>
                </div>

                <div style="background: rgba(31, 39, 27, 0.4); border: 1px solid var(--primary-green); border-radius: 15px; padding: 20px; text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 5px;">👥</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #fff; line-height: 1;"><?php echo e($teacher->classrooms->sum(function($classroom) { return $classroom->students->count(); })); ?></div>
                    <div style="color: var(--gold); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-top: 5px;">Total Students</div>
                </div>
            </div>

            <div style="background: rgba(31, 39, 27, 0.4); border: 1px solid var(--primary-green); border-radius: 15px; padding: 20px;">
                <h4 style="color: var(--gold); margin: 0 0 15px 0; font-size: 1rem;">🏫 My Classes</h4>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php $__empty_1 = true; $__currentLoopData = $teacher->classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div style="background: rgba(227, 216, 136, 0.05); border: 1px solid rgba(227, 216, 136, 0.1); border-radius: 10px; padding: 12px 18px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1;">
                            <div style="color: #fff; font-weight: 600; font-size: 0.9rem; margin-bottom: 3px;"><?php echo e($classroom->class_name); ?></div>
                            <div style="color: var(--color-light); opacity: 0.7; font-size: 0.75rem;"><?php echo e($classroom->students->count()); ?> students enrolled</div>
                        </div>
                        <a href="<?php echo e(route('classroom.show', $classroom)); ?>" style="color: var(--gold); text-decoration: none; font-size: 0.8rem; font-weight: 700; padding: 5px 12px; background: rgba(227, 216, 136, 0.1); border-radius: 6px; border: 1px solid var(--gold);">
                            View →
                        </a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p style="color: var(--color-light); opacity: 0.5; text-align: center; margin: 0; font-size: 0.85rem;">No classes created yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .classroom-card:hover {
        background: rgba(31, 39, 27, 0.8) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(227, 216, 136, 0.2);
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tajtrainerV2\resources\views/teachers/show.blade.php ENDPATH**/ ?>