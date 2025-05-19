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
    Schema::create('site_categories', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100)->unique();
        $table->text('description')->nullable();
        // No parent_category_id in SiteCategories as per schema
        $table->timestamps();
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_categories');
    }
};
