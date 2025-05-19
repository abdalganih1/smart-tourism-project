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
    Schema::create('product_categories', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100)->unique();
        $table->text('description')->nullable();
        // Self-referencing foreign key for hierarchy
        $table->foreignId('parent_category_id')->nullable()->constrained('product_categories')->onDelete('set null');
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
