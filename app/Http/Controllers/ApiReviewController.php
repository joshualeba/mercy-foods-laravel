<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Pedido;
use Illuminate\Http\Request;

class ApiReviewController extends Controller
{
    /**
     * GET /api/reviews
     * Listar reseñas del usuario autenticado
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $reviews = Review::where('cliente_id', $user->id)
            ->with(['pedido', 'restaurante', 'repartidor'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($review) {
                return [
                    'id' => $review->id,
                    'pedido_id' => $review->pedido_id,
                    'restaurante' => [
                        'id' => $review->restaurante->id,
                        'nombre' => $review->restaurante->full_name,
                    ],
                    'repartidor' => $review->repartidor ? [
                        'id' => $review->repartidor->id,
                        'nombre' => $review->repartidor->full_name,
                    ] : null,
                    'rating_restaurante' => $review->rating_restaurante,
                    'comentario_restaurante' => $review->comentario_restaurante,
                    'rating_repartidor' => $review->rating_repartidor,
                    'comentario_repartidor' => $review->comentario_repartidor,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ];
            });
        
        return response()->json([
            'reviews' => $reviews
        ], 200);
    }

    /**
     * POST /api/reviews
     * Crear una nueva reseña
     */
    public function store(Request $request)
    {
        // Validar datos de entrada
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'rating_restaurante' => 'required|integer|min:1|max:5',
            'comentario_restaurante' => 'nullable|string|max:500',
            'rating_repartidor' => 'nullable|integer|min:1|max:5',
            'comentario_repartidor' => 'nullable|string|max:500',
        ]);

        $pedido = Pedido::findOrFail($request->pedido_id);
        $user = $request->user();

        // Validar que el usuario sea el dueño del pedido
        if ($pedido->cliente_id !== $user->id) {
            return response()->json(['message' => 'No tienes permiso para calificar este pedido.'], 403);
        }

        // Validar que el estado sea "Entregado"
        if ($pedido->estado !== 'entregado') {
            return response()->json(['message' => 'Solo puedes calificar pedidos que han sido entregados.'], 400);
        }

        // Validar si ya existe una reseña
        if (Review::where('pedido_id', $pedido->id)->exists()) {
            return response()->json(['message' => 'Ya has enviado una reseña para este pedido.'], 400);
        }

        // Si el pedido tiene repartidor, validar que se haya calificado
        if ($pedido->repartidor_id && !$request->rating_repartidor) {
            return response()->json(['message' => 'Debes calificar al repartidor para este pedido.'], 400);
        }

        // Crear la reseña
        $review = Review::create([
            'pedido_id' => $pedido->id,
            'cliente_id' => $user->id,
            'restaurante_id' => $pedido->restaurante_id,
            'repartidor_id' => $pedido->repartidor_id,
            'rating_restaurante' => $request->rating_restaurante,
            'comentario_restaurante' => $request->comentario_restaurante,
            'rating_repartidor' => $request->rating_repartidor,
            'comentario_repartidor' => $request->comentario_repartidor,
        ]);

        return response()->json([
            'message' => 'Reseña creada exitosamente',
            'review' => [
                'id' => $review->id,
                'pedido_id' => $review->pedido_id,
                'rating_restaurante' => $review->rating_restaurante,
                'rating_repartidor' => $review->rating_repartidor,
                'created_at' => $review->created_at->format('Y-m-d H:i:s')
            ]
        ], 201);
    }
}
