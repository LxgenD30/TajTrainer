<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $primaryKey = 'assignment_id';
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'assignment_id';
    }
    
    protected $fillable = [
        'material_id',
        'class_id',
        'surah',
        'start_verse',
        'end_verse',
        'due_date',
        'instructions',
        'total_marks',
        'is_voice_submission',
        'tajweed_rules',
        'expected_recitation',
        'reference_audio_url',
    ];

    protected $casts = [
        'is_voice_submission' => 'boolean',
        'due_date' => 'datetime',
        'tajweed_rules' => 'array',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }
}
