<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlatilloController;
use App\Http\Controllers\RestauranteProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteProfileController;
use App\Http\Controllers\PaymentController;

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

// Rutas para procesar el formulario de login y registro
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    Route::get('/cliente-dashboard', [ClienteController::class, 'dashboard'])->name('cliente.dashboard');

    Route::get('/restaurante-dashboard', function () {
        return view('restaurante-dashboard');
    });

    Route::get('/repartidor-dashboard', function () {
        return view('repartidor-dashboard');
    });

    // Rutas para Platillos (ya existentes)
    Route::resource('platillos', PlatilloController::class)->except(['create', 'edit']); // Ajustado para API RESTful

    Route::get('/inicio', [ClienteController::class, 'inicio'])->name('cliente.inicio');
    Route::get('/ordenar', [ClienteController::class, 'index'])->name('cliente.restaurantes');

    // RUTAS PARA EL PERFIL DEL RESTAURANTE ---
    Route::get('/perfil-restaurante', [RestauranteProfileController::class, 'index'])->name('restaurante.perfil.index');
    Route::put('/perfil-restaurante', [RestauranteProfileController::class, 'update'])->name('restaurante.perfil.update');

    // RUTAS PARA EL PERFIL DEL CLIENTE ---
    Route::get('/perfil-cliente', [ClienteProfileController::class, 'index'])->name('cliente.perfil.index');
    Route::put('/perfil-cliente', [ClienteProfileController::class, 'update'])->name('cliente.perfil.update');

    // RUTAS PARA LA PASARELA DE PAGO ---
    Route::get('/metodo-pago', [PaymentController::class, 'index'])->name('cliente.pago.index');
    Route::post('/procesar-pago', [PaymentController::class, 'process'])->name('cliente.pago.procesar');
});

Route::get('/faq', [FaqController::class, 'index']);