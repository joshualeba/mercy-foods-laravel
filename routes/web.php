<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlatilloController;
use App\Http\Controllers\RestauranteProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RepartidorProfileController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\RepartidorController;

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
    Route::get('/cliente/pedidos', [PedidoController::class, 'verPedidosCliente'])->name('cliente.pedidos.index');
    Route::post('/cliente/pedidos/{id}/cancelar', [App\Http\Controllers\PedidoController::class, 'cancelar'])->name('cliente.pedidos.cancelar');
    Route::post('/carrito/procesar', [App\Http\Controllers\PedidoController::class, 'crearDesdeCarrito'])->name('carrito.procesar');
    Route::post('/cliente/perfil/eliminar-pago', [App\Http\Controllers\ClienteProfileController::class, 'eliminarMetodoPago'])->name('cliente.pago.eliminar');

    // RUTAS PARA LA PASARELA DE PAGO ---
    Route::get('/metodo-pago', [PaymentController::class, 'index'])->name('cliente.pago.index');
    Route::post('/pago', [App\Http\Controllers\PaymentController::class, 'store'])->name('cliente.pago.procesar');
    Route::get('/verificar-pago', [PaymentController::class, 'verify'])->name('cliente.pago.verificar');

    // RUTAS PARA EL PERFIL DEL REPARTIDOR ---
    Route::get('/repartidor/dashboard', [RepartidorController::class, 'dashboard'])->name('repartidor.dashboard');
    Route::get('/repartidor/pedidos', [RepartidorController::class, 'verPedidos'])->name('repartidor.pedidos');
    Route::get('/repartidor/perfil', [RepartidorProfileController::class, 'index'])->name('repartidor.perfil.index');
    Route::put('/repartidor/perfil', [RepartidorProfileController::class, 'update'])->name('repartidor.perfil.update');
    Route::put('/repartidor/pedidos/{pedido}/aceptar', [RepartidorController::class, 'aceptarPedido'])->name('repartidor.pedidos.aceptar');
    Route::put('/repartidor/pedidos/{pedido}/recogido', [RepartidorController::class, 'marcarRecogido'])->name('repartidor.pedidos.recogido');
    Route::put('/repartidor/pedidos/{pedido}/entregado', [RepartidorController::class, 'marcarEntregado'])->name('repartidor.pedidos.entregado');

    // Rutas de pedidos para restaurantes
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('restaurante.pedidos.index');
    Route::put('/pedidos/{pedido}/estado', [PedidoController::class, 'actualizarEstado'])->name('pedidos.actualizarEstado');
});

Route::get('/faq', [FaqController::class, 'index']);