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
    Schema::create('articles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('author_user_id')->nullable()->constrained('users')->onDelete('set null');
        $table->string('title', 250);
        $table->text('content');
        $table->text('excerpt')->nullable();
        $table->string('main_image_url')->nullable();
        $table->string('video_url')->nullable();
        $table->string('tags')->nullable(); // Consider JSON type or separate tags table for better querying
        $table->string('status', 20)->default('Draft');
        $table->timestamp('published_at')->nullable();
        $table->timestamps();
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
