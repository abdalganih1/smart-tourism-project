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
    Schema::create('tourist_sites', function (Blueprint $table) {
        $table->id();
        $table->string('name', 150);
        $table->text('description');
        $table->string('location_text')->nullable();
        $table->decimal('latitude', 10, 8)->nullable();
        $table->decimal('longitude', 11, 8)->nullable();
        $table->string('city', 100)->nullable();
        $table->string('country', 100)->default('Syria');
        $table->foreignId('category_id')->nullable()->constrained('site_categories')->onDelete('set null');
        $table->string('main_image_url')->nullable();
        $table->string('video_url')->nullable();
        $table->foreignId('added_by_user_id')->nullable()->constrained('users')->onDelete('set null');
        $table->timestamps();
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourist_sites');
    }
};
