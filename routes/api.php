<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiPedidoController;
use App\Http\Controllers\ApiReviewController;
use App\Http\Controllers\ApiRestauranteController;
use App\Http\Controllers\ApiPlatilloController;

// Rutas públicas (sin autenticación)
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/auth/google', [ApiAuthController::class, 'googleAuth']);

// Rutas públicas - Restaurantes y Platillos
Route::get('/restaurantes', [ApiRestauranteController::class, 'index']);
Route::get('/restaurantes/{id}', [ApiRestauranteController::class, 'show']);
Route::get('/restaurantes/{id}/platillos', [ApiRestauranteController::class, 'platillos']);
Route::get('/platillos', [ApiPlatilloController::class, 'index']);
Route::get('/platillos/{id}', [ApiPlatilloController::class, 'show']);

// Rutas protegidas (requieren token de Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Autenticación
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);
    
    // Pedidos
    Route::get('/pedidos', [ApiPedidoController::class, 'index']);
    Route::post('/pedidos', [ApiPedidoController::class, 'store']);
    Route::get('/pedidos/{id}', [ApiPedidoController::class, 'show']);
    
    // Reseñas
    Route::get('/reviews', [ApiReviewController::class, 'index']);
    Route::post('/reviews', [ApiReviewController::class, 'store']);
});