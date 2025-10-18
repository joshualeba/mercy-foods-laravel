<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;

Route::get('/', function () {
    return view('index');
});

// Ruta para la página de Inicio de Sesión
Route::get('/login', function () {
    return view('login');
});

// Ruta para la página de Registro
Route::get('/registro', function () {
    return view('registro');
});

// Ruta para la página de detalles de un restaurante
Route::get('/restaurante-detalle', function () {
    return view('restaurante-detalle');
});


// Ruta para el dashboard del Cliente
Route::get('/cliente-dashboard', function () {
    return view('cliente-dashboard');
});

// Ruta para el dashboard del Restaurante
Route::get('/restaurante-dashboard', function () {
    return view('restaurante-dashboard');
});

// Ruta para el dashboard del Repartidor
Route::get('/repartidor-dashboard', function () {
    return view('repartidor-dashboard');
});

Route::get('/faq', [FaqController::class, 'index']);