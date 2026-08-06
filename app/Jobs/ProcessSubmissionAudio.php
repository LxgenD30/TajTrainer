<?php

namespace App\Jobs;

use App\Models\AssignmentSubmission;
use App\Models\Assignment;
use App\Models\Score;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessSubmissionAudio implements ShouldQueue
{
    use Queueable;

    public $submissionId;
    public $timeout = 600; // 10 minutes

    /**
     * Create a new job instance.
     */
    public function __construct($submissionId)
    {
        $this->submissionId = $submissionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("=== Processing Audio Job Started for Submission #{$this->submissionId} ===");
            
            $submission = AssignmentSubmission::findOrFail($this->submissionId);
            $assignment = Assignment::findOrFail($submission->assignment_id);
            
            Log::info('Submission audio path: ' . ($submission->audio_file_path ?? 'NONE'));
            Log::info('Assignment: ' . $assignment->surah . ' ' . $assignment->start_verse . '-' . ($assignment->end_verse ?? $assignment->start_verse));
            
            // Analyze with Python (handles both Whisper transcription AND Tajweed analysis in one call)
            if ($submission->audio_file_path) {
                try {
                    Log::info('Starting Python analysis (Whisper + Tajweed) for submission #' . $submission->id);
                    
                    // Python analyzer does BOTH transcription (Whisper) and Tajweed analysis
                    $tajweedAnalysis = $this->analyzeTajweed(
                        $submission->audio_file_path,
                        '', // No pre-transcription needed, Python does it
                        $assignment->surah,
                        $assignment->start_verse,
                        $assignment->end_verse,
                        $assignment->reference_audio_url, // Pass stored reference audio path
                        $assignment->expected_recitation    // Pass stored expected text (fetched at creation)
                    );
                    
                    // Extract transcription from Python output
                    $pythonTranscription = $tajweedAnalysis['whisper_transcription']
                        ?? $tajweedAnalysis['whisper_transcription_raw']
                        ?? $tajweedAnalysis['transcribed_text']
                        ?? null;

                    if ($pythonTranscription !== null && trim((string) $pythonTranscription) !== '') {
                        $submission->transcription = trim((string) $pythonTranscription);
                        Log::info('✓ Whisper transcription: ' . substr($submission->transcription, 0, 100));
                    } else {
                        Log::warning('No whisper transcription returned from Python analysis for submission #' . $submission->id);
                    }
                    
                    // Store full Tajweed analysis
                    $submission->tajweed_analysis = json_encode($tajweedAnalysis);
                    $submission->save();
                    
                    Log::info('✓ Tajweed analysis completed');
                    
                    // Log errors to database
                    $this->logTajweedErrors($submission, $tajweedAnalysis, 'assignment');
                    
                    // Create score based on analysis
                    $overallScore = $tajweedAnalysis['overall_score']['score'] ?? 0;
                    
                    // Use AI feedback if available, otherwise use overall_score feedback
                    if (isset($tajweedAnalysis['ai_feedback']['summary'])) {
                        $feedback = $tajweedAnalysis['ai_feedback']['summary'];
                        
                        // Append improvements if available
                        if (isset($tajweedAnalysis['ai_feedback']['improvements']) && 
                            is_array($tajweedAnalysis['ai_feedback']['improvements']) && 
                            count($tajweedAnalysis['ai_feedback']['improvements']) > 0) {
                            $feedback .= "\n\nAreas for Improvement:\n";
                            foreach ($tajweedAnalysis['ai_feedback']['improvements'] as $improvement) {
                                if (is_array($improvement)) {
                                    $issue = $improvement['issue'] ?? '';
                                    $suggestion = $improvement['suggestion'] ?? '';
                                    if ($issue !== '' || $suggestion !== '') {
                                        $feedback .= "• " . trim($issue . ': ' . $suggestion, ': ') . "\n";
                                    }
                                } elseif (is_string($improvement) && trim($improvement) !== '') {
                                    $feedback .= "• " . trim($improvement) . "\n";
                                }
                            }
                        }
                        
                        // Append next steps if available
                        if (isset($tajweedAnalysis['ai_feedback']['next_steps'])) {
                            $feedback .= "\nNext Steps: " . $tajweedAnalysis['ai_feedback']['next_steps'];
                        }
                    } else {
                        $feedback = $tajweedAnalysis['overall_score']['feedback'] ?? 'Analysis completed.';
                    }
                    
                    $scoreValue = round(($overallScore / 100) * $assignment->total_marks);
                    
                    Log::info('Creating score: ' . $scoreValue . '/' . $assignment->total_marks);
                    
                    Score::updateOrCreate(
                        [
                            'assignment_id' => $assignment->assignment_id,
                            'user_id' => $submission->student_id,
                        ],
                        [
                            'score' => $scoreValue,
                            'feedback' => $feedback,
                        ]
                    );
                    
                    Log::info('✓ Score created');
                    
                    // Mark as pending review (teacher must manually review and finalize)
                    $submission->status = 'pending_review';
                    $submission->save();
                    
                } catch (\Exception $e) {
                    Log::error('Python analysis failed: ' . $e->getMessage());
                    Log::error('Stack trace: ' . $e->getTraceAsString());
                    // Keep status as 'submitted' - teacher can manually grade
                    return;
                }
            } else {
                Log::warning('No audio file path for submission #' . $submission->id . ' - skipping analysis');
            }
            
            Log::info("=== Processing Audio Job Completed for Submission #{$this->submissionId} ===");
            
        } catch (\Exception $e) {
            Log::error("=== Processing Audio Job FAILED for Submission #{$this->submissionId} ===");
            Log::error('Error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            // Keep status as 'submitted' so teacher can manually grade if needed
        }
    }
    
    private function analyzeTajweed($audioPath, $transcription, $surah, $startVerse, $endVerse, $referenceAudioPath = null, $storedExpectedRecitation = null)
    {
        // Prefer a fresh clean fetch (whole words, ۝-separated) so accuracy
        // matching is reliable; fall back to the stored expected recitation.
        $expectedText = $this->getQuranText($surah, $startVerse, $endVerse);
        if ($expectedText === '') {
            $expectedText = $this->storedToPlainText($storedExpectedRecitation);
        }
        $tajweedText = $this->getTajweedFormattedText($surah, $startVerse, $endVerse);
        
        Log::info('Expected text: ' . substr($expectedText, 0, 50) . '...');
        Log::info('Reference audio path: ' . ($referenceAudioPath ?? 'NONE'));
        
        // Call Python analyzer (it will do Whisper transcription internally)
        $result = $this->callPythonAnalyzer($audioPath, $expectedText, $referenceAudioPath, $tajweedText);
        
        // Add additional data
        $result['expected_text'] = $expectedText;
        $result['tajweed_text'] = $tajweedText;
        $result['reference_audio'] = $referenceAudioPath;
        
        // Use transcription from Python (Whisper) if available.
        // Prefer marker-enriched transcription for display while preserving raw fallback.
        $pythonTranscription = $result['whisper_transcription']
            ?? $result['whisper_transcription_raw']
            ?? $result['transcribed_text']
            ?? '';

        $result['transcribed_text'] = $pythonTranscription;
        $result['transcribed_text_raw'] = $result['whisper_transcription_raw'] ?? $pythonTranscription;
        
        // Calculate text accuracy (marker-safe normalization).
        if (!empty($pythonTranscription)) {
            $textAccuracy = $result['overall_score']['word_accuracy']
                ?? $this->calculateTextAccuracy($pythonTranscription, $expectedText);
            $result['text_accuracy'] = $textAccuracy;
            Log::info('Text accuracy: ' . number_format($textAccuracy, 2) . '%');
        }
        
        return $result;
    }
    
    /**
     * Convert stored tajweed HTML (expected_recitation) into plain Arabic text
     * for the analyzer, stripping tags and ayah-end marker digits.
     */
    private function storedToPlainText($html)
    {
        if (empty($html)) {
            return '';
        }

        $plain = preg_replace('/<[^>]+>/u', ' ', $html);
        $plain = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove isolated ayah-end marker digits (e.g. ٥, ١٧٣). Keep ۝ as the
        // ayah separator so the Python analyzer can align ayah boundaries.
        $plain = preg_replace('/(?<=\s)[\x{0660}-\x{0669}0-9]+(?=\s|$)/u', ' ', $plain);

        return trim(preg_replace('/\s+/u', ' ', $plain));
    }

    private function callPythonAnalyzer($audioPath, $expectedText, $referenceAudioPath, $tajweedHtml = '')
    {
        $fullPath = storage_path('app/public/' . $audioPath);
        $pythonScript = base_path('python/tajweed_analyzer.py');
        $pythonExecutable = $this->getPythonCommand();
        
        // Get reference audio path
        $referencePath = null;
        if ($referenceAudioPath) {
            // If it's a storage path (starts with 'references/'), convert to full path
            if (str_starts_with($referenceAudioPath, 'references/')) {
                $referencePath = storage_path('app/public/' . $referenceAudioPath);
                Log::info('Using stored reference audio: ' . $referencePath);
            } 
            // If it's a URL (old data), download it
            elseif (str_starts_with($referenceAudioPath, 'http')) {
                $referencePath = $this->downloadReferenceAudio($referenceAudioPath);
                Log::info('Downloaded reference audio from URL: ' . $referencePath);
            }
        }
        
        // Build command with OpenAI API key in environment
        $openaiKey = config('services.openai.api_key');
        $envVars = '';
        if ($openaiKey) {
            $envVars = 'OPENAI_API_KEY=' . escapeshellarg($openaiKey) . ' ';
        }
        
        $command = $envVars . escapeshellarg($pythonExecutable) . ' ' . 
                   escapeshellarg($pythonScript) . ' ' . 
                   escapeshellarg($fullPath) . ' ' . 
                   escapeshellarg($expectedText);
        
        if ($referencePath) {
            $command .= ' --reference=' . escapeshellarg($referencePath);
        }

        // Pass tajweed-colored HTML so Python can detect rules from the markup.
        if (!empty($tajweedHtml)) {
            $command .= ' --tajweed=' . escapeshellarg($tajweedHtml);
        }
        
        Log::info('Python command: ' . $command);
        Log::info('OpenAI API key configured: ' . ($openaiKey ? 'Yes' : 'No'));
        
        // Execute using proc_open for better control (consistent with practice page)
        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];
        
        $process = proc_open($command, $descriptorspec, $pipes);
        $outputStr = '';
        
        if (is_resource($process)) {
            fclose($pipes[0]);
            $outputStr = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);
            
            if (!empty($errors)) {
                Log::warning('Python stderr: ' . $errors);
            }
            Log::info('Python exit code: ' . $exitCode);
            Log::info('Python output length: ' . strlen($outputStr));
            
            if ($exitCode !== 0 && $exitCode !== -1) {
                Log::error('Python script failed with exit code: ' . $exitCode);
                Log::error('Output: ' . $outputStr);
            }
        } else {
            Log::error('Failed to execute Python process');
            throw new \Exception('Failed to execute Python analyzer');
        }
        
        // Parse JSON output
        $jsonOutput = $this->extractJsonFromOutput($outputStr);
        $result = json_decode($jsonOutput, true);
        
        if (!$result) {
            Log::error('Failed to parse JSON. Raw Python output:');
            Log::error($outputStr);
            Log::error('Extracted JSON attempt:');
            Log::error($jsonOutput);
            throw new \Exception('Failed to parse Python output as JSON');
        }
        
        return $result;
    }
    
    private function downloadReferenceAudio($url)
    {
        $hash = md5($url);
        $filename = 'ref_' . $hash . '.mp3';
        $dir = storage_path('app/temp_reference_audio');
        
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $path = $dir . '/' . $filename;
        
        if (file_exists($path)) {
            return $path;
        }
        
        $audioData = file_get_contents($url);
        file_put_contents($path, $audioData);
        
        return $path;
    }
    
    /**
     * Get Python command with proper path resolution
     * Same logic as StudentController for consistency
     */
    private function getPythonCommand()
    {
        // Check for Python path from environment variable (hosting)
        $pythonPath = env('PYTHON_PATH', '');
        
        if ($pythonPath && file_exists($pythonPath)) {
            return $pythonPath;  // No quotes - escapeshellarg() will handle it
        }
        
        // Try common Python paths for different environments
        $possiblePaths = [
            'C:\\Users\\moham\\AppData\\Local\\Microsoft\\WindowsApps\\PythonSoftwareFoundation.Python.3.13_qbz5n2kfra8p0\\python.exe', // Windows local
            '/usr/bin/python3',  // Linux
            '/usr/local/bin/python3',  // Linux/Mac
            'python3',  // System PATH
            'python',  // Fallback
        ];
        
        foreach ($possiblePaths as $path) {
            // For system commands (no path separator), just return them
            if (strpos($path, '/') === false && strpos($path, '\\') === false) {
                return $path;
            }
            
            if (file_exists($path)) {
                return $path;  // No quotes - escapeshellarg() will handle it
            }
        }
        
        // Ultimate fallback
        return 'python3';
    }
    
    private function extractJsonFromOutput($output)
    {
        // Find the LAST complete JSON object (the final analysis result)
        // Python outputs multiple JSON status messages, we want the final one
        
        $lines = explode("\n", $output);
        $allJsonObjects = [];
        $currentJson = [];
        $braceCount = 0;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Start of a new JSON object
            if (strpos($line, '{') !== false && $braceCount === 0) {
                $currentJson = [$line];
                $braceCount = substr_count($line, '{') - substr_count($line, '}');
                
                // Single line JSON
                if ($braceCount === 0) {
                    $allJsonObjects[] = $line;
                }
            } elseif ($braceCount > 0) {
                // Continue multi-line JSON
                $currentJson[] = $line;
                $braceCount += substr_count($line, '{') - substr_count($line, '}');
                
                // Complete multi-line JSON
                if ($braceCount === 0) {
                    $allJsonObjects[] = implode("\n", $currentJson);
                    $currentJson = [];
                }
            }
        }
        
        // Return the LAST JSON object (the final analysis result)
        // Earlier objects are status messages
        if (empty($allJsonObjects)) {
            return '{}';
        }
        
        // Find the largest/most complete JSON (final result is usually longest)
        $lastJson = end($allJsonObjects);
        
        // Verify it's the analysis result by checking for expected keys
        foreach (array_reverse($allJsonObjects) as $jsonStr) {
            if (strpos($jsonStr, '"audio_file"') !== false || 
                strpos($jsonStr, '"overall_score"') !== false) {
                return $jsonStr;
            }
        }
        
        return $lastJson;
    }
    
    private function getQuranText($surah, $startVerse, $endVerse)
    {
        $surahNumber = $this->getSurahNumber($surah);

        if (!$surahNumber) {
            Log::warning('Unable to resolve surah number for expected text fetch', ['surah' => $surah]);
            return '';
        }

        $verseData = $this->fetchQuranVerseRange($surahNumber, (int) $startVerse, (int) $endVerse);

        return $verseData['arabic_plain'] ?? '';
    }
    
    private function getTajweedFormattedText($surah, $startVerse, $endVerse)
    {
        $surahNumber = $this->getSurahNumber($surah);

        if (!$surahNumber) {
            return '';
        }

        $verseData = $this->fetchQuranVerseRange($surahNumber, (int) $startVerse, (int) $endVerse);

        return $verseData['arabic_html'] ?? '';
    }

    private function fetchQuranVerseRange($surahNumber, $startVerse, $endVerse = null)
    {
        $endVerse = $endVerse ?? $startVerse;

        try {
            $chapterResponse = Http::timeout(15)->get(
                "https://api.qurancdn.com/api/qdc/chapters/{$surahNumber}",
                ['language' => 'en']
            );

            $versesResponse = Http::timeout(15)->get(
                "https://api.qurancdn.com/api/qdc/verses/by_chapter/{$surahNumber}",
                [
                    'translations' => 131,
                    'per_page' => 300,
                    'page' => 1,
                    'fields' => 'text_uthmani,text_uthmani_tajweed',
                ]
            );

            if ($chapterResponse->failed() || $versesResponse->failed()) {
                Log::warning("Qurancdn verse API failed for surah {$surahNumber}", [
                    'chapter_status' => $chapterResponse->status(),
                    'verses_status' => $versesResponse->status(),
                ]);

                return null;
            }

            $verses = $versesResponse->json('verses') ?? [];

            if (empty($verses)) {
                return null;
            }

            $selectedVerses = [];
            foreach ($verses as $verse) {
                $verseKey = $verse['verse_key'] ?? '';
                $verseParts = explode(':', $verseKey);
                $verseNumber = (int) ($verseParts[1] ?? 0);

                if ($verseNumber >= $startVerse && $verseNumber <= $endVerse) {
                    $selectedVerses[] = $verse;
                }
            }

            if (empty($selectedVerses)) {
                return null;
            }

            $arabicHtml = [];
            $arabicPlain = [];

            foreach ($selectedVerses as $verse) {
                $tajweedText = $verse['text_uthmani_tajweed'] ?? $verse['text_uthmani'] ?? '';
                // Use the clean Uthmani text so words stay whole for accuracy matching.
                $plainText = trim($verse['text_uthmani'] ?? strip_tags($tajweedText));

                if ($tajweedText !== '') {
                    $arabicHtml[] = $tajweedText;
                }

                if ($plainText !== '') {
                    $arabicPlain[] = $plainText;
                }
            }

            return [
                'surah_number' => (int) $surahNumber,
                'start_verse' => (int) $startVerse,
                'end_verse' => (int) $endVerse,
                'arabic_html' => implode(' ۝ ', $arabicHtml),
                'arabic_plain' => implode(' ۝ ', $arabicPlain),
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching Qurancdn verses in audio job: ' . $e->getMessage());

            return null;
        }
    }
    
    private function getSurahNumber($surahName)
    {
        if (is_numeric($surahName)) {
            $value = (int) $surahName;
            if ($value >= 1 && $value <= 114) {
                return $value;
            }
        }

        $surahName = trim((string) $surahName);
        if ($surahName === '') {
            return null;
        }

        if (preg_match('/^(\d{1,3})\b/', $surahName, $matches)) {
            $value = (int) $matches[1];
            if ($value >= 1 && $value <= 114) {
                return $value;
            }
        }

        $surahs = [
            'Al-Faatiha' => 1, 'Al-Baqara' => 2, 'Aal-i-Imraan' => 3, 'An-Nisaa' => 4,
            'Al-Maaida' => 5, 'Al-An\'aam' => 6, 'Al-A\'raaf' => 7, 'Al-Anfaal' => 8,
            'At-Tawba' => 9, 'Yunus' => 10, 'Hud' => 11, 'Yusuf' => 12,
            'Ar-Ra\'d' => 13, 'Ibrahim' => 14, 'Al-Hijr' => 15, 'An-Nahl' => 16,
            'Al-Israa' => 17, 'Al-Kahf' => 18, 'Maryam' => 19, 'Taa-Haa' => 20,
            'Al-Anbiyaa' => 21, 'Al-Hajj' => 22, 'Al-Muminoon' => 23, 'An-Noor' => 24,
            'Al-Furqaan' => 25, 'Ash-Shu\'araa' => 26, 'An-Naml' => 27, 'Al-Qasas' => 28,
            'Al-Ankaboot' => 29, 'Ar-Room' => 30, 'Luqman' => 31, 'As-Sajda' => 32,
            'Al-Ahzaab' => 33, 'Saba' => 34, 'Faatir' => 35, 'Yaseen' => 36,
            'As-Saaffaat' => 37, 'Saad' => 38, 'Az-Zumar' => 39, 'Ghafir' => 40,
            'Fussilat' => 41, 'Ash-Shura' => 42, 'Az-Zukhruf' => 43, 'Ad-Dukhaan' => 44,
            'Al-Jaathiya' => 45, 'Al-Ahqaf' => 46, 'Muhammad' => 47, 'Al-Fath' => 48,
            'Al-Hujuraat' => 49, 'Qaaf' => 50, 'Adh-Dhaariyat' => 51, 'At-Tur' => 52,
            'An-Najm' => 53, 'Al-Qamar' => 54, 'Ar-Rahmaan' => 55, 'Al-Waaqia' => 56,
            'Al-Hadid' => 57, 'Al-Mujaadila' => 58, 'Al-Hashr' => 59, 'Al-Mumtahana' => 60,
            'As-Saff' => 61, 'Al-Jumu\'a' => 62, 'Al-Munaafiqoon' => 63, 'At-Taghaabun' => 64,
            'At-Talaaq' => 65, 'At-Tahrim' => 66, 'Al-Mulk' => 67, 'Al-Qalam' => 68,
            'Al-Haaqqa' => 69, 'Al-Ma\'aarij' => 70, 'Nooh' => 71, 'Al-Jinn' => 72,
            'Al-Muzzammil' => 73, 'Al-Muddaththir' => 74, 'Al-Qiyaama' => 75, 'Al-Insaan' => 76,
            'Al-Mursalaat' => 77, 'An-Naba' => 78, 'An-Naazi\'aat' => 79, 'Abasa' => 80,
            'At-Takwir' => 81, 'Al-Infitaar' => 82, 'Al-Mutaffifin' => 83, 'Al-Inshiqaaq' => 84,
            'Al-Burooj' => 85, 'At-Taariq' => 86, 'Al-A\'laa' => 87, 'Al-Ghaashiya' => 88,
            'Al-Fajr' => 89, 'Al-Balad' => 90, 'Ash-Shams' => 91, 'Al-Lail' => 92,
            'Ad-Dhuhaa' => 93, 'Ash-Sharh' => 94, 'At-Tin' => 95, 'Al-Alaq' => 96,
            'Al-Qadr' => 97, 'Al-Bayyina' => 98, 'Az-Zalzala' => 99, 'Al-Aadiyaat' => 100,
            'Al-Qaari\'a' => 101, 'At-Takaathur' => 102, 'Al-Asr' => 103, 'Al-Humaza' => 104,
            'Al-Fil' => 105, 'Quraish' => 106, 'Al-Maa\'un' => 107, 'Al-Kawthar' => 108,
            'Al-Kaafiroon' => 109, 'An-Nasr' => 110, 'Al-Masad' => 111, 'Al-Ikhlaas' => 112,
            'Al-Falaq' => 113, 'An-Naas' => 114,
            'Az-Zalzalah' => 99, 'Al-Zalzalah' => 99, 'Az-Zalzala' => 99,
            'Al-Lahab' => 111,
            'Al-Fatiha' => 1, 'Al-Baqarah' => 2, 'Ali Imran' => 3, 'An-Nisa' => 4,
            'Al-Ma\'idah' => 5, 'Al-An\'am' => 6, 'Al-A\'raf' => 7, 'Al-Anfal' => 8,
            'Tawbah' => 9, 'Ya-Sin' => 36, 'Yasin' => 36, 'Qaf' => 50,
            'Rahman' => 55, 'Mulk' => 67, 'Ikhlas' => 112, 'Falaq' => 113, 'Nas' => 114,
            // QuranCDN name_simple aliases (the exact format stored on assignments)
            'Al-Fatihah' => 1, 'Ali \'Imran' => 3, 'At-Tawbah' => 9, 'Ar-Ra\'d' => 13,
            'Al-Isra' => 17, 'Taha' => 20, 'Al-Anbya' => 21, 'Al-Mu\'minun' => 23,
            'An-Nur' => 24, 'Al-Furqan' => 25, 'Ash-Shu\'ara' => 26, 'Al-\'Ankabut' => 29,
            'Ar-Rum' => 30, 'As-Sajdah' => 32, 'Al-Ahzab' => 33, 'As-Saffat' => 37,
            'Ash-Shuraa' => 42, 'Ad-Dukhan' => 44, 'Al-Jathiyah' => 45, 'Al-Ahqaf' => 46,
            'Al-Hujurat' => 49, 'Adh-Dhariyat' => 51, 'Ar-Rahman' => 55, 'Al-Waqi\'ah' => 56,
            'Al-Mujadila' => 58, 'Al-Mumtahanah' => 60, 'As-Saf' => 61, 'Al-Jumu\'ah' => 62,
            'Al-Munafiqun' => 63, 'At-Taghabun' => 64, 'At-Talaq' => 65, 'Al-Haqqah' => 69,
            'Al-Ma\'arij' => 70, 'Nuh' => 71, 'Al-Qiyamah' => 75, 'Al-Insan' => 76,
            'Al-Mursalat' => 77, 'An-Nazi\'at' => 79, '\'Abasa' => 80, 'Al-Infitar' => 82,
            'Al-Inshiqaq' => 84, 'Al-Buruj' => 85, 'At-Tariq' => 86, 'Al-A\'la' => 87,
            'Al-Ghashiyah' => 88, 'Al-Layl' => 92, 'Ad-Duhaa' => 93, 'Ash-Sharh' => 94,
            'Al-\'Alaq' => 96, 'Al-Bayyinah' => 98, 'Al-\'Adiyat' => 100, 'Al-Qari\'ah' => 101,
            'At-Takathur' => 102, 'Al-\'Asr' => 103, 'Al-Humazah' => 104, 'Quraysh' => 106,
            'Al-Ma\'un' => 107, 'Al-Kafirun' => 109, 'An-Nasr' => 110, 'An-Nas' => 114,
        ];

        if (isset($surahs[$surahName])) {
            return (int) $surahs[$surahName];
        }

        $normalize = static function (string $value): string {
            $value = strtolower($value);
            $value = preg_replace('/[^a-z0-9]+/', '', $value);
            return $value ?? '';
        };

        $normalizedInput = $normalize($surahName);
        if ($normalizedInput !== '') {
            foreach ($surahs as $name => $number) {
                if ($normalize((string) $name) === $normalizedInput) {
                    return (int) $number;
                }
            }
        }

        return null;
    }
    
    private function calculateTextAccuracy($transcribed, $expected)
    {
        $transcribed = $this->normalizeArabicText($transcribed);
        $expected = $this->normalizeArabicText($expected);

        $transWords = preg_split('/\s+/u', trim($transcribed));
        $expectedWords = preg_split('/\s+/u', trim($expected));

        if (empty($expectedWords) || empty($transWords)) {
            return 0.0;
        }

        // Lenient, order-independent word matching (mirrors the Python scorer).
        $used = array_fill(0, count($transWords), false);
        $matched = 0;

        foreach ($expectedWords as $expectedWord) {
            $best = 0;
            $bestIndex = -1;
            foreach ($transWords as $index => $transWord) {
                if ($used[$index]) {
                    continue;
                }
                similar_text($expectedWord, $transWord, $percent);
                if ($percent > $best) {
                    $best = $percent;
                    $bestIndex = $index;
                }
            }
            if ($bestIndex >= 0 && $best >= 60) {
                $used[$bestIndex] = true;
                $matched++;
            }
        }

        return round($matched / count($expectedWords) * 100, 2);
    }
    
    private function normalizeArabicText($text)
    {
        $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove legacy tajweed tags and HTML markup.
        $text = preg_replace('/<\|[^|]+\|>/u', ' ', $text);
        $text = preg_replace('/<[^>]+>/u', ' ', $text);

        // Keep pause marker for display only; exclude it from accuracy.
        $text = str_replace('۝', ' ', $text);

        // Remove diacritics and Qur'anic annotation marks.
        $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}]/u', '', $text);

        // Normalize Arabic letter variants.
        $text = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $text);
        $text = str_replace(['ى'], 'ي', $text);

        // Remove tatweel and punctuation so pauses/punctuations do not penalize score.
        $text = preg_replace('/[ـ]/u', '', $text);
        $text = preg_replace('/[^\p{Arabic}0-9\s]/u', ' ', $text);

        // Normalize common Muqatta'at compact forms into canonical spoken names.
        $text = $this->normalizeMuqattaatText($text);

        $text = preg_replace('/\s+/u', ' ', $text);
        
        return trim($text);
    }

    private function normalizeMuqattaatText(string $text): string
    {
        $sequences = [
            'الم', 'المص', 'الر', 'المر', 'كهيعص', 'طه', 'طسم', 'طس',
            'يس', 'ص', 'حم', 'عسق', 'حمعسق', 'ق', 'ن',
        ];

        $letterNames = [
            'ا' => 'الف',
            'ل' => 'لام',
            'م' => 'ميم',
            'ح' => 'حا',
            'ي' => 'يا',
            'ط' => 'طا',
            'س' => 'سين',
            'ك' => 'كاف',
            'ه' => 'ها',
            'ع' => 'عين',
            'ر' => 'را',
            'ص' => 'صاد',
            'ق' => 'قاف',
            'ن' => 'نون',
        ];

        foreach ($sequences as $sequence) {
            $letters = preg_split('//u', $sequence, -1, PREG_SPLIT_NO_EMPTY);
            $spoken = [];
            foreach ($letters as $letter) {
                if (isset($letterNames[$letter])) {
                    $spoken[] = $letterNames[$letter];
                }
            }

            if (empty($spoken)) {
                continue;
            }

            $phrase = implode(' ', $spoken);
            $compactPhrase = str_replace(' ', '', $phrase);

            $text = preg_replace('/(?<!\S)' . preg_quote($sequence, '/') . '(?!\S)/u', $phrase, $text);
            $text = preg_replace('/(?<!\S)' . preg_quote($compactPhrase, '/') . '(?!\S)/u', $phrase, $text);
        }

        return $text;
    }
    
    private function logTajweedErrors($submission, $analysis, $type)
    {
        $maddErrors = 0;
        $noonErrors = 0;
        
        if (isset($analysis['madd_analysis']['issues'])) {
            foreach ($analysis['madd_analysis']['issues'] as $issue) {
                \App\Models\TajweedErrorLog::create([
                    'assignment_submission_id' => $submission->id,
                    'error_type' => 'madd',
                    'rule_name' => 'Madd (Elongation)',
                    'timestamp_in_audio' => $issue['time'] ?? null,
                    'severity' => 'moderate',
                    'was_correct' => false,
                    'issue_description' => is_array($issue) ? json_encode($issue) : $issue,
                    'recommendation' => 'Practice elongating vowels for 2 counts',
                ]);
                $maddErrors++;
            }
        }
        
        if (isset($analysis['idgham_bila_ghunnah_analysis']['issues'])) {
            foreach ($analysis['idgham_bila_ghunnah_analysis']['issues'] as $issue) {
                \App\Models\TajweedErrorLog::create([
                    'assignment_submission_id' => $submission->id,
                    'error_type' => 'idgham_bila_ghunnah',
                    'rule_name' => 'Idgham Bila Ghunnah',
                    'timestamp_in_audio' => $issue['time'] ?? null,
                    'severity' => 'moderate',
                    'was_correct' => false,
                    'issue_description' => is_array($issue) ? json_encode($issue) : $issue,
                    'recommendation' => 'Focus on merging letters ر and ل without nasalization',
                ]);
                $noonErrors++;
            }
        }
        
        if (isset($analysis['idgham_bi_ghunnah_analysis']['issues'])) {
            foreach ($analysis['idgham_bi_ghunnah_analysis']['issues'] as $issue) {
                \App\Models\TajweedErrorLog::create([
                    'assignment_submission_id' => $submission->id,
                    'error_type' => 'idgham_bi_ghunnah',
                    'rule_name' => 'Idgham Bi Ghunnah',
                    'timestamp_in_audio' => $issue['time'] ?? null,
                    'severity' => 'moderate',
                    'was_correct' => false,
                    'issue_description' => is_array($issue) ? json_encode($issue) : $issue,
                    'recommendation' => 'Practice merging letters و م ن ي with nasalization',
                ]);
                $noonErrors++;
            }
        }
        
        Log::info("Logged {$maddErrors} Madd errors and {$noonErrors} Idgham errors");
    }
}
