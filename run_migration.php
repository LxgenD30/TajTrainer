<?php
/**
 * Quick migration runner for production
 * Run this file once to update the database schema
 */

// Run the migration
echo "Running migration to change reference_audio_url to TEXT...\n";
$output = shell_exec('php artisan migrate --path=database/migrations/2026_02_04_193303_change_reference_audio_url_to_text_in_assignments_table.php 2>&1');
echo $output;
echo "\nMigration complete!\n";
