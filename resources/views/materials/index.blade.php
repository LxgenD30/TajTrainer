@php
    $currentUser = Auth::user();
    $isStudent = $currentUser->role_id == 2; // Role 2 = Student, Role 3 = Teacher
    $layout = $isStudent ? 'layouts.dashboard' : 'layouts.template';
@endphp

@extends($layout)

@section('title', 'Learning Materials')
@if($isStudent)
    @section('user-role', 'Student • Learning Resources')
@else
    @section('page-title', 'Materials')
    @section('page-subtitle', 'Manage Learning Resources')
@endif

@if($isStudent)
@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="nav-item">
        <i class="fas fa-home nav-icon"></i>
        <span class="nav-label">Dashboard</span>
    </a>
    
    <a href="{{ route('student.classes') }}" class="nav-item">
        <i class="fas fa-users nav-icon"></i>
        <span class="nav-label">My Classes</span>
    </a>
    
    <a href="{{ route('student.practice') }}" class="nav-item">
        <i class="fas fa-microphone-alt nav-icon"></i>
        <span class="nav-label">Practice</span>
    </a>
    
    <a href="{{ route('student.progress') }}" class="nav-item">
        <i class="fas fa-chart-line nav-icon"></i>
        <span class="nav-label">My Progress</span>
    </a>
    
    <a href="{{ route('student.materials') }}" class="nav-item active">
        <i class="fas fa-book-open nav-icon"></i>
        <span class="nav-label">Materials</span>
    </a>
@endsection
@endif

@section('content')
<!-- MATERIALS INDEX VIEW v2.0 - Updated Feb 2026 -->
<style>
    .modern-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);
        transition: all 0.3s ease;
        border: 3px solid #2a2a2a;
    }
    
    .modern-card:hover {
        box-shadow: 0 15px 40px rgba(10, 92, 54, 0.15);
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 3px solid #f5f5dc;
    }
    
    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .icon-badge {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }
    
    .section-title {
        font-size: 1.8rem;
        color: #0a5c36;
        font-weight: 700;
    }
    
    .btn-create {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
        color: white;
        padding: 12px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-create:hover {
        background: linear-gradient(135deg, #064e32, #0a5c36);
        transform: scale(1.05);
    }
    
    .materials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }
    
    .material-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(10, 92, 54, 0.1);
        transition: all 0.3s ease;
        border: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .material-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.2);
        border-color: #0a5c36;
    }
    
    .material-thumbnail {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
    }
    
    .material-icon {
        padding: 30px;
        text-align: center;
        font-size: 3rem;
        color: white;
    }
    
    .material-icon.pdf {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
    }
    
    .material-icon.video {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }
    
    .material-icon.audio {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
    }
    
    .material-icon.document {
        background: linear-gradient(135deg, #16a085, #1abc9c);
    }
    
    .material-body {
        padding: 20px;
    }
    
    .material-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #0a5c36;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .material-desc {
        color: #666;
        font-size: 0.95rem;
        margin-bottom: 15px;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .material-meta {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 0.85rem;
        color: #999;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    
    .material-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .badge-public {
        background: rgba(46, 204, 113, 0.2);
        color: #27ae60;
    }
    
    .badge-private {
        background: rgba(149, 165, 166, 0.2);
        color: #7f8c8d;
    }
    
    .btn-view {
        display: inline-block;
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
        color: white;
        padding: 10px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        text-align: center;
        width: 100%;
    }
    
    .btn-view:hover {
        background: linear-gradient(135deg, #064e32, #0a5c36);
        transform: scale(1.05);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    
    .empty-state i {
        font-size: 5rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        color: #666;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        font-size: 1.1rem;
    }
</style>

<!-- Materials Header -->
<div class="modern-card" style="margin-bottom: 30px;">
    <div class="section-header">
        <div class="header-left">
            <div class="icon-badge">
                <i class="fas fa-book-open"></i>
            </div>
            <div>
                <h1 class="section-title">Learning Materials</h1>
                <p style="color: #666; font-size: 1.1rem; margin: 0;">
                    {{ $isStudent ? 'Access Tajweed learning resources' : 'Manage teaching materials' }}
                </p>
            </div>
        </div>
        @if(!$isStudent)
            <a href="{{ route('materials.create') }}" class="btn-create">
                <i class="fas fa-plus-circle"></i> Add Material
            </a>
        @endif
    </div>
</div>

<!-- Success Message -->
@if(session('success'))
    <div style="background: linear-gradient(135deg, #d4f4dd, #a8e6cf); border-left: 5px solid #0a5c36; color: #064e32; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; font-weight: 500;">
        <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Materials Grid -->
@if(count($materials) > 0)
    <div class="materials-grid">
        @foreach($materials as $material)
            <div class="material-card">
                <!-- Thumbnail or Icon -->
                @if($material->thumbnail)
                    <img src="{{ filter_var($material->thumbnail, FILTER_VALIDATE_URL) ? $material->thumbnail : Storage::url($material->thumbnail) }}" 
                         alt="{{ $material->title }}" 
                         class="material-thumbnail"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div class="material-icon {{ $material->type ?? 'document' }}" style="display:none;">
                        @if($material->type === 'pdf')
                            <i class="fas fa-file-pdf"></i>
                        @elseif($material->type === 'video')
                            <i class="fas fa-video"></i>
                        @elseif($material->type === 'audio')
                            <i class="fas fa-headphones"></i>
                        @else
                            <i class="fas fa-file-alt"></i>
                        @endif
                    </div>
                @else
                    <div class="material-icon {{ $material->type ?? 'document' }}">
                        @if($material->type === 'pdf')
                            <i class="fas fa-file-pdf"></i>
                        @elseif($material->type === 'video')
                            <i class="fas fa-video"></i>
                        @elseif($material->type === 'audio')
                            <i class="fas fa-headphones"></i>
                        @else
                            <i class="fas fa-file-alt"></i>
                        @endif
                    </div>
                @endif
                
                <div class="material-body">
                    <h3 class="material-title">{{ $material->title }}</h3>
                    <p class="material-desc">{{ $material->description ?? 'Tajweed learning material' }}</p>
                    <div class="material-meta">
                        <span><i class="fas fa-calendar"></i> {{ $material->created_at->format('M d, Y') }}</span>
                        <span class="material-badge {{ $material->is_public ? 'badge-public' : 'badge-private' }}">
                            <i class="fas fa-{{ $material->is_public ? 'globe' : 'lock' }}"></i>
                            {{ $material->is_public ? 'Public' : 'Private' }}
                        </span>
                    </div>
                    <a href="{{ route('materials.show', $material->material_id) }}" class="btn-view">
                        <i class="fas fa-eye"></i> View Material
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    @if($materials->hasPages())
        <div style="margin-top: 30px; display: flex; justify-content: center;">
            {{ $materials->links() }}
        </div>
    @endif
@else
    <div class="modern-card">
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <h3>No Materials Available</h3>
            <p>{{ $isStudent ? 'Tajweed materials will appear here when your teacher adds them to the system.' : 'Start by creating your first teaching material.' }}</p>
            @if(!$isStudent)
                <a href="{{ route('materials.create') }}" class="btn-create" style="margin-top: 20px;">
                    <i class="fas fa-plus-circle"></i> Create First Material
                </a>
            @endif
        </div>
    </div>
@endif
@endsection
