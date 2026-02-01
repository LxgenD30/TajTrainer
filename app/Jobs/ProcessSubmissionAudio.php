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
            
            // Step 1: Transcribe audio using AssemblyAI if needed
            if ($submission->audio_file_path && config('services.assemblyai.api_key')) {
                if (empty($submission->transcription) || trim($submission->transcription) === '') {
                    try {
                        Log::info('Transcribing audio with AssemblyAI: ' . $submission->audio_file_path);
                        $transcription = $this->transcribeWithAssemblyAI($submission->audio_file_path);
                        $submission->transcription = $transcription;
                        $submission->save();
                        Log::info('✓ Transcription completed: ' . substr($transcription, 0, 100));
                    } catch (\Exception $e) {
                        Log::error('AssemblyAI transcription failed: ' . $e->getMessage());
                        // Keep status as 'submitted' - teacher can manually grade
                        return;
                    }
                }
            }
            
            // Step 2: Analyze Tajweed rules
            if ($submission->audio_file_path && $submission->transcription) {
                try {
                    Log::info('Starting Tajweed analysis for submission #' . $submission->id);
                    
                    $tajweedAnalysis = $this->analyzeTajweed(
                        $submission->audio_file_path,
                        $submission->transcription,
                        $assignment->surah,
                        $assignment->start_verse,
                        $assignment->end_verse
                    );
                    
                    $submission->tajweed_analysis = json_encode($tajweedAnalysis);
                    $submission->save();
                    
                    Log::info('✓ Tajweed analysis completed');
                    
                    // Log errors to database
                    $this->logTajweedErrors($submission->id, $tajweedAnalysis, 'assignment');
                    
                    // Step 3: Create score
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
                    
                } catch (\Exception $e) {
                    Log::error('Tajweed analysis failed: ' . $e->getMessage());
                    // Keep status as 'submitted' - teacher can manually grade
                    return;
                }
            }
            
            // Mark as graded (completed with score)
            $submission->status = 'graded';
            $submission->save();
            
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
        
        // Call Python analyzer
        $result = $this->callPythonAnalyzer($audioPath, $expectedText, $referenceAudio);
        
        // Add additional data
        $result['expected_text'] = $expectedText;
        $result['tajweed_text'] = $tajweedText;
        $result['reference_audio'] = $referenceAudio;
        $result['transcribed_text'] = $transcription;
        
        // Calculate text accuracy
        $textAccuracy = $this->calculateTextAccuracy($transcription, $expectedText);
        $result['text_accuracy'] = $textAccuracy;
        Log::info('Text accuracy: ' . number_format($textAccuracy, 2) . '%');
        
        return $result;
    }
    
    private function callPythonAnalyzer($audioPath, $expectedText, $referenceAudio)
    {
        $fullPath = storage_path('app/public/' . $audioPath);
        $pythonScript = base_path('python/tajweed_analyzer.py');
        $pythonExecutable = config('services.python.executable', '/usr/bin/python3');
        
        // Download reference audio
        $referenceUrl = $referenceAudio[0]['url'] ?? null;
        $referencePath = null;
        
        if ($referenceUrl) {
            $referencePath = $this->downloadReferenceAudio($referenceUrl);
            Log::info('Reference audio downloaded: ' . $referencePath);
        }
        
        // Build command
        $command = escapeshellarg($pythonExecutable) . ' ' . 
                   escapeshellarg($pythonScript) . ' ' . 
                   escapeshellarg($fullPath) . ' ' . 
                   escapeshellarg($expectedText);
        
        if ($referencePath) {
            $command .= ' --reference=' . escapeshellarg($referencePath);
        }
        
        Log::info('Python command: ' . $command);
        
        // Execute
        $output = [];
        $exitCode = 0;
        exec($command . ' 2>&1', $output, $exitCode);
        
        $outputStr = implode("\n", $output);
        Log::info('Python exit code: ' . $exitCode);
        Log::info('Python output length: ' . strlen($outputStr));
        
        if ($exitCode !== 0 && $exitCode !== -1) {
            Log::error('Python script failed with exit code: ' . $exitCode);
            Log::error('Output: ' . $outputStr);
        }
        
        // Parse JSON output
        $jsonOutput = $this->extractJsonFromOutput($outputStr);
        $result = json_decode($jsonOutput, true);
        
        if (!$result) {
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
    
    private function extractJsonFromOutput($output)
    {
        $lines = explode("\n", $output);
        $jsonLines = [];
        $inJson = false;
        $braceCount = 0;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            if (strpos($line, '{') !== false) {
                $inJson = true;
                $braceCount += substr_count($line, '{') - substr_count($line, '}');
                $jsonLines[] = $line;
            } elseif ($inJson) {
                $braceCount += substr_count($line, '{') - substr_count($line, '}');
                $jsonLines[] = $line;
                
                if ($braceCount <= 0) {
                    break;
                }
            }
        }
        
        return implode("\n", $jsonLines);
    }
    
    private function getQuranText($surah, $startVerse, $endVerse)
    {
        $verses = \App\Models\Material::getQuranVerses($surah, $startVerse, $endVerse);
        return implode("\n", array_column($verses, 'text'));
    }
    
    private function getTajweedFormattedText($surah, $startVerse, $endVerse)
    {
        $verses = \App\Models\Material::getQuranVerses($surah, $startVerse, $endVerse);
        return implode(' ', array_column($verses, 'tajweed_text'));
    }
    
    private function getReferenceAudio($surah, $startVerse, $endVerse)
    {
        return \App\Models\Material::getReferenceAudio($surah, $startVerse, $endVerse);
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
    
    private function logTajweedErrors($submissionId, $analysis, $type)
    {
        $maddErrors = 0;
        $noonErrors = 0;
        
        if (isset($analysis['madd_analysis']['issues'])) {
            foreach ($analysis['madd_analysis']['issues'] as $issue) {
                \App\Models\TajweedErrorLog::create([
                    'submission_id' => $submissionId,
                    'submission_type' => $type,
                    'rule_type' => 'madd',
                    'error_details' => json_encode($issue),
                    'timestamp' => now(),
                ]);
                $maddErrors++;
            }
        }
        
        if (isset($analysis['noon_sakin_analysis']['issues'])) {
            foreach ($analysis['noon_sakin_analysis']['issues'] as $issue) {
                \App\Models\TajweedErrorLog::create([
                    'submission_id' => $submissionId,
                    'submission_type' => $type,
                    'rule_type' => 'noon_sakin',
                    'error_details' => json_encode($issue),
                    'timestamp' => now(),
                ]);
                $noonErrors++;
            }
        }
        
        Log::info("Logged {$maddErrors} Madd errors and {$noonErrors} Noon Sakin errors");
    }
}
