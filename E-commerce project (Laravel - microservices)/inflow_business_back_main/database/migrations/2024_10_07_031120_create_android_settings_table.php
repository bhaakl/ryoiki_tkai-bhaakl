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
        Schema::create('android_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('user_agreement_url')->nullable();
            $table->string('user_delete_form_url')->nullable();
            $table->string('default_language')->default(\App\Enums\Languages::RU);
            $table->string('app_category')->default(\App\Enums\AppCategories::ESTORE);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('android_settings');
    }
};
