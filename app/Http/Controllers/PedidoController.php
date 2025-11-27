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
        $pedidosNuevos = $restaurante->pedidosRestaurante()->where('estado', 'pendiente')->with('cliente', 'detalles.platillo')->latest()->get();
        $pedidosEnPreparacion = $restaurante->pedidosRestaurante()->where('estado', 'en_preparacion')->with('cliente', 'detalles.platillo')->latest()->get();
        $pedidosListos = $restaurante->pedidosRestaurante()->where('estado', 'listo_para_recoger')->with('cliente', 'detalles.platillo')->latest()->get();

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
                                        ->with(['restaurante', 'detalles.platillo'])
                                        ->latest()
                                        ->get();
                                        
        $pedidosEnCamino = $cliente->pedidosCliente()
                                   ->whereIn('estado', ['listo_para_recoger', 'en_camino', 'recogido'])
                                   ->with(['restaurante', 'repartidor', 'detalles.platillo'])
                                   ->latest()
                                   ->get();

        $pedidosEntregados = $cliente->pedidosCliente()
                                    ->where('estado', 'entregado')
                                    ->with(['restaurante', 'repartidor', 'detalles.platillo', 'review'])
                                    ->latest()
                                    ->take(5)
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

        if (empty($user->address)) {
            return response()->json(['message' => 'Necesitas agregar una dirección de entrega antes de realizar un pedido.'], 400);
        }

        $restauranteId = null;
        $hasMultipleRestaurants = false;

        // --- LÓGICA DE VERIFICACIÓN DE MÚLTIPLES RESTAURANTES ---
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
                break; // Salimos del bucle tan pronto como encontramos una discrepancia
            }
        }

        if ($hasMultipleRestaurants) {
            return response()->json(['message' => 'No puedes ordenar de múltiples restaurantes a la vez.'], 400);
        }
        
        // Si es solo una pre-verificación, respondemos que todo está bien hasta ahora.
        if ($request->has('precheck')) {
            return response()->json(['message' => 'Pre-verificación exitosa.']);
        }

        // --- VALIDACIÓN DE PAGO DE PAYPAL (HU_4.3) ---
        // Validar que se reciban los datos de PayPal
        $request->validate([
            'paypal_order_id' => 'required|string',
            'paypal_payment_data' => 'required|array',
            'paypal_payment_data.status' => 'required|string'
        ]);

        // Verificar que el pago fue completado exitosamente
        $paymentStatus = $request->input('paypal_payment_data.status');
        if ($paymentStatus !== 'COMPLETED') {
            \Log::warning('Intento de crear pedido con pago no completado', [
                'user_id' => $user->id,
                'paypal_order_id' => $request->input('paypal_order_id'),
                'payment_status' => $paymentStatus
            ]);
            return response()->json([
                'message' => 'El pago no ha sido completado. Por favor, intenta nuevamente.'
            ], 400);
        }

        // Extraer información del pago de PayPal
        $paypalData = $request->input('paypal_payment_data');
        $paypalOrderId = $request->input('paypal_order_id');
        
        // Obtener el payer_id y capture_id de la respuesta de PayPal
        $paypalPayerId = $paypalData['payer']['payer_id'] ?? null;
        $paypalCaptureId = $paypalData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

        // --- A PARTIR DE AQUÍ, LA LÓGICA PARA CREAR EL PEDIDO CONTINÚA ---
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $platillo = Platillo::find($item['id']);
            $subtotal += $platillo->precio * $item['quantity'];
        }

        $costoEnvio = $subtotal * 0.15;
        $comisionPlataforma = $subtotal * 0.03;
        $total = $subtotal + $costoEnvio + $comisionPlataforma;

        // Crear el pedido con información de PayPal
        $pedido = Pedido::create([
            'cliente_id' => $user->id,
            'restaurante_id' => $restauranteId,
            'subtotal' => $subtotal,
            'costo_envio' => $costoEnvio,
            'comision_plataforma' => $comisionPlataforma,
            'total' => $total,
            'estado' => 'pendiente',
            'direccion_entrega' => $user->address,
            // Campos de PayPal (HU_4.3)
            'paypal_order_id' => $paypalOrderId,
            'paypal_payer_id' => $paypalPayerId,
            'paypal_payment_status' => $paymentStatus,
            'paypal_capture_id' => $paypalCaptureId,
            'metodo_pago' => 'paypal'
        ]);

        foreach ($cartItems as $item) {
            $platillo = Platillo::find($item['id']);
            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'platillo_id' => $platillo->id,
                'cantidad' => $item['quantity'],
                'precio_unitario' => $platillo->precio,
            ]);
        }

        \Log::info('Pedido creado exitosamente con pago de PayPal', [
            'pedido_id' => $pedido->id,
            'paypal_order_id' => $paypalOrderId,
            'total' => $total
        ]);

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

        // Devolvemos una respuesta de éxito con código 200 OK
        return response()->json(['success' => true, 'message' => '¡Pedido cancelado correctamente!'], 200);
    }
}