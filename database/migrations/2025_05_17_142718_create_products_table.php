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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('seller_user_id')->constrained('users')->onDelete('restrict'); // Restrict deletion
        $table->string('name', 150);
        $table->text('description');
        $table->string('color', 50)->nullable();
        $table->integer('stock_quantity')->default(0);
        $table->decimal('price', 12, 2);
        $table->string('main_image_url')->nullable();
        $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
        $table->boolean('is_available')->default(true);
        $table->timestamps();
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
