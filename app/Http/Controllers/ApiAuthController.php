<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    /**
     * Login con email y password - Devuelve token de API
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Intentar autenticar
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = Auth::user();

        // Generar token de API
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 200);
    }

    /**
     * Autenticación con Google (Opción 2 - Simplificada)
     * Acepta google_id y email directamente
     */
    public function googleAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_id' => 'required|string',
            'email' => 'required|email',
            'full_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar usuario por google_id o email
        $user = User::where('google_id', $request->google_id)
                    ->orWhere('email', $request->email)
                    ->first();

        if ($user) {
            // Usuario existe - actualizar google_id si es necesario
            if (is_null($user->google_id)) {
                $user->google_id = $request->google_id;
                $user->save();
            }
        } else {
            // Usuario no existe - verificar si necesita completar registro
            // Para la API, devolvemos que necesita registrarse con rol
            return response()->json([
                'message' => 'Usuario no encontrado',
                'needs_registration' => true,
                'google_data' => [
                    'google_id' => $request->google_id,
                    'email' => $request->email,
                    'full_name' => $request->full_name,
                ]
            ], 404);
        }

        // Generar token de API
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 200);
    }

    /**
     * Logout - Revoca el token actual
     */
    public function logout(Request $request)
    {
        // Revocar el token actual del usuario autenticado
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ], 200);
    }

    /**
     * Obtener usuario autenticado
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ], 200);
    }
}
