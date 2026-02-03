

<?php $__env->startSection('title', 'Assignment Details'); ?>
<?php $__env->startSection('user-role', (auth()->user()->role_id == 3 ? 'Teacher' : 'Student') . ' • Assignment Details'); ?>

<?php $__env->startSection('navigation'); ?>
    <a href="<?php echo e(route('home')); ?>" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="<?php echo e(route('classroom.index')); ?>" class="nav-item active">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <?php if(auth()->user()->role_id == 3): ?>
    <a href="<?php echo e(route('students.list')); ?>" class="nav-item">
        <div class="nav-icon"><i class="fas fa-user-graduate"></i></div>
        <div class="nav-label">My Students</div>
    </a>
    <?php endif; ?>
    <a href="<?php echo e(route('materials.index')); ?>" class="nav-item">
        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
        <div class="nav-label">Materials</div>
    </a>
    <?php if(auth()->user()->role_id == 2): ?>
    <a href="<?php echo e(route('student.practice')); ?>" class="nav-item">
        <div class="nav-icon"><i class="fas fa-microphone"></i></div>
        <div class="nav-label">Practice</div>
    </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-header h1 {
        color: white;
        font-size: 2rem;
        margin: 0;
    }
    
    .header-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 2px solid;
    }
    
    .btn-edit {
        background: #d4af37;
        color: #0a5c36;
        border-color: #d4af37;
    }
    
    .btn-edit:hover {
        background: rgba(212, 175, 55, 0.8);
    }
    
    .btn-delete {
        background: #e74c3c;
        color: white;
        border-color: #e74c3c;
    }
    
    .btn-delete:hover {
        background: #c0392b;
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        color: #1abc9c;
        border: 2px solid #1abc9c;
        padding: 12px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-top: 30px;
    }
    
    .btn-back:hover {
        background: rgba(26, 188, 156, 0.1);
    }
    
    .detail-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .detail-section {
        margin-bottom: 30px;
        padding: 25px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        border: 2px solid #0a5c36;
    }
    
    .detail-section.gold {
        background: rgba(212, 175, 55, 0.1);
        border-color: #d4af37;
    }
    
    .section-title {
        color: #0a5c36;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title.gold {
        color: #d4af37;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
    }
    
    .info-label {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }
    
    .info-value {
        color: #0a5c36;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .rules-container {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .rule-badge {
        display: inline-block;
        background: rgba(212, 175, 55, 0.2);
        color: #d4af37;
        padding: 6px 15px;
        border-radius: 15px;
        font-size: 0.9rem;
        font-weight: 600;
        border: 2px solid #d4af37;
    }
    
    .material-info {
        padding: 20px;
        background: rgba(26, 188, 156, 0.1);
        border-radius: 10px;
        border: 2px solid #1abc9c;
    }
    
    .material-title {
        color: #1abc9c;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .material-links {
        display: flex;
        gap: 10px;
    }
    
    .material-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #0a5c36;
        color: #d4af37;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .material-link:hover {
        background: #064e32;
    }
    
    .instructions-box {
        background: rgba(10, 92, 54, 0.1);
        padding: 20px;
        border-radius: 10px;
        border: 2px solid #0a5c36;
    }
    
    .instructions-text {
        color: #333;
        line-height: 1.6;
        white-space: pre-wrap;
    }
    
    .submission-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: rgba(212, 175, 55, 0.15);
        border: 2px solid #d4af37;
        border-radius: 20px;
        color: #d4af37;
        font-weight: 600;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h1>📝 Assignment Details</h1>
    <div class="header-actions">
        <?php if(auth()->user()->role_id == 3): ?>
            <a href="<?php echo e(route('assignment.edit', $assignment->assignment_id)); ?>" class="btn btn-edit">
                ✏️ Edit
            </a>
            <form action="<?php echo e(route('assignment.destroy', $assignment->assignment_id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-delete">
                    🗑️ Delete
                </button>
            </form>
        <?php else: ?>
            <?php if(isset($submission)): ?>
                <span class="btn" style="background: rgba(149, 165, 166, 0.2); color: #7f8c8d; border-color: #95a5a6; cursor: default;">
                    ✅ Submitted
                </span>
            <?php else: ?>
                <a href="<?php echo e(route('student.assignment.submit', $assignment->assignment_id)); ?>" class="btn" style="background: #95a5a6; color: white; border-color: #95a5a6;">
                    🎤 Attempt Assignment
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Assignment Details Card -->
<div class="detail-card">
    <!-- Quran Verse Section -->
    <div class="detail-section gold">
        <h4 class="section-title gold">
            <span>📖</span> Assigned Quran Verse
        </h4>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Surah</div>
                <div class="info-value"><?php echo e($assignment->surah); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Ayat From</div>
                <div class="info-value"><?php echo e($assignment->start_verse); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Ayat To</div>
                <div class="info-value"><?php echo e($assignment->end_verse ?? 'N/A'); ?></div>
            </div>
        </div>
        
        <?php if($assignment->tajweed_rules && count($assignment->tajweed_rules) > 0): ?>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid rgba(212, 175, 55, 0.2);">
                <div class="info-label" style="margin-bottom: 10px;">✨ Focus on Tajweed Rules:</div>
                <div class="rules-container">
                    <?php $__currentLoopData = $assignment->tajweed_rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="rule-badge"><?php echo e($rule); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Reference Materials -->
    <?php if($assignment->material): ?>
        <div class="detail-section">
            <h4 class="section-title">
                <span>📚</span> Reference Materials
            </h4>
            <div class="material-info">
                <p class="material-title"><?php echo e($assignment->material->title); ?></p>
                
                <div class="material-links">
                    <?php if($assignment->material->file_path): ?>
                        <a href="<?php echo e(Storage::url($assignment->material->file_path)); ?>" target="_blank" class="material-link">
                            📄 Download PDF
                        </a>
                    <?php endif; ?>
                    
                    <?php if($assignment->material->video_link): ?>
                        <a href="<?php echo e($assignment->material->video_link); ?>" target="_blank" class="material-link">
                            🎥 Watch Video
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Student Submission Section -->
    <?php if(auth()->user()->role_id == 2 && isset($submission)): ?>
        <div class="detail-section" style="background: rgba(46, 204, 113, 0.1); border-color: #27ae60;">
            <h4 class="section-title" style="color: #27ae60;">
                <span>✅</span> Your Submission
            </h4>
            
            <div class="info-grid" style="margin-bottom: 20px;">
                <div class="info-item">
                    <div class="info-label">📅 Submitted At</div>
                    <div class="info-value" style="color: #27ae60;"><?php echo e($submission->submitted_at ? $submission->submitted_at->format('F d, Y h:i A') : 'Processing...'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">📊 Status</div>
                    <div class="info-value" style="color: #27ae60;">
                        <span class="submission-badge" style="background: rgba(46, 204, 113, 0.2); border-color: #27ae60; color: #27ae60;">
                            <?php echo e(ucfirst($submission->status ?? 'pending')); ?>

                        </span>
                    </div>
                </div>
                <?php if(isset($submission->score)): ?>
                    <div class="info-item">
                        <div class="info-label">🎯 Score</div>
                        <div class="info-value" style="color: #d4af37;"><?php echo e($submission->score->marks_obtained ?? 'Not graded'); ?> / <?php echo e($assignment->total_marks); ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($submission->audio_file_path): ?>
                <div style="margin-bottom: 20px;">
                    <div class="info-label" style="margin-bottom: 10px;">🎤 Your Recording</div>
                    <audio controls style="width: 100%; border-radius: 10px;">
                        <source src="<?php echo e(Storage::url($submission->audio_file_path)); ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            <?php endif; ?>

            <?php if($submission->transcription): ?>
                <div style="margin-bottom: 20px;">
                    <div class="info-label" style="margin-bottom: 10px;">📝 Transcription</div>
                    <div class="instructions-box">
                        <p class="instructions-text"><?php echo e($submission->transcription); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($submission->tajweed_analysis && is_array($submission->tajweed_analysis)): ?>
                <div>
                    <div class="info-label" style="margin-bottom: 10px;">🎯 Tajweed Analysis</div>
                    <div style="background: white; padding: 20px; border-radius: 10px; border: 2px solid #27ae60;">
                        <?php $__currentLoopData = $submission->tajweed_analysis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="margin-bottom: 10px;">
                                <strong style="color: #0a5c36;"><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?>:</strong>
                                <span><?php echo e(is_array($value) ? json_encode($value) : $value); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Assignment Info -->
    <div class="info-grid" style="margin-bottom: 30px;">
        <div class="info-item">
            <div class="info-label">📅 Due Date</div>
            <div class="info-value"><?php echo e($assignment->due_date->format('F d, Y h:i A')); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">🎯 Total Marks</div>
            <div class="info-value"><?php echo e($assignment->total_marks); ?> points</div>
        </div>
        <div class="info-item">
            <div class="info-label">🎤 Submission Type</div>
            <div class="info-value">
                <span class="submission-badge">
                    <?php if($assignment->is_voice_submission): ?>
                        🎤 Voice Recording
                    <?php else: ?>
                        📝 Text Submission
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div>
        <div class="info-label" style="margin-bottom: 10px; font-size: 1rem; font-weight: 600; color: #0a5c36;">📋 Instructions</div>
        <div class="instructions-box">
            <p class="instructions-text"><?php echo e($assignment->instructions); ?></p>
        </div>
    </div>

    <!-- Back Button -->
    <a href="<?php echo e(route('classroom.show', $classroom->id)); ?>" class="btn-back">
        ← Back to Classroom
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tajtrainerV2\resources\views/assignment/show.blade.php ENDPATH**/ ?>