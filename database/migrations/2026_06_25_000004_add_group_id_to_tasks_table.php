<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration extends existing tasks so a task can optionally belong
        // to a group. Null means the task remains personal.
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')
                ->nullable()
                ->after('user_id');
        });
    }

    public function down(): void
    {
        // Removing group_id rolls back shared-task support from the tasks table.
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('group_id');
        });
    }
};
