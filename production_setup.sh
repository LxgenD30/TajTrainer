#!/bin/bash

# TajTrainer Production Setup Script
# Run this on your Linux hosted server after pulling from GitHub

echo "═══════════════════════════════════════════════════════════════"
echo "          TAJTRAINER PRODUCTION SETUP                          "
echo "═══════════════════════════════════════════════════════════════"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_info() {
    echo -e "${YELLOW}ℹ${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Error: artisan file not found. Are you in the project root?"
    exit 1
fi

print_info "Starting production setup..."
echo ""

# Step 1: Create required directories
echo "【 CREATING DIRECTORIES 】"
echo "───────────────────────────────────────────"

mkdir -p storage/app/public/audio
mkdir -p storage/app/public/materials
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

print_success "Directories created"
echo ""

# Step 2: Set proper permissions
echo "【 SETTING PERMISSIONS 】"
echo "───────────────────────────────────────────"

chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 775 storage/logs
chmod -R 775 storage/framework

print_success "Permissions set"
echo ""

# Step 3: Install Python dependencies (if Python3 is available)
echo "【 PYTHON DEPENDENCIES 】"
echo "───────────────────────────────────────────"

if command -v python3 &> /dev/null; then
    print_info "Python3 found, installing dependencies..."
    
    # Try to install pip packages
    if command -v pip3 &> /dev/null; then
        pip3 install --user librosa soundfile praat-parselmouth openai fastdtw numpy scipy 2>&1 | grep -E "Successfully|already satisfied" || print_error "Some packages failed to install"
        print_success "Python dependencies installed"
    else
        print_error "pip3 not found, please install Python dependencies manually:"
        echo "  pip3 install librosa soundfile praat-parselmouth openai fastdtw numpy scipy"
    fi
else
    print_error "Python3 not found, please install it first"
fi
echo ""

# Step 4: Run Laravel setup commands
echo "【 LARAVEL SETUP 】"
echo "───────────────────────────────────────────"

# Clear all caches
php artisan cache:clear 2>&1 > /dev/null
print_success "Cache cleared"

php artisan config:clear 2>&1 > /dev/null
print_success "Config cleared"

php artisan route:clear 2>&1 > /dev/null
print_success "Routes cleared"

php artisan view:clear 2>&1 > /dev/null
print_success "Views cleared"

# Create storage link
php artisan storage:link 2>&1 > /dev/null
print_success "Storage linked"

# Run migrations (ask for confirmation)
echo ""
read -p "Run database migrations? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    print_success "Migrations completed"
else
    print_info "Skipping migrations"
fi

echo ""

# Cache for production
php artisan config:cache 2>&1 > /dev/null
print_success "Config cached"

php artisan route:cache 2>&1 > /dev/null
print_success "Routes cached"

php artisan view:cache 2>&1 > /dev/null
print_success "Views cached"

echo ""

# Step 5: Verify .env configuration
echo "【 ENVIRONMENT CHECK 】"
echo "───────────────────────────────────────────"

if [ -f ".env" ]; then
    print_success ".env file exists"
    
    # Check critical env variables
    if grep -q "APP_KEY=base64:" .env; then
        print_success "APP_KEY is set"
    else
        print_error "APP_KEY not set, run: php artisan key:generate"
    fi
    
    if grep -q "APP_DEBUG=false" .env; then
        print_success "APP_DEBUG is false (production mode)"
    else
        print_error "WARNING: APP_DEBUG should be false in production!"
    fi
    
    if grep -q "ASSEMBLYAI_API_KEY=" .env && ! grep -q "ASSEMBLYAI_API_KEY=$" .env; then
        print_success "AssemblyAI API key configured"
    else
        print_error "AssemblyAI API key not configured"
    fi
    
    if grep -q "OPENAI_API_KEY=" .env && ! grep -q "OPENAI_API_KEY=$" .env; then
        print_success "OpenAI API key configured"
    else
        print_error "OpenAI API key not configured"
    fi
else
    print_error ".env file not found!"
    echo "  Copy .env.example to .env and configure it"
fi

echo ""

# Step 6: Run system check
echo "【 SYSTEM HEALTH CHECK 】"
echo "───────────────────────────────────────────"
echo ""

if [ -f "comprehensive_system_check.php" ]; then
    php comprehensive_system_check.php
else
    print_error "comprehensive_system_check.php not found"
fi

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo "                    SETUP COMPLETE                              "
echo "═══════════════════════════════════════════════════════════════"
echo ""
print_info "Next steps:"
echo "  1. Make sure APP_DEBUG=false in .env for production"
echo "  2. Test the application in your browser"
echo "  3. Monitor logs: tail -f storage/logs/laravel.log"
echo ""
