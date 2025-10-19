<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/registro', function () {
    return view('registro');
});

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
});

Route::get('/faq', [FaqController::class, 'index']);