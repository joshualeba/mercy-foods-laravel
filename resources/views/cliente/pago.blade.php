<div class="profile-section-container">
    <h1>Método de Pago</h1>
    <p>Añade un método de pago para finalizar tus órdenes futuras.</p>

    <div class="profile-card">
        <h2>Información de la Tarjeta</h2>
        <form id="payment-form" action="{{ route('cliente.pago.procesar') }}" novalidate>
            @csrf
            <div class="form-group span-2">
                <label for="card_name">Nombre en la tarjeta</label>
                <input type="text" id="card_name" name="card_name" required placeholder="Juan M. Pérez" class="form-control">
                <small class="error-message"></small>
            </div>

            <div class="form-group span-2">
                <label for="card_number">Número de tarjeta</label>
                <input type="text" id="card_number" name="card_number" required placeholder="4242 4242 4242 4242" maxlength="19" class="form-control">
                <small class="error-message"></small>
            </div>
            
            <small class="form-hint">
                <b>Modo de simulación:</b><br>
                Usa `4242 4242 4242 4242` para un pago exitoso.<br>
                Usa `1111 1111 1111 1111` para un pago rechazado.
            </small>

            <div class="profile-grid">
                <div class="form-group">
                    <label for="card_expiry">Expiración (MM/AA)</label>
                    <input type="text" id="card_expiry" name="card_expiry" required placeholder="12/26" class="form-control" maxlength="5">
                    <small class="error-message"></small>
                </div>
                <div class="form-group">
                    <label for="card_cvc">CVC</label>
                    <input type="text" id="card_cvc" name="card_cvc" required placeholder="123" class="form-control" maxlength="4">
                    <small class="error-message"></small>
                </div>
            </div>

            <div class="profile-actions">
                <button type="submit" id="submit-payment-btn" class="btn btn-primary">Guardar Método de Pago</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Pequeños scripts para formatear los inputs de la tarjeta
    const cardNumberInput = document.getElementById('card_number');
    if(cardNumberInput) {
        cardNumberInput.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
        });
    }

    const cardExpiryInput = document.getElementById('card_expiry');
    if(cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }

    if (typeof initializePaymentSection === 'function') {
        initializePaymentSection();
    } else {
        console.error('La función initializePaymentSection no está definida en dashboard.js');
    }
</script>