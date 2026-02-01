"""
Quick test to verify Parselmouth installation and functionality
"""
import sys

print("=" * 60)
print("Parselmouth Installation Test")
print("=" * 60)

try:
    import parselmouth
    from parselmouth.praat import call
    print("✓ Parselmouth installed successfully")
    print(f"  Version: {parselmouth.__version__}")
    print()
    
    # Test basic functionality
    print("Testing basic Parselmouth functionality...")
    try:
        # Create a simple test sound
        import numpy as np
        
        # Generate a 1-second tone at 440 Hz
        duration = 1.0
        sampling_frequency = 22050
        t = np.linspace(0, duration, int(sampling_frequency * duration))
        tone = np.sin(2 * np.pi * 440 * t)
        
        # Create Parselmouth Sound object
        snd = parselmouth.Sound(tone, sampling_frequency=sampling_frequency)
        print(f"✓ Created test sound: {duration}s at {sampling_frequency}Hz")
        
        # Extract pitch
        pitch = snd.to_pitch()
        f0 = call(pitch, "Get value at time", 0.5, "Hertz", "Linear")
        print(f"✓ Pitch analysis works: F0={f0:.1f}Hz (expected ~440Hz)")
        
        # Extract formants
        formant = snd.to_formant_burg()
        f1 = call(formant, "Get value at time", 1, 0.5, "Hertz", "Linear")
        print(f"✓ Formant analysis works: F1={f1:.1f}Hz")
        
        # Extract intensity
        intensity = snd.to_intensity()
        power = call(intensity, "Get value at time", 0.5, "Cubic")
        print(f"✓ Intensity analysis works: {power:.1f}dB")
        
        print()
        print("=" * 60)
        print("SUCCESS: Parselmouth is fully functional!")
        print("=" * 60)
        
    except Exception as e:
        print(f"✗ Parselmouth test failed: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
        
except ImportError:
    print("✗ Parselmouth not installed")
    print()
    print("To install Parselmouth:")
    print("  pip install praat-parselmouth")
    print()
    sys.exit(1)
