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
        'url',
        'category',
        'teacher_id',
    ];

    protected $casts = [
        'type' => 'string',
        'is_public' => 'boolean',
    ];

    protected $attributes = [
        'is_public' => true,
    ];

    /**
     * Get the teacher who created this material.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'material_id', 'material_id');
    }

    /**
     * Get all items (files, links, videos) for this material.
     */
    public function items()
    {
        return $this->hasMany(MaterialItem::class, 'material_id', 'material_id');
    }
}
