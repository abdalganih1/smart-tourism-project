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
    Schema::create('hotel_rooms', function (Blueprint $table) {
        $table->id();
        $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
        $table->foreignId('room_type_id')->constrained('hotel_room_types')->onDelete('restrict');
        $table->string('room_number', 20);
        $table->decimal('price_per_night', 12, 2);
        $table->decimal('area_sqm', 6, 2)->nullable();
        $table->integer('max_occupancy')->nullable()->default(1);
        $table->text('description')->nullable();
        $table->boolean('is_available_for_booking')->default(true);
        $table->timestamps();

        // Add unique constraint for room number within a hotel
        $table->unique(['hotel_id', 'room_number']);
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
