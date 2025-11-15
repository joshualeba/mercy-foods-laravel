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
            'restaurant_name' => 'required_if:role,restaurante|nullable|string|max:100',
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

                // solo para usuarios que ya existen.
                $user = Auth::user(); 
                switch ($user->role) {
                    case 'Restaurante':
                    case 'restaurante':
                        return redirect()->route('restaurante.dashboard');
                    case 'Repartidor':
                    case 'repartidor':
                        return redirect()->route('repartidor.dashboard');
                    case 'Cliente':
                    case 'cliente':
                    default:
                        return redirect()->route('cliente.dashboard');
                }

            } else {
                // Si no existe, revisamos si el email ya está en uso
                $userEmailExists = User::where('email', $googleUser->email)->exists();
                if ($userEmailExists) {
                    return redirect()->route('login')->with('error', 'Este correo ya está registrado. Por favor, inicia sesión con tu contraseña.');
                }

                // Si es nuevo, guardamos sus datos en sesión y lo mandamos a elegir rol
                session(['google_user_data' => $googleUser]);

                // Lo redirigimos al nuevo formulario de registro de Google
                return redirect()->route('google.register.form');
            }

            // El bloque 'switch' de redirección ya no está aquí.

        } catch (\Exception $e) {
            // Manejo de error
            \Log::error('Error en Google Callback: '.$e->getMessage());
            return redirect()->route('login')->with('error', 'Error al iniciar sesión con Google.');
        }
    }

    /**
     * Muestra el formulario para completar el registro de Google.
     */
    public function showGoogleRegisterForm()
    {
        // Asegurarse de que tengamos datos de Google en la sesión
        if (!session()->has('google_user_data')) {
            return redirect()->route('login')->with('error', 'Error en el proceso de Google.');
        }

        $googleUser = session('google_user_data');

        // Pasar los datos de Google a la vista
        return view('google-register', [
            'full_name' => $googleUser->name,
            'email' => $googleUser->email
        ]);
    }

    /**
     * Procesa el formulario de completar registro de Google.
     */
    public function processGoogleRegister(Request $request)
    {
        // 1. Asegurarse de que tengamos datos de Google en la sesión
        if (!session()->has('google_user_data')) {
            return redirect()->route('login')->with('error', 'Tu sesión ha expirado.');
        }
        $googleUser = session('google_user_data');

        // 2. Validar los datos del formulario (rol y campos extra)
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:cliente,restaurante,repartidor',
            'restaurant_address' => 'required_if:role,restaurante|nullable|string|max:200',
            'cuisine_type' => 'required_if:role,restaurante|nullable|string|max:50',
            'contact_phone' => 'required_if:role,restaurante|nullable|string|regex:/^\d{10}$/',
            'vehicle_type' => 'required_if:role,repartidor|nullable|string|max:50',
        ],[
            'contact_phone.regex' => 'El teléfono debe contener 10 dígitos numéricos.',
            'required_if' => 'Este campo es obligatorio para el rol seleccionado.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('google.register.form')
                        ->withErrors($validator)
                        ->withInput();
        }

        // 3. Crear el usuario (combinando Google + Formulario)
        try {
            $user = User::create([
                'google_id' => $googleUser->id,
                'full_name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(Str::random(24)), // Contraseña aleatoria
                'role' => $request->role,
                'restaurant_name' => $request->restaurant_name,
                'restaurant_address' => $request->restaurant_address,
                'cuisine_type' => $request->cuisine_type,
                'contact_phone' => $request->contact_phone,
                'vehicle_type' => $request->vehicle_type,
            ]);

            if ($user->role === 'restaurante') {
                $user->restaurantDetail()->create(['attention_schedule' => null]);
            }

            // 4. Limpiar la sesión y loguear
            session()->forget('google_user_data');
            Auth::login($user);

            // 5. Redirigir al dashboard correcto
            switch ($user->role) {
                case 'restaurante':
                    return redirect()->route('restaurante.dashboard');
                case 'repartidor':
                    return redirect()->route('repartidor.dashboard');
                case 'cliente':
                default:
                    return redirect()->route('cliente.dashboard');
            }

        } catch (\Exception $e) {
            \Log::error('Error en Google Register Process: '.$e->getMessage());
            // Si falla por 'email unique' (porque alguien se registró mientras elegía rol)
            if ($e instanceof \Illuminate\Database\QueryException && str_contains($e->getMessage(), 'UNIQUE constraint failed: users.email')) {
                return redirect()->route('login')->with('error', 'Ese correo ya fue registrado. Intenta iniciar sesión.');
            }
            return redirect()->route('login')->with('error', 'Error interno al crear tu cuenta.');
        }
    }
}