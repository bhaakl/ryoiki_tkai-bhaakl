<?php

use App\Models\Product;
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
        Schema::create('product_restrictions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->boolean('total')->default(false)->comment('Ограничение распространяется на все типы доставки и интервалы в указанную дату');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_restrictions');
    }
};
