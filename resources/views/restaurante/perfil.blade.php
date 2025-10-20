<div class="profile-container">
    {{-- Notificaciones de éxito o error --}}
    @if (session('success'))
        <div class="notification show" style="background-color: #28a745;">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="notification show error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Columna Izquierda: Avatar, Info y Botón de Contraseña --}}
    <aside class="profile-sidebar">
        <div class="profile-avatar-wrapper">
            <img src="{{ $user->profile_image_url ? Storage::url($user->profile_image_url) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=FF6347&color=fff&bold=true&size=150' }}" alt="Avatar del restaurante" class="profile-avatar" id="avatar-preview">
            <label for="profile_image_main" class="avatar-upload-button" title="Cambiar imagen" style="display: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
            </label>
        </div>
        <h2>{{ $user->full_name ?? 'Nombre no asignado' }}</h2>
        <p>{{ $user->email }}</p>

        {{-- Botón para desplegar el cambio de contraseña --}}
        <div class="profile-actions" style="margin-top: 1.5rem;">
            <button type="button" class="btn" id="toggle-password-form" style="background-color: transparent; border: 1px solid var(--border-color); color: var(--text-color-dark); width: 100%;">Cambiar Contraseña</button>
        </div>

        {{-- Sección oculta para Cambiar Contraseña --}}
        <div id="password-section" class="form-section" style="margin-top: 2rem; text-align: left; display: none;">
            <h3>Cambiar Contraseña</h3>
            <form action="{{ route('password.update') }}" method="POST" id="password-form">
                @csrf
                @method('PUT')
                <div class="input-group">
                    <label for="current_password">Contraseña Actual</label>
                    <input type="password" name="current_password" id="current_password" required>
                </div>
                <div class="input-group">
                    <label for="new_password">Nueva Contraseña</label>
                    <input type="password" name="new_password" id="new_password" required minlength="8">
                </div>
                <div class="input-group">
                    <label for="new_password_confirmation">Confirmar Contraseña</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-primary" style="background-color: #dc3545; width: 100%; margin-top: 1rem;">Actualizar Contraseña</button>
            </form>
        </div>
    </aside>

    {{-- Columna Derecha: Formulario de Información --}}
    <div class="profile-form-container">
        <form id="profile-form-main" action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <input type="file" name="profile_image" id="profile_image_main" accept="image/*" style="display: none;">

            <fieldset id="profile-fieldset" disabled>
                <div class="form-section">
                    <div class="form-header">
                        <h3>Información del Negocio</h3>
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" id="edit-button">Editar</button>
                            <button type="submit" class="btn" id="save-button" style="display: none; background-color: #28a745; color: white;">Guardar</button>
                            <button type="button" class="btn" id="cancel-button" style="display: none; background-color: #6c757d; color: white;">Cancelar</button>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="name">Nombre del Restaurante</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->full_name) }}" required pattern="[a-zA-Z0-9\sñÑáéíóúÁÉÍÓÚüÜ]+">
                    </div>
                    <div class="input-group">
                        <label for="description">Descripción (Bio)</label>
                        <textarea id="description" name="description" rows="4" style="resize: none;">{{ old('description', $user->restaurant_description) }}</textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Datos de Contacto y Ubicación</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="contact_phone">Teléfono de Contacto</label>
                            <input type="tel" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $user->contact_phone) }}" required pattern="[0-9]{10}">
                        </div>
                        <div class="input-group">
                            <label for="restaurant_address">Dirección del Local</label>
                            <input type="text" id="restaurant_address" name="restaurant_address" value="{{ old('restaurant_address', $user->restaurant_address) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Horarios</h3>
                    <div class="input-group">
                        <label for="opening_hours">Horario de Apertura</label>
                        <input type="text" id="opening_hours" name="opening_hours" value="{{ old('opening_hours', $user->opening_hours) }}" required placeholder="Ej: L-V: 9am - 6pm, S: 10am - 2pm">
                        <small class="form-hint">Describe tus horarios de atención.</small>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

{{-- Estilos para corregir el borde y la cabecera del formulario --}}
<style>
    .profile-form-container {
        box-shadow: none !important;
    }
    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.75rem;
        margin-bottom: 1.5rem;
    }
    .form-header h3 {
        margin: 0;
        border-bottom: none;
        padding-bottom: 0;
    }
</style>