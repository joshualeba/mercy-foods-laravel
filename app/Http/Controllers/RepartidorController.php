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

        // Lógica para verificar si el perfil está completo
        $profileComplete = !empty($user->contact_phone) && !empty($user->vehicle_type);
        $profileStatus = $profileComplete ? 'Completo' : 'Incompleto';

        // Calcula estadísticas reales
        $entregasHoy = Pedido::where('repartidor_id', $user->id)
                            ->where('estado', 'entregado')
                            ->whereDate('updated_at', today())
                            ->count();
        
        $gananciasHoy = Pedido::where('repartidor_id', $user->id)
                    ->where('estado', 'entregado')
                    ->whereDate('updated_at', today())
                    ->sum('costo_envio');
        
        $pedidosPendientes = Pedido::where('repartidor_id', $user->id)
                                ->whereIn('estado', ['en_camino', 'recogido'])
                                ->count();

        // Obtener calificación promedio y total de reseñas
        $averageRating = round($user->average_rating_repartidor, 1);
        $totalReviews = $user->total_reviews_repartidor;

        return view('repartidor-dashboard', compact(
            'entregasHoy', 
            'gananciasHoy', 
            'pedidosPendientes', 
            'profileStatus',
            'averageRating',
            'totalReviews'
        ));
    }

    // Dentro del método verPedidos, busca esta sección y modifícala
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

        // NUEVO: Pedidos que el repartidor ya recogió y están listos para entregar
        $pedidosRecogidos = Pedido::where('repartidor_id', $repartidor->id)
                                    ->where('estado', 'recogido')
                                    ->with(['restaurante', 'cliente'])
                                    ->latest()
                                    ->get();

        // Pedidos que el repartidor ya entregó
        $pedidosEntregados = Pedido::where('repartidor_id', $repartidor->id)
                                    ->where('estado', 'entregado')
                                    ->with(['restaurante', 'cliente'])
                                    ->latest()
                                    ->get();

        return view('repartidor.pedidos', compact('pedidosDisponibles', 'pedidosPorRecoger', 'pedidosRecogidos', 'pedidosEntregados'));
    }

    // Modifica el método marcarRecogido para que el estado sea 'recogido'
    public function marcarRecogido(Request $request, Pedido $pedido)
    {
        if ($pedido->repartidor_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $pedido->estado = 'recogido'; // <-- CAMBIO IMPORTANTE
        $pedido->save();

        return response()->json(['message' => '¡Pedido marcado como recogido!']);
    }

    // AGREGA este nuevo método para marcar como entregado
    public function marcarEntregado(Request $request, Pedido $pedido)
    {
        if ($pedido->repartidor_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $pedido->estado = 'entregado';
        $pedido->save();

        return response()->json(['message' => '¡Pedido entregado! El cliente ha sido notificado.']);
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
}