@extends('layouts.dashboard')

@section('title', 'Edit Assignment')
@section('user-role', 'Teacher • Edit Assignment')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
<style>
    .page-header {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 15px;
        padding: 25px 30px;
        margin-bottom: 25px;
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }
    
    .page-header h1 {
        color: white;
        font-size: 1.8rem;
        margin: 0 0 5px 0;
        font-weight: 700;
    }
    
    .page-header p {
        opacity: 0.95;
        font-size: 0.95rem;
        margin: 0;
    }
    
    .header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-shrink: 0;
    }
    
    .form-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    .form-section {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.02), rgba(26, 188, 156, 0.02));
        padding: 20px;
        border-radius: 12px;
        border: 3px solid #000000;
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        border-color: #000000;
    }
    
    .form-section.gold {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.03), rgba(241, 196, 15, 0.03));
        border: 3px solid #000000;
    }
    
    .form-section.gold:hover {
        border-color: #000000;
    }
    
    .section-title {
        color: #000000;
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .section-title.gold {
        color: #d4af37;
    }
    
    .section-desc {
        color: #000000;
        font-size: 0.87rem;
        margin-bottom: 15px;
        line-height: 1.5;
    }
    
    .form-label {
        display: block;
        color: #000000;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }
    
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 11px 14px;
        background: white;
        color: #000000;
        border: 3px solid #000000;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #1abc9c;
        box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.08);
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 100px;
        line-height: 1.6;
    }
    
    .filter-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    
    .filter-btn {
        padding: 7px 15px;
        background: rgba(10, 92, 54, 0.08);
        color: #0a5c36;
        border: 2px solid rgba(10, 92, 54, 0.2);
        border-radius: 20px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    
    .filter-btn:hover {
        background: rgba(10, 92, 54, 0.15);
        transform: translateY(-1px);
    }
    
    .filter-btn.active {
        background: linear-gradient(135deg, #1abc9c, #0a5c36);
        color: white;
        border-color: #0a5c36;
        box-shadow: 0 3px 10px rgba(10, 92, 54, 0.3);
    }
    
    .radio-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .radio-option {
        display: flex;
        align-items: flex-start;
        cursor: pointer;
        background: white;
        padding: 13px;
        border-radius: 10px;
        border: 3px solid #000000;
        transition: all 0.3s ease;
    }
    
    .radio-option:hover {
        border-color: #d4af37;
        background: rgba(212, 175, 55, 0.04);
        transform: translateX(3px);
    }
    
    .radio-option input[type="radio"]:checked + .radio-label {
        color: #000000;
    }
    
    .radio-option input[type="radio"]:checked {
        accent-color: #d4af37;
    }
    
    .radio-option input[type="radio"] {
        margin-right: 11px;
        margin-top: 3px;
        width: 17px;
        height: 17px;
        cursor: pointer;
    }
    
    .radio-label {
        flex: 1;
    }
    
    .radio-title {
        color: #d4af37;
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 3px;
    }
    
    .radio-desc {
        color: #000000;
        font-size: 0.82rem;
        line-height: 1.4;
    }
    
    .two-column-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }
    
    @media (max-width: 968px) {
        .two-column-grid {
            grid-template-columns: 1fr;
        }
        
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .header-actions {
            width: 100%;
            flex-direction: column;
        }
    }
    
    .verse-display {
        background: linear-gradient(135deg, rgba(10, 92, 54, 0.04), rgba(26, 188, 156, 0.04));
        padding: 18px;
        border-radius: 10px;
        border: 2px solid rgba(26, 188, 156, 0.4);
        margin-top: 15px;
    }
    
    .verse-arabic {
        text-align: right;
        direction: rtl;
        font-family: 'Amiri', 'Traditional Arabic', serif;
        font-size: 1.5rem;
        line-height: 2;
        color: #0a5c36;
        margin-bottom: 12px;
    }
    
    .verse-translation {
        color: #555;
        font-size: 0.9rem;
        line-height: 1.6;
        padding: 12px;
        background: white;
        border-radius: 8px;
        border-left: 4px solid #1abc9c;
    }
    
    .btn-insert {
        padding: 9px 18px;
        background: rgba(26, 188, 156, 0.12);
        color: #1abc9c;
        border: 2px solid #1abc9c;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        margin-top: 12px;
    }
    
    .btn-insert:hover {
        background: rgba(26, 188, 156, 0.22);
        transform: translateY(-2px);
    }
    
    .datetime-input {
        padding: 9px 13px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        font-size: 0.88rem;
        background: rgba(0, 0, 0, 0.2);
        color: white;
        font-family: 'Cairo', sans-serif;
    }
    
    .datetime-input:focus {
        outline: none;
        border-color: #d4af37;
        background: rgba(0, 0, 0, 0.3);
    }
    
    .btn-cancel {
        padding: 11px 22px;
        background: rgba(0, 0, 0, 0.1);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.92rem;
        transition: all 0.3s ease;
        display: inline-block;
    }
    
    .btn-cancel:hover {
        background: rgba(0, 0, 0, 0.2);
    }
    
    .btn-submit {
        padding: 11px 26px;
        background: linear-gradient(135deg, #d4af37, #f1c40f);
        color: #0a5c36;
        border: 2px solid rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
    }
    
    .error-text {
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 5px;
        display: block;
        font-weight: 600;
    }
    
    .help-text {
        color: #777;
        font-size: 0.82rem;
        margin-top: 8px;
        line-height: 1.5;
    }
    
    .material-preview {
        background: rgba(26, 188, 156, 0.05);
        padding: 14px;
        border-radius: 8px;
        border: 2px solid rgba(26, 188, 156, 0.3);
        margin-top: 14px;
    }
    
    .preview-title {
        color: #1abc9c;
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    
    .material-item {
        background: white;
        padding: 12px;
        border-radius: 8px;
        border: 2px solid #000000;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .material-item-info {
        flex: 1;
    }
    
    .material-item-type {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .type-image {
        background: rgba(52, 152, 219, 0.2);
        color: #3498db;
    }
    
    .type-file {
        background: rgba(231, 76, 60, 0.2);
        color: #e74c3c;
    }
    
    .type-youtube {
        background: rgba(255, 0, 0, 0.2);
        color: #ff0000;
    }
    
    .type-url {
        background: rgba(52, 152, 219, 0.2);
        color: #3498db;
    }
    
    .btn-view {
        padding: 6px 14px;
        background: #000000;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    
    .btn-view:hover {
        background: #333333;
        transform: translateY(-2px);
    }
    
    .view-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    
    .view-modal.active {
        display: flex;
    }
    
    .view-modal-content {
        background: white;
        border-radius: 15px;
        padding: 30px;
        max-width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        border: 3px solid #000000;
    }
    
    .view-modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #000000;
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

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1>✏️ Edit Assignment</h1>
            <p>Update assignment for {{ $classroom->class_name }}</p>
        </div>
        
        <div class="header-actions">
            <div style="display: flex; align-items: center; gap: 10px; background: rgba(0,0,0,0.15); padding: 11px 15px; border-radius: 10px;">
                <label style="color: #d4af37; font-size: 0.87rem; font-weight: 700; white-space: nowrap; margin: 0;">
                    📅 Due Date & Time *
                </label>
                <input type="datetime-local" id="dueDateInput" name="due_date_temp" value="{{ old('due_date', $assignment->due_date->format('Y-m-d\TH:i')) }}" required 
                    class="datetime-input">
            </div>

            <div style="display: flex; gap: 10px;">
                <a href="{{ route('classroom.show', $classroom->id) }}" class="btn-cancel">
                    Cancel
                </a>
                <button type="submit" form="assignmentForm" class="btn-submit">
                    ✓ Update Assignment
                </button>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form id="assignmentForm" action="{{ route('assignment.update', $assignment->assignment_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="due_date" id="dueDateHidden" value="{{ old('due_date', $assignment->due_date->format('Y-m-d H:i:s')) }}">
            <input type="hidden" name="total_marks" value="100">
            <input type="hidden" name="is_voice_submission" value="1">

            <div class="two-column-grid">
    .radio-title { color: #d4af37; font-weight: 700; font-size: 1.05rem; margin-bottom: 4px; }
    .radio-desc { color: #666; font-size: 0.85rem; line-height: 1.4; }
    
    .verse-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px; }
    
    .verse-display {
        background: rgba(10, 92, 54, 0.1);
        padding: 20px;
        border-radius: 10px;
        border: 2px solid #0a5c36;
        margin-top: 15px;
    }
    
    .verse-arabic {
        font-family: 'Traditional Arabic', 'Arabic Typesetting', serif;
        font-size: 1.5rem;
        line-height: 2.5;
        text-align: right;
        color: #0a5c36;
        margin-bottom: 15px;
    }
    
    .verse-translation { color: #1abc9c; font-size: 0.95rem; line-height: 1.6; }
    
    .info-box {
        margin-top: 15px;
        padding: 15px 20px;
        background: rgba(212, 175, 55, 0.1);
        border-radius: 10px;
        border-left: 4px solid #d4af37;
    }
    
    .info-title { color: #d4af37; font-weight: 600; margin-bottom: 5px; font-size: 0.95rem; }
    .info-content { color: #666; font-size: 0.9rem; line-height: 1.6; }
    
    .button-group { display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px; }
    
    .btn {
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        border: 2px solid;
    }
    
    .btn-primary { background: #0a5c36; color: #d4af37; border-color: #0a5c36; }
    .btn-primary:hover { background: #d4af37; color: #0a5c36; border-color: #d4af37; }
    .btn-secondary { background: transparent; color: #1abc9c; border-color: #1abc9c; }
    .btn-secondary:hover { background: rgba(26, 188, 156, 0.1); }
    
    .btn-insert {
        padding: 8px 18px;
        background: rgba(26, 188, 156, 0.2);
        color: #1abc9c;
        border: 2px solid #1abc9c;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .btn-insert:hover { background: rgba(26, 188, 156, 0.3); }
    .error-text { color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block; }
    .help-text { color: #666; font-size: 0.85rem; margin-top: 8px; opacity: 0.9; }
    
    .material-preview {
        background: rgba(10, 92, 54, 0.1);
        padding: 15px;
        border-radius: 8px;
        border: 2px solid #0a5c36;
        margin-top: 15px;
    }
    
    .preview-title { color: #1abc9c; font-weight: 600; margin-bottom: 10px; font-size: 0.95rem; }
    .current-selection {
        background: rgba(26, 188, 156, 0.1);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #1abc9c;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h1>✏️ Edit Assignment</h1>
    <p>Update assignment in {{ $classroom->class_name }}</p>
</div>

<!-- Form Card -->
<div class="form-card">
    <form action="{{ route('assignment.update', $assignment->assignment_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

            <div class="two-column-grid">
                
                <!-- LEFT COLUMN -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <!-- Reference Material Section -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <span>📚</span> Reference Material
                        </h4>
                        <p class="section-desc">
                            Choose from existing materials (optional)
                        </p>
                        
                        <!-- Category Filter Buttons -->
                        <div class="filter-buttons" id="categoryFilters">
                            <button type="button" class="filter-btn active" data-category="all" onclick="filterMaterials('all')">
                                All Materials
                            </button>
                            <button type="button" class="filter-btn" data-category="Madd Rules" onclick="filterMaterials('Madd Rules')">
                                Madd Rules
                            </button>
                            <button type="button" class="filter-btn" data-category="Idgham Billa Ghunnah" onclick="filterMaterials('Idgham Billa Ghunnah')">
                                Idgham Billa Ghunnah
                            </button>
                            <button type="button" class="filter-btn" data-category="Idgham Bi Ghunnah" onclick="filterMaterials('Idgham Bi Ghunnah')">
                                Idgham Bi Ghunnah
                            </button>
                            <button type="button" class="filter-btn" data-category="Others" onclick="filterMaterials('Others')">
                                Others
                            </button>
                        </div>
                        
                        <div>
                            <label class="form-label">Available Materials</label>
                            <select name="material_id" id="materialSelect" class="form-select">
                                <option value="">-- No material (students can reference on their own) --</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->material_id }}" 
                                            data-category="{{ $material->category ?? 'Others' }}"
                                            {{ old('material_id', $assignment->material_id) == $material->material_id ? 'selected' : '' }}>
                                        {{ $material->title }}
                                        @if($material->category)
                                            ({{ $material->category }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('material_id')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                            <p class="help-text">
                                💡 Need to add new materials? Visit <a href="{{ route('materials.index') }}" style="color: #1abc9c; text-decoration: underline; font-weight: 600;">Learning Materials</a> page first.
                            </p>
                        </div>
                        
                        <div id="materialPreview" style="display: none;" class="material-preview">
                            <h5 class="preview-title">📋 Material Preview</h5>
                            <div id="previewContent"></div>
                        </div>
                    </div>

                    <!-- Tajweed Rules Section -->
                    <div class="form-section gold">
                        <h4 class="section-title gold">
                            <span>✨</span> Tajweed Rules to Focus On *
                        </h4>
                        <p class="section-desc">
                            Select the specific Tajweed rules for this assignment
                        </p>
                        
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="tajweed_rules" value="Madd" {{ old('tajweed_rules', $assignment->tajweed_rules) == 'Madd' ? 'checked' : '' }} required>
                                <div class="radio-label">
                                    <div class="radio-title">1. Madd (Elongation)</div>
                                    <div class="radio-desc">Proper lengthening of vowels (ا و ي)</div>
                                </div>
                            </label>
                            
                            <label class="radio-option">
                                <input type="radio" name="tajweed_rules" value="Idgham Bi Ghunnah" {{ old('tajweed_rules', $assignment->tajweed_rules) == 'Idgham Bi Ghunnah' ? 'checked' : '' }} required>
                                <div class="radio-label">
                                    <div class="radio-title">2. Idgham Bi Ghunnah</div>
                                    <div class="radio-desc">Merging WITH nasalization (و م ن ي)</div>
                                </div>
                            </label>
                            
                            <label class="radio-option">
                                <input type="radio" name="tajweed_rules" value="Idgham Billa Ghunnah" {{ old('tajweed_rules', $assignment->tajweed_rules) == 'Idgham Billa Ghunnah' ? 'checked' : '' }} required>
                                <div class="radio-label">
                                    <div class="radio-title">3. Idgham Billa Ghunnah</div>
                                    <div class="radio-desc">Merging WITHOUT nasalization (ل ر)</div>
                                </div>
                            </label>
                        </div>
                        @error('tajweed_rules')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <!-- Quran Reference -->
                    <div class="form-section gold">
                        <h4 class="section-title gold">
                            <span>📖</span> Quran Reference *
                        </h4>
                        <p class="section-desc">
                            Select the surah and verse range for students to recite
                        </p>
                        
                        <div>
                            <label class="form-label">Select Surah *</label>
                            <select id="surahSelect" name="surah_number" class="form-select" required>
                                <option value="">Loading surahs...</option>
                            </select>
                            <input type="hidden" id="surah_name_hidden" name="surah" value="{{ old('surah', $assignment->surah) }}" required>
                            <input type="hidden" id="currentSurah" value="{{ old('surah', $assignment->surah) }}">
                            @error('surah')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px;">
                            <div>
                                <label class="form-label">Start Verse *</label>
                                <input type="number" id="ayahStart" name="start_verse" value="{{ old('start_verse', $assignment->start_verse) }}" min="1" placeholder="1" required class="form-input">
                                @error('start_verse')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label">End Verse</label>
                                <input type="number" id="ayahEnd" name="end_verse" value="{{ old('end_verse', $assignment->end_verse) }}" min="1" placeholder="Optional" class="form-input">
                                @error('end_verse')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div id="verseDisplay" class="verse-display" style="display: none;">
                            <div id="verseArabic" class="verse-arabic"></div>
                            <div id="verseTranslation" class="verse-translation"></div>
                        </div>
                        
                        <button type="button" id="insertVerseBtn" onclick="insertVerseToInstructions()" class="btn-insert" style="display: none;">
                            ➕ Insert to Instructions
                        </button>
                    </div>

                    <!-- Assignment Details -->
                    <div class="form-section gold">
                        <h4 class="section-title gold">
                            <span>📋</span> Assignment Details *
                        </h4>
                        <p class="section-desc">
                            Provide clear instructions and expectations for students
                        </p>
                        
                        <div>
                            <label class="form-label">Instructions for Students *</label>
                            <textarea name="instructions" rows="8" required class="form-textarea" placeholder="What should students do?&#10;&#10;E.g., Recite Surah Al-Fatiha verses 1-3 with proper Madd elongation. Pay attention to the lengthening of vowels and submit your recording.">{{ old('instructions', $assignment->instructions) }}</textarea>
                            @error('instructions')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div id="viewModal" class="view-modal" style="display: none;">
                <div class="view-modal-content">
                    <span class="view-close" onclick="closeViewModal()">&times;</span>
                    <div id="viewModalContent"></div>
                </div>
            </div>
    </form>
</div>

<script>
    let currentVerseData = '';

    async function loadSurahs() {
            const surahSelect = document.getElementById('surahSelect');
            const currentSurah = document.getElementById('currentSurah').value;
            try {
                const response = await fetch('https://api.alquran.cloud/v1/surah');
                if (!response.ok) throw new Error('Failed to load surahs');
                
                const result = await response.json();
                const surahs = result.data;
                
                // Clear loading option
                surahSelect.innerHTML = '<option value="">-- Choose Surah --</option>';
                
                // Populate dropdown with surahs
                surahs.forEach(surah => {
                    const option = document.createElement('option');
                    option.value = surah.number;
                    option.textContent = `${surah.number}. ${surah.englishName} (${surah.numberOfAyahs})`;
                    option.setAttribute('data-name', surah.englishName);
                    option.setAttribute('data-ayahs', surah.numberOfAyahs);
                    surahSelect.appendChild(option);
                });
                
                // Set current surah by matching the name
                if (currentSurah) {
                    const options = surahSelect.options;
                    for (let i = 0; i < options.length; i++) {
                        if (options[i].getAttribute('data-name') === currentSurah) {
                            surahSelect.selectedIndex = i;
                            // Update hidden field with the selected surah name
                            document.getElementById('surah_name_hidden').value = currentSurah;
                            // Trigger the verse preview
                            fetchQuranVerse();
                            break;
                        }
                    }
                }
            } catch (error) {
                console.error('Error loading surahs:', error);
                surahSelect.innerHTML = '<option value="">Error loading surahs. Please refresh.</option>';
            }
        }

        // Load surahs when page loads
        document.addEventListener('DOMContentLoaded', () => {
            loadSurahs();
            
            // Auto-preview verses when inputs change
            document.getElementById('surahSelect').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    // Update hidden field
                    document.getElementById('surah_name_hidden').value = selectedOption.getAttribute('data-name');
                }
                fetchQuranVerse();
            });
            document.getElementById('ayahStart').addEventListener('input', debounce(fetchQuranVerse, 800));
            document.getElementById('ayahEnd').addEventListener('input', debounce(fetchQuranVerse, 800));
        });

        // Debounce function to avoid too many API calls
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        async function fetchQuranVerse() {
            const surahNumber = document.getElementById('surahSelect').value;
            const ayahStart = document.getElementById('ayahStart').value;
            const ayahEnd = document.getElementById('ayahEnd').value;

            const verseDisplay = document.getElementById('verseDisplay');
            const insertBtn = document.getElementById('insertVerseBtn');

            if (!surahNumber || !ayahStart) {
                verseDisplay.style.display = 'none';
                insertBtn.style.display = 'none';
                return;
            }

            const verseArabic = document.getElementById('verseArabic');
            const verseTranslation = document.getElementById('verseTranslation');

            try {
                // Fetch verse(s) from Al-Quran Cloud API
                let verses = [];
                const start = parseInt(ayahStart);
                const end = ayahEnd ? parseInt(ayahEnd) : start;

                // Fetch the entire surah first
                const response = await fetch(`https://api.alquran.cloud/v1/surah/${surahNumber}/en.asad`);
                
                if (!response.ok) {
                    throw new Error(`Failed to fetch Surah ${surahNumber}`);
                }
                
                const result = await response.json();
                const surahData = result.data;
                
                // Get Arabic version
                const arabicResponse = await fetch(`https://api.alquran.cloud/v1/surah/${surahNumber}`);
                const arabicResult = await arabicResponse.json();
                const arabicSurah = arabicResult.data;

                // Extract requested ayahs
                let arabicTexts = [];
                let translations = [];
                
                for (let i = start; i <= end && i <= surahData.ayahs.length; i++) {
                    const ayahIndex = i - 1;
                    arabicTexts.push(arabicSurah.ayahs[ayahIndex].text);
                    translations.push(`[${i}] ${surahData.ayahs[ayahIndex].text}`);
                }

                // Display verses
                let arabicText = arabicTexts.join(' ۝ ');
                let translationText = translations.join('\n');

                verseArabic.textContent = arabicText;
                verseTranslation.textContent = translationText;
                verseDisplay.style.display = 'block';
                insertBtn.style.display = 'inline-block';

                // Store for insertion
                const surahName = surahData.englishName;
                currentVerseData = `\n\n--- Quran Verse ---\nSurah: ${surahName} (${surahData.name})\nAyat: ${ayahStart}${ayahEnd ? `-${ayahEnd}` : ''}\n\nArabic:\n${arabicText}\n\nTranslation:\n${translationText}\n--- End Verse ---\n\n`;

            } catch (error) {
                alert('Error fetching verse: ' + error.message);
                console.error(error);
            }
        }

        function insertVerseToInstructions() {
            const instructionsField = document.querySelector('textarea[name="instructions"]');
            const currentValue = instructionsField.value;
            instructionsField.value = currentValue + currentVerseData;
            
            // Visual feedback
            const btn = document.getElementById('insertVerseBtn');
            const originalText = btn.textContent;
            btn.textContent = '✓ Inserted!';
            btn.style.background = 'rgba(46, 125, 50, 0.3)';
            btn.style.borderColor = '#4caf50';
            
            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.background = 'rgba(77, 139, 49, 0.3)';
                btn.style.borderColor = 'var(--color-light-green)';
            }, 2000);
        }

        // Material Category Filter
        function filterMaterials(category) {
            const materialSelect = document.getElementById('materialSelect');
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            // Update active button
            filterButtons.forEach(btn => {
                if (btn.getAttribute('data-category') === category) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            
            // Show/hide materials based on category
            Array.from(materialSelect.options).forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block'; // Always show "No material" option
                    return;
                }
                
                if (category === 'all') {
                    option.style.display = 'block';
                } else {
                    const optionCategory = option.getAttribute('data-category');
                    option.style.display = optionCategory === category ? 'block' : 'none';
                }
            });
        }

        // Material Preview with Items
        const materialsData = @json($materials);
        const materialSelect = document.getElementById('materialSelect');
        const materialPreview = document.getElementById('materialPreview');
        const previewContent = document.getElementById('previewContent');

        materialSelect.addEventListener('change', function() {
            const materialId = this.value;
            
            if (!materialId) {
                materialPreview.style.display = 'none';
                return;
            }

            const material = materialsData.find(m => m.material_id == materialId);
            
            if (material) {
                let html = '<div style="display: grid; gap: 15px;">';
                html += `<div><p style="margin: 0; color: #0a5c36; font-weight: 600; font-size: 1rem;">${material.title}</p>`;
                
                if (material.category) {
                    html += `<span style="display: inline-block; background: rgba(212, 175, 55, 0.2); color: #d4af37; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; width: fit-content; margin-top: 5px;">${material.category}</span>`;
                }
                
                if (material.content) {
                    html += `<p style="margin: 8px 0 0 0; color: #666; opacity: 0.8; font-size: 0.9rem;">${material.content.substring(0, 150)}${material.content.length > 150 ? '...' : ''}</p>`;
                }
                html += '</div>';
                
                // Show attached files/items
                if (material.items && material.items.length > 0) {
                    html += '<div style="border-top: 2px solid #e8e8e8; padding-top: 12px;">';
                    html += '<p style="margin: 0 0 10px 0; color: #0a5c36; font-weight: 600; font-size: 0.9rem;">📎 Attached Files:</p>';
                    html += '<div style="display: grid; gap: 8px;">';
                    
                    material.items.forEach(item => {
                        let badgeClass = 'badge-file';
                        let icon = '📄';
                        if (item.item_type === 'image') {
                            badgeClass = 'badge-image';
                            icon = '🖼️';
                        } else if (item.item_type === 'youtube') {
                            badgeClass = 'badge-youtube';
                            icon = '🎥';
                        } else if (item.item_type === 'url') {
                            badgeClass = 'badge-url';
                            icon = '🔗';
                        }
                        
                        html += `<div class="material-item">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="item-badge ${badgeClass}">${icon} ${item.item_type.toUpperCase()}</span>
                                <span style="flex: 1; color: #000; font-weight: 500;">${item.title}</span>
                                <button type="button" class="btn-view" onclick="viewMaterialItem(${material.material_id}, ${item.item_id}, '${item.item_type}')">
                                    👁️ View
                                </button>
                            </div>
                        </div>`;
                    });
                    
                    html += '</div></div>';
                }
                
                html += '</div>';
                
                previewContent.innerHTML = html;
                materialPreview.style.display = 'block';
            }
        });

        // View Material Item in Modal
        function viewMaterialItem(materialId, itemId, itemType) {
            const material = materialsData.find(m => m.material_id == materialId);
            if (!material) return;
            
            const item = material.items.find(i => i.item_id == itemId);
            if (!item) return;
            
            const modal = document.getElementById('viewModal');
            const modalContent = document.getElementById('viewModalContent');
            
            let contentHtml = '';
            
            if (itemType === 'image') {
                contentHtml = `
                    <h3 style="margin: 0 0 15px 0; color: #0a5c36; font-size: 1.3rem;">🖼️ ${item.title}</h3>
                    <img src="/storage/${item.file_path}" alt="${item.title}" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                `;
            } else if (itemType === 'file') {
                contentHtml = `
                    <h3 style="margin: 0 0 15px 0; color: #0a5c36; font-size: 1.3rem;">📄 ${item.title}</h3>
                    <iframe src="/storage/${item.file_path}" style="width: 100%; height: 500px; border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></iframe>
                    <a href="/storage/${item.file_path}" target="_blank" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #1abc9c; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                        📥 Download PDF
                    </a>
                `;
            } else if (itemType === 'youtube') {
                const videoId = extractYoutubeId(item.youtube_url);
                contentHtml = `
                    <h3 style="margin: 0 0 15px 0; color: #0a5c36; font-size: 1.3rem;">🎥 ${item.title}</h3>
                    <iframe width="100%" height="400" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></iframe>
                `;
            } else if (itemType === 'url') {
                contentHtml = `
                    <h3 style="margin: 0 0 15px 0; color: #0a5c36; font-size: 1.3rem;">🔗 ${item.title}</h3>
                    <p style="color: #666; margin-bottom: 15px;">External resource link:</p>
                    <a href="${item.external_url}" target="_blank" style="display: inline-block; padding: 12px 24px; background: #1abc9c; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        🌐 Open Link
                    </a>
                `;
            }
            
            modalContent.innerHTML = contentHtml;
            modal.style.display = 'block';
        }

        // Close Modal
        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        // Helper function to extract YouTube video ID
        function extractYoutubeId(url) {
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            const match = url.match(regExp);
            return (match && match[2].length === 11) ? match[2] : null;
        }

        // Sync due date between header input and hidden field
        document.getElementById('dueDateInput').addEventListener('change', function() {
            document.getElementById('dueDateHidden').value = this.value;
        });
</script>
@endsection
