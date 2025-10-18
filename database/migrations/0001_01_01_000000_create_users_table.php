<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 120)->unique();
            $table->string('password'); 
            $table->string('full_name', 120);
            $table->string('role', 20);
            $table->string('restaurant_address', 200)->nullable();
            $table->string('cuisine_type', 50)->nullable();
            $table->string('contact_phone', 15)->nullable();
            $table->string('vehicle_type', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
