<?php

namespace App\Models;

use App\Models\User;
use App\Models\Exam;
use App\Models\AttemptAnswer;
use App\Models\AttemptViolation;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'started_at' ,
        'finished_at',
        'submitted_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(AttemptAnswer::class, 'attempt_id');
    }

    public function violations()
    {
        return $this->hasMany(AttemptViolation::class);
    }
}
