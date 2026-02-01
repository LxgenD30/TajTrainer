<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $primaryKey = 'score_id';
    
    protected $fillable = [
        'user_id',
        'assignment_id',
        'score',
        'feedback',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id', 'assignment_id');
    }
}
