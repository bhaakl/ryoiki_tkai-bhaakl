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
        Schema::create('delivery_restriction_interval', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_restriction_id')->constrained('delivery_restrictions', 'id')->cascadeOnDelete();
            $table->foreignId('delivery_interval_id')->constrained('delivery_intervals', 'id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_restriction_interval');
    }
};
