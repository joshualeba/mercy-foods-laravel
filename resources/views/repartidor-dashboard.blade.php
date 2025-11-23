<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel de repartidor - Mercy Food</title>
    <link rel="shortcut icon" href="{{ asset('multimedia/logo.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li><a href="{{ route('repartidor.pedidos') }}" class="nav-link" data-section="pedidos">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                    <span>Pedidos disponibles</span>
                </a></li>
                <li><a href="{{ route('repartidor.perfil.index') }}" class="nav-link" data-section="perfil">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>Mi Perfil</span>
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
                        <p>Listo/a para llevar los mejores sabores a su destino?</p>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon icon-pedidos">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                                </div>
                                <div class="stat-card-content">
                                    <p>Entregas de hoy</p>
                                    <h3>{{ $entregasHoy ?? 0 }}</h3>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <a href="{{ route('repartidor.pedidos') }}" class="card-link nav-link" data-section="pedidos">
                                    <span>Ver entregas</span>
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
                                    <p>Ganancias del día</p>
                                    <h3>${{ number_format($gananciasHoy ?? 0, 2) }}</h3>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <a href="#" class="card-link" style="visibility: hidden;">
                                    <span>Ver historial</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon icon-menu">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                </div>
                                <div class="stat-card-content">
                                    <p>Pedidos pendientes</p>
                                    <h3>{{ $pedidosPendientes ?? 0 }}</h3>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <a href="{{ route('repartidor.pedidos') }}" class="card-link nav-link" data-section="pedidos">
                                    <span>Ver pedidos</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </div>

                        {{-- Nueva Tarjeta de Perfil --}}
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon icon-perfil">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                </div>
                                <div class="stat-card-content">
                                    <p>Tu perfil</p>
                                    <h3 style="color: {{ $profileStatus === 'Completo' ? 'var(--color-success)' : 'var(--primary-color)' }};">
                                        {{ $profileStatus }}
                                    </h3>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <a href="{{ route('repartidor.perfil.index') }}" class="card-link nav-link" data-section="perfil">
                                    <span>Editar información</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </div>

                        {{-- Tarjeta de Calificación --}}
                        <div class="stat-card" style="grid-column: span 2;">
                            <div class="stat-card-header">
                                <div class="stat-card-icon icon-rating" style="background-color: #ffc107;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                </div>
                                <div class="stat-card-content">
                                    <p>Tu calificación promedio</p>
                                    <h3 id="average-rating">{{ $averageRating ?? '0.0' }}</h3>
                                    <div id="rating-stars" style="color: #ffc107; font-size: 1.5rem;">
                                        {{-- Estrellas se generarán con JavaScript --}}
                                    </div>
                                    <small id="total-reviews">{{ $totalReviews ?? 0 }} {{ $totalReviews == 1 ? 'reseña' : 'reseñas' }}</small>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <a href="#" class="card-link" onclick="event.preventDefault(); mostrarSeccionResenas();">
                                    <span>Ver todas las reseñas</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Reseñas -->
                    <div id="reviews-section" style="display: none; margin-top: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                            <h2>Reseñas de clientes</h2>
                            <button onclick="ocultarSeccionResenas()" style="background-color: #e74c3c; color: #fff; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.3s ease;">
                                Cerrar
                            </button>
                        </div>
                        <div id="reviews-list" class="reviews-container">
                            <!-- Las reseñas se cargarán aquí dinámicamente -->
                        </div>
                    </div>
                </section>
                <section id="pedidos" class="dashboard-section">
                    <h1>Pedidos disponibles</h1>
                    <div class="content-placeholder">
                        <p>Aquí se mostrará la lista de pedidos que puedes aceptar.</p>
                    </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para generar estrellas
            function generarEstrellas(rating) {
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= Math.round(rating)) {
                        stars += '★';
                    } else {
                        stars += '☆';
                    }
                }
                return stars;
            }

            // Generar estrellas iniciales si existe el elemento
            const ratingStarsEl = document.getElementById('rating-stars');
            if (ratingStarsEl) {
                const initialRating = {{ $averageRating ?? 0 }};
                ratingStarsEl.innerHTML = generarEstrellas(initialRating);
            }

            // Función para mostrar sección de reseñas
            window.mostrarSeccionResenas = function() {
                document.getElementById('reviews-section').style.display = 'block';
                cargarResenas();
            };

            // Función para ocultar sección de reseñas
            window.ocultarSeccionResenas = function() {
                document.getElementById('reviews-section').style.display = 'none';
            };

            // Función para cargar reseñas
            function cargarResenas() {
                fetch('/api/repartidor/reviews')
                    .then(response => response.json())
                    .then(reviews => {
                        const container = document.getElementById('reviews-list');
                        if (reviews.length === 0) {
                            container.innerHTML = '<p style="text-align: center; color: var(--text-color-light); padding: 2rem;">Aún no tienes reseñas de clientes.</p>';
                            return;
                        }

                        container.innerHTML = reviews.map(review => `
                            <div class="review-card" style="background: var(--card-bg); padding: 1.5rem; border-radius: 10px; margin-bottom: 1rem; border: 1px solid var(--border-color);">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div>
                                        <h4 style="margin: 0; color: var(--text-color-dark);">${review.cliente_nombre}</h4>
                                        <small style="color: var(--text-color-light);">Pedido #${review.pedido_id} - ${review.fecha}</small>
                                    </div>
                                    <div style="color: #ffc107; font-size: 1.2rem;">
                                        ${generarEstrellas(review.rating_repartidor)}
                                    </div>
                                </div>
                                ${review.comentario_repartidor ? `
                                    <p style="color: var(--text-color-semidark); margin: 0; line-height: 1.6;">
                                        "${review.comentario_repartidor}"
                                    </p>
                                ` : '<p style="color: var(--text-color-light); margin: 0; font-style: italic;">Sin comentario</p>'}
                            </div>
                        `).join('');
                    })
                    .catch(error => {
                        console.error('Error al cargar reseñas:', error);
                        document.getElementById('reviews-list').innerHTML = '<p style="text-align: center; color: var(--color-danger);">Error al cargar las reseñas.</p>';
                    });
            }
        });
    </script>

    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>