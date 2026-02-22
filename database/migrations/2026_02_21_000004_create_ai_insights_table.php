<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_insights', function (Blueprint $table) {
            $table->id();
            // One-to-one with assessments – ERD: ASSESSMENTS ||--|| AI_INSIGHTS
            $table->foreignId('assessment_id')->unique()->constrained()->cascadeOnDelete();

            // AI-generated narrative fields
            $table->text('core_strength')->nullable();
            $table->text('blind_spot')->nullable();
            $table->text('growth_suggestion')->nullable();
            $table->text('stress_pattern')->nullable();

            // Raw AI interaction logs – stored for debugging/audit only
            $table->longText('raw_prompt')->nullable();
            $table->longText('raw_response')->nullable();
            $table->string('model_version')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};
