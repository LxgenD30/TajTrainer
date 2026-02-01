<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $primaryKey = 'material_id';
    
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'video_link',
        'thumbnail',
        'type',
        'is_public',
    ];

    protected $casts = [
        'type' => 'string',
        'is_public' => 'boolean',
    ];

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'material_id', 'material_id');
    }
}
