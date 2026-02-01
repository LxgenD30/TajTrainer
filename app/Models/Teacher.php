<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    public $incrementing = false;
    
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
