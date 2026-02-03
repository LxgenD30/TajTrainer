@extends('layouts.dashboard')

@section('title', 'Quran Practice')
@section('user-role', 'Student • Practice Mode')

@section('navigation')
    <a href="{{ route('student.dashboard') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-label">Dashboard</div>
    </a>
    
    <a href="{{ route('student.classes') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="nav-label">My Classes</div>
    </a>
    
    <a href="{{ route('student.practice') }}" class="nav-item active">
        <div class="nav-icon">
            <i class="fas fa-microphone-alt"></i>
        </div>
        <div class="nav-label">Practice</div>
    </a>
    
    <a href="{{ route('student.progress') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="nav-label">My Progress</div>
    </a>
    
    <a href="{{ route('student.materials') }}" class="nav-item">
        <div class="nav-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="nav-label">Materials</div>
    </a>
@endsection

@section('extra-styles')
<style>
    :root {
        --turquoise: #1abc9c;
        --coral: #e74c3c;
        --purple: #9b59b6;
        --sky-blue: #3498db;
        --sunflower: #f1c40f;
        --orange: #e67e22;
    }
    
    /* Page Header */
    .page-header {
        padding: 40px 0 30px;
        text-align: center;
    }
    
    .page-title h1 {
        font-size: 2.8rem;
        color: var(--primary-green);
        margin-bottom: 10px;
    }
    
    .page-title p {
        font-size: 1.2rem;
        color: #666;
        max-width: 700px;
        margin: 0 auto;
    }
    
    /* Practice Container */
    .practice-container {
        padding: 20px 0 80px;
    }
    
    .practice-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    
    @media (max-width: 992px) {
        .practice-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Verse Display Card */
    .verse-card {
        background: var(--white);
        border-radius: 25px;
        padding: 35px;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.15);
        border: 1px solid rgba(10, 92, 54, 0.1);
        height: fit-content;
        position: sticky;
        top: 140px;
    }
    
    .verse-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .verse-header h3 {
        font-size: 1.5rem;
        color: var(--primary-green);
    }
    
    .verse-controls {
        display: flex;
        gap: 10px;
    }
    
    .verse-arabic {
        font-family: 'Amiri', serif;
        font-size: 2.2rem;
        line-height: 1.8;
        color: var(--dark-green);
        text-align: center;
        margin-bottom: 25px;
        padding: 25px;
        background: rgba(10, 92, 54, 0.03);
        border-radius: 15px;
        border: 2px solid rgba(10, 92, 54, 0.1);
        direction: rtl;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Tajweed color markers */
    .tajweed-green { color: #22c55e; font-weight: 600; }
    .tajweed-blue { color: #3b82f6; font-weight: 600; }
    .tajweed-red { color: #ef4444; font-weight: 600; }
    .tajweed-yellow { color: #eab308; font-weight: 600; }
    .tajweed-purple { color: #a855f7; font-weight: 600; }
    .tajweed-orange { color: #f97316; font-weight: 600; }
    .tajweed-grey { color: #9ca3af; }
    .tajweed-lime { color: #84cc16; font-weight: 600; }
    
    .verse-info {
        display: flex;
        justify-content: center;
        gap: 25px;
        margin-bottom: 20px;
        padding: 15px;
        background: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
    }
    
    .verse-info-item {
        text-align: center;
    }
    
    .verse-info-label {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 5px;
    }
    
    .verse-info-value {
        font-size: 1.2rem;
        color: var(--primary-green);
        font-weight: 600;
    }
    
    .verse-translation {
        background: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
        border-left: 4px solid var(--gold);
    }
    
    .verse-translation h4 {
        font-size: 1.1rem;
        color: var(--dark-green);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .verse-translation p {
        color: #666;
        line-height: 1.6;
        font-size: 1rem;
    }
    
    /* Recording Card */
    .recording-card {
        background: var(--white);
        border-radius: 25px;
        padding: 35px;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.15);
        border: 1px solid rgba(10, 92, 54, 0.1);
    }
    
    .recording-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .recording-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        background: linear-gradient(135deg, var(--coral), var(--orange));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: var(--white);
    }
    
    .recording-header h3 {
        font-size: 1.5rem;
        color: var(--primary-green);
    }
    
    /* Recording Status */
    .recording-status {
        background: rgba(231, 76, 60, 0.08);
        border: 2px solid var(--coral);
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        margin-bottom: 25px;
        display: none;
    }
    
    .recording-status.active {
        display: block;
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .recording-status h4 {
        color: var(--coral);
        margin-bottom: 10px;
        font-size: 1.2rem;
    }
    
    .recording-timer {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--coral);
        font-family: 'El Messiri', sans-serif;
        margin: 10px 0;
    }
    
    .recording-wave {
        display: flex;
        justify-content: center;
        gap: 4px;
        height: 40px;
        margin-top: 15px;
    }
    
    .recording-wave span {
        width: 4px;
        background: var(--coral);
        border-radius: 2px;
        animation: wave 1s ease-in-out infinite;
    }
    
    .recording-wave span:nth-child(2) { animation-delay: 0.1s; }
    .recording-wave span:nth-child(3) { animation-delay: 0.2s; }
    .recording-wave span:nth-child(4) { animation-delay: 0.3s; }
    .recording-wave span:nth-child(5) { animation-delay: 0.4s; }
    
    @keyframes wave {
        0%, 100% { height: 10px; }
        50% { height: 40px; }
    }
    
    /* Recording Controls */
    .recording-controls {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .record-btn {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--coral), #c0392b);
        border: none;
        color: var(--white);
        font-size: 2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(231, 76, 60, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    .record-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 35px rgba(231, 76, 60, 0.4);
    }
    
    .record-btn.recording {
        background: linear-gradient(135deg, #e74c3c, #e74c3c);
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { box-shadow: 0 10px 25px rgba(231, 76, 60, 0.3); }
        50% { box-shadow: 0 10px 35px rgba(231, 76, 60, 0.5); }
    }
    
    .record-label {
        font-size: 1.1rem;
        color: var(--dark-green);
        margin-top: 15px;
        font-weight: 600;
    }
    
    /* Secondary Controls */
    .secondary-controls {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 25px;
    }
    
    .control-btn {
        padding: 12px 25px;
        border-radius: 50px;
        border: 2px solid var(--primary-green);
        background: var(--white);
        color: var(--primary-green);
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .control-btn:hover {
        background: var(--primary-green);
        color: var(--white);
        transform: translateY(-3px);
    }
    
    /* Feedback Section */
    .feedback-section {
        display: none;
        animation: slideUp 0.5s ease;
    }
    
    .feedback-section.show {
        display: block;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .feedback-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(10, 92, 54, 0.1);
    }
    
    .feedback-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        background: linear-gradient(135deg, var(--turquoise), #16a085);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: var(--white);
    }
    
    .feedback-header h3 {
        font-size: 1.5rem;
        color: var(--primary-green);
    }
    
    /* Accuracy Display */
    .accuracy-display {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        border-radius: 20px;
        padding: 35px;
        text-align: center;
        color: var(--white);
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(10, 92, 54, 0.2);
    }
    
    .accuracy-score {
        font-size: 5rem;
        font-weight: bold;
        color: var(--gold);
        font-family: 'El Messiri', sans-serif;
        line-height: 1;
        margin-bottom: 10px;
    }
    
    .accuracy-label {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 10px;
    }
    
    .accuracy-rating {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 15px;
    }
    
    /* Analysis Grid */
    .analysis-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .analysis-card {
        background: var(--white);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 8px 20px rgba(10, 92, 54, 0.1);
        border: 1px solid rgba(10, 92, 54, 0.1);
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .analysis-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(10, 92, 54, 0.15);
    }
    
    .analysis-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--white);
        margin: 0 auto 15px;
    }
    
    .icon-makharij { background: linear-gradient(135deg, var(--sky-blue), #2980b9); }
    .icon-tajweed { background: linear-gradient(135deg, var(--purple), #8e44ad); }
    .icon-pronunciation { background: linear-gradient(135deg, var(--turquoise), #16a085); }
    .icon-fluency { background: linear-gradient(135deg, var(--sunflower), #f59e0b); }
    
    .analysis-label {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 8px;
    }
    
    .analysis-score {
        font-size: 1.8rem;
        font-weight: bold;
        color: var(--dark-green);
        font-family: 'El Messiri', sans-serif;
    }
    
    .analysis-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 8px;
    }
    
    .status-good { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
    .status-fair { background: rgba(234, 179, 8, 0.1); color: #eab308; }
    .status-poor { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    
    /* Tajweed Analysis Details */
    .tajweed-details {
        background: rgba(10, 92, 54, 0.05);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .tajweed-details h4 {
        font-size: 1.2rem;
        color: var(--primary-green);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .tajweed-items {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .tajweed-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: var(--white);
        border-radius: 10px;
        border-left: 4px solid var(--primary-green);
    }
    
    .tajweed-item.correct { border-left-color: #22c55e; }
    .tajweed-item.incorrect { border-left-color: #ef4444; }
    
    .tajweed-name {
        font-size: 0.95rem;
        color: var(--dark-green);
    }
    
    .tajweed-value {
        font-weight: 600;
        font-size: 0.95rem;
    }
    
    .tajweed-value.correct { color: #22c55e; }
    .tajweed-value.incorrect { color: #ef4444; }
    
    /* Audio Player */
    .audio-player-container {
        background: rgba(10, 92, 54, 0.05);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .audio-player-container h4 {
        font-size: 1.2rem;
        color: var(--primary-green);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .audio-player {
        width: 100%;
        border-radius: 10px;
    }
    
    /* Next Steps */
    .next-steps {
        text-align: center;
        margin-top: 30px;
    }
    
    .next-btn {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: var(--white);
        padding: 15px 35px;
        border-radius: 50px;
        border: none;
        font-family: 'El Messiri', sans-serif;
        font-weight: 600;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .next-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(10, 92, 54, 0.3);
    }
    
    /* Analyzing Overlay */
    .analyzing-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        display: none;
    }
    
    .analyzing-overlay.show {
        display: flex;
    }
    
    .analyzing-content {
        background: var(--white);
        border-radius: 25px;
        padding: 50px;
        text-align: center;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
    }
    
    .analyzing-spinner {
        width: 80px;
        height: 80px;
        border: 5px solid rgba(10, 92, 54, 0.1);
        border-radius: 50%;
        border-top-color: var(--primary-green);
        animation: spin 1s linear infinite;
        margin: 0 auto 25px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .analyzing-text {
        font-size: 1.5rem;
        color: var(--primary-green);
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .analyzing-subtext {
        font-size: 1rem;
        color: #666;
    }
    
    /* Reference Audio Section */
    .reference-section {
        background: rgba(10, 92, 54, 0.05);
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
        display: none;
    }
    
    .reference-section.show {
        display: block;
    }
    
    .reference-section h4 {
        font-size: 1.1rem;
        color: var(--primary-green);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .tajweed-text {
        direction: rtl;
        font-family: 'Amiri', serif;
        font-size: 1.8rem;
        line-height: 2;
        text-align: center;
        background: rgba(255, 255, 255, 0.5);
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
    }
    
    /* Responsive Design */
    @media (max-width: 992px) {
        .verse-card {
            position: static;
            margin-bottom: 30px;
        }
        
        .verse-controls {
            flex-direction: column;
            width: 100%;
        }
        
        .verse-controls button {
            width: 100%;
        }
        
        .analysis-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .secondary-controls {
            flex-direction: column;
        }
        
        .control-btn {
            width: 100%;
            justify-content: center;
        }
    }
    
    @media (max-width: 768px) {
        .page-title h1 {
            font-size: 2.2rem;
        }
        
        .verse-arabic {
            font-size: 1.8rem;
            min-height: 150px;
        }
        
        .analysis-grid {
            grid-template-columns: 1fr;
        }
        
        .record-btn {
            width: 100px;
            height: 100px;
        }
        
        .verse-card,
        .recording-card {
            padding: 25px;
        }
    }
</style>
@endsection

@section('content')
<main class="practice-container">
    <div class="practice-grid">
        <!-- Verse Display Card -->
        <div class="verse-card">
            <div class="verse-header">
                <h3>Practice Verse</h3>
                <div class="verse-controls">
                    <button class="control-btn" onclick="loadRandomAyah()">
                        <i class="fas fa-sync-alt"></i>
                        New Verse
                    </button>
                    <button class="control-btn" id="showReferenceBtn" onclick="toggleReferenceSection()">
                        <i class="fas fa-volume-up"></i>
                        Reference
                    </button>
                </div>
            </div>

            <div class="verse-arabic" id="ayahArabic">
                Loading verse...
            </div>

            <div class="verse-info">
                <div class="verse-info-item">
                    <div class="verse-info-label">Surah</div>
                    <div class="verse-info-value" id="surahInfo">---</div>
                </div>
                <div class="verse-info-item">
                    <div class="verse-info-label">Ayah</div>
                    <div class="verse-info-value" id="ayahInfo">---</div>
                </div>
            </div>

            <!-- Reference Audio Section -->
            <div class="reference-section" id="referenceSection">
                <h4><i class="fas fa-headphones"></i> Reference Recitation (Sheikh Mishary Alafasy)</h4>
                <audio id="referenceAudio" controls class="audio-player"></audio>
                <p style="font-size: 0.9rem; color: #666; margin-top: 10px; text-align: center;">
                    📚 Tajweed Color Guide
                </p>
                <div class="tajweed-text" id="tajweedText">
                    Loading tajweed text...
                </div>
            </div>

            <div class="verse-translation">
                <h4><i class="fas fa-language"></i> Translation</h4>
                <p id="ayahTranslation">
                    Loading translation...
                </p>
            </div>
        </div>

        <!-- Recording Card -->
        <div class="recording-card">
            <div class="recording-header">
                <div class="recording-icon">
                    <i class="fas fa-microphone-alt"></i>
                </div>
                <h3>Record Your Recitation</h3>
            </div>

            <!-- Recording Status -->
            <div class="recording-status" id="recordingStatus">
                <h4>Recording in Progress</h4>
                <div class="recording-timer" id="recordingTimer">00:00</div>
                <p>Speak clearly into your microphone</p>
                <div class="recording-wave">
                    <span></span><span></span><span></span><span></span><span></span>
                </div>
            </div>

            <!-- Recording Controls -->
            <div class="recording-controls">
                <button class="record-btn" id="recordBtn" onclick="startRecording()">
                    <i class="fas fa-microphone"></i>
                </button>
                <div class="record-label" id="recordLabel">Click to Start Recording</div>
            </div>

            <!-- Secondary Controls -->
            <div class="secondary-controls">
                <button class="control-btn" onclick="loadRandomAyah(); resetPractice();">
                    <i class="fas fa-redo"></i>
                    New Verse
                </button>
            </div>

            <!-- Feedback Section -->
            <div class="feedback-section" id="feedbackSection">
                <div class="feedback-header">
                    <div class="feedback-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analysis Results</h3>
                </div>

                <!-- Accuracy Display -->
                <div class="accuracy-display">
                    <div class="accuracy-score" id="accuracyScore">--</div>
                    <div class="accuracy-label">Overall Accuracy</div>
                    <div class="accuracy-rating" id="accuracyRating">Analyzing...</div>
                </div>

                <!-- Recorded Audio Player -->
                <div class="audio-player-container">
                    <h4><i class="fas fa-play-circle"></i> Your Recording</h4>
                    <audio id="recordedAudio" controls class="audio-player"></audio>
                </div>

                <!-- Tajweed Details -->
                <div class="tajweed-details" id="tajweedDetails">
                    <h4><i class="fas fa-search"></i> Detailed Analysis</h4>
                    <div class="tajweed-items" id="analysisDetails">
                        <!-- Filled by JavaScript -->
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="next-steps">
                    <button class="next-btn" onclick="loadRandomAyah(); resetPractice();">
                        <i class="fas fa-redo"></i>
                        Practice Another Verse
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Analyzing Overlay -->
<div class="analyzing-overlay" id="analyzingOverlay">
    <div class="analyzing-content">
        <div class="analyzing-spinner"></div>
        <div class="analyzing-text">Analyzing Your Recitation</div>
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
    let currentTajweedText = '';
    let referenceSectionVisible = false;

    // Load random ayah on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadRandomAyah();
    });

    async function loadRandomAyah() {
        try {
            // Random surah (1-114)
            const surahNumber = Math.floor(Math.random() * 114) + 1;
            console.log('🔄 Loading random ayah from Surah:', surahNumber);
            
            // First, get surah info to know how many ayahs
            const surahResponse = await fetch(`https://api.alquran.cloud/v1/surah/${surahNumber}`);
            
            if (!surahResponse.ok) {
                console.error('❌ Failed to fetch surah info:', surahResponse.status, surahResponse.statusText);
                throw new Error(`Failed to fetch surah: ${surahResponse.status}`);
            }
            
            const surahData = await surahResponse.json();
            console.log('✅ Surah data received:', surahData);
            
            if (surahData.status === 'OK') {
                const totalAyahs = surahData.data.numberOfAyahs;
                const ayahNumber = Math.floor(Math.random() * totalAyahs) + 1;
                console.log(`🎯 Selected Ayah ${ayahNumber} from ${totalAyahs} ayahs`);
                
                // Fetch the specific ayah with Arabic text, tajweed text, translation, and audio
                console.log('📡 Fetching ayah data with 3 parallel requests...');
                const [arabicResponse, tajweedResponse, translationResponse] = await Promise.all([
                    fetch(`https://api.alquran.cloud/v1/ayah/${surahNumber}:${ayahNumber}/ar.alafasy`),
                    fetch(`https://api.alquran.cloud/v1/ayah/${surahNumber}:${ayahNumber}/quran-tajweed`),
                    fetch(`https://api.alquran.cloud/v1/ayah/${surahNumber}:${ayahNumber}/en.asad`)
                ]);

                // Check responses
                if (!arabicResponse.ok) console.error('❌ Arabic response failed:', arabicResponse.status);
                if (!tajweedResponse.ok) console.warn('⚠️ Tajweed response failed:', tajweedResponse.status);
                if (!translationResponse.ok) console.error('❌ Translation response failed:', translationResponse.status);

                const arabicData = await arabicResponse.json();
                const tajweedData = await tajweedResponse.json();
                const translationData = await translationResponse.json();

                console.log('📦 API Responses:', {
                    arabic: arabicData.status,
                    tajweed: tajweedData.status,
                    translation: translationData.status
                });

                if (arabicData.status === 'OK' && translationData.status === 'OK') {
                    currentSurah = surahNumber;
                    currentAyah = ayahNumber;
                    currentAudioUrl = arabicData.data.audio;
                    currentTajweedText = tajweedData.status === 'OK' ? tajweedData.data.text : arabicData.data.text;

                    console.log('🎵 Audio URL:', currentAudioUrl);

                    document.getElementById('ayahArabic').textContent = arabicData.data.text;
                    document.getElementById('surahInfo').textContent = `Surah ${arabicData.data.surah.englishName} (${arabicData.data.surah.name})`;
                    document.getElementById('ayahInfo').textContent = `Ayah ${ayahNumber}`;
                    document.getElementById('ayahTranslation').textContent = translationData.data.text;
                    
                    // Set reference section data
                    document.getElementById('referenceAudio').src = currentAudioUrl;
                    document.getElementById('referenceVerseInfo').textContent = `Surah ${arabicData.data.surah.englishName} (${arabicData.data.surah.name}) - Ayah ${ayahNumber}`;
                    document.getElementById('tajweedText').innerHTML = parseTajweedText(currentTajweedText);
                    
                    // Show reference button
                    document.getElementById('showReferenceBtn').style.display = 'inline-flex';
                    
                    // Hide reference section when loading new verse
                    referenceSectionVisible = false;
                    document.getElementById('referenceSection').style.display = 'none';
                    
                    console.log('✅ Ayah loaded successfully!');
                } else {
                    console.error('❌ API returned non-OK status:', { arabicData, translationData });
                    throw new Error('API returned error status');
                }
            } else {
                console.error('❌ Surah data status not OK:', surahData);
                throw new Error('Surah API returned error status');
            }
        } catch (error) {
            console.error('💥 Error loading ayah:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack
            });
            document.getElementById('ayahArabic').textContent = 'Error loading verse. Please try again.';
            document.getElementById('ayahTranslation').textContent = 'Check console for details.';
        }
    }

    function toggleReferenceSection() {
        referenceSectionVisible = !referenceSectionVisible;
        const section = document.getElementById('referenceSection');
        const btn = document.getElementById('showReferenceBtn');
        
        if (referenceSectionVisible) {
            section.style.display = 'block';
            btn.innerHTML = '🔽 Hide Reference';
        } else {
            section.style.display = 'none';
            btn.innerHTML = '🔊 Show Reference';
        }
    }
    
    function parseTajweedText(text) {
        // Parse AlQuran.cloud tajweed markers and apply colors
        const colorMap = {
            '[a': 'green',      // Idgham Bi Ghunnah
            '[u': 'blue',       // Idgham Bila Ghunnah
            '[n': 'red',        // Madd Normal
            '[p': 'yellow',     // Madd Permissible
            '[m': 'green',      // Madd Necessary
            '[o': 'purple',     // Madd Obligatory
            '[h': 'grey',       // Hamza Wasl
            '[l': 'lime',       // Lam Shamsi
            '[s': 'grey',       // Lam Qamari
            '[q': 'orange'      // Qalqalah
        };
        
        let html = text;
        
        // Replace markers with colored spans
        // Pattern: [marker[text]
        for (const [marker, color] of Object.entries(colorMap)) {
            // Handle markers with colons (like [h:1)
            const markerEscaped = marker.replace('[', '\\[');
            const regex = new RegExp(markerEscaped + '(?::\\d+)?\\[([^\\[\\]]+)', 'g');
            html = html.replace(regex, `<span class="tajweed-${color}">$1</span>`);
        }
        
        return html;
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
            
            // Add OpenAI AI Feedback if available
            if (pythonAnalysis && pythonAnalysis.ai_feedback) {
                const aiFeedback = pythonAnalysis.ai_feedback;
                let aiFeedbackHtml = `
                    <div style="margin-top: 20px; padding: 20px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(31, 39, 27, 0.8) 100%); border: 2px solid rgba(102, 126, 234, 0.4); border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 2px solid rgba(102, 126, 234, 0.3);">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">🤖</div>
                            <div>
                                <div style="color: var(--color-gold); font-weight: 700; font-size: 1.1rem;">AI Recitation Coach</div>
                                <div style="font-size: 0.75rem; color: #b8a3ff; font-weight: 500;">✨ Powered by OpenAI GPT-4</div>
                            </div>
                        </div>
                `;
                
                // Summary
                if (aiFeedback.summary) {
                    aiFeedbackHtml += `
                        <div style="background: rgba(31, 39, 27, 0.6); padding: 15px; border-radius: 10px; margin-bottom: 15px; border-left: 3px solid #667eea;">
                            <div style="color: #b8a3ff; font-weight: 600; margin-bottom: 8px; font-size: 0.95rem;">📊 Summary</div>
                            <div style="color: var(--color-light-green); line-height: 1.7; font-size: 0.9rem;">${aiFeedback.summary}</div>
                        </div>
                    `;
                }
                
                // Strengths and Improvements side by side
                if (aiFeedback.strengths || aiFeedback.improvements) {
                    aiFeedbackHtml += `<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">`;
                    
                    if (aiFeedback.strengths && aiFeedback.strengths.length > 0) {
                        aiFeedbackHtml += `
                            <div style="background: rgba(76, 175, 80, 0.15); padding: 15px; border-radius: 10px; border-left: 3px solid #4caf50;">
                                <div style="color: #4caf50; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">💪 Strengths</div>
                                <ul style="margin: 0; padding-left: 18px; color: var(--color-light-green); line-height: 1.6; font-size: 0.85rem;">
                                    ${aiFeedback.strengths.map(s => `<li style="margin-bottom: 6px;">${s}</li>`).join('')}
                                </ul>
                            </div>
                        `;
                    }
                    
                    if (aiFeedback.improvements && aiFeedback.improvements.length > 0) {
                        aiFeedbackHtml += `
                            <div style="background: rgba(255, 152, 0, 0.15); padding: 15px; border-radius: 10px; border-left: 3px solid #ff9800;">
                                <div style="color: #ff9800; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">🎯 Improve</div>
                                <ul style="margin: 0; padding-left: 18px; color: var(--color-light-green); line-height: 1.6; font-size: 0.85rem;">
                                    ${aiFeedback.improvements.map(i => {
                                        const issue = typeof i === 'object' ? i.issue : i;
                                        return `<li style="margin-bottom: 6px;">${issue}</li>`;
                                    }).join('')}
                                </ul>
                            </div>
                        `;
                    }
                    
                    aiFeedbackHtml += `</div>`;
                }
                
                // Next Steps
                if (aiFeedback.next_steps) {
                    aiFeedbackHtml += `
                        <div style="background: rgba(102, 126, 234, 0.15); padding: 15px; border-radius: 10px; border-left: 3px solid #667eea;">
                            <div style="color: #b8a3ff; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">🚀 Next Steps</div>
                            <div style="color: var(--color-light-green); line-height: 1.7; font-size: 0.85rem;">${aiFeedback.next_steps}</div>
                        </div>
                    `;
                }
                
                aiFeedbackHtml += `</div>`;
                detailsContainer.innerHTML += aiFeedbackHtml;
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
