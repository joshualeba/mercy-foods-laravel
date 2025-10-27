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

    public function actualizarEstado(Request $request, Pedido $pedido)
    {
        // Verificamos que el pedido pertenezca al restaurante autenticado
        if ($pedido->restaurante_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'estado' => 'required|string|in:en_preparacion,listo_para_recoger',
        ]);

        $pedido->estado = $request->estado;
        $pedido->save();

        // Si el pedido está listo, buscamos un repartidor
        if ($request->estado === 'listo_para_recoger') {
            $this->asignarRepartidor($pedido);
        }

        return response()->json(['message' => 'Estado del pedido actualizado con éxito.']);
    }

    /**
     * Asigna el repartidor más cercano (simulado).
     */
    protected function asignarRepartidor(Pedido $pedido)
    {
        // Simulación: encontrar el primer repartidor disponible
        $repartidor = User::where('role', 'repartidor')->first();

        if ($repartidor) {
            $pedido->repartidor_id = $repartidor->id;
            $pedido->estado = 'en_camino'; // Cambiamos el estado a "en_camino"
            $pedido->save();
        }
    }

    /**
     * Muestra la lista de pedidos para el cliente autenticado.
     */
    public function verPedidosCliente()
    {
        $cliente = Auth::user();

        // Obtenemos los pedidos del cliente y los separamos por estado
        $pedidosEnPreparacion = $cliente->pedidosCliente()
                                        ->whereIn('estado', ['pendiente', 'en_preparacion'])
                                        ->with('restaurante')
                                        ->latest()
                                        ->get();
                                        
        $pedidosEnCamino = $cliente->pedidosCliente()
                                   ->whereIn('estado', ['listo_para_recoger', 'en_camino'])
                                   ->with('restaurante')
                                   ->latest()
                                   ->get();

        $pedidosEntregados = $cliente->pedidosCliente()
                                     ->where('estado', 'entregado')
                                     ->with('restaurante')
                                     ->latest()
                                     ->take(5) // Mostramos solo los últimos 5 entregados
                                     ->get();

        return view('cliente.pedidos', compact('pedidosEnPreparacion', 'pedidosEnCamino', 'pedidosEntregados'));
    }
}