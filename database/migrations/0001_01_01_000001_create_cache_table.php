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
        // The cache table stores cached values when the database cache driver is used.
        // Laravel features can use this to avoid recalculating expensive data.
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration')->index();
        });

        // Cache locks let Laravel coordinate work so two processes do not run
        // the same protected operation at the same time.
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback removes both cached values and cache lock records.
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
