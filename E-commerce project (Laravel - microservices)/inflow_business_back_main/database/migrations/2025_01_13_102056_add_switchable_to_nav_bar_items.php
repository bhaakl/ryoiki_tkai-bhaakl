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
        Schema::table('nav_bar_items', function (Blueprint $table) {
            $table->boolean('switchable')->default(true);
            $table->string('icon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nav_bar_items', function (Blueprint $table) {
            $table->dropColumn('switchable', 'icon');
        });
    }
};
