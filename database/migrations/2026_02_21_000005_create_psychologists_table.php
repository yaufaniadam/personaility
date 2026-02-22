<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychologists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('str_number');
            $table->string('sip_number')->nullable();
            $table->string('city');
            $table->string('province');
            $table->string('specialization');
            $table->string('contact_phone')->nullable();
            $table->string('contact_whatsapp')->nullable();
            $table->string('website')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('verified_status')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychologists');
    }
};
