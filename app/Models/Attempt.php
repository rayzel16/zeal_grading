<?php

namespace App\Models;

use App\Models\User;
use App\Models\Exam;
use App\Models\AttemptAnswer;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
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
        return $this->hasMany(AttemptAnswer::class);
    }
}
