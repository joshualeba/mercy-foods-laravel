{{-- Página de Método de Pago con PayPal --}}
<div class="profile-section-container">
    <h1>Método de pago</h1>
    <p>Configura tu método de pago con PayPal para realizar pedidos de forma rápida y segura.</p>

    <div class="profile-card">
        @if(Auth::user()->paypal_email)
            {{-- Usuario ya tiene un método de pago guardado --}}
            <h2>Método de pago guardado</h2>
            
            <div class="saved-payment-display">
                <div class="payment-method-card">
                    <div class="payment-icon">
                        <i class="fab fa-paypal" style="font-size: 2.5rem; color: #0070ba;"></i>
                    </div>
                    <div class="payment-details">
                        <h3>PayPal</h3>
                        <p>{{ Auth::user()->paypal_email }}</p>
                        <small>ID: {{ substr(Auth::user()->paypal_payer_id, 0, 10) }}...</small>
                    </div>
                </div>
            </div>

            <div class="profile-actions" style="margin-top: 2rem;">
                <button id="remove-payment-method-btn" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Eliminar método de pago
                </button>
            </div>

        @else
            {{-- Usuario no tiene método de pago --}}
            <h2>Configurar PayPal</h2>
            <p>Para configurar tu método de pago, haz clic en el botón de PayPal a continuación. Se realizará una transacción de verificación de $0.01 MXN que será reembolsada automáticamente.</p>

            <div class="paypal-setup-info">
                <div class="info-box">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Seguro y confiable</h3>
                    <p>Tus datos están protegidos con la seguridad de PayPal</p>
                </div>
                <div class="info-box">
                    <i class="fas fa-bolt"></i>
                    <h3>Pago rápido</h3>
                    <p>Realiza pedidos sin ingresar tus datos cada vez</p>
                </div>
            </div>

            {{-- Contenedor para botones de PayPal --}}
            <div id="paypal-setup-container" style="margin-top: 2rem; max-width: 400px;"></div>

            <div class="payment-note">
                <p><strong>Nota:</strong> Al configurar tu método de pago, autorizas a Mercy Food a procesar pagos a través de PayPal para tus pedidos futuros.</p>
            </div>
        @endif
    </div>
</div>

{{-- Script para inicializar PayPal --}}
<script>
// Esperar a que el SDK de PayPal esté disponible
function initializePayPalSetup() {
    const setupContainer = document.getElementById('paypal-setup-container');
    const removeBtn = document.getElementById('remove-payment-method-btn');

    // Si el contenedor existe y PayPal está disponible, renderizar botones
    if (setupContainer && typeof paypal !== 'undefined') {
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '0.01',
                            currency_code: 'MXN'
                        },
                        description: 'Verificación de método de pago - Mercy Food'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Guardar información de PayPal
                    fetch('{{ route("cliente.pago.save-paypal") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            paypal_email: details.payer.email_address,
                            paypal_payer_id: details.payer.payer_id,
                            paypal_order_id: data.orderID
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Método de pago configurado!',
                            text: 'Tu cuenta de PayPal ha sido vinculada exitosamente.',
                            confirmButtonColor: '#FF6347'
                        }).then(() => {
                            window.location.reload();
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo guardar el método de pago. Intenta de nuevo.',
                            confirmButtonColor: '#FF6347'
                        });
                    });
                });
            },
            onError: function(err) {
                console.error('PayPal Error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de PayPal',
                    text: 'Ocurrió un error al procesar con PayPal. Intenta de nuevo.',
                    confirmButtonColor: '#FF6347'
                });
            }
        }).render('#paypal-setup-container');
    } else if (setupContainer) {
        // Si PayPal no está disponible aún, esperar un poco más
        console.log('Esperando a que PayPal SDK esté disponible...');
        setTimeout(initializePayPalSetup, 500);
    }

    // Manejar eliminación de método de pago
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se eliminará tu método de pago guardado",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("cliente.pago.remove") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: data.message,
                            confirmButtonColor: '#FF6347'
                        }).then(() => {
                            window.location.reload();
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo eliminar el método de pago.',
                            confirmButtonColor: '#FF6347'
                        });
                    });
                }
            });
        });
    }
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePayPalSetup);
} else {
    initializePayPalSetup();
}
</script>

<style>
.saved-payment-display {
    padding: 2rem 0;
}

.payment-method-card {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 2rem;
    background-color: var(--main-bg);
    border-radius: 12px;
    border: 2px solid var(--border-color);
}

.payment-icon {
    flex-shrink: 0;
}

.payment-details h3 {
    margin: 0 0 0.5rem 0;
    color: var(--text-color-dark);
    font-size: 1.2rem;
}

.payment-details p {
    margin: 0;
    color: var(--text-color-semidark);
    font-size: 1rem;
}

.payment-details small {
    color: var(--text-color-light);
    font-size: 0.85rem;
}

.paypal-setup-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.info-box {
    text-align: center;
    padding: 1.5rem;
    background-color: var(--main-bg);
    border-radius: 10px;
    border: 1px solid var(--border-color);
}

.info-box i {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.info-box h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    color: var(--text-color-dark);
}

.info-box p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-color-light);
}

.payment-note {
    margin-top: 2rem;
    padding: 1rem;
    background-color: #fff3cd;
    border-left: 4px solid #ffc107;
    border-radius: 5px;
}

[data-theme="dark"] .payment-note {
    background-color: #3a3a2a;
    border-left-color: #ffc107;
}

.payment-note p {
    margin: 0;
    color: #856404;
    font-size: 0.9rem;
}

[data-theme="dark"] .payment-note p {
    color: #ffd966;
}

#paypal-setup-container {
    min-height: 150px;
}
</style>