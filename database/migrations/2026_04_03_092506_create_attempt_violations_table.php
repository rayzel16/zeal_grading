<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attempt_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->text('data')->nullable(); // screenshot path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempt_violations');
    }
};
