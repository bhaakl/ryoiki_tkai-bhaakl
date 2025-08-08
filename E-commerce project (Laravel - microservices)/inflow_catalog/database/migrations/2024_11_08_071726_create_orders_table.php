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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('ext_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_phone')->nullable();
            $table->foreignId('status_id')->constrained('order_statuses')->restrictOnDelete();
            $table->string('status_code');
            $table->foreignId('prev_status_id')->nullable()->constrained('order_statuses')->restrictOnDelete();
            $table->string('prev_status_code')->nullable();
            $table->unsignedInteger('paid_with_bonus')->default(0);
            $table->boolean('paid')->default(false);
            $table->boolean('quick')->default(false);
            $table->json('delivery_info');
            $table->string('comment')->nullable();
            $table->string('courier_name')->nullable();
            $table->string('courier_phone')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
