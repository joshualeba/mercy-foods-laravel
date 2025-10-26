<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RepartidorProfileController extends Controller
{
    /**
     * Muestra el formulario del perfil del repartidor.
     * Esta función no se llama directamente por una ruta,
     * sino a través del RepartidorController.
     */
    public function show()
    {
        return view('repartidor.perfil');
    }

    /**
     * Actualiza la información del perfil del repartidor.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->full_name = $request->full_name; 
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('status', '¡perfil actualizado con éxito!');
    }
}