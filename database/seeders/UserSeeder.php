<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            // 1. Usuario Cliente
            [
                'full_name' => 'Bepe', // Corregido de 'name' a 'full_name'
                'email' => 'bepe@gmail.com',
                'password' => Hash::make('Contraseña123!'),
                'role' => 'cliente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 2. Usuario Restaurante
            [
                'full_name' => 'Tacos don Chuy', // Corregido
                'email' => 'tacoschuy@gmail.com',
                'password' => Hash::make('Contraseña123!'),
                'role' => 'restaurante',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 3. Usuario Repartidor
            [
                'full_name' => 'Bepe Repartidor', // Corregido
                'email' => 'beperepartidor@gmail.com',
                'password' => Hash::make('Contraseña123!'),
                'role' => 'repartidor',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}