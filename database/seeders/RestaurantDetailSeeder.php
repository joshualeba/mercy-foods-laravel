<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestaurantDetailSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('restaurant_details')->insert([
            [
                'user_id' => 2, // ID del usuario "Tacos don Chuy"
                // Esta es la única columna que llenaremos de esta tabla
                'attention_schedule' => 'Martes a Domingo de 18:00 a 24:00', 
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 4, // ID de Sushi Zen
                'attention_schedule' => 'Lunes a Sábado de 13:00 a 22:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5, // ID de Bella Notte Trattoria
                'attention_schedule' => 'Miércoles a Domingo de 17:00 a 23:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 6, // ID de Burger Joint
                'attention_schedule' => 'Todos los días de 12:00 a 23:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}