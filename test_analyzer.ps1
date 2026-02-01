# Test Tajweed Analyzer

Write-Host "Testing Tajweed Analyzer..." -ForegroundColor Cyan

# Find a test audio file
$audioFile = Get-ChildItem -Path "C:\laragon\www\tajtrainerV2\storage\app\public\practice_recordings" -Filter "*.webm" | Select-Object -First 1

if ($audioFile) {
    Write-Host "Using audio file: $($audioFile.FullName)" -ForegroundColor Green
    
    # Test with sample Quranic text (Al-Fatiha verse 1)
    $sampleText = "بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ"
    
    Write-Host "Expected text: $sampleText" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Running analyzer..." -ForegroundColor Cyan
    
    # Run the analyzer
    python "C:\laragon\www\tajtrainerV2\python\tajweed_analyzer.py" "$($audioFile.FullName)" "$sampleText"
    
} else {
    Write-Host "No audio files found in practice_recordings folder" -ForegroundColor Red
    Write-Host "Please complete a practice session first" -ForegroundColor Yellow
}
