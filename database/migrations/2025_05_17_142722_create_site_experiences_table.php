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
    Schema::create('site_experiences', function (Blueprint $table) {
        $table->id('experience_id');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('site_id')->constrained('tourist_sites')->onDelete('cascade');
        $table->string('title', 200)->nullable();
        $table->text('content');
        $table->string('photo_url')->nullable();
        $table->date('visit_date')->nullable();
        $table->timestamps();
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_experiences');
    }
};
