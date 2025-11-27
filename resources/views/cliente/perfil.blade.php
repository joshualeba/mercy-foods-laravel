<div class="profile-section-container">
    <div class="profile-header">
        <h1>Mi perfil</h1>
        <button type="button" class="btn btn-primary view-mode-btn" id="edit-profile-btn">Editar perfil</button>
    </div>

    <form id="profile-form" action="{{ route('cliente.perfil.update') }}" class="profile-form view-mode" novalidate>
        @csrf
        @method('PUT')

        <div class="profile-card">
            <h2>Datos personales</h2>
            <div class="profile-grid">
                {{-- Nombre --}}
                <div class="form-group span-2">
                    <label for="profile-full_name">Nombre</label>
                    <input type="text" id="profile-full_name" name="full_name" value="{{ $user->full_name }}" class="is-editable" readonly>
                </div>
                
                {{-- Correo electrónico --}}
                <div class="form-group span-2">
                    <label for="profile-email">Correo electrónico</label>
                    <input type="email" id="profile-email" name="email" value="{{ $user->email }}" readonly>
                    <small class="form-hint">El correo no se puede modificar</small>
                </div>
                
                {{-- Dirección --}}
                <div class="form-group span-2">
                    <label for="profile-address">Dirección de entrega</label>
                    <input type="text" id="profile-address" name="address" value="{{ $user->address }}" maxlength="200" class="is-editable" readonly>
                    <small class="form-hint edit-mode-field" style="display: none;">Máximo 200 caracteres.</small>
                    <small class="error-message"></small>
                </div>
            </div>

            @if(!$user->google_id)
                {{-- Campos para cambiar contraseña (solo en modo edición) --}}
                <div class="password-change-fields edit-mode-field" style="display: none;">
                    <h3 style="margin-top: 2rem; margin-bottom: 1rem;">Cambiar contraseña (opcional)</h3>
                    <p class="password-info">Deja los campos de contraseña en blanco si no deseas cambiarla.</p>

                    <div class="profile-grid">
                        {{-- Contraseña actual --}}
                        <div class="form-group span-2">
                            <label for="profile-current_password">Contraseña actual</label>
                            <div class="input-icon-wrapper">
                                <input type="password" id="profile-current_password" name="current_password" class="form-control" autocomplete="current-password">
                                <i class="toggle-password fas fa-eye"></i>
                            </div>
                            <small class="error-message"></small>
                        </div>

                        {{-- Nueva contraseña --}}
                        <div class="form-group">
                            <label for="profile-new_password">Nueva contraseña</label>
                            <div class="input-icon-wrapper">
                                <input type="password" id="profile-new_password" name="new_password" class="form-control" autocomplete="new-password">
                                <i class="toggle-password fas fa-eye"></i>
                            </div>
                            <small class="error-message"></small>
                        </div>

                        {{-- Confirmar nueva contraseña --}}
                        <div class="form-group">
                            <label for="profile-new_password_confirmation">Confirmar nueva contraseña</label>
                            <div class="input-icon-wrapper">
                                <input type="password" id="profile-new_password_confirmation" name="new_password_confirmation" class="form-control" autocomplete="new-password">
                                <i class="toggle-password fas fa-eye"></i>
                            </div>
                            <small class="error-message"></small>
                        </div>
                    </div>

                    {{-- Lista de Requisitos de Contraseña --}}
                    <div id="password-requirements">
                        <ul class="password-checklist">
                            <li id="length">8-25 caracteres</li>
                            <li id="uppercase">Una mayúscula</li>
                            <li id="special">Un caracter especial (!@#$%)</li>
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Botones de acción (ahora solo para guardar/cancelar en modo edición) --}}
            <div class="profile-actions" style="margin-top: 1rem;">
                 <button type="submit" class="btn btn-confirm edit-mode-btn" id="save-profile-btn" style="display:none;">Guardar cambios</button>
                 <button type="button" class="btn btn-cancel edit-mode-btn" id="cancel-profile-btn" style="display:none;">Cancelar</button>
            </div>
        </div>
    </form>

    {{-- Sección de Método de Pago --}}
    <div class="profile-form" style="margin-top: 2rem;">
        <div class="profile-card">
            <h2>Método de pago</h2>

            @if(Auth::user()->paypal_email)
                {{-- Mostrar método de pago guardado --}}
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

                <div class="profile-actions" style="margin-top: 1.5rem;">
                    <button id="remove-payment-method-btn" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Eliminar método de pago
                    </button>
                </div>
            @else
                <p>No tienes ningún método de pago guardado.</p>
                <button class="btn btn-primary" onclick="document.querySelector('.nav-link[data-section=&quot;pago&quot;]').click();" style="margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Agregar método de pago
                </button>
            @endif
        </div>
    </div>
    </form>
</div>

<div class="confirmation-modal-overlay" id="success-modal">
    <div class="modal-box">
        <div class="icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>¡Éxito!</h2>
        <p id="success-message">Tus cambios se han guardado correctamente.</p>
        <div class="modal-buttons">
            <button class="btn-confirm btn-modal-close">Aceptar</button>
        </div>
    </div>
</div>

<div class="confirmation-modal-overlay" id="error-modal">
    <div class="modal-box">
        <div class="icon error">
            <i class="fas fa-times-circle"></i>
        </div>
        <h2>¡Oops! Hubo un error</h2>
        <p id="error-message">No se pudieron guardar los cambios. Por favor, revisa los datos e intenta de nuevo.</p>
        <div class="modal-buttons">
            <button class="btn-confirm btn-modal-close">Entendido</button>
        </div>
    </div>
</div>

<script>
    if (typeof initializeProfileSection === 'function') {
        initializeProfileSection();
    } else {
        console.error('La función initializeProfileSection no está definida en dashboard.js');
    }
</script>