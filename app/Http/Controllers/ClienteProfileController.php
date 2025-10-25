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
}