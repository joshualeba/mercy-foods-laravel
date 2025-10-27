<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi cuenta - Mercy Food</title>
    <link rel="shortcut icon" href="{{ asset('multimedia/logo.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <li><a href="{{ route('cliente.inicio') }}" class="nav-link active" data-section="inicio">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Inicio</span>
                </a></li>
                <li><a href="{{ route('cliente.restaurantes') }}" class="nav-link" data-section="ordenar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20M20 8v13H4V8M18 4H6v4h12V4z"></path></svg>
                    <span>Ordenar</span>
                </a></li>
                <li><a href="{{ route('cliente.pedidos.index') }}" class="nav-link" data-section="pedidos">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                    <span>Mis pedidos</span>
                </a></li>
                <li><a href="{{ route('cliente.perfil.index') }}" class="nav-link" data-section="perfil">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>Mi perfil</span>
                </a></li>
                <li><a href="{{ route('cliente.pago.index') }}" class="nav-link" data-section="pago">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    <span>Método de pago</span>
                </a></li>
            </ul>
            <div class="logout-link" id="logout-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span>Cerrar sesión</span>
            </div>
        </aside>

        <main class="main-content" id="main-content">
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
                        <label class="theme-switch" for="theme-toggle">
                            <input type="checkbox" id="theme-toggle" />
                            <div class="slider round"></div>
                        </label>
                    </div>
                    <div class="profile-menu">
                        <button class="profile-toggle" id="profile-toggle">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=FF6347&color=fff&bold=true" alt="User Avatar" class="profile-pic" id="profile-pic">
                        </button>
                        <div class="profile-dropdown" id="profile-dropdown">
                            <div class="dropdown-header">
                                <h4 id="profile-name">{{ Auth::user()->full_name }}</h4>
                                <p id="profile-email">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="#perfil" class="dropdown-item nav-link" data-section="perfil">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span>Ver perfil</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="main-content-inner">
                <section id="pedidos" class="dashboard-section">
                    <h1>Mis pedidos</h1>
                    <div class="content-placeholder">
                        <p>Aquí se mostrará el historial de tus pedidos.</p>
                    </div>
                </section>

                <section id="perfil" class="dashboard-section">
                    <h1>Mi perfil</h1>
                    <div class="content-placeholder">
                        <p>Aquí podrás editar tu información personal, dirección y contraseña.</p>
                    </div>
                </section>

                <div id="ajax-content-wrapper">
                    {!! $initialContent ?? '' !!}
                </div>
            </div>
        </main>
    </div>

    <div class="confirmation-modal-overlay" id="confirmation-modal">
        <div class="modal-box">
            <h2>Confirmar cierre de sesión</h2>
            <p>¿Estás seguro/a de que quieres salir de tu cuenta?</p>
            <div class="modal-buttons">
                <button class="btn-cancel" id="cancel-logout-btn">Cancelar</button>
                <button class="btn-confirm" id="confirm-logout-btn">Confirmar</button>
            </div>
        </div>
    </div>

    {{-- Modal para notificaciones --}}
    <div class="modal-overlay" id="notification-modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="notification-modal-title"></h3>
                <button class="close-modal" id="notification-modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <p id="notification-modal-message"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="notification-modal-accept-btn">Aceptar</button>
            </div>
        </div>
    </div>

    <div class="cart-fab" id="cart-fab" style="display: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
        <span class="cart-count" id="cart-count">0</span>
    </div>

    <div class="confirmation-modal-overlay" id="cart-modal">
        <div class="modal-box" style="max-width: 600px; text-align: left;">
            <button class="close-modal-btn" id="close-cart-btn">&times;</button>
            <h2>Mi carrito de compras</h2>
            <div id="cart-items-container" style="max-height: 40vh; overflow-y: auto; padding-right: 15px;">
                {{-- Los items del carrito se insertarán aquí con JavaScript --}}
            </div>
            <div class="cart-summary" style="margin-top: 20px; text-align: right;">
                <h4>Total: <span id="cart-total">$0.00</span></h4>
            </div>
            <div class="modal-buttons" style="margin-top: 20px;">
                <button class="btn btn-primary" id="checkout-btn" disabled>Realizar pedido</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script>
        const processCartUrl = '{{ route("carrito.procesar") }}';
        const verifyPaymentUrl = '{{ route("cliente.pago.verificar") }}';
    </script>

    {{-- Modal para detalles del platillo --}}
    <div class="confirmation-modal-overlay" id="platillo-modal-details">
        <div class="modal-box">
            <button class="close-modal" id="close-platillo-modal-btn">&times;</button>
            <div class="platillo-modal-content">
                <img id="modal-platillo-imagen" src="" alt="Imagen del platillo" class="platillo-modal-img">
                <h3 id="modal-platillo-nombre"></h3>
                <p class="platillo-modal-restaurante" id="modal-platillo-restaurante"></p>
                <p id="modal-platillo-descripcion"></p>
                <div class="platillo-modal-footer">
                    <span class="platillo-modal-precio" id="modal-platillo-precio"></span>
                    <button class="btn btn-primary btn-ordenar-modal" 
                            id="modal-ordenar-btn"
                            data-id=""
                            data-nombre=""
                            data-precio="">
                        Ordenar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Nuevo Modal para Confirmar Pedido --}}
    <div class="confirmation-modal-overlay" id="confirm-order-modal">
        <div class="modal-box">
            <h2>Confirmar tu pedido</h2>
            <p>¿Estás seguro/a de que deseas realizar el pago por un total de <strong id="confirm-total-amount"></strong>?</p>
            <div class="modal-buttons">
                <button class="btn-cancel" id="cancel-order-btn">Cancelar</button>
                <button class="btn-confirm" id="confirm-order-btn">Aceptar y pagar</button>
            </div>
        </div>
    </div>

    {{-- Nuevo Modal para agregar Método de Pago --}}
    <div class="confirmation-modal-overlay" id="add-payment-modal">
        <div class="modal-box">
            <h2>Método de pago requerido</h2>
            <p>Para continuar, necesitas agregar un método de pago a tu cuenta.</p>
            <div class="modal-buttons">
                <button class="btn btn-primary" id="go-to-payment-btn">Agregar método de pago</button>
            </div>
        </div>
    </div>
</body>
</body>
</html>