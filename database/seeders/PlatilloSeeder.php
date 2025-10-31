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

        $restaurantesConPlatillos = [
            // 1. Restaurante original (Tacos don Chuy - ID 2)
            [
                'user_id' => 2,
                'platillos' => [
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
                ]
            ],
            // 2. Restaurante Japonés (Sushi Zen - ID 4)
            [
                'user_id' => 4,
                'platillos' => [
                    [
                        'nombre' => 'Rollos California (8 pz)',
                        'descripcion' => 'Rollos de sushi con cangrejo, aguacate y pepino.',
                        'precio' => 120.00,
                        'imagen_local' => 'sushi_california.jpg'
                    ],
                    [
                        'nombre' => 'Ramen Tonkotsu',
                        'descripcion' => 'Caldo de cerdo intenso, fideos, chashu, huevo marinado y nori.',
                        'precio' => 150.00,
                        'imagen_local' => 'ramen.jpg'
                    ],
                    [
                        'nombre' => 'Gyozas de Cerdo (6 pz)',
                        'descripcion' => 'Empanadillas japonesas al vapor y plancha, rellenas de cerdo y vegetales.',
                        'precio' => 85.00,
                        'imagen_local' => 'gyozas.jpg'
                    ]
                ]
            ],
            // 3. Restaurante Italiano (Bella Notte - ID 5)
            [
                'user_id' => 5,
                'platillos' => [
                    [
                        'nombre' => 'Pasta Carbonara',
                        'descripcion' => 'Spaghetti con salsa cremosa de huevo, queso Pecorino y guanciale.',
                        'precio' => 160.00,
                        'imagen_local' => 'pasta_carbonara.jpg'
                    ],
                    [
                        'nombre' => 'Pizza Margherita',
                        'descripcion' => 'Salsa de tomate, mozzarella fresca, albahaca y aceite de oliva.',
                        'precio' => 170.00,
                        'imagen_local' => 'pizza_margherita.jpg'
                    ],
                    [
                        'nombre' => 'Lasaña Boloñesa',
                        'descripcion' => 'Capas de pasta, salsa boloñesa de res, salsa bechamel y queso parmesano.',
                        'precio' => 180.00,
                        'imagen_local' => 'lasana.jpg'
                    ]
                ]
            ],
            // 4. Restaurante Americano (Burger Joint - ID 6)
            [
                'user_id' => 6,
                'platillos' => [
                    [
                        'nombre' => 'Doble Cheeseburger',
                        'descripcion' => 'Doble carne de res, doble queso americano, tocino y salsa especial.',
                        'precio' => 140.00,
                        'imagen_local' => 'doble_burger.jpg'
                    ],
                    [
                        'nombre' => 'Alitas BBQ (10 pz)',
                        'descripcion' => 'Alitas de pollo crujientes bañadas en salsa BBQ, con aderezo ranch.',
                        'precio' => 130.00,
                        'imagen_local' => 'alitas_bbq.jpg'
                    ],
                    [
                        'nombre' => 'Hot Dog Clásico',
                        'descripcion' => 'Salchicha de res, pan suave, mostaza, catsup y relish.',
                        'precio' => 75.00,
                        'imagen_local' => 'hot_dog.jpg'
                    ]
                ]
            ]
        ];

        foreach ($restaurantesConPlatillos as $restaurante) {
            $userId = $restaurante['user_id'];
            
            foreach ($restaurante['platillos'] as $platillo) {
                $origen = database_path('seeders/images/' . $platillo['imagen_local']);
                $destino = 'platillos/' . uniqid() . '_' . $platillo['imagen_local'];

                if (!file_exists($origen)) {
                    $this->command->error("Advertencia: Imagen no encontrada, se omitirá: " . $platillo['imagen_local']);
                    continue; 
                }

                Storage::disk('public')->put($destino, file_get_contents($origen));

                DB::table('platillos')->insert([
                    'user_id' => $userId,
                    'nombre' => $platillo['nombre'],
                    'descripcion' => $platillo['descripcion'],
                    'precio' => $platillo['precio'],
                    'imagen_url' => $destino,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}