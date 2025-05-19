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
    Schema::create('product_order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained('product_orders')->onDelete('cascade');
        $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
        $table->integer('quantity');
        $table->decimal('price_at_purchase', 12, 2);
        // No timestamps based on schema, but could add if needed for auditing
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_order_items');
    }
};
