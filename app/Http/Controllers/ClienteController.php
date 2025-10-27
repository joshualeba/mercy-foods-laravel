<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Platillo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    public function index()
    {
        // Obtenemos todos los platillos que están disponibles y cargamos la información de su restaurante (usuario)
        $platillos = Platillo::where('disponible', true)
                            ->with('user') // Carga la relación con el restaurante
                            ->get();

        // Obtenemos una lista única de los tipos de cocina de los restaurantes que tienen platillos
        $tiposCocina = User::where('role', 'restaurante')
                            ->whereHas('platillos', function ($query) {
                                $query->where('disponible', true);
                            })
                            ->pluck('cuisine_type')
                            ->unique()
                            ->filter(); // Elimina valores nulos o vacíos

        // Ajustamos las URLs de las imágenes
        $platillos->each(function ($platillo) {
            if ($platillo->imagen_url) {
                $platillo->imagen_url = Storage::url($platillo->imagen_url);
            }
        });

        return view('cliente.ordenar', compact('platillos', 'tiposCocina'));
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
                                    ->take(5)
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