<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Groups represent collaboration spaces. A group can own shared tasks
        // and has one user who is responsible for managing it.
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            // owner_id points to users.id and identifies who controls group
            // settings and membership. cascadeOnDelete removes owned groups
            // when the owner account is deleted.
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Dropping groups rolls back the collaboration feature's main table.
        Schema::dropIfExists('groups');
    }
};
