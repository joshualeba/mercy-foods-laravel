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
        Schema::create('restaurant_details', function (Blueprint $table) {
            $table->id();
            // Esta línea crea la conexión con la tabla 'users'
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('attention_schedule', 255)->nullable(); // Aquí guardaremos el horario
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_details');
    }
};