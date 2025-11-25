<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaypalController extends Controller
{
    private $clientId;
    private $secret;
    private $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->secret = config('services.paypal.secret');
        $this->baseUrl = config('services.paypal.mode') === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';
    }

    private function getAccessToken()
    {
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        Log::error('PayPal Auth Failed: ' . $response->body());
        return null;
    }

    public function createOrder(Request $request)
    {
        $token = $this->getAccessToken();
        
        if (!$token) {
            return response()->json(['error' => 'Could not authenticate with PayPal'], 500);
        }

        // Validar que venga el monto, o calcularlo del carrito (por ahora asumimos que el frontend envía el total o se recalcula)
        // Para HU_4.1, vamos a recibir el total del request para probar la integración básica.
        // En una implementación real, deberíamos recalcular el total del servidor para evitar manipulaciones.
        $request->validate([
            'total' => 'required|numeric|min:0.01'
        ]);

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'MXN', // O USD según corresponda
                            'value' => $request->total,
                        ],
                    ],
                ],
            ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        Log::error('PayPal Create Order Failed: ' . $response->body());
        return response()->json(['error' => 'Failed to create PayPal order'], 500);
    }

    public function captureOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string'
        ]);

        $token = $this->getAccessToken();

        if (!$token) {
            return response()->json(['error' => 'Could not authenticate with PayPal'], 500);
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/v2/checkout/orders/{$request->order_id}/capture", [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        Log::error('PayPal Capture Order Failed: ' . $response->body());
        return response()->json(['error' => 'Failed to capture PayPal order'], 500);
    }
}
