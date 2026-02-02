@extends('layouts.template')

@section('page-title', 'Edit Material')
@section('page-subtitle', 'Update Material Details')

@section('content')
<style>
    .form-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
        max-width: 900px;
        margin: 0 auto;
    }
    
    .form-header {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 3px solid #f5f5dc;
    }
    
    .form-header h2 {
        font-size: 2rem;
        color: #0a5c36;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-label {
        display: block;
        color: #0a5c36;
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }
    
    .form-input,
    .form-textarea {
        width: 100%;
        padding: 15px;
        background: rgba(10, 92, 54, 0.05);
        color: #333;
        border: 2px solid rgba(10, 92, 54, 0.2);
        border-radius: 12px;
        font-family: 'El Messiri', sans-serif;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #0a5c36;
        background: rgba(10, 92, 54, 0.08);
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    .form-hint {
        display: block;
        color: #666;
        font-size: 0.9rem;
        margin-top: 6px;
    }
    
    .current-resources-box {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.1), rgba(39, 174, 96, 0.05));
        padding: 20px;
        border-radius: 15px;
        border: 2px solid #27ae60;
        margin-bottom: 20px;
    }
    
    .current-resources-title {
        color: #27ae60;
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .current-item {
        margin-bottom: 12px;
        color: #333;
        padding: 10px;
        background: white;
        border-radius: 8px;
    }
    
    .current-thumbnail {
        max-width: 300px;
        max-height: 200px;
        border-radius: 10px;
        margin-top: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .resources-section {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(212, 175, 55, 0.05));
        padding: 25px;
        border-radius: 15px;
        border: 2px solid #d4af37;
        margin-bottom: 25px;
    }
    
    .section-title-resources {
        color: #d4af37;
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .preview-box {
        margin-top: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        text-align: center;
        border: 2px dashed #0a5c36;
    }
    
    .preview-box img {
        max-width: 100%;
        max-height: 250px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .preview-label {
        color: #0a5c36;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .checkbox-container {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        border: 2px solid rgba(10, 92, 54, 0.2);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .checkbox-container:hover {
        background: rgba(10, 92, 54, 0.1);
        border-color: #0a5c36;
    }
    
    .checkbox-container input[type="checkbox"] {
        width: 24px;
        height: 24px;
        cursor: pointer;
    }
    
    .checkbox-label {
        color: #0a5c36;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .button-group {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    
    .btn-submit {
        flex: 1;
        padding: 15px 30px;
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
        color: white;
        border: none;
        border-radius: 25px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        background: linear-gradient(135deg, #064e32, #0a5c36);
        transform: scale(1.02);
    }
    
    .btn-cancel {
        flex: 1;
        padding: 15px 30px;
        background: transparent;
        color: #e74c3c;
        border: 2px solid #e74c3c;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-cancel:hover {
        background: rgba(231, 76, 60, 0.1);
    }
</style>

<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-edit"></i> Edit Material</h2>
    </div>

    <form action="{{ route('materials.update', $material->material_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div class="form-group">
            <label class="form-label">📝 Material Title *</label>
            <input type="text" name="title" value="{{ old('title', $material->title) }}" required class="form-input" placeholder="Enter a descriptive title for this material">
            @error('title')
                <span style="color: #e74c3c; font-size: 0.9rem; display: block; margin-top: 5px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Description -->
        <div class="form-group">
            <label class="form-label">📄 Description</label>
            <textarea name="description" class="form-textarea" placeholder="Describe this material and its learning objectives">{{ old('description', $material->description) }}</textarea>
            <span class="form-hint">Provide helpful context for students using this material</span>
            @error('description')
                <span style="color: #e74c3c; font-size: 0.9rem; display: block; margin-top: 5px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Current Resources -->
        @if($material->file_path || $material->video_link || $material->thumbnail)
            <div class="current-resources-box">
                <h4 class="current-resources-title">
                    <i class="fas fa-check-circle"></i> Current Resources
                </h4>
                
                @if($material->thumbnail)
                    <div class="current-item">
                        <strong>📸 Current Thumbnail:</strong>
                        <img src="{{ filter_var($material->thumbnail, FILTER_VALIDATE_URL) ? $material->thumbnail : Storage::url($material->thumbnail) }}" 
                             alt="Current thumbnail" 
                             class="current-thumbnail">
                    </div>
                @endif
                
                @if($material->file_path)
                    <div class="current-item">
                        <strong>📄 File:</strong> 
                        <a href="{{ Storage::url($material->file_path) }}" target="_blank" style="color: #0a5c36; text-decoration: underline;">View current file</a>
                    </div>
                @endif
                
                @if($material->video_link)
                    <div class="current-item">
                        <strong>🎥 Video:</strong> 
                        <a href="{{ $material->video_link }}" target="_blank" style="color: #0a5c36; text-decoration: underline; word-break: break-all;">{{ Str::limit($material->video_link, 80) }}</a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Resources Section -->
        <div class="resources-section">
            <h3 class="section-title-resources">
                <i class="fas fa-folder-open"></i> Update Resources
            </h3>

            <!-- Video Link -->
            <div class="form-group">
                <label class="form-label">🎥 Video Link</label>
                <input type="url" name="video_link" id="videoLinkInput" value="{{ old('video_link', $material->video_link) }}" class="form-input" placeholder="https://youtube.com/watch?v=... or any video URL" oninput="previewVideoThumbnail()">
                <span class="form-hint">YouTube links will automatically extract thumbnails</span>
                @error('video_link')
                    <span style="color: #e74c3c; font-size: 0.9rem; display: block; margin-top: 5px;">{{ $message }}</span>
                @enderror
                
                <!-- Video Thumbnail Preview -->
                <div id="videoThumbnailPreview" style="display: none;" class="preview-box">
                    <p class="preview-label">🎬 Video Thumbnail Preview:</p>
                    <img id="videoPreviewImage" src="" alt="Video Preview">
                </div>
            </div>

            <!-- File Upload -->
            <div class="form-group">
                <label class="form-label">📄 File {{ $material->file_path ? '(Replace Current)' : '' }}</label>
                <input type="file" name="file" accept=".pdf,.doc,.docx,.mp3,.mp4" id="materialFile" class="form-input" style="padding: 12px;">
                <span class="form-hint">{{ $material->file_path ? 'Upload new file to replace existing. Leave empty to keep current.' : 'Supported: PDF, DOC, DOCX, MP3, MP4 (Max 20MB)' }}</span>
                @error('file')
                    <span style="color: #e74c3c; font-size: 0.9rem; display: block; margin-top: 5px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Thumbnail Upload -->
            <div class="form-group">
                <label class="form-label">🖼️ Thumbnail {{ $material->thumbnail ? '(Replace Current)' : '(Optional)' }}</label>
                <input type="file" name="thumbnail" accept="image/*" id="thumbnailFile" class="form-input" style="padding: 12px;" onchange="previewThumbnail(event)">
                <span class="form-hint">{{ $material->thumbnail ? 'Upload new image to replace existing. Leave empty to keep current.' : 'JPG, PNG, or GIF (Max 2MB)' }}</span>
                @error('thumbnail')
                    <span style="color: #e74c3c; font-size: 0.9rem; display: block; margin-top: 5px;">{{ $message }}</span>
                @enderror
                
                <!-- Thumbnail Preview -->
                <div id="thumbnailPreview" style="display: none;" class="preview-box">
                    <p class="preview-label">New Thumbnail Preview:</p>
                    <img id="previewImage" src="" alt="Preview">
                </div>
            </div>
        </div>

        <!-- Visibility -->
        <div class="form-group">
            <label class="checkbox-container">
                <input type="checkbox" name="is_public" value="1" {{ old('is_public', $material->is_public) ? 'checked' : '' }}>
                <span class="checkbox-label">
                    🌐 Make this material publicly available to all students
                </span>
            </label>
            <span class="form-hint" style="margin-left: 36px;">Uncheck to restrict access to specific classes only</span>
        </div>

        <!-- Submit Buttons -->
        <div class="button-group">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Update Material
            </button>
            <a href="{{ route('materials.show', $material->material_id) }}" class="btn-cancel">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
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
    
    // Check on page load if there's a YouTube link
    document.addEventListener('DOMContentLoaded', function() {
        previewVideoThumbnail();
    });
</script>
@endsection
