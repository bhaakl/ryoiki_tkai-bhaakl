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
        Schema::create('delivery_intervals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->cascadeOnDelete();
            $table->string('interval');
            $table->time('time_from');
            $table->time('time_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_intervals');
    }
};
