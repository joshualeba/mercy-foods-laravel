<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Platillo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    /**
     * Muestra los restaurantes y sus platillos disponibles.
     */
    public function index()
    {
        // Obtenemos todos los usuarios que son restaurantes
        // y cargamos sus platillos que están marcados como "disponibles".
        $restaurantes = User::where('role', 'restaurante')
                            ->with(['platillos' => function ($query) {
                                $query->where('disponible', true);
                            }])
                            ->get();

        // Ajustamos las URLs de las imágenes para que se muestren correctamente
        $restaurantes->each(function ($restaurante) {
            $restaurante->platillos->each(function ($platillo) {
                if ($platillo->imagen_url) {
                    $platillo->imagen_url = Storage::url($platillo->imagen_url);
                }
            });
        });

        // Pasamos los datos a una nueva vista que crearemos a continuación
        return view('cliente.ordenar', compact('restaurantes'));
    }

    /**
     * Muestra la sección de inicio con platillos sugeridos.
     */
    public function inicio()
    {
        // Obtenemos hasta 3 platillos disponibles de forma aleatoria
        $platillosSugeridos = Platillo::where('disponible', true)
                                    ->with('user') // Precargamos la info del restaurante
                                    ->inRandomOrder()
                                    ->take(3)
                                    ->get();

        // Procesamos las URLs de las imágenes
        $platillosSugeridos->each(function ($platillo) {
            if ($platillo->imagen_url) {
                $platillo->imagen_url = Storage::url($platillo->imagen_url);
            }
        });

        // Pasamos los platillos a la vista 'cliente.inicio'
        return view('cliente.inicio', compact('platillosSugeridos'));
    }

    /**
     * Muestra el dashboard principal del cliente, cargando la sección de inicio por defecto.
     */
    public function dashboard()
    {
        // Obtenemos los datos de la sección de inicio
        $inicioView = $this->inicio()->render();

        // Pasamos la vista renderizada al dashboard principal
        return view('cliente-dashboard', ['initialContent' => $inicioView]);
    }
}