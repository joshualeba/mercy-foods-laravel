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

        // La validación se mantiene igual, ya que los campos que llegan son los correctos
        $validator = \Validator::make($request->all(), [
            'restaurant_address' => 'required|string|max:200',
            'cuisine_type' => 'required|string|max:50',
            'contact_phone' => 'required|string|regex:/^\d{10}$/',
            'attention_schedule' => 'nullable|string|max:255',
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => [
                'nullable',
                'string',
                'min:8',
                'max:25',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).+$/'
            ],
        ],[
            'contact_phone.regex' => 'El teléfono debe contener 10 dígitos numéricos.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.max' => 'La nueva contraseña no debe exceder los 25 caracteres.',
            'new_password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un caracter especial (!@#$%).',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // --- INICIO DE LA CORRECCIÓN ---

        // 1. Actualiza los campos que sí están en la tabla 'users'
        $user->restaurant_address = $request->restaurant_address;
        $user->cuisine_type = $request->cuisine_type;
        $user->contact_phone = $request->contact_phone;

        // 2. Actualiza la contraseña si se proporcionó una nueva
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        // 3. Guarda todos los cambios realizados en el modelo User
        $user->save();

        // 4. Actualiza o crea el horario en la tabla 'restaurant_details'
        $user->restaurantDetail()->updateOrCreate(
            ['user_id' => $user->id], // Condición para buscar o crear
            ['attention_schedule' => $request->attention_schedule] // Único dato para esta tabla
        );
        
        // 5. Recargamos la relación para asegurar que tenemos los datos más frescos
        $user->load('restaurantDetail');

        // 6. Devuelve la respuesta JSON con los datos correctos de cada modelo
        return response()->json([
            'full_name' => $user->full_name,
            'email' => $user->email,
            'restaurant_address' => $user->restaurant_address, // Dato del modelo User
            'cuisine_type' => $user->cuisine_type, // Dato del modelo User
            'contact_phone' => $user->contact_phone, // Dato del modelo User
            // Usamos un operador ternario para evitar un error si la relación aún no existe
            'attention_schedule' => $user->restaurantDetail ? $user->restaurantDetail->attention_schedule : '',
        ]);
    }
}