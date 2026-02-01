@extends('layouts.template')

@section('title', 'Learning Materials')
@section('page-title', 'Learning Materials')
@section('page-subtitle', 'Browse and access educational materials')

@section('content')
<div class="content-card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid rgba(227, 216, 136, 0.1); padding-bottom: 20px; margin-bottom: 25px;">
        <h3 class="card-title" style="margin: 0; display: flex; align-items: center; gap: 12px;">
            📚 All Materials 
            <span style="font-size: 0.9rem; font-weight: 400; color: var(--color-light-green); opacity: 0.8; background: rgba(77, 139, 49, 0.1); padding: 4px 12px; border-radius: 15px;">
                {{ $materials->count() }} Available
            </span>
        </h3>

        <div style="display: flex; gap: 10px; background: rgba(31, 39, 27, 0.5); padding: 6px; border-radius: 25px; border: 1px solid var(--color-dark-green);">
            <button onclick="switchView('grid')" id="gridBtn" class="view-toggle" style="padding: 8px 18px; border: none; border-radius: 20px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; gap: 6px; font-family: 'Cairo', sans-serif;">
                <span>▦</span> Grid
            </button>
            <button onclick="switchView('list')" id="listBtn" class="view-toggle" style="padding: 8px 18px; border: none; border-radius: 20px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; gap: 6px; font-family: 'Cairo', sans-serif;">
                <span>☰</span> List
            </button>
        </div>
    </div>

    <div class="card-body">
        @if($materials->isEmpty())
            <div style="text-align: center; padding: 60px 20px; color: var(--color-light-green); background: rgba(31, 39, 27, 0.3); border-radius: 15px; border: 2px dashed var(--color-dark-green);">
                <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;">📖</div>
                <h3 style="font-size: 1.3rem; margin-bottom: 10px; color: var(--color-gold);">No Materials Available</h3>
                <p style="font-size: 1rem; opacity: 0.8;">Check back later for new learning materials</p>
            </div>
        @else
            <div id="materialsContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
                @foreach($materials as $material)
                    <div class="material-card" style="background: rgba(31, 39, 27, 0.6); border: 2px solid var(--color-dark-green); border-radius: 15px; overflow: hidden; transition: all 0.3s ease; cursor: pointer;"
                        onmouseover="this.style.borderColor='var(--color-gold)'; this.style.transform='translateY(-5px)'"
                        onmouseout="this.style.borderColor='var(--color-dark-green)'; this.style.transform='translateY(0)'">
                        
                        <!-- Thumbnail -->
                        <div class="material-thumbnail" style="width: 100%; height: 180px; overflow: hidden; background: rgba(227, 216, 136, 0.1); position: relative;">
                            @php
                                $thumbnailUrl = null;
                                if ($material->thumbnail) {
                                    $thumbnailUrl = filter_var($material->thumbnail, FILTER_VALIDATE_URL) 
                                        ? $material->thumbnail 
                                        : Storage::url($material->thumbnail);
                                } elseif ($material->video_link) {
                                    // Extract YouTube thumbnail from video link
                                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $material->video_link, $matches)) {
                                        $thumbnailUrl = 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
                                    }
                                }
                            @endphp
                            
                            @if($thumbnailUrl)
                                <img src="{{ $thumbnailUrl }}" alt="{{ $material->title }}" 
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; font-size: 3rem; color: var(--color-gold); opacity: 0.5;">
                                    @if($material->file_path)
                                        📄
                                    @elseif($material->video_link)
                                        🎥
                                    @else
                                        📚
                                    @endif
                                </div>
                            @else
                                <div style="display: flex; width: 100%; height: 100%; align-items: center; justify-content: center; font-size: 3rem; color: var(--color-gold); opacity: 0.5;">
                                    @if($material->file_path)
                                        📄
                                    @elseif($material->video_link)
                                        🎥
                                    @else
                                        📚
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Public badge -->
                            @if($material->is_public)
                                <div style="position: absolute; top: 10px; right: 10px; background: rgba(77, 139, 49, 0.9); color: white; padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                                    🌍 Public
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="material-content" style="padding: 15px;">
                            <h4 style="color: var(--color-gold); margin: 0 0 10px 0; font-size: 1.1rem; line-height: 1.3;">
                                {{ $material->title }}
                            </h4>

                            @if($material->description)
                                <p style="color: var(--color-light); font-size: 0.9rem; margin: 10px 0; line-height: 1.5; opacity: 0.8;">
                                    {{ Str::limit($material->description, 80) }}
                                </p>
                            @endif

                            <!-- Resource indicators -->
                            <div style="display: flex; gap: 10px; margin: 15px 0; flex-wrap: wrap;">
                                @if($material->file_path)
                                    @php
                                        $fileName = basename($material->file_path);
                                        // Remove timestamp prefix if exists
                                        $fileName = preg_replace('/^\d+_[a-f0-9]+\./', '', $fileName);
                                        $fileName = Str::limit($fileName, 25);
                                    @endphp
                                    <span style="color: var(--color-light-green); font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">
                                        📄 {{ $fileName }}
                                    </span>
                                @endif
                                @if($material->video_link)
                                    <span style="color: var(--color-light-green); font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">
                                        🎥 Video
                                    </span>
                                @endif
                            </div>

                            <!-- Action buttons -->
                            <div style="display: flex; gap: 8px; margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(227, 216, 136, 0.2);">
                                <a href="{{ route('student.material.show', $material->material_id) }}" 
                                    style="flex: 1; padding: 8px 15px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 20px; text-decoration: none; font-weight: 600; text-align: center; font-size: 0.9rem; transition: all 0.3s ease;"
                                    onmouseover="this.style.background='var(--color-light-green)'; this.style.color='var(--color-dark)'"
                                    onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                                    👁️ View
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
    /* View Toggle Logic */
    .view-toggle { background: transparent; color: var(--color-light-green); }
    .view-toggle.active { background: var(--color-dark-green) !important; color: var(--color-gold) !important; border: 1px solid var(--color-gold) !important; }
    .view-toggle:hover:not(.active) { background: rgba(227, 216, 136, 0.05); }
</style>

<script>
    function setView(view) {
        const container = document.getElementById('materialsContainer');
        const gridBtn = document.getElementById('gridBtn');
        const listBtn = document.getElementById('listBtn');
        const cards = document.querySelectorAll('.material-card');
        const thumbnails = document.querySelectorAll('.material-thumbnail');
        const contents = document.querySelectorAll('.material-content');

        if (view === 'grid') {
            // Grid view layout
            container.style.display = 'grid';
            container.style.gridTemplateColumns = 'repeat(auto-fill, minmax(280px, 1fr))';
            container.style.gap = '25px';
            
            cards.forEach(card => {
                card.style.display = 'block';
                card.style.flexDirection = '';
            });
            
            thumbnails.forEach(thumb => {
                thumb.style.width = '100%';
                thumb.style.height = '180px';
                thumb.style.minHeight = '';
            });
            
            contents.forEach(content => {
                content.style.flex = '';
            });
            
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
        } else {
            // List view layout
            container.style.display = 'flex';
            container.style.flexDirection = 'column';
            container.style.gap = '15px';
            
            cards.forEach(card => {
                card.style.display = 'flex';
                card.style.flexDirection = 'row';
            });
            
            thumbnails.forEach(thumb => {
                thumb.style.width = '250px';
                thumb.style.height = 'auto';
                thumb.style.minHeight = '200px';
            });
            
            contents.forEach(content => {
                content.style.flex = '1';
            });
            
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
        }
        
        // Save preference
        localStorage.setItem('materialsView', view);
    }

    // Load saved view preference on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedView = localStorage.getItem('materialsView') || 'grid';
        setView(savedView);
    });

    function switchView(view) {
        setView(view);
    }
</script>
@endsection