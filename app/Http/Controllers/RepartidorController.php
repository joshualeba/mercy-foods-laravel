<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class RepartidorController extends Controller
{
    /**
     * Muestra el dashboard principal del repartidor.
     */
    public function dashboard()
    {
        // Datos de ejemplo para las estadísticas.
        // Más adelante, deberás obtener estos datos de tu base de datos.
        $entregasHoy = 8;
        $gananciasHoy = 550.75;
        $pedidosPendientes = 2;

        return view('repartidor-dashboard', compact(
            'entregasHoy',
            'gananciasHoy',
            'pedidosPendientes'
        ));
    }

    /**
     * Carga dinámicamente las secciones del dashboard (si es necesario en el futuro).
     */
    public function loadSection($section)
    {
        $viewPath = 'repartidor.' . $section;

        if (View::exists($viewPath)) {
            return view($viewPath);
        }

        return response()->json(['error' => 'La sección no fue encontrada.'], 404);
    }
}