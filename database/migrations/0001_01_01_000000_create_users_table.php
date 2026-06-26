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
        // The users table stores accounts used by Laravel authentication.
        // LoginController and RegisterController depend on this table.
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Email is unique because Laravel uses it as the login identifier.
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            // The password column stores a hash, never the plain password.
            $table->string('password');
            // rememberToken supports Laravel's "remember me" authentication cookie.
            $table->rememberToken();
            $table->timestamps();
        });

        // Password reset tokens are stored separately so users can request a
        // temporary reset without changing their account row.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // The sessions table lets Laravel store logged-in browser sessions in
        // the database. Auth middleware reads this session data on each request.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // user_id is nullable because guests can also have sessions.
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop related auth/session tables when rolling back the base schema.
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
