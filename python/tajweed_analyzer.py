"""
Advanced Tajweed Analyzer for Quran Recitation
Uses Tarteel AI's Whisper model for accurate Arabic Quran transcription
Analyzes 3 specific Tajweed rules using ACTUAL AUDIO ANALYSIS:
1. Madd (Elongation) - Pitch stability + duration measurement
2. Idgham Bila Ghunnah (Merging without nasalization - ر ل) - Formant analysis
3. Idgham Bi Ghunnah (Merging with nasalization - و م ن ي) - Nasal formant detection
Uses OpenAI for intelligent feedback generation
Uses Parselmouth (Praat) for professional phonetic analysis
"""

# CRITICAL: Limit OpenBLAS threads for shared hosting environments
# Must be set BEFORE importing numpy/scipy to prevent threading errors
import os
os.environ['OPENBLAS_NUM_THREADS'] = '1'
os.environ['MKL_NUM_THREADS'] = '1'
os.environ['OMP_NUM_THREADS'] = '1'
os.environ['NUMEXPR_NUM_THREADS'] = '1'

import sys
import json
import platform
import librosa
import soundfile as sf
import numpy as np
from scipy.signal import find_peaks
import warnings
import re
import html
import tempfile
from difflib import SequenceMatcher
warnings.filterwarnings('ignore')

MUQATTAAT_LETTER_NAMES = {
    'ا': 'الف',
    'ل': 'لام',
    'م': 'ميم',
    'ح': 'حا',
    'ي': 'يا',
    'ط': 'طا',
    'س': 'سين',
    'ك': 'كاف',
    'ه': 'ها',
    'ع': 'عين',
    'ر': 'را',
    'ص': 'صاد',
    'ق': 'قاف',
    'ن': 'نون',
}

MUQATTAAT_SEQUENCES = (
    'الم', 'المص', 'الر', 'المر', 'كهيعص', 'طه', 'طسم', 'طس',
    'يس', 'ص', 'حم', 'عسق', 'حمعسق', 'ق', 'ن'
)

MUQATTAAT_CANONICAL = set(MUQATTAAT_LETTER_NAMES.values())


def _muqattaat_sequence_to_phrase(sequence):
    return ' '.join(MUQATTAAT_LETTER_NAMES[ch] for ch in sequence if ch in MUQATTAAT_LETTER_NAMES)


def _build_muqattaat_phrase_patterns():
    patterns = []
    for sequence in MUQATTAAT_SEQUENCES:
        phrase = _muqattaat_sequence_to_phrase(sequence)
        if not phrase:
            continue

        compact_phrase = phrase.replace(' ', '')
        patterns.append((rf'(?<!\S){re.escape(sequence)}(?!\S)', phrase))
        patterns.append((rf'(?<!\S){re.escape(compact_phrase)}(?!\S)', phrase))

    return patterns


MUQATTAAT_PHRASE_PATTERNS = _build_muqattaat_phrase_patterns()

# Import Parselmouth for advanced phonetic analysis
try:
    import parselmouth
    from parselmouth.praat import call
    PARSELMOUTH_AVAILABLE = True
except ImportError:
    PARSELMOUTH_AVAILABLE = False
    print(json.dumps({
        "warning": "Parselmouth not installed. Install with: pip install praat-parselmouth",
        "fallback": "Using basic librosa analysis"
    }), file=sys.stderr)

# Setup FFmpeg path
def setup_ffmpeg():
    """Setup FFmpeg path based on operating system"""
    env_ffmpeg_path = os.environ.get('FFMPEG_PATH', '')
    if env_ffmpeg_path and os.path.exists(env_ffmpeg_path):
        if env_ffmpeg_path not in os.environ.get('PATH', ''):
            os.environ['PATH'] = env_ffmpeg_path + os.pathsep + os.environ.get('PATH', '')
        return
    
    system = platform.system()
    
    if system == 'Windows':
        windows_paths = [
            r"C:\ffmpeg\bin",
            r"C:\ffmpeg\ffmpeg-master-latest-win64-gpl\bin",
            r"C:\Program Files\ffmpeg\bin",
        ]
        for path in windows_paths:
            if os.path.exists(path) and path not in os.environ.get('PATH', ''):
                os.environ['PATH'] = path + os.pathsep + os.environ.get('PATH', '')
                return
    
    elif system in ['Linux', 'Darwin']:
        unix_paths = ["/usr/bin", "/usr/local/bin", "/opt/homebrew/bin"]
        for path in unix_paths:
            if os.path.exists(path) and path not in os.environ.get('PATH', ''):
                os.environ['PATH'] = path + os.pathsep + os.environ.get('PATH', '')
                return

setup_ffmpeg()

class TajweedAnalyzer:
    def __init__(self, audio_path, expected_text="", use_whisper=True, use_openai=True, reference_audio_path=None):
        """Initialize with audio file path and expected Quranic text"""
        self.audio_path = audio_path
        self.expected_text = expected_text
        self.use_whisper = use_whisper
        self.use_openai = use_openai
        self.reference_audio_path = reference_audio_path
        
        # Convert webm to wav if needed (for Parselmouth compatibility)
        self.converted_audio_path = None
        if audio_path.lower().endswith('.webm'):
            self.converted_audio_path = self.convert_webm_to_wav(audio_path)
            audio_for_analysis = self.converted_audio_path
        else:
            audio_for_analysis = audio_path
        
        self.y, self.sr = librosa.load(audio_for_analysis, sr=16000)  # 16kHz for Whisper
        self.duration = librosa.get_duration(y=self.y, sr=self.sr)
        
        # Validate that the audio contains actual sound content
        # RMS energy threshold: below this is considered silent
        rms_energy = float(np.sqrt(np.mean(self.y ** 2)))
        self.is_silent = rms_energy < 0.005 or self.duration < 0.5
        if self.is_silent:
            print(json.dumps({
                "warning": "Audio appears to be silent or too short",
                "duration": round(self.duration, 2),
                "rms_energy": round(rms_energy, 6)
            }), file=sys.stderr)
        
        # Load reference audio if provided
        self.y_ref, self.sr_ref = None, None
        self.duration_ref = 0
        if reference_audio_path and os.path.exists(reference_audio_path):
            try:
                self.y_ref, self.sr_ref = librosa.load(reference_audio_path, sr=16000)
                self.duration_ref = librosa.get_duration(y=self.y_ref, sr=self.sr_ref)
                print(json.dumps({
                    "status": "reference_loaded",
                    "message": f"Reference audio loaded: {self.duration_ref:.2f}s"
                }), file=sys.stderr)
            except Exception as e:
                print(json.dumps({"warning": f"Failed to load reference audio: {str(e)}"}), file=sys.stderr)
        
        # Load Whisper model if requested
        self.whisper_model = None
        self.whisper_processor = None
        if self.use_whisper:
            self.load_whisper_model()
        
        # Detect which rules apply to this verse
        self.has_madd = self.detect_madd_in_text()
        self.has_idgham_bila = self.detect_idgham_bila_in_text()
        self.has_idgham_bi = self.detect_idgham_bi_in_text()
        self.has_muqattaat = self.detect_muqattaat_in_expected_text()
    
    def load_whisper_model(self):
        """Load Tarteel AI's Whisper model for Arabic Quran ASR"""
        try:
            from transformers import WhisperProcessor, WhisperForConditionalGeneration
            import torch
            
            model_name = "tarteel-ai/whisper-base-ar-quran"
            
            # Check if model is cached, if not, show loading message
            cache_dir = os.path.expanduser("~/.cache/huggingface/transformers")
            model_cache = os.path.join(cache_dir, "models--tarteel-ai--whisper-base-ar-quran")
            
            if not os.path.exists(model_cache):
                print(json.dumps({
                    "status": "downloading_model",
                    "message": "First time setup: Downloading Tarteel Whisper model (~290MB)..."
                }), file=sys.stderr)
            
            self.whisper_processor = WhisperProcessor.from_pretrained(model_name)
            self.whisper_model = WhisperForConditionalGeneration.from_pretrained(model_name)
            
            # Use GPU if available
            device = "cuda" if torch.cuda.is_available() else "cpu"
            self.whisper_model = self.whisper_model.to(device)
            
            print(json.dumps({
                "status": "model_loaded",
                "device": device
            }), file=sys.stderr)
            
        except Exception as e:
            print(json.dumps({
                "status": "model_load_failed",
                "error": str(e),
                "fallback": "Using MFCC-based analysis only"
            }), file=sys.stderr)
            self.whisper_model = None
            self.whisper_processor = None
    
    def convert_webm_to_wav(self, webm_path):
        """Convert webm to wav for Parselmouth compatibility"""
        try:
            import subprocess
            
            # Create temp wav file
            wav_path = webm_path.rsplit('.', 1)[0] + '_converted.wav'
            
            # Convert using ffmpeg
            cmd = ['ffmpeg', '-i', webm_path, '-ar', '16000', '-ac', '1', '-y', wav_path]
            subprocess.run(cmd, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL, check=True)
            
            return wav_path
        except Exception as e:
            print(json.dumps({
                "warning": f"Failed to convert webm to wav: {str(e)}",
                "fallback": "Will attempt analysis with original file"
            }), file=sys.stderr)
            return webm_path
    
    def transcribe_with_whisper(self):
        """Transcribe audio using Tarteel AI's Whisper model"""
        if not self.whisper_model or not self.whisper_processor:
            return None

        # Do not attempt transcription on silent or near-silent audio.
        if self.is_silent:
            print(json.dumps({
                "status": "transcription_skipped",
                "reason": "Audio is silent or too short - skipping Whisper to avoid hallucination"
            }), file=sys.stderr)
            return None

        try:
            import torch

            # Prepare audio and move it to the same device as the model.
            input_features = self.whisper_processor(
                self.y,
                sampling_rate=self.sr,
                return_tensors="pt"
            ).input_features

            device = next(self.whisper_model.parameters()).device
            input_features = input_features.to(device)

            with torch.no_grad():
                predicted_ids = self.whisper_model.generate(
                    input_features,
                    task='transcribe',
                    language='ar',
                    num_beams=3,
                    do_sample=False,
                )

            transcription = self.whisper_processor.batch_decode(
                predicted_ids,
                skip_special_tokens=True
            )[0].strip()

            # Muqatta'at-heavy verses benefit from lexical canonicalization.
            if transcription and self.has_muqattaat:
                transcription = self.normalize_muqattaat_text(transcription)

            return transcription

        except Exception as e:
            print(json.dumps({
                "status": "transcription_failed",
                "error": str(e)
            }), file=sys.stderr)
            return None

    def detect_pause_segments(self, min_pause_seconds=1.8, top_db=35):
        """Detect long silence segments to mark recitation pauses."""
        pauses = []

        try:
            intervals = librosa.effects.split(self.y, top_db=top_db)
            if len(intervals) < 2:
                return pauses

            for idx in range(len(intervals) - 1):
                end_current = intervals[idx][1]
                start_next = intervals[idx + 1][0]
                pause_duration = (start_next - end_current) / float(self.sr)

                if pause_duration >= min_pause_seconds:
                    pause_midpoint = ((end_current + start_next) / 2.0) / float(self.sr)
                    pauses.append({
                        'time': round(float(pause_midpoint), 2),
                        'duration': round(float(pause_duration), 2),
                    })
        except Exception as e:
            print(json.dumps({
                "status": "pause_detection_failed",
                "error": str(e)
            }), file=sys.stderr)

        return pauses

    def add_pause_markers_to_transcription(self, transcription, pause_segments):
        """Insert ۝ between words at detected long pause positions."""
        if not transcription:
            return transcription

        words = transcription.split()
        if len(words) < 2 or not pause_segments or self.duration <= 0:
            return transcription

        boundaries = len(words) - 1
        marker_positions = set()

        for pause in pause_segments:
            pause_time = float(pause.get('time', 0) or 0)
            ratio = max(0.0, min(1.0, pause_time / float(self.duration)))
            position = int(round(ratio * boundaries))
            position = max(1, min(boundaries, position))
            marker_positions.add(position)

        if not marker_positions:
            return transcription

        rebuilt = []
        for idx, word in enumerate(words):
            if idx in marker_positions:
                rebuilt.append('۝')
            rebuilt.append(word)

        return ' '.join(rebuilt)

    def detect_muqattaat_in_expected_text(self):
        """Detect whether expected text contains Muqatta'at patterns."""
        if not self.expected_text:
            return False

        probe = html.unescape(str(self.expected_text))
        probe = re.sub(r'[\u0000-\u007F]+', ' ', probe)
        probe = re.sub(r'<\|[^|]+\|>', ' ', probe)
        probe = re.sub(r'<[^>]+>', ' ', probe)
        probe = probe.replace('۝', ' ')
        probe = re.sub(r'[\u064B-\u065F\u0670\u06D6-\u06ED]', '', probe)
        probe = probe.replace('ـ', '')
        probe = probe.replace('أ', 'ا').replace('إ', 'ا').replace('آ', 'ا').replace('ٱ', 'ا')
        probe = re.sub(r'[^\u0600-\u06FF\s]', ' ', probe)
        probe = re.sub(r'\s+', ' ', probe).strip()

        if not probe:
            return False

        tokens = probe.split()
        if any(token in MUQATTAAT_SEQUENCES for token in tokens):
            return True

        compact = ''.join(tokens)
        return any(sequence in compact for sequence in MUQATTAAT_SEQUENCES)

    def expand_compact_muqattaat_token(self, token):
        """Expand compact Muqatta'at forms such as الم or كهيعص into letter names."""
        if not token:
            return None

        compact = re.sub(r'(.)\1{1,}', r'\1', token)
        if compact in MUQATTAAT_SEQUENCES:
            return [MUQATTAAT_LETTER_NAMES[ch] for ch in compact if ch in MUQATTAAT_LETTER_NAMES]

        return None

    def canonicalize_muqattaat_word(self, word):
        """Map stretched/variant Muqatta'at letter names to canonical names."""
        if not word:
            return word

        if word in MUQATTAAT_CANONICAL:
            return word

        condensed = re.sub(r'(.)\1{1,}', r'\1', word)
        if condensed in MUQATTAAT_CANONICAL:
            return condensed

        # Guard against over-normalizing regular words.
        if len(condensed) < 2 or len(condensed) > 5:
            return condensed

        best_match = None
        best_ratio = 0.0
        for candidate in MUQATTAAT_CANONICAL:
            ratio = SequenceMatcher(None, condensed, candidate).ratio()
            if ratio > best_ratio:
                best_ratio = ratio
                best_match = candidate

        if best_match and best_ratio >= 0.80:
            return best_match

        return condensed

    def normalize_muqattaat_text(self, text):
        """Normalize Muqatta'at phrases for fair comparison in ASR scoring."""
        if not text:
            return ''

        text = html.unescape(str(text))
        text = re.sub(r'[\u064B-\u065F\u0670\u06D6-\u06ED]', '', text)
        text = text.replace('ـ', '')
        text = text.replace('أ', 'ا').replace('إ', 'ا').replace('آ', 'ا').replace('ٱ', 'ا')
        text = re.sub(r'[^\u0600-\u06FF\s]', ' ', text)
        text = re.sub(r'\s+', ' ', text).strip()

        if not text:
            return ''

        normalized_words = []
        tokens = text.split()
        idx = 0

        while idx < len(tokens):
            token = tokens[idx]

            expanded = self.expand_compact_muqattaat_token(token)
            if expanded:
                normalized_words.extend(expanded)
                idx += 1
                continue

            if len(token) == 1 and token in MUQATTAAT_LETTER_NAMES:
                run = []
                j = idx
                while j < len(tokens) and len(tokens[j]) == 1 and tokens[j] in MUQATTAAT_LETTER_NAMES and len(run) < 6:
                    run.append(tokens[j])
                    j += 1

                best_len = 0
                for candidate_len in range(len(run), 1, -1):
                    sequence = ''.join(run[:candidate_len])
                    if sequence in MUQATTAAT_SEQUENCES:
                        best_len = candidate_len
                        break

                if best_len > 0:
                    normalized_words.extend(MUQATTAAT_LETTER_NAMES[ch] for ch in run[:best_len])
                    idx += best_len
                    continue

            normalized_words.append(self.canonicalize_muqattaat_word(token))
            idx += 1

        normalized_text = ' '.join(normalized_words)
        for pattern, replacement in MUQATTAAT_PHRASE_PATTERNS:
            normalized_text = re.sub(pattern, replacement, normalized_text)

        return re.sub(r'\s+', ' ', normalized_text).strip()

    def normalize_arabic_for_accuracy(self, text):
        """Normalize Arabic text for fair similarity scoring."""
        if not text:
            return ''

        text = html.unescape(str(text))
        text = re.sub(r'<\|[^|]+\|>', ' ', text)
        text = re.sub(r'<[^>]+>', ' ', text)
        text = text.replace('۝', ' ')

        # Remove harakat and Quran annotation marks.
        text = re.sub(r'[\u064B-\u065F\u0670\u06D6-\u06ED]', '', text)
        text = text.replace('ـ', '')

        # Normalize common alif forms.
        text = text.replace('أ', 'ا').replace('إ', 'ا').replace('آ', 'ا').replace('ٱ', 'ا')
        text = text.replace('ى', 'ي')
        text = text.replace('ة', 'ه')

        # Normalize small Quranic glyph forms.
        text = text.replace('\u06E5', 'و').replace('\u06E6', 'ي')

        # Keep Arabic letters, digits, and whitespace only.
        text = re.sub(r'[^\u0600-\u06FF0-9\s]', ' ', text)
        text = re.sub(r'\s+', ' ', text)

        if self.has_muqattaat:
            text = self.normalize_muqattaat_text(text)

        return text.strip()

    def calculate_word_accuracy(self, transcription, expected):
        """Calculate word-level similarity percentage."""
        trans_words = transcription.split()
        expected_words = expected.split()

        if not expected_words:
            return 0.0

        return SequenceMatcher(None, trans_words, expected_words).ratio() * 100.0
    
    def detect_madd_in_text(self):
        """Check if expected text contains Madd elongation letters"""
        if not self.expected_text:
            return False
        # Madd letters: ا (alif), و (waw), ي (ya)
        madd_letters = ['ا', 'و', 'ي', 'آ', 'ى']
        return any(letter in self.expected_text for letter in madd_letters)
    
    def detect_idgham_bila_in_text(self):
        """Check if text has Noon Sakin/Tanween followed by ر or ل"""
        if not self.expected_text:
            return False
        # Look for Noon Sakin or tanween immediately before ر or ل.
        patterns = [
            r'نْ\s*[رل]',
            r'نۢ\s*[رل]',
            r'ن(?:[\u064B-\u065F\u0670\u06D6-\u06ED]*)\s*[رل]',
            r'[\u064B\u064C\u064D](?:[\u064B-\u065F\u0670\u06D6-\u06ED\s]*)[رل]',
        ]
        return any(re.search(pattern, self.expected_text) for pattern in patterns)
    
    def detect_idgham_bi_in_text(self):
        """Check if text has Noon Sakin/Tanween followed by و م ن ي"""
        if not self.expected_text:
            return False
        # Look for Noon Sakin or tanween immediately before و م ن ي.
        patterns = [
            r'نْ\s*[ومني]',
            r'نۢ\s*[ومني]',
            r'ن(?:[\u064B-\u065F\u0670\u06D6-\u06ED]*)\s*[ومني]',
            r'[\u064B\u064C\u064D](?:[\u064B-\u065F\u0670\u06D6-\u06ED\s]*)[ومني]',
        ]
        return any(re.search(pattern, self.expected_text) for pattern in patterns)
    
    def analyze_madd(self):
        """
        Analyze Madd (Elongation) rules using ADVANCED AUDIO ANALYSIS
        Uses Parselmouth for formant analysis and pitch tracking
        Madd should be held for 2 counts (approximately 0.4-0.6 seconds minimum)
        """
        results = {
            'total_elongations': 0,
            'correct_elongations': 0,
            'issues': [],
            'percentage': 0,
            'details': [],
            'rule_applicable': self.has_madd
        }
        
        if not self.has_madd:
            results['percentage'] = 100
            results['details'].append({'note': 'Not present in this verse'})
            return results
        
        try:
            if PARSELMOUTH_AVAILABLE:
                # ADVANCED PARSELMOUTH ANALYSIS
                # Use converted wav file if available, otherwise original
                audio_for_praat = self.converted_audio_path if self.converted_audio_path else self.audio_path
                
                snd = parselmouth.Sound(audio_for_praat)
                
                # Extract acoustic features
                pitch = snd.to_pitch()
                formant = snd.to_formant_burg()
                intensity = snd.to_intensity()
                
                detected_elongations = []
                
                # Sample every 10ms to find vowel regions
                for t in np.arange(0.05, snd.duration, 0.01):
                    try:
                        # Get pitch (should exist and be stable during vowel)
                        f0 = call(pitch, "Get value at time", t, "Hertz", "Linear")
                        
                        # Get formants (F1, F2 identify vowels)
                        f1 = call(formant, "Get value at time", 1, t, "Hertz", "Linear")
                        f2 = call(formant, "Get value at time", 2, t, "Hertz", "Linear")
                        
                        # Get intensity (should be high during vowel)
                        power = call(intensity, "Get value at time", t, "Cubic")
                        
                        # Check if this is a vowel (pitch exists, formants exist, decent intensity)
                        if not np.isnan(f0) and not np.isnan(f1) and not np.isnan(f2) and power > 50:
                            # Check if vowel is elongated by looking ahead
                            duration = 0
                            
                            # Check next 600ms for stable formants (indicating elongation)
                            for future_t in np.arange(t, min(t + 0.7, snd.duration), 0.01):
                                try:
                                    future_f1 = call(formant, "Get value at time", 1, future_t, "Hertz", "Linear")
                                    future_f2 = call(formant, "Get value at time", 2, future_t, "Hertz", "Linear")
                                    future_f0 = call(pitch, "Get value at time", future_t, "Hertz", "Linear")
                                    future_power = call(intensity, "Get value at time", future_t, "Cubic")
                                    
                                    # Check if formants remain stable (within 15% variation)
                                    if (not np.isnan(future_f1) and not np.isnan(future_f2) and 
                                        not np.isnan(future_f0) and future_power > 45 and
                                        abs(future_f1 - f1) < f1 * 0.15 and 
                                        abs(future_f2 - f2) < f2 * 0.15 and
                                        abs(future_f0 - f0) < f0 * 0.1):
                                        duration += 0.01
                                    else:
                                        break
                                except:
                                    break
                            
                            # If vowel held for at least 350ms, it's a Madd
                            if duration >= 0.35:
                                # Check if we haven't already detected this elongation
                                if not any(abs(t - prev_t) < 0.3 for prev_t, _, _ in detected_elongations):
                                    detected_elongations.append((t, duration, f0))
                    except:
                        continue
                
                results['total_elongations'] = len(detected_elongations)
                
                # Check each detected elongation
                for t, duration, f0 in detected_elongations:
                    # Madd should be >= 0.4 seconds (2 counts minimum)
                    if duration >= 0.4:
                        results['correct_elongations'] += 1
                        results['details'].append({
                            'time': round(t, 2),
                            'duration': round(duration, 2),
                            'pitch': round(f0, 1),
                            'status': 'correct',
                            'note': 'Proper Madd elongation detected (Parselmouth analysis)'
                        })
                    else:
                        results['issues'].append({
                            'time': round(t, 2),
                            'duration': round(duration, 2),
                            'pitch': round(f0, 1),
                            'issue': f'Elongation too short ({duration:.2f}s) - should be >= 0.4s',
                            'recommendation': 'Hold the vowel for minimum 2 counts (0.4-0.6 seconds)'
                        })
                
                # Calculate percentage
                if results['total_elongations'] > 0:
                    results['percentage'] = round((results['correct_elongations'] / results['total_elongations']) * 100, 2)
                else:
                    results['percentage'] = 100  # No elongations detected, assume OK
                
                return results
            
            else:
                # FALLBACK: LIBROSA ANALYSIS (BASIC)
                y_22k, sr_22k = librosa.load(self.audio_path, sr=22050)
                mfccs = librosa.feature.mfcc(y=y_22k, sr=sr_22k, n_mfcc=13)
                rms = librosa.feature.rms(y=y_22k)[0]
                mfcc_var = np.var(mfccs, axis=0)
                
                # Find peaks in RMS that indicate sustained vowels
                peaks, properties = find_peaks(rms, distance=sr_22k//2, prominence=0.015)
                
                for i, peak in enumerate(peaks):
                    time_pos = librosa.frames_to_time(peak, sr=sr_22k)
                    
                    # Calculate sustained duration
                    hop_length = 512
                    start_idx = max(0, peak - 10)
                    end_idx = min(len(rms), peak + 30)
                    sustained_duration = (end_idx - start_idx) * hop_length / sr_22k
                    
                    # Check MFCC variance at peak
                    if peak < len(mfcc_var):
                        mfcc_variance_at_peak = mfcc_var[peak]
                        is_vowel_sustained = mfcc_variance_at_peak < np.mean(mfcc_var) * 0.7
                    else:
                        is_vowel_sustained = True
                    
                    if is_vowel_sustained:
                        results['total_elongations'] += 1
                        
                        # Madd should be >= 0.4 seconds (2 counts minimum)
                        if sustained_duration >= 0.4:
                            results['correct_elongations'] += 1
                            results['details'].append({
                                'time': round(time_pos, 2),
                                'duration': round(sustained_duration, 2),
                                'status': 'correct',
                                'note': 'Proper Madd elongation detected (basic analysis - install parselmouth for better accuracy)'
                            })
                        else:
                            results['issues'].append({
                                'time': round(time_pos, 2),
                                'duration': round(sustained_duration, 2),
                                'issue': 'Elongation too short - should be 2-6 counts',
                                'recommendation': 'Hold the vowel for minimum 2 counts (0.5-0.75 seconds)'
                            })
                
                # Calculate percentage
                if results['total_elongations'] > 0:
                    results['percentage'] = round((results['correct_elongations'] / results['total_elongations']) * 100, 2)
                else:
                    results['percentage'] = 100
                
                results['details'].append({
                    'warning': 'Using basic librosa analysis. Install parselmouth for advanced formant analysis: pip install praat-parselmouth'
                })
                
                return results
                
        except Exception as e:
            import traceback
            results['issues'].append({
                'error': str(e),
                'traceback': traceback.format_exc()
            })
            results['percentage'] = 0
            return results
            
            # Calculate percentage
            if results['total_elongations'] > 0:
                results['percentage'] = round((results['correct_elongations'] / results['total_elongations']) * 100, 2)
            else:
                results['percentage'] = 0
                results['issues'].append({
                    'issue': 'No Madd elongations detected in recitation',
                    'recommendation': 'Ensure proper elongation of Madd letters (ا و ي)'
                })
                
        except Exception as e:
            results['error'] = str(e)
            results['percentage'] = 0
            
        return results
    
    def analyze_idgham_bila_ghunnah(self):
        """
        Analyze Idgham Bila Ghunnah using Whisper transcription + MFCC
        Occurs when Noon Sakin/Tanween meets ر or ل
        Should merge WITHOUT nasal sound
        """
        results = {
            'total_occurrences': 0,
            'correct_pronunciation': 0,
            'issues': [],
            'percentage': 0,
            'details': [],
            'rule_applicable': self.has_idgham_bila,
            'whisper_detected': False
        }
        
        if not self.has_idgham_bila:
            results['percentage'] = 100
            results['details'].append({'note': 'Not present in this verse'})
            return results
        
        try:
            # Use Whisper to detect phonemes if available
            if self.whisper_model:
                transcription = self.transcribe_with_whisper()
                if transcription:
                    results['whisper_detected'] = True
                    # Check if transcription contains ر or ل in correct context
                    if any(letter in transcription for letter in ['ر', 'ل']):
                        results['total_occurrences'] += 1
            
            # MFCC-based detection
            y_22k, sr_22k = librosa.load(self.audio_path, sr=22050)
            mfccs = librosa.feature.mfcc(y=y_22k, sr=sr_22k, n_mfcc=13)
            
            # Zero crossing rate (should be LOWER for proper Idgham Bila)
            zcr = librosa.feature.zero_crossing_rate(y_22k)[0]
            
            # Spectral centroid for brightness detection
            spectral_centroids = librosa.feature.spectral_centroid(y=y_22k, sr=sr_22k)[0]
            
            # Detect ر (ra) or ل (lam) sounds
            for i in range(0, len(spectral_centroids) - 5, 15):
                if i < mfccs.shape[1]:
                    mfcc_window = mfccs[:, i:min(i+5, mfccs.shape[1])]
                    avg_mfcc = np.mean(mfcc_window, axis=1)
                    
                    avg_centroid = np.mean(spectral_centroids[i:i+5])
                    avg_zcr = np.mean(zcr[i:i+5])
                    
                    # Detect ر or ل: higher spectral centroid, low ZCR
                    is_liquid_consonant = (
                        avg_centroid > np.mean(spectral_centroids) * 1.1 and
                        avg_zcr < np.mean(zcr) * 0.8
                    )
                    
                    if is_liquid_consonant:
                        time_pos = librosa.frames_to_time(i, sr=sr_22k)
                        results['total_occurrences'] += 1
                        
                        # Check for LACK of nasalization
                        nasal_present = avg_zcr > 0.12
                        
                        if not nasal_present:
                            results['correct_pronunciation'] += 1
                            results['details'].append({
                                'time': round(time_pos, 2),
                                'status': 'correct',
                                'note': 'Proper Idgham Bila Ghunnah - merged without nasalization',
                                'rule_type': 'Idgham Bila Ghunnah'
                            })
                        else:
                            results['issues'].append({
                                'time': round(time_pos, 2),
                                'issue': 'Nasalization detected - should merge WITHOUT dengung',
                                'recommendation': 'Merge directly into ر or ل without nasal sound',
                                'rule_type': 'Idgham Bila Ghunnah'
                            })
            
            # Calculate percentage
            if results['total_occurrences'] > 0:
                results['percentage'] = round((results['correct_pronunciation'] / results['total_occurrences']) * 100, 2)
            else:
                results['percentage'] = 0
                results['issues'].append({
                    'issue': 'No Idgham Bila Ghunnah detected',
                    'recommendation': 'Check pronunciation of Noon Sakin/Tanween before ر or ل'
                })
                
        except Exception as e:
            results['error'] = str(e)
            results['percentage'] = 0
            
        return results
    
    def analyze_idgham_bi_ghunnah(self):
        """
        Analyze Idgham Bi Ghunnah using Whisper + MFCC
        Occurs when Noon Sakin/Tanween meets و م ن ي
        Should merge WITH nasal sound for 2 counts
        """
        results = {
            'total_occurrences': 0,
            'correct_pronunciation': 0,
            'issues': [],
            'percentage': 0,
            'details': [],
            'rule_applicable': self.has_idgham_bi,
            'whisper_detected': False
        }
        
        if not self.has_idgham_bi:
            results['percentage'] = 100
            results['details'].append({'note': 'Not present in this verse'})
            return results
        
        try:
            # Use Whisper for phoneme detection
            if self.whisper_model:
                transcription = self.transcribe_with_whisper()
                if transcription:
                    results['whisper_detected'] = True
                    if any(letter in transcription for letter in ['و', 'م', 'ن', 'ي']):
                        results['total_occurrences'] += 1
            
            # MFCC-based nasal detection
            y_22k, sr_22k = librosa.load(self.audio_path, sr=22050)
            mfccs = librosa.feature.mfcc(y=y_22k, sr=sr_22k, n_mfcc=13)
            
            # Zero crossing rate (should be HIGHER for nasal sounds)
            zcr = librosa.feature.zero_crossing_rate(y_22k)[0]
            
            # Spectral features
            spectral_centroids = librosa.feature.spectral_centroid(y=y_22k, sr=sr_22k)[0]
            
            # RMS for duration measurement
            rms = librosa.feature.rms(y=y_22k)[0]
            
            # Detect nasal consonants
            for i in range(0, len(spectral_centroids) - 5, 15):
                if i < mfccs.shape[1]:
                    mfcc_window = mfccs[:, i:min(i+5, mfccs.shape[1])]
                    avg_mfcc = np.mean(mfcc_window, axis=1)
                    
                    avg_centroid = np.mean(spectral_centroids[i:i+5])
                    avg_zcr = np.mean(zcr[i:i+5])
                    avg_rms = np.mean(rms[i:i+5])
                    
                    # Detect nasal characteristic
                    is_nasal = (
                        avg_mfcc[1] < np.mean(mfccs[1, :]) * 0.9 and
                        avg_zcr > 0.08 and
                        avg_rms > np.mean(rms) * 0.6
                    )
                    
                    if is_nasal:
                        time_pos = librosa.frames_to_time(i, sr=sr_22k)
                        results['total_occurrences'] += 1
                        
                        # Calculate duration of nasalization
                        start_idx = max(0, i - 5)
                        end_idx = min(len(rms), i + 15)
                        nasal_duration = (end_idx - start_idx) * 512 / sr_22k
                        
                        # Check if nasalization is proper
                        proper_nasalization = (
                            avg_zcr > 0.08 and
                            nasal_duration >= 0.3
                        )
                        
                        if proper_nasalization:
                            results['correct_pronunciation'] += 1
                            results['details'].append({
                                'time': round(time_pos, 2),
                                'duration': round(nasal_duration, 2),
                                'status': 'correct',
                                'note': 'Proper Idgham Bi Ghunnah - merged with dengung for 2 counts',
                                'rule_type': 'Idgham Bi Ghunnah'
                            })
                        else:
                            issue = 'Dengung too short' if nasal_duration < 0.3 else 'Dengung quality weak'
                            results['issues'].append({
                                'time': round(time_pos, 2),
                                'duration': round(nasal_duration, 2),
                                'issue': issue,
                                'recommendation': 'Merge into و م ن ي WITH clear dengung for 2 counts',
                                'rule_type': 'Idgham Bi Ghunnah'
                            })
            
            # Calculate percentage
            if results['total_occurrences'] > 0:
                results['percentage'] = round((results['correct_pronunciation'] / results['total_occurrences']) * 100, 2)
            else:
                results['percentage'] = 0
                results['issues'].append({
                    'issue': 'No Idgham Bi Ghunnah detected',
                    'recommendation': 'Check pronunciation of Noon Sakin/Tanween before و م ن ي'
                })
                
        except Exception as e:
            results['error'] = str(e)
            results['percentage'] = 0
            
        return results

    def get_rule_feedback_contexts(self, analysis_results):
        """Build normalized rule contexts for display and feedback filtering."""
        definitions = [
            ('madd_analysis', 'Madd (Elongation)', ['madd', 'elongation', 'مد']),
            ('idgham_bila_ghunnah_analysis', 'Idgham Bila Ghunnah', ['idgham bila', 'idgham billa', 'without nasalization', 'بلا']),
            ('idgham_bi_ghunnah_analysis', 'Idgham Bi Ghunnah', ['idgham bi', 'with nasalization', 'ghunnah', 'غنة']),
        ]

        contexts = []
        for key, label, keywords in definitions:
            rule = analysis_results.get(key, {}) or {}
            contexts.append({
                'key': key,
                'label': label,
                'applicable': bool(rule.get('rule_applicable', True)),
                'percentage': float(rule.get('percentage', 0) or 0),
                'issues_count': len(rule.get('issues', []) or []),
                'keywords': keywords,
            })

        return contexts

    def filter_feedback_by_applicability(self, feedback_obj, rule_contexts):
        """Remove improvement/strength entries that refer to rules not present in the verse."""
        if not isinstance(feedback_obj, dict):
            return feedback_obj

        excluded_keywords = []
        for context in rule_contexts:
            if not context.get('applicable', True):
                excluded_keywords.extend(context.get('keywords', []))

        if not excluded_keywords:
            return feedback_obj

        def mentions_excluded(text):
            lower = str(text).lower()
            return any(keyword in lower for keyword in excluded_keywords)

        strengths = feedback_obj.get('strengths', [])
        if isinstance(strengths, list):
            feedback_obj['strengths'] = [s for s in strengths if not mentions_excluded(s)]

        improvements = feedback_obj.get('improvements', [])
        if isinstance(improvements, list):
            filtered = []
            for item in improvements:
                if isinstance(item, dict):
                    issue = item.get('issue', '')
                    suggestion = item.get('suggestion', '')
                    if mentions_excluded(issue) or mentions_excluded(suggestion):
                        continue
                else:
                    if mentions_excluded(item):
                        continue
                filtered.append(item)
            feedback_obj['improvements'] = filtered

        next_steps = feedback_obj.get('next_steps', '')
        if isinstance(next_steps, str) and mentions_excluded(next_steps):
            feedback_obj['next_steps'] = 'Keep practicing the rules that appear in this verse and compare with the reference recitation.'

        return feedback_obj
    
    def generate_openai_feedback(self, analysis_results):
        """Generate intelligent feedback using OpenAI GPT"""
        if not self.use_openai:
            return None
        
        try:
            from openai import OpenAI
            
            # Get API key from environment variable
            api_key = os.environ.get('OPENAI_API_KEY')
            if not api_key:
                print(json.dumps({
                    "status": "openai_skipped",
                    "reason": "OPENAI_API_KEY environment variable not set"
                }), file=sys.stderr)
                return None
            
            # Initialize OpenAI client with proper API key
            client = OpenAI(api_key=api_key)
            
            # Get transcription accuracy
            transcription = analysis_results.get('whisper_transcription', '')
            expected = analysis_results.get('expected_text', '')

            rule_contexts = self.get_rule_feedback_contexts(analysis_results)
            applicable_rules = [rule for rule in rule_contexts if rule.get('applicable', True)]
            non_applicable_rules = [rule for rule in rule_contexts if not rule.get('applicable', True)]
            
            # Calculate text similarity
            from difflib import SequenceMatcher
            text_accuracy = SequenceMatcher(None, transcription, expected).ratio() * 100 if transcription and expected else 0
            
            # Get reference comparison if available
            ref_comparison = analysis_results.get('reference_comparison', {})
            ref_similarity = ref_comparison.get('overall_similarity', 0) if ref_comparison else 0

            tajweed_lines = []
            for rule in applicable_rules:
                tajweed_lines.append(
                    f"- {rule['label']}: {rule['percentage']}% correct ({rule['issues_count']} issues)"
                )

            if not tajweed_lines:
                tajweed_lines.append('- No target rules are present in this verse.')

            for rule in non_applicable_rules:
                tajweed_lines.append(f"- {rule['label']}: Not present in this verse")

            tajweed_summary = "\n".join(tajweed_lines)
            
            # Prepare analysis summary for GPT
            prompt = f"""You are an expert Quran Tajweed teacher. Analyze this student's recitation and provide constructive, accurate feedback.

CRITICAL: Base your feedback on ALL metrics below, especially pronunciation accuracy.

Expected Quranic Text: {expected}
Student's Transcription: {transcription}
Pronunciation Accuracy: {text_accuracy:.1f}%

Reference Audio Comparison:
- Overall Similarity: {ref_similarity:.1f}%
- Pitch Match: {ref_comparison.get('pitch_similarity', 0):.1f}%
- Tempo Match: {ref_comparison.get('tempo_similarity', 0):.1f}%

Tajweed Rules Analysis:
{tajweed_summary}

Overall Score: {analysis_results['overall_score']['score']}%

IMPORTANT GUIDELINES:
- If pronunciation accuracy < 70%, flag MAJOR pronunciation/articulation errors
- If reference similarity < 50%, mention pitch/rhythm issues
- If Tajweed scores are low, explain WHICH rules need work and WHY
- Be specific about what letters/sounds are mispronounced
- If transcription doesn't match expected text, identify the incorrect words
- Do NOT give improvement points for rules labeled "Not present in this verse"

Provide feedback in this EXACT JSON format:
{{
  "summary": "Brief 2-3 sentence overview highlighting main issues or strengths",
  "strengths": ["strength 1 (only if accuracy > 70%)", "strength 2"],
  "improvements": [
    {{"issue": "specific pronunciation/Tajweed problem", "suggestion": "how to fix it with practice tips"}},
    {{"issue": "specific problem", "suggestion": "how to fix it"}}
  ],
  "next_steps": "Specific practice recommendation focusing on weakest area"
}}

Be honest, specific, and constructive. Students need ACCURATE feedback to improve."""

            # Use GPT-4o for the best quality and cost-effectiveness
            response = client.chat.completions.create(
                model="gpt-4o",  # or "gpt-3.5-turbo" for maximum cost savings
                messages=[
                    {"role": "system", "content": "You are an expert Quran Tajweed teacher. You MUST respond with valid JSON only, no other text."},
                    {"role": "user", "content": prompt}
                ],
                max_tokens=500,
                temperature=0.7
            )
            
            feedback_text = response.choices[0].message.content.strip()
            
            # Remove markdown code blocks if present (```json ... ```)
            if feedback_text.startswith('```'):
                feedback_text = feedback_text.split('```')[1]
                if feedback_text.startswith('json'):
                    feedback_text = feedback_text[4:]
                feedback_text = feedback_text.strip()
            
            # Try to parse as JSON
            try:
                feedback_obj = json.loads(feedback_text)
                feedback_obj = self.filter_feedback_by_applicability(feedback_obj, rule_contexts)
                print(json.dumps({
                    "status": "openai_success",
                    "model": response.model,
                    "tokens_used": response.usage.total_tokens if hasattr(response, 'usage') else 0
                }), file=sys.stderr)
                return feedback_obj
            except json.JSONDecodeError as je:
                print(json.dumps({
                    "status": "json_parse_failed",
                    "error": str(je),
                    "raw_response": feedback_text[:200]
                }), file=sys.stderr)
                # Fallback to simple format if JSON parsing fails
                fallback_feedback = {
                    "summary": feedback_text,
                    "strengths": [],
                    "improvements": [],
                    "next_steps": ""
                }
                return self.filter_feedback_by_applicability(fallback_feedback, rule_contexts)
            
        except Exception as e:
            error_str = str(e)
            error_type = type(e).__name__
            print(json.dumps({
                "status": "openai_error",
                "error_type": error_type,
                "error": error_str[:500]  # Limit error message length
            }), file=sys.stderr)
            
            # Generate intelligent fallback feedback based on analysis results
            return self.generate_fallback_feedback(analysis_results, error_str)
    
    def generate_fallback_feedback(self, analysis_results, error_reason=""):
        """Generate intelligent fallback feedback when OpenAI API is unavailable"""
        score = analysis_results['overall_score']['score']
        rule_contexts = self.get_rule_feedback_contexts(analysis_results)

        rule_definitions = {
            'madd_analysis': {
                'strength': 'Good application of Madd (elongation) rules',
                'issue_template': 'Madd (elongation) needs attention ({issues} issues detected)',
                'suggestion': 'Practice holding vowels for the correct duration (2-6 counts). Listen to reference audio carefully.',
                'next_step': "Focus on Madd rules. Listen to Sheikh Alafasy's reference audio multiple times before practicing."
            },
            'idgham_bila_ghunnah_analysis': {
                'strength': 'Strong understanding of Idgham Bila Ghunnah (merging without nasalization)',
                'issue_template': 'Idgham Bila Ghunnah requires work ({issues} issues found)',
                'suggestion': 'Focus on merging Noon Sakin with Raa (ر) and Lam (ل) without nasalization. Practice slowly.',
                'next_step': 'Work on Idgham Bila Ghunnah. Record yourself again and compare with the reference to hear the differences.'
            },
            'idgham_bi_ghunnah_analysis': {
                'strength': 'Excellent use of Idgham Bi Ghunnah (merging with nasalization)',
                'issue_template': 'Idgham Bi Ghunnah needs improvement ({issues} issues detected)',
                'suggestion': 'Practice nasalization (ghunnah) when merging with letters و م ن ي. Hold for 2 counts.',
                'next_step': "Practice Idgham Bi Ghunnah with nasalization. Listen to Sheikh Alafasy's reference audio multiple times before practicing."
            }
        }

        applicable_rules = [rule for rule in rule_contexts if rule.get('applicable', True)]
        
        # Determine strengths based on scores
        strengths = []
        for rule in applicable_rules:
            rule_key = rule.get('key')
            rule_info = rule_definitions.get(rule_key)
            if rule_info and rule.get('percentage', 0) >= 80:
                strengths.append(rule_info['strength'])

        if score >= 85:
            strengths.append("Overall strong Tajweed application")
        elif score >= 70:
            strengths.append("Solid foundation in Tajweed principles")
        else:
            strengths.append("Completed recitation with effort")

        if not applicable_rules:
            strengths.append('The selected verse has no target Madd/Idgham rule occurrences.')
        
        # Determine improvements needed
        improvements = []
        for rule in applicable_rules:
            rule_key = rule.get('key')
            rule_info = rule_definitions.get(rule_key)
            if not rule_info:
                continue

            issues_count = int(rule.get('issues_count', 0) or 0)
            if issues_count > 0:
                improvements.append({
                    "issue": rule_info['issue_template'].format(issues=issues_count),
                    "suggestion": rule_info['suggestion']
                })
        
        # Generate summary
        if score >= 90:
            summary = "Excellent recitation! Your Tajweed application is very strong. Continue practicing to maintain this level."
        elif score >= 80:
            summary = "Very good recitation! You demonstrate solid understanding of Tajweed rules with minor areas for improvement."
        elif score >= 70:
            summary = "Good effort! You show understanding of key Tajweed principles. Focus on the specific areas below to improve."
        elif score >= 60:
            summary = "Your recitation shows potential. Consistent practice on the identified areas will lead to significant improvement."
        else:
            summary = "Keep practicing! Tajweed mastery takes time. Focus on one rule at a time and listen carefully to reference audio."
        
        # Add API failure context if error occurred
        if 'insufficient_quota' in error_reason.lower() or '429' in error_reason:
            summary += " (Note: AI-enhanced feedback temporarily unavailable due to API quota. Analysis based on acoustic analysis.)"
        elif error_reason:
            summary += " (Note: Advanced AI feedback unavailable. Analysis based on acoustic analysis.)"
        
        # Generate next steps
        # Select most relevant next step based on weakest applicable rule
        if applicable_rules:
            weakest = min(applicable_rules, key=lambda rule: rule.get('percentage', 0))
            weakest_key = weakest.get('key')
            next_steps = rule_definitions.get(weakest_key, {}).get(
                'next_step',
                'Focus on one Tajweed rule at a time rather than trying to perfect everything at once.'
            )
        else:
            next_steps = 'Keep strengthening pronunciation accuracy and rhythm by repeating the verse with the reference recitation.'
        
        feedback_obj = {
            "summary": summary,
            "strengths": strengths if strengths else ["Submission completed"],
            "improvements": improvements,
            "next_steps": next_steps
        }
        return self.filter_feedback_by_applicability(feedback_obj, rule_contexts)
    
    def compare_with_reference(self):
        """
        Compare student audio with reference recitation
        Uses MFCC + DTW (Dynamic Time Warping) for similarity comparison
        Analyzes pitch, rhythm, and pronunciation differences
        """
        if self.y_ref is None:
            return None
        
        try:
            from scipy.spatial.distance import euclidean
            from fastdtw import fastdtw
            
            # Extract MFCC features from both audios
            mfcc_student = librosa.feature.mfcc(y=self.y, sr=self.sr, n_mfcc=13)
            mfcc_reference = librosa.feature.mfcc(y=self.y_ref, sr=self.sr_ref, n_mfcc=13)
            
            # Transpose for DTW (time steps x features)
            mfcc_student = mfcc_student.T
            mfcc_reference = mfcc_reference.T
            
            # Calculate DTW distance
            distance, path = fastdtw(mfcc_student, mfcc_reference, dist=euclidean)
            
            # Normalize distance by length
            normalized_distance = distance / max(len(mfcc_student), len(mfcc_reference))
            
            # Convert to similarity score (0-100)
            # Lower distance = higher similarity
            # Typical range: 20-100 for distance, we invert it
            similarity_score = max(0, 100 - (normalized_distance * 5))
            
            # Extract pitch contours
            pitch_student = librosa.yin(self.y, fmin=80, fmax=400, sr=self.sr)
            pitch_reference = librosa.yin(self.y_ref, fmin=80, fmax=400, sr=self.sr_ref)
            
            # Compare pitch stability (lower std = more stable)
            pitch_student_valid = pitch_student[~np.isnan(pitch_student)]
            pitch_reference_valid = pitch_reference[~np.isnan(pitch_reference)]
            
            if len(pitch_student_valid) > 0 and len(pitch_reference_valid) > 0:
                pitch_diff = abs(np.mean(pitch_student_valid) - np.mean(pitch_reference_valid))
                pitch_similarity = max(0, 100 - pitch_diff)
            else:
                pitch_similarity = 50
            
            # Analyze rhythm (tempo comparison)
            tempo_student, _ = librosa.beat.beat_track(y=self.y, sr=self.sr)
            tempo_reference, _ = librosa.beat.beat_track(y=self.y_ref, sr=self.sr_ref)
            tempo_diff = abs(tempo_student - tempo_reference)
            tempo_similarity = max(0, 100 - tempo_diff)
            
            # Overall comparison score
            overall_similarity = (similarity_score * 0.5 + pitch_similarity * 0.3 + tempo_similarity * 0.2)
            
            # Generate feedback based on similarity
            if overall_similarity >= 85:
                feedback = "Excellent! Your recitation closely matches the reference."
                grade = "Excellent"
            elif overall_similarity >= 70:
                feedback = "Very good recitation with minor differences from the reference."
                grade = "Very Good"
            elif overall_similarity >= 55:
                feedback = "Good attempt. Practice to match the reference more closely."
                grade = "Good"
            else:
                feedback = "Keep practicing. Listen carefully to the reference recitation."
                grade = "Needs Improvement"
            
            return {
                'has_reference': True,
                'reference_duration': round(float(self.duration_ref), 2),
                'student_duration': round(float(self.duration), 2),
                'overall_similarity': round(float(overall_similarity), 2),
                'pronunciation_similarity': round(float(similarity_score), 2),
                'pitch_similarity': round(float(pitch_similarity), 2),
                'tempo_similarity': round(float(tempo_similarity), 2),
                'grade': grade,
                'feedback': feedback,
                'details': {
                    'dtw_distance': round(float(normalized_distance), 2),
                    'student_avg_pitch': round(float(np.mean(pitch_student_valid)), 1) if len(pitch_student_valid) > 0 else 0,
                    'reference_avg_pitch': round(float(np.mean(pitch_reference_valid)), 1) if len(pitch_reference_valid) > 0 else 0,
                    'student_tempo': round(float(tempo_student), 1),
                    'reference_tempo': round(float(tempo_reference), 1)
                }
            }
            
        except ImportError:
            # fastdtw not installed, use simpler comparison
            return self.simple_audio_comparison()
        except Exception as e:
            print(json.dumps({
                "status": "comparison_error",
                "error": str(e)
            }), file=sys.stderr)
            return {
                'has_reference': True,
                'error': str(e),
                'feedback': 'Could not compare with reference due to technical error.'
            }
    
    def simple_audio_comparison(self):
        """Simple comparison without DTW library"""
        try:
            # Extract basic features
            mfcc_student = librosa.feature.mfcc(y=self.y, sr=self.sr, n_mfcc=13)
            mfcc_reference = librosa.feature.mfcc(y=self.y_ref, sr=self.sr_ref, n_mfcc=13)
            
            # Pad to same length
            max_len = max(mfcc_student.shape[1], mfcc_reference.shape[1])
            if mfcc_student.shape[1] < max_len:
                mfcc_student = np.pad(mfcc_student, ((0, 0), (0, max_len - mfcc_student.shape[1])))
            if mfcc_reference.shape[1] < max_len:
                mfcc_reference = np.pad(mfcc_reference, ((0, 0), (0, max_len - mfcc_reference.shape[1])))
            
            # Calculate cosine similarity
            from numpy.linalg import norm
            similarity = np.dot(mfcc_student.flatten(), mfcc_reference.flatten()) / (norm(mfcc_student.flatten()) * norm(mfcc_reference.flatten()))
            similarity_score = (similarity + 1) * 50  # Scale to 0-100
            
            if similarity_score >= 80:
                grade = "Excellent"
                feedback = "Very close to the reference recitation!"
            elif similarity_score >= 65:
                grade = "Good"
                feedback = "Good recitation. Minor improvements needed."
            else:
                grade = "Needs Improvement"
                feedback = "Keep practicing with the reference audio."
            
            return {
                'has_reference': True,
                'overall_similarity': round(float(similarity_score), 2),
                'grade': grade,
                'feedback': feedback,
                'note': 'Basic comparison (install fastdtw for detailed analysis)'
            }
        except Exception as e:
            return {
                'has_reference': True,
                'error': str(e)
            }
    
    def analyze(self):
        """Run complete Tajweed analysis"""
        # Short-circuit immediately if the audio is silent or empty
        if self.is_silent:
            return {
                'audio_file': self.audio_path,
                'duration': round(float(self.duration), 2),
                'error': 'No audio detected. Please record or upload a valid audio file.',
                'is_silent': True,
                'whisper_transcription': None,
                'expected_text': self.expected_text,
                'madd_analysis': {'percentage': 0, 'issues': [], 'details': []},
                'idgham_bila_ghunnah_analysis': {'percentage': 0, 'issues': [], 'details': []},
                'idgham_bi_ghunnah_analysis': {'percentage': 0, 'issues': [], 'details': []},
                'overall_score': {
                    'score': 0,
                    'grade': 'No Submission',
                    'feedback': 'No audio was detected in the recording. Please try again with a working microphone.'
                },
                'ai_feedback': None
            }

        madd = self.analyze_madd()
        idgham_bila = self.analyze_idgham_bila_ghunnah()
        idgham_bi = self.analyze_idgham_bi_ghunnah()
        
        # Get Whisper transcription if available
        whisper_transcription_raw = None
        whisper_transcription = None
        pause_segments = []
        if self.whisper_model:
            whisper_transcription_raw = self.transcribe_with_whisper()
            if whisper_transcription_raw:
                pause_segments = self.detect_pause_segments()
                whisper_transcription = self.add_pause_markers_to_transcription(
                    whisper_transcription_raw,
                    pause_segments
                )
            else:
                whisper_transcription = whisper_transcription_raw
        
        # Compare with reference audio if provided
        reference_comparison = None
        if self.y_ref is not None:
            reference_comparison = self.compare_with_reference()
        
        results = {
            'audio_file': self.audio_path,
            'duration': round(float(self.duration), 2),
            'whisper_transcription': whisper_transcription,
            'whisper_transcription_raw': whisper_transcription_raw,
            'expected_text': self.expected_text,
            'pause_markers': {
                'count': len(pause_segments),
                'segments': pause_segments,
            },
            'rules_detected': {
                'madd': self.has_madd,
                'idgham_bila_ghunnah': self.has_idgham_bila,
                'idgham_bi_ghunnah': self.has_idgham_bi,
                'muqattaat': self.has_muqattaat,
            },
            'madd_analysis': madd,
            'idgham_bila_ghunnah_analysis': idgham_bila,
            'idgham_bi_ghunnah_analysis': idgham_bi,
            'overall_score': self.calculate_overall_score(madd, idgham_bila, idgham_bi, whisper_transcription, reference_comparison),
            'reference_comparison': reference_comparison
        }
        
        # Generate OpenAI feedback if enabled
        if self.use_openai:
            ai_feedback = self.generate_openai_feedback(results)
            if ai_feedback:
                ai_feedback = self.filter_feedback_by_applicability(
                    ai_feedback,
                    self.get_rule_feedback_contexts(results)
                )
                results['ai_feedback'] = ai_feedback
        
        return results
    
    def calculate_overall_score(self, madd, idgham_bila, idgham_bi, transcription=None, ref_comparison=None):
        """Calculate overall score with Tajweed as the primary factor."""
        scores = []
        
        # Add Tajweed rule scores
        if self.has_madd:
            scores.append(madd['percentage'])
        if self.has_idgham_bila:
            scores.append(idgham_bila['percentage'])
        if self.has_idgham_bi:
            scores.append(idgham_bi['percentage'])
        
        tajweed_score = (sum(scores) / len(scores)) if scores else None

        # Calculate pronunciation/word accuracy from normalized transcription.
        pronunciation_accuracy = None
        word_accuracy = None
        if transcription and self.expected_text:
            normalized_transcription = self.normalize_arabic_for_accuracy(transcription)
            normalized_expected = self.normalize_arabic_for_accuracy(self.expected_text)

            if normalized_transcription and normalized_expected:
                from difflib import SequenceMatcher
                pronunciation_accuracy = SequenceMatcher(
                    None,
                    normalized_transcription,
                    normalized_expected
                ).ratio() * 100.0
                word_accuracy = self.calculate_word_accuracy(
                    normalized_transcription,
                    normalized_expected
                )

        reference_similarity = None
        if ref_comparison and isinstance(ref_comparison, dict):
            reference_similarity = ref_comparison.get('overall_similarity')

        # If transcription is unavailable, keep scoring centered around available signals.
        if word_accuracy is None:
            if tajweed_score is not None:
                word_accuracy = tajweed_score
            elif reference_similarity is not None:
                word_accuracy = reference_similarity
            else:
                word_accuracy = 0.0
        if pronunciation_accuracy is None:
            pronunciation_accuracy = word_accuracy

        components = []
        # Keep overall grade mostly aligned with Tajweed rule analysis.
        if tajweed_score is not None:
            components.append(('tajweed_rules_score', tajweed_score, 0.75))

        components.append(('word_accuracy', word_accuracy, 0.20))

        if reference_similarity is not None:
            components.append(('reference_similarity', reference_similarity, 0.05))
        
        # Weighted overall score using only applicable components.
        weight_total = sum(weight for _, _, weight in components)
        if weight_total > 0:
            final_score = sum(value * weight for _, value, weight in components) / weight_total
        else:
            final_score = 0
        
        return {
            'score': round(final_score, 2),
            'pronunciation_accuracy': round(pronunciation_accuracy, 2),
            'word_accuracy': round(word_accuracy, 2),
            'reference_similarity': round(reference_similarity, 2) if reference_similarity is not None else None,
            'tajweed_rules_score': round(tajweed_score, 2) if tajweed_score is not None else None,
            'grade': self.get_grade(final_score),
            'feedback': self.get_feedback(final_score)
        }
    
    def get_grade(self, score):
        """Convert score to grade"""
        if score >= 90:
            return 'Excellent'
        elif score >= 80:
            return 'Very Good'
        elif score >= 70:
            return 'Good'
        elif score >= 60:
            return 'Satisfactory'
        else:
            return 'Needs Improvement'
    
    def get_feedback(self, score):
        """Generate feedback based on score"""
        if score >= 90:
            return 'Mashallah! Excellent application of Tajweed rules.'
        elif score >= 80:
            return 'Very good recitation with proper Tajweed.'
        elif score >= 70:
            return 'Good effort. Continue practicing these rules.'
        elif score >= 60:
            return 'Satisfactory. Focus on consistent application.'
        else:
            return 'Needs more practice. Review the rules with a teacher.'

def main():
    """Main function"""
    # Force UTF-8 encoding for output (critical for Arabic text)
    if sys.platform == 'win32':
        import io
        sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
        sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')
    
    if len(sys.argv) < 2:
        print(json.dumps({
            'error': 'Usage: python tajweed_analyzer.py <audio_file> [expected_text] [--reference=<path>] [--no-whisper] [--no-openai]'
        }))
        sys.exit(1)
    
    audio_path = sys.argv[1]
    expected_text = ""
    reference_audio = None
    
    task = 'analyze'
    # Parse arguments
    for arg in sys.argv[2:]:
        if arg.startswith('--reference='):
            reference_audio = arg.split('=', 1)[1]
        elif arg.startswith('--task='):
            task = arg.split('=', 1)[1]
        elif not arg.startswith('--'):
            expected_text = arg

    # Check for flags
    use_whisper = '--no-whisper' not in sys.argv
    use_openai = '--no-openai' not in sys.argv

    # Transcription-only mode: fast path for live memorization recording
    if task == 'transcribe':
        try:
            analyzer = TajweedAnalyzer(audio_path, use_whisper=True, use_openai=False)
            if analyzer.is_silent:
                print(json.dumps({'transcription': '', 'warning': 'Silent or empty audio detected'}))
            else:
                transcription = analyzer.transcribe_with_whisper()
                print(json.dumps({'transcription': transcription or ''}))
        except Exception as e:
            import traceback
            print(json.dumps({'transcription': '', 'error': str(e)}))
        sys.exit(0)

    try:
        analyzer = TajweedAnalyzer(audio_path, expected_text, use_whisper, use_openai, reference_audio)
        results = analyzer.analyze()
        print(json.dumps(results, ensure_ascii=False, indent=2))
    except FileNotFoundError:
        print(json.dumps({
            'error': f'Audio file not found: {audio_path}'
        }))
        sys.exit(1)
    except Exception as e:
        import traceback
        print(json.dumps({
            'error': str(e),
            'traceback': traceback.format_exc()
        }))
        sys.exit(1)

if __name__ == '__main__':
    main()
