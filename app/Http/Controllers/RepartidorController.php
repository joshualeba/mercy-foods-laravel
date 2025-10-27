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

        $pedidosPorRecoger = Pedido::where('repartidor_id', $repartidor->id)
                                    ->where('estado', 'en_camino')
                                    ->with(['restaurante', 'cliente'])
                                    ->latest()
                                    ->get();

        return view('repartidor.pedidos', compact('pedidosPorRecoger'));
    }
}