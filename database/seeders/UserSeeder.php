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
                'cuisine_type' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 2. Usuario Restaurante
            [
                'full_name' => 'Tacos don Chuy', // Corregido
                'email' => 'tacoschuy@gmail.com',
                'password' => Hash::make('Contraseña123!'),
                'role' => 'restaurante',
                'cuisine_type' => 'mexicana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 3. Usuario Repartidor
            [
                'full_name' => 'Bepe Repartidor', // Corregido
                'email' => 'beperepartidor@gmail.com',
                'password' => Hash::make('Contraseña123!'),
                'role' => 'repartidor',
                'cuisine_type' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 4. Restaurante Japonés
            [
                'full_name' => 'Sushi Zen',
                'email' => 'sushizen@gmail.com',
                'password' => Hash::make('Contraseña123!'),
                'role' => 'restaurante',
                'cuisine_type' => 'japonesa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 5. Restaurante Italiano
            [
                'full_name' => 'Bella Notte Trattoria',
                'email' => 'bellanotte@gmail.com',
                'password' => Hash::make('Contraseña123!'),
                'role' => 'restaurante',
                'cuisine_type' => 'italiana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 6. Restaurante Americano
            [
                'full_name' => 'Burger Joint',
                'email' => 'burgerjoint@gmail.com',
                'password' => Hash::make('Contraseña123!'),
                'role' => 'restaurante',
                'cuisine_type' => 'americana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}