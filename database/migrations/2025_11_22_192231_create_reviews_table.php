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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('users');
            $table->foreignId('restaurante_id')->constrained('users');
            $table->foreignId('repartidor_id')->nullable()->constrained('users');
            $table->integer('rating_restaurante');
            $table->text('comentario_restaurante')->nullable();
            $table->integer('rating_repartidor')->nullable();
            $table->text('comentario_repartidor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
