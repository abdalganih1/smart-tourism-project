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
    Schema::create('comments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('target_type', 30); // e.g., 'Article', 'Product', 'TouristSite', 'Hotel', 'SiteExperience'
        $table->unsignedBigInteger('target_id'); // ID of the commented item
        // Self-referencing foreign key for threaded comments
        $table->foreignId('parent_comment_id')->nullable()->constrained('comments')->onDelete('cascade');
        $table->text('content');
        $table->timestamps();

        // Note: No foreign key constraint for target_id here due to polymorphic nature
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
