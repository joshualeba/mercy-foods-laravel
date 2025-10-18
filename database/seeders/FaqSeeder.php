<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $sampleFaqs = [
        [
            'question' => '¿Cómo funciona Mercy Food para los clientes?',
            'answer' => 'Es muy fácil. Simplemente buscas tu platillo o restaurante favorito en nuestra plataforma, seleccionas lo que quieres ordenar del menú, pagas de forma segura a través de la aplicación y uno de nuestros repartidores afiliados te lo llevará directamente a tu puerta. ¡Así de simple apoyas a los negocios locales!'
        ],
        [
            'question' => '¿Qué métodos de pago son aceptados y qué tan seguros son?',
            'answer' => 'Aceptamos las principales tarjetas de crédito y débito. La seguridad es nuestra máxima prioridad, por lo que toda tu información de pago viaja encriptada y procesamos las transacciones cumpliendo con los más altos estándares de seguridad para proteger tus datos en todo momento.'
        ],
        [
            'question' => 'Tengo un restaurante, ¿cómo puedo asociarme con Mercy Food?',
            'answer' => '¡Nos encantaría que te unieras! El proceso es sencillo: ve a nuestra página de registro, selecciona la opción "Soy restaurante" y completa la información de tu negocio. Nuestro equipo revisará tu solicitud para darte acceso a las herramientas con las que podrás gestionar tu menú, recibir pedidos y aumentar tus ventas llegando a miles de nuevos clientes.'
        ],
        [
            'question' => 'Quiero ser repartidor, ¿qué necesito para empezar?',
            'answer' => 'Para ser repartidor de Mercy Food, solo necesitas ser mayor de edad, tener un vehículo (motocicleta, bicicleta o automóvil) y un smartphone. Regístrate en nuestra plataforma seleccionando "Soy repartidor", completa tu perfil y, una vez aprobado, podrás empezar a recibir notificaciones de pedidos para generar ingresos con un horario totalmente flexible.'
        ],
        [
            'question' => '¿Qué debo hacer si hay un problema con mi pedido?',
            'answer' => 'Si tienes algún inconveniente, como un retraso o un error en tu orden, por favor contacta a nuestro equipo de soporte a través del chat en vivo disponible en la aplicación. Nuestro objetivo es ayudarte a resolver cualquier problema de la manera más rápida y eficiente posible para garantizar tu satisfacción.'
        ]
    ];

        foreach ($sampleFaqs as $faq) {
            Faq::create($faq);
        }
    }
}