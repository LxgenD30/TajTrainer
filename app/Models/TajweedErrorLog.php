<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TajweedErrorLog extends Model
{
    protected $table = 'tajweed_error_logs';
    
    protected $fillable = [
        'practice_session_id',
        'assignment_submission_id',
        'error_type',
        'rule_name',
        'timestamp_in_audio',
        'severity',
        'was_correct',
        'issue_description',
        'recommendation',
    ];
    
    protected $casts = [
        'timestamp_in_audio' => 'decimal:2',
        'was_correct' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the practice session if this error is from practice
     */
    public function practiceSession(): BelongsTo
    {
        return $this->belongsTo(PracticeSession::class, 'practice_session_id');
    }
    
    /**
     * Get the assignment submission if this error is from an assignment
     */
    public function assignmentSubmission(): BelongsTo
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }
    
    /**
     * Get the student through the session
     */
    public function getStudentAttribute()
    {
        if ($this->assignment_submission_id) {
            return $this->assignmentSubmission?->student;
        } elseif ($this->practice_session_id) {
            return $this->practiceSession?->student;
        }
        return null;
    }
    
    /**
     * Scope for errors only (not correct entries)
     */
    public function scopeErrors($query)
    {
        return $query->where('was_correct', false);
    }
    
    /**
     * Scope for correct entries only
     */
    public function scopeCorrect($query)
    {
        return $query->where('was_correct', true);
    }
    
    /**
     * Scope for specific error type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('error_type', $type);
    }
    
    /**
     * Scope for specific rule
     */
    public function scopeOfRule($query, $rule)
    {
        return $query->where('rule_name', $rule);
    }
    
    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
