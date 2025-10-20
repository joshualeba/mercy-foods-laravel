<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de restaurante - Mercy Food</title>
    <link rel="shortcut icon" href="{{ asset('multimedia/logo.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
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
                <li><a href="#pedidos" class="nav-link" data-section="pedidos">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                    <span>Gestionar pedidos</span>
                </a></li>
                <li><a href="{{ route('platillos.index') }}" class="nav-link" data-section="menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                    <span>Mi menú</span>
                </a></li>
                <li><a href="#perfil" class="nav-link" data-section="perfil">
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
                            <a href="#perfil" class="dropdown-item nav-link" data-section="perfil">
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
                        <p>Listo para recibir pedidos y hacer crecer tu negocio.</p>
                    </div>
                    <div class="content-placeholder">
                        <h2>Panel de control</h2>
                        <p>Aquí verás un resumen de tus ventas, pedidos activos y estadísticas clave.</p>
                    </div>
                </section>
                <section id="pedidos" class="dashboard-section">
                    <h1>Gestionar pedidos</h1>
                    <div class="content-placeholder"><p>Aquí se mostrarán los pedidos activos y el historial.</p></div>
                </section>
                
                <section id="perfil" class="dashboard-section">
                    <h1>Perfil del local</h1>
                    <div class="content-placeholder"><p>Aquí podrás editar la información de tu restaurante.</p></div>
                </section>
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

    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>