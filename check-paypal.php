<?php

// Script de diagnóstico para PayPal
echo "=== Diagnóstico de Configuración PayPal ===\n\n";

echo "1. Variables de entorno (.env):\n";
echo "   PAYPAL_CLIENT_ID: " . (env('PAYPAL_CLIENT_ID') ?: '[NO CONFIGURADO]') . "\n";
echo "   PAYPAL_SECRET: " . (env('PAYPAL_SECRET') ? '[CONFIGURADO - ' . strlen(env('PAYPAL_SECRET')) . ' caracteres]' : '[NO CONFIGURADO]') . "\n";
echo "   PAYPAL_MODE: " . (env('PAYPAL_MODE', 'sandbox')) . "\n\n";

echo "2. Configuración de services.php:\n";
echo "   client_id: " . (config('services.paypal.client_id') ?: '[NO CONFIGURADO]') . "\n";
echo "   secret: " . (config('services.paypal.secret') ? '[CONFIGURADO - ' . strlen(config('services.paypal.secret')) . ' caracteres]' : '[NO CONFIGURADO]') . "\n";
echo "   mode: " . (config('services.paypal.mode', 'sandbox')) . "\n\n";

echo "3. Verificación:\n";
if (empty(config('services.paypal.client_id'))) {
    echo "   ❌ ERROR: PAYPAL_CLIENT_ID no está configurado\n";
    echo "   Solución: Agrega PAYPAL_CLIENT_ID=tu_client_id en el archivo .env\n\n";
} else {
    echo "   ✓ Client ID configurado correctamente\n";
}

if (empty(config('services.paypal.secret'))) {
    echo "   ❌ ERROR: PAYPAL_SECRET no está configurado\n";
    echo "   Solución: Agrega PAYPAL_SECRET=tu_secret en el archivo .env\n\n";
} else {
    echo "   ✓ Secret configurado correctamente\n";
}

echo "\n4. Instrucciones:\n";
echo "   - Ve a https://developer.paypal.com/dashboard/\n";
echo "   - Inicia sesión con tu cuenta\n";
echo "   - Ve a 'Apps & Credentials'\n";
echo "   - En 'Sandbox', crea una app o usa una existente\n";
echo "   - Copia el 'Client ID' y 'Secret'\n";
echo "   - Agrégalos a tu archivo .env\n";
echo "   - Ejecuta: php artisan config:clear\n";
