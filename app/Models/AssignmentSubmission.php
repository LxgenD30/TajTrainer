<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'text_submission',
        'audio_file_path',
        'transcription',
        'tajweed_analysis',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'tajweed_analysis' => 'array',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id', 'assignment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    /**
     * Get the score for this submission
     * Note: Due to composite key (user_id, assignment_id), we can't use a simple relationship
     * Use the getScoreAttribute() accessor below instead
     */
    public function scoreRelation(): HasOne
    {
        return $this->hasOne(Score::class, 'assignment_id', 'assignment_id');
    }
    
    /**
     * Accessor to get the score with proper composite key matching
     */
    public function getScoreAttribute()
    {
        if (!isset($this->attributes['student_id']) || !isset($this->attributes['assignment_id'])) {
            return null;
        }
        
        return Score::where('user_id', $this->attributes['student_id'])
                    ->where('assignment_id', $this->attributes['assignment_id'])
                    ->first();
    }

    /**
     * Get the tajweed error logs for this submission
     */
    public function tajweedErrorLogs(): HasMany
    {
        return $this->hasMany(TajweedErrorLog::class, 'assignment_submission_id');
    }

    /**
     * Get the overall tajweed score (percentage) from the stored analysis.
     * Note: 'tajweed_score' is NOT a real DB column in the active schema,
     * it is derived from the tajweed_analysis JSON (overall_score.score).
     */
    public function getTajweedScoreAttribute()
    {
        $analysis = $this->tajweed_analysis;

        if (is_array($analysis) && isset($analysis['overall_score']['score'])) {
            return round((float) $analysis['overall_score']['score'], 1);
        }

        return null;
    }

    /**
     * Get the overall tajweed grade from the stored analysis.
     * Derived from the tajweed_analysis JSON (overall_score.grade).
     */
    public function getTajweedGradeAttribute()
    {
        $analysis = $this->tajweed_analysis;

        if (is_array($analysis) && isset($analysis['overall_score']['grade'])) {
            return $analysis['overall_score']['grade'];
        }

        return null;
    }

    /**
     * Get the transcribed recitation text.
     * Falls back to the tajweed_analysis JSON when the column is empty
     * (e.g. Whisper transcription stored only inside the analysis payload).
     */
    public function getTranscriptionAttribute($value)
    {
        if (!empty(trim((string) $value))) {
            return $value;
        }

        $analysis = $this->tajweed_analysis;

        if (is_array($analysis)) {
            foreach (['whisper_transcription', 'transcribed_text', 'whisper_transcription_raw'] as $key) {
                if (isset($analysis[$key]) && !empty(trim((string) $analysis[$key]))) {
                    return $analysis[$key];
                }
            }
        }

        return $value;
    }
}
