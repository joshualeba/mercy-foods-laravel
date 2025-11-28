<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Platillo;
use App\Models\DetallePedido;
use Illuminate\Http\Request;

class ApiPedidoController extends Controller
{
    /**
     * GET /api/pedidos
     * Listar pedidos del usuario autenticado
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Filtro opcional por estado
        $query = $user->pedidosCliente()->with(['restaurante', 'repartidor', 'detalles.platillo']);
        
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }
        
        $pedidos = $query->latest()->get()->map(function($pedido) {
            return [
                'id' => $pedido->id,
                'restaurante' => [
                    'id' => $pedido->restaurante->id,
                    'nombre' => $pedido->restaurante->full_name,
                ],
                'repartidor' => $pedido->repartidor ? [
                    'id' => $pedido->repartidor->id,
                    'nombre' => $pedido->repartidor->full_name,
                ] : null,
                'subtotal' => $pedido->subtotal,
                'costo_envio' => $pedido->costo_envio,
                'comision_plataforma' => $pedido->comision_plataforma,
                'total' => $pedido->total,
                'estado' => $pedido->estado,
                'direccion_entrega' => $pedido->direccion_entrega,
                'metodo_pago' => $pedido->metodo_pago,
                'created_at' => $pedido->created_at->format('Y-m-d H:i:s'),
                'detalles' => $pedido->detalles->map(function($detalle) {
                    return [
                        'platillo_id' => $detalle->platillo_id,
                        'platillo_nombre' => $detalle->platillo->nombre,
                        'cantidad' => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                    ];
                }),
            ];
        });
        
        return response()->json([
            'pedidos' => $pedidos
        ], 200);
    }

    /**
     * POST /api/pedidos
     * Crear un nuevo pedido con pago PayPal
     */
    public function store(Request $request)
    {
        $cartItems = $request->input('cart');
        $user = $request->user();

        if (empty($cartItems)) {
            return response()->json(['message' => 'El carrito está vacío.'], 400);
        }

        if (empty($user->address)) {
            return response()->json(['message' => 'Necesitas agregar una dirección de entrega antes de realizar un pedido.'], 400);
        }

        $restauranteId = null;
        $hasMultipleRestaurants = false;

        // Verificar que todos los platillos sean del mismo restaurante
        foreach ($cartItems as $item) {
            $platillo = Platillo::find($item['id']);
            if (!$platillo) {
                return response()->json(['message' => 'Platillo no encontrado.'], 404);
            }
            if ($restauranteId === null) {
                $restauranteId = $platillo->user_id;
            }
            if ($restauranteId !== $platillo->user_id) {
                $hasMultipleRestaurants = true;
                break;
            }
        }

        if ($hasMultipleRestaurants) {
            return response()->json(['message' => 'No puedes ordenar de múltiples restaurantes a la vez.'], 400);
        }

        // Validar datos de PayPal
        $request->validate([
            'paypal_order_id' => 'required|string',
            'paypal_payment_data' => 'required|array',
            'paypal_payment_data.status' => 'required|string'
        ]);

        // Verificar que el pago fue completado
        $paymentStatus = $request->input('paypal_payment_data.status');
        if ($paymentStatus !== 'COMPLETED') {
            \Log::warning('Intento de crear pedido con pago no completado (API)', [
                'user_id' => $user->id,
                'paypal_order_id' => $request->input('paypal_order_id'),
                'payment_status' => $paymentStatus
            ]);
            return response()->json([
                'message' => 'El pago no ha sido completado. Por favor, intenta nuevamente.'
            ], 400);
        }

        // Extraer información del pago
        $paypalData = $request->input('paypal_payment_data');
        $paypalOrderId = $request->input('paypal_order_id');
        $paypalPayerId = $paypalData['payer']['payer_id'] ?? null;
        $paypalCaptureId = $paypalData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

        // Calcular totales
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $platillo = Platillo::find($item['id']);
            $subtotal += $platillo->precio * $item['quantity'];
        }

        $costoEnvio = $subtotal * 0.15;
        $comisionPlataforma = $subtotal * 0.03;
        $total = $subtotal + $costoEnvio + $comisionPlataforma;

        // Crear el pedido
        $pedido = Pedido::create([
            'cliente_id' => $user->id,
            'restaurante_id' => $restauranteId,
            'subtotal' => $subtotal,
            'costo_envio' => $costoEnvio,
            'comision_plataforma' => $comisionPlataforma,
            'total' => $total,
            'estado' => 'pendiente',
            'direccion_entrega' => $user->address,
            'paypal_order_id' => $paypalOrderId,
            'paypal_payer_id' => $paypalPayerId,
            'paypal_payment_status' => $paymentStatus,
            'paypal_capture_id' => $paypalCaptureId,
            'metodo_pago' => 'paypal'
        ]);

        // Crear detalles del pedido
        foreach ($cartItems as $item) {
            $platillo = Platillo::find($item['id']);
            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'platillo_id' => $platillo->id,
                'cantidad' => $item['quantity'],
                'precio_unitario' => $platillo->precio,
            ]);
        }

        \Log::info('Pedido creado exitosamente vía API', [
            'pedido_id' => $pedido->id,
            'paypal_order_id' => $paypalOrderId,
            'total' => $total
        ]);

        return response()->json([
            'message' => 'Pedido creado exitosamente',
            'pedido' => [
                'id' => $pedido->id,
                'total' => $pedido->total,
                'estado' => $pedido->estado,
                'created_at' => $pedido->created_at->format('Y-m-d H:i:s')
            ]
        ], 201);
    }

    /**
     * GET /api/pedidos/{id}
     * Ver detalle de un pedido específico
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $pedido = Pedido::with(['restaurante', 'repartidor', 'detalles.platillo', 'review'])
                        ->find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido no encontrado.'], 404);
        }

        // Verificar que el pedido pertenezca al usuario autenticado
        if ($pedido->cliente_id !== $user->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        return response()->json([
            'pedido' => [
                'id' => $pedido->id,
                'restaurante' => [
                    'id' => $pedido->restaurante->id,
                    'nombre' => $pedido->restaurante->full_name,
                    'direccion' => $pedido->restaurante->restaurant_address,
                ],
                'repartidor' => $pedido->repartidor ? [
                    'id' => $pedido->repartidor->id,
                    'nombre' => $pedido->repartidor->full_name,
                ] : null,
                'subtotal' => $pedido->subtotal,
                'costo_envio' => $pedido->costo_envio,
                'comision_plataforma' => $pedido->comision_plataforma,
                'total' => $pedido->total,
                'estado' => $pedido->estado,
                'direccion_entrega' => $pedido->direccion_entrega,
                'metodo_pago' => $pedido->metodo_pago,
                'paypal_order_id' => $pedido->paypal_order_id,
                'created_at' => $pedido->created_at->format('Y-m-d H:i:s'),
                'detalles' => $pedido->detalles->map(function($detalle) {
                    return [
                        'platillo_id' => $detalle->platillo_id,
                        'platillo_nombre' => $detalle->platillo->nombre,
                        'platillo_descripcion' => $detalle->platillo->descripcion,
                        'cantidad' => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'subtotal' => $detalle->cantidad * $detalle->precio_unitario,
                    ];
                }),
                'review' => $pedido->review ? [
                    'rating_restaurante' => $pedido->review->rating_restaurante,
                    'comentario_restaurante' => $pedido->review->comentario_restaurante,
                    'rating_repartidor' => $pedido->review->rating_repartidor,
                    'comentario_repartidor' => $pedido->review->comentario_repartidor,
                ] : null,
            ]
        ], 200);
    }
}
