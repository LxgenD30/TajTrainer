@extends('layouts.dashboard')

@section('content')
<style>
    .create-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .compact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .section-card {
        background: rgba(255, 255, 255, 0.95);
        border: 3px solid #2a2a2a;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .section-header {
        font-family: 'El Messiri', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: #0a5c36;
        margin: 0 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #0a5c36;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        font-weight: 600;
        color: #2a2a2a;
        margin-bottom: 6px;
        font-size: 0.95rem;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0a5c36;
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.1);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }
    
    .search-card {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border: 3px solid #2a2a2a;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        color: white;
    }
    
    .search-type-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .search-type-btn {
        flex: 1;
        padding: 10px;
        border: 2px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.1);
        color: white;
        font-weight: 700;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .search-type-btn.active {
        background: rgba(255,255,255,0.3);
        border-color: white;
    }
    
    .search-flex {
        display: flex;
        gap: 10px;
    }
    
    .search-flex input {
        flex: 1;
    }
    
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
    }
    
    .btn-search {
        background: white;
        color: #0a5c36;
        border: 2px solid white;
    }
    
    .btn-search:hover {
        background: #f0f0f0;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #27ae60, #229954);
        color: white;
        border: 2px solid #1e8449;
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        border: 2px solid #1f5f8b;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: 2px solid #a93226;
    }
    
    .radio-group {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .radio-option {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .radio-option input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    .radio-option label {
        margin: 0;
        cursor: pointer;
        font-weight: 600;
    }
    
    .category-pills {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .category-pill {
        padding: 10px 20px;
        border: 2px solid #ddd;
        border-radius: 25px;
        background: white;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .category-pill input[type="radio"] {
        display: none;
    }
    
    .category-pill input[type="radio"]:checked + label {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: white;
        border-color: #0a5c36;
    }
    
    .item-card {
        background: #f8f9fa;
        border: 2px solid #ddd;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        position: relative;
    }
    
    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .remove-btn {
        background: #e74c3c;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .hidden {
        display: none !important;
    }
    
    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 15px;
        margin-top: 15px;
        max-height: 500px;
        overflow-y: auto;
    }
    
    .result-card {
        background: rgba(255,255,255,0.95);
        border: 2px solid rgba(255,255,255,0.5);
        border-radius: 10px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #2a2a2a;
    }
    
    .result-card:hover {
        transform: translateY(-3px);
        border-color: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .result-card h4 {
        margin: 0 0 10px 0;
        font-size: 1.1rem;
        color: #0a5c36;
        font-weight: 700;
    }
    
    .result-card p {
        margin: 0;
        font-size: 0.9rem;
        color: #666;
        line-height: 1.4;
    }
    
    @media (max-width: 992px) {
        .compact-grid,
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="create-container">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('materials.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Materials
        </a>
    </div>
    
    <div style="background: linear-gradient(135deg, #0a5c36, #1abc9c); border: 3px solid #2a2a2a; border-radius: 15px; padding: 30px; margin-bottom: 20px; color: white;">
        <h1 style="margin: 0 0 10px 0; font-family: 'El Messiri', sans-serif; font-size: 2rem; font-weight: 700;">
            <i class="fas fa-plus-circle"></i> Create New Material
        </h1>
        <p style="margin: 0; font-size: 1.1rem; opacity: 0.9;">Add educational resources for students</p>
    </div>
    
    <!-- Online Search -->
    <div class="search-card">
        <h2 class="section-header" style="color: white; border-color: rgba(255,255,255,0.3);">
            <i class="fas fa-search"></i> Search Online Materials
        </h2>
        
        <div class="search-type-buttons">
            <button type="button" class="search-type-btn active" onclick="setSearchType('pdf')">
                <i class="fas fa-file-pdf"></i> PDF Resources
            </button>
            <button type="button" class="search-type-btn" onclick="setSearchType('youtube')">
                <i class="fab fa-youtube"></i> YouTube Videos
            </button>
        </div>
        
        <div class="search-flex">
            <input type="text" 
                   id="searchQuery" 
                   class="form-control" 
                   placeholder="Search for educational materials..."
                   onkeypress="if(event.key === 'Enter') searchOnline()">
            <button type="button" class="btn btn-search" onclick="searchOnline()">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
        
        <div id="searchResults"></div>
    </div>
    
    <!-- Material Form -->
    <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="compact-grid">
            <!-- Left Column: Basic Info -->
            <div class="section-card">
                <h2 class="section-header"><i class="fas fa-info-circle"></i> Basic Information</h2>
                
                <div class="form-group">
                    <label>Material Title *</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g., Introduction to Tajweed Rules">
                </div>
                
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" class="form-control" required placeholder="Describe what students will learn..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Thumbnail (Optional)</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="is_public" value="1" checked style="width: 20px; height: 20px;">
                        Make this material public
                    </label>
                </div>
            </div>
            
            <!-- Right Column: Category -->
            <div class="section-card">
                <h2 class="section-header">
                    <i class="fas fa-tag"></i> Category *
                    <button type="button" class="btn btn-secondary" onclick="suggestCategory()" style="margin-left: auto; padding: 8px 16px; font-size: 0.9rem;">
                        <i class="fas fa-magic"></i> AI Suggest
                    </button>
                </h2>
                
                <div class="category-pills">
                    <div class="category-pill">
                        <input type="radio" name="category" id="cat1" value="Madd Rules" required>
                        <label for="cat1" style="cursor: pointer; margin: 0;">Madd Rules</label>
                    </div>
                    <div class="category-pill">
                        <input type="radio" name="category" id="cat2" value="Idgham Billa Ghunnah" required>
                        <label for="cat2" style="cursor: pointer; margin: 0;">Idgham Billa Ghunnah</label>
                    </div>
                    <div class="category-pill">
                        <input type="radio" name="category" id="cat3" value="Idgham Bi Ghunnah" required>
                        <label for="cat3" style="cursor: pointer; margin: 0;">Idgham Bi Ghunnah</label>
                    </div>
                </div>
                
                <div id="aiSuggestion" style="margin-top: 15px; padding: 15px; background: rgba(52, 152, 219, 0.1); border: 2px dashed #3498db; border-radius: 8px; display: none;">
                    <strong style="color: #3498db;"><i class="fas fa-lightbulb"></i> AI Suggestion:</strong>
                    <p id="suggestedCategory" style="margin: 5px 0 0 0; font-weight: 600;"></p>
                </div>
            </div>
        </div>
        
        <!-- Material Items -->
        <div class="section-card">
            <h2 class="section-header">
                <i class="fas fa-layer-group"></i> Material Resources
                <button type="button" class="btn btn-secondary" onclick="addMaterialItem()" style="margin-left: auto; padding: 8px 16px; font-size: 0.9rem;">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </h2>
            
            <div id="materialItemsContainer"></div>
        </div>
        
        <!-- Submit -->
        <div style="display: flex; justify-content: flex-end; gap: 15px;">
            <a href="{{ route('materials.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Material
            </button>
        </div>
    </form>
</div>

<script>
let itemCounter = 0;
let currentSearchType = 'pdf';

// Set search type
function setSearchType(type) {
    currentSearchType = type;
    document.querySelectorAll('.search-type-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('.search-type-btn').classList.add('active');
}

// Search online
async function searchOnline() {
    const query = document.getElementById('searchQuery').value.trim();
    if (!query) {
        alert('Please enter a search query');
        return;
    }
    
    const resultsDiv = document.getElementById('searchResults');
    resultsDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: white;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem;"></i><p style="margin-top: 10px;">Searching...</p></div>';
    
    try {
        const response = await fetch('{{ route("materials.search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                query: query,
                type: currentSearchType
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayResults(data.results);
        } else {
            resultsDiv.innerHTML = `<div style="padding: 20px; background: rgba(231, 76, 60, 0.2); border: 2px solid rgba(231, 76, 60, 0.5); border-radius: 8px; color: white; margin-top: 15px;"><i class="fas fa-exclamation-triangle"></i> ${data.message || 'Search failed'}</div>`;
        }
    } catch (error) {
        resultsDiv.innerHTML = '<div style="padding: 20px; background: rgba(231, 76, 60, 0.2); border: 2px solid rgba(231, 76, 60, 0.5); border-radius: 8px; color: white; margin-top: 15px;"><i class="fas fa-exclamation-triangle"></i> An error occurred during search</div>';
    }
}

// Display search results
function displayResults(results) {
    const resultsDiv = document.getElementById('searchResults');
    
    if (!results || results.length === 0) {
        resultsDiv.innerHTML = '<div style="padding: 20px; background: rgba(241, 196, 15, 0.2); border: 2px solid rgba(241, 196, 15, 0.5); border-radius: 8px; color: white; margin-top: 15px;"><i class="fas fa-info-circle"></i> No results found</div>';
        return;
    }
    
    let html = '<div class="results-grid">';
    results.forEach((result, index) => {
        html += `
            <div class="result-card" onclick="addFromSearch(${index})">
                ${result.is_pdf ? '<div style="background: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; display: inline-block; font-size: 0.8rem; font-weight: 700; margin-bottom: 8px;"><i class="fas fa-file-pdf"></i> PDF</div>' : ''}
                ${result.video_id ? '<div style="background: #ff0000; color: white; padding: 4px 8px; border-radius: 4px; display: inline-block; font-size: 0.8rem; font-weight: 700; margin-bottom: 8px;"><i class="fab fa-youtube"></i> VIDEO</div>' : ''}
                <h4>${escapeHtml(result.title)}</h4>
                <p>${escapeHtml(result.content.substring(0, 100))}...</p>
                ${result.is_pdf ? `<button type="button" onclick="event.stopPropagation(); downloadPDF('${result.download_url}', '${result.title}')" style="margin-top: 10px; padding: 6px 12px; background: #27ae60; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;"><i class="fas fa-download"></i> Download PDF</button>` : ''}
            </div>
        `;
    });
    html += '</div>';
    
    resultsDiv.innerHTML = html;
    
    // Store results for later use
    window.searchResults = results;
}

// Add from search
function addFromSearch(index) {
    const result = window.searchResults[index];
    const itemId = addMaterialItem();
    
    if (result.video_id) {
        document.getElementById(`type_youtube_${itemId}`).checked = true;
        toggleItemFields(itemId, 'youtube');
        document.querySelector(`[name="items[${itemId}][youtube_link]"]`).value = result.url;
    } else {
        document.getElementById(`type_url_${itemId}`).checked = true;
        toggleItemFields(itemId, 'url');
        document.querySelector(`[name="items[${itemId}][url]"]`).value = result.url;
    }
    
    document.querySelector(`[name="items[${itemId}][title]"]`).value = result.title;
    document.querySelector(`[name="items[${itemId}][description]"]`).value = result.content.substring(0, 200);
    
    // Scroll to the new item
    document.querySelector(`[data-item-id="${itemId}"]`).scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Download PDF
function downloadPDF(url, filename) {
    const link = document.createElement('a');
    link.href = url;
    link.download = filename.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.pdf';
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Add material item
function addMaterialItem() {
    itemCounter++;
    const itemId = itemCounter;
    const container = document.getElementById('materialItemsContainer');
    
    const html = `
        <div class="item-card" data-item-id="${itemId}">
            <div class="item-header">
                <strong style="font-size: 1.1rem; color: #0a5c36;">Item #${itemId}</strong>
                <button type="button" class="remove-btn" onclick="removeItem(${itemId})">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            
            <div class="radio-group">
                <div class="radio-option">
                    <input type="radio" id="type_file_${itemId}" name="items[${itemId}][type]" value="file" onchange="toggleItemFields(${itemId}, 'file')" required>
                    <label for="type_file_${itemId}">Upload File</label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="type_youtube_${itemId}" name="items[${itemId}][type]" value="youtube" onchange="toggleItemFields(${itemId}, 'youtube')">
                    <label for="type_youtube_${itemId}">YouTube</label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="type_url_${itemId}" name="items[${itemId}][type]" value="url" onchange="toggleItemFields(${itemId}, 'url')">
                    <label for="type_url_${itemId}">Link</label>
                </div>
            </div>
            
            <div id="fields_file_${itemId}" class="hidden">
                <div class="form-group">
                    <label>Upload File</label>
                    <input type="file" name="items[${itemId}][file]" class="form-control" accept=".pdf,.doc,.docx,.mp3,.mp4">
                </div>
            </div>
            
            <div id="fields_youtube_${itemId}" class="hidden">
                <div class="form-group">
                    <label>YouTube Link</label>
                    <input type="url" name="items[${itemId}][youtube_link]" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                </div>
            </div>
            
            <div id="fields_url_${itemId}" class="hidden">
                <div class="form-group">
                    <label>External URL</label>
                    <input type="url" name="items[${itemId}][url]" class="form-control" placeholder="https://example.com/resource">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Item Title (Optional)</label>
                    <input type="text" name="items[${itemId}][title]" class="form-control" placeholder="e.g., Lesson 1">
                </div>
                <div class="form-group">
                    <label>Item Description (Optional)</label>
                    <input type="text" name="items[${itemId}][description]" class="form-control" placeholder="Brief description...">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    return itemId;
}

// Toggle item fields
function toggleItemFields(itemId, type) {
    ['file', 'youtube', 'url'].forEach(t => {
        const field = document.getElementById(`fields_${t}_${itemId}`);
        if (field) field.classList.toggle('hidden', t !== type);
    });
}

// Remove item
function removeItem(itemId) {
    if (confirm('Remove this item?')) {
        document.querySelector(`[data-item-id="${itemId}"]`).remove();
    }
}

// Suggest category
async function suggestCategory() {
    const title = document.querySelector('[name="title"]').value;
    const description = document.querySelector('[name="description"]').value;
    
    if (!title && !description) {
        alert('Please enter a title or description first');
        return;
    }
    
    const suggestionDiv = document.getElementById('aiSuggestion');
    suggestionDiv.style.display = 'block';
    suggestionDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> AI is analyzing...';
    
    try {
        const response = await fetch('{{ route("materials.suggest-category") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ title, description })
        });
        
        const data = await response.json();
        
        if (data.success && data.category) {
            document.getElementById('suggestedCategory').textContent = data.category;
            suggestionDiv.innerHTML = `<strong style="color: #3498db;"><i class="fas fa-lightbulb"></i> AI Suggestion:</strong><p style="margin: 5px 0 0 0; font-weight: 600;">${data.category}</p>`;
            
            // Auto-select the suggested category
            if (data.category === 'Madd Rules') document.getElementById('cat1').checked = true;
            else if (data.category === 'Idgham Billa Ghunnah') document.getElementById('cat2').checked = true;
            else if (data.category === 'Idgham Bi Ghunnah') document.getElementById('cat3').checked = true;
        } else {
            suggestionDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Could not determine category';
        }
    } catch (error) {
        suggestionDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error getting suggestion';
    }
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Add first item on load
document.addEventListener('DOMContentLoaded', () => addMaterialItem());
</script>
@endsection
