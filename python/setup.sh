#!/bin/bash

# TajTrainer Python Analyzer Setup Script
# This script sets up the Python environment for the Tajweed audio analyzer

set -e  # Exit on error

echo "============================================================"
echo "TajTrainer Python Analyzer Setup"
echo "============================================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Python is installed
echo -n "Checking Python installation... "
if command -v python3 &> /dev/null; then
    PYTHON_VERSION=$(python3 --version 2>&1 | awk '{print $2}')
    echo -e "${GREEN}✓${NC} Python $PYTHON_VERSION"
else
    echo -e "${RED}✗ Python 3 not found!${NC}"
    echo "Please install Python 3.8 or higher"
    exit 1
fi

# Check Python version
echo -n "Checking Python version... "
PYTHON_MAJOR=$(python3 -c 'import sys; print(sys.version_info.major)')
PYTHON_MINOR=$(python3 -c 'import sys; print(sys.version_info.minor)')

if [ "$PYTHON_MAJOR" -ge 3 ] && [ "$PYTHON_MINOR" -ge 8 ]; then
    echo -e "${GREEN}✓${NC} Python $PYTHON_MAJOR.$PYTHON_MINOR (>= 3.8)"
else
    echo -e "${RED}✗ Python $PYTHON_MAJOR.$PYTHON_MINOR is too old${NC}"
    echo "Please upgrade to Python 3.8 or higher"
    exit 1
fi

# Check if pip is installed
echo -n "Checking pip installation... "
if python3 -m pip --version &> /dev/null; then
    PIP_VERSION=$(python3 -m pip --version | awk '{print $2}')
    echo -e "${GREEN}✓${NC} pip $PIP_VERSION"
else
    echo -e "${RED}✗ pip not found!${NC}"
    echo "Please install pip"
    exit 1
fi

# Navigate to python directory
cd "$(dirname "$0")"

# Check if requirements.txt exists
if [ ! -f "requirements.txt" ]; then
    echo -e "${RED}✗ requirements.txt not found!${NC}"
    echo "Please ensure requirements.txt is in the python/ directory"
    exit 1
fi

# Install dependencies
echo ""
echo "Installing Python dependencies..."
echo "This may take a few minutes..."
echo ""

if python3 -m pip install -r requirements.txt; then
    echo ""
    echo -e "${GREEN}✓ Dependencies installed successfully!${NC}"
else
    echo ""
    echo -e "${RED}✗ Failed to install dependencies!${NC}"
    exit 1
fi

# Run dependency checker
echo ""
echo "============================================================"
echo "Verifying Installation"
echo "============================================================"
echo ""

if python3 check_dependencies.py; then
    echo ""
    echo -e "${GREEN}============================================================${NC}"
    echo -e "${GREEN}✓✓✓ Setup Complete! ✓✓✓${NC}"
    echo -e "${GREEN}============================================================${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Add PYTHON_PATH to Laravel .env file:"
    echo "   PYTHON_PATH=$(which python3)"
    echo ""
    echo "2. Add OpenAI API key to .env:"
    echo "   OPENAI_API_KEY=your-api-key-here"
    echo ""
    echo "3. Clear Laravel config cache:"
    echo "   php artisan config:clear"
    echo ""
    echo "4. Test with a submission!"
    echo ""
else
    echo ""
    echo -e "${RED}============================================================${NC}"
    echo -e "${RED}⚠ Setup incomplete - some tests failed${NC}"
    echo -e "${RED}============================================================${NC}"
    echo ""
    echo "Please review the errors above and fix them before proceeding."
    exit 1
fi
