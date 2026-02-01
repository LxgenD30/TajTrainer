"""
Test the enhanced Tajweed analyzer with reference audio comparison
"""
import sys
import os

# Add parent directory to path
sys.path.insert(0, os.path.dirname(__file__))

print("=" * 70)
print("TESTING REFERENCE AUDIO COMPARISON")
print("=" * 70)
print()

# Create two test audio files - one "good" and one "poor" recitation
import numpy as np
from scipy.io import wavfile

def create_test_audio(filename, quality="good"):
    """Create a synthetic Quran recitation with different quality levels"""
    sr = 22050
    duration = 3.0
    
    # Base frequencies for Arabic vowels
    if quality == "good":
        # Proper Madd duration (0.5s) with stable pitch
        f0 = 180  # Stable pitch
        madd_duration = 0.5
        pitch_variation = 2  # Hz - very stable
    else:
        # Poor Madd duration (0.3s) with unstable pitch
        f0 = 180
        madd_duration = 0.3
        pitch_variation = 15  # Hz - unstable
    
    t = np.linspace(0, duration, int(sr * duration))
    
    # Generate vowel with varying pitch
    pitch = f0 + pitch_variation * np.sin(2 * np.pi * 3 * t)  # Pitch modulation
    signal = np.sin(2 * np.pi * pitch * t)
    
    # Add formants for vowel quality
    signal += 0.3 * np.sin(2 * np.pi * 700 * t)  # F1
    signal += 0.2 * np.sin(2 * np.pi * 1220 * t)  # F2
    
    # Create envelope with Madd
    envelope = np.ones_like(t)
    attack = int(0.1 * sr)
    sustain_start = attack
    sustain_end = sustain_start + int(madd_duration * sr)
    release = int(0.1 * sr)
    
    envelope[:attack] = np.linspace(0, 1, attack)
    if sustain_end + release < len(envelope):
        envelope[sustain_end:sustain_end + release] = np.linspace(1, 0, release)
        envelope[sustain_end + release:] = 0
    
    signal *= envelope
    signal = signal / np.max(np.abs(signal))
    signal = (signal * 32767).astype(np.int16)
    
    wavfile.write(filename, sr, signal)
    print(f"✓ Created {quality} quality audio: {filename}")
    print(f"  - Madd duration: {madd_duration}s")
    print(f"  - Pitch stability: {'Stable' if pitch_variation < 5 else 'Unstable'}")
    print()

# Create test directory
test_dir = "test_audio_samples"
os.makedirs(test_dir, exist_ok=True)

# Create reference (excellent recitation)
reference_file = os.path.join(test_dir, "reference_excellent.wav")
create_test_audio(reference_file, "good")

# Create student audio - good quality
student_good_file = os.path.join(test_dir, "student_good.wav")
create_test_audio(student_good_file, "good")

# Create student audio - poor quality
student_poor_file = os.path.join(test_dir, "student_poor.wav")
create_test_audio(student_poor_file, "poor")

print("=" * 70)
print("RUNNING COMPARISON TESTS")
print("=" * 70)
print()

# Test 1: Compare good student with reference
print("TEST 1: Good student vs Reference")
print("-" * 70)
os.system(f'python tajweed_analyzer.py "{student_good_file}" --reference="{reference_file}" --no-whisper --no-openai')
print()

# Test 2: Compare poor student with reference
print("\nTEST 2: Poor student vs Reference")
print("-" * 70)
os.system(f'python tajweed_analyzer.py "{student_poor_file}" --reference="{reference_file}" --no-whisper --no-openai')
print()

print("=" * 70)
print("EXPECTED RESULTS:")
print("=" * 70)
print("✓ Test 1 should show HIGH similarity (>80%)")
print("✓ Test 2 should show LOWER similarity (<70%)")
print("✓ Both should include 'reference_comparison' section in output")
print("✓ Comparison should show pronunciation, pitch, and tempo similarities")
print("=" * 70)
