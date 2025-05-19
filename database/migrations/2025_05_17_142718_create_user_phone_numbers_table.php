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
    Schema::create('user_phone_numbers', function (Blueprint $table) {
        $table->id('phone_id'); // Specify PK name if not 'id'
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // FK to users table
        $table->string('phone_number');
        $table->boolean('is_primary')->default(false);
        $table->string('description')->nullable();
        // created_at is implicitly needed here
        $table->timestamps(); // Adds created_at and updated_at
    });
}
// ... down() ...
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_phone_numbers');
    }
};
