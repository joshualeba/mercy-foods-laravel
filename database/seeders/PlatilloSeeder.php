<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PlatilloSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('platillos');
        Storage::disk('public')->makeDirectory('platillos');

        $platillos = [
            [
                'nombre' => 'Tacos al Pastor (Orden de 3)',
                'descripcion' => 'Deliciosos tacos de cerdo marinado con su tradicional trozo de piña, cilantro y cebolla. ¡Un clásico!',
                'precio' => 65.00,
                'imagen_local' => 'tacos.jpg'
            ],
            [
                'nombre' => 'Hamburguesa Clásica',
                'descripcion' => 'Jugosa carne de res de 150g con queso americano, lechuga, tomate fresco y cebolla morada en pan brioche.',
                'precio' => 110.50,
                'imagen_local' => 'hamburguesa.jpg'
            ],
            [
                'nombre' => 'Pizza de Pepperoni Grande',
                'descripcion' => 'La favorita de todos. Abundante pepperoni y queso mozzarella sobre nuestra salsa de tomate especial.',
                'precio' => 180.00,
                'imagen_local' => 'pizza.jpg'
            ]
        ];

        foreach ($platillos as $platillo) {
            $origen = database_path('seeders/images/' . $platillo['imagen_local']);
            $destino = 'platillos/' . uniqid() . '_' . $platillo['imagen_local'];

            Storage::disk('public')->put($destino, file_get_contents($origen));

            DB::table('platillos')->insert([
                'user_id' => 2, // Corregido de 'restaurante_id' a 'user_id'
                'nombre' => $platillo['nombre'],
                'descripcion' => $platillo['descripcion'],
                'precio' => $platillo['precio'],
                'imagen_url' => $destino, // Corregido a imagen_url
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}