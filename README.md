<p align="center">
  <img src="https://raw.githubusercontent.com/joshualeba/mercy-foods-laravel/main/public/multimedia/logo.png" width="200" alt="Mercy Food Logo">
</p>

<h1 align="center">
  Mercy Food
</h1>

<p align="center">
  <strong>Una plataforma web de entrega de comida desarrollada con Laravel.</strong>
  <br>
  <br>
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php" alt="PHP Version">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel Version">
  <img src="https://img.shields.io/badge/Licencia-MIT-green?style=for-the-badge" alt="License">
</p>

---

## Acerca del proyecto

**Mercy Food** es una aplicación web robusta y escalable que simula una plataforma de _food delivery_. El sistema está diseñado para conectar a tres tipos de usuarios principales: **Clientes**, **Restaurantes** y **Repartidores**, cada uno con su propio panel de control y funcionalidades específicas para gestionar el ciclo completo de un pedido de comida.

El proyecto está construido siguiendo el patrón de arquitectura **Modelo-Vista-Controlador (MVC)**, asegurando una separación clara de responsabilidades, un código limpio y un mantenimiento sencillo.

### Características principales

* **Sistema de Autenticación por Roles**: Registro e inicio de sesión seguro para Clientes, Restaurantes y Repartidores.
* **Panel de cliente**:
    * Explorar restaurantes y sus menús.
    * Crear, personalizar y confirmar pedidos.
    * Realizar seguimiento del estado de sus pedidos.
    * Gestionar su información de perfil y dirección.
* **Panel de restaurante**:
    * Gestión completa de su menú (Crear, Leer, Actualizar y Eliminar platillos).
    * Visualizar y actualizar el estado de los pedidos entrantes.
    * Administrar la información del perfil del restaurante.
* **Panel de repartidor**:
    * Ver los pedidos disponibles para entrega.
    * Aceptar pedidos y actualizar su estado (en camino, entregado).
    * Gestionar su perfil personal.

---

## Tecnologías utilizadas

* **Backend**: PHP 8.2, Laravel 11
* **Frontend**: HTML5, CSS3, JavaScript, Blade (motor de plantillas de Laravel)
* **Base de Datos**: MySQL
* **Servidor**: Servidor de desarrollo de Laravel (Artisan)
* **Gestor de Dependencias**: Composer, NPM

---

## Guía de instalación y despliegue

Sigue estos pasos para configurar y ejecutar el proyecto en tu entorno de desarrollo local.

### **1. Prerrequisitos**

Asegúrate de tener instalado lo siguiente en tu sistema:
* [PHP](https://www.php.net/downloads.php) (versión 8.2 o superior)
* [Composer](https://getcomposer.org/) (gestor de dependencias para PHP)
* [Node.js y NPM](https://nodejs.org/)
* Un gestor de base de datos como [MySQL](https://www.mysql.com/) o MariaDB.

### **2. Pasos de instalación**

1.  **Clona el repositorio**
    Abre tu terminal y ejecuta el siguiente comando para descargar el proyecto:
    ```sh
    git clone [https://github.com/joshualeba/mercy-foods-laravel.git](https://github.com/joshualeba/mercy-foods-laravel.git)
    ```

2.  **Navega al directorio del proyecto**
    ```sh
    cd mercy-foods-laravel
    ```

3.  **Instala las dependencias de PHP y JavaScript**
    ```sh
    composer install
    npm install
    ```

4.  **Configura el archivo de entorno**
    Crea una copia del archivo `.env.example` y renómbrala a `.env`:
    ```sh
    cp .env.example .env
    ```
    Abre el archivo `.env` recién creado y configura las credenciales de tu base de datos:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=mercy_food_db
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Genera la clave de la aplicación**
    Este comando es crucial para la seguridad de tu aplicación Laravel:
    ```sh
    php artisan key:generate
    ```

6.  **Ejecuta las migraciones y los seeders**
    Este comando creará todas las tablas necesarias en tu base de datos y las poblará con datos de prueba (usuarios, platillos, etc.):
    ```sh
    php artisan migrate --seed
    ```

7.  **Inicia el servidor de desarrollo**
    ```sh
    php artisan serve
    ```

¡Y listo! La aplicación estará corriendo en `http://127.0.0.1:8000`.

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.
