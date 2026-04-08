<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionResponse extends Model
{
    protected $fillable = [
        'user_id',
        'question_id',
        'answer_id',
        'answer_text',
        'score',
        'feedback',
        'attempt_id'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}