<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class ClienteProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('cliente.perfil', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = \Validator::make($request->all(), [
            'full_name' => 'required|string|max:120',
            'address' => 'nullable|string|max:200',
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
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.max' => 'La nueva contraseña no debe exceder los 25 caracteres.',
            'new_password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un caracter especial (!@#$%).',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->full_name = $request->full_name;
        $user->address = $request->address;

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return response()->json([
            'full_name' => $user->full_name,
            'email' => $user->email,
            'address' => $user->address,
        ]);
    }

    public function eliminarMetodoPago()
    {
        // Obtenemos al usuario autenticado
        $user = auth()->user();

        // Verificamos si realmente tiene un método de pago para eliminar
        if ($user->card_last_four) {
            // Establecemos los campos de la tarjeta a null
            $user->card_last_four = null;
            $user->card_expiry = null;
            $user->card_name = null;
            // La columna card_name no existe, por eso la quitamos de aquí.
            
            // Guardamos los cambios en la base de datos
            $user->save();

            return response()->json(['message' => 'Método de pago eliminado correctamente.']);
        }

        return response()->json(['message' => 'No se encontró ningún método de pago para eliminar.'], 404);
    }
}