<?php $__env->startSection('title', 'Edit Classroom'); ?>
<?php $__env->startSection('user-role', 'Teacher • Edit Classroom'); ?>

<?php $__env->startSection('navigation'); ?>
    <a href="<?php echo e(route('home')); ?>" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="<?php echo e(route('classroom.index')); ?>" class="nav-item active">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <a href="<?php echo e(route('teachers.show', Auth::id())); ?>" class="nav-item">
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
    <!-- Success Message -->
    <?php if(session('success')): ?>
        <div style="background: rgba(46, 125, 50, 0.2); border: 3px solid #4caf50; color: #2e7d32; padding: 15px 20px; border-radius: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">✓</span>
            <span style="font-weight: 600;"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Back Button -->
    <div style="margin-bottom: 20px;">
        <a href="<?php echo e(route('classroom.index')); ?>" 
            style="display: inline-flex; align-items: center; gap: 10px; color: #1a1a1a; text-decoration: none; font-family: 'El Messiri', sans-serif; font-weight: 600; font-size: 1rem; padding: 8px 16px; border-radius: 50px; background: rgba(255, 255, 255, 0.9); border: 3px solid #2a2a2a; transition: all 0.3s ease;"
            onmouseover="this.style.background='rgba(10, 92, 54, 0.1)'; this.style.transform='translateX(-5px)'; this.style.borderColor='#0a5c36'"
            onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.transform='translateX(0)'; this.style.borderColor='#2a2a2a'">
            <i class="fas fa-arrow-left"></i> Back to Classes
        </a>
    </div>

    <!-- Welcome Banner -->
    <div style="background: linear-gradient(135deg, #0a5c36, #1abc9c); border-radius: 25px; padding: 40px; margin-bottom: 30px; color: white; position: relative; overflow: hidden; box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25); border: 3px solid #2a2a2a;">
        <div style="position: relative; z-index: 1;">
            <h1 style="margin: 0 0 15px 0; font-family: 'El Messiri', serif; font-size: 2.5rem; font-weight: 700;">
                <i class="fas fa-edit"></i> Edit Classroom
            </h1>
            <p style="margin: 0; font-size: 1.1rem; opacity: 0.95; font-weight: 500; font-family: 'Cairo', sans-serif;">
                Update your classroom information and manage access settings
            </p>
        </div>
    </div>

    <!-- Form Card -->
    <div style="background: white; border-radius: 20px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 3px solid #2a2a2a; margin-bottom: 30px;">
        <form action="<?php echo e(route('classroom.update', $classroom->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <!-- Class Name -->
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 700; color: #0a5c36; font-size: 1.1rem; font-family: 'Cairo', sans-serif;">
                    <i class="fas fa-chalkboard"></i> Class Name <span style="color: #e74c3c;">*</span>
                </label>
                <input type="text" name="class_name" value="<?php echo e(old('class_name', $classroom->class_name)); ?>" required
                    style="width: 100%; padding: 15px 20px; background: white; border: 3px solid #e0e0e0; border-radius: 12px; color: #1a1a1a; font-size: 1rem; font-family: 'Cairo', sans-serif; font-weight: 500; transition: all 0.3s ease;"
                    placeholder="e.g., Tajweed Level 1"
                    onfocus="this.style.borderColor='#0a5c36'; this.style.boxShadow='0 0 0 3px rgba(10, 92, 54, 0.1)'"
                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                <?php $__errorArgs = ['class_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #e74c3c; font-size: 0.9rem; display: block; margin-top: 8px; font-weight: 600;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?>

                    </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Description -->
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 700; color: #0a5c36; font-size: 1.1rem; font-family: 'Cairo', sans-serif;">
                    <i class="fas fa-align-left"></i> Description
                </label>
                <textarea name="description" rows="5"
                    style="width: 100%; padding: 15px 20px; background: white; border: 3px solid #e0e0e0; border-radius: 12px; color: #1a1a1a; font-size: 1rem; font-family: 'Cairo', sans-serif; font-weight: 500; resize: vertical; transition: all 0.3s ease;"
                    placeholder="Describe the classroom objectives and content..."
                    onfocus="this.style.borderColor='#0a5c36'; this.style.boxShadow='0 0 0 3px rgba(10, 92, 54, 0.1)'"
                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'"><?php echo e(old('description', $classroom->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #e74c3c; font-size: 0.9rem; display: block; margin-top: 8px; font-weight: 600;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?>

                    </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Access Code Display -->
            <div style="background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(255, 215, 0, 0.05)); border: 3px solid #d4af37; border-radius: 15px; padding: 30px; margin-bottom: 35px;">
                <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                    <div style="font-size: 2.5rem; flex-shrink: 0;">🔑</div>
                    <div style="flex: 1; min-width: 250px;">
                        <h4 style="color: #d4af37; margin: 0 0 10px 0; font-family: 'El Messiri', serif; font-size: 1.3rem; font-weight: 700;">
                            Access Code
                        </h4>
                        <p style="margin: 0 0 15px 0; color: #1a1a1a; font-weight: 500;">Current access code for this classroom:</p>
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <div style="background: rgba(10, 92, 54, 0.1); padding: 18px 25px; border-radius: 12px; border: 2px solid #d4af37;">
                                <span id="accessCodeDisplay" style="font-family: 'JetBrains Mono', monospace; font-size: 2rem; font-weight: 700; color: #d4af37; letter-spacing: 5px;">
                                    ••••••
                                </span>
                                <span id="accessCodeHidden" style="display: none;"><?php echo e($classroom->access_code); ?></span>
                            </div>
                            <button type="button" onclick="toggleAccessCodeEdit()" 
                                style="padding: 12px 20px; background: #0a5c36; color: white; border: none; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;"
                                onmouseover="this.style.background='#1abc9c'"
                                onmouseout="this.style.background='#0a5c36'">
                                <span id="toggleIconEdit">👁️</span> <span id="toggleTextEdit">Show</span>
                            </button>
                            <button type="button" onclick="confirmRegenerate()" 
                                style="padding: 12px 20px; background: transparent; color: #ff9800; border: 2px solid #ff9800; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;"
                                onmouseover="this.style.background='rgba(255, 152, 0, 0.1)'"
                                onmouseout="this.style.background='transparent'">
                                <i class="fas fa-sync-alt"></i> Regenerate
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function toggleAccessCodeEdit() {
                    const codeElement = document.getElementById('accessCodeDisplay');
                    const hiddenCode = document.getElementById('accessCodeHidden').textContent.trim();
                    const toggleIcon = document.getElementById('toggleIconEdit');
                    const toggleText = document.getElementById('toggleTextEdit');
                    
                    if (codeElement.textContent.includes('•')) {
                        codeElement.textContent = hiddenCode;
                        toggleIcon.textContent = '🙈';
                        toggleText.textContent = 'Hide';
                    } else {
                        codeElement.textContent = '••••••';
                        toggleIcon.textContent = '👁️';
                        toggleText.textContent = 'Show';
                    }
                }
                
                function confirmRegenerate() {
                    if (confirm('⚠️ Warning: Generating a new access code will invalidate the current code. Students with the old code will no longer be able to join. Continue?')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?php echo e(route("classroom.regenerate", $classroom->id)); ?>';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '<?php echo e(csrf_token()); ?>';
                        
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'PATCH';
                        
                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            </script>

            <!-- Buttons -->
            <div style="display: flex; gap: 15px; justify-content: flex-end; flex-wrap: wrap;">
                <a href="<?php echo e(route('classroom.index')); ?>" 
                    style="padding: 15px 35px; background: white; color: #666; border: 3px solid #e0e0e0; border-radius: 50px; text-decoration: none; font-family: 'Cairo', sans-serif; font-weight: 700; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 10px;"
                    onmouseover="this.style.background='#f5f5f5'; this.style.borderColor='#d0d0d0'"
                    onmouseout="this.style.background='white'; this.style.borderColor='#e0e0e0'">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" 
                    style="padding: 15px 35px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border: none; border-radius: 50px; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif; font-weight: 700; box-shadow: 0 5px 20px rgba(10, 92, 54, 0.3); display: inline-flex; align-items: center; gap: 10px; font-size: 1.05rem;"
                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(10, 92, 54, 0.4)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 20px rgba(10, 92, 54, 0.3)'">
                    <i class="fas fa-save"></i> Update Classroom
                </button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tajtrainerV2\resources\views/classroom/edit.blade.php ENDPATH**/ ?>