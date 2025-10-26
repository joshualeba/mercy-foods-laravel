<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard del Repartidor - Mercy Foods</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="dashboard-container" data-role="repartidor">
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('multimedia/logo.png') }}" alt="Logo Mercy Foods" class="logo">
                </a>
                <h2>Repartidor</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#" class="nav-link active" data-section="pedidos"><i class="fas fa-box"></i> Pedidos Asignados</a></li>
                    <li><a href="#" class="nav-link" data-section="historial"><i class="fas fa-history"></i> Historial de Entregas</a></li>
                    <li><a href="#" class="nav-link" data-section="perfil"><i class="fas fa-user"></i> Mi Perfil</a></li>
                    <li><a href="#" class="nav-link" data-section="soporte"><i class="fas fa-question-circle"></i> Soporte</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div class="header-left">
                    <button id="toggle-sidebar"><i class="fas fa-bars"></i></button>
                    <h1 id="section-title">Pedidos Asignados</h1>
                </div>
                <div class="header-right">
                    <div class="theme-switcher">
                        <i class="fas fa-sun"></i>
                        <label class="switch">
                            <input type="checkbox" id="theme-toggle">
                            <span class="slider round"></span>
                        </label>
                        <i class="fas fa-moon"></i>
                    </div>
                    <div class="user-info">
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </header>

            <div id="dynamic-content">
                {{-- El contenido se cargará aquí vía JavaScript --}}
            </div>
        </main>
    </div>

    {{-- USAMOS EL MISMO SCRIPT PARA TODOS --}}
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>