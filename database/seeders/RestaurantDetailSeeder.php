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
            ]
        ]);
    }
}