<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PracticeSession extends Model
{
    protected $table = 'practice_sessions';
    
    protected $fillable = [
        'student_id',
        'surah_number',
        'ayah_number',
        'audio_path',
        'accuracy_score',
    ];
    
    protected $casts = [
        'surah_number' => 'integer',
        'ayah_number' => 'integer',
        'accuracy_score' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the student that owns the practice session
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    
    /**
     * Get the tajweed error logs for this session
     */
    public function tajweedErrorLogs(): HasMany
    {
        return $this->hasMany(TajweedErrorLog::class, 'session_id')
            ->where('session_type', 'practice');
    }
}
