<p align="center">
  <img src="https://raw.githubusercontent.com/joshualeba/mercy-foods-laravel/main/public/multimedia/logo.png" width="200" alt="Mercy Food Logo">
</p>

<h1 align="center">
  Mercy Food
</h1>

<p align="center">
  <strong>Una plataforma web de entrega de comida desarrollada con Laravel y SQLite.</strong>
  <br>
  <br>
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php" alt="PHP Version">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel Version">
  <img src="https://img.shields.io/badge/Database-SQLite-003B57?style=for-the-badge&logo=sqlite" alt="Database">
</p>

---

## Acerca del proyecto

**Mercy Food** es una aplicación web robusta y escalable que simula una plataforma de _food delivery_. El sistema está diseñado para conectar a tres tipos de usuarios principales: **Clientes**, **Restaurantes** y **Repartidores**, cada uno con su propio panel de control y funcionalidades específicas para gestionar el ciclo completo de un pedido de comida.

El proyecto está construido siguiendo el patrón de arquitectura **Modelo-Vista-Controlador (MVC)**, asegurando una separación clara de responsabilidades y un código limpio.

### Características principales

* **Sistema de autenticación por roles**: Registro e inicio de sesión seguro para Clientes, Restaurantes y Repartidores.
* **Panel de cliente**: Explorar menús, crear pedidos y dar seguimiento a su estado.
* **Panel de restaurante**: Gestión completa de su menú (CRUD) y administración de pedidos entrantes.
* **Panel de repartidor**: Visualización y aceptación de pedidos disponibles para entrega.

---

## Guía de instalación y despliegue

Sigue estos pasos para configurar y ejecutar el proyecto en tu entorno de desarrollo local.

### **1. Prerrequisitos**

Asegúrate de tener instalado lo siguiente en tu sistema:
* [PHP](https://www.php.net/downloads.php) (versión 8.2 o superior)
* [Composer](https://getcomposer.org/) (gestor de dependencias para PHP)
* La extensión de PHP para SQLite (generalmente viene activada por defecto en entornos como Laragon o XAMPP).

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

3.  **Instala las dependencias de PHP**
    ```sh
    composer install
    ```

4.  **Crea el archivo de la base de datos**
    Dentro de la carpeta `database/`, crea un archivo vacío llamado `database.sqlite`.
    ```sh
    touch database/database.sqlite
    ```
    *(Si estás en Windows y el comando `touch` no funciona, simplemente crea el archivo manualmente con el explorador de archivos).*

5.  **Configura el archivo de entorno**
    Crea una copia del archivo `.env.example` y renómbrala a `.env`:
    ```sh
    cp .env.example .env
    ```
    No necesitas modificar nada más en este archivo, ya que por defecto viene configurado para usar SQLite.

6.  **Genera la clave de la aplicación**
    Este comando es crucial para la seguridad de tu aplicación Laravel:
    ```sh
    php artisan key:generate
    ```

7.  **Ejecuta las migraciones**
    Este comando creará todas las tablas necesarias en tu archivo `database.sqlite` y agregará datos de prueba:
    ```sh
    php artisan migrate --seed
    ```

8.  **Inicia el servidor de desarrollo**
    ```sh
    php artisan serve
    ```

¡Y listo! La aplicación estará corriendo en `http://127.0.0.1:8000`.

### Datos de prueba (Seeders)

El proyecto incluye datos de prueba para que puedas ingresar y probar los tres roles principales.

La contraseña para todas las cuentas es: `Contraseña123!`

* **Cliente**:
    * **Email**: `bepe@gmail.com`
* **Restaurante**:
    * **Email**: `tacoschuy@gmail.com`
* **Repartidor**:
    * **Email**: `beperepartidor@gmail.com`

---

## Documentación adicional

Puedes encontrar la documentación técnica completa del proyecto (diagramas E-R, casos de uso, arquitectura y reglas de negocio) en la carpeta [`/docs`](./docs/).

---

## Licencia

Este proyecto está bajo la Licencia MIT.

