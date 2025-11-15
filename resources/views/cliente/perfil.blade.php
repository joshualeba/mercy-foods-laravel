<div class="profile-section-container">
    <h1>Mi perfil</h1>

    <form id="profile-form" action="{{ route('cliente.perfil.update') }}" class="profile-form view-mode" novalidate>
        @csrf
        @method('PUT')

        <div class="profile-card">
            <h2>Datos personales</h2>
            <div class="profile-grid">
                {{-- Nombre --}}
                <div class="form-group span-2">
                    <label for="profile-full_name">Nombre</label>
                    {{-- AÑADIDA LA CLASE 'is-editable' --}}
                    <input type="text" id="profile-full_name" name="full_name" value="{{ $user->full_name }}" class="is-editable" readonly>
                </div>
                {{-- Dirección --}}
                <div class="form-group span-2">
                    <label for="profile-address">Dirección de entrega</label>
                    {{-- AÑADIDA LA CLASE 'is-editable' --}}
                    <input type="text" id="profile-address" name="address" value="{{ $user->address }}" maxlength="200" class="is-editable" readonly>
                    <small class="form-hint edit-mode-field" style="display: none;">Máximo 200 caracteres.</small>
                    <small class="error-message"></small>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <h2>Datos de acceso</h2>
            <div class="profile-grid">
                {{-- Correo electrónico (SIN CLASE, NO EDITABLE) --}}
                <div class="form-group span-2">
                    <label for="profile-email">Correo electrónico</label>
                    <input type="email" id="profile-email" name="email" value="{{ $user->email }}" readonly>
                </div>
            </div>

            {{-- Campos para cambiar contraseña --}}
            @if(!$user->google_id)
                <div class="password-change-fields edit-mode-field" style="display: none;">
                    {{-- ... (contenido de contraseña sin cambios) ... --}}
                    <p class="password-info">Deja los campos de contraseña en blanco si no deseas cambiarla.</p>

                    {{-- Contraseña actual --}}
                    <div class="form-group">
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

            {{-- Botones de acción --}}
            <div class="profile-actions">
                 <button type="button" class="btn btn-primary view-mode-btn" id="edit-profile-btn">Editar perfil</button>
                 <button type="submit" class="btn btn-confirm edit-mode-btn" id="save-profile-btn" style="display:none;">Guardar cambios</button>
                 <button type="button" class="btn btn-cancel edit-mode-btn" id="cancel-profile-btn" style="display:none;">Cancelar</button>
            </div>
    </form>

    <div class="profile-form" style="margin-top: 2rem;">
        <div class="profile-card">
            <h2>Método de pago agregados a esta cuenta</h2>

            @if(Auth::user()->card_last_four)
                {{-- Contenedor que muestra los datos de la tarjeta --}}
                <div class="profile-grid">
                    {{-- Nombre del titular --}}
                    <div class="form-group span-2">
                        <label>Nombre del titular</label>
                        <input type="text" value="{{ Auth::user()->card_name }}" readonly>
                    </div>

                    {{-- Número de tarjeta --}}
                    <div class="form-group">
                        <label>Tarjeta registrada</label>
                        <input type="text" value="**** **** **** {{ Auth::user()->card_last_four }}" readonly>
                    </div>
                    
                    {{-- Fecha de expiración --}}
                    <div class="form-group">
                        <label>Fecha de expiración</label>
                        <input type="text" value="{{ Auth::user()->card_expiry }}" readonly>
                    </div>
                </div>

                {{-- Botón para eliminar el método de pago (centrado) --}}
                <div class="text-center" style="padding: 1rem 0;">
                    <button id="delete-payment-method-btn" class="btn btn-danger" style="border-radius: 8px; padding: 10px 20px;">
                        <i class="fas fa-trash-alt"></i> Eliminar este método de pago
                    </button>
                </div>

            @else
                {{-- Mensaje y botón para agregar método si no existe --}}
                <div class="text-center" style="padding: 1rem 0;">
                    <p>No tienes ningún método de pago guardado.</p>
                    <button id="add-payment-method-from-profile" class="btn btn-primary" style="border-radius: 8px; padding: 10px 20px;">
                        <i class="fas fa-plus"></i> Agregar método de pago
                    </button>
                </div>
            @endif
        </div>
    </div>
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