<?php

use App\Models\DeliveryInterval;
use App\Models\DeliveryProductRestriction;
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
        Schema::create('delivery_product_restriction_interval', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dpr_id')->constrained('delivery_product_restriction', 'id')->cascadeOnDelete();
            $table->foreignId('di_id')->constrained('delivery_intervals', 'id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_product_restriction_interval');
    }
};
