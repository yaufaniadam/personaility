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
        Schema::table('ai_insights', function (Blueprint $table) {
            $table->tinyInteger('feedback_score')->nullable()->after('model_version');
            $table->text('feedback_comment')->nullable()->after('feedback_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_insights', function (Blueprint $table) {
            $table->dropColumn(['feedback_score', 'feedback_comment']);
        });
    }
};
