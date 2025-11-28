<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;

// Rutas públicas (sin autenticación)
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/auth/google', [ApiAuthController::class, 'googleAuth']);

// Rutas protegidas (requieren token de Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);
});