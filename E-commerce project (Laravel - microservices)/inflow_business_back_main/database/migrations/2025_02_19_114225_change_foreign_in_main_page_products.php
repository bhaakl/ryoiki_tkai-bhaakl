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
        Schema::table('main_page_products', function (Blueprint $table) {
            $table->dropForeign(['main_page_block_id']);

            $table->foreign('main_page_block_id')->references('id')->on('main_page_blocks')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('main_page_products', function (Blueprint $table) {
            $table->dropForeign(['main_page_block_id']);

            $table->foreign('main_page_block_id')->references('id')->on('main_page_blocks');
        });
    }
};
