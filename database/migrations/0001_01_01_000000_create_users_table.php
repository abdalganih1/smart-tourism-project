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
        Schema::create('users', function (Blueprint $table) {
            // Laravel uses 'id' as the primary key by default, which is standard practice.
            // Let's keep it as 'id' (integer, auto-increment, primary key).
            // If you MUST use 'user_id', it's more complex and requires overriding Eloquent conventions.
            // Sticking to 'id' is recommended for standard Laravel functionality.
            $table->id(); // This creates an auto-incrementing unsigned BigInteger column named 'id'

            $table->string('username')->unique(); // Added username as unique string
            // Default Laravel uses 'password'. Rename it to password_hash as per your schema.
            $table->string('password');
            $table->string('email')->unique();
            $table->string('user_type')->default('Tourist'); // Added user_type with a default
            $table->boolean('is_active')->default(true); // Added is_active boolean

            // Optional: Keep if needed for email verification feature
            // $table->timestamp('email_verified_at')->nullable();
            // Optional: Keep if needed for "remember me" functionality
            // $table->rememberToken();

            $table->timestamps(); // This adds 'created_at' and 'updated_at'
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
