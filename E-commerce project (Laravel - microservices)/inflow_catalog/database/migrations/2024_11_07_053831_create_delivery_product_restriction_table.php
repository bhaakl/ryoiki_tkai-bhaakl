<?php

use App\Models\Delivery;
use App\Models\ProductRestriction;
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
        Schema::create('delivery_product_restriction', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Delivery::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ProductRestriction::class)->constrained()->cascadeOnDelete();
            $table->boolean('all_day')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_product_restriction');
    }
};
