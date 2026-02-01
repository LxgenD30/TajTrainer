"""
Tajweed Analyzer for Quran Recitation
Analyzes audio for specific Tajweed rules:
1. Madd (Elongation)
2. Idgham Bila Ghunnah (Merging without nasalization - ر ل)
3. Idgham Bi Ghunnah (Merging with nasalization - و م ن ي)
Uses MFCC-based phonetic analysis
"""

import sys
import json
import os
import platform
import librosa
import numpy as np
from scipy.signal import find_peaks
import warnings
import re
warnings.filterwarnings('ignore')

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
    def __init__(self, audio_path, expected_text=""):
        """Initialize with audio file path and expected Quranic text"""
        self.audio_path = audio_path
        self.expected_text = expected_text
        self.y, self.sr = librosa.load(audio_path, sr=22050)
        self.duration = librosa.get_duration(y=self.y, sr=self.sr)
        
        # Detect which rules apply to this verse
        self.has_madd = self.detect_madd_in_text()
        self.has_idgham_bila = self.detect_idgham_bila_in_text()
        self.has_idgham_bi = self.detect_idgham_bi_in_text()
        
    def detect_madd_in_text(self):
        """Check if expected text contains Madd elongation letters"""
        if not self.expected_text:
            return True  # Assume it exists if no text provided
        # Madd letters: ا (alif), و (waw), ي (ya)
        madd_letters = ['ا', 'و', 'ي', 'آ', 'ى']
        return any(letter in self.expected_text for letter in madd_letters)
    
    def detect_idgham_bila_in_text(self):
        """Check if text has Noon Sakin/Tanween followed by ر or ل"""
        if not self.expected_text:
            return True
        # Look for patterns: نْ or tanween (ً ٌ ٍ) followed by ر or ل
        pattern = r'[نً ٌٍ][رل]'
        return bool(re.search(pattern, self.expected_text))
    
    def detect_idgham_bi_in_text(self):
        """Check if text has Noon Sakin/Tanween followed by و م ن ي"""
        if not self.expected_text:
            return True
        # Look for patterns: نْ or tanween followed by و م ن ي
        pattern = r'[نً ٌٍ][ومني]'
        return bool(re.search(pattern, self.expected_text))
    
    def analyze_madd(self):
        """Analyze Madd (Elongation) rules using MFCC"""
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
            # Extract MFCC features
            mfccs = librosa.feature.mfcc(y=self.y, sr=self.sr, n_mfcc=13)
            mfcc_delta = librosa.feature.delta(mfccs)
            
            # RMS energy for sustained sound detection
            rms = librosa.feature.rms(y=self.y)[0]
            
            # MFCC variance to detect sustained vowels
            mfcc_var = np.var(mfccs, axis=0)
            
            # Find peaks in RMS that indicate sustained vowels
            peaks, properties = find_peaks(rms, distance=self.sr//2, prominence=0.015)
            
            for i, peak in enumerate(peaks):
                time_pos = librosa.frames_to_time(peak, sr=self.sr)
                
                # Calculate sustained duration
                hop_length = 512
                start_idx = max(0, peak - 10)
                end_idx = min(len(rms), peak + 30)
                sustained_duration = (end_idx - start_idx) * hop_length / self.sr
                
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
                            'note': 'Proper Madd elongation detected'
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
                # If expected but not detected, give low score
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
        Analyze Idgham Bila Ghunnah (Merging without nasalization)
        Occurs when Noon Sakin/Tanween meets ر or ل
        Should merge WITHOUT nasal sound
        """
        results = {
            'total_occurrences': 0,
            'correct_pronunciation': 0,
            'issues': [],
            'percentage': 0,
            'details': [],
            'rule_applicable': self.has_idgham_bila
        }
        
        if not self.has_idgham_bila:
            results['percentage'] = 100
            results['details'].append({'note': 'No Idgham Bila Ghunnah applicable to this verse'})
            return results
        
        try:
            # Extract features for consonant detection
            mfccs = librosa.feature.mfcc(y=self.y, sr=self.sr, n_mfcc=13)
            
            # Zero crossing rate (should be LOWER for proper Idgham Bila)
            zcr = librosa.feature.zero_crossing_rate(self.y)[0]
            
            # Spectral centroid for brightness detection
            spectral_centroids = librosa.feature.spectral_centroid(y=self.y, sr=self.sr)[0]
            
            # Detect ر (ra) or ل (lam) sounds - they have specific spectral characteristics
            for i in range(0, len(spectral_centroids) - 5, 15):
                if i < mfccs.shape[1]:
                    mfcc_window = mfccs[:, i:min(i+5, mfccs.shape[1])]
                    avg_mfcc = np.mean(mfcc_window, axis=1)
                    
                    avg_centroid = np.mean(spectral_centroids[i:i+5])
                    avg_zcr = np.mean(zcr[i:i+5])
                    
                    # Detect ر or ل: higher spectral centroid, low ZCR
                    # These are liquid consonants with clear merging
                    is_liquid_consonant = (
                        avg_centroid > np.mean(spectral_centroids) * 1.1 and
                        avg_zcr < np.mean(zcr) * 0.8
                    )
                    
                    if is_liquid_consonant:
                        time_pos = librosa.frames_to_time(i, sr=self.sr)
                        results['total_occurrences'] += 1
                        
                        # Check for LACK of nasalization (ZCR should be low, no nasal resonance)
                        nasal_present = avg_zcr > 0.12  # If higher, nasal sound detected
                        
                        if not nasal_present:  # Correct - no nasalization
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
        Analyze Idgham Bi Ghunnah (Merging with nasalization)
        Occurs when Noon Sakin/Tanween meets و م ن ي
        Should merge WITH nasal sound for 2 counts
        """
        results = {
            'total_occurrences': 0,
            'correct_pronunciation': 0,
            'issues': [],
            'percentage': 0,
            'details': [],
            'rule_applicable': self.has_idgham_bi
        }
        
        if not self.has_idgham_bi:
            results['percentage'] = 100
            results['details'].append({'note': 'No Idgham Bi Ghunnah applicable to this verse'})
            return results
        
        try:
            # Extract features for nasal detection
            mfccs = librosa.feature.mfcc(y=self.y, sr=self.sr, n_mfcc=13)
            
            # Zero crossing rate (should be HIGHER for nasal sounds)
            zcr = librosa.feature.zero_crossing_rate(self.y)[0]
            
            # Spectral features
            spectral_centroids = librosa.feature.spectral_centroid(y=self.y, sr=self.sr)[0]
            
            # RMS for duration measurement
            rms = librosa.feature.rms(y=self.y)[0]
            
            # Detect nasal consonants و م ن ي
            for i in range(0, len(spectral_centroids) - 5, 15):
                if i < mfccs.shape[1]:
                    mfcc_window = mfccs[:, i:min(i+5, mfccs.shape[1])]
                    avg_mfcc = np.mean(mfcc_window, axis=1)
                    
                    avg_centroid = np.mean(spectral_centroids[i:i+5])
                    avg_zcr = np.mean(zcr[i:i+5])
                    avg_rms = np.mean(rms[i:i+5])
                    
                    # Detect nasal characteristic: specific MFCC pattern + higher ZCR
                    is_nasal = (
                        avg_mfcc[1] < np.mean(mfccs[1, :]) * 0.9 and  # Nasal formant
                        avg_zcr > 0.08 and  # Nasal resonance
                        avg_rms > np.mean(rms) * 0.6  # Sufficient energy
                    )
                    
                    if is_nasal:
                        time_pos = librosa.frames_to_time(i, sr=self.sr)
                        results['total_occurrences'] += 1
                        
                        # Calculate duration of nasalization
                        start_idx = max(0, i - 5)
                        end_idx = min(len(rms), i + 15)
                        nasal_duration = (end_idx - start_idx) * 512 / self.sr
                        
                        # Check if nasalization is present AND properly sustained (2 counts = ~0.4-0.5s)
                        proper_nasalization = (
                            avg_zcr > 0.08 and  # Nasal quality present
                            nasal_duration >= 0.3  # Sustained for 2 counts
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
                            issue = ''
                            if nasal_duration < 0.3:
                                issue = 'Dengung too short - should be 2 counts'
                            else:
                                issue = 'Dengung quality weak'
                            
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
    
    def analyze(self):
        """Run complete Tajweed analysis for the 3 specific rules"""
        madd = self.analyze_madd()
        idgham_bila = self.analyze_idgham_bila_ghunnah()
        idgham_bi = self.analyze_idgham_bi_ghunnah()
        
        return {
            'audio_file': self.audio_path,
            'duration': round(self.duration, 2),
            'rules_detected': {
                'madd': self.has_madd,
                'idgham_bila_ghunnah': self.has_idgham_bila,
                'idgham_bi_ghunnah': self.has_idgham_bi
            },
            'madd_analysis': madd,
            'idgham_bila_ghunnah_analysis': idgham_bila,
            'idgham_bi_ghunnah_analysis': idgham_bi,
            'overall_score': self.calculate_overall_score(madd, idgham_bila, idgham_bi)
        }
    
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
    if len(sys.argv) < 2:
        print(json.dumps({
            'error': 'Usage: python tajweed_analyzer.py <audio_file> [expected_text]'
        }))
        sys.exit(1)
    
    audio_path = sys.argv[1]
    expected_text = sys.argv[2] if len(sys.argv) > 2 else ""
    
    try:
        analyzer = TajweedAnalyzer(audio_path, expected_text)
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
