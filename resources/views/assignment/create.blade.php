@extends('layouts.dashboard')

@section('title', 'Create Assignment')
@section('user-role', 'Teacher • Create Assignment')

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

    .verse-display tajweed {
        display: inline;
    }
    
    .verse-arabic {
        text-align: right;
        direction: rtl;
        font-family: 'Amiri', 'Traditional Arabic', serif;
        font-size: 1.5rem;
        line-height: 2;
        color: #000000;
        margin-bottom: 12px;
    }

    .verse-arabic tajweed {
        display: inline;
        font-weight: 700;
    }

    .verse-arabic tajweed.ham_wasl { color: #2e7d32; }
    .verse-arabic tajweed.laam_shamsiyah { color: #ef6c00; }
    .verse-arabic tajweed.ikhafa { color: #1565c0; }
    .verse-arabic tajweed.idgham_ghunnah { color: #7b1fa2; }
    .verse-arabic tajweed.idgham_shafawi { color: #00897b; }
    .verse-arabic tajweed.gunnah { color: #2e7d32; }
    .verse-arabic tajweed.madda_obligatory,
    .verse-arabic tajweed.madda_normal,
    .verse-arabic tajweed.madda_permissible { color: #c62828; }
    .verse-arabic tajweed.slnt { color: #6d4c41; }
    .verse-arabic tajweed.end { color: #555555; }
    
    .verse-translation {
        color: #000000;
        font-size: 0.9rem;
        line-height: 1.6;
        white-space: pre-wrap;
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
            <h1>📝 Create New Assignment</h1>
            <p>Assign work to students in {{ $classroom->class_name }}</p>
        </div>
        
        <div class="header-actions">
            <div style="display: flex; align-items: center; gap: 10px; background: #e67e22; padding: 11px 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(230, 126, 34, 0.4);">
                <label style="color: #ffffff; font-size: 0.87rem; font-weight: 700; white-space: nowrap; margin: 0; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">
                    📅 Due Date & Time *
                </label>
                <input type="datetime-local" id="dueDateInput" name="due_date_temp" value="{{ old('due_date') }}" required 
                    class="datetime-input">
            </div>

            <div style="display: flex; gap: 10px;">
                <a href="{{ route('classroom.show', $classroom->id) }}" class="btn-cancel" style="background: #e74c3c; color: #ffffff; border: 2px solid #c0392b; box-shadow: 0 2px 8px rgba(231, 76, 60, 0.4);">
                    Cancel
                </a>
                <button type="submit" form="assignmentForm" class="btn-submit">
                    ✓ Create Assignment
                </button>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form id="assignmentForm" action="{{ route('assignment.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="class_id" value="{{ $classroom->id }}">
            <input type="hidden" name="due_date" id="dueDateHidden">
            <input type="hidden" name="total_marks" value="100">
            <input type="hidden" name="is_voice_submission" value="1">
            <input type="hidden" id="surah_name_hidden" name="surah" required>
            <input type="hidden" id="surah_number_hidden" name="surah_number" required>
            
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
                                            {{ old('material_id') == $material->material_id ? 'selected' : '' }}>
                                        {{ $material->title }}
                                        @if($material->category)
                                            ({{ $material->category }})
                                        @endif
                                        @if($material->file_path) 📄 @endif
                                        @if($material->video_link) 🎥 @endif
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

                </div>
                
                <!-- RIGHT COLUMN -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <!-- Assignment Details -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <span>📝</span> Assignment Details *
                        </h4>
                        <p class="section-desc">
                            Core information for the assignment
                        </p>
                        
                        <div style="margin-bottom: 16px;">
                            <label class="form-label">Assignment Title *</label>
                            <input type="text" name="assignment_title" value="{{ old('assignment_title') }}" required 
                                class="form-input" placeholder="e.g., Surah Al-Fatiha - Madd Practice">
                            @error('assignment_title')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="form-label">Instructions for Students *</label>
                            <textarea name="instructions" class="form-textarea" required
                                placeholder="Provide clear instructions...">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Quran Reference -->
                    <div class="form-section gold">
                        <h4 class="section-title gold">
                            <span>📖</span> Quran Verses *
                        </h4>
                        <p class="section-desc">
                            Students will practice these specific verses
                        </p>

                        @php
                            $surahs = [
                                1=>'Al-Fatiha',2=>'Al-Baqarah',3=>"Ali 'Imran",4=>"An-Nisa",5=>"Al-Ma'idah",
                                6=>"Al-An'am",7=>"Al-A'raf",8=>'Al-Anfal',9=>'At-Tawbah',10=>'Yunus',
                                11=>'Hud',12=>'Yusuf',13=>"Ar-Ra'd",14=>'Ibrahim',15=>'Al-Hijr',
                                16=>'An-Nahl',17=>"Al-Isra",18=>'Al-Kahf',19=>'Maryam',20=>'Ta-Ha',
                                21=>"Al-Anbiya",22=>'Al-Hajj',23=>"Al-Mu'minun",24=>'An-Nur',25=>'Al-Furqan',
                                26=>"Ash-Shu'ara",27=>'An-Naml',28=>'Al-Qasas',29=>"Al-'Ankabut",30=>'Ar-Rum',
                                31=>'Luqman',32=>'As-Sajdah',33=>'Al-Ahzab',34=>'Saba',35=>'Fatir',
                                36=>'Ya-Sin',37=>"As-Saffat",38=>'Sad',39=>'Az-Zumar',40=>'Ghafir',
                                41=>'Fussilat',42=>"Ash-Shura",43=>'Az-Zukhruf',44=>'Ad-Dukhan',45=>'Al-Jathiyah',
                                46=>'Al-Ahqaf',47=>'Muhammad',48=>'Al-Fath',49=>'Al-Hujurat',50=>'Qaf',
                                51=>'Adh-Dhariyat',52=>'At-Tur',53=>'An-Najm',54=>'Al-Qamar',55=>'Ar-Rahman',
                                56=>"Al-Waqi'ah",57=>'Al-Hadid',58=>'Al-Mujadila',59=>'Al-Hashr',60=>'Al-Mumtahanah',
                                61=>'As-Saf',62=>"Al-Jumu'ah",63=>'Al-Munafiqun',64=>'At-Taghabun',65=>'At-Talaq',
                                66=>'At-Tahrim',67=>'Al-Mulk',68=>'Al-Qalam',69=>'Al-Haqqah',70=>"Al-Ma'arij",
                                71=>'Nuh',72=>'Al-Jinn',73=>'Al-Muzzammil',74=>'Al-Muddaththir',75=>'Al-Qiyamah',
                                76=>'Al-Insan',77=>'Al-Mursalat',78=>"An-Naba",79=>"An-Nazi'at",80=>"'Abasa",
                                81=>'At-Takwir',82=>'Al-Infitar',83=>'Al-Mutaffifin',84=>'Al-Inshiqaq',85=>'Al-Buruj',
                                86=>'At-Tariq',87=>"Al-A'la",88=>'Al-Ghashiyah',89=>'Al-Fajr',90=>'Al-Balad',
                                91=>'Ash-Shams',92=>'Al-Layl',93=>'Ad-Duha',94=>'Ash-Sharh',95=>'At-Tin',
                                96=>"Al-'Alaq",97=>'Al-Qadr',98=>'Al-Bayyinah',99=>'Az-Zalzalah',100=>"Al-'Adiyat",
                                101=>"Al-Qari'ah",102=>'At-Takathur',103=>"Al-'Asr",104=>'Al-Humazah',105=>'Al-Fil',
                                106=>'Quraysh',107=>"Al-Ma'un",108=>'Al-Kawthar',109=>'Al-Kafirun',110=>'An-Nasr',
                                111=>'Al-Masad',112=>'Al-Ikhlas',113=>'Al-Falaq',114=>'An-Nas',
                            ];
                        @endphp
                        
                        <div style="margin-bottom: 16px;">
                            <label class="form-label">Select Surah *</label>
                            <select name="surah_number" id="surahSelect" class="form-select" required>
                                <option value="">-- Choose a Surah --</option>
                                @foreach($surahs as $number => $name)
                                    <option value="{{ $number }}" data-name="{{ $name }}" {{ old('surah_number') == $number ? 'selected' : '' }}>
                                        {{ $number }}. {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('surah')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div id="verseRangeSection" style="display: none;">
                            <label class="form-label">Verse Range *</label>
                            <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 10px; align-items: center; margin-bottom: 14px;">
                                <div>
                                    <input type="number" name="start_verse" id="ayahStart" value="{{ old('start_verse') }}" 
                                        class="form-input" placeholder="From" min="1" required>
                                </div>
                                <span style="color: #999; font-weight: 600;">to</span>
                                <div>
                                    <input type="number" name="end_verse" id="ayahEnd" value="{{ old('end_verse') }}" 
                                        class="form-input" placeholder="To" min="1">
                                </div>
                            </div>
                            @error('start_verse')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                            @error('end_verse')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div id="versePreview" style="display: none;" class="verse-display">
                            <div id="verseArabic" class="verse-arabic"></div>
                            <div id="verseTranslation" class="verse-translation"></div>
                            <button type="button" id="insertVerseBtn" class="btn-insert" onclick="insertVerseToInstructions()">
                                ✓ Insert to Instructions
                            </button>
                        </div>
                    </div>
                    
                </div>
                
            </div>
        </form>
    </div>
</div>

<script>
    let currentVerseData = '';

    // Copy due date from visible input to hidden input
    document.getElementById('dueDateInput').addEventListener('change', function() {
        document.getElementById('dueDateHidden').value = this.value;
    });

    // Filter materials by category
    function filterMaterials(category) {
        const materialSelect = document.getElementById('materialSelect');
        const options = materialSelect.querySelectorAll('option[data-category]');
        const filterBtns = document.querySelectorAll('.filter-btn');
        
        // Update active button
        filterBtns.forEach(btn => {
            if (btn.getAttribute('data-category') === category) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        // Filter options
        options.forEach(option => {
            const optionCategory = option.getAttribute('data-category');
            if (category === 'all' || optionCategory === category) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        
        // Reset select to default if current selection is filtered out
        const currentValue = materialSelect.value;
        if (currentValue && category !== 'all') {
            const currentOption = materialSelect.querySelector(`option[value="${currentValue}"]`);
            if (currentOption && currentOption.getAttribute('data-category') !== category) {
                materialSelect.value = '';
                document.getElementById('materialPreview').style.display = 'none';
            }
        }
    }

    // Fetch and display Quran verses
    async function fetchQuranVerse() {
        const surahNumber = document.getElementById('surahSelect').value;
        const ayahStart = document.getElementById('ayahStart').value;
        const ayahEnd = document.getElementById('ayahEnd').value;

        const versePreview = document.getElementById('versePreview');

        if (!surahNumber || !ayahStart) {
            versePreview.style.display = 'none';
            return;
        }

        const verseArabic = document.getElementById('verseArabic');
        const verseTranslation = document.getElementById('verseTranslation');

        try {
            const start = parseInt(ayahStart);
            const requestedEnd = ayahEnd ? parseInt(ayahEnd) : start;

            const previewUrl = "{{ route('assignment.preview-verse') }}";
            const response = await fetch(`${previewUrl}?surah_number=${encodeURIComponent(surahNumber)}&start_verse=${encodeURIComponent(start)}&end_verse=${encodeURIComponent(requestedEnd)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || `Failed to fetch Surah ${surahNumber}`);
            }

            const verse = result.verse || {};

            verseArabic.innerHTML = verse.arabic_html || verse.arabic_plain || '';
            verseTranslation.textContent = verse.translation || '';
            versePreview.style.display = 'block';

            currentVerseData = `\n\n--- Quran Verse ---\nSurah: ${verse.surah_name || ''}${verse.surah_name_arabic ? ` (${verse.surah_name_arabic})` : ''}\nAyat: ${start}${requestedEnd !== start ? `-${requestedEnd}` : ''}\n\nArabic:\n${verse.arabic_plain || ''}\n\nTranslation:\n${verse.translation || ''}\n--- End Verse ---\n\n`;

        } catch (error) {
            alert('Error fetching verse: ' + error.message);
            console.error(error);
        }
    }

    function insertVerseToInstructions() {
        const instructionsField = document.querySelector('textarea[name="instructions"]');
        const currentValue = instructionsField.value;
        instructionsField.value = currentValue + currentVerseData;
        
        const btn = document.getElementById('insertVerseBtn');
        const originalText = btn.textContent;
        const originalBg = btn.style.background;
        const originalBorder = btn.style.borderColor;
        
        btn.textContent = '✓ Inserted!';
        btn.style.background = 'rgba(46, 204, 113, 0.2)';
        btn.style.borderColor = '#2ecc71';
        
        setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = originalBg || 'rgba(26, 188, 156, 0.12)';
            btn.style.borderColor = originalBorder || '#1abc9c';
        }, 2000);
    }

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

    // Update surah name in hidden field
    function updateSurahName() {
        const select = document.getElementById('surahSelect');
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value) {
            document.getElementById('surah_name_hidden').value = selectedOption.getAttribute('data-name');
            document.getElementById('surah_number_hidden').value = selectedOption.value;
            document.getElementById('verseRangeSection').style.display = 'block';
        } else {
            document.getElementById('surah_name_hidden').value = '';
            document.getElementById('surah_number_hidden').value = '';
            document.getElementById('verseRangeSection').style.display = 'none';
        }
    }

    // Material Preview
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
            let html = '<div style="display: flex; flex-direction: column; gap: 8px;">';
            html += `<p style="margin: 0; color: #000000; font-weight: 600; font-size: 0.95rem;">${material.title}</p>`;
            
            if (material.category) {
                html += `<span style="display: inline-block; background: rgba(0, 0, 0, 0.1); color: #000000; padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; width: fit-content; font-weight: 600;">${material.category}</span>`;
            }
            
            if (material.description) {
                html += `<p style="margin: 0; color: #000000; font-size: 0.85rem; line-height: 1.5;">${material.description.substring(0, 150)}${material.description.length > 150 ? '...' : ''}</p>`;
            }
            
            // Show attached files
            if (material.items && material.items.length > 0) {
                html += '<div style="margin-top: 12px;"><p style="font-weight: 600; color: #000000; margin-bottom: 8px;">📎 Attached Files:</p>';
                
                material.items.forEach((item, index) => {
                    let typeClass = 'type-' + item.type;
                    let typeLabel = item.type.toUpperCase();
                    let itemTitle = item.title || `Item ${index + 1}`;
                    
                    html += `
                        <div class="material-item">
                            <div class="material-item-info">
                                <span class="material-item-type ${typeClass}">${typeLabel}</span>
                                <div style="color: #000000; font-weight: 600; font-size: 0.9rem;">${itemTitle}</div>
                            </div>
                            <button type="button" class="btn-view" onclick="viewMaterialItem(${material.material_id}, ${item.item_id}, '${item.type}')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    `;
                });
                
                html += '</div>';
            }
            
            html += '</div>';
            
            previewContent.innerHTML = html;
            materialPreview.style.display = 'block';
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const surahSelect = document.getElementById('surahSelect');
        surahSelect.addEventListener('change', () => {
            updateSurahName();
            fetchQuranVerse();
        });
        
        updateSurahName();
        if (surahSelect.value && document.getElementById('ayahStart').value) {
            fetchQuranVerse();
        }

        document.getElementById('ayahStart').addEventListener('input', debounce(fetchQuranVerse, 800));
        document.getElementById('ayahEnd').addEventListener('input', debounce(fetchQuranVerse, 800));
    });

    // View material item in modal
    function viewMaterialItem(materialId, itemId, itemType) {
        const material = materialsData.find(m => m.material_id == materialId);
        if (!material) return;
        
        const item = material.items.find(i => i.item_id == itemId);
        if (!item) return;
        
        const modal = document.getElementById('viewModal');
        const modalBody = document.getElementById('viewModalBody');
        
        let content = '';
        
        if (itemType === 'image' || itemType === 'file') {
            const path = item.path;
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(path.split('.').pop().toLowerCase());
            
            if (isImage) {
                content = `<img src="/storage/${path}" style="max-width: 100%; border-radius: 10px; border: 2px solid #000000;" alt="${item.title || 'Image'}">`;
            } else {
                content = `<iframe src="/storage/${path}" style="width: 100%; height: 600px; border: 2px solid #000000; border-radius: 10px;"></iframe>`;
            }
        } else if (itemType === 'youtube') {
            const videoId = extractYoutubeId(item.path || item.youtube_link);
            if (videoId) {
                content = `<iframe width="100%" height="500" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 10px; border: 2px solid #000000;"></iframe>`;
            }
        } else if (itemType === 'url') {
            content = `<iframe src="${item.path}" style="width: 100%; height: 600px; border: 2px solid #000000; border-radius: 10px;"></iframe>`;
        }
        
        if (item.title) {
            content = `<h3 style="color: #000000; margin-bottom: 15px;">${item.title}</h3>` + content;
        }
        
        if (item.description) {
            content += `<p style="color: #000000; margin-top: 15px; font-size: 0.9rem;">${item.description}</p>`;
        }
        
        modalBody.innerHTML = content;
        modal.classList.add('active');
    }
    
    function closeViewModal() {
        document.getElementById('viewModal').classList.remove('active');
    }
    
    function extractYoutubeId(url) {
        if (!url) return null;
        const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
        return match ? match[1] : null;
    }
</script>

<!-- View Modal -->
<div id="viewModal" class="view-modal">
    <div class="view-modal-content">
        <button class="view-modal-close" onclick="closeViewModal()">&times;</button>
        <div id="viewModalBody"></div>
    </div>
</div>

@endsection
