<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Trait scores – average of Likert responses per trait
            $table->decimal('openness_score', 5, 2)->nullable();
            $table->decimal('conscientiousness_score', 5, 2)->nullable();
            $table->decimal('extraversion_score', 5, 2)->nullable();
            $table->decimal('agreeableness_score', 5, 2)->nullable();
            $table->decimal('neuroticism_score', 5, 2)->nullable();

            // Percentile ranks – nullable as per ERD
            $table->integer('openness_percentile')->nullable();
            $table->integer('conscientiousness_percentile')->nullable();
            $table->integer('extraversion_percentile')->nullable();
            $table->integer('agreeableness_percentile')->nullable();
            $table->integer('neuroticism_percentile')->nullable();

            // Tracks the question-set version used
            $table->string('version')->default('1.0');

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
