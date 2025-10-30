# Mercy Foods 🍔

Una breve descripción de tu proyecto. Por ejemplo: "Mercy Foods es una plataforma web de entrega de comida que conecta a restaurantes, repartidores y clientes, desarrollada con el framework Laravel."

## ✨ Características Principales

* Registro y autenticación de usuarios con 3 roles distintos (Cliente, Restaurante, Repartidor).
* Gestión de menú (CRUD) para los restaurantes.
* Creación y seguimiento de pedidos en tiempo real.
* Actualización de perfiles para todos los roles.
* Dashboards personalizados según el rol del usuario.

## 🚀 Guía de Instalación y Despliegue

Estos son los pasos para instalar y ejecutar el proyecto en un entorno de desarrollo local.

### **Requisitos Previos**

* PHP 8.2 o superior
* Composer
* Node.js y NPM
* Una base de datos (ej. MySQL, MariaDB)

### **Pasos de Instalación**

1.  **Clonar el repositorio:**
    ```sh
    git clone [https://github.com/tu-usuario/mercy-foods-laravel.git](https://github.com/tu-usuario/mercy-foods-laravel.git)
    cd mercy-foods-laravel
    ```

2.  **Instalar dependencias:**
    ```sh
    composer install
    npm install
    ```

3.  **Configurar el entorno:**
    Copia el archivo de ejemplo `.env.example` y renómbralo a `.env`.
    ```sh
    cp .env.example .env
    ```
    Luego, abre el archivo `.env` y configura tus credenciales de base de datos (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

4.  **Generar la clave de la aplicación:**
    ```sh
    php artisan key:generate
    ```

5.  **Ejecutar las migraciones y seeders:**
    Esto creará las tablas en tu base de datos y la llenará con datos de prueba.
    ```sh
    php artisan migrate --seed
    ```

6.  **Iniciar el servidor:**
    ```sh
    php artisan serve
    ```

¡Listo! Ahora puedes acceder a la aplicación en `http://127.0.0.1:8000`.

##  credentials de prueba
* Cliente: email@cliente.com / password
* Restaurante: email@restaurante.com / password
