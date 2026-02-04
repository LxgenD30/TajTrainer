@extends('layouts.app')

@section('content')
<style>
    /* Page Container */
    .materials-create-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 40px 20px;
    }

    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, #0a5c36 0%, #1abc9c 100%);
        border: 3px solid #2a2a2a;
        border-radius: 12px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }

    .welcome-banner h1 {
        color: white;
        font-size: 2rem;
        font-weight: 800;
        margin: 0 0 15px 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    .welcome-banner .subtitle {
        color: #e0f2f1;
        font-size: 1.1rem;
        font-weight: 500;
        margin: 0;
    }

    /* Back Button */
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: rgba(255,255,255,0.15);
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 8px;
        color: white;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s ease;
        margin-top: 20px;
    }

    .back-btn:hover {
        background: rgba(255,255,255,0.25);
        border-color: rgba(255,255,255,0.5);
        color: white;
        transform: translateX(-5px);
    }

    /* Form Card */
    .form-card {
        background: white;
        border: 2px solid #2a2a2a;
        border-radius: 12px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .form-card h2 {
        font-size: 1.6rem;
        font-weight: 800;
        color: #2a2a2a;
        margin: 0 0 25px 0;
        padding-bottom: 15px;
        border-bottom: 3px solid #2a2a2a;
    }

    /* Search Section */
    .search-section {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        border: 3px solid #2a2a2a;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }

    .search-section h2 {
        color: white;
        font-size: 1.6rem;
        font-weight: 800;
        margin: 0 0 20px 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        border-bottom: none;
    }

    .search-input-group {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }

    .search-input {
        flex: 1;
        padding: 14px 20px;
        border: 2px solid #2a2a2a;
        border-radius: 8px;
        font-size: 1.05rem;
        font-weight: 500;
    }

    .search-btn {
        padding: 14px 32px;
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        border: 2px solid #2a2a2a;
        border-radius: 8px;
        color: white;
        font-weight: 700;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }

    /* Search Results */
    .search-results {
        display: none;
        margin-top: 20px;
    }

    .search-results.active {
        display: block;
    }

    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
    }

    .result-card {
        background: white;
        border: 2px solid #2a2a2a;
        border-radius: 8px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .result-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        border-color: #3498db;
    }

    .result-card h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2a2a2a;
        margin: 0 0 10px 0;
    }

    .result-card .url {
        color: #3498db;
        font-size: 0.9rem;
        margin-bottom: 10px;
        word-break: break-all;
    }

    .result-card .content {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    /* Form Sections */
    .form-section {
        background: #f9f9f9;
        border: 2px solid #2a2a2a;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 25px;
    }

    .form-section h3 {
        font-size: 1.3rem;
        font-weight: 800;
        color: #2a2a2a;
        margin: 0 0 20px 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 1.05rem;
        font-weight: 700;
        color: #2a2a2a;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #2a2a2a;
        border-radius: 6px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    select.form-control {
        cursor: pointer;
        background: white;
    }

    /* Material Items Container */
    .items-container {
        margin-top: 20px;
    }

    .material-item {
        background: white;
        border: 2px solid #3498db;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        position: relative;
    }

    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e0e0e0;
    }

    .item-number {
        font-size: 1.2rem;
        font-weight: 800;
        color: #3498db;
    }

    .remove-item-btn {
        padding: 6px 16px;
        background: #e74c3c;
        border: 2px solid #c0392b;
        border-radius: 6px;
        color: white;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .remove-item-btn:hover {
        background: #c0392b;
        transform: scale(1.05);
    }

    .add-item-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        border: 2px solid #2a2a2a;
        border-radius: 8px;
        color: white;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .add-item-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }

    /* Submit Button */
    .submit-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        border: 3px solid #2a2a2a;
        border-radius: 8px;
        color: white;
        font-size: 1.2rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }

    /* Alert Messages */
    .alert {
        padding: 16px 20px;
        border: 2px solid;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .alert-info {
        background: #d1ecf1;
        border-color: #17a2b8;
        color: #0c5460;
    }

    /* Loading Spinner */
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 20px;
    }

    .loading-spinner.active {
        display: block;
    }

    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Radio Button Group */
    .radio-group {
        display: flex;
        gap: 20px;
        margin-top: 10px;
    }

    .radio-option {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .radio-option input[type="radio"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .radio-option label {
        margin: 0;
        cursor: pointer;
        font-weight: 600;
    }

    .hidden {
        display: none;
    }
</style>

<div class="materials-create-page">
    <div class="container">
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <h1>📚 Create New Teaching Material</h1>
            <p class="subtitle">Add educational resources with multiple files, videos, and links</p>
            <a href="{{ route('materials.index') }}" class="back-btn">
                ← Back to Materials
            </a>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <h2>🔍 Search Online Resources</h2>
            <p style="color: white; margin-bottom: 15px;">Find educational materials from across the internet using AI-powered search</p>
            
            <div class="search-input-group">
                <input type="text" 
                       id="searchQuery" 
                       class="search-input" 
                       placeholder="Search for Tajweed materials, lessons, guides..."
                       onkeypress="if(event.key === 'Enter') searchOnline()">
                <button onclick="searchOnline()" class="search-btn">
                    Search
                </button>
            </div>

            <div id="searchResults" class="search-results"></div>
            <div id="loadingSpinner" class="loading-spinner">
                <div class="spinner"></div>
                <p style="color: white; margin-top: 10px;">Searching...</p>
            </div>
        </div>

        <!-- Main Form -->
        <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Material Information Section -->
            <div class="form-card">
                <h2>📖 Material Information</h2>
                
                <div class="alert alert-info">
                    <strong>Note:</strong> All materials are automatically public and visible to all students.
                </div>

                <div class="form-group">
                    <label for="title">Material Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="form-control" 
                           placeholder="e.g., Tajweed Rules Basics"
                           required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control" 
                              rows="4"
                              placeholder="Describe what students will learn from this material..."></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="">Auto-detect using AI</option>
                        <option value="Madd Rules">Madd Rules</option>
                        <option value="Idgham Billa Ghunnah">Idgham Billa Ghunnah</option>
                        <option value="Idgham Bi Ghunnah">Idgham Bi Ghunnah</option>
                    </select>
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Leave as "Auto-detect" to let AI categorize this material based on title and description
                    </small>
                </div>

                <div class="form-group">
                    <label for="thumbnail">Material Thumbnail</label>
                    <input type="file" 
                           id="thumbnail" 
                           name="thumbnail" 
                           class="form-control"
                           accept="image/*">
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Optional - Will auto-extract from YouTube if not provided
                    </small>
                </div>
            </div>

            <!-- Material Items Section -->
            <div class="form-card">
                <h2>📎 Material Items (Files, Videos, Links)</h2>
                <p style="color: #666; margin-bottom: 20px;">Add multiple resources under this material</p>

                <div id="itemsContainer" class="items-container">
                    <!-- Items will be added here dynamically -->
                </div>

                <button type="button" onclick="addMaterialItem()" class="add-item-btn">
                    + Add Item
                </button>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn">
                Create Material
            </button>
        </form>
    </div>
</div>

<script>
let itemCounter = 0;

// Search online resources using Tavily API
async function searchOnline() {
    const query = document.getElementById('searchQuery').value.trim();
    
    if (!query) {
        showMessage('Please enter a search query', 'warning');
        return;
    }

    const resultsDiv = document.getElementById('searchResults');
    const spinner = document.getElementById('loadingSpinner');
    
    resultsDiv.classList.remove('active');
    resultsDiv.innerHTML = '';
    spinner.classList.add('active');

    try {
        const response = await fetch('{{ route("materials.search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ query: query })
        });

        const data = await response.json();
        spinner.classList.remove('active');

        if (data.success && data.results.length > 0) {
            displayResults(data.results);
        } else {
            resultsDiv.innerHTML = '<p style="color: white;">No results found. Try different keywords.</p>';
            resultsDiv.classList.add('active');
        }
    } catch (error) {
        spinner.classList.remove('active');
        console.error('Search error:', error);
        showMessage('Search failed. Please try again.', 'danger');
    }
}

// Display search results
function displayResults(results) {
    const resultsDiv = document.getElementById('searchResults');
    let html = '<div class="results-grid">';
    
    results.forEach((result, index) => {
        html += `
            <div class="result-card" onclick="addResultAsItem(${index})">
                <h3>${escapeHtml(result.title)}</h3>
                <div class="url">${escapeHtml(result.url)}</div>
                <div class="content">${escapeHtml(result.content.substring(0, 150))}...</div>
            </div>
        `;
    });
    
    html += '</div>';
    resultsDiv.innerHTML = html;
    resultsDiv.classList.add('active');
    
    // Store results for later use
    window.searchResults = results;
}

// Add search result as a material item
function addResultAsItem(index) {
    const result = window.searchResults[index];
    
    // Add new item
    const itemId = addMaterialItem();
    
    // Fill in the details
    setTimeout(() => {
        const itemDiv = document.querySelector(`[data-item-id="${itemId}"]`);
        if (itemDiv) {
            // Select URL type
            const urlRadio = itemDiv.querySelector('input[value="url"]');
            if (urlRadio) {
                urlRadio.checked = true;
                urlRadio.dispatchEvent(new Event('change'));
            }
            
            // Fill in fields
            setTimeout(() => {
                const titleInput = itemDiv.querySelector('input[name*="[title]"]');
                const descInput = itemDiv.querySelector('textarea[name*="[description]"]');
                const urlInput = itemDiv.querySelector('input[name*="[url]"]');
                
                if (titleInput) titleInput.value = result.title;
                if (descInput) descInput.value = result.content.substring(0, 200);
                if (urlInput) urlInput.value = result.url;
            }, 100);
        }
    }, 100);
    
    showMessage('Resource added! You can edit or add more items.', 'success');
    
    // Scroll to the new item
    setTimeout(() => {
        const itemDiv = document.querySelector(`[data-item-id="${itemId}"]`);
        if (itemDiv) {
            itemDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, 200);
}

// Add a new material item
function addMaterialItem() {
    const container = document.getElementById('itemsContainer');
    const itemId = ++itemCounter;
    
    const itemHtml = `
        <div class="material-item" data-item-id="${itemId}">
            <div class="item-header">
                <div class="item-number">Item #${itemId}</div>
                <button type="button" class="remove-item-btn" onclick="removeItem(${itemId})">Remove</button>
            </div>
            
            <div class="form-group">
                <label>Item Type *</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" 
                               id="type_file_${itemId}" 
                               name="items[${itemId}][type]" 
                               value="file" 
                               onchange="toggleItemFields(${itemId}, 'file')"
                               required>
                        <label for="type_file_${itemId}">Upload File</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" 
                               id="type_youtube_${itemId}" 
                               name="items[${itemId}][type]" 
                               value="youtube"
                               onchange="toggleItemFields(${itemId}, 'youtube')">
                        <label for="type_youtube_${itemId}">YouTube Video</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" 
                               id="type_url_${itemId}" 
                               name="items[${itemId}][type]" 
                               value="url"
                               onchange="toggleItemFields(${itemId}, 'url')">
                        <label for="type_url_${itemId}">External Link</label>
                    </div>
                </div>
            </div>
            
            <div id="fields_file_${itemId}" class="hidden">
                <div class="form-group">
                    <label>Upload File</label>
                    <input type="file" 
                           name="items[${itemId}][file]" 
                           class="form-control"
                           accept=".pdf,.doc,.docx,.mp3,.mp4">
                </div>
            </div>
            
            <div id="fields_youtube_${itemId}" class="hidden">
                <div class="form-group">
                    <label>YouTube Link</label>
                    <input type="url" 
                           name="items[${itemId}][youtube_link]" 
                           class="form-control"
                           placeholder="https://www.youtube.com/watch?v=...">
                </div>
            </div>
            
            <div id="fields_url_${itemId}" class="hidden">
                <div class="form-group">
                    <label>External URL</label>
                    <input type="url" 
                           name="items[${itemId}][url]" 
                           class="form-control"
                           placeholder="https://example.com/resource">
                </div>
            </div>
            
            <div class="form-group">
                <label>Item Title (Optional)</label>
                <input type="text" 
                       name="items[${itemId}][title]" 
                       class="form-control"
                       placeholder="e.g., Lesson 1 - Introduction">
            </div>
            
            <div class="form-group">
                <label>Item Description (Optional)</label>
                <textarea name="items[${itemId}][description]" 
                          class="form-control" 
                          rows="2"
                          placeholder="Brief description of this item..."></textarea>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    return itemId;
}

// Toggle item type fields
function toggleItemFields(itemId, type) {
    const types = ['file', 'youtube', 'url'];
    types.forEach(t => {
        const field = document.getElementById(`fields_${t}_${itemId}`);
        if (field) {
            field.classList.toggle('hidden', t !== type);
        }
    });
}

// Remove item
function removeItem(itemId) {
    const item = document.querySelector(`[data-item-id="${itemId}"]`);
    if (item) {
        if (confirm('Are you sure you want to remove this item?')) {
            item.remove();
        }
    }
}

// Show message
function showMessage(message, type) {
    alert(message);
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addMaterialItem();
});
</script>
@endsection
