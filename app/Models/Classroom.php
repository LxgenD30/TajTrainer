<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'teacher_id',
        'class_name',
        'description',
        'access_code',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'class_id', 'id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollment', 'class_id', 'user_id')
                    ->withPivot('date_joined')
                    ->withTimestamps();
    }
}
