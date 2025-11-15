<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\RestaurantDetail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // Método para mostrar el formulario de login
    public function showLoginForm()
    {
        return view('login');
    }

    // Método para procesar el login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // --- RESPUESTA JSON PARA LOGIN EXITOSO ---
            return response()->json([
                'message' => 'Inicio de sesión exitoso.',
                'user' => [
                    'id' => $user->id,
                    'fullName' => $user->full_name, // Cambiado a fullName como espera el JS
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);
        }

        // --- RESPUESTA JSON PARA LOGIN FALLIDO ---
        return response()->json([
            'message' => 'Correo electrónico o contraseña incorrectos.'
        ], 401);
    }

    // Método para mostrar el formulario de registro
    public function showRegistrationForm()
    {
        return view('registro');
    }

    // --- MÉTODO REGISTER ACTUALIZADO ---
    public function register(Request $request)
    {
        // La validación puede quedarse igual que la tenías
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:120', // Cambiado a full_name
            'email' => 'required|string|email|max:120|unique:users',
            'password' => 'required|string|min:8', // Quité confirmed aquí, el JS ya lo valida
            'role' => 'required|in:cliente,restaurante,repartidor',
            'restaurant_address' => 'required_if:role,restaurante|nullable|string|max:200',
            'cuisine_type' => 'required_if:role,restaurante|nullable|string|max:50',
            'contact_phone' => 'required_if:role,restaurante|nullable|string|regex:/^\d{10}$/',
            'vehicle_type' => 'required_if:role,repartidor|nullable|string|max:50',
        ],[
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contact_phone.regex' => 'El teléfono debe contener 10 dígitos numéricos.',
            'required_if' => 'Este campo es obligatorio para el rol seleccionado.',
        ]);

        // --- RESPUESTA JSON SI LA VALIDACIÓN FALLA ---
        if ($validator->fails()) {
             // Devolvemos los errores como JSON, código 422 Unprocessable Entity
            return response()->json(['message' => 'Datos inválidos.', 'errors' => $validator->errors()], 422);
        }

        try {
            // Crea el usuario (igual que antes)
            $user = User::create([
                'full_name' => $request->full_name, // Asegúrate que el JS envía 'full_name'
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'restaurant_address' => $request->restaurant_address,
                'cuisine_type' => $request->cuisine_type,
                'contact_phone' => $request->contact_phone,
                'vehicle_type' => $request->vehicle_type,
            ]);

            if ($user->role === 'restaurante') {
                $user->restaurantDetail()->create(['attention_schedule' => null]);
            }

            // No iniciamos sesión aquí, el JS redirigirá al login

            // --- RESPUESTA JSON PARA REGISTRO EXITOSO ---
            return response()->json(['message' => '¡Cuenta creada con éxito!'], 201); // Código 201 Created

        } catch (\Exception $e) {
            // --- RESPUESTA JSON SI HAY ERROR AL CREAR ---
            // Loguea el error real si los logs funcionan
             \Log::error('Error en registro: '.$e->getMessage());
            return response()->json(['message' => 'Error interno al crear la cuenta.'], 500); // Código 500 Internal Server Error
        }
    }

    // Método para logout (ya existe)
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Redirige al usuario a la página de autenticación de Google.
     */
    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Maneja el callback de Google y loguea o registra al usuario.
     */
    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Buscar un usuario existente por google_id o email
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // Si el usuario existe, actualizar su google_id si es nulo
                if (is_null($user->google_id)) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                }

                // Loguear al usuario
                Auth::login($user);

            } else {
                // Si no existe, crear un nuevo usuario
                // DECISIÓN DE LÓGICA DE NEGOCIO:
                // Todos los usuarios registrados por Google tendrán el rol "Cliente" por defecto.

                $newUser = User::create([
                    'google_id' => $googleUser->id,
                    'full_name' => $googleUser->name, // 'name' de Google a 'full_name' nuestro
                    'email' => $googleUser->email,
                    'role' => 'Cliente', // Rol por defecto
                    'password' => Hash::make(Str::random(24)) // Contraseña aleatoria
                ]);

                Auth::login($newUser);
            }

            // Redirigir al dashboard correspondiente basado en el rol
            // ESTO ES DIFERENTE A TU DOC, PERO ES MEJOR PORQUE TU LOGIN DEVUELVE JSON
            // Para mantener consistencia con tu login actual, deberíamos devolver JSON
            // PERO... Socialite es un flujo web. La redirección es lo correcto aquí.

            $user = Auth::user(); // Obtenemos el usuario (nuevo o existente)
            switch ($user->role) {
                case 'Restaurante':
                    return redirect()->route('restaurante.dashboard');
                case 'Repartidor':
                    return redirect()->route('repartidor.dashboard');
                case 'Cliente':
                default:
                    return redirect()->route('cliente.dashboard');
            }

        } catch (\Exception $e) {
            // Manejo de error
            \Log::error('Error en Google Callback: '.$e->getMessage());
            return redirect()->route('login')->with('error', 'Error al iniciar sesión con Google.');
        }
    }
}