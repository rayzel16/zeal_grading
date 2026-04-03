<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // Create Exam
        $exam = Exam::create([
            'title' => 'Basic Programming Quiz',
            'description' => 'Test your basic programming knowledge',
            'max_attempts' => 3,
            'code' => 'ABCD',
        ]);

        // Questions + Answers
        $questions = [
            [
                'question_text' => 'What does PHP stand for?',
                'answers' => [
                    ['text' => 'Personal Home Page', 'correct' => true],
                    ['text' => 'Private Home Page', 'correct' => false],
                    ['text' => 'Public Hypertext Processor', 'correct' => false],
                    ['text' => 'Programming Home Page', 'correct' => false],
                ]
            ],
            [
                'question_text' => 'Which symbol is used for variables in PHP?',
                'answers' => [
                    ['text' => '#', 'correct' => false],
                    ['text' => '$', 'correct' => true],
                    ['text' => '@', 'correct' => false],
                    ['text' => '%', 'correct' => false],
                ]
            ],
            [
                'question_text' => 'Which of the following is a PHP framework?',
                'answers' => [
                    ['text' => 'Laravel', 'correct' => true],
                    ['text' => 'Django', 'correct' => false],
                    ['text' => 'React', 'correct' => false],
                    ['text' => 'Vue', 'correct' => false],
                ]
            ],
        ];

        foreach ($questions as $q) {
            $question = Question::create([
                'exam_id' => $exam->id,
                'question_text' => $q['question_text'],
            ]);

            foreach ($q['answers'] as $a) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $a['text'],
                    'is_correct' => $a['correct'],
                ]);
            }
        }
    }
}