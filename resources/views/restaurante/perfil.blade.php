<div class="profile-section-container">
    <h1>Perfil del local</h1>

    <form id="profile-form" class="profile-form view-mode" novalidate>
        @csrf
        @method('PUT')

        <div class="profile-card">
            <h2>Datos del restaurante</h2>
            <div class="profile-grid">
                {{-- Nombre del restaurante --}}
                <div class="form-group span-2">
                    <label for="profile-full_name">Nombre del restaurante</label>
                    <input type="text" id="profile-full_name" name="full_name" value="{{ $user->full_name }}" required maxlength="120" readonly>
                    {{-- <small class="form-hint edit-mode-field" style="display: none;">Requerido. Máximo 120 caracteres.</small> --}} {{-- LÍNEA ELIMINADA --}}
                    <small class="error-message"></small>
                </div>
                {{-- Dirección --}}
                <div class="form-group span-2">
                    <label for="profile-restaurant_address">Dirección del local</label>
                    <input type="text" id="profile-restaurant_address" name="restaurant_address" value="{{ $user->restaurant_address }}" required maxlength="200" readonly>
                    <small class="form-hint edit-mode-field" style="display: none;">Requerido. Máximo 200 caracteres.</small>
                    <small class="error-message"></small>
                </div>
                {{-- Tipo de cocina --}}
                <div class="form-group">
                    <label for="profile-cuisine_type">Tipo de cocina</label>
                    <select id="profile_cuisine_type" name="cuisine_type" required>
                        @php
                            $opciones = ['mexicana', 'italiana', 'japonesa', 'americana', 'cafeteria', 'otro'];
                        @endphp
                        <option value="" disabled {{ !$user->cuisine_type ? 'selected' : '' }}>Selecciona una categoría</option>
                        @foreach ($opciones as $opcion)
                            <option value="{{ $opcion }}" {{ $user->cuisine_type == $opcion ? 'selected' : '' }}>
                                {{ ucfirst($opcion) }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-hint edit-mode-field" style="display: none;">Requerido. Selecciona una categoría.</small>
                    <small class="error-message"></small>
                </div>
                {{-- Teléfono --}}
                <div class="form-group">
                    <label for="profile-contact_phone">Teléfono de contacto</label>
                    <input type="tel" id="profile-contact_phone" name="contact_phone" value="{{ $user->contact_phone }}" required pattern="\d{10}" readonly>
                    <small class="form-hint edit-mode-field" style="display: none;">Requerido. 10 dígitos numéricos.</small>
                    <small class="error-message"></small>
                </div>
                {{-- Horario de atención --}}
                <div class="form-group span-2"> {{-- span-2 para que ocupe todo el ancho --}}
                    <label for="profile-attention_schedule">Horario de Atención</label>
                    {{-- Usamos la relación para obtener el valor --}}
                    <input type="text" id="profile-attention_schedule" name="attention_schedule" value="{{ $user->restaurantDetail ? $user->restaurantDetail->attention_schedule : '' }}" maxlength="255" readonly>
                    <small class="form-hint edit-mode-field" style="display: none;">Máximo 255 caracteres. Ej: L-V 9am-6pm.</small>
                    <small class="error-message"></small>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <h2>Datos de acceso</h2>
            <div class="profile-grid">
                {{-- Correo electrónico --}}
                <div class="form-group span-2">
                    <label for="profile-email">Correo electrónico</label>
                    <input type="email" id="profile-email" name="email" value="{{ $user->email }}" required maxlength="120" readonly>
                    {{-- <small class="form-hint edit-mode-field" style="display: none;">Requerido. Formato de email válido.</small> --}} {{-- LÍNEA ELIMINADA --}}
                    <small class="error-message"></small>
                </div>
            </div>
             
             {{-- Campos para cambiar contraseña (ocultos en modo vista) --}}
             <div class="password-change-fields edit-mode-field" style="display: none;">
                 <p class="password-info">Deja los campos de contraseña en blanco si no deseas cambiarla.</p>
                 <div class="profile-grid">
                    {{-- Contraseña actual --}}
                    <div class="form-group">
                        <label for="profile-current_password">Contraseña actual</label>
                        <input type="password" id="profile-current_password" name="current_password" autocomplete="current-password">
                        <small class="form-hint">Requerida si cambias la contraseña.</small>
                        <small class="error-message"></small>
                    </div>
                    {{-- Nueva contraseña --}}
                    <div class="form-group">
                        <label for="profile-new_password">Nueva contraseña</label>
                        <input type="password" id="profile-new_password" name="new_password" autocomplete="new-password">
                        <small class="form-hint">Mínimo 8 caracteres.</small>
                        <small class="error-message"></small>
                    </div>
                    {{-- Confirmar nueva contraseña --}}
                    <div class="form-group">
                        <label for="profile-new_password_confirmation">Confirmar nueva contraseña</label>
                        <input type="password" id="profile-new_password_confirmation" name="new_password_confirmation" autocomplete="new-password">
                        <small class="form-hint">Debe coincidir con la nueva contraseña.</small>
                        <small class="error-message"></small>
                    </div>
                 </div>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="profile-actions">
             <button type="button" class="btn btn-primary view-mode-btn" id="edit-profile-btn">Editar Perfil</button>
             <button type="submit" class="btn btn-confirm edit-mode-btn" id="save-profile-btn" style="display:none;">Guardar Cambios</button>
             <button type="button" class="btn btn-cancel edit-mode-btn" id="cancel-profile-btn" style="display:none;">Cancelar</button>
        </div>
    </form>
</div>

{{-- Script para inicializar la lógica JS específica de esta vista --}}
<script>
    if (typeof initializeProfileSection === 'function') {
        initializeProfileSection();
    } else {
        console.error('La función initializeProfileSection no está definida en dashboard.js');
    }
</script>