<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    // Muestra la vista del formulario de pago
    public function index()
    {
        return view('cliente.pago');
    }

    // Procesa la simulación de pago
    public function process(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_name' => 'required|string|max:100',
            'card_number' => 'required|string|min:19|max:19',
            'card_expiry' => 'required|string|size:5',
            'card_cvc' => 'required|string|min:3|max:4',
        ], [
            'card_number.min' => 'El número de tarjeta debe tener 16 dígitos.',
            'card_number.max' => 'El número de tarjeta debe tener 16 dígitos.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $cardNumber = str_replace(' ', '', $request->card_number);

        if ($cardNumber === '4242424242424242') {
            return response()->json([
                'success' => true, 
                'message' => '¡Método de pago guardado con éxito!'
            ]);
        }

        if ($cardNumber === '1111111111111111') {
            return response()->json([
                'success' => false, 
                'message' => 'Pago rechazado por el banco emisor.'
            ], 400);
        }

        return response()->json([
            'success' => false, 
            'message' => 'El número de tarjeta es inválido. Intenta con otro.'
        ], 400);
    }
}