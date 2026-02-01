@echo off
REM TajTrainer Python Analyzer Setup Script for Windows
REM This script sets up the Python environment for the Tajweed audio analyzer

echo ============================================================
echo TajTrainer Python Analyzer Setup
echo ============================================================
echo.

REM Check if Python is installed
echo Checking Python installation...
python --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Python not found!
    echo Please install Python 3.8 or higher from https://www.python.org/
    pause
    exit /b 1
)

python --version
echo.

REM Check if pip is installed
echo Checking pip installation...
python -m pip --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] pip not found!
    echo Please ensure pip is installed with Python
    pause
    exit /b 1
)

python -m pip --version
echo.

REM Navigate to python directory
cd /d "%~dp0"

REM Check if requirements.txt exists
if not exist "requirements.txt" (
    echo [ERROR] requirements.txt not found!
    echo Please ensure requirements.txt is in the python directory
    pause
    exit /b 1
)

REM Install dependencies
echo.
echo Installing Python dependencies...
echo This may take a few minutes...
echo.

python -m pip install -r requirements.txt
if %errorlevel% neq 0 (
    echo.
    echo [ERROR] Failed to install dependencies!
    pause
    exit /b 1
)

echo.
echo Dependencies installed successfully!

REM Run dependency checker
echo.
echo ============================================================
echo Verifying Installation
echo ============================================================
echo.

python check_dependencies.py
if %errorlevel% neq 0 (
    echo.
    echo ============================================================
    echo [WARNING] Setup incomplete - some tests failed
    echo ============================================================
    echo.
    echo Please review the errors above and fix them before proceeding.
    pause
    exit /b 1
)

echo.
echo ============================================================
echo Setup Complete!
echo ============================================================
echo.
echo Next steps:
echo 1. Add PYTHON_PATH to Laravel .env file:
echo    PYTHON_PATH=C:\Path\To\Python\python.exe
echo.
echo 2. Add OpenAI API key to .env:
echo    OPENAI_API_KEY=your-api-key-here
echo.
echo 3. Clear Laravel config cache:
echo    php artisan config:clear
echo.
echo 4. Test with a submission!
echo.
pause
