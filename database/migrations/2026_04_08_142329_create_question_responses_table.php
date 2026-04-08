<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('question_responses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attempt_id')->constrained('attempts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();

            // MCQ
            $table->foreignId('answer_id')->nullable()->constrained()->nullOnDelete();

            // Essay / Identification
            $table->text('answer_text')->nullable();

            // Grading
            $table->float('score')->nullable();
            $table->text('feedback')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_responses');
    }
};
