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
    Schema::create('product_orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
        $table->timestamp('order_date')->useCurrent();
        $table->decimal('total_amount', 14, 2);
        $table->string('order_status', 30)->default('Pending');
        $table->string('shipping_address_line1')->nullable();
        $table->string('shipping_address_line2')->nullable();
        $table->string('shipping_city', 100)->nullable();
        $table->string('shipping_postal_code', 20)->nullable();
        $table->string('shipping_country', 100)->nullable();
        $table->string('payment_transaction_id', 100)->nullable();
        $table->timestamps();
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_orders');
    }
};
