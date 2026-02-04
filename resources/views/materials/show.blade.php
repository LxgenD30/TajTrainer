@php
    $currentUser = Auth::user();
    $isStudent = $currentUser->role_id == 2;
@endphp

@extends('layouts.dashboard')

@section('title', $material->title)
@section('user-role', ($isStudent ? 'Student' : 'Teacher') . ' • Material Details')

@section('navigation')
    @if($isStudent)
        @include('partials.student-nav')
    @else
        @include('partials.teacher-nav')
    @endif
@endsection

@section('content')
<style>
    .material-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-top: 20px;
    }
    
    .material-column {
        background: white;
        border-radius: 20px;
        padding: 30px;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);
    }
    
    .column-header {
        border-bottom: 3px solid #0a5c36;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }
    
    .column-header h2 {
        font-family: 'El Messiri', sans-serif;
        font-size: 1.8rem;
        color: #0a5c36;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .resource-item {
        background: #f8f9fa;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    
    .resource-item:hover {
        border-color: #0a5c36;
        box-shadow: 0 4px 12px rgba(10, 92, 54, 0.1);
    }
    
    .resource-type-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .badge-file { background: #e74c3c; color: white; }
    .badge-youtube { background: #ff0000; color: white; }
    .badge-url { background: #3498db; color: white; }
    
    .image-preview {
        width: 100%;
        max-height: 400px;
        object-fit: cover;
        border-radius: 12px;
        margin: 15px 0;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 3px solid #e0e0e0;
    }
    
    .image-preview:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.2);
        border-color: #0a5c36;
    }
    
    .image-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.95);
        align-items: center;
        justify-content: center;
    }
    
    .image-modal.active {
        display: flex;
    }
    
    .modal-content-img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 10px 50px rgba(0,0,0,0.5);
    }
    
    .modal-close {
        position: absolute;
        top: 20px;
        right: 40px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10000;
    }
    
    .modal-close:hover {
        color: #f39c12;
        transform: scale(1.1);
    }
    
    .info-row {
        padding: 15px 0;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 700;
        color: #0a5c36;
        margin-bottom: 5px;
        font-size: 0.95rem;
    }
    
    .info-value {
        color: #666;
        font-size: 1.05rem;
    }
    
    @media (max-width: 992px) {
        .material-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div style="background: linear-gradient(135deg, #0a5c36, #1abc9c); border: 3px solid #2a2a2a; border-radius: 15px; padding: 25px; margin-bottom: 30px; color: white; display: flex; align-items: center; justify-content: space-between;">
    <div>
        <h1 style="margin: 0; font-family: 'El Messiri', sans-serif; font-size: 2.2rem; font-weight: 700; color: white;">
            <i class="fas fa-book-open"></i> {{ $material->title }}
        </h1>
        @if($material->category)
            <div style="margin-top: 10px;">
                <span style="background: rgba(255,255,255,0.2); padding: 6px 15px; border-radius: 20px; font-size: 0.95rem; font-weight: 600;">
                    <i class="fas fa-tag"></i> {{ $material->category }}
                </span>
            </div>
        @endif
    </div>
    <a href="{{ route('materials.index') }}" 
        style="display: inline-flex; align-items: center; gap: 10px; color: #1a1a1a; text-decoration: none; font-family: 'Cairo', sans-serif; font-weight: 700; font-size: 1rem; padding: 12px 24px; border-radius: 50px; background: #d4af37; border: 3px solid #b8860b; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2); white-space: nowrap;"
        onmouseover="this.style.background='#ffcc33'; this.style.transform='translateY(-2px)';"
        onmouseout="this.style.background='#d4af37'; this.style.transform='translateY(0)';">
        <i class="fas fa-arrow-left"></i> Back to Materials
    </a>
</div>

<div class="material-container">
    <div class="material-column">
        <div class="column-header">
            <h2><i class="fas fa-layer-group"></i> Material Resources</h2>
            <p style="margin: 5px 0 0 0; color: #666; font-size: 1rem;">
                {{ $material->items->count() }} item{{ $material->items->count() != 1 ? 's' : '' }} available
            </p>
        </div>
        
        @if($material->items && $material->items->count() > 0)
            @foreach($material->items as $item)
                <div class="resource-item">
                    <span class="resource-type-badge badge-{{ $item->type }}">
                        @if($item->type === 'file')
                            <i class="fas fa-file"></i> FILE
                        @elseif($item->type === 'image')
                            <i class="fas fa-image"></i> IMAGE
                        @elseif($item->type === 'youtube')
                            <i class="fab fa-youtube"></i> YOUTUBE
                        @else
                            <i class="fas fa-link"></i> LINK
                        @endif
                    </span>
                    
                    <h3 style="margin: 10px 0; font-size: 1.3rem; color: #2a2a2a; font-weight: 700;">
                        {{ $item->title ?: 'Resource Item' }}
                    </h3>
                    
                    @if($item->description)
                        <p style="color: #666; margin: 10px 0; line-height: 1.6;">
                            {{ $item->description }}
                        </p>
                    @endif
                    
                    @if($item->type === 'file' || $item->type === 'image')
                        @php
                            $extension = strtolower(pathinfo($item->path, PATHINFO_EXTENSION));
                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                            // Check if it's an image type OR has image extension
                            $isImage = $item->type === 'image' || in_array($extension, $imageExtensions);
                            
                            // Debug logging
                            \Log::info('Material Item Display Debug', [
                                'item_id' => $item->item_id,
                                'type' => $item->type,
                                'path' => $item->path,
                                'extension' => $extension,
                                'isImage' => $isImage,
                                'storage_url' => Storage::url($item->path),
                            ]);
                        @endphp
                        
                        @if($isImage)
                            <!-- Image Thumbnail Preview -->
                            <div style="margin: 15px 0;">
                                <img src="{{ Storage::url($item->path) }}" 
                                     alt="{{ $item->title ?: 'Image' }}"
                                     class="image-preview"
                                     onclick="openImageModal('{{ Storage::url($item->path) }}')"
                                     loading="lazy"
                                     onerror="console.error('Failed to load image:', '{{ Storage::url($item->path) }}')">
                            </div>
                        @else
                            <!-- Debug: Show it's a document -->
                            <div style="padding: 10px; background: #f0f0f0; border-radius: 5px; margin: 10px 0;">
                                <small style="color: #666;">
                                    <i class="fas fa-file"></i> Document file: {{ $extension }}
                                </small>
                            </div>
                        @endif
                    @endif
                    
                    <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                        @if($item->type === 'file' || $item->type === 'image')
                            @php
                                $extension = strtolower(pathinfo($item->path, PATHINFO_EXTENSION));
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                $isImage = $item->type === 'image' || in_array($extension, $imageExtensions);
                            @endphp
                            
                            @if($isImage)
                                <!-- Preview Button for Images -->
                                <button onclick="openImageModal('{{ Storage::url($item->path) }}')"
                                        style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white; border-radius: 10px; border: 2px solid #6c3483; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(155, 89, 182, 0.3);"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(155, 89, 182, 0.4)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(155, 89, 182, 0.3)';">
                                    <i class="fas fa-search-plus"></i> Preview Image
                                </button>
                                
                                <!-- Download Button for Images -->
                                <a href="{{ Storage::url($item->path) }}" 
                                   download
                                   style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #27ae60, #229954); color: white; border-radius: 10px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; border: 2px solid #1e8449; box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);"
                                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(39, 174, 96, 0.4)';"
                                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(39, 174, 96, 0.3)';">
                                    <i class="fas fa-download"></i> Download Image
                                </a>
                            @else
                                <!-- Download Button for Files -->
                                <a href="{{ Storage::url($item->path) }}" 
                                   download
                                   style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #27ae60, #229954); color: white; border-radius: 10px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; border: 2px solid #1e8449; box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);"
                                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(39, 174, 96, 0.4)';"
                                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(39, 174, 96, 0.3)';">
                                    <i class="fas fa-download"></i> Download File
                                </a>
                                
                                @if(str_ends_with(strtolower($item->path), '.pdf'))
                                    <a href="{{ Storage::url($item->path) }}" 
                                       target="_blank"
                                       style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #3498db, #2980b9); color: white; border-radius: 10px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; border: 2px solid #1f5f8b; box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);"
                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(52, 152, 219, 0.4)';"
                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(52, 152, 219, 0.3)';">
                                        <i class="fas fa-eye"></i> View PDF
                                    </a>
                                @endif
                            @endif
                        @elseif($item->type === 'youtube')
                            <a href="{{ $item->path }}" 
                               target="_blank"
                               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: linear-gradient(135deg, #ff0000, #cc0000); color: white; border-radius: 8px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; border: 2px solid #990000;">
                                <i class="fab fa-youtube"></i> Watch Video
                            </a>
                        @else
                            <a href="{{ $item->path }}" 
                               target="_blank"
                               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: linear-gradient(135deg, #3498db, #2980b9); color: white; border-radius: 8px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; border: 2px solid #1f5f8b;">
                                <i class="fas fa-external-link-alt"></i> Open Link
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                <p style="font-size: 1.1rem;">No resources available</p>
            </div>
        @endif
    </div>
    
    <div class="material-column">
        <div class="column-header">
            <h2><i class="fas fa-info-circle"></i> Material Information</h2>
        </div>
        
        <div class="info-row">
            <div class="info-label"><i class="fas fa-align-left"></i> Description</div>
            <div class="info-value">{{ $material->description ?: 'No description provided' }}</div>
        </div>
        
        @if($material->category)
            <div class="info-row">
                <div class="info-label"><i class="fas fa-tag"></i> Category</div>
                <div class="info-value">
                    <span style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 6px 15px; border-radius: 20px; font-weight: 700;">
                        {{ $material->category }}
                    </span>
                </div>
            </div>
        @endif
        
        <div class="info-row">
            <div class="info-label"><i class="fas fa-user"></i> Uploaded By</div>
            <div class="info-value">
                <div style="display: flex; align-items: center; gap: 10px;">
                    @if($material->teacher && $material->teacher->user)
                        @if($material->teacher->user->profile_picture)
                            <img src="{{ Storage::url($material->teacher->user->profile_picture) }}" 
                                 alt="{{ $material->teacher->user->name }}"
                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #0a5c36;">
                        @else
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #0a5c36, #1abc9c); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem;">
                                {{ strtoupper(substr($material->teacher->user->name, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <div style="font-weight: 600; color: #2a2a2a;">{{ $material->teacher->user->name }}</div>
                            <div style="font-size: 0.85rem; color: #999;">Teacher</div>
                        </div>
                    @else
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #999, #666); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem;">
                            U
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #2a2a2a;">Unknown</div>
                            <div style="font-size: 0.85rem; color: #999;">Teacher</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-label"><i class="fas fa-calendar"></i> Upload Date</div>
            <div class="info-value">{{ $material->created_at->format('F d, Y') }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label"><i class="fas fa-clock"></i> Last Updated</div>
            <div class="info-value">{{ $material->updated_at->diffForHumans() }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label"><i class="fas fa-eye"></i> Visibility</div>
            <div class="info-value">
                @if($material->is_public)
                    <span style="background: rgba(26, 188, 156, 0.15); color: #1abc9c; padding: 6px 15px; border-radius: 20px; font-weight: 700;">
                        <i class="fas fa-globe"></i> Public
                    </span>
                @else
                    <span style="background: rgba(231, 76, 60, 0.15); color: #e74c3c; padding: 6px 15px; border-radius: 20px; font-weight: 700;">
                        <i class="fas fa-lock"></i> Private
                    </span>
                @endif
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-label"><i class="fas fa-layer-group"></i> Total Resources</div>
            <div class="info-value">
                <span style="font-size: 1.5rem; font-weight: 700; color: #0a5c36;">
                    {{ $material->items->count() }}
                </span>
                <span style="color: #999; font-size: 0.95rem;">
                    item{{ $material->items->count() != 1 ? 's' : '' }}
                </span>
            </div>
        </div>
        
        @if(!$isStudent && auth()->user()->teacher && $material->teacher_id == auth()->user()->teacher->id)
            <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
                <div class="info-label" style="margin-bottom: 15px;"><i class="fas fa-cog"></i> Actions</div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('materials.edit', $material->material_id) }}" 
                       style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #f39c12, #e67e22); color: white; border-radius: 10px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; border: 2px solid #d68910;">
                        <i class="fas fa-edit"></i> Edit Material
                    </a>
                    <form action="{{ route('materials.destroy', $material->material_id) }}" method="POST" onsubmit="return confirm('Delete this material and all its resources?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: 2px solid #a93226; border-radius: 10px; font-weight: 700; cursor: pointer; transition: all 0.3s ease;">
                            <i class="fas fa-trash"></i> Delete Material
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
    <span class="modal-close" onclick="closeImageModal()">&times;</span>
    <img id="modalImage" class="modal-content-img" src="" alt="Full size image">
</div>

<script>
function openImageModal(imageUrl) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    modal.classList.add('active');
    modalImg.src = imageUrl;
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endsection
