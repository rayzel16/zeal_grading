<?php

namespace App\Models;

use App\Models\Answer;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
