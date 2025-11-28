<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiPedidoController;
use App\Http\Controllers\ApiReviewController;

// Rutas públicas (sin autenticación)
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/auth/google', [ApiAuthController::class, 'googleAuth']);

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