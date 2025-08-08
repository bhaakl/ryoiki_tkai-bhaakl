<?php

use App\Enums\DeliveryIcons;
use App\Enums\DeliveryTypes;
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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('type')->default(DeliveryTypes::DELIVERY);
            $table->string('icon')->nullable();
            $table->string('name')->nullable();
            $table->text('description');
            $table->integer('base_cost');
            $table->unsignedInteger('mkad_min')->default(0);
            $table->unsignedInteger('mkad_max')->default(0);
            $table->boolean('has_intervals')->default(false);
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
