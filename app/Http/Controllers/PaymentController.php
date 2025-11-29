<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Muestra la vista del formulario de pago.
     */
    public function index()
    {
        // Obtiene la información del usuario autenticado
        $user = Auth::user();
        
        // Devuelve la vista 'cliente.pago' y le pasa los datos del usuario
        return view('cliente.pago', compact('user'));
    }

    /**
     * Almacena el método de pago del cliente.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos que llegan del formulario
        $validator = Validator::make($request->all(), [
            'card_name' => 'required|string|max:255|min:5',
            'card_number' => 'required|string|min:19|max:19', // 16 dígitos + 3 espacios
            'card_expiry' => 'required|string|min:5|max:5',
            'card_cvc' => 'required|string|min:3|max:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Datos inválidos. Por favor, revisa el formulario.'], 422);
        }

        // 2. Simulación de la pasarela de pago
        $cardNumber = str_replace(' ', '', $request->input('card_number'));

        if (substr($cardNumber, 0, 4) !== '4242') {
            return response()->json(['success' => false, 'message' => 'La tarjeta fue rechazada por el banco.'], 400);
        }

        try {
            // 3. Guardar la información de forma segura
            $user = Auth::user();
            $user->card_name = $request->input('card_name');
            $user->card_expiry = $request->input('card_expiry');
            $user->card_last_four = substr($cardNumber, -4); // Guardamos solo los últimos 4 dígitos
            $user->save();

            return response()->json(['success' => true, 'message' => '¡Tu método de pago se ha guardado con éxito!']);

        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json(['success' => false, 'message' => 'Ocurrió un error inesperado al guardar tus datos.'], 500);
        }
    }

    public function verify()
    {
        $user = Auth::user();

        // Verificamos si tiene PayPal o tarjeta guardada
        $hasPaymentMethod = !is_null($user->paypal_email) || !is_null($user->card_last_four);

        return response()->json(['hasPaymentMethod' => $hasPaymentMethod]);
    }

    /**
     * Guarda el método de pago de PayPal del usuario.
     */
    public function savePayPal(Request $request)
    {
        $request->validate([
            'paypal_email' => 'required|email',
            'paypal_payer_id' => 'required|string',
        ]);

        try {
            $user = Auth::user();
            $user->paypal_email = $request->paypal_email;
            $user->paypal_payer_id = $request->paypal_payer_id;
            $user->save();

            \Log::info('Método de pago PayPal guardado', [
                'user_id' => $user->id,
                'paypal_email' => $request->paypal_email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Método de pago guardado exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al guardar método de pago PayPal', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el método de pago'
            ], 500);
        }
    }

    /**
     * Elimina el método de pago guardado del usuario.
     */
    public function removePaymentMethod()
    {
        try {
            $user = Auth::user();
            
            // Limpiar todos los campos de pago
            $user->card_name = null;
            $user->card_last_four = null;
            $user->card_expiry = null;
            $user->paypal_email = null;
            $user->paypal_payer_id = null;
            $user->save();

            \Log::info('Método de pago eliminado', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Método de pago eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al eliminar método de pago', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el método de pago'
            ], 500);
        }
    }
}