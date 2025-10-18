<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Registra un nuevo usuario.
     */
    public function register(Request $request)
    {
        // Valida que los datos básicos vengan en la petición
        $request->validate([
            'nombre' => 'required|string|max:120',
            'email' => 'required|string|email|max:120|unique:users',
            'password' => 'required|string',
            'role' => 'required|string|in:cliente,restaurante,repartidor',
        ]);

        // Crea el usuario en la base de datos
        $user = User::create([
            'full_name' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Encripta la contraseña
            'role' => $request->role,
            'restaurant_address' => $request->direccion,
            'cuisine_type' => $request->tipo,
            'contact_phone' => $request->telefono,
            'vehicle_type' => $request->vehiculo,
        ]);

        return response()->json(['message' => '¡Cuenta creada con éxito!'], 201);
    }

    /**
     * Inicia sesión para un usuario existente.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intenta autenticar al usuario con las credenciales proporcionadas
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciales incorrectas...'], 401);
        }

        // Si la autenticación es exitosa, obtiene los datos del usuario
        $user = User::where('email', $request->email)->firstOrFail();

        // Devuelve una respuesta de éxito con los datos del usuario
        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'user' => [
                'id' => $user->id,
                'fullName' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role, // El rol se envía para la redirección en el frontend
            ]
        ]);
    }
}