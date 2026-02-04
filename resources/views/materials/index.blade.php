@php
    $currentUser = Auth::user();
    $isStudent = $currentUser->role_id == 2;
@endphp

@extends('layouts.dashboard')

@section('title', 'Learning Materials')
@section('user-role', $isStudent ? 'Student • Learning Resources' : 'Teacher • Materials Management')

@section('navigation')
    @if($isStudent)
        @include('partials.student-nav')
    @else
        @include('partials.teacher-nav')
    @endif
@endsection

@section('content')
<style>
    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 30px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
        border: 3px solid #2a2a2a;
    }
    
    .welcome-banner:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .welcome-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .welcome-content h1 {
        font-size: 2rem;
        margin-bottom: 8px;
        font-weight: 700;
        color: #ffffff;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .welcome-content p {
        font-size: 1.05rem;
        opacity: 0.95;
        margin: 0;
        color: #ffffff;
    }
    
    .btn-create {
        background: linear-gradient(135deg, #d4af37, #f1c40f);
        color: #0a5c36;
        padding: 12px 24px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 2px solid #b38f2d;
        box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
        transition: all 0.3s ease;
    }
    
    .btn-create:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(212, 175, 55, 0.4);
    }
    
    .view-toggle-btn {
        padding: 10px 20px;
        background: white;
        color: #666;
        border: 2px solid #2a2a2a;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 700;
        font-family: 'Cairo', sans-serif;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 1.05rem;
    }
    
    .view-toggle-btn.active {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: white;
        border-color: #2a2a2a;
    }
    
    .view-toggle-btn:hover:not(.active) {
        background: #f9f9f9;
        transform: translateY(-2px);
    }
    
    /* Category Filters */
    .category-filters {
        background: white;
        border: 3px solid #2a2a2a;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    .category-filters h3 {
        font-size: 1.4rem;
        font-weight: 800;
        color: #2a2a2a;
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .filter-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .filter-btn {
        padding: 10px 16px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 3px solid #2a2a2a;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #2a2a2a;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
        white-space: nowrap;
    }
    
    .filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .filter-btn:hover::before {
        left: 100%;
    }
    
    .filter-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        color: #2a2a2a;
    }
    
    .filter-btn.active {
        background: linear-gradient(135deg, #0a5c36 0%, #1abc9c 100%);
        color: white;
        border-color: #2a2a2a;
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.3);
    }
    
    .filter-btn .category-icon {
        font-size: 1rem;
        min-width: 20px;
        text-align: center;
    }
    
    .filter-badge {
        background: rgba(0,0,0,0.15);
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 700;
        min-width: 24px;
        text-align: center;
    }
    
    .filter-btn.active .filter-badge {
        background: rgba(255,255,255,0.3);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    #materialSearch:focus {
        border-color: #0a5c36;
        box-shadow: 0 5px 15px rgba(10, 92, 54, 0.2);
    }
</style>

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="welcome-content" style="width: 100%;">
            <div>
                <h1><i class="fas fa-book-open"></i> Learning Materials</h1>
                <p>{{ $materials->total() }} material{{ $materials->total() != 1 ? 's' : '' }} available</p>
            </div>
            <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <div style="display: flex; gap: 10px;">
                    <button onclick="setView('grid')" id="gridBtn" class="view-toggle-btn active">
                        <i class="fas fa-th"></i> Grid
                    </button>
                    <button onclick="setView('list')" id="listBtn" class="view-toggle-btn">
                        <i class="fas fa-list"></i> List
                    </button>
                </div>
                @if(!$isStudent)
                    <a href="{{ route('materials.create') }}" class="btn-create">
                        <i class="fas fa-plus-circle"></i> Add Material
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Category Filters -->
    <div class="category-filters">
        <h3>
            <i class="fas fa-filter"></i>
            Filter by Category
        </h3>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <div class="search-wrapper" style="position: relative; flex: 0 0 auto; min-width: 250px;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #0a5c36; z-index: 10; font-size: 0.9rem;"></i>
                <input 
                    type="text" 
                    id="materialSearch" 
                    placeholder="Search materials..." 
                    onkeyup="filterMaterials()"
                    value="{{ request('search') }}"
                    style="width: 100%; padding: 10px 15px 10px 40px; border: 3px solid #2a2a2a; border-radius: 25px; font-size: 0.9rem; font-weight: 600; background: white; font-family: 'Cairo', sans-serif; outline: none; transition: all 0.3s ease;"
                >
            </div>
            <div class="filter-buttons" style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
            <a href="{{ route('materials.index', array_filter(['search' => request('search')])) }}" 
               class="filter-btn {{ !request('category') ? 'active' : '' }}">
                <span class="category-icon"><i class="fas fa-th-large"></i></span>
                <span>All Materials</span>
                <span class="filter-badge">{{ $categoryCounts['all'] }}</span>
            </a>
            <a href="{{ route('materials.index', array_filter(['category' => 'Madd Rules', 'search' => request('search')])) }}" 
               class="filter-btn {{ request('category') == 'Madd Rules' ? 'active' : '' }}">
                <span class="category-icon"><i class="fas fa-circle"></i></span>
                <span>Madd Rules</span>
                <span class="filter-badge">{{ $categoryCounts['Madd Rules'] }}</span>
            </a>
            <a href="{{ route('materials.index', array_filter(['category' => 'Idgham Billa Ghunnah', 'search' => request('search')])) }}" 
               class="filter-btn {{ request('category') == 'Idgham Billa Ghunnah' ? 'active' : '' }}">
                <span class="category-icon"><i class="fas fa-wave-square"></i></span>
                <span>Idgham Billa Ghunnah</span>
                <span class="filter-badge">{{ $categoryCounts['Idgham Billa Ghunnah'] }}</span>
            </a>
            <a href="{{ route('materials.index', array_filter(['category' => 'Idgham Bi Ghunnah', 'search' => request('search')])) }}" 
               class="filter-btn {{ request('category') == 'Idgham Bi Ghunnah' ? 'active' : '' }}">
                <span class="category-icon"><i class="fas fa-water"></i></span>
                <span>Idgham Bi Ghunnah</span>
                <span class="filter-badge">{{ $categoryCounts['Idgham Bi Ghunnah'] }}</span>
            </a>
            <a href="{{ route('materials.index', array_filter(['category' => 'Others', 'search' => request('search')])) }}" 
               class="filter-btn {{ request('category') == 'Others' ? 'active' : '' }}">
                <span class="category-icon"><i class="fas fa-ellipsis-h"></i></span>
                <span>Others</span>
                <span class="filter-badge">{{ $categoryCounts['Others'] ?? 0 }}</span>
            </a>
            </div>
        </div>
    </div>

    @if($materials->count() > 0)
        <!-- Materials Container -->
        <div id="materialsContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; margin-bottom: 30px;">
            @foreach($materials as $material)
                <div class="material-card" 
                     data-title="{{ strtolower($material->title) }}"
                     data-description="{{ strtolower($material->description ?? '') }}"
                     data-category="{{ strtolower($material->category ?? '') }}"
                     style="background: white; border-radius: 20px; overflow: hidden; border: 3px solid #e0e0e0; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);"
                     onmouseover="this.style.borderColor='#0a5c36'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 25px rgba(10, 92, 54, 0.15)'"
                     onmouseout="this.style.borderColor='#e0e0e0'; this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0, 0, 0, 0.05)'">
                    
                    <!-- Thumbnail -->
                    <div class="material-thumbnail" style="position: relative; width: 100%; padding-top: 56.25%; background: linear-gradient(135deg, #0a5c36, #1abc9c); overflow: hidden;">
                        @php
                            $thumbnailUrl = null;
                            $videoId = null;
                            
                            // Check if thumbnail exists and is a full URL or local path
                            if ($material->thumbnail) {
                                if (str_starts_with($material->thumbnail, 'http')) {
                                    // It's already a full URL (YouTube thumbnail)
                                    $thumbnailUrl = $material->thumbnail;
                                } else {
                                    // It's a local file path
                                    $thumbnailUrl = asset('storage/' . $material->thumbnail);
                                }
                            } elseif ($material->video_link) {
                                // Extract YouTube video ID from various URL formats
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $material->video_link, $matches)) {
                                    $videoId = $matches[1];
                                    $thumbnailUrl = 'https://img.youtube.com/vi/' . $videoId . '/maxresdefault.jpg';
                                }
                            }
                        @endphp
                        
                        @if($thumbnailUrl)
                            <img src="{{ $thumbnailUrl }}" 
                                 alt="{{ $material->title }}"
                                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; align-items: center; justify-content: center; font-size: 4rem; color: white; opacity: 0.5;">
                                @if($material->file_path)
                                    <i class="fas fa-file-alt"></i>
                                @elseif($material->video_link)
                                    <i class="fas fa-video"></i>
                                @else
                                    <i class="fas fa-book-open"></i>
                                @endif
                            </div>
                        @else
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 4rem; color: white; opacity: 0.5;">
                                @if($material->file_path)
                                    <i class="fas fa-file-alt"></i>
                                @elseif($material->video_link)
                                    <i class="fas fa-video"></i>
                                @else
                                    <i class="fas fa-book-open"></i>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Type Badge -->
                        <div style="position: absolute; top: 15px; right: 15px; padding: 6px 12px; background: rgba(255, 255, 255, 0.95); border-radius: 12px; font-size: 0.85rem; font-weight: 600; font-family: 'Cairo', sans-serif; color: #0a5c36; box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);">
                            @if($material->video_link)
                                <i class="fas fa-video"></i> Video
                            @elseif($material->file_path && Str::endsWith($material->file_path, '.pdf'))
                                <i class="fas fa-file-pdf"></i> PDF
                            @elseif($material->file_path && (Str::endsWith($material->file_path, '.mp3') || Str::endsWith($material->file_path, '.wav')))
                                <i class="fas fa-volume-up"></i> Audio
                            @else
                                <i class="fas fa-file"></i> Document
                            @endif
                        </div>
                        
                        <!-- Public Badge -->
                        @if($material->is_public)
                            <div style="position: absolute; top: 15px; left: 15px; padding: 6px 12px; background: rgba(26, 188, 156, 0.95); color: white; border-radius: 12px; font-size: 0.85rem; font-weight: 600; font-family: 'Cairo', sans-serif; box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);">
                                <i class="fas fa-globe"></i> Public
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="material-content" style="padding: 20px;">
                        <!-- Category Badge -->
                        @if($material->category)
                            <div style="display: inline-block; padding: 6px 14px; background: linear-gradient(135deg, #3498db, #2980b9); color: white; border-radius: 20px; font-size: 0.85rem; font-weight: 700; margin-bottom: 12px;">
                                <i class="fas fa-tag"></i> {{ $material->category }}
                            </div>
                        @endif
                        
                        <h3 style="margin: 0 0 10px 0; font-size: 1.3rem; color: #1a1a1a; font-weight: 700; font-family: 'El Messiri', serif; line-height: 1.4;">
                            {{ $material->title }}
                        </h3>
                        
                        <!-- Item Count -->
                        @if($material->items && $material->items->count() > 0)
                            <div style="display: inline-block; padding: 4px 10px; background: rgba(142, 68, 173, 0.15); color: #8e44ad; border-radius: 12px; font-size: 0.8rem; font-weight: 700; margin-bottom: 10px;">
                                <i class="fas fa-layer-group"></i> {{ $material->items->count() }} item{{ $material->items->count() != 1 ? 's' : '' }}
                            </div>
                        @endif
                        <p style="margin: 0 0 15px 0; font-size: 0.95rem; color: #666; font-family: 'Cairo', sans-serif; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $material->description ?? 'No description available' }}
                        </p>
                        
                        <!-- Resource Indicators -->
                        @if($material->file_path || $material->video_link)
                            <div style="display: flex; gap: 10px; margin: 15px 0; flex-wrap: wrap;">
                                @if($material->file_path)
                                    @php
                                        $fileName = basename($material->file_path);
                                        // Remove timestamp prefix if exists
                                        $fileName = preg_replace('/^\d+_[a-f0-9]+\./', '', $fileName);
                                        $fileName = Str::limit($fileName, 25);
                                    @endphp
                                    <span style="color: #1abc9c; font-size: 0.85rem; display: flex; align-items: center; gap: 5px; padding: 5px 10px; background: rgba(26, 188, 156, 0.1); border-radius: 8px;">
                                        <i class="fas fa-paperclip"></i> {{ $fileName }}
                                    </span>
                                @endif
                                @if($material->video_link)
                                    <span style="color: #1abc9c; font-size: 0.85rem; display: flex; align-items: center; gap: 5px; padding: 5px 10px; background: rgba(26, 188, 156, 0.1); border-radius: 8px;">
                                        <i class="fas fa-play-circle"></i> Video Content
                                    </span>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Meta Info -->
                        <div style="display: flex; align-items: center; gap: 15px; padding-top: 15px; border-top: 2px solid #f5f5f5; margin-bottom: 15px;">
                            <span style="font-size: 0.85rem; color: #999; font-family: 'Cairo', sans-serif;">
                                <i class="fas fa-calendar"></i> {{ $material->created_at->format('M d, Y') }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('materials.show', $material->material_id) }}" 
                               style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 0.95rem; font-family: 'Cairo', sans-serif; transition: all 0.3s ease;"
                               onmouseover="this.style.transform='scale(1.05)'"
                               onmouseout="this.style.transform='scale(1)'">
                                <i class="fas fa-eye"></i> View
                            </a>
                            @if(!$isStudent)
                                <a href="{{ route('materials.edit', $material->material_id) }}" 
                                   style="display: flex; align-items: center; justify-content: center; padding: 10px 15px; background: rgba(212, 175, 55, 0.15); color: #d4af37; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 0.95rem; font-family: 'Cairo', sans-serif; transition: all 0.3s ease;"
                                   onmouseover="this.style.background='rgba(212, 175, 55, 0.25)'"
                                   onmouseout="this.style.background='rgba(212, 175, 55, 0.15)'">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div style="display: flex; justify-content: center;">
            {{ $materials->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div style="background: white; border-radius: 25px; padding: 60px 40px; text-align: center; border: 3px solid #e0e0e0;">
            <div style="font-size: 5rem; margin-bottom: 20px; opacity: 0.3;">📚</div>
            <h3 style="margin: 0 0 10px 0; font-size: 1.8rem; color: #333; font-family: 'El Messiri', serif;">No Materials Yet</h3>
            <p style="margin: 0 0 20px 0; font-size: 1.1rem; color: #666; font-family: 'Cairo', sans-serif;">
                @if($isStudent)
                    Your teacher hasn't uploaded any materials yet.
                @else
                    Start building your course library by adding materials.
                @endif
            </p>
            @if(!$isStudent)
                <a href="{{ route('materials.create') }}" 
                   style="display: inline-flex; align-items: center; gap: 10px; padding: 12px 25px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 15px; text-decoration: none; font-weight: 700; font-size: 1rem; font-family: 'Cairo', sans-serif; transition: all 0.3s ease;"
                   onmouseover="this.style.transform='scale(1.05)'"
                   onmouseout="this.style.transform='scale(1)'">
                    <i class="fas fa-plus-circle"></i> Add First Material
                </a>
            @endif
        </div>
    @endif
@endsection

@section('extra-scripts')
<script>
    console.log('Materials page script loaded');
    
    // View toggle functionality
    function setView(view) {
        console.log('setView called with:', view);
        
        const container = document.getElementById('materialsContainer');
        const cards = document.querySelectorAll('.material-card');
        const thumbnails = document.querySelectorAll('.material-thumbnail');
        const contents = document.querySelectorAll('.material-content');
        const gridBtn = document.getElementById('gridBtn');
        const listBtn = document.getElementById('listBtn');
        
        console.log('Elements found:', {
            container: !!container,
            cardsCount: cards.length,
            thumbnailsCount: thumbnails.length,
            contentsCount: contents.length,
            gridBtn: !!gridBtn,
            listBtn: !!listBtn
        });
        
        if (!container) {
            console.error('Materials container not found!');
            return;
        }
        
        if (view === 'grid') {
            console.log('Switching to grid view');
            // Grid view styles
            container.style.display = 'grid';
            container.style.gridTemplateColumns = 'repeat(auto-fill, minmax(320px, 1fr))';
            container.style.gap = '25px';
            
            cards.forEach(card => {
                card.style.display = 'block';
                card.style.flexDirection = '';
            });
            
            thumbnails.forEach(thumb => {
                thumb.style.width = '100%';
                thumb.style.paddingTop = '56.25%';
                thumb.style.minHeight = '';
            });
            
            contents.forEach(content => {
                content.style.flex = '';
            });
            
            if (gridBtn) gridBtn.classList.add('active');
            if (listBtn) listBtn.classList.remove('active');
        } else {
            console.log('Switching to list view');
            // List view styles
            container.style.display = 'flex';
            container.style.flexDirection = 'column';
            container.style.gap = '20px';
            
            cards.forEach(card => {
                card.style.display = 'flex';
                card.style.flexDirection = 'row';
            });
            
            thumbnails.forEach(thumb => {
                thumb.style.width = '250px';
                thumb.style.paddingTop = '0';
                thumb.style.minHeight = '200px';
                thumb.style.flexShrink = '0';
            });
            
            contents.forEach(content => {
                content.style.flex = '1';
            });
            
            if (listBtn) listBtn.classList.add('active');
            if (gridBtn) gridBtn.classList.remove('active');
        }
        
        // Save preference to localStorage
        localStorage.setItem('materialsView', view);
        console.log('View saved to localStorage:', view);
    }
    
    // Load saved view preference on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - Materials page');
        const savedView = localStorage.getItem('materialsView') || 'grid';
        console.log('Saved view preference:', savedView);
        setView(savedView);
    });
    
    // Live search filter function
    function filterMaterials() {
        const searchInput = document.getElementById('materialSearch').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.material-card');
        let visibleCount = 0;
        
        cards.forEach(card => {
            const title = card.getAttribute('data-title') || '';
            const description = card.getAttribute('data-description') || '';
            const category = card.getAttribute('data-category') || '';
            
            if (title.includes(searchInput) || description.includes(searchInput) || category.includes(searchInput)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    console.log('setView function defined:', typeof setView);
</script>
@endsection
