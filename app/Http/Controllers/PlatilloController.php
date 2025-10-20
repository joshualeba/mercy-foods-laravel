<?php

namespace App\Http\Controllers;

use App\Models\Platillo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // Asegúrate de importar Validator

class PlatilloController extends Controller
{
    public function index()
    {
        // Obtenemos los platillos del usuario y preparamos la URL completa de la imagen
        $platillos = Auth::user()->platillos()->get()->map(function ($platillo) {
            $platillo->imagen_url = Storage::url($platillo->imagen_url);
            return $platillo;
        });
        
        // Devolvemos la vista con los platillos
        return view('restaurante.menu', compact('platillos'));
    }

    public function store(Request $request)
    {
        // 1. Validar los datos, incluyendo la imagen
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:150',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Guardar la imagen en storage/app/public/platillos
        $imagePath = $request->file('imagen')->store('platillos', 'public');

        // 3. Crear el nuevo platillo en la base de datos
        $platillo = Platillo::create([
            'user_id' => Auth::id(),
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'imagen_url' => $imagePath, // Guardamos la ruta relativa
        ]);
        
        // 4. Preparamos la respuesta con la URL pública de la imagen
        $platillo->imagen_url = Storage::url($imagePath);

        return response()->json($platillo, 201);
    }
}