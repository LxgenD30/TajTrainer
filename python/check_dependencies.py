#!/usr/bin/env python3
"""
Dependency checker for TajTrainer Python audio analyzer.
This script verifies all required Python packages are installed and functional.
"""

import os
import sys
import json

# Limit OpenBLAS threads for shared hosting environments
# This prevents "Resource temporarily unavailable" errors
os.environ['OPENBLAS_NUM_THREADS'] = '1'
os.environ['MKL_NUM_THREADS'] = '1'
os.environ['OMP_NUM_THREADS'] = '1'
os.environ['NUMEXPR_NUM_THREADS'] = '1'

def check_dependencies():
    """Check all required dependencies and return detailed report."""
    
    results = {
        'python_version': f"{sys.version_info.major}.{sys.version_info.minor}.{sys.version_info.micro}",
        'dependencies': {},
        'all_ok': True
    }
    
    # List of required packages with their import names
    required_packages = {
        'numpy': 'numpy',
        'scipy': 'scipy',
        'librosa': 'librosa',
        'parselmouth': 'parselmouth (Praat integration)',
        'fastdtw': 'fastdtw (Dynamic Time Warping)',
        'openai': 'openai (GPT-4 API)',
        'requests': 'requests (HTTP client)',
        'soundfile': 'soundfile (Audio I/O)'
    }
    
    print("=" * 60)
    print("TajTrainer Python Dependencies Check")
    print("=" * 60)
    print(f"\nPython Version: {results['python_version']}")
    print(f"Python Executable: {sys.executable}")
    print(f"\nEnvironment:")
    print(f"  OPENBLAS_NUM_THREADS: {os.environ.get('OPENBLAS_NUM_THREADS', 'not set')}")
    print(f"  Working Directory: {os.getcwd()}")
    print("\nChecking dependencies...\n")
    
    for package_name, description in required_packages.items():
        try:
            # Try to import the package
            if package_name == 'parselmouth':
                import parselmouth
                version = parselmouth.__version__
            elif package_name == 'librosa':
                import librosa
                version = librosa.__version__
            elif package_name == 'numpy':
                import numpy
                version = numpy.__version__
            elif package_name == 'scipy':
                import scipy
                version = scipy.__version__
            elif package_name == 'fastdtw':
                import fastdtw
                version = fastdtw.__version__ if hasattr(fastdtw, '__version__') else 'unknown'
            elif package_name == 'openai':
                import openai
                version = openai.__version__
            elif package_name == 'requests':
                import requests
                version = requests.__version__
            elif package_name == 'soundfile':
                import soundfile
                version = soundfile.__version__
            else:
                __import__(package_name)
                version = 'installed'
            
            results['dependencies'][package_name] = {
                'status': 'OK',
                'version': version,
                'description': description
            }
            print(f"✓ {package_name:20s} v{version:15s} - {description}")
            
        except ImportError as e:
            results['dependencies'][package_name] = {
                'status': 'MISSING',
                'error': str(e),
                'description': description
            }
            results['all_ok'] = False
            print(f"✗ {package_name:20s} {'NOT FOUND':15s} - {description}")
            print(f"  Error: {str(e)[:100]}")
        except RuntimeError as e:
            # Handle CPU dispatcher errors gracefully
            if 'CPU dispatcher' in str(e):
                results['dependencies'][package_name] = {
                    'status': 'WARNING',
                    'error': str(e),
                    'description': description
                }
                print(f"⚠ {package_name:20s} {'WARNING':15s} - {description}")
                print(f"  Note: Threading issue (safe to ignore on shared hosting)")
            else:
                results['dependencies'][package_name] = {
                    'status': 'ERROR',
                    'error': str(e),
                    'description': description
                }
                results['all_ok'] = False
                print(f"✗ {package_name:20s} {'ERROR':15s} - {description}")
                print(f"  Error: {str(e)[:100]}")
        except Exception as e:
            results['dependencies'][package_name] = {
                'status': 'ERROR',
                'error': str(e),
                'description': description
            }
            results['all_ok'] = False
            print(f"⚠ {package_name:20s} {'ERROR':15s} - {description}")
            print(f"  Error: {str(e)[:100]}")
    
    print("\n" + "=" * 60)
    
    if results['all_ok']:
        print("✓ All dependencies are installed and functional!")
        print("\nThe tajweed_analyzer.py should work correctly.")
    else:
        print("✗ Some dependencies are missing or have errors!")
        print("\nTo install missing dependencies, run:")
        print("  pip install -r requirements.txt")
        print("\nOr install individual packages:")
        missing = [pkg for pkg, info in results['dependencies'].items() 
                  if info['status'] != 'OK']
        if missing:
            print(f"  pip install {' '.join(missing)}")
    
    print("=" * 60)
    
    return results

def test_parselmouth():
    """Test Parselmouth (Praat) functionality with a simple sound object."""
    try:
        import parselmouth
        import numpy as np
        
        print("\n" + "=" * 60)
        print("Testing Parselmouth (Praat) Functionality")
        print("=" * 60)
        
        # Create a simple sine wave for testing
        sample_rate = 16000
        duration = 0.5
        frequency = 440  # A4 note
        
        t = np.linspace(0, duration, int(sample_rate * duration))
        wave = np.sin(2 * np.pi * frequency * t)
        
        # Create a Sound object
        sound = parselmouth.Sound(wave, sampling_frequency=sample_rate)
        
        # Try to extract pitch
        pitch = sound.to_pitch()
        pitch_values = pitch.selected_array['frequency']
        
        # Try to extract formants
        formants = sound.to_formant_burg()
        
        # Try to extract intensity
        intensity = sound.to_intensity()
        
        print(f"\n✓ Successfully created Sound object")
        print(f"✓ Duration: {sound.duration:.2f}s")
        print(f"✓ Sampling rate: {sound.sampling_frequency} Hz")
        print(f"✓ Pitch extraction: OK ({len(pitch_values)} values)")
        print(f"✓ Formant extraction: OK")
        print(f"✓ Intensity extraction: OK")
        print(f"\n✓ Parselmouth is fully functional!")
        print("=" * 60)
        
        return True
        
    except Exception as e:
        print(f"\n✗ Parselmouth test failed!")
        print(f"Error: {e}")
        print("=" * 60)
        return False

def test_dtw():
    """Test FastDTW functionality."""
    try:
        from fastdtw import fastdtw
        from scipy.spatial.distance import euclidean
        import numpy as np
        
        print("\n" + "=" * 60)
        print("Testing FastDTW (Dynamic Time Warping)")
        print("=" * 60)
        
        # Create two simple sequences (reshape to 2D for DTW)
        x = np.array([[1], [2], [3], [4], [5]])
        y = np.array([[1], [2], [2], [3], [4], [5]])
        
        # Compute DTW distance
        distance, path = fastdtw(x, y, dist=euclidean)
        
        print(f"\n✓ Successfully computed DTW distance")
        print(f"✓ Distance: {distance:.2f}")
        print(f"✓ Path length: {len(path)}")
        print(f"\n✓ FastDTW is fully functional!")
        print("=" * 60)
        
        return True
        
    except Exception as e:
        print(f"\n✗ FastDTW test failed!")
        print(f"Error: {e}")
        print("=" * 60)
        return False

if __name__ == '__main__':
    # Check all dependencies
    results = check_dependencies()
    
    # If all basic dependencies are OK, run functional tests
    if results['all_ok']:
        parselmouth_ok = test_parselmouth()
        dtw_ok = test_dtw()
        
        if parselmouth_ok and dtw_ok:
            print("\n✓✓✓ All systems ready! ✓✓✓\n")
            sys.exit(0)
        else:
            print("\n⚠ Some functionality tests failed.\n")
            sys.exit(1)
    else:
        print("\n✗ Please install missing dependencies before proceeding.\n")
        # Output JSON for programmatic parsing
        print("\nJSON Report:")
        print(json.dumps(results, indent=2))
        sys.exit(1)
