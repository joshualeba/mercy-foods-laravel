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

        // Verificamos si la columna 'card_last_four' tiene un valor
        $hasPaymentMethod = !is_null($user->card_last_four);

        return response()->json(['hasPaymentMethod' => $hasPaymentMethod]);
    }
}