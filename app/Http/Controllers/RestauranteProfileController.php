<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\RestaurantDetail;

class RestauranteProfileController extends Controller
{
    // Método para mostrar la vista del perfil
    public function index()
    {
        // Carga el usuario y su relación restaurantDetail
        $user = Auth::user()->loadMissing('restaurantDetail');
        return view('restaurante.perfil', ['user' => $user]);
    }

    // Método para actualizar los datos del perfil
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = \Validator::make($request->all(), [
            'restaurant_address' => 'required|string|max:200',
            'cuisine_type' => 'required|string|max:50',
            'contact_phone' => 'required|string|regex:/^\d{10}$/',
            'attention_schedule' => 'nullable|string|max:255', // Nuevo campo
            
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => 'nullable|min:8|confirmed',
        ],[
            'contact_phone.regex' => 'El teléfono debe contener 10 dígitos numéricos.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 1. Actualiza los datos en la tabla 'users'
        $user->restaurant_address = $request->restaurant_address;
        $user->cuisine_type = $request->cuisine_type;
        $user->contact_phone = $request->contact_phone;
        // (full_name y email no se actualizan desde aquí)

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save(); // Guarda los cambios en 'users'

        // 2. Actualiza o crea los datos en la tabla 'restaurant_details'
        $user->restaurantDetail()->updateOrCreate(
            ['user_id' => $user->id], // Busca por user_id
            ['attention_schedule' => $request->attention_schedule] // Actualiza/crea el horario
        );

        // 3. Devuelve los datos actualizados
        return response()->json([
            'restaurant_address' => $user->restaurant_address,
            'cuisine_type' => $user->cuisine_type,
            'contact_phone' => $user->contact_phone,
            'attention_schedule' => $user->restaurantDetail->attention_schedule, // Devuelve el nuevo horario
        ]);
    }
}