<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This method will drop the existing 'favorites' table (if it exists)
     * and then create a new one with the updated schema.
     */
    public function up(): void
    {
        // Drop the existing table if it exists
        // This ensures a clean slate before recreating the table.
        Schema::dropIfExists('favorites');

        // Create the new 'favorites' table with the specified schema
        Schema::create('favorites', function (Blueprint $table) {
            // Using a standard auto-incrementing primary key named 'id'
            $table->id();

            // Foreign key to the users table. If a user is deleted, their favorites are also deleted.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Columns for the polymorphic relationship
            $table->string('target_type'); // e.g., 'App\Models\TouristSite', or a shorter alias from morphMap
            $table->unsignedBigInteger('target_id');

            // Timestamp for when the favorite was added, defaults to the current time.
            $table->timestamp('added_at')->useCurrent();

            // Add a unique constraint to prevent duplicate favorites by the same user for the same item.
            $table->unique(['user_id', 'target_type', 'target_id']);

            // Add an index on the polymorphic columns for faster lookups.
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * This method will drop the 'favorites' table if this migration is rolled back.
     * Note: Rolling back will not restore the *previous* version of the table,
     * it will simply remove the table created by the 'up' method.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};