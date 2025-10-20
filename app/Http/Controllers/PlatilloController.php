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
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50',
            'descripcion' => 'required|string|max:200',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = $request->file('imagen')->store('platillos', 'public');

        $platillo = Platillo::create([
            'user_id' => Auth::id(),
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'imagen_url' => $imagePath,
        ]);
        
        $platillo->imagen_url = Storage::url($imagePath);

        return response()->json($platillo, 201);
    }
}