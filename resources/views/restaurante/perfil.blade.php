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
        <h2>{{ $user->name ?? 'Nombre no asignado' }}</h2>
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
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required pattern="[a-zA-Z0-9\sñÑáéíóúÁÉÍÓÚüÜ]+">
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
                    <div class="input-group" style="display: flex; align-items: center; gap: 10px;">
                        @php
                            $horarios = explode(' - ', $user->opening_hours);
                            $opening_time = $horarios[0] ?? '';
                            $closing_time = $horarios[1] ?? '';
                        @endphp
                        <label for="opening_time" style="margin-bottom: 0;">De:</label>
                        <input type="time" name="opening_time" id="opening_time" value="{{ old('opening_time', $opening_time) }}" required>
                        <label for="closing_time" style="margin-bottom: 0;">a:</label>
                        <input type="time" name="closing_time" id="closing_time" value="{{ old('closing_time', $closing_time) }}" required>
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
        border: 1px solid var(--border-color);
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Previene que el script se ejecute múltiples veces
    if (window.profileScriptLoaded) return;
    window.profileScriptLoaded = true;

    // --- Lógica para desplegar el formulario de contraseña ---
    const togglePasswordBtn = document.getElementById('toggle-password-form');
    const passwordSection = document.getElementById('password-section');
    togglePasswordBtn.addEventListener('click', function() {
        const isHidden = passwordSection.style.display === 'none';
        passwordSection.style.display = isHidden ? 'block' : 'none';
        this.textContent = isHidden ? 'Ocultar' : 'Cambiar Contraseña';
    });
    
    // --- Lógica de Edición del Formulario Principal ---
    const editButton = document.getElementById('edit-button');
    const saveButton = document.getElementById('save-button');
    const cancelButton = document.getElementById('cancel-button');
    const profileFieldset = document.getElementById('profile-fieldset');
    const uploadButton = document.querySelector('.avatar-upload-button');
    const profileImageInput = document.getElementById('profile_image_main');
    const avatarPreview = document.getElementById('avatar-preview');
    
    // Almacenar valores originales para poder cancelar
    const originalValues = {};
    document.querySelectorAll('#profile-fieldset input, #profile-fieldset textarea').forEach(input => {
        originalValues[input.name] = input.value;
    });
    const originalImageSrc = avatarPreview.src;

    // Activar modo edición
    editButton.addEventListener('click', function() {
        profileFieldset.disabled = false;
        editButton.style.display = 'none';
        saveButton.style.display = 'inline-block';
        cancelButton.style.display = 'inline-block';
        uploadButton.style.display = 'flex';
    });

    // Cancelar edición
    cancelButton.addEventListener('click', function() {
        profileFieldset.disabled = true;
        editButton.style.display = 'inline-block';
        saveButton.style.display = 'none';
        cancelButton.style.display = 'none';
        uploadButton.style.display = 'none';
        
        // Restaurar valores originales
        document.querySelectorAll('#profile-fieldset input, #profile-fieldset textarea').forEach(input => {
            input.value = originalValues[input.name];
        });
        
        avatarPreview.src = originalImageSrc;
        profileImageInput.value = ''; // Limpiar el input de archivo
    });
    
    // Previsualizar imagen al seleccionarla
    profileImageInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = (event) => avatarPreview.src = event.target.result;
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Ocultar notificaciones después de 5 segundos
    setTimeout(() => {
        document.querySelectorAll('.notification.show').forEach(n => n.classList.remove('show'));
    }, 5000);
});
</script>