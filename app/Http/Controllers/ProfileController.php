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

        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'cuisine_type' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
            'contact_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'restaurant_address' => 'required|string|max:200',
            'opening_hours' => 'required|string|max:100',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        
        // Mapeo de campos del formulario a la base de datos
        $updateData = [
            'full_name' => $validatedData['name'],
            'restaurant_name' => $validatedData['name'],
            'cuisine_type' => $validatedData['cuisine_type'],
            'restaurant_description' => $validatedData['description'],
            'contact_phone' => $validatedData['contact_phone'],
            'restaurant_address' => $validatedData['restaurant_address'],
            'opening_hours' => $validatedData['opening_hours'],
        ];

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image_url) {
                Storage::disk('public')->delete($user->profile_image_url);
            }
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $updateData['profile_image_url'] = $path;
        }

        $user->update($updateData);

        // Prepara los datos del usuario para la respuesta
        $userResponse = $user->fresh()->only([
            'name', 'email', 'profile_image_url'
        ]);
        
        if ($userResponse['profile_image_url']) {
            $userResponse['profile_image_url'] = Storage::url($userResponse['profile_image_url']);
        }

        return response()->json([
            'message' => '¡Perfil actualizado con éxito!',
            'user' => $userResponse
        ]);
    }
}