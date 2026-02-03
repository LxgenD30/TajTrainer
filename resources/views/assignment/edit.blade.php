@extends('layouts.dashboard')

@section('title', 'Edit Assignment')
@section('user-role', 'Teacher • Edit Assignment')

@section('navigation')
    <a href="{{ route('home') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-home"></i></div>
        <div class="nav-label">Dashboard</div>
    </a>
    <a href="{{ route('classroom.index') }}" class="nav-item active">
        <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="nav-label">My Classes</div>
    </a>
    <a href="{{ route('students.list') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-user-graduate"></i></div>
        <div class="nav-label">My Students</div>
    </a>
    <a href="{{ route('materials.index') }}" class="nav-item">
        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
        <div class="nav-label">Materials</div>
    </a>
@endsection

@section('page-title', 'Edit Assignment')
@section('page-subtitle', 'Update assignment in ' . $classroom->class_name)

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
    
    .page-header h1 { color: white; font-size: 2rem; margin-bottom: 10px; }
    .page-header p { opacity: 0.9; font-size: 1.05rem; }
    
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
    
    .form-section.gold { background: rgba(212, 175, 55, 0.1); border-color: #d4af37; }
    
    .section-title {
        color: #0a5c36;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title.gold { color: #d4af37; }
    .section-desc { color: #666; font-size: 0.95rem; margin-bottom: 20px; line-height: 1.6; }
    
    .form-label { display: block; color: #0a5c36; font-weight: 600; margin-bottom: 8px; font-size: 1rem; }
    
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
    
    .form-textarea { resize: vertical; min-height: 120px; }
    
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
    
    .radio-option:hover { border-color: #d4af37; background: rgba(212, 175, 55, 0.1); }
    .radio-option input[type="radio"] { margin-right: 12px; margin-top: 3px; width: 20px; height: 20px; cursor: pointer; }
    .radio-label { flex: 1; }
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

        <!-- Reference Material Section -->
        <div class="form-section">
            <h4 class="section-title">
                <span>📚</span> Select Reference Material (Optional)
            </h4>
            <p class="section-desc">
                Choose from existing materials to help students with this assignment
            </p>
            
            @if($assignment->material)
                <div class="current-selection">
                    <p style="margin: 0; color: #1abc9c; font-weight: 600;">Currently Selected: {{ $assignment->material->title }}</p>
                    @if($assignment->material->file_path)
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9rem;">📄 PDF attached</p>
                    @endif
                    @if($assignment->material->video_link)
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9rem;">🎥 Video attached</p>
                    @endif
                </div>
            @endif
            
            <div>
                <label class="form-label">Available Materials</label>
                <select name="material_id" id="materialSelect" class="form-select">
                    <option value="">-- No material (students can reference on their own) --</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->material_id }}" {{ old('material_id', $assignment->material_id) == $material->material_id ? 'selected' : '' }}>
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
                <p class="help-text">
                    💡 Need to add new materials? Visit <a href="{{ route('materials.index') }}" style="color: #d4af37; text-decoration: underline;">Learning Materials</a> page first.
                </p>
            </div>
            
            <!-- Material Preview -->
            <div id="materialPreview" class="material-preview" style="display: none;">
                <h5 class="preview-title">📋 Material Preview</h5>
                <div id="previewContent"></div>
            </div>
        </div>

        <!-- Tajweed Rules Selection -->
        <div class="form-section gold">
            <h4 class="section-title gold">
                <span>✨</span> Tajweed Rule to Focus On *
            </h4>
            <p class="section-desc">
                Select one specific Tajweed rule that students must pay attention to in this recitation
            </p>
            
            <div class="radio-group">
                <label class="radio-option">
                    <input type="radio" name="tajweed_rules" value="Madd" 
                        {{ (old('tajweed_rules') == 'Madd') || (is_array($assignment->tajweed_rules) && in_array('Madd', $assignment->tajweed_rules)) ? 'checked' : '' }} required>
                    <div class="radio-label">
                        <div class="radio-title">Madd (Elongation)</div>
                        <div class="radio-desc">Proper elongation of vowels</div>
                    </div>
                </label>
                
                <label class="radio-option">
                    <input type="radio" name="tajweed_rules" value="Idgham Bi Ghunnah" 
                        {{ (old('tajweed_rules') == 'Idgham Bi Ghunnah') || (is_array($assignment->tajweed_rules) && in_array('Idgham Bi Ghunnah', $assignment->tajweed_rules)) ? 'checked' : '' }} required>
                    <div class="radio-label">
                        <div class="radio-title">Idgham Bi Ghunnah</div>
                        <div class="radio-desc">Merging WITH nasalization</div>
                    </div>
                </label>
                
                <label class="radio-option">
                    <input type="radio" name="tajweed_rules" value="Idgham Bila Ghunnah" 
                        {{ (old('tajweed_rules') == 'Idgham Bila Ghunnah') || (is_array($assignment->tajweed_rules) && in_array('Idgham Bila Ghunnah', $assignment->tajweed_rules)) ? 'checked' : '' }} required>
                    <div class="radio-label">
                        <div class="radio-title">Idgham Bila Ghunnah</div>
                        <div class="radio-desc">Merging WITHOUT nasalization</div>
                    </div>
                </label>
            </div>
            @error('tajweed_rules')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Quran Verse Selection -->
        <div class="form-section gold">
            <h4 class="section-title gold">
                <span>📖</span> Assign Quran Verse for Recitation
            </h4>
            <p class="section-desc">
                Select the surah and verse range that students must recite for this assignment
            </p>
            
            <div class="verse-grid">
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
                <div>
                    <label class="form-label">Ayat From *</label>
                    <input type="number" id="ayahFrom" name="start_verse" value="{{ old('start_verse', $assignment->start_verse) }}" min="1" placeholder="Start" required class="form-input">
                    @error('start_verse')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="form-label">Ayat To</label>
                    <input type="number" id="ayahTo" name="end_verse" value="{{ old('end_verse', $assignment->end_verse) }}" min="1" placeholder="End" class="form-input">
                    @error('end_verse')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div id="verseDisplay" class="verse-display" style="display: none;">
                <div id="verseContent" class="verse-arabic"></div>
                <div id="verseTranslation" class="verse-translation"></div>
            </div>
            
            <button type="button" id="insertVerseBtn" onclick="insertVerseToInstructions()" class="btn-insert" style="display: none;">
                ➕ Insert to Instructions
            </button>
        </div>

        <!-- Instructions -->
        <div class="form-section">
            <label class="form-label">
                📋 Instructions for Students *
            </label>
            <textarea name="instructions" rows="6" required class="form-textarea" placeholder="Provide clear instructions for the assignment...">{{ old('instructions', $assignment->instructions) }}</textarea>
            @error('instructions')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Due Date -->
        <div class="form-section">
            <label class="form-label">
                📅 Due Date *
            </label>
            <input type="datetime-local" name="due_date" value="{{ old('due_date', $assignment->due_date->format('Y-m-d\TH:i')) }}" required class="form-input">
            @error('due_date')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Hidden fields for fixed values -->
        <input type="hidden" name="total_marks" value="100">
        <input type="hidden" name="is_voice_submission" value="1">
        
        <!-- Info box showing fixed values -->
        <div class="info-box">
            <p style="margin: 0; color: #666; font-size: 0.95rem;">
                <span style="color: #d4af37; font-weight: 600;">ℹ️ Note:</span> 
                All assignments are automatically set to <strong>100 points</strong> and require <strong>voice recordings</strong>.
            </p>
        </div>

        <!-- Buttons -->
        <div class="button-group">
            <a href="{{ route('classroom.show', $classroom->id) }}" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                Update Assignment
            </button>
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
            document.getElementById('ayahFrom').addEventListener('input', debounce(fetchQuranVerse, 800));
            document.getElementById('ayahTo').addEventListener('input', debounce(fetchQuranVerse, 800));
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
                // Fetch verse(s) from Al-Quran Cloud API
                let verses = [];
                const start = parseInt(ayahFrom);
                const end = ayahTo ? parseInt(ayahTo) : start;

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

                verseContent.textContent = arabicText;
                verseTranslation.textContent = translationText;
                verseDisplay.style.display = 'block';
                insertBtn.style.display = 'inline-block';

                // Store for insertion
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
                    html += `<p style="margin: 5px 0 0 0; color: #666; opacity: 0.8; font-size: 0.9rem;">${material.content.substring(0, 150)}${material.content.length > 150 ? '...' : ''}</p>`;
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
