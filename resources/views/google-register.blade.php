<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completa tu Registro - Mercy Food</title>
    
    <link rel="shortcut icon" href="{{ asset('multimedia/logo.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/registro.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
</head>
<body>

    <div id="loader-wrapper">
        <div class="loadingspinner">
            <div id="square1"></div><div id="square2"></div><div id="square3"></div><div id="square4"></div><div id="square5"></div>
        </div>
        <span id="loader-text">Cargando tu experiencia...</span>
    </div>

    <div class="split-screen-container">
        <div class="left-pane">
            <div class="video-background">
                <video playsinline autoplay muted loop><source src="{{ asset('multimedia/r_video.mp4') }}" type="video/mp4"></video>
            </div>
            <div class="video-overlay"></div>
            <a href="/" class="return-link"><i class="fas fa-arrow-left"></i><span>Regresar</span></a>
            <a href="/" class="logo-corner"><img src="{{ asset('multimedia/logo.png') }}" alt="Mercy Food Logo"></a>
        </div>

        <div class="right-pane dark-mode">
            <div class="form-container-glass">
                <h2>Completa tu registro</h2>
                <p style="text-align: center; margin-top: -1.5rem; margin-bottom: 1.5rem; color: #ccc;">
                    Estás iniciando con Google. Solo necesitamos saber qué tipo de usuario eres.
                </p>
                
                <div class="tabs">
                    <button class="tab-link active" data-tab="cliente">Soy cliente</button>
                    <button class="tab-link" data-tab="restaurante">Soy restaurante</button>
                    <button class="tab-link" data-tab="repartidor">Soy repartidor</button>
                </div>

                <form id="google-register-form" method="POST" action="{{ route('google.register.process') }}">
                    @csrf <input type="hidden" id="role-input" name="role" value="cliente">

                    <div class="input-group">
                        <label for="google-name">Nombre (de Google)</label>
                        <input type="text" id="google-name" value="{{ $full_name ?? '' }}" disabled style="background: rgba(0,0,0,0.4); color: #999;">
                    </div>
                    <div class="input-group">
                        <label for="google-email">Email (de Google)</label>
                        <input type="email" id="google-email" value="{{ $email ?? '' }}" disabled style="background: rgba(0,0,0,0.4); color: #999;">
                    </div>

                    <hr style="border-color: rgba(255,255,255,0.1); margin: 2rem 0;">

                    <div id="cliente" class="tab-content active">
                        <p style="color: #ccc; text-align: center;">Si quieres continuar como cliente, solo presiona el botón de abajo.</p>
                    </div>
                    
                    <div id="restaurante" class="tab-content">
                        <div class="input-group">
                            <label for="restaurante-nombre">Nombre del restaurante</label>
                            <input type="text" id="restaurante-nombre" name="restaurant_name" placeholder="Pitzeria Mercy" value="{{ old('restaurant_name') }}">
                            <small class="error-message">{{ $errors->first('restaurant_name') }}</small>
                        </div>
                        <div class="input-group">
                            <label for="restaurante-direccion">Dirección del local</label>
                            <input type="text" id="restaurante-direccion" name="restaurant_address" placeholder="Av. Tepito" value="{{ old('restaurant_address') }}">
                            <small class="error-message">{{ $errors->first('restaurant_address') }}</small>
                        </div>
                        <div class="input-group">
                            <label for="restaurante-tipo">Tipo de cocina</label>
                            <select id="restaurante-tipo" name="cuisine_type">
                                <option value="" disabled selected>Selecciona una categoría</option>
                                <option value="mexicana" @selected(old('cuisine_type') == 'mexicana')>Mexicana</option>
                                <option value="italiana" @selected(old('cuisine_type') == 'italiana')>Italiana</option>
                                <option value="japonesa" @selected(old('cuisine_type') == 'japonesa')>Japonesa</option>
                                <option value="americana" @selected(old('cuisine_type') == 'americana')>Americana</option>
                                <option value="cafeteria" @selected(old('cuisine_type') == 'cafeteria')>Cafetería</option>
                                <option value="otro" @selected(old('cuisine_type') == 'otro')>Otro</option>
                            </select>
                            <small class="error-message">{{ $errors->first('cuisine_type') }}</small>
                        </div>
                        <div class="input-group">
                            <label for="restaurante-telefono">Teléfono de contacto</label>
                            <input type="tel" id="restaurante-telefono" name="contact_phone" placeholder="Ej. 4421234567" value="{{ old('contact_phone') }}">
                            <small class="error-message">{{ $errors->first('contact_phone') }}</small>
                        </div>
                    </div>

                    <div id="repartidor" class="tab-content">
                        <div class="input-group">
                            <label for="repartidor-vehiculo">Tipo de vehículo</label>
                            <select id="repartidor-vehiculo" name="vehicle_type">
                                <option value="" disabled selected>Selecciona tu vehículo</option>
                                <option value="motocicleta" @selected(old('vehicle_type') == 'motocicleta')>Motocicleta</option>
                                <option value="bicicleta" @selected(old('vehicle_type') == 'bicicleta')>Bicicleta</option>
                                <option value="automovil" @selected(old('vehicle_type') == 'automovil')>Automóvil</option>
                            </select>
                            <small class="error-message">{{ $errors->first('vehicle_type') }}</small>
                        </div>
                    </div>

                    <div class="terms-group">
                        <input type="checkbox" id="terms-check" name="terms" required>
                        <label for="terms-check">Acepto los <a id="open-terms-link">Términos y condiciones</a></label>
                    </div>

                    <button type="submit" class="submit-btn">Completar registro</button>
                </form>

                <p class="login-redirect">¿Cancelar registro? <a href="/login">Volver a inicio de sesión</a></p>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="terms-modal">
        <div class="modal-glass">
            <button class="modal-close-btn" id="close-terms-btn">&times;</button>
            <h2>Términos y condiciones</h2>
            <div class="modal-content">
                <p><strong>Última actualización: 28 de septiembre de 2025</strong></p>
                <p>Bienvenido a Mercy Food. Al utilizar nuestra plataforma, aceptas los siguientes términos y condiciones que rigen la relación entre los clientes, restaurantes, repartidores y nosotros.</p>
                
                <h4>1. Aceptación de los términos</h4>
                <p>Al crear una cuenta o utilizar los servicios de Mercy Food, confirmas que has leído, entendido y aceptado estar sujeto a estos Términos y Condiciones. Si no estás de acuerdo con alguna parte de los términos, no podrás utilizar nuestros servicios.</p>

                <h4>2. Descripción del servicio</h4>
                <p>Mercy Food es una plataforma tecnológica que actúa como intermediario para conectar a:</p>
                <ul>
                    <li><strong>Clientes:</strong> que buscan y piden comida de establecimientos locales.</li>
                    <li><strong>Restaurantes Socios:</strong> que ofrecen sus productos a través de nuestro catálogo digital.</li>
                    <li><strong>Repartidores:</strong> que prestan servicios de entrega de manera independiente.</li>
                </ul>

                <h4>3. Cuentas de usuario</h4>
                <p>Eres responsable de mantener la confidencialidad de tu cuenta y contraseña. Te comprometes a notificar a Mercy Food sobre cualquier uso no autorizado de tu cuenta. Debes proporcionar información precisa y completa al registrarte.</p>

                <h4>4. Pedidos, pagos y entregas</h4>
                <ul>
                    <li><strong>Pedidos:</strong> Al realizar un pedido, entras en un contrato directo con el restaurante socio para la preparación de tu comida.</li>
                    <li><strong>Pagos:</strong> Todos los pagos se procesan a través de nuestra plataforma segura. Aseguramos la encriptación de tu información financiera bajo el estándar PCI DSS.</li>
                    <li><strong>Entregas:</strong> El tiempo de entrega es un estimado y puede variar. Nuestra plataforma asigna al repartidor más cercano para optimizar la logística.</li>
                </ul>

                <h4>5. Obligaciones de restaurantes y repartidores</h4>
                <ul>
                    <li><strong>Restaurantes:</strong> Son responsables de la calidad de la comida, la precisión del menú y de actualizar su información (horarios, disponibilidad).</li>
                    <li><strong>Repartidores:</strong> Actúan como contratistas independientes y son responsables de realizar la entrega de manera segura y profesional.</li>
                </ul>

                <h4>6. Sistema de valoraciones</h4>
                <p>Después de cada pedido completado, los clientes pueden calificar y dejar comentarios sobre el restaurante y el repartidor. Estas valoraciones son públicas y ayudan a mantener la calidad del servicio. Mercy Food se reserva el derecho de moderar o eliminar comentarios que infrinjan nuestras políticas.</p>

                <h4>7. Limitación de responsabilidad</h4>
                <p>Mercy Food no se hace responsable de la calidad de los productos ofrecidos por los restaurantes ni de incidentes durante la entrega por parte de los repartidores. Nuestra responsabilidad se limita a la correcta operación de la plataforma tecnológica.</p>
            </div>
        </div>
    </div>

    <div class="notification-modal-overlay" id="notification-modal">
        <div class="notification-modal-glass">
            <p id="modal-message">Este es el contenido del mensaje.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- JS para las pestañas ---
            const tabs = document.querySelectorAll('.tab-link');
            const contents = document.querySelectorAll('.tab-content');
            const roleInput = document.getElementById('role-input');

            if (tabs.length > 0 && contents.length > 0 && roleInput) {
                tabs.forEach(tab => {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault();
                        tabs.forEach(t => t.classList.remove('active'));
                        contents.forEach(c => c.classList.remove('active'));

                        this.classList.add('active');
                        const targetContent = document.getElementById(this.dataset.tab);
                        if (targetContent) {
                            targetContent.classList.add('active');
                        }
                        roleInput.value = this.dataset.tab; // Actualiza el rol
                    });
                });
            }

            // --- JS para el modal de términos ---
            const openTerms = document.getElementById('open-terms-link');
            const closeTerms = document.getElementById('close-terms-btn');
            const termsModal = document.getElementById('terms-modal');

            if (openTerms && termsModal) {
                openTerms.addEventListener('click', (e) => {
                    e.preventDefault(); 
                    termsModal.classList.add('active');
                });
            }
            if (closeTerms && termsModal) {
                closeTerms.addEventListener('click', (e) => {
                    e.preventDefault();
                    termsModal.classList.remove('active');
                });
            }

            // --- JS para ocultar el loader ---
            const loader = document.getElementById('loader-wrapper');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500); // 500ms de transición
            }

            // Cierra el modal si se hace clic en el fondo (overlay)
            if (termsModal) {
                termsModal.addEventListener('click', function(e) {
                    // Comprueba si el clic fue en el overlay (this)
                    // y no en un elemento hijo (e.target)
                    if (e.target === this) {
                        termsModal.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>