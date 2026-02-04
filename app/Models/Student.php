<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'integer';
    
    protected $fillable = [
        'id',
        'name',
        'biodata',
        'current_level',
        'phone_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'enrollment', 'user_id', 'class_id')
                    ->withPivot('date_joined')
                    ->withTimestamps();
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'user_id');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id', 'id');
    }
}
