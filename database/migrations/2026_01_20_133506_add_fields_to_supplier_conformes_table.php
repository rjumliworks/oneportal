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
        Schema::table('supplier_conformes', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_conformes', 'position')) {
                $table->string('position')->nullable();
            }
            if (!Schema::hasColumn('supplier_conformes', 'is_active')) {
                $table->boolean('is_active')->default(1);
            }
            if (!Schema::hasColumn('supplier_conformes', 'user_id')) {
                $table->tinyInteger('user_id')->unsigned()->index()->nullable();
                $table->foreign('user_id')->references('id')->on('list_units');
            }
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_conformes', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'user_id']);
        });
    }
};
