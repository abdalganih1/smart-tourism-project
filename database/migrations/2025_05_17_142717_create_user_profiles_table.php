<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // ... uses statements ...
public function up(): void
{
    Schema::create('user_profiles', function (Blueprint $table) {
        // This is a one-to-one relationship where the PK is also the FK
        // Reference the 'id' column in the 'users' table
        $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade'); // user_id is PK and FK

        $table->string('first_name');
        $table->string('last_name');
        $table->string('father_name')->nullable();
        $table->string('mother_name')->nullable();
        $table->string('passport_image_url')->nullable();
        $table->text('bio')->nullable();
        $table->string('profile_picture_url')->nullable();
        // created_at is implicitly handled by Users table's creation.
        // You might want updated_at explicitly here as it relates to the profile data itself.
        $table->timestamps(); // Explicit updated_at
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
