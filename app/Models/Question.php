<?php

namespace App\Models;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\QuestionResponse;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'exam_id',
        'question_text',
        'type',
        'expected_answer'
    ];
    
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function responses()
    {
        return $this->hasMany(QuestionResponse::class);
    }
}
