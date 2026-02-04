@extends('layouts.dashboard')

@section('title', 'Create Material')
@section('user-role', 'Teacher • Add New Material')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.3);
        border: 3px solid #2a2a2a;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    
    .banner-content {
        position: relative;
        z-index: 2;
    }
    
    .banner-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 5px;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .banner-subtitle {
        font-size: 1.05rem;
        opacity: 0.9;
    }
    
    .back-to-materials {
        background: white;
        color: #0a5c36;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 2;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }
    
    .back-to-materials:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        color: #0a5c36;
    }
    
    .form-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
        margin-bottom: 25px;
    }
    
    .form-section {
        background: #f9f9f9;
        padding: 25px;
        border-radius: 12px;
        border: 2px solid #2a2a2a;
        margin-bottom: 25px;
    }
    
    .section-title {
        font-size: 1.6rem;
        color: #000;
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        color: #666;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 1.05rem;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        background: white;
        color: #000;
        border: 2px solid #2a2a2a;
        border-radius: 10px;
        font-size: 1.05rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0a5c36;
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.1);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    
    .form-hint {
        display: block;
        font-size: 0.95rem;
        color: #666;
        margin-top: 5px;
        font-weight: 600;
    }
    
    .btn-group {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #2a2a2a;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: white;
        border: none;
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(10, 92, 54, 0.3);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .btn-secondary {
        background: white;
        color: #000;
        border: 2px solid #2a2a2a;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.05rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-search {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    
    .btn-search:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(52, 152, 219, 0.3);
    }

    .btn-search:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .search-results {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .result-card {
        background: white;
        border: 2px solid #2a2a2a;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .result-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.15);
        border-color: #0a5c36;
    }

    .result-card.selected {
        border-color: #0a5c36;
        background: rgba(10, 92, 54, 0.05);
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.1);
    }

    .result-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #000;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .result-url {
        font-size: 0.9rem;
        color: #0a5c36;
        margin-bottom: 10px;
        word-break: break-all;
        font-weight: 600;
    }

    .result-content {
        font-size: 1.05rem;
        color: #666;
        line-height: 1.6;
    }

    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .search-section {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.05), rgba(41, 128, 185, 0.05));
        border: 2px solid #3498db;
        border-radius: 12px;
        padding: 25px;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-info {
        background: rgba(52, 152, 219, 0.1);
        border: 2px solid #3498db;
        color: #2980b9;
    }

    .alert-success {
        background: rgba(10, 92, 54, 0.1);
        border: 2px solid #0a5c36;
        color: #0a5c36;
    }

    .alert-error {
        background: rgba(231, 76, 60, 0.1);
        border: 2px solid #e74c3c;
        color: #c0392b;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }
</style>

<!-- Banner -->
<div class="welcome-banner">
    <div class="banner-content">
        <h1 class="banner-title"><i class="fas fa-plus-circle"></i> Create New Material</h1>
        <p class="banner-subtitle">Upload files, add links, or search online resources</p>
    </div>
    <a href="{{ route('materials.index') }}" class="back-to-materials">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<!-- Search Online Resources Section -->
<div class="form-card">
    <div class="search-section">
        <h2 class="section-title"><i class="fas fa-globe"></i> Search Online Resources</h2>
        <p class="form-hint" style="margin-bottom: 15px;">Find educational materials from the internet using AI-powered search</p>
        
        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <input type="text" 
                   id="search-query" 
                   class="form-control" 
                   placeholder="e.g., Tajweed rules for beginners, Quran recitation lessons..."
                   style="flex: 1;">
            <button type="button" id="search-btn" class="btn-search">
                <i class="fas fa-search"></i> Search
            </button>
        </div>

        <div id="search-message"></div>
        <div id="search-results" class="search-results" style="display: none;"></div>
    </div>
</div>

<!-- Main Form -->
<div class="form-card">
    <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data" id="material-form">
        @csrf

        <div class="form-section">
            <h3 class="section-title"><i class="fas fa-info-circle"></i> Material Information</h3>
            
            <!-- Title -->
            <div class="form-group">
                <label for="title" class="form-label">
                    <i class="fas fa-heading"></i> Material Title *
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title') }}"
                       required
                       class="form-control"
                       placeholder="e.g., Introduction to Tajweed Rules">
                @error('title')
                    <span class="form-hint" style="color: #e74c3c;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description" class="form-label">
                    <i class="fas fa-align-left"></i> Description
                </label>
                <textarea name="description" 
                          id="description" 
                          class="form-control"
                          placeholder="Describe what students will learn from this material">{{ old('description') }}</textarea>
                @error('description')
                    <span class="form-hint" style="color: #e74c3c;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- External URL (from search) -->
        <input type="hidden" name="url" id="material-url">

        <div class="form-section">
            <h3 class="section-title"><i class="fas fa-link"></i> Links (Optional)</h3>
            
            <!-- Video Link -->
            <div class="form-group">
                <label for="video_link" class="form-label">
                    <i class="fas fa-video"></i> YouTube Video Link
                </label>
                <input type="url" 
                       name="video_link" 
                       id="video_link" 
                       value="{{ old('video_link') }}"
                       class="form-control"
                       placeholder="https://www.youtube.com/watch?v=...">
                <span class="form-hint"><i class="fas fa-info-circle"></i> Add a YouTube video link for video-based learning</span>
                @error('video_link')
                    <span class="form-hint" style="color: #e74c3c;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title"><i class="fas fa-upload"></i> File Uploads (Optional)</h3>
            
            <!-- File Upload -->
            <div class="form-group">
                <label for="file" class="form-label">
                    <i class="fas fa-file-upload"></i> Upload File
                </label>
                <input type="file" 
                       name="file" 
                       id="file"
                       accept=".pdf,.doc,.docx,.mp3,.mp4"
                       class="form-control">
                <span class="form-hint"><i class="fas fa-info-circle"></i> Accepted: PDF, DOC, DOCX, MP3, MP4 • Max size: 20MB</span>
                @error('file')
                    <span class="form-hint" style="color: #e74c3c;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            <!-- Thumbnail Upload -->
            <div class="form-group">
                <label for="thumbnail" class="form-label">
                    <i class="fas fa-image"></i> Thumbnail Image
                </label>
                <input type="file" 
                       name="thumbnail" 
                       id="thumbnail"
                       accept="image/jpeg,image/png,image/jpg,image/gif"
                       class="form-control">
                <span class="form-hint"><i class="fas fa-info-circle"></i> YouTube videos auto-generate thumbnails if not provided</span>
                @error('thumbnail')
                    <span class="form-hint" style="color: #e74c3c;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-globe"></i>
            <span>All materials are automatically made public for all students. You can add YouTube links, upload files, or select resources from the online search above.</span>
        </div>

        <!-- Submit Buttons -->
        <div class="btn-group">
            <a href="{{ route('materials.index') }}" class="btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-check"></i> Create Material
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchBtn = document.getElementById('search-btn');
    const searchQuery = document.getElementById('search-query');
    const searchResults = document.getElementById('search-results');
    const searchMessage = document.getElementById('search-message');
    const materialUrl = document.getElementById('material-url');
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');

    searchBtn.addEventListener('click', async function() {
        const query = searchQuery.value.trim();
        
        if (!query) {
            showMessage('Please enter a search query', 'error');
            return;
        }

        searchBtn.disabled = true;
        searchBtn.innerHTML = '<span class="loading-spinner"></span> Searching...';
        searchResults.style.display = 'none';
        searchMessage.innerHTML = '';

        try {
            const response = await fetch('{{ route("materials.search") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ query: query })
            });

            const data = await response.json();

            if (data.success) {
                displayResults(data.results);
                if (data.results.length > 0) {
                    showMessage(`Found ${data.results.length} educational resources`, 'success');
                } else {
                    showMessage('No results found. Try a different search query.', 'info');
                }
            } else {
                showMessage(data.message || 'Search failed. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Search error:', error);
            showMessage('An error occurred while searching. Please check your API key configuration.', 'error');
        } finally {
            searchBtn.disabled = false;
            searchBtn.innerHTML = '<i class="fas fa-search"></i> Search';
        }
    });

    function displayResults(results) {
        if (results.length === 0) {
            searchResults.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No results found. Try refining your search query.</div>';
            searchResults.style.display = 'block';
            return;
        }

        searchResults.innerHTML = results.map(result => `
            <div class="result-card" data-url="${escapeHtml(result.url)}" data-title="${escapeHtml(result.title)}" data-content="${escapeHtml(result.content || '')}">
                <h4 class="result-title">${escapeHtml(result.title)}</h4>
                <p class="result-url"><i class="fas fa-link"></i> ${escapeHtml(result.url)}</p>
                <p class="result-content">${escapeHtml((result.content || '').substring(0, 200))}${result.content && result.content.length > 200 ? '...' : ''}</p>
                <div style="margin-top: 15px;">
                    <span style="background: rgba(10, 92, 54, 0.1); color: #0a5c36; padding: 8px 16px; border-radius: 8px; font-size: 0.95rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-check-circle"></i> Click to select
                    </span>
                </div>
            </div>
        `).join('');
        searchResults.style.display = 'grid';

        // Add click handlers
        document.querySelectorAll('.result-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove previous selection
                document.querySelectorAll('.result-card').forEach(c => c.classList.remove('selected'));
                
                // Mark as selected
                this.classList.add('selected');
                
                // Fill form fields
                materialUrl.value = this.dataset.url;
                if (!titleInput.value) {
                    titleInput.value = this.dataset.title;
                }
                if (!descriptionInput.value) {
                    descriptionInput.value = this.dataset.content;
                }

                showMessage('Resource selected! The URL, title, and description have been added to the form below.', 'success');
                
                // Scroll to form
                setTimeout(() => {
                    document.getElementById('material-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 500);
            });
        });
    }

    function showMessage(message, type) {
        const iconMap = {
            'error': 'exclamation-circle',
            'success': 'check-circle',
            'info': 'info-circle'
        };
        searchMessage.innerHTML = `<div class="alert alert-${type}"><i class="fas fa-${iconMap[type]}"></i> ${message}</div>`;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    // Allow Enter key to trigger search
    searchQuery.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchBtn.click();
        }
    });
});
</script>
@endsection
