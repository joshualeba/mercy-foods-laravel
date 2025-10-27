<div class="profile-section-container">
    <h1>Mi perfil</h1>

    <form id="profile-form" action="{{ route('repartidor.perfil.update') }}" class="profile-form view-mode" novalidate>
        @csrf
        @method('PUT')

        <div class="profile-card">
            <h2>Datos personales y del vehículo</h2>
            <div class="profile-grid">
                {{-- Nombre --}}
                <div class="form-group span-2">
                    <label for="profile-full_name">Nombre completo</label>
                    <input type="text" id="profile-full_name" name="full_name" value="{{ $user->full_name }}" required readonly>
                </div>

                {{-- Teléfono de Contacto --}}
                <div class="form-group">
                    <label for="profile-contact_phone">Teléfono de contacto</label>
                    <input type="tel" id="profile-contact_phone" name="contact_phone" value="{{ $user->contact_phone }}" required pattern="\d{10}" readonly>
                    <small class="form-hint edit-mode-field">requerido, 10 dígitos.</small>
                    <small class="error-message"></small>
                </div>

                {{-- Tipo de Vehículo --}}
                <div class="form-group">
                    <label for="profile-vehicle_type">Tipo de vehículo</label>
                    <select id="profile-vehicle_type" name="vehicle_type" required disabled>
                        @php
                            $opciones = ['motocicleta', 'bicicleta', 'automovil'];
                        @endphp
                        <option value="" disabled {{ !$user->vehicle_type ? 'selected' : '' }}>selecciona un vehículo</option>
                        @foreach ($opciones as $opcion)
                            <option value="{{ $opcion }}" {{ $user->vehicle_type == $opcion ? 'selected' : '' }}>
                                {{ ucfirst($opcion) }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-hint edit-mode-field">requerido.</small>
                    <small class="error-message"></small>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <h2>Datos de acceso</h2>
            <div class="profile-grid">
                {{-- Correo Electrónico --}}
                <div class="form-group span-2">
                    <label for="profile-email">Correo electrónico</label>
                    <input type="email" id="profile-email" name="email" value="{{ $user->email }}" required readonly>
                </div>
            </div>
             
            {{-- Campos para cambiar contraseña --}}
            <div class="password-change-fields edit-mode-field">
                <p class="password-info">Deja los campos de contraseña en blanco si no deseas cambiarla.</p>
                
                <div class="form-group">
                    <label for="profile-current_password">Contraseña actual</label>
                    <div class="input-icon-wrapper">
                        <input type="password" id="profile-current_password" name="current_password" class="form-control" autocomplete="current-password">
                        <i class="toggle-password fas fa-eye"></i>
                    </div>
                    <small class="error-message"></small>
                </div>

                <div class="form-group">
                    <label for="profile-new_password">Nueva contraseña</label>
                    <div class="input-icon-wrapper">
                        <input type="password" id="profile-new_password" name="new_password" class="form-control" autocomplete="new-password">
                        <i class="toggle-password fas fa-eye"></i>
                    </div>
                    <small class="error-message"></small>
                </div>

                <div class="form-group">
                    <label for="profile-new_password_confirmation">Confirmar nueva contraseña</label>
                    <div class="input-icon-wrapper">
                        <input type="password" id="profile-new_password_confirmation" name="new_password_confirmation" class="form-control" autocomplete="new-password">
                        <i class="toggle-password fas fa-eye"></i>
                    </div>
                    <small class="error-message"></small>
                </div>

                <div id="password-requirements">
                    <ul class="password-checklist">
                        <li id="length">8-25 caracteres</li>
                        <li id="uppercase">una mayúscula</li>
                        <li id="special">un caracter especial (!@#$%)</li>
                    </ul>
                </div>
            </div>

            {{-- Botones de Acción --}}
            <div class="profile-actions">
                 <button type="button" class="btn btn-primary view-mode-btn" id="edit-profile-btn">Editar perfil</button>
                 <button type="submit" class="btn btn-confirm edit-mode-btn" id="save-profile-btn">Guardar cambios</button>
                 <button type="button" class="btn btn-cancel edit-mode-btn" id="cancel-profile-btn">Cancelar</button>
            </div>
        </div>
    </form>
</div>

<script>
    if (typeof initializeProfileSection === 'function') {
        initializeProfileSection();
    }
</script>