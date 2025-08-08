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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('article')->nullable();
            $table->string('ext_id')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->string('title');
            $table->mediumText('description')->nullable();
            $table->integer('price')->nullable();
            $table->integer('promo_price')->nullable();
            $table->integer('discount')->nullable();
            $table->integer('bonus')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('active')->default(true);
            $table->boolean('new')->default(false);
            $table->boolean('preorderable')->default(false);
            $table->boolean('popular')->default(false);
            $table->boolean('special')->default(false);
            $table->boolean('extra')->default(false);
            $table->boolean('bonus_multiplier')->default(false);
            $table->boolean('by_order')->default(false);
            $table->boolean('has_package')->default(false);
            $table->boolean('searchable')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
