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
    Schema::create('favorites', function (Blueprint $table) {
        // Composite primary key as per schema (user_id, target_type, target_id)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('target_type', 30); // e.g., 'TouristSite', 'Product', 'Article', 'Hotel'
        $table->unsignedBigInteger('target_id'); // ID of the favorited item
        $table->timestamp('added_at')->useCurrent();

        // Define the composite primary key
        $table->primary(['user_id', 'target_type', 'target_id']);

        // Note: No foreign key constraint for target_id here due to polymorphic nature
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
