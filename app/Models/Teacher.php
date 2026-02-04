<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'integer';
    
    protected $fillable = [
        'id',
        'name',
        'biodata',
        'title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }
}
