<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
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

        // 1. Validar que el usuario sea el dueño del pedido
        if ($pedido->cliente_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para calificar este pedido.');
        }

        // 2. Validar que el estado sea "Entregado"
        if ($pedido->estado !== 'entregado') {
            return redirect()->back()->with('error', 'Solo puedes calificar pedidos que han sido entregados.');
        }

        // 3. Validar si ya existe una reseña para evitar duplicados
        if (Review::where('pedido_id', $pedido->id)->exists()) {
            return redirect()->back()->with('error', 'Ya has enviado una reseña para este pedido.');
        }

        // 4. Si el pedido tiene repartidor, validar que se haya calificado
        if ($pedido->repartidor_id && !$request->rating_repartidor) {
            return redirect()->back()->with('error', 'Debes calificar al repartidor para este pedido.');
        }

        // Crear la reseña
        Review::create([
            'pedido_id' => $pedido->id,
            'cliente_id' => Auth::id(),
            'restaurante_id' => $pedido->restaurante_id,
            'repartidor_id' => $pedido->repartidor_id,
            'rating_restaurante' => $request->rating_restaurante,
            'comentario_restaurante' => $request->comentario_restaurante,
            'rating_repartidor' => $request->rating_repartidor,
            'comentario_repartidor' => $request->comentario_repartidor,
        ]);

        return redirect()->back()->with('success', '¡Gracias por tu calificación!');
    }
}