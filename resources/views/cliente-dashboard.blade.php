<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - Mercy Food</title>
    <link rel="shortcut icon" href="{{ asset('multimedia/logo.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body data-theme="light">

    <div class="dashboard-container">
        <aside class="sidebar" id="sidebar">
            <ul class="sidebar-nav">
                <li><a href="/cliente-dashboard" class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Inicio</span>
                </a></li>
                <li><a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                    <span>Mis Pedidos</span>
                </a></li>
                <li><a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>Mi Perfil</span>
                </a></li>
            </ul>
            <div class="logout-link" id="logout-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span>Cerrar Sesión</span>
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
                    <!-- Toggle Switch Día/Noche -->
                    <div class="theme-switch-wrapper">
                        <label class="theme-switch" for="theme-toggle">
                            <input type="checkbox" id="theme-toggle" />
                            <div class="slider round"></div>
                        </label>
                    </div>
                    <!-- Menú de Perfil -->
                    <div class="profile-menu">
                        <button class="profile-toggle" id="profile-toggle">
                            <img src="https://ui-avatars.com/api/?name=U" alt="User Avatar" class="profile-pic" id="profile-pic">
                        </button>
                        <div class="profile-dropdown" id="profile-dropdown">
                            <div class="dropdown-header">
                                <h4 id="profile-name">Nombre Usuario</h4>
                                <p id="profile-email">email@ejemplo.com</p>
                            </div>
                            <a href="#" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span>Ver Perfil</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Contenido de la página -->
            <div class="main-content-inner">
                <div class="welcome-message">
                    <h1 id="welcome-title"></h1>
                    <p>Es un gusto tenerte de vuelta.</p>
                </div>

                <section class="content-placeholder">
                    <h2>Panel Principal</h2>
                    <p>Aquí irá el contenido principal de tu dashboard, como un resumen de pedidos, restaurantes recomendados, etc.</p>
                </section>
            </div>
        </main>
    </div>

    <!-- Modales (sin cambios) -->
    <div class="confirmation-modal-overlay" id="confirmation-modal">
        <div class="modal-box">
            <h2>Confirmar Cierre de Sesión</h2>
            <p>¿Estás seguro de que quieres salir de tu cuenta?</p>
            <div class="modal-buttons">
                <button class="btn-cancel" id="cancel-logout-btn">Cancelar</button>
                <button class="btn-confirm" id="confirm-logout-btn">Confirmar</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
