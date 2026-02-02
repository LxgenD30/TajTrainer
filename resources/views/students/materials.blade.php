@extends('layouts.dashboard')

@section('title', 'Learning Materials')
@section('user-role', 'Student • Learning Resources')

@section('navigation')
    <a href="{{ url('/student/classes') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ url('/student/classes') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="nav-label">My Classes</div>
    </a>
    
    <a href="{{ url('/student/practice') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-microphone-alt"></i>
        </div>
        <div class="nav-label">Practice</div>
    </a>
    
    <a href="{{ url('/student/progress') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="nav-label">My Progress</div>
    </a>
    
    <a href="{{ url('/student/materials') }}" class="nav-item active">
        <div class="nav-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="nav-label">Materials</div>
    </a>
    
    <a href="{{ route('students.show', Auth::id()) }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="nav-label">Profile</div>
    </a>
    
    <form action="{{ route('logout') }}" method="POST" style="display: inline;" class="nav-item">
        @csrf
        <button type="submit" style="all: unset; width: 100%; cursor: pointer;">
            <div class="nav-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="nav-label">Logout</div>
        </button>
    </form>
@endsection

@section('extra-styles')
<style>
    .materials-header {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        color: var(--white);
        box-shadow: 0 8px 20px var(--shadow);
    }
    
    .materials-header h2 {
        color: var(--white);
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .materials-header p {
        font-size: 1.1rem;
        opacity: 0.9;
    }
    
    .materials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
    }
    
    .material-card {
        background: var(--white);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 20px var(--shadow);
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .material-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
    }
    
    .material-icon-container {
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.08), rgba(46, 139, 87, 0.08));
        position: relative;
        overflow: hidden;
    }
    
    .material-icon-container:before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
    }
    
    .material-icon {
        font-size: 4rem;
        color: var(--primary-green);
        position: relative;
        z-index: 2;
    }
    
    .material-body {
        padding: 25px;
    }
    
    .material-type {
        display: inline-block;
        padding: 5px 12px;
        background: rgba(10, 92, 54, 0.1);
        color: var(--primary-green);
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 12px;
        font-family: 'El Messiri', sans-serif;
    }
    
    .material-title {
        font-size: 1.3rem;
        color: var(--primary-green);
        margin-bottom: 10px;
        font-weight: 600;
    }
    
    .material-desc {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    
    .material-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    .material-meta {
        display: flex;
        gap: 15px;
        font-size: 0.85rem;
        color: #999;
    }
    
    .material-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-view {
        padding: 8px 18px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
        border-radius: 50px;
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .material-card:hover .btn-view {
        transform: scale(1.05);
    }
    
    .empty-materials {
        background: var(--white);
        border-radius: 20px;
        padding: 80px 40px;
        text-align: center;
        box-shadow: 0 8px 20px var(--shadow);
    }
    
    .empty-materials i {
        font-size: 6rem;
        color: rgba(10, 92, 54, 0.2);
        margin-bottom: 20px;
    }
    
    .empty-materials h3 {
        color: var(--primary-green);
        font-size: 1.8rem;
        margin-bottom: 10px;
    }
    
    .empty-materials p {
        color: #666;
        font-size: 1.1rem;
    }
    
    .filter-section {
        background: var(--white);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px var(--shadow);
    }
    
    .filter-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .filter-btn {
        padding: 10px 20px;
        background: rgba(10, 92, 54, 0.05);
        border: 2px solid rgba(10, 92, 54, 0.1);
        border-radius: 50px;
        color: var(--primary-green);
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .filter-btn:hover, .filter-btn.active {
        background: var(--primary-green);
        color: var(--white);
        border-color: var(--primary-green);
    }
    
    @media (max-width: 768px) {
        .materials-grid {
            grid-template-columns: 1fr;
        }
        
        .filter-buttons {
            flex-direction: column;
        }
        
        .filter-btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<!-- Materials Header -->
<div class="materials-header">
    <h2><i class="fas fa-book-reader"></i> Learning Materials</h2>
    <p>Access Tajweed guides, audio examples, and practice resources</p>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <div class="filter-buttons">
        <button class="filter-btn active" onclick="filterMaterials('all')">
            <i class="fas fa-th"></i> All Materials
        </button>
        <button class="filter-btn" onclick="filterMaterials('pdf')">
            <i class="fas fa-file-pdf"></i> PDF Documents
        </button>
        <button class="filter-btn" onclick="filterMaterials('audio')">
            <i class="fas fa-headphones"></i> Audio Files
        </button>
        <button class="filter-btn" onclick="filterMaterials('video')">
            <i class="fas fa-video"></i> Video Tutorials
        </button>
    </div>
</div>

<!-- Materials Grid -->
@php
    $materials = \App\Models\Material::latest()->get();
    
    // If no materials in database, show sample materials
    if($materials->isEmpty()) {
        $sampleMaterials = [
            [
                'title' => 'Complete Tajweed Rules Guide',
                'description' => 'Comprehensive PDF guide covering all Tajweed rules with examples and exercises.',
                'type' => 'pdf',
                'icon' => 'fa-file-pdf',
                'size' => '2.5 MB'
            ],
            [
                'title' => 'Makharij Audio Examples',
                'description' => 'Professional recitation examples demonstrating correct pronunciation points.',
                'type' => 'audio',
                'icon' => 'fa-headphones',
                'size' => '15 MB'
            ],
            [
                'title' => 'Ghunnah Practice Sessions',
                'description' => 'Interactive audio lessons for perfecting nasal sound pronunciation.',
                'type' => 'audio',
                'icon' => 'fa-microphone',
                'size' => '8.3 MB'
            ],
            [
                'title' => 'Tajweed Rules Video Series',
                'description' => 'Step-by-step video tutorials covering each Tajweed rule in detail.',
                'type' => 'video',
                'icon' => 'fa-video',
                'size' => '450 MB'
            ],
            [
                'title' => 'Qalqalah Demonstration',
                'description' => 'Visual and audio guide to mastering the bouncing pronunciation technique.',
                'type' => 'video',
                'icon' => 'fa-play-circle',
                'size' => '120 MB'
            ],
            [
                'title' => 'Madd Rules Reference Sheet',
                'description' => 'Quick reference guide for all elongation rules with visual aids.',
                'type' => 'pdf',
                'icon' => 'fa-file-alt',
                'size' => '1.2 MB'
            ]
        ];
    }
@endphp

@if($materials->isEmpty())
    <div class="materials-grid" id="materialsGrid">
        @foreach($sampleMaterials as $index => $material)
            <div class="material-card" data-type="{{ $material['type'] }}" onclick="alert('This is a sample material. Real materials will be available when added by your teacher.')" style="cursor: pointer;">
                <div class="material-icon-container">
                    <i class="fas {{ $material['icon'] }} material-icon"></i>
                </div>
                <div class="material-body">
                    <span class="material-type">{{ strtoupper($material['type']) }}</span>
                    <h3 class="material-title">{{ $material['title'] }}</h3>
                    <p class="material-desc">{{ $material['description'] }}</p>
                    <div class="material-footer">
                        <div class="material-meta">
                            <span><i class="fas fa-hdd"></i> {{ $material['size'] }}</span>
                        </div>
                        <div class="btn-view">
                            <i class="fas fa-eye"></i> View
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="materials-grid" id="materialsGrid">
        @foreach($materials as $material)
            @php
                $fileType = strtolower(pathinfo($material->file_path ?? '', PATHINFO_EXTENSION));
                $icon = 'fa-file';
                $type = 'document';
                
                if(in_array($fileType, ['pdf'])) {
                    $icon = 'fa-file-pdf';
                    $type = 'pdf';
                } elseif(in_array($fileType, ['mp3', 'wav', 'ogg'])) {
                    $icon = 'fa-headphones';
                    $type = 'audio';
                } elseif(in_array($fileType, ['mp4', 'avi', 'mov'])) {
                    $icon = 'fa-video';
                    $type = 'video';
                } elseif(in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $icon = 'fa-image';
                    $type = 'image';
                }
            @endphp
            
            <div class="material-card" data-type="{{ $type }}" onclick="window.location.href='{{ route('student.material.show', $material->id) }}'" style="cursor: pointer;">
                <div class="material-icon-container">
                    <i class="fas {{ $icon }} material-icon"></i>
                </div>
                <div class="material-body">
                    <span class="material-type">{{ strtoupper($type) }}</span>
                    <h3 class="material-title">{{ $material->title }}</h3>
                    <p class="material-desc">{{ Str::limit($material->description ?? 'Learning material', 100) }}</p>
                    <div class="material-footer">
                        <div class="material-meta">
                            <span><i class="fas fa-calendar"></i> {{ $material->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="btn-view">
                            <i class="fas fa-eye"></i> View
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

@section('extra-scripts')
<script>
    function filterMaterials(type) {
        const cards = document.querySelectorAll('.material-card');
        const buttons = document.querySelectorAll('.filter-btn');
        
        // Update active button
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        // Filter cards
        cards.forEach(card => {
            if (type === 'all' || card.dataset.type === type) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
