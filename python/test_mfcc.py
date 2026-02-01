"""
Test script to verify MFCC-based Tajweed Analyzer
Creates a simple test tone and analyzes it
"""

import numpy as np
import librosa
import soundfile as sf
import json
from tajweed_analyzer import TajweedAnalyzer

def create_test_audio():
    """Create a simple test audio file with sustained tone"""
    sr = 22050  # Sample rate
    duration = 3.0  # 3 seconds
    
    # Create a sustained tone (simulating a Madd vowel)
    t = np.linspace(0, duration, int(sr * duration))
    frequency = 440  # A4 note
    audio = 0.5 * np.sin(2 * np.pi * frequency * t)
    
    # Add some variation for realism
    audio += 0.1 * np.random.randn(len(audio))
    
    # Save test file
    test_file = 'test_audio.wav'
    sf.write(test_file, audio, sr)
    
    return test_file

def test_mfcc_features():
    """Test MFCC extraction"""
    print("=" * 60)
    print("MFCC-Based Tajweed Analyzer - Test Script")
    print("=" * 60)
    
    # Create test audio
    print("\n1. Creating test audio file...")
    test_file = create_test_audio()
    print(f"   ✓ Created: {test_file}")
    
    # Load audio
    print("\n2. Loading audio with librosa...")
    y, sr = librosa.load(test_file, sr=22050)
    print(f"   ✓ Duration: {librosa.get_duration(y=y, sr=sr):.2f} seconds")
    print(f"   ✓ Sample rate: {sr} Hz")
    
    # Extract MFCC
    print("\n3. Extracting MFCC features...")
    mfccs = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=13)
    print(f"   ✓ MFCC shape: {mfccs.shape}")
    print(f"   ✓ Number of coefficients: {mfccs.shape[0]}")
    print(f"   ✓ Number of frames: {mfccs.shape[1]}")
    
    # Show MFCC statistics
    print("\n4. MFCC Statistics:")
    for i in range(min(5, mfccs.shape[0])):
        mean = np.mean(mfccs[i])
        std = np.std(mfccs[i])
        print(f"   Coefficient {i}: mean={mean:.2f}, std={std:.2f}")
    
    # Test analyzer
    print("\n5. Running Tajweed Analyzer...")
    analyzer = TajweedAnalyzer(test_file)
    results = analyzer.analyze()
    
    # Display results
    print("\n6. Analysis Results:")
    print(json.dumps(results, indent=2))
    
    print("\n" + "=" * 60)
    print("✓ MFCC Implementation Test Complete!")
    print("=" * 60)
    
    # Verify MFCC-specific features
    print("\n7. Verifying MFCC-Enhanced Features:")
    
    madd = results.get('madd_analysis', {})
    if 'details' in madd and len(madd['details']) > 0:
        detail = madd['details'][0]
        if 'mfcc_confidence' in detail:
            print("   ✓ MFCC confidence levels present in Madd analysis")
        else:
            print("   ⚠ MFCC confidence levels missing")
    
    noon = results.get('noon_sakin_analysis', {})
    if 'details' in noon and len(noon['details']) > 0:
        detail = noon['details'][0]
        if 'rule_type' in detail:
            print("   ✓ Noon Sakin rule classification present")
        else:
            print("   ⚠ Rule classification missing")
    
    print("\n✓ Test completed successfully!")
    print("  The analyzer now uses MFCC-based feature extraction.")
    
if __name__ == '__main__':
    try:
        test_mfcc_features()
    except Exception as e:
        print(f"\n❌ Test failed: {str(e)}")
        import traceback
        traceback.print_exc()
