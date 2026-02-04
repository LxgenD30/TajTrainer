#!/bin/bash
# Deploy script for production server
# Run this on your production server: bash deploy_fix.sh

echo "================================================"
echo "Deploying edit.blade.php fix to production"
echo "================================================"

cd /home/tajweedf/tajtrainer

echo "1. Pulling latest code from GitHub..."
git pull origin main

echo "2. Clearing compiled views cache..."
php artisan view:clear

echo "3. Clearing application cache..."
php artisan cache:clear

echo "4. Clearing config cache..."
php artisan config:clear

echo "5. Clearing route cache..."
php artisan route:clear

echo "================================================"
echo "Deployment complete! Try accessing Materials page now."
echo "================================================"
