@php
    $currentUser = Auth::user();
    $isStudent = $currentUser->role_id == 2;
@endphp

@extends('layouts.dashboard')

@section('title', 'Learning Materials')
@section('user-role', $isStudent ? 'Student • Learning Resources' : 'Teacher • Materials Management')

@section('navigation')
    @if($isStudent)
        <a href="{{ route('student.dashboard') }}" class="nav-item">
            <div class="nav-icon"><i class="fas fa-home"></i></div>
            <div class="nav-label">Dashboard</div>
        </a>
        <a href="{{ route('student.classes') }}" class="nav-item">
            <div class="nav-icon"><i class="fas fa-users"></i></div>
            <div class="nav-label">My Classes</div>
        </a>
        <a href="{{ route('student.practice') }}" class="nav-item">
            <div class="nav-icon"><i class="fas fa-microphone-alt"></i></div>
            <div class="nav-label">Practice</div>
        </a>
        <a href="{{ route('student.progress') }}" class="nav-item">
            <div class="nav-icon"><i class="fas fa-chart-line"></i></div>
            <div class="nav-label">My Progress</div>
        </a>
        <a href="{{ route('student.materials') }}" class="nav-item active">
            <div class="nav-icon"><i class="fas fa-book-open"></i></div>
            <div class="nav-label">Materials</div>
        </a>
    @else
        <a href="{{ route('home') }}" class="nav-item">
            <div class="nav-icon"><i class="fas fa-home"></i></div>
            <div class="nav-label">Dashboard</div>
        </a>
        <a href="{{ route('classroom.index') }}" class="nav-item">
            <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="nav-label">My Classes</div>
        </a>
        <a href="{{ route('students.list') }}" class="nav-item">
            <div class="nav-icon"><i class="fas fa-user-graduate"></i></div>
            <div class="nav-label">My Students</div>
        </a>
        <a href="{{ route('materials.index') }}" class="nav-item active">
            <div class="nav-icon"><i class="fas fa-book-open"></i></div>
            <div class="nav-label">Materials</div>
        </a>
    @endif
@endsection

@section('content')
<style>
    .view-toggle-btn {
        padding: 10px 20px;
        background: transparent;
        color: #666;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        font-family: 'Cairo', sans-serif;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .view-toggle-btn.active {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: white;
        border-color: #0a5c36;
    }
    
    .view-toggle-btn:hover:not(.active) {
        border-color: #0a5c36;
        color: #0a5c36;
    }
</style>
    <!-- Success Message -->
    @if(session('success'))
        <div style="background: rgba(46, 125, 50, 0.2); border: 3px solid #4caf50; color: #2e7d32; padding: 15px 20px; border-radius: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">✓</span>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div style="background: white; border-radius: 25px; padding: 30px; margin-bottom: 30px; border: 3px solid #2a2a2a; box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <h1 style="margin: 0 0 10px 0; font-family: 'El Messiri', serif; font-size: 2.5rem; color: #0a5c36; font-weight: 700;">
                    <i class="fas fa-book-open"></i> Learning Materials
                </h1>
                <p style="margin: 0; font-size: 1.1rem; color: #666; font-family: 'Cairo', sans-serif;">
                    {{ $materials->total() }} material{{ $materials->total() != 1 ? 's' : '' }} available
                </p>
            </div>
            <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <!-- View Toggle -->
                <div style="display: flex; gap: 8px;">
                    <button onclick="setView('grid')" id="gridBtn" class="view-toggle-btn active">
                        <i class="fas fa-th"></i> Grid
                    </button>
                    <button onclick="setView('list')" id="listBtn" class="view-toggle-btn">
                        <i class="fas fa-list"></i> List
                    </button>
                </div>
                
                @if(!$isStudent)
                    <a href="{{ route('materials.create') }}" 
                       style="display: inline-flex; align-items: center; gap: 10px; padding: 12px 25px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 15px; text-decoration: none; font-weight: 700; font-size: 1rem; font-family: 'Cairo', sans-serif; transition: all 0.3s ease; border: 3px solid transparent;"
                       onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 5px 20px rgba(10, 92, 54, 0.4)'"
                       onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'">
                        <i class="fas fa-plus-circle"></i> Add New Material
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if($materials->count() > 0)
        <!-- Materials Container -->
        <div id="materialsContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; margin-bottom: 30px;">
            @foreach($materials as $material)
                <div class="material-card" style="background: white; border-radius: 20px; overflow: hidden; border: 3px solid #e0e0e0; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);"
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
                        <h3 style="margin: 0 0 10px 0; font-size: 1.3rem; color: #1a1a1a; font-weight: 700; font-family: 'El Messiri', serif; line-height: 1.4;">
                            {{ $material->title }}
                        </h3>
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
    
    console.log('setView function defined:', typeof setView);
</script>
@endsection
