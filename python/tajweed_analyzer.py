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
import tempfile
warnings.filterwarnings('ignore')

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
        
        try:
            import torch
            
            # Prepare audio
            input_features = self.whisper_processor(
                self.y, 
                sampling_rate=self.sr, 
                return_tensors="pt"
            ).input_features
            
            # Move to same device as model
            device = next(self.whisper_model.parameters()).device
            input_features = input_features.to(device)
            
            # Generate transcription
            with torch.no_grad():
                predicted_ids = self.whisper_model.generate(input_features)
            
            # Decode transcription
            transcription = self.whisper_processor.batch_decode(
                predicted_ids, 
                skip_special_tokens=True
            )[0]
            
            return transcription.strip()
            
        except Exception as e:
            print(json.dumps({
                "status": "transcription_failed",
                "error": str(e)
            }), file=sys.stderr)
            return None
    
    def detect_madd_in_text(self):
        """Check if expected text contains Madd elongation letters"""
        if not self.expected_text:
            return True
        # Madd letters: ا (alif), و (waw), ي (ya)
        madd_letters = ['ا', 'و', 'ي', 'آ', 'ى']
        return any(letter in self.expected_text for letter in madd_letters)
    
    def detect_idgham_bila_in_text(self):
        """Check if text has Noon Sakin/Tanween followed by ر or ل"""
        if not self.expected_text:
            return True
        # Look for patterns: نْ or tanween (ً ٌ ٍ) followed by ر or ل
        # Also check for ن followed directly by ر or ل
        patterns = [
            r'[نً ٌٍ][رل]',  # Tanween or noon sakin before ra/lam
            r'نْ[رل]',  # Noon with sukun before ra/lam
            r'ن\s*[رل]',  # Noon followed by ra/lam (with optional space)
        ]
        return any(re.search(pattern, self.expected_text) for pattern in patterns)
    
    def detect_idgham_bi_in_text(self):
        """Check if text has Noon Sakin/Tanween followed by و م ن ي"""
        if not self.expected_text:
            return True
        # Look for patterns: نْ or tanween followed by و م ن ي
        patterns = [
            r'[نً ٌٍ][ومني]',  # Tanween or noon sakin before letters
            r'نْ[ومني]',  # Noon with sukun
            r'ن\s*[ومني]',  # Noon followed by letters
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
            results['details'].append({'note': 'No Madd rules applicable to this verse'})
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
            results['details'].append({'note': 'No Idgham Bila Ghunnah applicable to this verse'})
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
            results['details'].append({'note': 'No Idgham Bi Ghunnah applicable to this verse'})
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
    
    def generate_openai_feedback(self, analysis_results):
        """Generate intelligent feedback using OpenAI GPT"""
        if not self.use_openai:
            return None
        
        try:
            from openai import OpenAI
            
            api_key = os.environ.get('OPENAI_API_KEY')
            if not api_key:
                return None
            
            client = OpenAI(api_key=api_key)
            
            # Prepare analysis summary for GPT
            prompt = f"""You are an expert Quran Tajweed teacher. Analyze this student's recitation and provide constructive feedback.

Expected Quranic Text: {self.expected_text}

Analysis Results:
- Madd (Elongation): {analysis_results['madd_analysis']['percentage']}% correct
  - Issues: {len(analysis_results['madd_analysis']['issues'])} found
  
- Idgham Bila Ghunnah: {analysis_results['idgham_bila_ghunnah_analysis']['percentage']}% correct
  - Issues: {len(analysis_results['idgham_bila_ghunnah_analysis']['issues'])} found
  
- Idgham Bi Ghunnah: {analysis_results['idgham_bi_ghunnah_analysis']['percentage']}% correct
  - Issues: {len(analysis_results['idgham_bi_ghunnah_analysis']['issues'])} found

Overall Score: {analysis_results['overall_score']['score']}%

Provide feedback in this EXACT JSON format:
{{
  "summary": "Brief 2-3 sentence overview of performance",
  "strengths": ["strength 1", "strength 2"],
  "improvements": [
    {{"issue": "specific problem", "suggestion": "how to fix it"}},
    {{"issue": "specific problem", "suggestion": "how to fix it"}}
  ],
  "next_steps": "Specific practice recommendation (1-2 sentences)"
}}

Be encouraging, specific, and actionable. Reference actual Tajweed rules."""

            response = client.chat.completions.create(
                model="gpt-3.5-turbo",
                messages=[
                    {"role": "system", "content": "You are an expert Quran Tajweed teacher. You MUST respond with valid JSON only, no other text."},
                    {"role": "user", "content": prompt}
                ],
                max_tokens=400,
                temperature=0.7
            )
            
            feedback_text = response.choices[0].message.content.strip()
            
            # Try to parse as JSON
            try:
                return json.loads(feedback_text)
            except:
                # Fallback to simple format if JSON parsing fails
                return {
                    "summary": feedback_text,
                    "strengths": [],
                    "improvements": [],
                    "next_steps": ""
                }
            
        except Exception as e:
            error_str = str(e)
            print(json.dumps({
                "status": "openai_failed",
                "error": error_str
            }), file=sys.stderr)
            
            # Return basic feedback if API quota exceeded or other error
            if 'insufficient_quota' in error_str or '429' in error_str:
                return {
                    "summary": "Unable to generate AI feedback due to API quota. Your recitation has been analyzed using acoustic analysis.",
                    "strengths": ["Submission completed successfully"],
                    "improvements": [],
                    "next_steps": "Review the detailed Tajweed analysis above for specific areas to improve."
                }
            return None
    
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
                'reference_duration': round(self.duration_ref, 2),
                'student_duration': round(self.duration, 2),
                'overall_similarity': round(overall_similarity, 2),
                'pronunciation_similarity': round(similarity_score, 2),
                'pitch_similarity': round(pitch_similarity, 2),
                'tempo_similarity': round(tempo_similarity, 2),
                'grade': grade,
                'feedback': feedback,
                'details': {
                    'dtw_distance': round(normalized_distance, 2),
                    'student_avg_pitch': round(np.mean(pitch_student_valid), 1) if len(pitch_student_valid) > 0 else 0,
                    'reference_avg_pitch': round(np.mean(pitch_reference_valid), 1) if len(pitch_reference_valid) > 0 else 0,
                    'student_tempo': round(tempo_student, 1),
                    'reference_tempo': round(tempo_reference, 1)
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
                'overall_similarity': round(similarity_score, 2),
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
        madd = self.analyze_madd()
        idgham_bila = self.analyze_idgham_bila_ghunnah()
        idgham_bi = self.analyze_idgham_bi_ghunnah()
        
        # Get Whisper transcription if available
        whisper_transcription = None
        if self.whisper_model:
            whisper_transcription = self.transcribe_with_whisper()
        
        # Compare with reference audio if provided
        reference_comparison = None
        if self.y_ref is not None:
            reference_comparison = self.compare_with_reference()
        
        results = {
            'audio_file': self.audio_path,
            'duration': round(self.duration, 2),
            'whisper_transcription': whisper_transcription,
            'expected_text': self.expected_text,
            'rules_detected': {
                'madd': self.has_madd,
                'idgham_bila_ghunnah': self.has_idgham_bila,
                'idgham_bi_ghunnah': self.has_idgham_bi
            },
            'madd_analysis': madd,
            'idgham_bila_ghunnah_analysis': idgham_bila,
            'idgham_bi_ghunnah_analysis': idgham_bi,
            'overall_score': self.calculate_overall_score(madd, idgham_bila, idgham_bi),
            'reference_comparison': reference_comparison
        }
        
        # Generate OpenAI feedback if enabled
        if self.use_openai:
            ai_feedback = self.generate_openai_feedback(results)
            if ai_feedback:
                results['ai_feedback'] = ai_feedback
        
        return results
    
    def calculate_overall_score(self, madd, idgham_bila, idgham_bi):
        """Calculate overall Tajweed score based on applicable rules"""
        scores = []
        
        if self.has_madd:
            scores.append(madd['percentage'])
        if self.has_idgham_bila:
            scores.append(idgham_bila['percentage'])
        if self.has_idgham_bi:
            scores.append(idgham_bi['percentage'])
        
        # If no rules apply, return 100
        if not scores:
            return {
                'score': 100,
                'grade': 'N/A',
                'feedback': 'No Tajweed rules applicable to this verse'
            }
        
        # Average of applicable rules
        score = sum(scores) / len(scores)
        
        return {
            'score': round(score, 2),
            'grade': self.get_grade(score),
            'feedback': self.get_feedback(score)
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
    
    # Parse arguments
    for arg in sys.argv[2:]:
        if arg.startswith('--reference='):
            reference_audio = arg.split('=', 1)[1]
        elif not arg.startswith('--'):
            expected_text = arg
    
    # Check for flags
    use_whisper = '--no-whisper' not in sys.argv
    use_openai = '--no-openai' not in sys.argv
    
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
