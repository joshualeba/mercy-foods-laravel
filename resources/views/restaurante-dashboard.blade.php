<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel de restaurante - Mercy Food</title>
    <link rel="shortcut icon" href="{{ asset('multimedia/logo.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body data-theme="light">

    <div id="loader-wrapper">
        <div class="loadingspinner">
            <div id="square1"></div>
            <div id="square2"></div>
            <div id="square3"></div>
            <div id="square4"></div>
            <div id="square5"></div>
        </div>
        <span id="loader-text">Cargando tu experiencia...</span>
    </div>

    <div class="dashboard-container">
        <aside class="sidebar" id="sidebar">
            <ul class="sidebar-nav">
                <li><a href="#inicio" class="nav-link active" data-section="inicio">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Inicio</span>
                </a></li>
                <li><a href="{{ route('restaurante.pedidos.index') }}" class="nav-link" data-section="pedidos">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                    <span>Gestionar pedidos</span>
                </a></li>
                <li><a href="{{ route('platillos.index') }}" class="nav-link" data-section="menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                    <span>Mi menú</span>
                </a></li>
                <li><a href="{{ route('restaurante.perfil.index') }}" class="nav-link" data-section="perfil">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>Perfil del local</span>
                </a></li>
            </ul>
            <div class="logout-link" id="logout-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span>Cerrar sesión</span>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <button class="menu-toggle" id="menu-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                <a href="#" class="topbar-brand">
                    <img src="{{ asset('multimedia/logo.png') }}" alt="Mercy Food Logo" class="topbar-logo">
                    <span class="topbar-brand-text">Mercy Food</span>
                </a>
                <div class="topbar-right">
                    <div class="theme-switch-wrapper">
                        <label class="theme-switch" for="theme-toggle"><input type="checkbox" id="theme-toggle" /><div class="slider round"></div></label>
                    </div>
                    <div class="profile-menu">
                        <button class="profile-toggle" id="profile-toggle">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=FF6347&color=fff&bold=true" alt="User Avatar" class="profile-pic">
                        </button>
                        <div class="profile-dropdown" id="profile-dropdown">
                            <div class="dropdown-header">
                                <h4>{{ Auth::user()->full_name }}</h4>
                                <p>{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('restaurante.perfil.index') }}" class="dropdown-item nav-link" data-section="perfil"> {{-- Cambiado href --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span>Ver perfil</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="main-content-inner" id="main-dynamic-content">
                <section id="inicio" class="dashboard-section active">
                    <div class="welcome-message">
                        <h1>Bienvenido/a, {{ Auth::user()->full_name }}</h1>
                        <p>Conectando tu cocina con el apetito de la comunidad</p>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon icon-pedidos">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                                </div>
                                <div class="stat-card-content">
                                    <p>Pedidos Activos</p>
                                    <h3>0</h3>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <a href="#pedidos" class="card-link nav-link" data-section="pedidos">
                                    <span>Gestionar pedidos</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon icon-menu">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                                </div>
                                <div class="stat-card-content">
                                    <p>Platillos en tu menú</p>
                                    <h3>{{ Auth::user()->platillos->count() }}</h3>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <a href="{{ route('platillos.index') }}" class="card-link nav-link" data-section="menu">
                                    <span>Administrar menú</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon icon-ganancias">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                </div>
                                <div class="stat-card-content">
                                    <p>Ingresos del día</p>
                                    <h3>$0.00</h3>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <a href="#" class="card-link">
                                    <span>Ver estadísticas</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon icon-perfil">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                </div>
                                <div class="stat-card-content">
                                    <p>Tu Perfil</p>
                                    <h3>Completo</h3>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <a href="#perfil" class="card-link nav-link" data-section="perfil">
                                    <span>Editar información</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="pedidos" class="dashboard-section">
                    {{-- El contenido se cargará dinámicamente desde el controlador --}}
                </section>

                <div id="ajax-content-wrapper"></div>
            </div>
        </main>
    </div>

    <div class="confirmation-modal-overlay" id="confirmation-modal">
        <div class="modal-box">
            <h2>Confirmar cierre de sesión</h2>
            <p>¿Estás seguro de que quieres salir de tu cuenta?</p>
            <div class="modal-buttons">
                <button class="btn-cancel" id="cancel-logout-btn">Cancelar</button>
                <button class="btn-confirm" id="confirm-logout-btn">Confirmar</button>
            </div>
        </div>
    </div>

    <div class="confirmation-modal-overlay" id="platillo-modal">
        <div class="modal-box glass-modal">
            <h2>Agregar nuevo platillo</h2>
            <form id="platillo-form" action="{{ route('platillos.store') }}" enctype="multipart/form-data" novalidate>
                <div class="input-group-modal">
                    <label for="nombre">Nombre del Platillo</label>
                    <input type="text" id="nombre" name="nombre" required maxlength="50">
                    <small class="form-hint">Máximo 50 caracteres.</small>
                    <small class="error-message"></small>
                </div>
                <div class="input-group-modal">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" required maxlength="150"></textarea>
                    <small class="form-hint">Máximo 150 caracteres.</small>
                    <small class="error-message"></small>
                </div>
                <div class="input-group-modal">
                    <label for="precio">Precio</label>
                    <input type="text" id="precio" name="precio" inputmode="decimal" required placeholder="Ej: 120.50">
                    <small class="form-hint">Solo números y punto decimal.</small>
                    <small class="error-message"></small>
                </div>
                <div class="input-group-modal">
                    <label for="imagen">Imagen del Platillo</label>
                    <div class="file-input-wrapper">
                        <label for="imagen" class="file-input-button">Seleccionar archivo</label>
                        <span id="file-name" class="file-name-display">Ningún archivo seleccionado</span>
                        <input type="file" id="imagen" name="imagen" accept="image/*" required>
                    </div>
                    <small class="form-hint">Tamaño máximo: 2MB.</small>
                    <small class="error-message"></small>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" id="cancel-platillo-btn">Cancelar</button>
                    <button type="submit" class="btn-confirm">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="confirmation-modal-overlay" id="details-modal">
        <div class="modal-box glass-modal">
            <button type="button" class="close-modal-btn" id="close-details-btn">&times;</button>
            <h2 id="details-modal-title">Detalles del platillo</h2>
            
            <form id="details-form" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="_method" value="PUT">
                
                <div class="modal-image-container">
                    <img id="details-image" src="" alt="Imagen del platillo">
                </div>

                <div class="form-scroll-content">
                    <div class="input-group-modal">
                        <label for="details-nombre">Nombre del Platillo</label>
                        <input type="text" id="details-nombre" name="nombre" required maxlength="50" readonly>
                        <small class="form-hint edit-mode-field" style="display: none;">Máximo 50 caracteres.</small>
                        <small class="error-message"></small>
                    </div>
                    <div class="input-group-modal">
                        <label for="details-descripcion">Descripción</label>
                        <textarea id="details-descripcion" name="descripcion" rows="4" required maxlength="150" readonly></textarea>
                        <small class="form-hint edit-mode-field" style="display: none;">Máximo 150 caracteres.</small>
                        <small class="error-message"></small>
                    </div>
                    <div class="input-group-modal">
                        <label for="details-precio">Precio</label>
                        <input type="text" id="details-precio" name="precio" inputmode="decimal" required placeholder="Ej: 120.50" readonly>
                        <small class="form-hint edit-mode-field" style="display: none;">Solo números y punto decimal.</small>
                        <small class="error-message"></small>
                    </div>
                    <div class="input-group-modal edit-mode-field" style="display: none;">
                        <label for="details-new-image">Cambiar Imagen (opcional)</label>
                        <div class="file-input-wrapper">
                            <label for="details-new-image" class="file-input-button">Seleccionar archivo</label>
                            <span id="details-file-name" class="file-name-display">Ningún archivo nuevo</span>
                            <input type="file" id="details-new-image" name="imagen" accept="image/*">
                        </div>
                        <small class="form-hint">Tamaño máximo: 2MB.</small>
                    </div>
                    <div class="availability-container">
                        <div class="availability-text">
                            <label for="details-disponible">Disponibilidad en el menú - Si está activo, los clientes podrán ver y ordenar este platillo.</label>
                        </div>
                        <label class="availability-switch">
                            <input type="checkbox" id="details-disponible" name="disponible" disabled>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="modal-buttons modal-buttons-stacked">
                    <button type="button" class="btn-primary view-mode-btn" id="edit-btn">Editar</button>
                    <button type="button" class="btn-danger view-mode-btn" id="delete-btn">Eliminar</button>
                    <button type="submit" class="btn-confirm edit-mode-btn" style="display:none;">Guardar cambios</button>
                    <button type="button" class="btn-cancel edit-mode-btn" id="cancel-edit-btn" style="display:none;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="profile-notification-modal-overlay" id="profile-notification-modal">
        <div class="modal-box">
            <h2 id="profile-notification-title"></h2>
            <p id="profile-notification-message"></p>
            <div class="modal-buttons">
                <button class="btn-confirm" id="profile-notification-close-btn">Entendido</button>
            </div>
        </div>
    </div>

    <div class="confirmation-modal-overlay" id="delete-platillo-modal">
        <div class="modal-box">
            <h2 id="delete-modal-title">Confirmar eliminación</h2>
            <p id="delete-modal-message">¿Estás seguro/a de que quieres eliminar este platillo? Esta acción no se puede deshacer.</p>
            <div class="modal-buttons">
                <button class="btn-cancel" id="cancel-delete-btn">Cancelar</button>
                <button class="btn-confirm" id="confirm-delete-btn">Eliminar</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>