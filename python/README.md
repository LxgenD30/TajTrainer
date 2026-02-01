# Tajweed Audio Analysis System

## Overview
TajTrainer's audio analysis system uses professional phonetic analysis powered by **Praat Parselmouth** for formant extraction and **FastDTW** for reference audio comparison. This approach provides accurate Tajweed rule detection by analyzing the acoustic properties of recitation.

## Version 2.0 - Phonetic Analysis

### Key Features
- **Formant Analysis**: Uses Praat to analyze vowel quality and Madd elongations
- **Reference Comparison**: Compares student audio with correct recitation using Dynamic Time Warping (DTW)
- **AI Feedback**: OpenAI GPT-4 generates personalized improvement suggestions
- **Comprehensive Logging**: Detailed debugging information for production troubleshooting

## Prerequisites
- Python 3.8 or higher
- pip (Python package manager)
- OpenAI API key (for AI feedback generation)

## Quick Start

### Option 1: Automated Setup (Recommended)

#### Windows
```bash
cd python
setup.bat
```

#### Linux/Mac
```bash
cd python
chmod +x setup.sh
./setup.sh
```

### Option 2: Manual Installation

#### 1. Install Dependencies
```bash
cd python
pip install -r requirements.txt
```

#### 2. Verify Installation
```bash
python check_dependencies.py
```

Expected output:
```
============================================================
TajTrainer Python Dependencies Check
============================================================
✓ numpy                v1.24.3         - numpy
✓ scipy                v1.11.2         - scipy
✓ librosa              v0.10.1         - librosa
✓ parselmouth          v0.4.3          - parselmouth (Praat integration)
✓ fastdtw              v0.3.4          - fastdtw (Dynamic Time Warping)
✓ openai               v1.3.0          - openai (GPT-4 API)
...
✓ All dependencies are installed and functional!
============================================================
```

## Technical Approach

### 1. Formant Analysis (Praat Parselmouth)
The analyzer uses Praat's formant extraction to detect Madd (elongation) rules:

- **Formant 1 (F1)**: Low frequency formant indicating vowel openness
- **Formant 2 (F2)**: Higher frequency formant indicating tongue position
- **Madd Detection**: Sustained formant patterns over time indicate proper elongation

### 2. Reference Audio Comparison (FastDTW)
Compares student recitation with correct recitation from AlQuran.cloud:

- Downloads reference audio from Mishary Alafasy
- Extracts MFCC features from both audios
- Uses Dynamic Time Warping to align sequences
- Calculates similarity score (0-100%)

### 3. AI Feedback Generation (OpenAI GPT-4)
Generates personalized feedback based on analysis results:

- Summary of overall performance
- List of strengths identified
- Specific improvement suggestions
- Next steps for mastery
- Standard technique used in speech recognition systems

### Multi-Feature Analysis
Combines MFCC with complementary features:
- **RMS Energy**: Detects sustained sounds
- **Spectral Centroid**: Analyzes frequency distribution
- **Zero Crossing Rate**: Identifies nasal sounds
- **Spectral Contrast**: Differentiates phoneme types
- **Chroma Features**: Pitch class analysis

## Library Details

### librosa (0.10.1)
- Audio analysis and music information retrieval
- **MFCC extraction**: `librosa.feature.mfcc()`
- Used for: pitch detection, onset detection, spectral analysis, delta features

### scipy (1.11.4)
- Scientific computing
- Used for: signal processing, peak detection with `find_peaks()`

### numpy (1.26.3)
- Numerical computing
- Used for: array operations, statistical computations on MFCC features

### pydub (0.25.1)
- Audio file manipulation
- Used for: audio format conversion, file handling

### python-speech-features (0.6)
- Speech audio feature extraction
- Backup MFCC implementation

### arabic-reshaper (3.0.0)
- Arabic text processing
- Used for: proper Arabic text rendering

## Features

### Madd (Elongation) Detection
**MFCC-Enhanced Analysis:**
- Analyzes MFCC variance to detect sustained vowels
- Low MFCC variance indicates proper elongation
- Validates elongation duration (research-based: ~0.379s per count)
- Detects if elongations meet requirements (2, 4, or 6 counts)
- Provides timestamps with MFCC confidence levels
- Identifies short elongations with specific duration recommendations

**Output includes:**
- Time position of each Madd
- Actual duration vs expected duration
- MFCC confidence level (high/medium)
- Vowel quality verification

### Noon Sakin Analysis
**MFCC-Based Phoneme Classification:**
- Uses MFCC coefficients 1-3 for nasal detection
- Classifies Noon Sakin rule types:
  - **Idhar** (clear): High spectral contrast
  - **Idgham** (merging): Low MFCC variance
  - **Iqlab** (conversion): Specific MFCC pattern
  - **Ikhfa** (concealment): Moderate features
- Analyzes nasalization quality with ZCR + MFCC
- Research-validated Ghunnah duration (0.89-1.17 ratio)

**Output includes:**
- Rule type identification (Idhar/Idgham/Iqlab/Ikhfa)
- Nasalization quality assessment
- MFCC-verified pronunciation accuracy
- Specific recommendations per rule type

## Output Format
The analyzer returns JSON with enhanced MFCC data:
```json
{
  "audio_file": "path/to/file.wav",
  "duration": 45.5,
  "madd_analysis": {
    "total_elongations": 10,
    "correct_elongations": 8,
    "percentage": 80.0,
    "issues": [...],
    "details": [{
      "time": 2.5,
      "duration": 0.85,
      "status": "correct",
      "note": "Proper elongation detected (MFCC-verified vowel quality)",
      "mfcc_confidence": "high"
    }]
  },
  "noon_sakin_analysis": {
    "total_occurrences": 15,
    "correct_pronunciation": 12,
    "percentage": 80.0,
    "issues": [...],
    "details": [{
      "time": 5.3,
      "status": "correct",
      "note": "Proper Ikhfa pronunciation detected (MFCC-verified)",
      "rule_type": "Ikhfa",
      "mfcc_confidence": "high"
    }]
  },
  "overall_score": {
    "score": 80.0,
    "grade": "Very Good",
    "feedback": "Very good recitation..."
  }
}
```

## Troubleshooting

### "Python not found"
- Ensure Python is installed and added to PATH
- Restart your terminal/IDE after installation

### "No module named 'librosa'"
- Run: `pip install librosa`
- Make sure you're using the correct pip (pip3 on some systems)

### "Audio file format not supported"
- Supported formats: WAV, MP3, M4A, OGG
- Convert audio to WAV for best results

### "Analysis taking too long"
- Large audio files (>10MB) may take 30-60 seconds
- Consider audio compression or splitting for very long recordings

## Integration with Laravel
The analyzer is called automatically after audio submission:
1. Student submits audio recording
2. AssemblyAI transcribes the audio (Arabic text)
3. Python script analyzes Tajweed rules
4. Results stored in database (tajweed_analysis column)
5. Teacher reviews both transcription and Tajweed analysis

## Notes
- Analysis accuracy improves with clear, high-quality audio
- Best results with sample rate of 22050 Hz or higher
- Analysis is AI-assisted, not a replacement for qualified teacher review
- Use as a supplementary tool for initial feedback
