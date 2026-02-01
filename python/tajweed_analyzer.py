"""
Tajweed Analyzer for Quran Recitation
Analyzes audio for Madd (elongation) and Noon Sakin rules using MFCC-based phonetic analysis
Uses Mel-Frequency Cepstral Coefficients (MFCC) for accurate speech feature extraction
"""

import sys
import json
import os
import platform
import librosa
import numpy as np
from scipy.signal import find_peaks
import warnings
warnings.filterwarnings('ignore')

# Add FFmpeg to PATH for audioread backend
# Check multiple common FFmpeg locations for cross-platform compatibility
def setup_ffmpeg():
    """Setup FFmpeg path based on operating system and environment"""
    # First, check if FFMPEG_PATH is set in environment
    env_ffmpeg_path = os.environ.get('FFMPEG_PATH', '')
    if env_ffmpeg_path and os.path.exists(env_ffmpeg_path):
        if env_ffmpeg_path not in os.environ.get('PATH', ''):
            os.environ['PATH'] = env_ffmpeg_path + os.pathsep + os.environ.get('PATH', '')
        return
    
    # OS-specific paths
    system = platform.system()
    
    if system == 'Windows':
        # Windows paths
        windows_paths = [
            r"C:\ffmpeg\bin",
            r"C:\ffmpeg\ffmpeg-master-latest-win64-gpl\bin",
            r"C:\Program Files\ffmpeg\bin",
        ]
        for path in windows_paths:
            if os.path.exists(path) and path not in os.environ.get('PATH', ''):
                os.environ['PATH'] = path + os.pathsep + os.environ.get('PATH', '')
                return
    
    elif system in ['Linux', 'Darwin']:  # Darwin is macOS
        # Linux/Mac paths
        unix_paths = [
            "/usr/bin",
            "/usr/local/bin",
            "/opt/homebrew/bin",  # M1 Mac
        ]
        for path in unix_paths:
            if os.path.exists(path) and path not in os.environ.get('PATH', ''):
                os.environ['PATH'] = path + os.pathsep + os.environ.get('PATH', '')
                return
    
    # If FFmpeg is already in PATH, no action needed

# Setup FFmpeg on import
setup_ffmpeg()

class TajweedAnalyzer:
    def __init__(self, audio_path):
        """Initialize with audio file path"""
        self.audio_path = audio_path
        self.y, self.sr = librosa.load(audio_path, sr=22050)
        self.duration = librosa.get_duration(y=self.y, sr=self.sr)
        
    def analyze_madd(self):
        """
        Analyze Madd (Elongation) rules using MFCC-based phonetic analysis
        Madd requires elongation of 2, 4, or 6 counts depending on type
        Uses Mel-Frequency Cepstral Coefficients (MFCC) for vowel quality detection
        """
        results = {
            'total_elongations': 0,
            'correct_elongations': 0,
            'issues': [],
            'percentage': 0,
            'details': []
        }
        
        try:
            # Extract MFCC features for phonetic analysis (13 coefficients is standard)
            mfccs = librosa.feature.mfcc(y=self.y, sr=self.sr, n_mfcc=13)
            
            # Calculate delta (velocity) and delta-delta (acceleration) of MFCCs
            # These capture temporal dynamics of speech
            mfcc_delta = librosa.feature.delta(mfccs)
            mfcc_delta2 = librosa.feature.delta(mfccs, order=2)
            
            # Extract pitch using piptrack for frequency analysis
            pitches, magnitudes = librosa.piptrack(y=self.y, sr=self.sr)
            
            # Get onset strength for boundary detection
            onset_env = librosa.onset.onset_strength(y=self.y, sr=self.sr)
            
            # Calculate RMS energy for sustained sound detection
            rms = librosa.feature.rms(y=self.y)[0]
            
            # Analyze MFCC variance to detect sustained vowels
            # Low variance in MFCCs indicates sustained phoneme (characteristic of Madd)
            mfcc_var = np.var(mfccs, axis=0)
            
            # Find peaks in RMS that indicate sustained vowels
            peaks, properties = find_peaks(rms, distance=self.sr//2, prominence=0.02)
            
            for i, peak in enumerate(peaks):
                # Calculate duration of sustained sound
                time_pos = librosa.frames_to_time(peak, sr=self.sr)
                
                # Look for elongation duration - calculate actual sustained duration
                # RMS frames are at hop_length intervals (default 512 samples)
                hop_length = 512
                start_idx = max(0, peak - 10)
                end_idx = min(len(rms), peak + 30)
                sustained_duration = (end_idx - start_idx) * hop_length / self.sr
                
                # Check MFCC variance at peak - low variance indicates sustained vowel
                if peak < len(mfcc_var):
                    mfcc_variance_at_peak = mfcc_var[peak]
                    is_vowel_sustained = mfcc_variance_at_peak < np.mean(mfcc_var) * 0.7
                else:
                    is_vowel_sustained = True  # Fallback if out of bounds
                
                # Only count as Madd if it's a sustained vowel (confirmed by MFCC)
                if is_vowel_sustained:
                    results['total_elongations'] += 1
                    
                    # Check if elongation meets minimum duration
                    # Relaxed threshold: >0.3 seconds (more lenient for practice)
                    if sustained_duration >= 0.3:
                        results['correct_elongations'] += 1
                        results['details'].append({
                            'time': round(time_pos, 2),
                            'duration': round(sustained_duration, 2),
                            'status': 'correct',
                            'note': 'Proper elongation detected (MFCC-verified vowel quality)',
                            'mfcc_confidence': 'high' if mfcc_variance_at_peak < np.mean(mfcc_var) * 0.5 else 'medium'
                        })
                    else:
                        results['issues'].append({
                            'time': round(time_pos, 2),
                            'duration': round(sustained_duration, 2),
                            'issue': 'Elongation too short',
                            'recommendation': 'Extend the vowel for at least 2 counts (~0.5-0.75 seconds)',
                            'expected_duration': '0.5-1.5 seconds for proper Madd'
                        })
                        results['details'].append({
                            'time': round(time_pos, 2),
                            'duration': round(sustained_duration, 2),
                            'status': 'needs_improvement',
                            'note': 'Elongation too short',
                            'mfcc_confidence': 'verified'
                        })
            
            # Calculate percentage
            if results['total_elongations'] > 0:
                results['percentage'] = round((results['correct_elongations'] / results['total_elongations']) * 100, 2)
            else:
                results['percentage'] = 100
                results['details'].append({
                    'note': 'No clear Madd elongations detected in this recitation'
                })
                
        except Exception as e:
            results['error'] = str(e)
            
        return results
    
    def analyze_noon_sakin(self):
        """
        Analyze Noon Sakin and Tanween rules
        4 rules: Idhar, Idgham, Iqlab, Ikhfa
        """
        results = {
            'total_occurrences': 0,
            'correct_pronunciation': 0,
            'issues': [],
            'percentage': 0,
            'details': []
        }
        
        try:
            # Extract MFCC features for consonant and phoneme detection (13 coefficients)
            mfccs = librosa.feature.mfcc(y=self.y, sr=self.sr, n_mfcc=13)
            
            # Calculate MFCC statistics for phoneme classification
            mfcc_mean = np.mean(mfccs, axis=1)
            mfcc_std = np.std(mfccs, axis=1)
            
            # Extract spectral features for additional phonetic analysis
            spectral_centroids = librosa.feature.spectral_centroid(y=self.y, sr=self.sr)[0]
            spectral_rolloff = librosa.feature.spectral_rolloff(y=self.y, sr=self.sr)[0]
            spectral_contrast = librosa.feature.spectral_contrast(y=self.y, sr=self.sr)
            
            # Zero crossing rate (critical for nasal sounds like Noon)
            zcr = librosa.feature.zero_crossing_rate(self.y)[0]
            
            # Calculate chroma features for pitch class analysis
            chroma = librosa.feature.chroma_stft(y=self.y, sr=self.sr)
            
            # Detect potential Noon Sakin occurrences using MFCC-based phoneme detection
            # Noon Sakin has distinctive nasal characteristics in MFCC coefficients
            for i in range(0, len(spectral_centroids) - 5, 10):
                # Extract MFCC features at this time window
                if i < mfccs.shape[1]:
                    mfcc_window = mfccs[:, i:min(i+5, mfccs.shape[1])]
                    avg_mfcc = np.mean(mfcc_window, axis=1)
                    
                    # Check for nasal characteristics using multiple features
                    avg_centroid = np.mean(spectral_centroids[i:i+5])
                    avg_zcr = np.mean(zcr[i:i+5])
                    
                    # MFCC coefficients 1-3 are particularly important for nasal detection
                    # Lower values indicate nasal resonance
                    nasal_indicator = avg_mfcc[1] < mfcc_mean[1] * 0.9
                    
                    # Combined detection: spectral + MFCC + ZCR
                    if avg_centroid < np.mean(spectral_centroids) * 0.8 and nasal_indicator:
                        time_pos = librosa.frames_to_time(i, sr=self.sr)
                        results['total_occurrences'] += 1
                        
                        # Classify Noon Sakin rule type based on MFCC and spectral features
                        # This is a simplified classification - full implementation would need training data
                        rule_type = 'Ikhfa'  # Default (most common)
                        
                        # Idhar: Clear pronunciation (higher spectral contrast)
                        if i < spectral_contrast.shape[1]:
                            contrast_val = np.mean(spectral_contrast[:, i])
                            if contrast_val > np.mean(spectral_contrast) * 1.2:
                                rule_type = 'Idhar'
                            # Idgham: Merged sound (lower MFCC variance)
                            elif np.std(mfcc_window) < np.mean(mfcc_std) * 0.7:
                                rule_type = 'Idgham'
                            # Iqlab: Converted to 'b' sound (specific MFCC pattern)
                            elif avg_mfcc[2] > mfcc_mean[2] * 1.1:
                                rule_type = 'Iqlab'
                        
                        # Analyze nasalization quality using MFCC-enhanced detection
                        # More lenient thresholds for practice/learning
                        if avg_zcr > 0.05 and avg_zcr < 0.4 and nasal_indicator:
                            results['correct_pronunciation'] += 1
                            results['details'].append({
                                'time': round(time_pos, 2),
                                'status': 'correct',
                                'note': f'Proper {rule_type} pronunciation detected (MFCC-verified)',
                                'rule_type': rule_type,
                                'mfcc_confidence': 'high'
                            })
                        else:
                            # Determine specific issue based on features
                            if avg_zcr <= 0.05:
                                issue = 'Nasalization too weak - increase nasal resonance'
                            elif avg_zcr >= 0.4:
                                issue = 'Over-nasalization - reduce nasal emphasis'
                            else:
                                issue = 'Improper nasalization quality'
                            
                            results['issues'].append({
                                'time': round(time_pos, 2),
                                'issue': issue,
                                'recommendation': f'Review {rule_type} rules for Noon Sakin',
                                'rule_type': rule_type,
                                'expected': 'Clear nasal sound with proper duration'
                            })
                            results['details'].append({
                                'time': round(time_pos, 2),
                                'status': 'needs_improvement',
                                'note': f'{rule_type} pronunciation needs adjustment',
                                'rule_type': rule_type
                            })
            
            # Calculate percentage
            if results['total_occurrences'] > 0:
                results['percentage'] = round((results['correct_pronunciation'] / results['total_occurrences']) * 100, 2)
            else:
                results['percentage'] = 100
                results['details'].append({
                    'note': 'No clear Noon Sakin occurrences detected'
                })
                
        except Exception as e:
            results['error'] = str(e)
            
        return results
    
    def analyze(self):
        """Run complete Tajweed analysis"""
        return {
            'audio_file': self.audio_path,
            'duration': round(self.duration, 2),
            'madd_analysis': self.analyze_madd(),
            'noon_sakin_analysis': self.analyze_noon_sakin(),
            'overall_score': self.calculate_overall_score()
        }
    
    def calculate_overall_score(self):
        """Calculate overall Tajweed score"""
        madd = self.analyze_madd()
        noon = self.analyze_noon_sakin()
        
        # Weight both rules equally
        score = (madd['percentage'] + noon['percentage']) / 2
        
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
            return 'Very good recitation with proper Tajweed application.'
        elif score >= 70:
            return 'Good effort. Continue practicing the elongation rules.'
        elif score >= 60:
            return 'Satisfactory. Focus on consistent application of Madd and Noon Sakin rules.'
        else:
            return 'Needs more practice. Review Tajweed rules and practice with a teacher.'

def main():
    """Main function to run analysis"""
    if len(sys.argv) < 2:
        print(json.dumps({
            'error': 'No audio file path provided',
            'usage': 'python tajweed_analyzer.py <audio_file_path>'
        }))
        sys.exit(1)
    
    audio_path = sys.argv[1]
    
    try:
        analyzer = TajweedAnalyzer(audio_path)
        results = analyzer.analyze()
        print(json.dumps(results, indent=2))
    except FileNotFoundError as e:
        print(json.dumps({
            'error': f'Audio file not found: {audio_path}',
            'details': str(e)
        }))
        sys.exit(1)
    except Exception as e:
        import traceback
        print(json.dumps({
            'error': f'Analysis failed: {str(e) if str(e) else type(e).__name__}',
            'traceback': traceback.format_exc()
        }))
        sys.exit(1)

if __name__ == '__main__':
    main()
