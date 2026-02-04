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
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.2);
    }
    
    .page-header h1 {
        color: white;
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .page-header p {
        opacity: 0.9;
        font-size: 1.05rem;
    }
    
    .form-card {
        background: white;
        border-radius: 15px;
        padding: 35px;
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.1);
        border: 3px solid #2a2a2a;
    }
    
    .form-section {
        margin-bottom: 35px;
        background: rgba(10, 92, 54, 0.05);
        padding: 25px;
        border-radius: 12px;
        border: 2px solid #0a5c36;
    }
    
    .form-section.gold {
        background: rgba(212, 175, 55, 0.1);
        border-color: #d4af37;
    }
    
    .section-title {
        color: #0a5c36;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title.gold {
        color: #d4af37;
    }
    
    .section-desc {
        color: #666;
        font-size: 0.95rem;
        margin-bottom: 20px;
        line-height: 1.6;
    }
    
    .form-label {
        display: block;
        color: #0a5c36;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 1rem;
    }
    
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 15px;
        background: white;
        color: #333;
        border: 2px solid #0a5c36;
        border-radius: 8px;
        font-family: 'El Messiri', sans-serif;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #d4af37;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    .radio-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .radio-option {
        display: flex;
        align-items: flex-start;
        cursor: pointer;
        background: rgba(212, 175, 55, 0.05);
        padding: 18px;
        border-radius: 10px;
        border: 2px solid #0a5c36;
        transition: all 0.3s ease;
    }
    
    .radio-option:hover {
        border-color: #d4af37;
        background: rgba(212, 175, 55, 0.1);
    }
    
    .radio-option input[type="radio"] {
        margin-right: 12px;
        margin-top: 3px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    
    .radio-label {
        flex: 1;
    }
    
    .radio-title {
        color: #d4af37;
        font-weight: 700;
        font-size: 1.05rem;
        margin-bottom: 4px;
    }
    
    .radio-desc {
        color: #666;
        font-size: 0.85rem;
        line-height: 1.4;
    }
    
    .verse-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 15px;
    }
    
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
    
    .verse-translation {
        color: #1abc9c;
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .info-box {
        margin-top: 15px;
        padding: 15px 20px;
        background: rgba(212, 175, 55, 0.1);
        border-radius: 10px;
        border-left: 4px solid #d4af37;
    }
    
    .info-title {
        color: #d4af37;
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 0.95rem;
    }
    
    .info-content {
        color: #666;
        font-size: 1.05rem;
        line-height: 1.6;
    }
    
    .button-group {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
    }
    
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
    
    .btn-primary {
        background: #0a5c36;
        color: #d4af37;
        border-color: #0a5c36;
    }
    
    .btn-primary:hover {
        background: #d4af37;
        color: #0a5c36;
        border-color: #d4af37;
    }
    
    .btn-secondary {
        background: transparent;
        color: #1abc9c;
        border-color: #1abc9c;
    }
    
    .btn-secondary:hover {
        background: rgba(26, 188, 156, 0.1);
    }
    
    .btn-insert {
        padding: 8px 18px;
        background: rgba(26, 188, 156, 0.2);
        color: #1abc9c;
        border: 2px solid #1abc9c;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.05rem;
    }
    
    .btn-insert:hover {
        background: rgba(26, 188, 156, 0.3);
    }
    
    .error-text {
        color: #e74c3c;
        font-size: 1.05rem;
        margin-top: 5px;
        display: block;
    }
    
    .help-text {
        color: #666;
        font-size: 0.85rem;
        margin-top: 8px;
        opacity: 0.9;
    }
    
    .material-preview {
        background: rgba(10, 92, 54, 0.1);
        padding: 15px;
        border-radius: 8px;
        border: 2px solid #0a5c36;
        margin-top: 15px;
    }
    
    .preview-title {
        color: #1abc9c;
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }
</style>

<!-- Form Card -->
<div class="form-card">
    <form action="{{ route('assignment.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classroom->id }}">
        
        <!-- Page Header -->
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; gap: 20px; margin: -35px -35px 30px -35px; padding: 30px 35px;">
            <div>
                <h1 style="margin-bottom: 5px;">📝 Create New Assignment</h1>
                <p style="margin: 0;">Assign work to students in {{ $classroom->class_name }}</p>
            </div>
            
            <div style="display: flex; gap: 15px; align-items: center;">
                <div style="background: #1a1a1a; padding: 15px 20px; border-radius: 15px; border: 2px solid #d4af37; box-shadow: 0 4px 15px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 15px;">
                    <label style="color: #d4af37; font-size: 0.95rem; font-weight: 700; white-space: nowrap; margin: 0;">
                        📅 Due Date & Time *
                    </label>
                    <input type="datetime-local" name="due_date" value="{{ old('due_date') }}" required 
                        style="padding: 8px 12px; border: 1px solid #333; border-radius: 8px; font-size: 0.95rem; background: #2a2a2a; color: white; outline: none;">
                    @error('due_date')
                        <span style="color: #ff4d4d; font-size: 0.95rem; position: absolute; bottom: -20px;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('classroom.show', $classroom->id) }}" 
                       style="padding: 12px 20px; background: #333; color: white; border: 2px solid #444; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease;" 
                       onmouseover="this.style.background='#444'" onmouseout="this.style.background='#333'">
                        Cancel
                    </a>
                    <button type="submit" 
                        style="padding: 12px 25px; background: #d4af37; color: #0a5c36; border: 2px solid #b38f2d; border-radius: 12px; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2);" 
                        onmouseover="this.style.background='#f1c40f'; this.style.transform='translateY(-2px)'" 
                        onmouseout="this.style.background='#d4af37'; this.style.transform='translateY(0)'">
                        ✓ Create Assignment
                    </button>
                </div>
            </div>
        </div>

        <!-- 2-Column Grid Layout -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
            
            <!-- LEFT COLUMN: Reference Materials + Tajweed Rules -->
            <div style="display: grid; grid-template-rows: 1fr 1fr; gap: 25px;">
        <!-- Reference Material Section -->
        <div class="form-section" style="margin-bottom: 0;">
            <h4 class="section-title" style="font-size: 1.3rem;">
                <span>📚</span> Reference Material
            </h4>
            <p class="section-desc" style="font-size: 0.95rem; margin-bottom: 15px;">
                Choose from existing materials (optional)
            </p>
            
            <div>
                <label class="form-label" style="font-size: 1.05rem;">Available Materials</label>
                <select name="material_id" id="materialSelect" class="form-select" style="font-size: 1rem;">
                    <option value="">-- No material (students can reference on their own) --</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->material_id }}" {{ old('material_id') == $material->material_id ? 'selected' : '' }}>
                            {{ $material->title }}
                            @if($material->category)
                                ({{ $material->category }})
                            @endif
                            @if($material->file_path)
                                📄
                            @endif
                            @if($material->video_link)
                                🎥
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('material_id')
                    <span class="error-text">{{ $message }}</span>
                @enderror
                <p class="help-text" style="font-size: 0.95rem;">
                    💡 Need to add new materials? Visit <a href="{{ route('materials.index') }}" style="color: #d4af37; text-decoration: underline;">Learning Materials</a> page first.
                </p>
            </div>
            
            <div id="materialPreview" style="display: none;" class="material-preview">
                <h5 class="preview-title">📋 Material Preview</h5>
                <div id="previewContent"></div>
            </div>
        </div>

        <!-- Tajweed Rules Section -->
        <div class="form-section gold" style="margin-bottom: 0;">
            <h4 class="section-title gold" style="font-size: 1.3rem;">
                <span>✨</span> Tajweed Rules to Focus On *
            </h4>
            <p class="section-desc" style="font-size: 0.95rem; margin-bottom: 15px;">
                Select the specific Tajweed rules
            </p>
            
            <div class="radio-group" style="grid-template-columns: 1fr;">
                <label class="radio-option" style="padding: 15px;">
                    <input type="radio" name="tajweed_rules" value="Madd" {{ old('tajweed_rules', 'Madd') == 'Madd' ? 'checked' : '' }} required>
                    <div class="radio-label">
                        <div class="radio-title" style="font-size: 1rem;">1. Madd (Elongation)</div>
                        <div class="radio-desc" style="font-size: 0.85rem;">Proper lengthening of vowels (ا و ي)</div>
                    </div>
                </label>
                
                <label class="radio-option" style="padding: 15px;">
                    <input type="radio" name="tajweed_rules" value="Idgham Bi Ghunnah" {{ old('tajweed_rules') == 'Idgham Bi Ghunnah' ? 'checked' : '' }} required>
                    <div class="radio-label">
                        <div class="radio-title" style="font-size: 1rem;">2. Idgham Bi Ghunnah</div>
                        <div class="radio-desc" style="font-size: 0.85rem;">Merging WITH nasalization (و م ن ي)</div>
                    </div>
                </label>
                
                <label class="radio-option" style="padding: 15px;">
                    <input type="radio" name="tajweed_rules" value="Idgham Bila Ghunnah" {{ old('tajweed_rules') == 'Idgham Bila Ghunnah' ? 'checked' : '' }} required>
                    <div class="radio-label">
                        <div class="radio-title" style="font-size: 1rem;">3. Idgham Bila Ghunnah</div>
                        <div class="radio-desc" style="font-size: 0.85rem;">Merging WITHOUT nasalization (ر ل)</div>
                    </div>
                </label>
            </div>
            @error('tajweed_rules')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>
            </div> <!-- End LEFT COLUMN -->
            
            <!-- RIGHT COLUMN: Quran Verse Selection + Instructions -->
            <div style="display: grid; grid-template-rows: 1fr 1fr; gap: 25px;">
        <!-- Quran Verse Selection -->
        <div class="form-section gold" style="margin-bottom: 0;">
            <h4 class="section-title gold" style="font-size: 1.3rem;">
                <span>📖</span> Assign Quran Verse *
            </h4>
            <p class="section-desc" style="font-size: 0.95rem; margin-bottom: 15px;">
                Select surah and verse range
            </p>
            
            <div class="verse-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div>
                    <label class="form-label" style="font-size: 1.05rem;">Select Surah *</label>
                    <select id="surahSelect" name="surah_number" required class="form-select" style="font-size: 1rem;" onchange="document.getElementById('surah_name_hidden').value = this.options[this.selectedIndex].getAttribute('data-name');">
                        <option value="">Loading surahs...</option>
                    </select>
                    <input type="hidden" id="surah_name_hidden" name="surah" required>
                    @error('surah')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="form-label" style="font-size: 1.05rem;">Ayat From *</label>
                    <input type="number" id="ayahFrom" name="start_verse" min="1" placeholder="Start" required class="form-input" style="font-size: 1rem;">
                    @error('start_verse')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="form-label" style="font-size: 1.05rem;">Ayat To</label>
                    <input type="number" id="ayahTo" name="end_verse" min="1" placeholder="End" class="form-input" style="font-size: 1rem;">
                    @error('end_verse')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div id="verseDisplay" style="display: none;" class="verse-display">
                <div id="verseContent" class="verse-arabic"></div>
                <div id="verseTranslation" class="verse-translation"></div>
            </div>
            
            <button type="button" id="insertVerseBtn" onclick="insertVerseToInstructions()" style="display: none; margin-top: 15px; font-size: 1rem;" class="btn-insert">
                ➕ Insert to Instructions
            </button>
        </div>

        <!-- Instructions -->
        <div class="form-section" style="margin-bottom: 0;">
            <h4 class="section-title" style="font-size: 1.3rem; margin-bottom: 15px;">
                <span>📋</span> Instructions for Students *
            </h4>
            <textarea name="instructions" rows="12" required class="form-textarea" style="font-size: 1rem; min-height: 240px;" placeholder="Provide clear instructions for the assignment...">{{ old('instructions') }}</textarea>
            @error('instructions')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>
            </div> <!-- End RIGHT COLUMN -->
            
        </div> <!-- End 2-column grid -->

        <input type="hidden" name="total_marks" value="100">
        <input type="hidden" name="is_voice_submission" value="1">
    </form>
</div>

<script>
    let currentVerseData = '';

    async function loadSurahs() {
        const surahSelect = document.getElementById('surahSelect');
        try {
            const response = await fetch('https://api.alquran.cloud/v1/surah');
            if (!response.ok) throw new Error('Failed to load surahs');
            
            const result = await response.json();
            const surahs = result.data;
            
            surahSelect.innerHTML = '<option value="">-- Choose Surah --</option>';
            
            surahs.forEach(surah => {
                const option = document.createElement('option');
                option.value = surah.number;
                option.textContent = `${surah.number}. ${surah.englishName} (${surah.numberOfAyahs})`;
                option.setAttribute('data-name', surah.englishName);
                option.setAttribute('data-ayahs', surah.numberOfAyahs);
                surahSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading surahs:', error);
            surahSelect.innerHTML = '<option value="">Error loading surahs. Please refresh.</option>';
        }
    }

    function updateSurahName() {
        const select = document.getElementById('surahSelect');
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value) {
            select.setAttribute('data-selected-name', selectedOption.getAttribute('data-name'));
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadSurahs();
        
        const surahSelect = document.getElementById('surahSelect');
        surahSelect.addEventListener('change', () => {
            updateSurahName();
            fetchQuranVerse();
        });
        
        document.getElementById('ayahFrom').addEventListener('input', debounce(fetchQuranVerse, 800));
        document.getElementById('ayahTo').addEventListener('input', debounce(fetchQuranVerse, 800));
    });

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
        const ayahFrom = document.getElementById('ayahFrom').value;
        const ayahTo = document.getElementById('ayahTo').value;

        const verseDisplay = document.getElementById('verseDisplay');
        const insertBtn = document.getElementById('insertVerseBtn');

        if (!surahNumber || !ayahFrom) {
            verseDisplay.style.display = 'none';
            insertBtn.style.display = 'none';
            return;
        }

        const verseContent = document.getElementById('verseContent');
        const verseTranslation = document.getElementById('verseTranslation');

        try {
            let verses = [];
            const start = parseInt(ayahFrom);
            const end = ayahTo ? parseInt(ayahTo) : start;

            const response = await fetch(`https://api.alquran.cloud/v1/surah/${surahNumber}/en.asad`);
            
            if (!response.ok) {
                throw new Error(`Failed to fetch Surah ${surahNumber}`);
            }
            
            const result = await response.json();
            const surahData = result.data;
            
            const arabicResponse = await fetch(`https://api.alquran.cloud/v1/surah/${surahNumber}`);
            const arabicResult = await arabicResponse.json();
            const arabicSurah = arabicResult.data;

            let arabicTexts = [];
            let translations = [];
            
            for (let i = start; i <= end && i <= surahData.ayahs.length; i++) {
                const ayahIndex = i - 1;
                arabicTexts.push(arabicSurah.ayahs[ayahIndex].text);
                translations.push(`[${i}] ${surahData.ayahs[ayahIndex].text}`);
            }

            let arabicText = arabicTexts.join(' ۝ ');
            let translationText = translations.join('\n');

            verseContent.textContent = arabicText;
            verseTranslation.textContent = translationText;
            verseDisplay.style.display = 'block';
            insertBtn.style.display = 'inline-block';

            const surahName = surahData.englishName;
            currentVerseData = `\n\n--- Quran Verse ---\nSurah: ${surahName} (${surahData.name})\nAyat: ${ayahFrom}${ayahTo ? `-${ayahTo}` : ''}\n\nArabic:\n${arabicText}\n\nTranslation:\n${translationText}\n--- End Verse ---\n\n`;

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
        btn.textContent = '✓ Inserted!';
        btn.style.background = 'rgba(46, 125, 50, 0.3)';
        btn.style.borderColor = '#4caf50';
        
        setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = 'rgba(26, 188, 156, 0.2)';
            btn.style.borderColor = '#1abc9c';
        }, 2000);
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
            let html = '<div style="display: grid; gap: 10px;">';
            html += `<p style="margin: 0; color: #0a5c36; font-weight: 600; font-size: 1rem;">${material.title}</p>`;
            
            if (material.category) {
                html += `<span style="display: inline-block; background: rgba(212, 175, 55, 0.2); color: #d4af37; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; width: fit-content;">${material.category}</span>`;
            }
            
            if (material.content) {
                html += `<p style="margin: 5px 0 0 0; color: #666; font-size: 0.9rem;">${material.content.substring(0, 150)}${material.content.length > 150 ? '...' : ''}</p>`;
            }
            
            html += '<div style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap;">';
            
            if (material.file_path) {
                html += '<span style="color: #1abc9c; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">📄 PDF Available</span>';
            }
            
            if (material.video_link) {
                html += '<span style="color: #1abc9c; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">🎥 Video Available</span>';
            }
            
            html += '</div></div>';
            
            previewContent.innerHTML = html;
            materialPreview.style.display = 'block';
        }
    });
</script>
@endsection
