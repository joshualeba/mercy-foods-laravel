<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Muestra la vista del perfil del restaurante.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->profile_image_url) {
            $user->profile_image_url = Storage::url($user->profile_image_url);
        }
        return view('restaurante.perfil', ['user' => $user]);
    }

    /**
     * Actualiza la información del perfil del restaurante.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'restaurant_name' => 'required|string|max:100',
            'full_name' => 'required|string|max:120',
            'email' => ['required', 'email', 'max:120', Rule::unique('users')->ignore($user->id)],
            'contact_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'restaurant_address' => 'required|string|max:200',
            'cuisine_type' => 'required|string|max:50',
            'opening_hours' => 'required|string|max:100',
            'restaurant_description' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            // Elimina la imagen anterior si existe
            if ($user->getRawOriginal('profile_image_url')) {
                Storage::disk('public')->delete($user->getRawOriginal('profile_image_url'));
            }
            // Almacena la nueva imagen y guarda la ruta relativa
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $validated['profile_image_url'] = $path;
        }

        $user->update($validated);

        // Prepara la URL completa para la respuesta JSON
        if (isset($validated['profile_image_url'])) {
            $validated['profile_image_url'] = Storage::url($validated['profile_image_url']);
        } else if ($user->profile_image_url) {
            $validated['profile_image_url'] = Storage::url($user->getRawOriginal('profile_image_url'));
        }


        return response()->json([
            'message' => '¡Perfil actualizado con éxito!',
            'user' => $validated
        ]);
    }
}