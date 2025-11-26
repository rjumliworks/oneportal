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
        Schema::create('events', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('code',30)->unique()->index();
            $table->text('title');
            $table->longText('purpose');
            $table->string('remarks')->nullable();
            $table->boolean('is_host')->default(true);
            $table->unsignedSmallInteger('audience_id');
            $table->foreign('audience_id')->references('id')->on('list_data');
            $table->unsignedSmallInteger('mode_id');
            $table->foreign('mode_id')->references('id')->on('list_data')->onDelete('cascade');
            $table->unsignedSmallInteger('type_id');
            $table->foreign('type_id')->references('id')->on('list_data')->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
