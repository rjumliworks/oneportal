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
        Schema::create('procurement_bac_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('bac_resolution_id')->unsigned()->index();
            $table->foreign('bac_resolution_id')->references('id')->on('procurement_bacs');
            $table->integer('procurement_item_id')->unsigned()->index();;
            $table->foreign('procurement_item_id')->references('id')->on('procurement_items');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_bac_items');
    }
};
