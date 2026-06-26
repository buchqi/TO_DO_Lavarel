<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // task_tag is a pivot table. It exists because tasks and tags have a
        // many-to-many relationship: one task can have many tags, and one tag
        // can appear on many tasks.
        Schema::create('task_tag', function (Blueprint $table) {
            $table->id();
            // task_id points to tasks.id. cascadeOnDelete removes pivot rows
            // automatically when a task is deleted.
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            // tag_id points to tags.id. cascadeOnDelete prevents old pivot rows
            // from referencing a tag that no longer exists.
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // This unique constraint prevents assigning the same tag to the
            // same task more than once.
            $table->unique(['task_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        // Rolling back removes the pivot table before removing related tables.
        Schema::dropIfExists('task_tag');
    }
};
