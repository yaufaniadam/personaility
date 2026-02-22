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
        Schema::table('psychologists', function (Blueprint $table) {
            $table->char('province_code', 2)->nullable()->after('province');
            $table->char('city_code', 4)->nullable()->after('city');
            $table->string('gender')->nullable()->after('name'); // 'male' or 'female'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('psychologists', function (Blueprint $table) {
            $table->dropColumn(['province_code', 'city_code', 'gender']);
        });
    }
};
