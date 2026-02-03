<?php $__env->startSection('title', 'My Students'); ?>
<?php $__env->startSection('user-role', 'Teacher • Student Management'); ?>

<?php $__env->startSection('navigation'); ?>
    <a href="<?php echo e(route('home')); ?>" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="<?php echo e(route('classroom.index')); ?>" class="nav-item">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <a href="<?php echo e(route('students.list')); ?>" class="nav-item active">
        <div class="nav-icon"><i class="fas fa-user-graduate"></i></div>
        <div class="nav-label">My Students</div>
    </a>
    <a href="<?php echo e(route('materials.index')); ?>" class="nav-item">
        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
        <div class="nav-label">Materials</div>
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Success Message -->
    <?php if(session('success')): ?>
        <div style="background: rgba(46, 125, 50, 0.2); border: 3px solid #4caf50; color: #2e7d32; padding: 15px 20px; border-radius: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">✓</span>
            <span style="font-weight: 600;"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div style="background: white; border-radius: 25px; padding: 30px; margin-bottom: 30px; border: 3px solid #2a2a2a; box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin: 0 0 10px 0; font-family: 'El Messiri', serif; font-size: 2.5rem; color: #0a5c36; font-weight: 700;">
                    <i class="fas fa-user-graduate"></i> My Students
                </h1>
                <p style="margin: 0; font-size: 1.1rem; color: #666; font-family: 'Cairo', sans-serif;">
                    <?php echo e($students->count()); ?> student<?php echo e($students->count() != 1 ? 's' : ''); ?> enrolled across <?php echo e($classrooms->count()); ?> classroom<?php echo e($classrooms->count() != 1 ? 's' : ''); ?>

                </p>
            </div>
            <div style="font-size: 4rem; opacity: 0.1;">👨‍🎓</div>
        </div>
    </div>

    <?php if($students->count() > 0): ?>
        <!-- Students Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="background: white; border-radius: 20px; padding: 25px; border: 3px solid #e0e0e0; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);"
                     onmouseover="this.style.borderColor='#0a5c36'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 25px rgba(10, 92, 54, 0.15)'"
                     onmouseout="this.style.borderColor='#e0e0e0'; this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0, 0, 0, 0.05)'">
                    
                    <!-- Student Header -->
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #f5f5f5;">
                        <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #0a5c36, #1abc9c); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700; font-family: 'El Messiri', serif; box-shadow: 0 5px 15px rgba(10, 92, 54, 0.3);">
                            <?php echo e(strtoupper(substr($student->user->name, 0, 1))); ?>

                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <h3 style="margin: 0 0 5px 0; font-size: 1.3rem; color: #1a1a1a; font-weight: 700; font-family: 'El Messiri', serif;">
                                <?php echo e($student->user->name); ?>

                            </h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #666; font-family: 'Cairo', sans-serif; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <i class="fas fa-envelope" style="color: #0a5c36;"></i> <?php echo e($student->user->email); ?>

                            </p>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-phone" style="color: #0a5c36; width: 20px;"></i>
                                <span style="color: #666; font-family: 'Cairo', sans-serif;"><?php echo e($student->phone_number ?? 'No phone number'); ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-layer-group" style="color: #0a5c36; width: 20px;"></i>
                                <span style="padding: 4px 12px; background: rgba(26, 188, 156, 0.15); color: #1abc9c; border-radius: 12px; font-size: 0.9rem; font-weight: 600; font-family: 'Cairo', sans-serif;">
                                    <?php echo e(ucfirst($student->level ?? 'Beginner')); ?>

                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Classes -->
                    <div style="margin-bottom: 20px;">
                        <p style="margin: 0 0 10px 0; font-size: 0.85rem; color: #999; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Cairo', sans-serif;">
                            Enrolled Classes
                        </p>
                        <?php if($student->classrooms->count() > 0): ?>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                <?php $__currentLoopData = $student->classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span style="display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: rgba(212, 175, 55, 0.15); color: #d4af37; border-radius: 15px; font-size: 0.85rem; font-weight: 600; font-family: 'Cairo', sans-serif;">
                                        <i class="fas fa-chalkboard-teacher"></i> <?php echo e($classroom->class_name); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p style="margin: 0; color: #999; font-style: italic; font-family: 'Cairo', sans-serif;">Not enrolled in any classes</p>
                        <?php endif; ?>
                    </div>

                    <!-- Action Button -->
                    <a href="<?php echo e(route('teacher.student.profile', $student->id)); ?>" 
                       style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 12px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 15px; text-decoration: none; font-weight: 700; font-size: 1rem; font-family: 'Cairo', sans-serif; transition: all 0.3s ease; border: 3px solid transparent;"
                       onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 5px 20px rgba(10, 92, 54, 0.4)'"
                       onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'">
                        <i class="fas fa-user-circle"></i> View Profile
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div style="background: white; border-radius: 25px; padding: 60px 40px; text-align: center; border: 3px solid #e0e0e0;">
            <div style="font-size: 5rem; margin-bottom: 20px; opacity: 0.3;">👥</div>
            <h3 style="margin: 0 0 10px 0; font-size: 1.8rem; color: #333; font-family: 'El Messiri', serif;">No Students Yet</h3>
            <p style="margin: 0; font-size: 1.1rem; color: #666; font-family: 'Cairo', sans-serif;">
                Students will appear here once they enroll in your classrooms.
            </p>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tajtrainerV2\resources\views/teachers/students.blade.php ENDPATH**/ ?>