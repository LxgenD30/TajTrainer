

<?php $__env->startSection('page-title', 'Add New Material'); ?>
<?php $__env->startSection('page-subtitle', 'Create educational resource'); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">➕ Add New Material</h3>
        </div>

        <div class="card-body">
            <form action="<?php echo e(route('materials.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <!-- Title -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Title *</label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required
                        style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;"
                        placeholder="Enter material title">
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Description -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Description</label>
                    <textarea name="description" rows="4"
                        style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem; resize: vertical;"
                        placeholder="Describe this material and its learning objectives"><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Resources Section -->
                <div style="background: rgba(227, 216, 136, 0.05); padding: 20px; border-radius: 12px; border: 2px solid var(--color-dark-green); margin-bottom: 20px;">
                    <h4 style="color: var(--color-gold); margin: 0 0 15px 0; font-size: 1.1rem;">📁 Resources</h4>

                    <!-- Video Link -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-light); font-weight: 600; margin-bottom: 8px;">🎥 Video Link</label>
                        <input type="url" name="video_link" id="videoLinkInput" value="<?php echo e(old('video_link')); ?>"
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;"
                            placeholder="https://youtube.com/watch?v=... or any video URL"
                            oninput="previewVideoThumbnail()">
                        <small style="color: var(--color-light); opacity: 0.7; display: block; margin-top: 5px;">YouTube links will automatically extract thumbnails</small>
                        <?php $__errorArgs = ['video_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <!-- Video Thumbnail Preview -->
                        <div id="videoThumbnailPreview" style="display: none; margin-top: 15px; padding: 15px; background: rgba(31, 39, 27, 0.5); border-radius: 8px; text-align: center;">
                            <p style="color: var(--color-light); margin: 0 0 10px 0; font-weight: 600;">🎬 Video Thumbnail Preview:</p>
                            <img id="videoPreviewImage" src="" alt="Video Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-light); font-weight: 600; margin-bottom: 8px;">📄 Upload File</label>
                        <input type="file" name="file" accept=".pdf,.doc,.docx,.mp3,.mp4" id="materialFile"
                            style="width: 100%; padding: 10px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <small style="color: var(--color-light); opacity: 0.7; display: block; margin-top: 5px;">PDF, DOC, DOCX, MP3, MP4. Max size: 20MB</small>
                        <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Thumbnail Upload -->
                    <div>
                        <label style="display: block; color: var(--color-light); font-weight: 600; margin-bottom: 8px;">🖼️ Thumbnail (Optional)</label>
                        <input type="file" name="thumbnail" accept="image/*" id="thumbnailFile" onchange="previewThumbnail(event)"
                            style="width: 100%; padding: 10px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <small style="color: var(--color-light); opacity: 0.7; display: block; margin-top: 5px;">JPG, PNG, or GIF. Max size: 2MB</small>
                        <?php $__errorArgs = ['thumbnail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <!-- Thumbnail Preview -->
                        <div id="thumbnailPreview" style="display: none; margin-top: 15px; padding: 15px; background: rgba(31, 39, 27, 0.5); border-radius: 8px; text-align: center;">
                            <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                        </div>
                    </div>
                </div>

                <!-- Visibility -->
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="is_public" value="1" <?php echo e(old('is_public', true) ? 'checked' : ''); ?>

                            style="width: 20px; height: 20px; cursor: pointer;">
                        <span style="color: var(--color-light); font-weight: 600;">
                            🌐 Make this material publicly available to all students
                        </span>
                    </label>
                    <small style="color: var(--color-light); opacity: 0.7; display: block; margin-top: 5px; margin-left: 30px;">
                        Uncheck to restrict access to specific classes only
                    </small>
                </div>

                <!-- Submit Buttons -->
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" 
                        style="flex: 1; padding: 12px 30px; background: var(--color-gold); color: var(--color-dark); border: none; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif; font-size: 1rem;"
                        onmouseover="this.style.opacity='0.8'"
                        onmouseout="this.style.opacity='1'">
                        ✅ Create Material
                    </button>
                    <a href="<?php echo e(route('materials.index')); ?>" 
                        style="flex: 1; padding: 12px 30px; background: transparent; color: var(--color-light-green); border: 2px solid var(--color-light-green); border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; text-align: center; display: flex; align-items: center; justify-content: center;"
                        onmouseover="this.style.background='rgba(226, 241, 175, 0.1)'"
                        onmouseout="this.style.background='transparent'">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewThumbnail(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('thumbnailPreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
        
        function previewVideoThumbnail() {
            const videoLink = document.getElementById('videoLinkInput').value;
            const previewDiv = document.getElementById('videoThumbnailPreview');
            const previewImg = document.getElementById('videoPreviewImage');
            
            // Check if it's a YouTube link
            const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
            const match = videoLink.match(youtubeRegex);
            
            if (match && match[1]) {
                const videoId = match[1];
                previewImg.src = 'https://img.youtube.com/vi/' + videoId + '/maxresdefault.jpg';
                previewDiv.style.display = 'block';
            } else {
                previewDiv.style.display = 'none';
            }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tajtrainerV2\resources\views/materials/create.blade.php ENDPATH**/ ?>