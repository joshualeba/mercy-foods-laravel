<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Backend PayPal (HU_4.1)</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h1>Verificación de HU_4.1: Backend PayPal</h1>
    <p>Esta página permite probar los endpoints del backend sin usar el frontend principal.</p>

    <div class="card mb-4">
        <div class="card-header">1. Crear Orden (/paypal/create)</div>
        <div class="card-body">
            <p>Envía una petición POST para crear una orden de $100.00 MXN.</p>
            <button id="btn-create" class="btn btn-primary">Crear Orden</button>
            <div id="result-create" class="mt-3 alert alert-secondary" style="display:none;"></div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">2. Capturar Orden (/paypal/capture)</div>
        <div class="card-body">
            <p>Intenta capturar la orden creada arriba. <br>
            <strong>Nota:</strong> Esto fallará con un error de PayPal (ORDER_NOT_APPROVED) porque no hemos aprobado la orden en el navegador, pero confirmará que el endpoint conecta con PayPal.</p>
            <div class="input-group mb-3">
                <input type="text" id="order-id-input" class="form-control" placeholder="ID de la orden (se llena automático)">
                <button id="btn-capture" class="btn btn-success" disabled>Capturar Orden</button>
            </div>
            <div id="result-capture" class="mt-3 alert alert-secondary" style="display:none;"></div>
        </div>
    </div>

    <script>
        const btnCreate = document.getElementById('btn-create');
        const btnCapture = document.getElementById('btn-capture');
        const orderIdInput = document.getElementById('order-id-input');
        const resultCreate = document.getElementById('result-create');
        const resultCapture = document.getElementById('result-capture');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        btnCreate.addEventListener('click', () => {
            resultCreate.style.display = 'block';
            resultCreate.textContent = 'Creando orden...';
            resultCreate.className = 'mt-3 alert alert-info';

            fetch('/paypal/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ total: 100.00 })
            })
            .then(async res => {
                const isJson = res.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await res.json() : null;

                if (!res.ok) {
                    const error = (data && data.message) || res.statusText;
                    return Promise.reject(error);
                }
                
                if (!data) {
                    // Si no es JSON, probablemente es HTML (error 500 o redirect)
                    const text = await res.text();
                    throw new Error('Respuesta no válida (posiblemente HTML): ' + text.substring(0, 100) + '...');
                }

                return data;
            })
            .then(data => {
                resultCreate.textContent = JSON.stringify(data, null, 2);
                if (data.id) {
                    resultCreate.className = 'mt-3 alert alert-success';
                    orderIdInput.value = data.id;
                    btnCapture.disabled = false;
                } else {
                    resultCreate.className = 'mt-3 alert alert-danger';
                }
            })
            .catch(err => {
                console.error(err);
                resultCreate.textContent = 'Error: ' + err.toString();
                resultCreate.className = 'mt-3 alert alert-danger';
            });
        });

        btnCapture.addEventListener('click', () => {
            const orderId = orderIdInput.value;
            resultCapture.style.display = 'block';
            resultCapture.textContent = 'Capturando orden...';
            resultCapture.className = 'mt-3 alert alert-info';

            fetch('/paypal/capture', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ order_id: orderId })
            })
            .then(res => res.json())
            .then(data => {
                resultCapture.textContent = JSON.stringify(data, null, 2);
                // Esperamos un error de PayPal (400/422) o un éxito si mágicamente se aprobó
                if (data.error || data.name === 'UNPROCESSABLE_ENTITY') {
                    resultCapture.className = 'mt-3 alert alert-warning'; // Warning porque es esperado
                    resultCapture.innerHTML += '<br><strong>¡Correcto!</strong> El backend respondió. El error es normal porque no aprobamos el pago en PayPal UI.';
                } else if (data.status === 'COMPLETED') {
                    resultCapture.className = 'mt-3 alert alert-success';
                } else {
                    resultCapture.className = 'mt-3 alert alert-secondary';
                }
            })
            .catch(err => {
                resultCapture.textContent = 'Error: ' + err;
                resultCapture.className = 'mt-3 alert alert-danger';
            });
        });
    </script>
</body>
</html>
