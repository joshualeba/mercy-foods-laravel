<?php

namespace App\Http\Controllers\Cliente;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Platillo;
use App\Models\User;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        // Obtener todos los tipos de cocina de los restaurantes registrados
        $tiposDeCocina = User::where('role', 'restaurante')
            ->whereNotNull('cuisine_type')
            ->distinct()
            ->orderBy('cuisine_type')
            ->pluck('cuisine_type');

        // Consulta para obtener los platillos
        $query = Platillo::with('user')
            ->where('disponible', true)
            ->whereHas('user', function ($q) {
                $q->where('role', 'restaurante');
            });

        // Filtro por término de búsqueda
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

        // Filtro por tipo de cocina
        if ($request->filled('tipo_comida')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('cuisine_type', $request->input('tipo_comida'));
            });
        }

        // Filtro por rango de precio
        if ($request->filled('min_precio')) {
            $query->where('precio', '>=', $request->input('min_precio'));
        }
        if ($request->filled('max_precio')) {
            $query->where('precio', '<=', $request->input('max_precio'));
        }

        // Ordenar resultados
        $platillos = $query->join('users', 'platillos.user_id', '=', 'users.id')
                            ->orderBy('users.full_name', 'asc')
                            ->orderBy('platillos.nombre', 'asc')
                            ->select('platillos.*')
                            ->get();

        // Respuesta para peticiones AJAX
        if ($request->ajax()) {
            return view('cliente.partials.lista-platillos', compact('platillos'))->render();
        }

        // Respuesta para la carga inicial de la página
        return view('cliente.restaurantes', compact('platillos', 'tiposDeCocina'));
    }
}