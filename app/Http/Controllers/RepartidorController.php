<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pedido;

class RepartidorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Aquí puedes calcular las estadísticas reales
        $entregasHoy = 0; // Implementa la lógica real
        $gananciasHoy = 0; // Implementa la lógica real
        $pedidosPendientes = 0; // Implementa la lógica real
        
        return view('repartidor-dashboard', compact('entregasHoy', 'gananciasHoy', 'pedidosPendientes'));
    }

    public function verPedidos()
    {
        $repartidor = Auth::user();

        // Pedidos listos que ningún repartidor ha tomado
        $pedidosDisponibles = Pedido::where('estado', 'listo_para_recoger')
                                    ->whereNull('repartidor_id')
                                    ->with(['restaurante', 'cliente'])
                                    ->latest()
                                    ->get();

        // Pedidos que el repartidor actual ya aceptó y tiene que recoger/entregar
        $pedidosPorRecoger = Pedido::where('repartidor_id', $repartidor->id)
                                    ->where('estado', 'en_camino')
                                    ->with(['restaurante', 'cliente'])
                                    ->latest()
                                    ->get();

        return view('repartidor.pedidos', compact('pedidosDisponibles', 'pedidosPorRecoger'));
    }

    public function aceptarPedido(Request $request, Pedido $pedido)
    {
        // Verificamos que el pedido esté disponible
        if ($pedido->estado === 'listo_para_recoger' && is_null($pedido->repartidor_id)) {
            $pedido->repartidor_id = Auth::id();
            $pedido->estado = 'en_camino';
            $pedido->save();

            return response()->json(['message' => '¡Pedido aceptado! Dirígete a recogerlo.']);
        }

        return response()->json(['message' => 'Este pedido ya no está disponible.'], 409); // 409 Conflict
    }

    public function marcarRecogido(Request $request, Pedido $pedido)
    {
        // Verificamos que el pedido le pertenezca al repartidor autenticado
        if ($pedido->repartidor_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Cambiamos el estado a 'entregado' y guardamos
        $pedido->estado = 'entregado';
        $pedido->save();

        return response()->json(['message' => '¡Pedido marcado como recogido! El cliente ha sido notificado.']);
    }
}