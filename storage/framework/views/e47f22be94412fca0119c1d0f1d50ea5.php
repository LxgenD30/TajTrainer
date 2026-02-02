<?php $__env->startSection('title', 'Student Submissions'); ?>
<?php $__env->startSection('user-role', 'Teacher • Grading Queue'); ?>

<?php $__env->startSection('navigation'); ?>
    <a href="<?php echo e(route('home')); ?>" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="<?php echo e(route('classroom.index')); ?>" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="nav-label">My Classes</div>
    </a>
    
    <a href="<?php echo e(route('materials.index')); ?>" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="nav-label">Materials</div>
    </a>
    
    <a href="<?php echo e(route('teachers.show', Auth::id())); ?>" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="nav-label">Submissions</div>
    </a>
    
    <a href="<?php echo e(url('/materials')); ?>" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="nav-label">Materials</div>
    </a>
    
    <a href="<?php echo e(route('teachers.show', Auth::id())); ?>" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="nav-label">Profile</div>
    </a>
    
    <form action="<?php echo e(route('logout')); ?>" method="POST" style="display: inline;" class="nav-item">
        <?php echo csrf_field(); ?>
        <button type="submit" style="all: unset; width: 100%; cursor: pointer;">
            <div class="nav-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="nav-label">Logout</div>
        </button>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('extra-styles'); ?>
<style>
    .submissions-header {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        color: var(--white);
        box-shadow: 0 8px 20px var(--shadow);
        position: relative;
        overflow: hidden;
    }
    
    .submissions-header:before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    }
    
    .submissions-header h2 {
        color: var(--white);
        font-size: 2rem;
        margin-bottom: 10px;
        position: relative;
        z-index: 2;
    }
    
    .submissions-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }
    
    .submission-card {
        background: var(--white);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 8px 20px var(--shadow);
        transition: all 0.3s ease;
    }
    
    .submission-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(10, 92, 54, 0.2);
    }
    
    .submission-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .student-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .student-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 1.3rem;
        font-weight: 600;
    }
    
    .student-details h3 {
        color: var(--primary-green);
        font-size: 1.3rem;
        margin-bottom: 5px;
    }
    
    .student-details p {
        color: #666;
        font-size: 0.9rem;
    }
    
    .submission-status {
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        font-family: 'El Messiri', sans-serif;
    }
    
    .status-pending {
        background: rgba(255, 152, 0, 0.1);
        color: #ff9800;
    }
    
    .status-graded {
        background: rgba(76, 175, 80, 0.1);
        color: #4caf50;
    }
    
    .assignment-info {
        background: rgba(10, 92, 54, 0.05);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .assignment-info h4 {
        color: var(--primary-green);
        font-size: 1.2rem;
        margin-bottom: 10px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #666;
        font-size: 0.95rem;
    }
    
    .info-item i {
        color: var(--primary-green);
        font-size: 1.1rem;
    }
    
    .audio-player {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(46, 139, 87, 0.05));
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .audio-player h5 {
        color: var(--primary-green);
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    
    .audio-controls {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .play-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
        border: none;
        font-size: 1.3rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .play-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.3);
    }
    
    .audio-timeline {
        flex: 1;
        height: 8px;
        background: rgba(10, 92, 54, 0.1);
        border-radius: 10px;
        position: relative;
        cursor: pointer;
    }
    
    .audio-progress {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-green), var(--light-green));
        border-radius: 10px;
        width: 0%;
        transition: width 0.1s linear;
    }
    
    .tajweed-errors {
        margin-bottom: 20px;
    }
    
    .tajweed-errors h5 {
        color: var(--primary-green);
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    
    .error-list {
        display: grid;
        gap: 12px;
    }
    
    .error-item {
        background: rgba(231, 76, 60, 0.05);
        border-left: 4px solid #e74c3c;
        border-radius: 10px;
        padding: 15px;
    }
    
    .error-item h6 {
        color: #e74c3c;
        font-size: 1rem;
        margin-bottom: 5px;
    }
    
    .error-item p {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .grading-form {
        background: rgba(212, 175, 55, 0.05);
        border-radius: 15px;
        padding: 25px;
        margin-top: 20px;
    }
    
    .grading-form h5 {
        color: var(--primary-green);
        margin-bottom: 20px;
        font-size: 1.1rem;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        color: var(--primary-green);
        font-weight: 600;
        margin-bottom: 8px;
        font-family: 'El Messiri', sans-serif;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid rgba(10, 92, 54, 0.2);
        border-radius: 10px;
        font-family: 'Amiri', serif;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.1);
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .btn-submit-grade {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        font-family: 'El Messiri', sans-serif;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-submit-grade:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.3);
    }
    
    .empty-submissions {
        background: var(--white);
        border-radius: 20px;
        padding: 80px 40px;
        text-align: center;
        box-shadow: 0 8px 20px var(--shadow);
    }
    
    .empty-submissions i {
        font-size: 6rem;
        color: rgba(10, 92, 54, 0.2);
        margin-bottom: 20px;
    }
    
    .empty-submissions h3 {
        color: var(--primary-green);
        font-size: 1.8rem;
        margin-bottom: 10px;
    }
    
    .empty-submissions p {
        color: #666;
        font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
        .submission-header {
            flex-direction: column;
            gap: 15px;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Submissions Header -->
<div class="submissions-header">
    <h2><i class="fas fa-clipboard-check"></i> Student Submissions</h2>
    <p>Review and grade student recitations with AI-powered Tajweed analysis</p>
</div>

<?php
    $submissions = \App\Models\AssignmentSubmission::with(['student', 'assignment'])
        ->whereNull('marks')
        ->latest()
        ->get();
?>

<?php if($submissions->isEmpty()): ?>
    <div class="empty-submissions">
        <i class="fas fa-check-double"></i>
        <h3>All Caught Up!</h3>
        <p>No pending submissions to grade at the moment.</p>
    </div>
<?php else: ?>
    <?php $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="submission-card">
            <!-- Submission Header -->
            <div class="submission-header">
                <div class="student-info">
                    <div class="student-avatar">
                        <?php echo e(strtoupper(substr($submission->student->user->name ?? 'S', 0, 1))); ?>

                    </div>
                    <div class="student-details">
                        <h3><?php echo e($submission->student->user->name ?? 'Student Name'); ?></h3>
                        <p>Submitted <?php echo e($submission->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <span class="submission-status <?php echo e($submission->marks ? 'status-graded' : 'status-pending'); ?>">
                    <?php echo e($submission->marks ? 'Graded' : 'Pending Review'); ?>

                </span>
            </div>
            
            <!-- Assignment Info -->
            <div class="assignment-info">
                <h4><?php echo e($submission->assignment->title ?? 'Assignment Title'); ?></h4>
                <div class="info-grid">
                    <div class="info-item">
                        <i class="fas fa-book-open"></i>
                        <span><?php echo e($submission->assignment->classroom->name ?? 'Class Name'); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span>Due: <?php echo e($submission->assignment->due_date ? \Carbon\Carbon::parse($submission->assignment->due_date)->format('M d, Y') : 'N/A'); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-star"></i>
                        <span>Total Marks: <?php echo e($submission->assignment->total_marks ?? 100); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Audio Player -->
            <?php if($submission->audio_file_path): ?>
                <div class="audio-player">
                    <h5><i class="fas fa-headphones"></i> Student Recitation</h5>
                    <div class="audio-controls">
                        <button class="play-btn" onclick="togglePlay('audio-<?php echo e($submission->id); ?>')">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="audio-timeline">
                            <div class="audio-progress"></div>
                        </div>
                        <span class="audio-time">0:00 / 0:00</span>
                    </div>
                    <audio id="audio-<?php echo e($submission->id); ?>" src="<?php echo e(asset('storage/' . $submission->audio_file_path)); ?>" style="display: none;"></audio>
                </div>
            <?php endif; ?>
            
            <!-- Tajweed Errors -->
            <?php
                $errors = \App\Models\TajweedErrorLog::where('submission_id', $submission->id)->get();
            ?>
            
            <?php if($errors->isNotEmpty()): ?>
                <div class="tajweed-errors">
                    <h5><i class="fas fa-exclamation-triangle"></i> Detected Tajweed Issues (<?php echo e($errors->count()); ?>)</h5>
                    <div class="error-list">
                        <?php $__currentLoopData = $errors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="error-item">
                                <h6><?php echo e(ucfirst($error->error_type)); ?></h6>
                                <p><?php echo e($error->error_message); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Grading Form -->
            <?php if(!$submission->marks): ?>
                <div class="grading-form">
                    <h5><i class="fas fa-pen"></i> Grade Submission</h5>
                    <form action="<?php echo e(route('submissions.grade', $submission->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="form-group">
                            <label for="marks-<?php echo e($submission->id); ?>">
                                <i class="fas fa-award"></i> Marks Obtained
                            </label>
                            <input 
                                type="number" 
                                id="marks-<?php echo e($submission->id); ?>" 
                                name="marks" 
                                min="0" 
                                max="<?php echo e($submission->assignment->total_marks ?? 100); ?>" 
                                required
                                placeholder="Enter marks (out of <?php echo e($submission->assignment->total_marks ?? 100); ?>)"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="feedback-<?php echo e($submission->id); ?>">
                                <i class="fas fa-comment-dots"></i> Teacher Feedback
                            </label>
                            <textarea 
                                id="feedback-<?php echo e($submission->id); ?>" 
                                name="feedback" 
                                rows="4"
                                placeholder="Provide constructive feedback on the student's recitation..."
                            ></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit-grade">
                            <i class="fas fa-check-circle"></i> Submit Grade
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('extra-scripts'); ?>
<script>
    function togglePlay(audioId) {
        const audio = document.getElementById(audioId);
        const btn = event.currentTarget;
        const icon = btn.querySelector('i');
        
        if (audio.paused) {
            audio.play();
            icon.classList.remove('fa-play');
            icon.classList.add('fa-pause');
        } else {
            audio.pause();
            icon.classList.remove('fa-pause');
            icon.classList.add('fa-play');
        }
        
        audio.addEventListener('timeupdate', function() {
            const progress = (audio.currentTime / audio.duration) * 100;
            btn.closest('.audio-player').querySelector('.audio-progress').style.width = progress + '%';
            
            const currentMin = Math.floor(audio.currentTime / 60);
            const currentSec = Math.floor(audio.currentTime % 60);
            const durationMin = Math.floor(audio.duration / 60);
            const durationSec = Math.floor(audio.duration % 60);
            
            btn.closest('.audio-player').querySelector('.audio-time').textContent = 
                `${currentMin}:${currentSec.toString().padStart(2, '0')} / ${durationMin}:${durationSec.toString().padStart(2, '0')}`;
        });
        
        audio.addEventListener('ended', function() {
            icon.classList.remove('fa-pause');
            icon.classList.add('fa-play');
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tajtrainerV2\resources\views/teachers/student-submissions.blade.php ENDPATH**/ ?>