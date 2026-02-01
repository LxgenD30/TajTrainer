@extends('layouts.template')

@section('page-title', 'Edit Material')
@section('page-subtitle', 'Update material details')

@section('content')
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">✏️ Edit Material</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('materials.update', $material->material_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Title *</label>
                    <input type="text" name="title" value="{{ old('title', $material->title) }}" required
                        style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;"
                        placeholder="Enter material title">
                    @error('title')
                        <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">Description</label>
                    <textarea name="description" rows="4"
                        style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem; resize: vertical;"
                        placeholder="Describe this material and its learning objectives">{{ old('description', $material->description) }}</textarea>
                    @error('description')
                        <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Current Resources -->
                @if($material->file_path || $material->video_link || $material->thumbnail)
                    <div style="background: rgba(77, 139, 49, 0.1); padding: 20px; border-radius: 12px; border: 2px solid var(--color-light-green); margin-bottom: 20px;">
                        <h4 style="color: var(--color-light-green); margin: 0 0 15px 0; font-size: 1.1rem;">📋 Current Resources</h4>
                        
                        @if($material->thumbnail)
                            <div style="margin-bottom: 15px;">
                                <label style="color: var(--color-light); font-weight: 600; display: block; margin-bottom: 8px;">Current Thumbnail:</label>
                                @if(filter_var($material->thumbnail, FILTER_VALIDATE_URL))
                                    <img src="{{ $material->thumbnail }}" alt="Current thumbnail" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                                @else
                                    <img src="{{ Storage::url($material->thumbnail) }}" alt="Current thumbnail" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                                @endif
                            </div>
                        @endif
                        
                        @if($material->file_path)
                            <div style="margin-bottom: 10px; color: var(--color-light);">
                                <span style="font-weight: 600;">📄 File:</span> 
                                <a href="{{ Storage::url($material->file_path) }}" target="_blank" style="color: var(--color-gold); text-decoration: underline;">View current file</a>
                            </div>
                        @endif
                        
                        @if($material->video_link)
                            <div style="color: var(--color-light);">
                                <span style="font-weight: 600;">🎥 Video:</span> 
                                <a href="{{ $material->video_link }}" target="_blank" style="color: var(--color-gold); text-decoration: underline; word-break: break-all;">{{ Str::limit($material->video_link, 60) }}</a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Resources Section -->
                <div style="background: rgba(227, 216, 136, 0.05); padding: 20px; border-radius: 12px; border: 2px solid var(--color-dark-green); margin-bottom: 20px;">
                    <h4 style="color: var(--color-gold); margin: 0 0 15px 0; font-size: 1.1rem;">📁 Update Resources</h4>

                    <!-- Video Link -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-light); font-weight: 600; margin-bottom: 8px;">🎥 Video Link</label>
                        <input type="url" name="video_link" id="videoLinkInput" value="{{ old('video_link', $material->video_link) }}"
                            style="width: 100%; padding: 12px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;"
                            placeholder="https://youtube.com/watch?v=... or any video URL"
                            oninput="previewVideoThumbnail()">
                        <small style="color: var(--color-light); opacity: 0.7; display: block; margin-top: 5px;">YouTube links will automatically extract thumbnails</small>
                        @error('video_link')
                            <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                        
                        <!-- Video Thumbnail Preview -->
                        <div id="videoThumbnailPreview" style="display: none; margin-top: 15px; padding: 15px; background: rgba(31, 39, 27, 0.5); border-radius: 8px; text-align: center;">
                            <p style="color: var(--color-light); margin: 0 0 10px 0; font-weight: 600;">🎬 Video Thumbnail Preview:</p>
                            <img id="videoPreviewImage" src="" alt="Video Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--color-light); font-weight: 600; margin-bottom: 8px;">📄 File {{ $material->file_path ? '(Replace)' : '' }}</label>
                        <input type="file" name="file" accept=".pdf,.doc,.docx,.mp3,.mp4" id="materialFile"
                            style="width: 100%; padding: 10px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <small style="color: var(--color-light); opacity: 0.7; display: block; margin-top: 5px;">
                            {{ $material->file_path ? 'Upload new file to replace existing. Leave empty to keep current.' : 'Max size: 20MB' }}
                        </small>
                        @error('file')
                            <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Thumbnail Upload -->
                    <div id="thumbnailSection">
                        <label style="display: block; color: var(--color-light); font-weight: 600; margin-bottom: 8px;">🖼️ Thumbnail {{ $material->thumbnail ? '(Replace)' : '(Optional)' }}</label>
                        <input type="file" name="thumbnail" accept="image/*" id="thumbnailFile" onchange="previewThumbnail(event)"
                            style="width: 100%; padding: 10px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <small style="color: var(--color-light); opacity: 0.7; display: block; margin-top: 5px;">
                            {{ $material->thumbnail ? 'Upload new image to replace existing. Leave empty to keep current.' : 'JPG, PNG, or GIF. Max size: 2MB' }}
                        </small>
                        @error('thumbnail')
                            <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                        
                        <!-- Thumbnail Preview -->
                        <div id="thumbnailPreview" style="display: none; margin-top: 15px; padding: 15px; background: rgba(31, 39, 27, 0.5); border-radius: 8px; text-align: center;">
                            <p style="color: var(--color-light); margin: 0 0 10px 0; font-weight: 600;">New Thumbnail Preview:</p>
                            <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                        </div>
                    </div>
                </div>

                <!-- Visibility -->
                <div style="margin-bottom: 25px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="is_public" value="1" {{ old('is_public', $material->is_public) ? 'checked' : '' }}
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
                        ✅ Update Material
                    </button>
                    <a href="{{ route('materials.show', $material->material_id) }}" 
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
            const youtubeRegex = /(?:youtube\\.com\\/(?:[^\\/]+\\/.+\\/|(?:v|e(?:mbed)?)\\/|.*[?&]v=)|youtu\\.be\\/)([^"&?\\/\\s]{11})/;
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
