<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // group_user is a pivot table for group membership.
        // It exists because a group can have many users and a user can join
        // many groups.
        Schema::create('group_user', function (Blueprint $table) {
            $table->id();
            // group_id points to groups.id. cascadeOnDelete cleans up membership
            // rows when a group is deleted.
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            // user_id points to users.id. cascadeOnDelete cleans up memberships
            // when an account is deleted.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // role is pivot data: it describes the user's role inside this
            // specific group membership.
            $table->string('role')->default('member')->nullable();
            $table->timestamps();

            // A user should not be attached to the same group twice.
            // This database rule supports the duplicate check in GroupController.
            $table->unique(['group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        // Rolling back removes membership records.
        Schema::dropIfExists('group_user');
    }
};
