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
    Schema::create('hotel_bookings', function (Blueprint $table) {
        $table->id('booking_id');
        $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
        $table->foreignId('room_id')->constrained('hotel_rooms')->onDelete('restrict');
        $table->date('check_in_date');
        $table->date('check_out_date');
        $table->integer('num_adults')->default(1);
        $table->integer('num_children')->default(0);
        $table->decimal('total_amount', 14, 2);
        $table->string('booking_status', 30)->default('PendingConfirmation');
        $table->string('payment_status', 30)->default('Unpaid');
        $table->string('payment_transaction_id', 100)->nullable();
        $table->timestamp('booked_at')->useCurrent();
        $table->text('special_requests')->nullable();
        $table->timestamps();
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
    }
};
