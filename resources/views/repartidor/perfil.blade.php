<div class="profile-container">
    <h2>Mi perfil</h2>
    <p>actualiza tu información personal y de contacto.</p>

    <form action="{{ route('repartidor.profile.update') }}" method="POST" class="profile-form">
        @csrf
        @method('PUT')

        <div class="form-section">
            <div class="form-header">
                <h3>Información personal</h3>
                <p>estos datos son privados y no se mostrarán a otros usuarios.</p>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Nombre completo</label>
                    <input type="text" id="full_name" name="full_name" value="{{ Auth::user()->full_name }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone_number">Número de teléfono</label>
                    <input type="tel" id="phone_number" name="phone_number" value="{{ Auth::user()->phone_number }}" required>
                </div>
                <div class="form-group">
                    <label for="password">Nueva contraseña (opcional)</label>
                    <input type="password" id="password" name="password" placeholder="deja en blanco para no cambiar">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Guardar cambios</button>
        </div>
    </form>
</div>