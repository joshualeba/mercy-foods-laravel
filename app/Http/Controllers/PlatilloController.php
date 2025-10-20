<?php

namespace App\Http\Controllers;

use App\Models\Platillo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PlatilloController extends Controller
{
    public function index()
    {
        $platillos = Auth::user()->platillos()->get()->map(function ($platillo) {
            if ($platillo->imagen_url) {
                $platillo->imagen_url = Storage::url($platillo->imagen_url);
            }
            return $platillo;
        });
        
        return view('restaurante.menu', compact('platillos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50',
            'descripcion' => 'required|string|max:150',
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
            'disponible' => true, // Por defecto, un platillo nuevo está activo
        ]);
        
        $platillo->imagen_url = Storage::url($imagePath);
        return response()->json($platillo, 201);
    }

    public function show(Platillo $platillo)
    {
        if ($platillo->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        if ($platillo->imagen_url) {
            $platillo->imagen_url = Storage::url($platillo->imagen_url);
        }

        return response()->json($platillo);
    }

    public function update(Request $request, Platillo $platillo)
    {
        if ($platillo->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50',
            'descripcion' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $platillo->nombre = $request->nombre;
        $platillo->descripcion = $request->descripcion;
        $platillo->precio = $request->precio;
        $platillo->disponible = $request->input('disponible', 0);

        if ($request->hasFile('imagen')) {
            if ($platillo->imagen_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $platillo->imagen_url));
            }
            $platillo->imagen_url = $request->file('imagen')->store('platillos', 'public');
        }

        $platillo->save();

        if ($platillo->imagen_url) {
            $platillo->imagen_url = Storage::url($platillo->imagen_url);
        }

        return response()->json($platillo);
    }
    
    public function destroy(Platillo $platillo)
    {
        if ($platillo->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($platillo->imagen_url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $platillo->imagen_url));
        }

        $platillo->delete();

        return response()->json(['message' => 'Platillo eliminado con éxito']);
    }
}