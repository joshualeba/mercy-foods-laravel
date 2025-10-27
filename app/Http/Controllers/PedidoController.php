<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    /**
     * Muestra la lista de pedidos para el restaurante autenticado.
     */
    public function index()
    {
        $restaurante = Auth::user();

        // Obtenemos los pedidos y los separamos por estado
        $pedidosNuevos = $restaurante->pedidosRestaurante()->where('estado', 'pendiente')->with('cliente')->latest()->get();
        $pedidosEnPreparacion = $restaurante->pedidosRestaurante()->where('estado', 'en_preparacion')->with('cliente')->latest()->get();
        $pedidosListos = $restaurante->pedidosRestaurante()->where('estado', 'listo_para_recoger')->with('cliente')->latest()->get();

        return view('restaurante.pedidos', compact('pedidosNuevos', 'pedidosEnPreparacion', 'pedidosListos'));
    }

    /**
     * Actualiza el estado de un pedido específico.
     */
    public function actualizarEstado(Request $request, Pedido $pedido)
    {
        // Verificamos que el pedido pertenezca al restaurante
        if ($pedido->restaurante_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'estado' => 'required|string|in:en_preparacion,listo_para_recoger,entregado,cancelado',
        ]);

        $pedido->estado = $request->estado;
        $pedido->save();

        return response()->json(['message' => 'Estado del pedido actualizado con éxito.']);
    }
}