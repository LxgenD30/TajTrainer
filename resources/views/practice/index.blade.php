@extends('layouts.template')

@section('page-title', 'Quran Practice')
@section('page-subtitle', 'Practice your Quran recitation with instant feedback')

@section('content')
<style>
    .practice-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 25px;
    }

    .practice-card {
        background: rgba(31, 39, 27, 0.6);
        border: 2px solid var(--color-dark-green);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
    }

    .ayah-display {
        background: rgba(227, 216, 136, 0.1);
        border: 2px solid var(--color-gold);
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        margin-bottom: 25px;
    }

    .ayah-arabic {
        font-family: 'Amiri', serif;
        font-size: 2.5rem;
        color: var(--color-gold);
        line-height: 2;
        margin-bottom: 15px;
        direction: rtl;
    }

    .ayah-translation {
        font-size: 1.1rem;
        color: var(--color-light-green);
        font-style: italic;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(227, 216, 136, 0.3);
    }

    .ayah-info {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 15px;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .control-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .btn-practice {
        padding: 12px 24px;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-new-verse {
        background: var(--color-dark-green);
        color: var(--color-gold);
        border-color: var(--color-dark-green);
    }

    .btn-new-verse:hover {
        background: var(--color-gold);
        color: var(--color-dark);
        transform: translateY(-2px);
    }

    .btn-play-reference {
        background: rgba(227, 216, 136, 0.1);
        color: var(--color-gold);
        border-color: var(--color-gold);
    }

    .btn-play-reference:hover {
        background: var(--color-gold);
        color: var(--color-dark);
    }

    .btn-record {
        background: #c53030;
        color: white;
        border-color: #c53030;
        font-size: 1.1rem;
        padding: 15px 35px;
    }

    .btn-record:hover {
        background: #9b2c2c;
        transform: scale(1.05);
    }

    .btn-record.recording {
        background: #e53e3e;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .btn-stop {
        background: #2d3748;
        color: white;
        border-color: #2d3748;
    }

    .recording-status {
        text-align: center;
        padding: 15px;
        background: rgba(197, 48, 48, 0.2);
        border: 2px solid #c53030;
        border-radius: 10px;
        color: var(--color-light-green);
        font-weight: 600;
        margin-bottom: 20px;
        display: none;
    }

    .recording-status.active {
        display: block;
    }

    .recording-timer {
        font-size: 1.5rem;
        color: var(--color-gold);
        margin-top: 5px;
    }

    .feedback-section {
        background: rgba(227, 216, 136, 0.05);
        border: 2px solid var(--color-dark-green);
        border-radius: 12px;
        padding: 25px;
        margin-top: 25px;
        display: none;
        animation: slideIn 0.5s ease-out;
    }

    .feedback-section.show {
        display: block;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .feedback-title {
        font-size: 1.3rem;
        color: var(--color-gold);
        font-family: 'Amiri', serif;
        margin-bottom: 15px;
    }

    .accuracy-display {
        text-align: center;
        margin: 20px 0;
        padding: 25px;
        background: rgba(31, 39, 27, 0.5);
        border-radius: 12px;
        border: 2px solid var(--color-dark-green);
    }

    .accuracy-score {
        font-size: 4rem;
        font-weight: bold;
        color: var(--color-gold);
        font-family: 'Cairo', sans-serif;
        margin-bottom: 10px;
    }

    .accuracy-label {
        font-size: 1.2rem;
        color: var(--color-light-green);
        margin-bottom: 5px;
    }

    .accuracy-rating {
        font-size: 1.5rem;
        margin-top: 10px;
        font-weight: 600;
    }

    .accuracy-rating.excellent {
        color: #48bb78;
    }

    .accuracy-rating.good {
        color: #38a169;
    }

    .accuracy-rating.fair {
        color: #ecc94b;
    }

    .accuracy-rating.needs-improvement {
        color: #ed8936;
    }

    .accuracy-rating.poor {
        color: #e53e3e;
    }

    .tajweed-analysis {
        margin-top: 20px;
        padding: 20px;
        background: rgba(227, 216, 136, 0.05);
        border-radius: 10px;
        border: 1px solid rgba(227, 216, 136, 0.2);
    }

    .analysis-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        margin-bottom: 10px;
        background: rgba(31, 39, 27, 0.5);
        border-radius: 8px;
        border-left: 4px solid var(--color-dark-green);
    }

    .analysis-item.correct {
        border-left-color: #48bb78;
    }

    .analysis-item.incorrect {
        border-left-color: #e53e3e;
    }

    .analysis-label {
        font-size: 0.95rem;
        color: var(--color-light-green);
    }

    .analysis-value {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .analysis-value.correct {
        color: #48bb78;
    }

    .analysis-value.incorrect {
        color: #e53e3e;
    }

    .analyzing-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        display: none;
    }

    .analyzing-overlay.show {
        display: flex;
    }

    .analyzing-content {
        background: rgba(31, 39, 27, 0.95);
        border: 3px solid var(--color-dark-green);
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        max-width: 400px;
    }

    .analyzing-spinner {
        width: 60px;
        height: 60px;
        border: 5px solid rgba(227, 216, 136, 0.2);
        border-radius: 50%;
        border-top-color: var(--color-gold);
        animation: spin 1s ease-in-out infinite;
        margin: 0 auto 20px;
    }

    .analyzing-text {
        font-size: 1.2rem;
        color: var(--color-gold);
        font-weight: 600;
        margin-bottom: 10px;
    }

    .analyzing-subtext {
        font-size: 0.9rem;
        color: var(--color-light-green);
        opacity: 0.8;
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: var(--color-gold);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .audio-player {
        width: 100%;
        margin-top: 15px;
        border-radius: 8px;
    }
</style>

<div class="practice-container">
    <div class="practice-card">
        <h2 style="color: var(--color-gold); font-family: 'Amiri', serif; font-size: 1.8rem; text-align: center; margin-bottom: 25px;">
            🎯 Quran Recitation Practice
        </h2>

        <div id="ayahDisplay" class="ayah-display">
            <div class="ayah-arabic" id="ayahArabic">Loading...</div>
            <div class="ayah-info">
                <span id="surahInfo">---</span>
                <span id="ayahInfo">---</span>
            </div>
            <div class="ayah-translation" id="ayahTranslation">Loading translation...</div>
        </div>

        <div class="control-buttons">
            <button class="btn-practice btn-new-verse" onclick="loadRandomAyah()">
                🔄 New Verse
            </button>
            <button class="btn-practice btn-play-reference" onclick="playReferenceAudio()">
                🔊 Play Reference
            </button>
        </div>

        <audio id="referenceAudio" class="audio-player" controls style="display: none;"></audio>

        <div class="recording-status" id="recordingStatus">
            <div>🎤 Recording in progress...</div>
            <div class="recording-timer" id="recordingTimer">00:00</div>
        </div>

        <div style="text-align: center; margin-top: 25px;">
            <button class="btn-practice btn-record" id="recordBtn" onclick="startRecording()">
                🎤 Start Recording
            </button>
            <button class="btn-practice btn-stop" id="stopBtn" onclick="stopRecording()" style="display: none;">
                ⏹ Stop Recording
            </button>
        </div>

        <div class="feedback-section" id="feedbackSection">
            <h3 class="feedback-title">📊 Tajweed Analysis Results</h3>
            
            <div class="accuracy-display" id="accuracyDisplay">
                <div class="accuracy-score" id="accuracyScore">--</div>
                <div class="accuracy-label">Accuracy Score</div>
                <div class="accuracy-rating" id="accuracyRating">Analyzing...</div>
            </div>

            <audio id="recordedAudio" class="audio-player" controls></audio>

            <div class="tajweed-analysis" id="tajweedAnalysis">
                <h4 style="color: var(--color-gold); font-family: 'Amiri', serif; margin-bottom: 15px; font-size: 1.1rem;">
                    🎯 Detailed Analysis
                </h4>
                <div id="analysisDetails"></div>
            </div>

            <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(227, 216, 136, 0.2);">
                <button class="btn-practice btn-new-verse" onclick="loadRandomAyah(); hideFeedback();" style="margin: 0;">
                    🔄 Try Another Verse
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Analyzing Overlay -->
<div class="analyzing-overlay" id="analyzingOverlay">
    <div class="analyzing-content">
        <div class="analyzing-spinner"></div>
        <div class="analyzing-text">Analyzing Your Recitation...</div>
        <div class="analyzing-subtext">Please wait while we evaluate your Tajweed</div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let mediaRecorder;
    let audioChunks = [];
    let recordingTimer;
    let recordingSeconds = 0;
    let currentSurah = null;
    let currentAyah = null;
    let currentAudioUrl = null;

    // Load random ayah on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadRandomAyah();
    });

    async function loadRandomAyah() {
        try {
            // Random surah (1-114)
            const surahNumber = Math.floor(Math.random() * 114) + 1;
            
            // First, get surah info to know how many ayahs
            const surahResponse = await fetch(`https://api.alquran.cloud/v1/surah/${surahNumber}`);
            const surahData = await surahResponse.json();
            
            if (surahData.status === 'OK') {
                const totalAyahs = surahData.data.numberOfAyahs;
                const ayahNumber = Math.floor(Math.random() * totalAyahs) + 1;
                
                // Fetch the specific ayah with Arabic text, translation, and audio
                const [arabicResponse, translationResponse] = await Promise.all([
                    fetch(`https://api.alquran.cloud/v1/ayah/${surahNumber}:${ayahNumber}/ar.alafasy`),
                    fetch(`https://api.alquran.cloud/v1/ayah/${surahNumber}:${ayahNumber}/en.asad`)
                ]);

                const arabicData = await arabicResponse.json();
                const translationData = await translationResponse.json();

                if (arabicData.status === 'OK' && translationData.status === 'OK') {
                    currentSurah = surahNumber;
                    currentAyah = ayahNumber;
                    currentAudioUrl = arabicData.data.audio;

                    document.getElementById('ayahArabic').textContent = arabicData.data.text;
                    document.getElementById('surahInfo').textContent = `Surah ${arabicData.data.surah.englishName} (${arabicData.data.surah.name})`;
                    document.getElementById('ayahInfo').textContent = `Ayah ${ayahNumber}`;
                    document.getElementById('ayahTranslation').textContent = translationData.data.text;
                    
                    // Set reference audio
                    const audioElement = document.getElementById('referenceAudio');
                    audioElement.src = currentAudioUrl;
                    audioElement.style.display = 'block';
                }
            }
        } catch (error) {
            console.error('Error loading ayah:', error);
            document.getElementById('ayahArabic').textContent = 'Error loading verse. Please try again.';
        }
    }

    function playReferenceAudio() {
        const audioElement = document.getElementById('referenceAudio');
        audioElement.play();
    }

    function hideFeedback() {
        document.getElementById('feedbackSection').classList.remove('show');
    }

    async function startRecording() {
        try {
            console.log('=== Starting Practice Recording ===');
            console.log('Requesting microphone access...');
            
            // Check if getUserMedia is supported
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Your browser does not support audio recording. Please use HTTPS or a modern browser (Chrome, Firefox, Edge).');
            }
            
            const stream = await navigator.mediaDevices.getUserMedia({ 
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    sampleRate: 44100
                }
            });
            console.log('✓ Microphone access granted');
            
            // Determine best MIME type
            let mimeType = 'audio/webm;codecs=opus';
            if (!MediaRecorder.isTypeSupported(mimeType)) {
                mimeType = 'audio/webm';
            }
            console.log('Using MIME type:', mimeType);
            
            mediaRecorder = new MediaRecorder(stream, { mimeType: mimeType });
            audioChunks = [];
            recordingSeconds = 0;

            mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    audioChunks.push(event.data);
                    console.log('Audio chunk collected:', event.data.size, 'bytes');
                }
            };

            mediaRecorder.onstop = async () => {
                console.log('Recording stopped, processing audio...');
                
                if (audioChunks.length === 0) {
                    console.error('⚠️ No audio chunks collected');
                    alert('⚠️ No audio was recorded. Please try again.');
                    return;
                }
                
                const audioBlob = new Blob(audioChunks, { type: mimeType });
                console.log('Audio blob created:', audioBlob.size, 'bytes');
                
                // Create audio URL for playback after analysis
                const audioUrl = URL.createObjectURL(audioBlob);
                document.getElementById('recordedAudio').src = audioUrl;
                console.log('✓ Audio ready for playback');
                
                // Show analyzing overlay
                document.getElementById('analyzingOverlay').classList.add('show');

                // Submit recording to server for analysis
                const formData = new FormData();
                formData.append('audio_file', audioBlob, 'recording.webm');
                formData.append('surah_number', currentSurah);
                formData.append('ayah_number', currentAyah);
                formData.append('expected_text', document.getElementById('ayahArabic').textContent);
                
                console.log('Submitting to server:');
                console.log('- Surah:', currentSurah);
                console.log('- Ayah:', currentAyah);
                console.log('- Audio size:', audioBlob.size, 'bytes');

                try {
                    console.log('Sending request to:', '{{ route('student.practice.submit') }}');
                    
                    const response = await fetch('{{ route('student.practice.submit') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });
                    
                    console.log('Response status:', response.status, response.statusText);
                    
                    const result = await response.json();
                    console.log('Server response:', result);
                    
                    // Hide analyzing overlay
                    document.getElementById('analyzingOverlay').classList.remove('show');
                    
                    if (result.success) {
                        console.log('✓ Analysis successful');
                        console.log('Overall score:', result.analysis.accuracy_score);
                        console.log('Details:', result.analysis.details);
                        
                        // Display results
                        displayAnalysisResults(result.analysis);
                        
                        // Show feedback section
                        const feedbackSection = document.getElementById('feedbackSection');
                        feedbackSection.classList.add('show');
                        
                        // Smooth scroll to feedback section
                        setTimeout(() => {
                            feedbackSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }, 100);
                    } else {
                        console.error('❌ Server error:', result.message);
                        alert('Error analyzing recording: ' + (result.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('❌ Error submitting recording:', error);
                    document.getElementById('analyzingOverlay').classList.remove('show');
                    alert('Error analyzing your recitation. Please try again.');
                }

                // Stop all tracks
                stream.getTracks().forEach(track => {
                    track.stop();
                    console.log('Audio track stopped');
                });
            };

            mediaRecorder.start(1000); // Collect data every 1 second
            console.log('✓ Recording started');
            
            document.getElementById('recordBtn').style.display = 'none';
            document.getElementById('stopBtn').style.display = 'inline-flex';
            document.getElementById('recordingStatus').classList.add('active');

            // Start timer
            recordingTimer = setInterval(() => {
                recordingSeconds++;
                const minutes = Math.floor(recordingSeconds / 60).toString().padStart(2, '0');
                const seconds = (recordingSeconds % 60).toString().padStart(2, '0');
                document.getElementById('recordingTimer').textContent = `${minutes}:${seconds}`;
            }, 1000);

        } catch (error) {
            console.error('❌ Error accessing microphone:', error);
            
            if (error.name === 'NotAllowedError') {
                alert('🎤 Microphone permission denied.\n\nPlease allow microphone access and try again.');
            } else if (error.name === 'NotFoundError') {
                alert('🎤 No microphone detected.\n\nPlease connect a microphone and try again.');
            } else {
                alert('Could not access microphone: ' + error.message);
            }
        }
    }

    function stopRecording() {
        console.log('Stopping recording...');
        
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            console.log('✓ MediaRecorder stopped');
        }
        
        if (recordingTimer) {
            clearInterval(recordingTimer);
            console.log('✓ Timer cleared');
        }
        
        document.getElementById('recordBtn').style.display = 'inline-flex';
        document.getElementById('stopBtn').style.display = 'none';
        document.getElementById('recordingStatus').classList.remove('active');
    }

    function displayAnalysisResults(analysis) {
        console.log('=== Displaying Analysis Results ===');
        console.log('Full analysis:', analysis);
        
        // Display accuracy score
        const score = analysis.accuracy_score || 0;
        document.getElementById('accuracyScore').textContent = score + '%';
        console.log('Overall score:', score + '%');
        
        // Determine rating
        let rating = '';
        let ratingClass = '';
        if (score >= 90) {
            rating = '⭐ Excellent! Mashallah!';
            ratingClass = 'excellent';
        } else if (score >= 80) {
            rating = '👍 Very Good!';
            ratingClass = 'good';
        } else if (score >= 70) {
            rating = '✓ Good';
            ratingClass = 'fair';
        } else if (score >= 60) {
            rating = '📖 Keep Practicing';
            ratingClass = 'needs-improvement';
        } else {
            rating = '💪 Needs More Practice';
            ratingClass = 'poor';
        }
        
        console.log('Rating:', rating, '(' + ratingClass + ')');
        
        const ratingElement = document.getElementById('accuracyRating');
        ratingElement.textContent = rating;
        ratingElement.className = 'accuracy-rating ' + ratingClass;
        
        // Display detailed analysis
        const detailsContainer = document.getElementById('analysisDetails');
        detailsContainer.innerHTML = '';
        
        if (analysis.details) {
            const details = analysis.details;
            const pythonAnalysis = analysis.python_analysis;
            
            // Tajweed rules with detailed Madd analysis
            if (details.tajweed_rules !== undefined) {
                let maddDetails = '';
                if (pythonAnalysis && pythonAnalysis.madd_analysis) {
                    const madd = pythonAnalysis.madd_analysis;
                    maddDetails = `
                        <div style="margin-top: 10px; padding: 12px; background: rgba(0,0,0,0.3); border-radius: 6px; font-size: 0.85rem;">
                            <strong style="color: var(--color-gold);">Madd (Elongation) Analysis:</strong><br>
                            <div style="margin-top: 8px; color: rgba(255,255,255,0.8);">
                                📊 Total: ${madd.total_elongations} | ✅ Correct: ${madd.correct_elongations} | Accuracy: ${madd.percentage.toFixed(1)}%
                            </div>
                            ${madd.issues && madd.issues.length > 0 ? `
                                <div style="margin-top: 10px;">
                                    <strong style="color: #ff6b6b;">Issues Found:</strong>
                                    ${madd.issues.map(issue => `
                                        <div style="margin-top: 6px; padding: 8px; background: rgba(255,107,107,0.1); border-left: 3px solid #ff6b6b; border-radius: 4px;">
                                            <div>⏰ Time: ${issue.time}s | Duration: ${issue.duration}s</div>
                                            <div style="margin-top: 4px;">❌ ${issue.issue}</div>
                                            <div style="margin-top: 4px; color: var(--color-light-green);">💡 ${issue.recommendation}</div>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : ''}
                            ${madd.details && madd.details.filter(d => d.status === 'correct').length > 0 ? `
                                <div style="margin-top: 10px;">
                                    <strong style="color: #51d488;">Correct Elongations:</strong>
                                    ${madd.details.filter(d => d.status === 'correct').map(detail => `
                                        <div style="margin-top: 6px; padding: 8px; background: rgba(81,212,136,0.1); border-left: 3px solid #51d488; border-radius: 4px;">
                                            ✅ Time: ${detail.time}s | Duration: ${detail.duration}s | ${detail.note}
                                        </div>
                                    `).join('')}
                                </div>
                            ` : ''}
                        </div>
                    `;
                }
                detailsContainer.innerHTML += createAnalysisItemWithDetails(
                    'Tajweed Rules (Madd)',
                    details.tajweed_rules + '%',
                    details.tajweed_rules >= 70,
                    maddDetails
                );
            }
            
            // Makharij with detailed Noon Sakin analysis
            if (details.makharij !== undefined) {
                let noonSakinDetails = '';
                if (pythonAnalysis && pythonAnalysis.noon_sakin_analysis) {
                    const noon = pythonAnalysis.noon_sakin_analysis;
                    noonSakinDetails = `
                        <div style="margin-top: 10px; padding: 12px; background: rgba(0,0,0,0.3); border-radius: 6px; font-size: 0.85rem;">
                            <strong style="color: var(--color-gold);">Noon Sakin/Tanween Analysis:</strong><br>
                            <div style="margin-top: 8px; color: rgba(255,255,255,0.8);">
                                📊 Total: ${noon.total_occurrences} | ✅ Correct: ${noon.correct_pronunciation} | Accuracy: ${noon.percentage.toFixed(1)}%
                            </div>
                            ${noon.issues && noon.issues.length > 0 ? `
                                <div style="margin-top: 10px;">
                                    <strong style="color: #ff6b6b;">Issues Found:</strong>
                                    ${noon.issues.map(issue => `
                                        <div style="margin-top: 6px; padding: 8px; background: rgba(255,107,107,0.1); border-left: 3px solid #ff6b6b; border-radius: 4px;">
                                            <div>⏰ Time: ${issue.time}s | Rule: ${issue.rule_type}</div>
                                            <div style="margin-top: 4px;">❌ ${issue.issue}</div>
                                            <div style="margin-top: 4px; color: var(--color-light-green);">💡 ${issue.recommendation}</div>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : ''}
                            ${noon.details && noon.details.filter(d => d.status === 'correct').length > 0 ? `
                                <div style="margin-top: 10px;">
                                    <strong style="color: #51d488;">Correct Pronunciations:</strong>
                                    ${noon.details.filter(d => d.status === 'correct').map(detail => `
                                        <div style="margin-top: 6px; padding: 8px; background: rgba(81,212,136,0.1); border-left: 3px solid #51d488; border-radius: 4px;">
                                            ✅ Time: ${detail.time}s | Rule: ${detail.rule_type} | ${detail.note}
                                        </div>
                                    `).join('')}
                                </div>
                            ` : ''}
                        </div>
                    `;
                }
                detailsContainer.innerHTML += createAnalysisItemWithDetails(
                    'Makharij & Noon Sakin',
                    details.makharij + '%',
                    details.makharij >= 70,
                    noonSakinDetails
                );
            }
            
            // Pronunciation accuracy
            if (details.pronunciation !== undefined) {
                detailsContainer.innerHTML += createAnalysisItem(
                    'Overall Pronunciation',
                    details.pronunciation + '%',
                    details.pronunciation >= 70
                );
            }
            
            // Fluency
            if (details.fluency !== undefined) {
                detailsContainer.innerHTML += createAnalysisItem(
                    'Fluency',
                    details.fluency + '%',
                    details.fluency >= 70
                );
            }
            
            // Add feedback message
            if (analysis.feedback) {
                detailsContainer.innerHTML += `
                    <div style="margin-top: 15px; padding: 15px; background: rgba(227, 216, 136, 0.1); border-radius: 8px; border-left: 4px solid var(--color-gold);">
                        <div style="font-size: 0.9rem; color: var(--color-light-green); line-height: 1.6;">
                            <strong style="color: var(--color-gold);">💡 Feedback:</strong><br>
                            ${analysis.feedback}
                        </div>
                    </div>
                `;
            }
        } else {
            detailsContainer.innerHTML = `
                <div style="text-align: center; padding: 20px; color: var(--color-light-green); opacity: 0.7;">
                    Analysis completed successfully!
                </div>
            `;
        }
    }

    function createAnalysisItem(label, value, isCorrect) {
        const correctClass = isCorrect ? 'correct' : 'incorrect';
        const icon = isCorrect ? '✓' : '⚠';
        return `
            <div class="analysis-item ${correctClass}">
                <span class="analysis-label">${label}</span>
                <span class="analysis-value ${correctClass}">${icon} ${value}</span>
            </div>
        `;
    }

    function createAnalysisItemWithDetails(label, value, isCorrect, detailsHtml) {
        const correctClass = isCorrect ? 'correct' : 'incorrect';
        const icon = isCorrect ? '✓' : '⚠';
        const itemId = 'analysis-' + label.replace(/\s+/g, '-').toLowerCase();
        
        return `
            <div class="analysis-item-expandable ${correctClass}" 
                 onmouseenter="document.getElementById('${itemId}-details').style.maxHeight = '1000px'; document.getElementById('${itemId}-details').style.opacity = '1';"
                 onmouseleave="document.getElementById('${itemId}-details').style.maxHeight = '0'; document.getElementById('${itemId}-details').style.opacity = '0';"
                 style="cursor: pointer; position: relative;">
                <div class="analysis-item ${correctClass}">
                    <span class="analysis-label">${label} 👁️</span>
                    <span class="analysis-value ${correctClass}">${icon} ${value}</span>
                </div>
                <div id="${itemId}-details" style="max-height: 0; opacity: 0; overflow: hidden; transition: all 0.3s ease;">
                    ${detailsHtml}
                </div>
            </div>
        `;
    }
    
    // Page loaded
    console.log('✓ Practice interface ready');
    console.log('Available features: Record, Upload, Analyze');
</script>
@endsection
