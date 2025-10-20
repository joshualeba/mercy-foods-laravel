<div class="profile-container">
    <aside class="profile-sidebar">
        <form id="profile-form">
            <div class="profile-avatar-wrapper">
                <img src="{{ $user->profile_image_url ?: 'https://ui-avatars.com/api/?name='.urlencode($user->restaurant_name ?: $user->full_name).'&background=FF6347&color=fff&bold=true&size=150' }}" alt="Avatar del restaurante" class="profile-avatar" id="avatar-preview">
                <label for="profile_image" class="avatar-upload-button" title="Cambiar imagen">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                </label>
                <input type="file" name="profile_image" id="profile_image" accept="image/*">
            </div>
            <h2 id="restaurant-name-preview">{{ $user->restaurant_name ?: 'Nombre no asignado' }}</h2>
            <p>{{ $user->email }}</p>
        </form>
    </aside>

    <div class="profile-form-container">
        <div class="form-section">
            <h3>Información del Negocio</h3>
            <div class="form-grid">
                <div class="input-group">
                    <label for="restaurant_name">Nombre del Restaurante</label>
                    <input type="text" id="restaurant_name" name="restaurant_name" value="{{ old('restaurant_name', $user->restaurant_name) }}" required>
                    <small class="error-message"></small>
                </div>
                <div class="input-group">
                    <label for="cuisine_type">Tipo de Cocina</label>
                    <input type="text" id="cuisine_type" name="cuisine_type" value="{{ old('cuisine_type', $user->cuisine_type) }}" required>
                    <small class="error-message"></small>
                </div>
            </div>
            <div class="input-group" style="margin-top: 1.5rem;">
                <label for="restaurant_description">Descripción del Restaurante (Bio)</label>
                <textarea id="restaurant_description" name="restaurant_description" rows="4" placeholder="Ej: Las mejores pizzas al horno de leña de la ciudad, con ingredientes frescos y un toque artesanal.">{{ old('restaurant_description', $user->restaurant_description) }}</textarea>
                <small class="error-message"></small>
            </div>
        </div>

        <div class="form-section">
            <h3>Datos de Contacto y Ubicación</h3>
            <div class="form-grid">
                <div class="input-group">
                    <label for="full_name">Nombre del Propietario</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" required>
                    <small class="error-message"></small>
                </div>
                <div class="input-group">
                    <label for="contact_phone">Teléfono de Contacto</label>
                    <input type="tel" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $user->contact_phone) }}" required>
                    <small class="error-message"></small>
                </div>
                <div class="input-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    <small class="error-message"></small>
                </div>
                <div class="input-group">
                    <label for="restaurant_address">Dirección del Local</label>
                    <input type="text" id="restaurant_address" name="restaurant_address" value="{{ old('restaurant_address', $user->restaurant_address) }}" required>
                    <small class="error-message"></small>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Horarios</h3>
            <div class="input-group">
                <label for="opening_hours">Horario de Apertura</label>
                <input type="text" id="opening_hours" name="opening_hours" value="{{ old('opening_hours', $user->opening_hours) }}" required>
                <small class="error-message"></small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="profile-save-btn">Guardar Cambios</button>
        </div>
    </div>
</div>

<div class="notification" id="notification-banner"></div>

<script>
// Para asegurar que el script se ejecute solo una vez.
if (typeof profileScriptLoaded === 'undefined') {
    const profileScriptLoaded = true;

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('profile-form');
        const saveBtn = document.getElementById('profile-save-btn');
        const avatarInput = document.getElementById('profile_image');
        const avatarPreview = document.getElementById('avatar-preview');
        const topbarAvatar = document.querySelector('.profile-pic');
        const restaurantNamePreview = document.getElementById('restaurant-name-preview');

        // Previsualizar imagen de perfil
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    avatarPreview.src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Guardar cambios del perfil
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Recolectar todos los datos del formulario, incluida la imagen
            const formData = new FormData();
            formData.append('restaurant_name', document.getElementById('restaurant_name').value);
            formData.append('full_name', document.getElementById('full_name').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('contact_phone', document.getElementById('contact_phone').value);
            formData.append('restaurant_address', document.getElementById('restaurant_address').value);
            formData.append('cuisine_type', document.getElementById('cuisine_type').value);
            formData.append('opening_hours', document.getElementById('opening_hours').value);
            formData.append('restaurant_description', document.getElementById('restaurant_description').value);
            
            if (avatarInput.files[0]) {
                formData.append('profile_image', avatarInput.files[0]);
            }

            // Limpiar errores previos
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

            fetch('{{ route("perfil.update") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                showNotification(data.message, false);
                // Actualizar UI con nuevos datos
                restaurantNamePreview.textContent = data.user.restaurant_name;
                if (data.user.profile_image_url) {
                    // Añadimos un timestamp para evitar problemas de caché del navegador
                    topbarAvatar.src = data.user.profile_image_url + '?t=' + new Date().getTime();
                }
            })
            .catch(error => {
                if (error.errors) {
                    showNotification('Por favor, corrige los errores.', true);
                    for (const key in error.errors) {
                        const input = document.getElementById(key);
                        const errorContainer = input ? input.nextElementSibling : null;
                        if (errorContainer && errorContainer.classList.contains('error-message')) {
                            errorContainer.textContent = error.errors[key][0];
                        }
                    }
                } else {
                    showNotification('Ocurrió un error inesperado.', true);
                }
            });
        });

        function showNotification(message, isError = false) {
            const banner = document.getElementById('notification-banner');
            banner.textContent = message;
            banner.className = 'notification'; // Resetea clases
            if (isError) {
                banner.classList.add('error');
            }
            banner.classList.add('show');
            setTimeout(() => {
                banner.classList.remove('show');
            }, 3000);
        }
    });
}
</script>