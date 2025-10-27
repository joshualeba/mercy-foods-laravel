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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id(); // ID único del pedido
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade'); // Quién hizo el pedido
            $table->foreignId('restaurante_id')->constrained('users')->onDelete('cascade'); // A qué restaurante se le pidió
            $table->foreignId('repartidor_id')->nullable()->constrained('users')->onDelete('set null'); // Qué repartidor lo entregará
            $table->decimal('total', 8, 2); // El costo total del pedido
            $table->string('estado')->default('pendiente'); // pendiente, en_preparacion, listo_para_recoger, en_camino, entregado, cancelado
            $table->text('direccion_entrega'); // Dónde se entregará
            $table->timestamps(); // Fecha de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
