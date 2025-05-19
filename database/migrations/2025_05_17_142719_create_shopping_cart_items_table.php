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
    Schema::create('shopping_cart_items', function (Blueprint $table) {
        $table->id('cart_item_id'); // Using auto-increment PK as per schema
        // Alternative: $table->primary(['user_id', 'product_id']); for composite PK

        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        $table->integer('quantity')->default(1);
        $table->timestamp('added_at')->useCurrent();

        // If using composite PK, remove $table->id('cart_item_id'); and add:
        // $table->unique(['user_id', 'product_id']); // Ensure uniqueness if using auto-increment PK
    });
}
// ... down() ...
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_cart_items');
    }
};
