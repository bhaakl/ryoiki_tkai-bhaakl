<?php

use App\Enums\PaymentIcons;
use App\Enums\PaymentSystems;
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
        Schema::create('payment_systems', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default(PaymentSystems::ONLINE);
            $table->string('name');
            $table->string('description');
            $table->string('icon')->default(PaymentIcons::CONTACTLESS);
            $table->float('platform_commission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_systems');
    }
};
