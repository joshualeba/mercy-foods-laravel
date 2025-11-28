<?php

namespace App\Http\Controllers;

use App\Models\Platillo;
use Illuminate\Http\Request;

class ApiPlatilloController extends Controller
{
    /**
     * GET /api/platillos
     * Listar todos los platillos disponibles
     */
    public function index(Request $request)
    {
        $query = Platillo::with('user')->where('disponible', true);
        
        // Filtro opcional por restaurante
        if ($request->has('restaurante_id')) {
            $query->where('user_id', $request->restaurante_id);
        }
        
        $platillos = $query->get()->map(function($platillo) {
            return [
                'id' => $platillo->id,
                'nombre' => $platillo->nombre,
                'descripcion' => $platillo->descripcion,
                'precio' => $platillo->precio,
                'imagen_url' => $platillo->imagen_url,
                'disponible' => $platillo->disponible,
                'restaurante' => [
                    'id' => $platillo->user->id,
                    'nombre' => $platillo->user->full_name,
                    'direccion' => $platillo->user->restaurant_address,
                ]
            ];
        });
        
        return response()->json([
            'platillos' => $platillos
        ], 200);
    }

    /**
     * GET /api/platillos/{id}
     * Ver detalle de un platillo específico
     */
    public function show($id)
    {
        $platillo = Platillo::with('user')->find($id);
        
        if (!$platillo) {
            return response()->json(['message' => 'Platillo no encontrado.'], 404);
        }
        
        return response()->json([
            'platillo' => [
                'id' => $platillo->id,
                'nombre' => $platillo->nombre,
                'descripcion' => $platillo->descripcion,
                'precio' => $platillo->precio,
                'imagen_url' => $platillo->imagen_url,
                'disponible' => $platillo->disponible,
                'restaurante' => [
                    'id' => $platillo->user->id,
                    'nombre' => $platillo->user->full_name,
                    'direccion' => $platillo->user->restaurant_address,
                    'telefono' => $platillo->user->phone,
                ]
            ]
        ], 200);
    }
}
