<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->enum('trait', [
                'openness',
                'conscientiousness',
                'extraversion',
                'agreeableness',
                'neuroticism',
            ]);
            $table->text('question_text');
            $table->boolean('is_reverse')->default(false);
            $table->boolean('allow_note')->default(false);
            $table->integer('order_number')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
