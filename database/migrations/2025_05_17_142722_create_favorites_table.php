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
    Schema::create('favorites', function (Blueprint $table) {
        $table->id(); // OR a composite primary key
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('target_type');
        $table->unsignedBigInteger('target_id');
        $table->timestamp('added_at')->useCurrent(); // ** هل هذا السطر موجود هكذا؟ **

        // Laravel's default timestamps (optional but good practice)
        // $table->timestamps(); // This adds created_at and updated_at
    });
}
// ... down() ...

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
