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
    Schema::create('hotels', function (Blueprint $table) {
        $table->id();
        $table->string('name', 150);
        $table->integer('star_rating')->nullable(); // Consider adding ->check('star_rating BETWEEN 1 AND 7') if your DB supports it
        $table->text('description')->nullable();
        $table->string('address_line1')->nullable();
        $table->string('city', 100)->nullable();
        $table->string('country', 100)->default('Syria');
        $table->decimal('latitude', 10, 8)->nullable();
        $table->decimal('longitude', 11, 8)->nullable();
        $table->string('contact_phone', 30)->nullable();
        $table->string('contact_email', 100)->nullable();
        $table->string('main_image_url')->nullable();
        $table->foreignId('managed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
        $table->timestamps();
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
