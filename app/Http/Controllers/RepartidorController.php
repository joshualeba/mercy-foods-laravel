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
        return view('repartidor-dashboard');
    }

    /**
     * Carga dinámicamente las secciones del dashboard.
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