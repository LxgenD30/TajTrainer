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
        padding: 25px 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .section-header {
        font-family: 'El Messiri', sans-serif;
        font-size: 1.8rem;
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
    
    .search-type-btn[onclick*="pdf"].active {
        background: linear-gradient(135deg, #f1c40f, #f39c12);
        border-color: #d68910;
        color: #0a5c36;
        box-shadow: 0 4px 10px rgba(241, 196, 15, 0.4);
    }
    
    .search-type-btn[onclick*="youtube"].active {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        border-color: #a93226;
        color: white;
        box-shadow: 0 4px 10px rgba(231, 76, 60, 0.4);
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
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    
    .category-pill {
        padding: 10px 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .category-pill:has(input:checked) {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: white;
        border-color: #0a5c36;
    }
    
    .category-pill input[type="radio"] {
        display: none;
    }
    
    .category-pill label {
        color: inherit;
    }
    
    .category-selector {
        padding: 16px 20px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 3px solid #2a2a2a;
        border-radius: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
    }
    
    .category-selector::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .category-selector:hover::before {
        left: 100%;
    }
    
    .category-selector:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    
    .category-selector:has(input:checked) {
        background: linear-gradient(135deg, #0a5c36 0%, #1abc9c 100%);
        color: white;
        border-color: #0a5c36;
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.3);
    }
    
    .category-selector:has(input:checked) label {
        color: white;
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
        color: #333;
        line-height: 1.4;
    }
    
    @media (max-width: 992px) {
        .compact-grid,
        .form-row {
            grid-template-columns: 1fr;
        }
        .category-pills {
            grid-template-columns: 1fr;
        }
    }
    
    /* Custom Alert */
    .custom-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        max-width: 400px;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
    }
    
    .custom-alert.success {
        background: linear-gradient(135deg, #27ae60, #229954);
        color: white;
        border: 2px solid #1e8449;
    }
    
    .custom-alert.error {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: 2px solid #a93226;
    }
    
    .custom-alert.info {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        border: 2px solid #1f5f8b;
    }
    
    .custom-alert.warning {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: white;
        border: 2px solid #d68910;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Preview Modal */
    .preview-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    
    .preview-modal.active {
        display: flex;
    }
    
    .preview-content {
        background: white;
        border-radius: 15px;
        padding: 30px;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        position: relative;
    }
    
    .preview-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #e74c3c;
        color: white;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.2rem;
        font-weight: 700;
    }
</style>

<div class="create-container">
    <div style="background: linear-gradient(135deg, #0a5c36, #1abc9c); border: 3px solid #2a2a2a; border-radius: 15px; padding: 30px; margin-bottom: 20px; color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="margin: 0 0 10px 0; font-family: 'El Messiri', sans-serif; font-size: 2rem; font-weight: 700; color: white;">
                    <i class="fas fa-plus-circle"></i> Create New Material
                </h1>
                <p style="margin: 0; font-size: 1.1rem; opacity: 0.9; color: white;">Add educational resources for students</p>
            </div>
            <a href="{{ route('materials.index') }}" 
                style="display: inline-flex; align-items: center; gap: 10px; color: #1a1a1a; text-decoration: none; font-family: 'Cairo', sans-serif; font-weight: 700; font-size: 1rem; padding: 12px 24px; border-radius: 50px; background: #d4af37; border: 3px solid #b8860b; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2); white-space: nowrap;"
                onmouseover="this.style.background='#ffcc33'; this.style.transform='translateY(-2px)';"
                onmouseout="this.style.background='#d4af37'; this.style.transform='translateY(0)';">
                <i class="fas fa-arrow-left"></i> Back to Materials
            </a>
        </div>
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
            <!-- Left Column: Basic Info + Category -->
            <div class="section-card">
                <h2 class="section-header">
                    <i class="fas fa-info-circle"></i> Basic Information
                    <button type="button" id="generateInfoBtn" class="btn btn-secondary" onclick="generateBasicInfo()" style="margin-left: auto; padding: 8px 16px; font-size: 0.9rem;" disabled>
                        <i class="fas fa-magic"></i> Generate Information
                    </button>
                </h2>
                
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
                
                <!-- Category moved here -->
                <div class="form-group" style="margin-top: 25px;">
                    <label style="font-weight: 700; color: #0a5c36; font-size: 1.1rem; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-tag"></i> Category *
                        <button type="button" class="btn btn-secondary" onclick="suggestCategory()" style="margin-left: auto; padding: 6px 12px; font-size: 0.85rem;">
                            <i class="fas fa-magic"></i> AI Suggest
                        </button>
                    </label>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                        <div class="category-selector" onclick="document.getElementById('cat1').click()">
                            <input type="radio" name="category" id="cat1" value="Madd Rules" required style="display: none;">
                            <label for="cat1" style="cursor: pointer; display: flex; align-items: center; gap: 12px; width: 100%; height: 100%;">
                                <i class="fas fa-circle" style="font-size: 1.3rem; min-width: 24px; text-align: center;"></i>
                                <span style="font-weight: 700; flex: 1;">Madd Rules</span>
                            </label>
                        </div>
                        <div class="category-selector" onclick="document.getElementById('cat2').click()">
                            <input type="radio" name="category" id="cat2" value="Idgham Billa Ghunnah" required style="display: none;">
                            <label for="cat2" style="cursor: pointer; display: flex; align-items: center; gap: 12px; width: 100%; height: 100%;">
                                <i class="fas fa-wave-square" style="font-size: 1.3rem; min-width: 24px; text-align: center;"></i>
                                <span style="font-weight: 700; flex: 1;">Idgham Billa Ghunnah</span>
                            </label>
                        </div>
                        <div class="category-selector" onclick="document.getElementById('cat3').click()">
                            <input type="radio" name="category" id="cat3" value="Idgham Bi Ghunnah" required style="display: none;">
                            <label for="cat3" style="cursor: pointer; display: flex; align-items: center; gap: 12px; width: 100%; height: 100%;">
                                <i class="fas fa-water" style="font-size: 1.3rem; min-width: 24px; text-align: center;"></i>
                                <span style="font-weight: 700; flex: 1;">Idgham Bi Ghunnah</span>
                            </label>
                        </div>
                        <div class="category-selector" onclick="document.getElementById('cat4').click()">
                            <input type="radio" name="category" id="cat4" value="Others" required style="display: none;">
                            <label for="cat4" style="cursor: pointer; display: flex; align-items: center; gap: 12px; width: 100%; height: 100%;">
                                <i class="fas fa-ellipsis-h" style="font-size: 1.3rem; min-width: 24px; text-align: center;"></i>
                                <span style="font-weight: 700; flex: 1;">Others</span>
                            </label>
                        </div>
                    </div>
                    
                    <div id="aiSuggestion" style="margin-top: 15px; padding: 12px; background: rgba(52, 152, 219, 0.1); border-left: 4px solid #3498db; border-radius: 6px; display: none;">
                        <strong style="color: #3498db; font-size: 0.9rem;"><i class="fas fa-lightbulb"></i> AI Suggestion:</strong>
                        <p id="suggestedCategory" style="margin: 5px 0 0 0; font-weight: 600; color: #0a5c36;"></p>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Material Resources -->
            <div class="section-card">
                <h2 class="section-header">
                    <i class="fas fa-layer-group"></i> Material Resources
                    <button type="button" class="btn btn-secondary" onclick="addMaterialItem()" style="margin-left: auto; padding: 8px 16px; font-size: 0.9rem;">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </h2>
                
                <div id="materialItemsContainer"></div>
            </div>
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

<!-- Preview Modal -->
<div id="previewModal" class="preview-modal">
    <div class="preview-content">
        <button class="preview-close" onclick="closePreview()">&times;</button>
        <h2 style="margin: 0 0 20px 0; font-family: 'El Messiri', sans-serif; color: #0a5c36;">
            <i class="fas fa-eye"></i> Preview Result
        </h2>
        <div id="previewBody"></div>
        <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
            <button class="btn btn-secondary" onclick="closePreview()">
                <i class="fas fa-times"></i> Close
            </button>
            <button class="btn btn-primary" id="addPreviewBtn" onclick="addPreviewResult()">
                <i class="fas fa-plus"></i> Add to Material
            </button>
        </div>
    </div>
</div>

<script>
let itemCounter = 0;
let currentSearchType = 'pdf';
let previewResult = null;

// Custom Alert System
function showCustomAlert(message, type = 'info') {
    console.log(`[ALERT ${type.toUpperCase()}]:`, message);
    
    const alert = document.createElement('div');
    alert.className = `custom-alert ${type}`;
    
    const icon = type === 'success' ? 'fa-check-circle' : 
                 type === 'error' ? 'fa-exclamation-circle' : 
                 type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
    
    alert.innerHTML = `
        <i class="fas ${icon}" style="font-size: 1.5rem;"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

// Preview Modal Functions
function openPreview(result) {
    console.log('[PREVIEW] Opening preview for result:', result);
    previewResult = result;
    
    const modal = document.getElementById('previewModal');
    const body = document.getElementById('previewBody');
    
    body.innerHTML = `
        ${result.is_pdf ? '<div style="background: #e74c3c; color: white; padding: 8px 12px; border-radius: 6px; display: inline-block; font-weight: 700; margin-bottom: 15px;"><i class="fas fa-file-pdf"></i> PDF Document</div>' : ''}
        ${result.video_id || result.is_youtube ? '<div style="background: #ff0000; color: white; padding: 8px 12px; border-radius: 6px; display: inline-block; font-weight: 700; margin-bottom: 15px;"><i class="fab fa-youtube"></i> YouTube Video</div>' : ''}
        
        <h3 style="margin: 15px 0 10px 0; color: #2a2a2a;">${escapeHtml(result.title)}</h3>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0; border: 2px solid #e0e0e0;">
            <strong style="color: #0a5c36;"><i class="fas fa-link"></i> Source URL:</strong>
            <p style="margin: 5px 0 0 0; word-break: break-all;">
                <a href="${escapeHtml(result.url)}" target="_blank" style="color: #3498db; text-decoration: none; font-weight: 600;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                    ${escapeHtml(result.url)} <i class="fas fa-external-link-alt" style="font-size: 0.85rem;"></i>
                </a>
            </p>
        </div>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0; border: 2px solid #e0e0e0;">
            <strong style="color: #0a5c36;"><i class="fas fa-align-left"></i> Description:</strong>
            <p style="margin: 5px 0 0 0; line-height: 1.6; color: #333;">${escapeHtml(result.content)}</p>
        </div>
        
        ${result.is_pdf ? `<div style="background: rgba(39, 174, 96, 0.1); padding: 15px; border-radius: 8px; border: 2px solid #27ae60; margin: 15px 0;"><i class="fas fa-download" style="color: #27ae60;"></i> <strong style="color: #27ae60;">PDF will be downloaded to server</strong></div>` : ''}
    `;
    
    modal.classList.add('active');
}

function closePreview() {
    console.log('[PREVIEW] Closing preview modal');
    document.getElementById('previewModal').classList.remove('active');
    previewResult = null;
}

function addPreviewResult() {
    if (!previewResult) return;
    console.log('[PREVIEW] Adding result to material:', previewResult);
    addFromSearch(window.searchResults.indexOf(previewResult));
    closePreview();
}

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
        showCustomAlert('Please enter a search query', 'warning');
        return;
    }
    
    console.log('[SEARCH] Starting search:', { query, type: currentSearchType });
    
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
        console.log('[SEARCH] Response received:', data);
        
        if (data.success) {
            console.log('[SEARCH] Results found:', data.results.length);
            displayResults(data.results);
        } else {
            console.error('[SEARCH] Search failed:', data.message);
            resultsDiv.innerHTML = `<div style="padding: 20px; background: rgba(231, 76, 60, 0.2); border: 2px solid rgba(231, 76, 60, 0.5); border-radius: 8px; color: white; margin-top: 15px;"><i class="fas fa-exclamation-triangle"></i> ${data.message || 'Search failed'}</div>`;
            showCustomAlert(data.message || 'Search failed', 'error');
        }
    } catch (error) {
        console.error('[SEARCH] Error:', error);
        resultsDiv.innerHTML = '<div style="padding: 20px; background: rgba(231, 76, 60, 0.2); border: 2px solid rgba(231, 76, 60, 0.5); border-radius: 8px; color: white; margin-top: 15px;"><i class="fas fa-exclamation-triangle"></i> An error occurred during search</div>';
        showCustomAlert('An error occurred during search', 'error');
    }
}

// Display search results
function displayResults(results) {
    const resultsDiv = document.getElementById('searchResults');
    
    if (!results || results.length === 0) {
        console.log('[SEARCH] No results found');
        resultsDiv.innerHTML = '<div style="padding: 20px; background: rgba(241, 196, 15, 0.2); border: 2px solid rgba(241, 196, 15, 0.5); border-radius: 8px; color: white; margin-top: 15px;"><i class="fas fa-info-circle"></i> No results found</div>';
        return;
    }
    
    console.log('[DISPLAY] Rendering', results.length, 'results');
    results.forEach((result, index) => {
        console.log(`[RESULT ${index + 1}]:`, {
            title: result.title,
            url: result.url,
            isPdf: result.is_pdf,
            isVideo: result.video_id || result.is_youtube ? true : false,
            contentPreview: result.content.substring(0, 50) + '...'
        });
    });
    
    let html = '<div class="results-grid">';
    results.forEach((result, index) => {
        html += `
            <div class="result-card">
                ${result.is_pdf ? '<div style="background: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; display: inline-block; font-size: 0.8rem; font-weight: 700; margin-bottom: 8px;"><i class="fas fa-file-pdf"></i> PDF</div>' : ''}
                ${result.video_id || result.is_youtube ? '<div style="background: #ff0000; color: white; padding: 4px 8px; border-radius: 4px; display: inline-block; font-size: 0.8rem; font-weight: 700; margin-bottom: 8px;"><i class="fab fa-youtube"></i> VIDEO</div>' : ''}
                <h4>${escapeHtml(result.title)}</h4>
                <p>${escapeHtml(result.content.substring(0, 100))}...</p>
                <div style="margin-top: 10px; display: flex; gap: 8px;">
                    <button type="button" onclick="openPreview(window.searchResults[${index}])" style="flex: 1; padding: 8px 12px; background: #3498db; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;"><i class="fas fa-eye"></i> Preview</button>
                    <button type="button" onclick="addFromSearch(${index})" style="flex: 1; padding: 8px 12px; background: #27ae60; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;"><i class="fas fa-plus"></i> Add</button>
                </div>
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
    console.log('[ADD] Adding search result to material:', result);
    
    const itemId = addMaterialItem();
    
    // Check if it's a YouTube video (either has video_id or is_youtube flag)
    if (result.video_id || result.is_youtube) {
        console.log('[ADD] Type: YouTube video, ID:', result.video_id);
        document.getElementById(`type_youtube_${itemId}`).checked = true;
        toggleItemFields(itemId, 'youtube');
        document.querySelector(`[name="items[${itemId}][youtube_link]"]`).value = result.url;
    } else if (result.is_pdf) {
        console.log('[ADD] Type: PDF Document');
        document.getElementById(`type_document_${itemId}`).checked = true;
        toggleItemFields(itemId, 'document');
        
        // Add hidden field for PDF URL to be downloaded by backend
        const documentField = document.getElementById(`fields_document_${itemId}`);
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `items[${itemId}][pdf_url]`;
        hiddenInput.value = result.url;
        documentField.appendChild(hiddenInput);
        
        // Show indicator that PDF will be downloaded
        const indicator = document.createElement('div');
        indicator.style.cssText = 'margin-top: 10px; padding: 12px; background: rgba(231, 76, 60, 0.1); border: 2px solid #e74c3c; border-radius: 8px; color: #e74c3c; font-weight: 600;';
        indicator.innerHTML = '<i class="fas fa-download"></i> PDF will be downloaded from search result';
        documentField.appendChild(indicator);
        
        console.log('[ADD] PDF will be auto-downloaded from:', result.url);
    } else {
        console.log('[ADD] Type: Document (general link)');
        document.getElementById(`type_document_${itemId}`).checked = true;
        toggleItemFields(itemId, 'document');
        console.log('[ADD] Link:', result.url);
    }
    
    document.querySelector(`[name="items[${itemId}][title]"]`).value = result.title;
    document.querySelector(`[name="items[${itemId}][description]"]`).value = result.content.substring(0, 200);
    
    showCustomAlert('Resource added to material items', 'success');
    
    // Update Generate Information button state
    updateGenerateButtonState();
    
    // Scroll to the new item
    document.querySelector(`[data-item-id="${itemId}"]`).scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Add material item
function addMaterialItem() {
    itemCounter++;
    const itemId = itemCounter;
    
    // Get current number of visible items
    const currentItems = document.querySelectorAll('[data-item-id]').length;
    const displayNumber = currentItems + 1;
    
    console.log('[ITEM] Adding new material item. Counter ID:', itemId, 'Display #:', displayNumber);
    const container = document.getElementById('materialItemsContainer');
    
    const html = `
        <div class="item-card" data-item-id="${itemId}" data-display-number="${displayNumber}">
            <div class="item-header">
                <strong style="font-size: 1.1rem; color: #0a5c36;" class="item-number">Item #${displayNumber}</strong>
                <button type="button" class="remove-btn" onclick="removeItem(${itemId})">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            
            <div class="radio-group">
                <div class="radio-option">
                    <input type="radio" id="type_file_${itemId}" name="items[${itemId}][type]" value="image" onchange="toggleItemFields(${itemId}, 'images')" checked required>
                    <label for="type_file_${itemId}">Images</label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="type_document_${itemId}" name="items[${itemId}][type]" value="file" onchange="toggleItemFields(${itemId}, 'document')">
                    <label for="type_document_${itemId}">Document</label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="type_youtube_${itemId}" name="items[${itemId}][type]" value="youtube" onchange="toggleItemFields(${itemId}, 'youtube')">
                    <label for="type_youtube_${itemId}">YouTube</label>
                </div>
            </div>
            
            <div id="fields_images_${itemId}" class="">
                <div class="form-group">
                    <label>Upload Images</label>
                    <input type="file" name="items[${itemId}][file]" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp,image/*" onchange="handleFileUpload(${itemId}, 'images', this)">
                    <div id="file_preview_images_${itemId}" class="file-preview" style="display: none; margin-top: 10px; padding: 12px; background: rgba(39, 174, 96, 0.1); border: 2px solid #27ae60; border-radius: 8px; color: #27ae60; font-weight: 600;">
                        <i class="fas fa-check-circle"></i> <span class="file-name"></span>
                    </div>
                </div>
            </div>
            
            <div id="fields_document_${itemId}" class="hidden">
                <div class="form-group">
                    <label>Upload Document</label>
                    <input type="file" name="items[${itemId}][file]" class="form-control" accept=".pdf,.doc,.docx" onchange="handleFileUpload(${itemId}, 'document', this)">
                    <div id="file_preview_document_${itemId}" class="file-preview" style="display: none; margin-top: 10px; padding: 12px; background: rgba(231, 76, 60, 0.1); border: 2px solid #e74c3c; border-radius: 8px; color: #e74c3c; font-weight: 600;">
                        <i class="fas fa-file-pdf"></i> <span class="file-name"></span>
                    </div>
                </div>
            </div>
            
            <div id="fields_youtube_${itemId}" class="hidden">
                <div class="form-group">
                    <label>YouTube Link</label>
                    <input type="url" name="items[${itemId}][youtube_link]" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
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
    
    // Update Generate Information button state
    updateGenerateButtonState();
    
    return itemId;
}

// Toggle item fields
function toggleItemFields(itemId, type) {
    ['images', 'document', 'youtube'].forEach(t => {
        const field = document.getElementById(`fields_${t}_${itemId}`);
        if (field) field.classList.toggle('hidden', t !== type);
    });
}

// Handle file upload preview
function handleFileUpload(itemId, type, inputElement) {
    const file = inputElement.files[0];
    if (file) {
        const previewDiv = document.getElementById(`file_preview_${type}_${itemId}`);
        const fileNameSpan = previewDiv.querySelector('.file-name');
        
        // Get file size in readable format
        const fileSize = (file.size / 1024).toFixed(2); // KB
        const sizeText = fileSize > 1024 ? `${(fileSize / 1024).toFixed(2)} MB` : `${fileSize} KB`;
        
        // Determine file type icon and color
        let icon = 'fa-file';
        let backgroundColor = 'rgba(52, 152, 219, 0.1)';
        let borderColor = '#3498db';
        let textColor = '#3498db';
        
        if (type === 'document') {
            if (file.name.toLowerCase().endsWith('.pdf')) {
                icon = 'fa-file-pdf';
                backgroundColor = 'rgba(231, 76, 60, 0.1)';
                borderColor = '#e74c3c';
                textColor = '#e74c3c';
            } else if (file.name.toLowerCase().match(/\.(doc|docx)$/)) {
                icon = 'fa-file-word';
                backgroundColor = 'rgba(41, 128, 185, 0.1)';
                borderColor = '#2980b9';
                textColor = '#2980b9';
            }
        } else if (type === 'images') {
            icon = 'fa-image';
            backgroundColor = 'rgba(39, 174, 96, 0.1)';
            borderColor = '#27ae60';
            textColor = '#27ae60';
        }
        
        // Update preview style
        previewDiv.style.background = backgroundColor;
        previewDiv.style.borderColor = borderColor;
        previewDiv.style.color = textColor;
        
        // Update preview content
        previewDiv.innerHTML = `
            <i class="fas ${icon}" style="font-size: 1.2rem;"></i> 
            <strong>${file.name}</strong> (${sizeText})
            <div style="margin-top: 5px; font-size: 0.85rem; opacity: 0.8;">
                <i class="fas fa-check-circle"></i> File ready to upload
            </div>
        `;
        
        previewDiv.style.display = 'block';
        
        console.log(`[FILE] ${type} selected:`, file.name, sizeText);
        showCustomAlert(`${type === 'document' ? 'Document' : 'Image'} selected: ${file.name}`, 'success');
    }
}

// Remove item
function removeItem(itemId) {
    if (confirm('Remove this item?')) {
        console.log('[ITEM] Removing item #' + itemId);
        document.querySelector(`[data-item-id="${itemId}"]`).remove();
        
        // Renumber remaining items
        const remainingItems = document.querySelectorAll('[data-item-id]');
        remainingItems.forEach((item, index) => {
            const displayNumber = index + 1;
            item.setAttribute('data-display-number', displayNumber);
            item.querySelector('.item-number').textContent = `Item #${displayNumber}`;
        });
        
        console.log('[ITEM] Renumbered', remainingItems.length, 'remaining items');
        showCustomAlert('Item removed and list renumbered', 'info');
        
        // Update Generate Information button state
        updateGenerateButtonState();
    }
}

// Suggest category
async function suggestCategory() {
    const title = document.querySelector('[name="title"]').value;
    const description = document.querySelector('[name="description"]').value;
    
    if (!title && !description) {
        showCustomAlert('Please enter a title or description first', 'warning');
        return;
    }
    
    console.log('[AI] Requesting category suggestion for:', { title, description });
    
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
        console.log('[AI] Response received:', data);
        
        if (data.success && data.category) {
            const category = data.category;
            console.log('[AI] Suggested category:', category);
            
            // Update the suggestion div with new HTML including the category
            suggestionDiv.innerHTML = `<strong style="color: #3498db;"><i class="fas fa-lightbulb"></i> AI Suggestion:</strong><p id="suggestedCategory" style="margin: 5px 0 0 0; font-weight: 600;">${category}</p>`;
            
            // Auto-select the suggested category radio button
            if (category === 'Madd Rules') {
                document.getElementById('cat1').checked = true;
                console.log('[AI] Auto-selected: Madd Rules');
            } else if (category === 'Idgham Billa Ghunnah') {
                document.getElementById('cat2').checked = true;
                console.log('[AI] Auto-selected: Idgham Billa Ghunnah');
            } else if (category === 'Idgham Bi Ghunnah') {
                document.getElementById('cat3').checked = true;
                console.log('[AI] Auto-selected: Idgham Bi Ghunnah');
            }
            
            showCustomAlert('AI suggested: ' + category, 'success');
        } else {
            console.warn('[AI] Could not determine category:', data);
            suggestionDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Could not determine category. Please select manually.';
            showCustomAlert('Could not determine category', 'warning');
        }
    } catch (error) {
        console.error('[AI] Error:', error);
        suggestionDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error getting suggestion';
        showCustomAlert('Error getting AI suggestion', 'error');
    }
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Check if materials exist and update Generate Information button state
function updateGenerateButtonState() {
    const items = document.querySelectorAll('[data-item-id]');
    const generateBtn = document.getElementById('generateInfoBtn');
    
    if (generateBtn) {
        if (items.length === 0) {
            generateBtn.disabled = true;
            generateBtn.style.opacity = '0.5';
            generateBtn.style.cursor = 'not-allowed';
            generateBtn.title = 'Please add at least one material item first';
        } else {
            generateBtn.disabled = false;
            generateBtn.style.opacity = '1';
            generateBtn.style.cursor = 'pointer';
            generateBtn.title = 'Generate title and description based on added materials';
        }
    }
}

// Generate basic information using AI
async function generateBasicInfo() {
    // Get items that have been added
    const items = document.querySelectorAll('[data-item-id]');
    
    if (items.length === 0) {
        showCustomAlert('Please add at least one material item first', 'warning');
        return;
    }
    
    // Collect information from items
    let context = 'Based on these materials:\n';
    items.forEach((item, index) => {
        const title = item.querySelector('[name*="[title]"]')?.value || '';
        const desc = item.querySelector('[name*="[description]"]')?.value || '';
        if (title || desc) {
            context += `${index + 1}. ${title}${desc ? ': ' + desc : ''}\n`;
        }
    });
    
    console.log('[AI] Generating basic info from context:', context);
    
    showCustomAlert('AI is generating title and description...', 'info');
    
    try {
        const response = await fetch('{{ route("materials.generate-info") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ context })
        });
        
        const data = await response.json();
        console.log('[AI] Generated info:', data);
        
        if (data.success) {
            if (data.title) {
                document.querySelector('[name="title"]').value = data.title;
                console.log('[AI] Title generated:', data.title);
            }
            if (data.description) {
                document.querySelector('[name="description"]').value = data.description;
                console.log('[AI] Description generated:', data.description);
            }
            showCustomAlert('Basic information generated successfully!', 'success');
        } else {
            showCustomAlert(data.message || 'Could not generate information', 'warning');
        }
    } catch (error) {
        console.error('[AI] Error:', error);
        showCustomAlert('Error generating information', 'error');
    }
}

// Page ready - no auto-add items, teacher adds manually
document.addEventListener('DOMContentLoaded', () => {
    console.log('[INIT] Create materials page loaded. Teacher can add items manually.');
    updateGenerateButtonState();
});
</script>
@endsection
