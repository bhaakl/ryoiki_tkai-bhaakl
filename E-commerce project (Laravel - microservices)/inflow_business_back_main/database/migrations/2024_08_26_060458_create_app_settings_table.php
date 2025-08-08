<?php

use App\Enums\AuthTypes;
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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('icon_name')->nullable();
            $table->string('state');
            $table->string('auth_type')->default(AuthTypes::Email);
            $table->boolean('two_factor')->default(false);
            $table->text('splash_screen_text')->nullable();
            $table->string('loyalty_type')->default(\App\Enums\LoyaltyTypes::NONE->value);
            $table->uuid('loyalty_uuid')->nullable();
            $table->boolean('has_delivery')->default(false);
            $table->boolean('has_pickup')->default(false);
            $table->boolean('has_services')->default(false);
            $table->boolean('has_market')->default(false);
            $table->boolean('has_favorite')->default(false);
            $table->string('primary')->default('#FFEF8078');
            $table->string('secondary')->default('#FFE17770');
            $table->string('background_1')->default('#FFFEF5F0');
            $table->string('background_2')->default('#FFFFEEE5');
            $table->string('icon')->default('#FFEF8078');
            $table->string('text')->default('#FFEF8078');
            $table->string('gradient_1')->default('#FFFB7547');
            $table->string('gradient_2')->default('#FFEF8078');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
