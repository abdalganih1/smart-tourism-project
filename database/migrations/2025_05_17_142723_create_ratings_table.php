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
    Schema::create('ratings', function (Blueprint $table) {
        $table->id('rating_id');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('target_type', 30); // e.g., 'TouristSite', 'Product', 'Hotel', 'Article'
        $table->unsignedBigInteger('target_id'); // ID of the rated item
        $table->integer('rating_value'); // e.g., 1-5
        $table->string('review_title', 150)->nullable();
        $table->text('review_text')->nullable();
        $table->timestamps();

        // Add unique constraint for one rating per user per target item
        $table->unique(['user_id', 'target_type', 'target_id']);

        // Note: No foreign key constraint for target_id here due to polymorphic nature
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
