<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlatilloController;
use App\Http\Controllers\RestauranteProfileController;
use App\Http\Controllers\Cliente\RestaurantController;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/registro', function () {
    return view('registro');
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/restaurante-detalle', function () {
    return view('restaurante-detalle');
});

// Rutas para procesar el formulario de login y registro
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    Route::get('/cliente-dashboard', function () {
        return view('cliente-dashboard', ['user' => Auth::user()]);
    });

    Route::get('/restaurante-dashboard', function () {
        return view('restaurante-dashboard');
    });

    Route::get('/repartidor-dashboard', function () {
        return view('repartidor-dashboard');
    });

    // Rutas para Platillos (ya existentes)
    Route::resource('platillos', PlatilloController::class)->except(['create', 'edit']); // Ajustado para API RESTful

    // RUTAS PARA EL PERFIL DEL RESTAURANTE ---
    Route::get('/perfil-restaurante', [RestauranteProfileController::class, 'index'])->name('restaurante.perfil.index');
    Route::put('/perfil-restaurante', [RestauranteProfileController::class, 'update'])->name('restaurante.perfil.update');

    // Ruta para la sección de restaurantes del cliente
    Route::get('/restaurantes', [RestaurantController::class, 'index'])->name('cliente.restaurantes');
});

Route::get('/faq', [FaqController::class, 'index']);