<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DetallePedido;
use App\Models\Platillo;

class PedidoController extends Controller
{
    /**
     * Muestra la lista de pedidos para el restaurante autenticado.
     */
    public function index()
    {
        $restaurante = Auth::user();

        // Obtenemos los pedidos y los separamos por estado
        $pedidosNuevos = $restaurante->pedidosRestaurante()->where('estado', 'pendiente')->with('cliente')->latest()->get();
        $pedidosEnPreparacion = $restaurante->pedidosRestaurante()->where('estado', 'en_preparacion')->with('cliente')->latest()->get();
        $pedidosListos = $restaurante->pedidosRestaurante()->where('estado', 'listo_para_recoger')->with('cliente')->latest()->get();

        return view('restaurante.pedidos', compact('pedidosNuevos', 'pedidosEnPreparacion', 'pedidosListos'));
    }

    public function actualizarEstado(Request $request, Pedido $pedido)
    {
        // Verificamos que el pedido pertenezca al restaurante autenticado
        if ($pedido->restaurante_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'estado' => 'required|string|in:en_preparacion,listo_para_recoger',
        ]);

        $pedido->estado = $request->estado;
        $pedido->save();

        // Si el pedido está listo, buscamos un repartidor
        if ($request->estado === 'listo_para_recoger') {
            $this->asignarRepartidor($pedido);
        }

        return response()->json(['message' => 'Estado del pedido actualizado con éxito.']);
    }

    /**
     * Asigna el repartidor más cercano (simulado).
     */
    protected function asignarRepartidor(Pedido $pedido)
    {
        // Simulación: encontrar el primer repartidor disponible
        $repartidor = User::where('role', 'repartidor')->first();

        if ($repartidor) {
            $pedido->repartidor_id = $repartidor->id;
            $pedido->estado = 'en_camino'; // Cambiamos el estado a "en_camino"
            $pedido->save();
        }
    }

    /**
     * Muestra la lista de pedidos para el cliente autenticado.
     */
    public function verPedidosCliente()
    {
        $cliente = Auth::user();

        // Obtenemos los pedidos del cliente y los separamos por estado
        $pedidosEnPreparacion = $cliente->pedidosCliente()
                                        ->whereIn('estado', ['pendiente', 'en_preparacion'])
                                        ->with('restaurante')
                                        ->latest()
                                        ->get();
                                        
        $pedidosEnCamino = $cliente->pedidosCliente()
                                   ->whereIn('estado', ['listo_para_recoger', 'en_camino'])
                                   ->with('restaurante')
                                   ->latest()
                                   ->get();

        $pedidosEntregados = $cliente->pedidosCliente()
                                     ->where('estado', 'entregado')
                                     ->with('restaurante')
                                     ->latest()
                                     ->take(5) // Mostramos solo los últimos 5 entregados
                                     ->get();

        return view('cliente.pedidos', compact('pedidosEnPreparacion', 'pedidosEnCamino', 'pedidosEntregados'));
    }

    public function crearDesdeCarrito(Request $request)
    {
        $cartItems = $request->input('cart');
        $user = Auth::user();

        if (empty($cartItems)) {
            return response()->json(['message' => 'El carrito está vacío.'], 400);
        }

        $total = 0;
        $restauranteId = null;

        foreach ($cartItems as $item) {
            $platillo = Platillo::find($item['id']);
            if (!$platillo) {
                return response()->json(['message' => 'Platillo no encontrado.'], 404);
            }
            // Asignamos el restaurante del primer platillo
            if ($restauranteId === null) {
                $restauranteId = $platillo->user_id;
            }
            // Validamos que todos los platillos sean del mismo restaurante
            if ($restauranteId !== $platillo->user_id) {
                return response()->json(['message' => 'No puedes ordenar de múltiples restaurantes a la vez.'], 400);
            }
            $total += $platillo->precio * $item['quantity'];
        }
        
        // Crear el pedido principal
        $pedido = Pedido::create([
            'cliente_id' => $user->id,
            'restaurante_id' => $restauranteId,
            'total' => $total,
            'estado' => 'pendiente',
            'direccion_entrega' => $user->address, // Asume que el usuario tiene una dirección guardada
        ]);

        // Crear los detalles del pedido
        foreach ($cartItems as $item) {
            $platillo = Platillo::find($item['id']);
            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'platillo_id' => $platillo->id,
                'cantidad' => $item['quantity'],
                'precio_unitario' => $platillo->precio,
            ]);
        }

        return response()->json(['message' => '¡Pedido realizado con éxito!', 'pedido_id' => $pedido->id]);
    }

    public function cancelar($id)
    {
        // Buscamos el pedido por su ID
        $pedido = \App\Models\Pedido::find($id);

        // Verificamos si el pedido existe y si pertenece al usuario autenticado
        if (!$pedido || $pedido->cliente_id !== auth()->id()) {
            return response()->json(['message' => 'Pedido no encontrado.'], 404);
        }

        // Solo permitimos cancelar pedidos que estén en estado 'pendiente'
        if ($pedido->estado !== 'pendiente') {
            return response()->json(['message' => 'Este pedido ya no se puede cancelar.'], 400);
        }

        // Cambiamos el estado a 'cancelado' y guardamos
        $pedido->estado = 'cancelado';
        $pedido->save();

        // Devolvemos una respuesta de éxito
        return response()->json(['message' => '¡Pedido cancelado correctamente!']);
    }
}