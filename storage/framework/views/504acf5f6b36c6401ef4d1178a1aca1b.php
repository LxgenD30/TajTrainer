<?php
    $isStudent = Auth::user()->role_id == 2; // Role 2 = Student, Role 3 = Teacher
    $layout = $isStudent ? 'layouts.dashboard' : 'layouts.template';
?>



<?php $__env->startSection('title', $material->title); ?>
<?php if($isStudent): ?>
    <?php $__env->startSection('user-role', 'Student • Material Details'); ?>
<?php else: ?>
    <?php $__env->startSection('page-title', $material->title); ?>
    <?php $__env->startSection('page-subtitle', 'Material Details'); ?>
<?php endif; ?>

<?php if($isStudent): ?>
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
    <a href="<?php echo e(route('student.materials')); ?>" class="nav-item active">
        <i class="fas fa-book-open nav-icon"></i>
        <span class="nav-label">Materials</span>
    </a>
<?php $__env->stopSection(); ?>
<?php endif; ?>

<?php $__env->startSection('content'); ?>
<style>
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        padding: 10px 20px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
        position: relative;
        z-index: 10;
    }
    
    .back-button:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateX(-5px);
        color: #ffffff;
    }
    
    .material-header-card {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 40px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
        border: 3px solid #2a2a2a;
    }
    
    .material-header-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .material-title-section {
        display: flex;
        justify-content: space-between;
        align-items: start;
        gap: 20px;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }
    
    .title-left {
        flex: 1;
        min-width: 300px;
    }
    
    .material-title-large {
        font-size: 2.5rem;
        color: #ffffff !important;
        margin-bottom: 15px;
        font-weight: 700;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .material-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 15px;
    }
    
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 15px;
        font-size: 0.9rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(5px);
        color: #ffffff;
    }
    
    .badge-public {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    
    .badge-private {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
    }
    
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.4);
        cursor: pointer;
        font-size: 0.95rem;
    }
    
    .btn-edit {
        background: rgba(212, 175, 55, 0.9);
        color: #1a1a1a;
        border-color: #d4af37;
    }
    
    .btn-edit:hover {
        background: #d4af37;
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
    }
    
    .btn-delete {
        background: rgba(231, 76, 60, 0.9);
        color: white;
        border-color: #e74c3c;
    }
    
    .btn-delete:hover {
        background: #e74c3c;
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
    }
    
    .content-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .video-container {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 15px;
    }
    
    .thumbnail-container {
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .thumbnail-container img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .section-heading {
        font-size: 1.6rem;
        color: #0a5c36;
        margin-bottom: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .description-box {
        background: rgba(10, 92, 54, 0.05);
        padding: 25px;
        border-radius: 15px;
        border-left: 5px solid #0a5c36;
        line-height: 1.8;
        font-size: 1.1rem;
        color: #333;
        white-space: pre-wrap;
    }
    
    .resources-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .resource-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.05), rgba(46, 139, 87, 0.05));
        border: 2px solid #0a5c36;
        border-radius: 15px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .resource-card:hover {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.1), rgba(46, 139, 87, 0.1));
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(10, 92, 54, 0.2);
    }
    
    .resource-icon-large {
        font-size: 2.5rem;
    }
    
    .resource-info h4 {
        color: #0a5c36;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .resource-info p {
        color: #666;
        font-size: 0.9rem;
        margin: 0;
    }
    
    .tips-box {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(212, 175, 55, 0.05));
        border: 2px solid #d4af37;
        border-radius: 15px;
        padding: 25px;
    }
    
    .tips-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .tips-title {
        color: #d4af37;
        font-size: 1.3rem;
        font-weight: 700;
    }
    
    .tips-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .tips-list li {
        position: relative;
        padding-left: 30px;
        margin-bottom: 12px;
        color: #333;
        line-height: 1.6;
    }
    
    .tips-list li:before {
        content: "✓";
        position: absolute;
        left: 0;
        color: #d4af37;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    /* Compact 2-Column Layout */
    .material-content-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 25px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    @media (max-width: 1200px) {
        .material-content-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .material-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .compact-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.1);
        border: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .compact-heading {
        font-size: 1.1rem;
        color: #0a5c36;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<!-- Material Header with Back Button -->
<section class="material-header-card">
    <?php
        $backRoute = Auth::user()->role_id == 2 ? route('student.materials') : route('materials.index');
    ?>
    
    <div class="material-title-section">
        <div class="title-left">
            <h1 class="material-title-large"><?php echo e($material->title); ?></h1>
            <div class="material-badges">
                <span class="badge <?php echo e($material->is_public ? 'badge-public' : 'badge-private'); ?>">
                    <i class="fas fa-<?php echo e($material->is_public ? 'globe' : 'lock'); ?>"></i>
                    <?php echo e($material->is_public ? 'Public' : 'Private'); ?>

                </span>
                <span class="badge">
                    <i class="fas fa-calendar"></i>
                    <?php echo e($material->created_at->format('M d, Y')); ?>

                </span>
                <?php if($material->type): ?>
                    <span class="badge">
                        <i class="fas fa-<?php echo e($material->type === 'pdf' ? 'file-pdf' : ($material->type === 'video' ? 'video' : ($material->type === 'audio' ? 'headphones' : 'file-alt'))); ?>"></i>
                        <?php echo e(ucfirst($material->type)); ?>

                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="action-buttons">
            <?php if(!$isStudent): ?>
                <a href="<?php echo e(route('materials.edit', $material->material_id)); ?>" class="btn-action btn-edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="<?php echo e(route('materials.destroy', $material->material_id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this material?')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn-action btn-delete">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            <?php endif; ?>
            <a href="<?php echo e($backRoute); ?>" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Materials
            </a>
        </div>
    </div>
</section>

<!-- Success Message -->
<?php if(session('success')): ?>
    <div style="background: linear-gradient(135deg, #d4f4dd, #a8e6cf); border-left: 5px solid #0a5c36; color: #064e32; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; font-weight: 500; max-width: 1400px; margin-left: auto; margin-right: auto;">
        <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
        <span><?php echo e(session('success')); ?></span>
    </div>
<?php endif; ?>

<!-- Compact 2-Column Layout -->
<div class="material-content-grid">
    <!-- Left: Video -->
    <div>
        <?php if($material->video_link): ?>
            <?php
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $material->video_link, $matches);
                $videoId = $matches[1] ?? null;
            ?>
            <?php if($videoId): ?>
                <div class="content-card" style="padding: 0; overflow: hidden;">
                    <div class="video-container" style="margin: 0;">
                        <iframe 
                            src="https://www.youtube.com/embed/<?php echo e($videoId); ?>" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            <?php else: ?>
                <div class="compact-card">
                    <div class="resource-card">
                        <div class="resource-icon-large">🎥</div>
                        <div class="resource-info" style="flex: 1;">
                            <h4>External Video</h4>
                            <p>Click to watch the video</p>
                        </div>
                        <a href="<?php echo e($material->video_link); ?>" target="_blank" style="color: #0a5c36; font-size: 1.5rem;">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php elseif($material->thumbnail): ?>
            <div class="content-card" style="padding: 0; overflow: hidden;">
                <img src="<?php echo e(filter_var($material->thumbnail, FILTER_VALIDATE_URL) ? $material->thumbnail : Storage::url($material->thumbnail)); ?>" 
                     alt="<?php echo e($material->title); ?>"
                     style="width: 100%; height: auto; display: block;">
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Right Sidebar: Description, Downloads, Tips -->
    <div class="material-sidebar">
        <!-- Description -->
        <?php if($material->description): ?>
            <div class="compact-card">
                <h3 class="compact-heading">
                    <i class="fas fa-align-left"></i> Description
                </h3>
                <p style="color: #666; line-height: 1.6; font-size: 0.95rem;"><?php echo e($material->description); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Download Resources -->
        <?php if($material->file_path || $material->video_link): ?>
            <div class="compact-card">
                <h3 class="compact-heading">
                    <i class="fas fa-download"></i> Resources
                </h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <?php if($material->file_path): ?>
                        <a href="<?php echo e(Storage::url($material->file_path)); ?>" target="_blank" download 
                           style="display: flex; align-items: center; gap: 12px; padding: 12px; background: rgba(10, 92, 54, 0.05); border-radius: 10px; text-decoration: none; color: inherit; border: 2px solid rgba(10, 92, 54, 0.1); transition: all 0.3s ease;">
                            <div style="font-size: 2rem;">📄</div>
                            <div style="flex: 1;">
                                <h4 style="color: #0a5c36; font-size: 0.95rem; margin-bottom: 3px;">PDF Document</h4>
                                <p style="color: #999; font-size: 0.8rem; margin: 0;">Download or view</p>
                            </div>
                            <i class="fas fa-arrow-right" style="color: #0a5c36;"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php if($material->video_link): ?>
                        <a href="<?php echo e($material->video_link); ?>" target="_blank" 
                           style="display: flex; align-items: center; gap: 12px; padding: 12px; background: rgba(10, 92, 54, 0.05); border-radius: 10px; text-decoration: none; color: inherit; border: 2px solid rgba(10, 92, 54, 0.1); transition: all 0.3s ease;">
                            <div style="font-size: 2rem;">🎥</div>
                            <div style="flex: 1;">
                                <h4 style="color: #0a5c36; font-size: 0.95rem; margin-bottom: 3px;">Video Link</h4>
                                <p style="color: #999; font-size: 0.8rem; margin: 0;">Watch video</p>
                            </div>
                            <i class="fas fa-arrow-right" style="color: #0a5c36;"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tajtrainerV2\resources\views/materials/show.blade.php ENDPATH**/ ?>