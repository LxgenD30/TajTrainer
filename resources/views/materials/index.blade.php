@extends('layouts.template')

@section('page-title', 'Learning Materials')
@section('page-subtitle', 'Browse and manage educational resources')

@section('content')
    <div class="content-card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <h3 class="card-title">📚 All Materials</h3>
            <div style="display: flex; gap: 10px; align-items: center;">
                <!-- View Toggle -->
                <div style="display: flex; gap: 5px; background: rgba(31, 39, 27, 0.5); border-radius: 20px; padding: 5px;">
                    <button onclick="setView('grid')" id="gridBtn"
                        style="padding: 8px 15px; background: var(--color-gold); color: var(--color-dark); border: none; border-radius: 15px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                        ⊞ Grid
                    </button>
                    <button onclick="setView('list')" id="listBtn"
                        style="padding: 8px 15px; background: transparent; color: var(--color-light); border: none; border-radius: 15px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                        ☰ List
                    </button>
                </div>
                <a href="{{ route('materials.create') }}" 
                    style="padding: 10px 25px; background: var(--color-gold); color: var(--color-dark); border: none; border-radius: 25px; text-decoration: none; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif; display: inline-flex; align-items: center; gap: 8px;"
                    onmouseover="this.style.opacity='0.8'"
                    onmouseout="this.style.opacity='1'">
                    <span style="font-size: 1.2rem;">➕</span> Add New Material
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="background: rgba(77, 139, 49, 0.2); border: 2px solid var(--color-light-green); color: var(--color-light-green); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="card-body">
            @if($materials->count() > 0)
                <div id="materialsContainer" class="materials-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
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
                                            🎥 {{ Str::limit($material->title, 30) }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Action buttons -->
                                <div style="display: flex; gap: 8px; margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(227, 216, 136, 0.2);">
                                    <a href="{{ route('materials.show', $material->material_id) }}" 
                                        style="flex: 1; padding: 8px 15px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 20px; text-decoration: none; font-weight: 600; text-align: center; font-size: 0.9rem; transition: all 0.3s ease;"
                                        onmouseover="this.style.background='var(--color-light-green)'; this.style.color='var(--color-dark)'"
                                        onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                                        👁️ View
                                    </a>
                                    <a href="{{ route('materials.edit', $material->material_id) }}" 
                                        style="flex: 1; padding: 8px 15px; background: transparent; color: var(--color-gold); border: 2px solid var(--color-gold); border-radius: 20px; text-decoration: none; font-weight: 600; text-align: center; font-size: 0.9rem; transition: all 0.3s ease;"
                                        onmouseover="this.style.background='rgba(227, 216, 136, 0.1)'"
                                        onmouseout="this.style.background='transparent'">
                                        ✏️ Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div style="margin-top: 30px; display: flex; justify-content: center;">
                    {{ $materials->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;">📚</div>
                    <h3 style="color: var(--color-light); margin-bottom: 10px;">No Materials Yet</h3>
                    <p style="color: var(--color-light); opacity: 0.7; margin-bottom: 25px;">Start by adding your first learning material!</p>
                    <a href="{{ route('materials.create') }}" 
                        style="display: inline-block; padding: 12px 30px; background: var(--color-gold); color: var(--color-dark); border-radius: 25px; text-decoration: none; font-weight: 600;">
                        ➕ Create First Material
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        // View toggle functionality
        function setView(view) {
            const container = document.getElementById('materialsContainer');
            const cards = document.querySelectorAll('.material-card');
            const gridBtn = document.getElementById('gridBtn');
            const listBtn = document.getElementById('listBtn');
            
            if (view === 'grid') {
                container.style.display = 'grid';
                container.style.gridTemplateColumns = 'repeat(auto-fill, minmax(280px, 1fr))';
                container.classList.add('materials-grid');
                container.classList.remove('materials-list');
                
                // Reset card styles for grid view
                cards.forEach(card => {
                    card.style.display = 'block';
                    card.style.flexDirection = '';
                    const thumbnail = card.querySelector('.material-thumbnail');
                    const content = card.querySelector('.material-content');
                    if (thumbnail) {
                        thumbnail.style.width = '100%';
                        thumbnail.style.height = '180px';
                        thumbnail.style.flexShrink = '0';
                    }
                    if (content) {
                        content.style.flex = '';
                    }
                });
                
                gridBtn.style.background = 'var(--color-gold)';
                gridBtn.style.color = 'var(--color-dark)';
                listBtn.style.background = 'transparent';
                listBtn.style.color = 'var(--color-light)';
                localStorage.setItem('materialsView', 'grid');
            } else {
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.gridTemplateColumns = 'none';
                container.classList.remove('materials-grid');
                container.classList.add('materials-list');
                
                // Update card styles for list view
                cards.forEach(card => {
                    card.style.display = 'flex';
                    card.style.flexDirection = 'row';
                    card.style.alignItems = 'stretch';
                    const thumbnail = card.querySelector('.material-thumbnail');
                    const content = card.querySelector('.material-content');
                    if (thumbnail) {
                        thumbnail.style.width = '250px';
                        thumbnail.style.height = 'auto';
                        thumbnail.style.minHeight = '200px';
                        thumbnail.style.flexShrink = '0';
                    }
                    if (content) {
                        content.style.flex = '1';
                    }
                });
                
                gridBtn.style.background = 'transparent';
                gridBtn.style.color = 'var(--color-light)';
                listBtn.style.background = 'var(--color-gold)';
                listBtn.style.color = 'var(--color-dark)';
                localStorage.setItem('materialsView', 'list');
            }
        }
        
        // Load saved view preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('materialsView') || 'grid';
            setView(savedView);
        });
    </script>
@endsection
