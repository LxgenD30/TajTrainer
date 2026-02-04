<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialItem extends Model
{
    protected $primaryKey = 'item_id';
    
    protected $fillable = [
        'material_id',
        'type',
        'path',
        'title',
        'description',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    /**
     * Get the material that owns this item.
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }
}
