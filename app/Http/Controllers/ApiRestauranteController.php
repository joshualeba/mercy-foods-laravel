<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ApiRestauranteController extends Controller
{
    /**
     * GET /api/restaurantes
     * Listar todos los restaurantes
     */
    public function index()
    {
        $restaurantes = User::where('role', 'restaurante')
            ->withAvg('reviewsAsRestaurante', 'rating_restaurante')
            ->withCount('reviewsAsRestaurante')
            ->get()
            ->map(function($restaurante) {
                return [
                    'id' => $restaurante->id,
                    'nombre' => $restaurante->full_name,
                    'direccion' => $restaurante->restaurant_address,
                    'telefono' => $restaurante->phone,
                    'email' => $restaurante->email,
                    'rating_promedio' => round($restaurante->reviews_as_restaurante_avg_rating_restaurante ?? 0, 1),
                    'total_reviews' => $restaurante->reviews_as_restaurante_count,
                ];
            });
        
        return response()->json([
            'restaurantes' => $restaurantes
        ], 200);
    }

    /**
     * GET /api/restaurantes/{id}
     * Ver detalle de un restaurante específico
     */
    public function show($id)
    {
        $restaurante = User::where('role', 'restaurante')
            ->withAvg('reviewsAsRestaurante', 'rating_restaurante')
            ->withCount('reviewsAsRestaurante')
            ->find($id);
        
        if (!$restaurante) {
            return response()->json(['message' => 'Restaurante no encontrado.'], 404);
        }
        
        return response()->json([
            'restaurante' => [
                'id' => $restaurante->id,
                'nombre' => $restaurante->full_name,
                'direccion' => $restaurante->restaurant_address,
                'telefono' => $restaurante->phone,
                'email' => $restaurante->email,
                'rating_promedio' => round($restaurante->reviews_as_restaurante_avg_rating_restaurante ?? 0, 1),
                'total_reviews' => $restaurante->reviews_as_restaurante_count,
            ]
        ], 200);
    }

    /**
     * GET /api/restaurantes/{id}/platillos
     * Listar platillos de un restaurante específico
     */
    public function platillos($id)
    {
        $restaurante = User::where('role', 'restaurante')->find($id);
        
        if (!$restaurante) {
            return response()->json(['message' => 'Restaurante no encontrado.'], 404);
        }
        
        $platillos = $restaurante->platillos()
            ->where('disponible', true)
            ->get()
            ->map(function($platillo) {
                return [
                    'id' => $platillo->id,
                    'nombre' => $platillo->nombre,
                    'descripcion' => $platillo->descripcion,
                    'precio' => $platillo->precio,
                    'imagen_url' => $platillo->imagen_url,
                    'disponible' => $platillo->disponible,
                ];
            });
        
        return response()->json([
            'restaurante' => [
                'id' => $restaurante->id,
                'nombre' => $restaurante->full_name,
            ],
            'platillos' => $platillos
        ], 200);
    }
}
