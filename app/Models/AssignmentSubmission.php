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
}
