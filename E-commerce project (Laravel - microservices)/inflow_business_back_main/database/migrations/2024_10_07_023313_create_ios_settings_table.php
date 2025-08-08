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
        Schema::create('ios_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('user_agreement_url')->nullable();
            $table->string('support_url')->nullable();
            $table->text('description')->nullable();
            $table->text('key_words')->nullable();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('copyright')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ios_settings');
    }
};
