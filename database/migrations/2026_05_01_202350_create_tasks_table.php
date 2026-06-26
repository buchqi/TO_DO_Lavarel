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
        // The tasks table stores the main business object of the application.
        // Controllers use the Task model to insert and query rows from here.
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            // user_id connects each task to its creator in the users table.
            // cascadeOnDelete means if a user is deleted, their tasks are removed too,
            // preventing orphan tasks that no longer have an owner.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            // The enum limits status to known values so the database cannot
            // store random strings such as "almost done".
            $table->enum('status', ['pending', 'done'])->default('pending');
            $table->date('deadline');
            // Only the file path is stored in the database. The actual uploaded
            // file lives in Laravel storage.
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The down method reverses the migration so development databases can
        // roll back schema changes safely.
        Schema::dropIfExists('tasks');
    }
};
