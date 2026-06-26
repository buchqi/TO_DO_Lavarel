<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tags are reusable labels such as Exam or Assignment.
        // They are separated into their own table so many tasks can share
        // the same tag without duplicating text everywhere.
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Dropping the table removes tag records when this migration is rolled back.
        Schema::dropIfExists('tags');
    }
};
