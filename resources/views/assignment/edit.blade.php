@extends('layouts.template')

@section('page-title', 'Edit Assignment')
@section('page-subtitle', 'Update assignment in ' . $classroom->class_name)

@section('content')
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">✏️ Edit Assignment</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('assignment.update', $assignment->assignment_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Select Reference Material (Optional) -->
                <div style="margin-bottom: 25px; background: rgba(77, 139, 49, 0.1); padding: 20px; border-radius: 12px; border: 2px solid var(--color-light-green);">
                    <h4 style="color: var(--color-light-green); margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.5rem;">📚</span> Select Reference Material (Optional)
                    </h4>
                    <p style="color: var(--color-light); opacity: 0.8; font-size: 0.9rem; margin-bottom: 15px;">
                        Choose from existing materials to help students with this assignment
                    </p>
                    
                    @if($assignment->material)
                        <div style="background: rgba(77, 139, 49, 0.2); padding: 12px; border-radius: 8px; margin-bottom: 15px;">
                            <p style="margin: 0; color: var(--color-light-green); font-weight: 600;">Currently Selected: {{ $assignment->material->title }}</p>
                            @if($assignment->material->file_path)
                                <p style="margin: 5px 0 0 0; color: var(--color-light); font-size: 0.9rem;">📄 PDF attached</p>
                            @endif
                            @if($assignment->material->video_link)
                                <p style="margin: 5px 0 0 0; color: var(--color-light); font-size: 0.9rem;">🎥 Video attached</p>
                            @endif
                        </div>
                    @endif
                    
                    <div style="margin-bottom: 0;">
                        <label style="display: block; color: var(--color-light-green); font-weight: 600; margin-bottom: 8px;">
                            Available Materials
                        </label>
                        <select name="material_id" id="materialSelect"
                            style="width: 100%; padding: 12px 15px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                            <option value="">-- No material (students can reference on their own) --</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->material_id }}" 
                                    {{ old('material_id', $assignment->material_id) == $material->material_id ? 'selected' : '' }}>
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
                            <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                        <p style="color: var(--color-light); opacity: 0.7; font-size: 0.85rem; margin-top: 8px;">
                            💡 Need to add new materials? Visit <a href="{{ route('materials.index') }}" style="color: var(--color-gold); text-decoration: underline;">Learning Materials</a> page first.
                        </p>
                    </div>
                    
                    <!-- Material Preview -->
                    <div id="materialPreview" style="display: none; margin-top: 15px; background: rgba(31, 39, 27, 0.5); padding: 15px; border-radius: 8px; border: 2px solid var(--color-dark-green);">
                        <h5 style="color: var(--color-light-green); margin: 0 0 10px 0; font-size: 0.95rem;">📋 Material Preview</h5>
                        <div id="previewContent"></div>
                    </div>
                </div>

                <!-- Tajweed Rules Selection -->
                <div style="margin-bottom: 25px; background: rgba(227, 216, 136, 0.1); padding: 20px; border-radius: 12px; border: 2px solid var(--color-gold);">
                    <h4 style="color: var(--color-gold); margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.5rem;">✨</span> Tajweed Rule to Focus On *
                    </h4>
                    <p style="color: var(--color-light); opacity: 0.8; font-size: 0.9rem; margin-bottom: 15px;">
                        Select one specific Tajweed rule that students must pay attention to in this recitation
                    </p>
                    
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; cursor: pointer; background: rgba(227, 216, 136, 0.1); padding: 12px 20px; border-radius: 8px; border: 2px solid var(--color-dark-green); transition: all 0.3s ease;"
                            onmouseover="this.style.borderColor='var(--color-gold)'"
                            onmouseout="this.style.borderColor='var(--color-dark-green)'">
                            <input type="radio" name="tajweed_rules" value="Madd" 
                                {{ (old('tajweed_rules') == 'Madd') || (is_array($assignment->tajweed_rules) && in_array('Madd', $assignment->tajweed_rules)) ? 'checked' : '' }} required
                                style="margin-right: 10px; width: 18px; height: 18px; cursor: pointer;">
                            <span style="color: var(--color-light); font-weight: 600;">Madd (Elongation)</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; cursor: pointer; background: rgba(227, 216, 136, 0.1); padding: 12px 20px; border-radius: 8px; border: 2px solid var(--color-dark-green); transition: all 0.3s ease;"
                            onmouseover="this.style.borderColor='var(--color-gold)'"
                            onmouseout="this.style.borderColor='var(--color-dark-green)'">
                            <input type="radio" name="tajweed_rules" value="Noon Saakin" 
                                {{ (old('tajweed_rules') == 'Noon Saakin') || (is_array($assignment->tajweed_rules) && in_array('Noon Saakin', $assignment->tajweed_rules)) ? 'checked' : '' }} required
                                style="margin-right: 10px; width: 18px; height: 18px; cursor: pointer;">
                            <span style="color: var(--color-light); font-weight: 600;">Noon Saakin (Rules of ن)</span>
                        </label>
                    </div>
                    @error('tajweed_rules')
                        <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Quran Verse Selection -->
                <div style="margin-bottom: 25px; background: rgba(227, 216, 136, 0.1); padding: 20px; border-radius: 12px; border: 2px solid var(--color-gold);">
                    <h4 style="color: var(--color-gold); margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.5rem;">📖</span> Assign Quran Verse for Recitation
                    </h4>
                    <p style="color: var(--color-light); opacity: 0.8; font-size: 0.9rem; margin-bottom: 15px;">
                        Select the surah and verse range that students must recite for this assignment
                    </p>
                    
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; color: var(--color-gold); font-size: 0.9rem; margin-bottom: 5px;">Select Surah *</label>
                            <select id="surahSelect" name="surah_number" required style="width: 100%; padding: 10px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif;" onchange="document.getElementById('surah_name_hidden').value = this.options[this.selectedIndex].getAttribute('data-name');">
                                <option value="">Loading surahs...</option>
                            </select>
                            <input type="hidden" id="surah_name_hidden" name="surah" value="{{ old('surah', $assignment->surah) }}" required>
                            <input type="hidden" id="currentSurah" value="{{ old('surah', $assignment->surah) }}">
                            @error('surah')
                                <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label style="display: block; color: var(--color-gold); font-size: 0.9rem; margin-bottom: 5px;">Ayat From *</label>
                            <input type="number" id="ayahFrom" name="start_verse" value="{{ old('start_verse', $assignment->start_verse) }}" min="1" placeholder="Start" required
                                style="width: 100%; padding: 10px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif;">
                            @error('start_verse')
                                <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label style="display: block; color: var(--color-gold); font-size: 0.9rem; margin-bottom: 5px;">Ayat To</label>
                            <input type="number" id="ayahTo" name="end_verse" value="{{ old('end_verse', $assignment->end_verse) }}" min="1" placeholder="End"
                                style="width: 100%; padding: 10px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif;">
                            @error('end_verse')
                                <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div id="verseDisplay" style="display: none; background: rgba(31, 39, 27, 0.8); padding: 15px; border-radius: 8px; margin-bottom: 10px; margin-top: 15px;">
                        <div id="verseContent" style="font-family: 'Traditional Arabic', 'Arabic Typesetting', serif; font-size: 1.5rem; line-height: 2.5; text-align: right; color: var(--color-light); margin-bottom: 10px;"></div>
                        <div id="verseTranslation" style="color: var(--color-light-green); font-size: 0.95rem; line-height: 1.6;"></div>
                    </div>
                    
                    <button type="button" id="insertVerseBtn" onclick="insertVerseToInstructions()" style="display: none; padding: 8px 18px; background: rgba(77, 139, 49, 0.3); color: var(--color-light-green); border: 2px solid var(--color-light-green); border-radius: 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif;"
                        onmouseover="this.style.background='rgba(226, 241, 175, 0.2)'"
                        onmouseout="this.style.background='rgba(77, 139, 49, 0.3)'">
                        ➕ Insert to Instructions
                    </button>
                </div>

                <!-- Instructions -->
                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">
                        📋 Instructions for Students *
                    </label>
                    <textarea name="instructions" rows="6" required
                        style="width: 100%; padding: 12px 15px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem; resize: vertical;"
                        placeholder="Provide clear instructions for the assignment...">{{ old('instructions', $assignment->instructions) }}</textarea>
                    @error('instructions')
                        <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Due Date, Total Marks, Voice Submission - Side by Side -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <!-- Due Date -->
                    <div>
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">
                            📅 Due Date *
                        </label>
                        <input type="datetime-local" name="due_date" value="{{ old('due_date', $assignment->due_date->format('Y-m-d\TH:i')) }}" required
                            style="width: 100%; padding: 12px 15px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                        @error('due_date')
                            <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Total Marks -->
                    <div>
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">
                            🎯 Total Marks *
                        </label>
                        <input type="number" name="total_marks" value="{{ old('total_marks', $assignment->total_marks) }}" min="1" required
                            style="width: 100%; padding: 12px 15px; background: rgba(31, 39, 27, 0.5); color: var(--color-light); border: 2px solid var(--color-dark-green); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem;">
                        @error('total_marks')
                            <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Voice Submission -->
                    <div>
                        <label style="display: block; color: var(--color-gold); font-weight: 600; margin-bottom: 8px;">
                            🎤 Submission Type *
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; background: rgba(227, 216, 136, 0.15); padding: 12px 15px; border-radius: 8px; border: 2px solid var(--color-gold); height: 48px;">
                            <input type="hidden" name="is_voice_submission" value="0">
                            <input type="checkbox" name="is_voice_submission" value="1" 
                                {{ old('is_voice_submission', $assignment->is_voice_submission) ? 'checked' : '' }}
                                style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                            <span style="color: var(--color-gold); font-weight: 600; font-size: 0.95rem;">
                                Voice Required
                            </span>
                        </label>
                        @error('is_voice_submission')
                            <span style="color: #e74c3c; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <a href="{{ route('classroom.show', $classroom->id) }}" 
                        style="padding: 12px 30px; background: transparent; color: var(--color-light-green); border: 2px solid var(--color-light-green); border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-block;"
                        onmouseover="this.style.background='rgba(226, 241, 175, 0.1)'"
                        onmouseout="this.style.background='transparent'">
                        Cancel
                    </a>
                    <button type="submit"
                        style="padding: 12px 30px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif;"
                        onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                        onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                        Update Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentVerseData = '';

        // Load surahs from API when page loads
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
            document.getElementById('surahSelect').addEventListener('change', fetchQuranVerse);
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
                html += `<p style="margin: 0; color: var(--color-light); font-weight: 600; font-size: 1rem;">${material.title}</p>`;
                
                if (material.category) {
                    html += `<span style="display: inline-block; background: rgba(227, 216, 136, 0.2); color: var(--color-gold); padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; width: fit-content;">${material.category}</span>`;
                }
                
                if (material.content) {
                    html += `<p style="margin: 5px 0 0 0; color: var(--color-light); opacity: 0.8; font-size: 0.9rem;">${material.content.substring(0, 150)}${material.content.length > 150 ? '...' : ''}</p>`;
                }
                
                html += '<div style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap;">';
                
                if (material.file_path) {
                    html += '<span style="color: var(--color-light-green); font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">📄 PDF Available</span>';
                }
                
                if (material.video_link) {
                    html += '<span style="color: var(--color-light-green); font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">🎥 Video Available</span>';
                }
                
                html += '</div></div>';
                
                previewContent.innerHTML = html;
                materialPreview.style.display = 'block';
            }
        });
    </script>
@endsection
