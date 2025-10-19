<?php

namespace App\Http\Controllers;

use App\Models\Platillo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatilloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtiene el ID del usuario (restaurante) que ha iniciado sesión
        $userId = Auth::id();

        // Busca en la base de datos SÓLO los platillos que pertenecen a ese usuario
        $platillos = Platillo::where('user_id', $userId)->get();

        // Carga la vista y le pasa la lista de platillos
        // (Crearemos esta vista en el siguiente paso)
        return view('restaurante.menu', ['platillos' => $platillos]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Más adelante, esto mostrará el formulario para crear un nuevo platillo
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Más adelante, aquí guardaremos el nuevo platillo en la BD
    }

    /**
     * Display the specified resource.
     */
    public function show(Platillo $platillo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Platillo $platillo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Platillo $platillo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Platillo $platillo)
    {
        //
    }
}