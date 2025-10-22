<?php

namespace App\Http-Controllers\Cliente;

use App\Http-Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Platillo;
use App\Models\User;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $tiposDeComida = Platillo::select('tipo_comida')
            ->whereNotNull('tipo_comida')
            ->distinct()
            ->orderBy('tipo_comida')
            ->pluck('tipo_comida');

        // Empezamos la consulta desde los Platillos disponibles
        $query = Platillo::with('user') // <- ¡CAMBIO CLAVE! Cargamos la relación aquí
            ->where('disponible', true)
            ->whereHas('user', function ($q) {
                $q->where('role', 'restaurante');
            });

        // Filtro 1: Búsqueda por término general
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nombre', 'like', "%{$searchTerm}%")
                  ->orWhere('descripcion', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('full_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Filtro 2: Por tipo de comida
        if ($request->filled('tipo_comida')) {
            $query->where('tipo_comida', $request->input('tipo_comida'));
        }

        // Filtro 3: Por rango de precio
        if ($request->filled('min_precio')) {
            $query->where('precio', '>=', $request->input('min_precio'));
        }
        if ($request->filled('max_precio')) {
            $query->where('precio', '<=', $request->input('max_precio'));
        }

        // Ordenamos los resultados por el nombre del restaurante y luego por el nombre del platillo
        $platillos = $query->join('users', 'platillos.user_id', '=', 'users.id')
                            ->orderBy('users.full_name', 'asc')
                            ->orderBy('platillos.nombre', 'asc')
                            ->select('platillos.*') // Seleccionamos solo las columnas de platillos para evitar conflictos
                            ->get();

        if ($request->ajax()) {
            return view('cliente.partials.lista-platillos', compact('platillos'))->render();
        }

        return view('cliente.restaurantes', compact('platillos', 'tiposDeComida'));
    }
}