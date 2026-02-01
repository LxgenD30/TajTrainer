@echo off
echo ==========================================
echo Tajweed Analyzer - Python Setup
echo ==========================================
echo.

REM Check if Python is installed
python --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Python is not installed or not in PATH
    echo Please install Python from https://www.python.org/downloads/
    echo Make sure to check "Add Python to PATH" during installation
    pause
    exit /b 1
)

echo Python found!
python --version
echo.

echo Installing Python dependencies...
echo.

cd /d "%~dp0"

pip install -r requirements.txt

if %errorlevel% equ 0 (
    echo.
    echo ==========================================
    echo Installation completed successfully!
    echo ==========================================
    echo.
    echo You can now use the Tajweed analyzer.
    echo Test it with: python tajweed_analyzer.py "audio_file.wav"
) else (
    echo.
    echo ==========================================
    echo Installation failed!
    echo ==========================================
    echo Please check the error messages above.
)

echo.
pause
