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
    Schema::create('tourist_activities', function (Blueprint $table) {
        $table->id();
        $table->string('name', 200);
        $table->text('description')->nullable();
        $table->foreignId('site_id')->nullable()->constrained('tourist_sites')->onDelete('cascade'); // Cascade deletion
        $table->string('location_text')->nullable();
        $table->timestamp('start_datetime');
        $table->integer('duration_minutes')->nullable();
        $table->foreignId('organizer_user_id')->nullable()->constrained('users')->onDelete('set null');
        $table->decimal('price', 10, 2)->nullable()->default(0.00);
        $table->integer('max_participants')->nullable();
        $table->timestamps();
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourist_activities');
    }
};
