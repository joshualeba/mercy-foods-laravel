<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}