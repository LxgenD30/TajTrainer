@extends('layouts.dashboard')

@section('title', 'Create Material')
@section('user-role', 'Teacher • Add New Material')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
    <!-- Back Button -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('materials.index') }}" 
            style="display: inline-flex; align-items: center; gap: 10px; color: #1a1a1a; text-decoration: none; font-family: 'El Messiri', sans-serif; font-weight: 600; font-size: 1rem; padding: 8px 16px; border-radius: 50px; background: rgba(255, 255, 255, 0.9); border: 3px solid #2a2a2a; transition: all 0.3s ease;"
            onmouseover="this.style.background='rgba(10, 92, 54, 0.1)'; this.style.transform='translateX(-5px)'; this.style.borderColor='#0a5c36'"
            onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.transform='translateX(0)'; this.style.borderColor='#2a2a2a'">
            <i class="fas fa-arrow-left"></i> Back to Materials
        </a>
    </div>

    <!-- Page Header -->
    <div style="background: white; border-radius: 25px; padding: 30px; margin-bottom: 30px; border: 3px solid #2a2a2a; box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);">
        <h1 style="margin: 0 0 10px 0; font-family: 'El Messiri', serif; font-size: 2.5rem; color: #0a5c36; font-weight: 700;">
            <i class="fas fa-plus-circle"></i> Create New Material
        </h1>
        <p style="margin: 0; font-size: 1.1rem; color: #666; font-family: 'Cairo', sans-serif;">
            Upload documents, videos, or audio files for your students
        </p>
    </div>

    <!-- Form Card -->
    <div style="background: white; border-radius: 25px; padding: 40px; border: 3px solid #e0e0e0; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);">
        <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Title -->
            <div style="margin-bottom: 25px;">
                <label for="title" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1a1a1a; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    <i class="fas fa-heading"></i> Material Title *
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title') }}"
                       required
                       style="width: 100%; padding: 12px 15px; border: 3px solid #e0e0e0; border-radius: 12px; font-size: 1rem; font-family: 'Cairo', sans-serif; transition: all 0.3s ease;"
                       onfocus="this.style.borderColor='#0a5c36'; this.style.boxShadow='0 0 0 4px rgba(10, 92, 54, 0.1)'"
                       onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'"
                       placeholder="e.g., Introduction to Tajweed Rules">
                @error('title')
                    <p style="margin: 5px 0 0 0; color: #e74c3c; font-size: 0.9rem; font-family: 'Cairo', sans-serif;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div style="margin-bottom: 25px;">
                <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1a1a1a; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    <i class="fas fa-align-left"></i> Description
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4"
                          style="width: 100%; padding: 12px 15px; border: 3px solid #e0e0e0; border-radius: 12px; font-size: 1rem; font-family: 'Cairo', sans-serif; transition: all 0.3s ease; resize: vertical;"
                          onfocus="this.style.borderColor='#0a5c36'; this.style.boxShadow='0 0 0 4px rgba(10, 92, 54, 0.1)'"
                          onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'"
                          placeholder="Describe what students will learn from this material">{{ old('description') }}</textarea>
                @error('description')
                    <p style="margin: 5px 0 0 0; color: #e74c3c; font-size: 0.9rem; font-family: 'Cairo', sans-serif;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
                <!-- Video Link -->
                <div>
                    <label for="video_link" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1a1a1a; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                        <i class="fas fa-video"></i> Video Link (YouTube)
                    </label>
                    <input type="url" 
                           name="video_link" 
                           id="video_link" 
                           value="{{ old('video_link') }}"
                           style="width: 100%; padding: 12px 15px; border: 3px solid #e0e0e0; border-radius: 12px; font-size: 1rem; font-family: 'Cairo', sans-serif; transition: all 0.3s ease;"
                           onfocus="this.style.borderColor='#0a5c36'; this.style.boxShadow='0 0 0 4px rgba(10, 92, 54, 0.1)'"
                           onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'"
                           placeholder="https://www.youtube.com/watch?v=...">
                    @error('video_link')
                        <p style="margin: 5px 0 0 0; color: #e74c3c; font-size: 0.9rem; font-family: 'Cairo', sans-serif;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Public Checkbox -->
                <div style="display: flex; align-items: center; margin-top: 35px;">
                    <input type="checkbox" 
                           name="is_public" 
                           id="is_public" 
                           value="1"
                           {{ old('is_public') ? 'checked' : '' }}
                           style="width: 20px; height: 20px; margin-right: 10px; cursor: pointer;">
                    <label for="is_public" style="font-weight: 600; color: #1a1a1a; font-family: 'Cairo', sans-serif; font-size: 1rem; cursor: pointer;">
                        <i class="fas fa-globe"></i> Make this material public
                    </label>
                </div>
            </div>

            <!-- File Upload -->
            <div style="margin-bottom: 25px;">
                <label for="file" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1a1a1a; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    <i class="fas fa-file-upload"></i> Upload File (PDF, DOC, MP3, MP4)
                </label>
                <input type="file" 
                       name="file" 
                       id="file"
                       accept=".pdf,.doc,.docx,.mp3,.mp4"
                       style="width: 100%; padding: 12px; border: 3px solid #e0e0e0; border-radius: 12px; font-family: 'Cairo', sans-serif; background: white; transition: all 0.3s ease;"
                       onfocus="this.style.borderColor='#0a5c36'"
                       onblur="this.style.borderColor='#e0e0e0'">
                <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: #666; font-family: 'Cairo', sans-serif;">
                    <i class="fas fa-info-circle"></i> Max file size: 20MB
                </p>
                @error('file')
                    <p style="margin: 5px 0 0 0; color: #e74c3c; font-size: 0.9rem; font-family: 'Cairo', sans-serif;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Thumbnail Upload -->
            <div style="margin-bottom: 30px;">
                <label for="thumbnail" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1a1a1a; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                    <i class="fas fa-image"></i> Thumbnail Image (Optional)
                </label>
                <input type="file" 
                       name="thumbnail" 
                       id="thumbnail"
                       accept="image/jpeg,image/png,image/jpg,image/gif"
                       style="width: 100%; padding: 12px; border: 3px solid #e0e0e0; border-radius: 12px; font-family: 'Cairo', sans-serif; background: white; transition: all 0.3s ease;"
                       onfocus="this.style.borderColor='#0a5c36'"
                       onblur="this.style.borderColor='#e0e0e0'">
                <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: #666; font-family: 'Cairo', sans-serif;">
                    <i class="fas fa-info-circle"></i> YouTube videos will auto-generate thumbnails if not provided
                </p>
                @error('thumbnail')
                    <p style="margin: 5px 0 0 0; color: #e74c3c; font-size: 0.9rem; font-family: 'Cairo', sans-serif;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 15px; padding-top: 20px; border-top: 3px solid #f5f5f5;">
                <button type="submit" 
                        style="flex: 1; padding: 15px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border: none; border-radius: 15px; font-weight: 700; font-size: 1.1rem; font-family: 'Cairo', sans-serif; cursor: pointer; transition: all 0.3s ease;"
                        onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 5px 20px rgba(10, 92, 54, 0.4)'"
                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'">
                    <i class="fas fa-check"></i> Create Material
                </button>
                <a href="{{ route('materials.index') }}" 
                   style="flex: 1; padding: 15px; background: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 3px solid #e74c3c; border-radius: 15px; font-weight: 700; font-size: 1.1rem; font-family: 'Cairo', sans-serif; text-decoration: none; text-align: center; transition: all 0.3s ease;"
                   onmouseover="this.style.background='rgba(231, 76, 60, 0.2)'"
                   onmouseout="this.style.background='rgba(231, 76, 60, 0.1)'">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
