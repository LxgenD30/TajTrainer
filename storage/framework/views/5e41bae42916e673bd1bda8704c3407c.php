<?php $__env->startSection('title', $classroom->class_name); ?>
<?php $__env->startSection('user-role', (auth()->user()->role_id == 3 ? 'Teacher' : 'Student') . ' • ' . $classroom->class_name); ?>

<?php $__env->startSection('navigation'); ?>
    <?php if(auth()->user()->role_id == 3): ?>
        
        <a href="<?php echo e(route('home')); ?>" class="nav-item">
            <div class="nav-icon"><i class="fas fa-home"></i></div>
            <div class="nav-label">Dashboard</div>
        </a>
        <a href="<?php echo e(route('classroom.index')); ?>" class="nav-item active">
            <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="nav-label">My Classes</div>
        </a>
        <a href="<?php echo e(route('students.list')); ?>" class="nav-item">
            <div class="nav-icon"><i class="fas fa-user-graduate"></i></div>
            <div class="nav-label">My Students</div>
        </a>
        <a href="<?php echo e(route('materials.index')); ?>" class="nav-item">
            <div class="nav-icon"><i class="fas fa-book-open"></i></div>
            <div class="nav-label">Materials</div>
        </a>
    <?php else: ?>
        
        <a href="<?php echo e(route('home')); ?>" class="nav-item">
            <div class="nav-icon"><i class="fas fa-home"></i></div>
            <div class="nav-label">Dashboard</div>
        </a>
        <a href="<?php echo e(route('student.classes')); ?>" class="nav-item active">
            <div class="nav-icon"><i class="fas fa-chalkboard"></i></div>
            <div class="nav-label">My Classes</div>
        </a>
        <a href="<?php echo e(route('materials.index')); ?>" class="nav-item">
            <div class="nav-icon"><i class="fas fa-book-open"></i></div>
            <div class="nav-label">Materials</div>
        </a>
        <a href="<?php echo e(route('student.practice')); ?>" class="nav-item">
            <div class="nav-icon"><i class="fas fa-microphone"></i></div>
            <div class="nav-label">Practice</div>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Success Message -->
    <?php if(session('success')): ?>
        <div style="background: rgba(46, 125, 50, 0.2); border: 3px solid #4caf50; color: #2e7d32; padding: 15px 20px; border-radius: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">✓</span>
            <span style="font-weight: 600;"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Classroom Header Banner (Like Student Welcome Banner) -->
    <div style="background: linear-gradient(135deg, #0a5c36, #1abc9c); border-radius: 25px; padding: 40px; margin-bottom: 30px; color: white; position: relative; overflow: hidden; box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25); border: 3px solid #2a2a2a;">
        <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url(\"data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E\"); opacity: 0.4;"></div>
        <div style="position: relative; z-index: 2;">
            <h1 style="margin: 0 0 15px 0; font-family: 'El Messiri', serif; font-size: 2.5rem; font-weight: 700; text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);">
                <i class="fas fa-chalkboard-teacher"></i> <?php echo e($classroom->class_name); ?>

            </h1>
            <p style="margin: 0 0 30px 0; font-size: 1.1rem; opacity: 0.95; font-weight: 500; font-family: 'Cairo', sans-serif; line-height: 1.6;">
                <?php echo e($classroom->description ?? 'Manage your classroom students and assignments'); ?>

            </p>
            
            <!-- Stats Grid (Like Student Stats) -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px;">
                <div style="background: rgba(255, 255, 255, 0.2); border-radius: 15px; padding: 20px; backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.3); text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: 700; line-height: 1;"><?php echo e($classroom->students->count()); ?></div>
                    <div style="font-size: 0.95rem; opacity: 0.9; margin-top: 8px;">Students</div>
                </div>
                <div style="background: rgba(255, 255, 255, 0.2); border-radius: 15px; padding: 20px; backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.3); text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: 700; line-height: 1; color: #ffd700;"><?php echo e($assignments->count()); ?></div>
                    <div style="font-size: 0.95rem; opacity: 0.9; margin-top: 8px;">Assignments</div>
                </div>
                <div style="background: rgba(255, 255, 255, 0.2); border-radius: 15px; padding: 20px; backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.3); text-align: center;">
                    <div style="font-size: 1.6rem; font-weight: 700; line-height: 1; letter-spacing: 3px; font-family: 'JetBrains Mono', monospace;"><?php echo e($classroom->access_code); ?></div>
                    <div style="font-size: 0.95rem; opacity: 0.9; margin-top: 8px;">Access Code</div>
                </div>
            </div>

            <!-- Quick Actions (Inside Banner) -->
            <?php if(auth()->user()->role_id == 3): ?>
                
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="<?php echo e(route('classroom.index')); ?>" 
                        style="padding: 12px 25px; background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(10px); border-radius: 50px; text-decoration: none; font-weight: 600; font-family: 'Cairo', sans-serif; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;"
                        onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.borderColor='rgba(255, 255, 255, 0.6)'"
                        onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.borderColor='rgba(255, 255, 255, 0.4)'">
                        <i class="fas fa-arrow-left"></i> Back to Classes
                    </a>
                    <a href="<?php echo e(route('classroom.edit', $classroom->id)); ?>" 
                        style="padding: 12px 25px; background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(10px); border-radius: 50px; text-decoration: none; font-weight: 600; font-family: 'Cairo', sans-serif; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;"
                        onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.borderColor='rgba(255, 255, 255, 0.6)'"
                        onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.borderColor='rgba(255, 255, 255, 0.4)'">
                        <i class="fas fa-edit"></i> Edit Classroom
                    </a>
                    <a href="<?php echo e(route('assignment.create', $classroom->id)); ?>" 
                        style="padding: 12px 25px; background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(10px); border-radius: 50px; text-decoration: none; font-weight: 600; font-family: 'Cairo', sans-serif; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;"
                        onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.borderColor='rgba(255, 255, 255, 0.6)'"
                        onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.borderColor='rgba(255, 255, 255, 0.4)'">
                        <i class="fas fa-plus"></i> New Assignment
                    </a>
                    <form action="<?php echo e(route('classroom.destroy', $classroom->id)); ?>" method="POST" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this classroom? This action cannot be undone.')"
                            style="padding: 12px 25px; background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(10px); border-radius: 50px; cursor: pointer; font-weight: 600; font-family: 'Cairo', sans-serif; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;"
                            onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.borderColor='rgba(255, 255, 255, 0.6)'"
                            onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.borderColor='rgba(255, 255, 255, 0.4)'">
                            <i class="fas fa-trash-alt"></i> Delete Classroom
                        </button>
                    </form>
                </div>
            <?php else: ?>
                
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="<?php echo e(route('student.classes')); ?>" 
                        style="padding: 12px 25px; background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(10px); border-radius: 50px; text-decoration: none; font-weight: 600; font-family: 'Cairo', sans-serif; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;"
                        onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.borderColor='rgba(255, 255, 255, 0.6)'"
                        onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.borderColor='rgba(255, 255, 255, 0.4)'">
                        <i class="fas fa-arrow-left"></i> Back to My Classes
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Two Column Layout for Teacher, Single Column for Student -->
    <div style="display: grid; grid-template-columns: <?php echo e(auth()->user()->role_id == 3 ? '1fr 1fr' : '1fr'); ?>; gap: 30px; margin-bottom: 30px;">
        <!-- Students Section (Teachers Only) -->
        <?php if(auth()->user()->role_id == 3): ?>
            <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 3px solid #2a2a2a;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #e0e0e0;">
                    <h3 style="margin: 0; font-family: 'El Messiri', serif; font-size: 1.5rem; color: #1a1a1a;">
                        <i class="fas fa-users"></i> Students (<?php echo e($classroom->students->count()); ?>)
                    </h3>
                </div>
                
                <?php if($classroom->students->count() > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php $__currentLoopData = $classroom->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="background: rgba(10, 92, 54, 0.05); border-radius: 12px; padding: 15px 20px; border: 2px solid rgba(10, 92, 54, 0.3); transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(10, 92, 54, 0.1);"
                            onmouseover="this.style.background='rgba(10, 92, 54, 0.1)'; this.style.borderColor='#0a5c36'"
                            onmouseout="this.style.background='rgba(10, 92, 54, 0.05)'; this.style.borderColor='rgba(10, 92, 54, 0.1)'">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #0a5c36, #1abc9c); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem;">
                                        <?php echo e(strtoupper(substr($student->name, 0, 1))); ?>

                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #1a1a1a; font-family: 'Cairo', sans-serif;"><?php echo e($student->name); ?></div>
                                        <div style="font-size: 0.85rem; color: #666;"><?php echo e($student->email); ?></div>
                                    </div>
                                </div>
                                <?php
                                    $submissionCount = \App\Models\AssignmentSubmission::where('student_id', $student->id)
                                        ->whereHas('assignment', function($query) use ($classroom) {
                                            $query->where('class_id', $classroom->id);
                                        })->count();
                                    $gradedCount = \App\Models\AssignmentSubmission::where('student_id', $student->id)
                                        ->where('status', 'graded')
                                        ->whereHas('assignment', function($query) use ($classroom) {
                                            $query->where('class_id', $classroom->id);
                                        })->count();
                                ?>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="padding: 6px 15px; background: rgba(76, 175, 80, 0.1); color: #4caf50; border-radius: 50px; font-size: 0.85rem; font-weight: 600;">
                                        <?php echo e($submissionCount); ?> submission<?php echo e($submissionCount != 1 ? 's' : ''); ?>

                                    </span>
                                </div>
                            </div>
                            <a href="<?php echo e(route('teacher.student.submissions', ['classroom' => $classroom->id, 'student' => $student->id])); ?>" 
                                style="display: block; width: 100%; padding: 10px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 8px; text-align: center; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(10, 92, 54, 0.3)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                <i class="fas fa-clipboard-check"></i> View Submissions & Grade
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 50px 20px; color: #999;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">👥</div>
                    <p style="margin: 0; font-size: 1.1rem; color: #666;">No students enrolled yet</p>
                    <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #999;">Share the access code to invite students</p>
                </div>
            <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Assignments Section -->
        <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 3px solid #2a2a2a;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #e0e0e0;">
                <h3 style="margin: 0; font-family: 'El Messiri', serif; font-size: 1.5rem; color: #1a1a1a;">
                    <i class="fas fa-tasks"></i> Assignments (<?php echo e($assignments->count()); ?>)
                </h3>
                <?php if(auth()->user()->role_id == 3): ?>
                <a href="<?php echo e(route('assignment.create', $classroom->id)); ?>" style="padding: 8px 20px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(10, 92, 54, 0.3)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <i class="fas fa-plus"></i> New
                </a>
                <?php endif; ?>
            </div>
            
            <?php if($assignments->count() > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="background: rgba(10, 92, 54, 0.05); border-left: 4px solid #0a5c36; border-radius: 12px; padding: 20px; transition: all 0.3s ease;"
                            onmouseover="this.style.background='rgba(10, 92, 54, 0.1)'; this.style.transform='translateX(5px)'"
                            onmouseout="this.style.background='rgba(10, 92, 54, 0.05)'; this.style.transform='translateX(0)'">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <h4 style="margin: 0; font-family: 'Cairo', sans-serif; font-size: 1.1rem; color: #1a1a1a;">
                                    <?php echo e($assignment->surah); ?> (<?php echo e($assignment->start_verse); ?><?php echo e($assignment->end_verse ? '-' . $assignment->end_verse : ''); ?>)
                                </h4>
                                <span style="padding: 5px 12px; background: rgba(212, 175, 55, 0.1); color: #d4af37; border-radius: 50px; font-size: 0.8rem; font-weight: 600;">
                                    <?php echo e($assignment->total_marks); ?> pts
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 15px; font-size: 0.85rem; color: #666; margin-bottom: 15px;">
                                <span><i class="far fa-calendar"></i> Due: <?php echo e(\Carbon\Carbon::parse($assignment->due_date)->format('M d, Y')); ?></span>
                                <?php
                                    $submissionCount = \App\Models\AssignmentSubmission::where('assignment_id', $assignment->assignment_id)->count();
                                ?>
                                <span><i class="fas fa-file-alt"></i> <?php echo e($submissionCount); ?> submission<?php echo e($submissionCount != 1 ? 's' : ''); ?></span>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <a href="<?php echo e(route('assignment.show', $assignment->assignment_id)); ?>" style="flex: 1; padding: 8px; background: white; color: #0a5c36; border: 2px solid #0a5c36; border-radius: 8px; text-align: center; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;"
                                    onmouseover="this.style.background='#0a5c36'; this.style.color='white'"
                                    onmouseout="this.style.background='white'; this.style.color='#0a5c36'">
                                    View
                                </a>
                                <?php if(auth()->user()->role_id == 3): ?>
                                <a href="<?php echo e(route('assignment.edit', $assignment->assignment_id)); ?>" style="flex: 1; padding: 8px; background: white; color: #ff9800; border: 2px solid #ff9800; border-radius: 8px; text-align: center; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;"
                                    onmouseover="this.style.background='#ff9800'; this.style.color='white'"
                                    onmouseout="this.style.background='white'; this.style.color='#ff9800'">
                                    Edit
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 50px 20px; color: #999;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">📋</div>
                    <p style="margin: 0; font-size: 1.1rem; color: #666;">No assignments yet</p>
                    <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #999;">Create your first assignment to get started</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tajtrainerV2\resources\views/classroom/show.blade.php ENDPATH**/ ?>