<?php

namespace App\Jobs;

use App\Models\AssignmentSubmission;
use App\Models\Assignment;
use App\Models\Score;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
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
                        $assignment->end_verse
                    );
                    
                    // Extract transcription from Python output
                    if (isset($tajweedAnalysis['whisper_transcription'])) {
                        $submission->transcription = $tajweedAnalysis['whisper_transcription'];
                        Log::info('✓ Whisper transcription: ' . substr($submission->transcription, 0, 100));
                    }
                    
                    // Store full Tajweed analysis
                    $submission->tajweed_analysis = json_encode($tajweedAnalysis);
                    $submission->save();
                    
                    Log::info('✓ Tajweed analysis completed');
                    
                    // Log errors to database
                    $this->logTajweedErrors($submission, $tajweedAnalysis, 'assignment');
                    
                    // Create score based on analysis
                    $overallScore = $tajweedAnalysis['overall_score']['score'] ?? 0;
                    $feedback = $tajweedAnalysis['overall_score']['feedback'] ?? 'Analysis completed.';
                    $scoreValue = round(($overallScore / 100) * $assignment->total_marks);
                    
                    Log::info('Creating score: ' . $scoreValue . '/' . $assignment->total_marks);
                    
                    Score::updateOrCreate(
                        [
                            'assignment_id' => $assignment->id,
                            'user_id' => $submission->student_id,
                        ],
                        [
                            'score' => $scoreValue,
                            'feedback' => $feedback,
                        ]
                    );
                    
                    Log::info('✓ Score created');
                    
                    // Mark as graded
                    $submission->status = 'graded';
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
    
    private function transcribeWithAssemblyAI($audioPath)
    {
        $apiKey = config('services.assemblyai.api_key');
        $fullPath = storage_path('app/public/' . $audioPath);
        
        if (!file_exists($fullPath)) {
            throw new \Exception('Audio file not found: ' . $audioPath);
        }

        // Step 1: Upload audio file
        $uploadUrl = $this->uploadAudioToAssemblyAI($fullPath, $apiKey);
        
        // Step 2: Request transcription
        $transcriptId = $this->requestTranscription($uploadUrl, $apiKey);
        
        // Step 3: Poll for result
        $transcription = $this->pollTranscription($transcriptId, $apiKey);
        
        return $transcription;
    }
    
    private function uploadAudioToAssemblyAI($filePath, $apiKey)
    {
        $audioData = file_get_contents($filePath);
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.assemblyai.com/v2/upload',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'authorization: ' . $apiKey,
                'Content-Type: application/octet-stream',
            ],
            CURLOPT_POSTFIELDS => $audioData,
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode !== 200) {
            throw new \Exception('AssemblyAI upload failed: ' . $response);
        }
        
        $result = json_decode($response, true);
        return $result['upload_url'];
    }
    
    private function requestTranscription($audioUrl, $apiKey)
    {
        $requestBody = json_encode([
            'audio_url' => $audioUrl,
            'language_code' => 'ar',
        ]);
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.assemblyai.com/v2/transcript',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'authorization: ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => $requestBody,
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode !== 200) {
            throw new \Exception('AssemblyAI transcription request failed: ' . $response);
        }
        
        $result = json_decode($response, true);
        return $result['id'];
    }
    
    private function pollTranscription($transcriptId, $apiKey)
    {
        $maxAttempts = 60; // 5 minutes
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.assemblyai.com/v2/transcript/' . $transcriptId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'authorization: ' . $apiKey,
                ],
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode !== 200) {
                throw new \Exception('AssemblyAI polling failed: ' . $response);
            }
            
            $result = json_decode($response, true);
            
            if ($result['status'] === 'completed') {
                return $result['text'] ?? '';
            } elseif ($result['status'] === 'error') {
                throw new \Exception('Transcription failed: ' . ($result['error'] ?? 'Unknown error'));
            }
            
            sleep(5);
            $attempt++;
        }
        
        throw new \Exception('Transcription timeout after 5 minutes');
    }
    
    private function analyzeTajweed($audioPath, $transcription, $surah, $startVerse, $endVerse)
    {
        // Get expected Quranic text
        $expectedText = $this->getQuranText($surah, $startVerse, $endVerse);
        $tajweedText = $this->getTajweedFormattedText($surah, $startVerse, $endVerse);
        $referenceAudio = $this->getReferenceAudio($surah, $startVerse, $endVerse);
        
        Log::info('Expected text: ' . substr($expectedText, 0, 50) . '...');
        
        // Call Python analyzer (it will do Whisper transcription internally)
        $result = $this->callPythonAnalyzer($audioPath, $expectedText, $referenceAudio);
        
        // Add additional data
        $result['expected_text'] = $expectedText;
        $result['tajweed_text'] = $tajweedText;
        $result['reference_audio'] = $referenceAudio;
        
        // Use transcription from Python (Whisper) if available
        $pythonTranscription = $result['whisper_transcription'] ?? $result['transcribed_text'] ?? '';
        $result['transcribed_text'] = $pythonTranscription;
        
        // Calculate text accuracy
        if (!empty($pythonTranscription)) {
            $textAccuracy = $this->calculateTextAccuracy($pythonTranscription, $expectedText);
            $result['text_accuracy'] = $textAccuracy;
            Log::info('Text accuracy: ' . number_format($textAccuracy, 2) . '%');
        }
        
        return $result;
    }
    
    private function callPythonAnalyzer($audioPath, $expectedText, $referenceAudio)
    {
        $fullPath = storage_path('app/public/' . $audioPath);
        $pythonScript = base_path('python/tajweed_analyzer.py');
        $pythonExecutable = $this->getPythonCommand();
        
        // Download reference audio
        $referenceUrl = $referenceAudio[0]['url'] ?? null;
        $referencePath = null;
        
        if ($referenceUrl) {
            $referencePath = $this->downloadReferenceAudio($referenceUrl);
            Log::info('Reference audio downloaded: ' . $referencePath);
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
        $verses = [];
        
        for ($verse = $startVerse; $verse <= $endVerse; $verse++) {
            $url = "https://api.alquran.cloud/v1/ayah/{$surahNumber}:{$verse}/quran-uthmani";
            
            try {
                $response = @file_get_contents($url);
                if ($response !== false) {
                    $data = json_decode($response, true);
                    if ($data['code'] == 200 && isset($data['data']['text'])) {
                        $verses[] = $data['data']['text'];
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error fetching Quran text: " . $e->getMessage());
            }
        }
        
        return implode("\n", $verses);
    }
    
    private function getTajweedFormattedText($surah, $startVerse, $endVerse)
    {
        $surahNumber = $this->getSurahNumber($surah);
        $verses = [];
        
        for ($verse = $startVerse; $verse <= $endVerse; $verse++) {
            $url = "https://api.alquran.cloud/v1/ayah/{$surahNumber}:{$verse}/quran-tajweed";
            
            try {
                $response = @file_get_contents($url);
                if ($response !== false) {
                    $data = json_decode($response, true);
                    if ($data['code'] == 200 && isset($data['data']['text'])) {
                        $verses[] = $data['data']['text'];
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error fetching tajweed text: " . $e->getMessage());
            }
        }
        
        return implode(' ۝ ', $verses);
    }
    
    private function getReferenceAudio($surah, $startVerse, $endVerse)
    {
        $surahNumber = $this->getSurahNumber($surah);
        $audioUrls = [];
        
        for ($verse = $startVerse; $verse <= $endVerse; $verse++) {
            $url = "https://api.alquran.cloud/v1/ayah/{$surahNumber}:{$verse}/ar.alafasy";
            
            try {
                $response = @file_get_contents($url);
                if ($response !== false) {
                    $data = json_decode($response, true);
                    if ($data['code'] == 200 && isset($data['data']['audio'])) {
                        $audioUrls[] = [
                            'verse' => "{$surahNumber}:{$verse}",
                            'url' => $data['data']['audio'],
                            'number' => $data['data']['number'] ?? $verse,
                            'text' => $data['data']['text'] ?? ''
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error fetching reference audio: " . $e->getMessage());
            }
        }
        
        return $audioUrls;
    }
    
    private function getSurahNumber($surahName)
    {
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
            'Al-Falaq' => 113, 'An-Naas' => 114
        ];
        
        return $surahs[$surahName] ?? 1;
    }
    
    private function calculateTextAccuracy($transcribed, $expected)
    {
        $transcribed = $this->normalizeArabicText($transcribed);
        $expected = $this->normalizeArabicText($expected);
        
        similar_text($transcribed, $expected, $percent);
        
        return round($percent, 2);
    }
    
    private function normalizeArabicText($text)
    {
        $text = preg_replace('/[\x{064B}-\x{065F}]/u', '', $text);
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
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
