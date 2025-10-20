<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{

    /**
     * Actualiza la contraseña del usuario.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual no es correcta.'],
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

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

        // Validación actualizada
        $validatedData = $request->validate([
            // 'name' => 'required|string|max:100', // El nombre ya no se envía editable
            'description' => 'nullable|string|max:500',
            'contact_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'restaurant_address' => 'required|string|max:200',
            'opening_hours' => 'required|string|max:100', // Campo de horarios actualizado
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // Añade aquí otros campos si los hubiera, como 'cuisine_type' si lo vas a hacer editable
            'cuisine_type' => 'nullable|string|max:50', // Asegúrate de tener este campo si es parte del formulario editable
        ]);

        // Mapeo de campos del formulario a la base de datos
        // Usamos $user->full_name para mantener el nombre original ya que no es editable
        $updateData = [
            'full_name' => $user->full_name, // Mantenemos el nombre existente
            'restaurant_name' => $user->full_name, // Sincronizamos con full_name
            'restaurant_description' => $validatedData['description'],
            'contact_phone' => $validatedData['contact_phone'],
            'restaurant_address' => $validatedData['restaurant_address'],
            'opening_hours' => $validatedData['opening_hours'], // Campo de horarios actualizado
            'cuisine_type' => $validatedData['cuisine_type'] ?? $user->cuisine_type, // Actualiza si se envió
        ];

        // Manejo de la imagen de perfil (sin cambios)
        if ($request->hasFile('profile_image')) {
            // Elimina la imagen anterior si existe
            if ($user->profile_image_url && Storage::disk('public')->exists(str_replace('/storage/', '', $user->profile_image_url))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->profile_image_url));
            }
            // Guarda la nueva imagen
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $updateData['profile_image_url'] = $path; // Guarda la ruta relativa
        }


        $user->update($updateData);

        // Retornamos a la vista anterior con un mensaje de éxito
        return back()->with('success', '¡Perfil actualizado con éxito!');
    }
}