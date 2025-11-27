<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Los atributos que se pueden asignar de forma masiva.
     */
    protected $fillable = [
         'full_name',
         'google_id',
         'email',
         'password',
         'role',
         'restaurant_address',
         'cuisine_type',
         'contact_phone',
         'vehicle_type',
         'address',
        'card_last_four',
        'card_expiry',
        'card_name',
        'paypal_email',
        'paypal_payer_id',
     ];

    /**
     * Los atributos que deben ocultarse.
     */
    protected $hidden = [ 'password', 'remember_token' ];

    /**
     * Atributos que deben ser casteados.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Esto encripta la contraseña automáticamente
        ];
    }

    public function platillos()
    {
        return $this->hasMany(Platillo::class);
    }

    public function restaurantDetail()
    {
        return $this->hasOne(RestaurantDetail::class);
    }

    public function pedidosRestaurante()
    {
        return $this->hasMany(Pedido::class, 'restaurante_id');
    }

    public function pedidosCliente()
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    // Relaciones para reseñas
    public function reviewsAsRestaurante()
    {
        return $this->hasMany(Review::class, 'restaurante_id');
    }

    public function reviewsAsRepartidor()
    {
        return $this->hasMany(Review::class, 'repartidor_id');
    }

    // Método para obtener el promedio de calificación como restaurante
    public function getAverageRatingRestauranteAttribute()
    {
        return $this->reviewsAsRestaurante()->avg('rating_restaurante') ?? 0;
    }

    // Método para obtener el promedio de calificación como repartidor
    public function getAverageRatingRepartidorAttribute()
    {
        return $this->reviewsAsRepartidor()->avg('rating_repartidor') ?? 0;
    }

    // Método para obtener el total de reseñas como restaurante
    public function getTotalReviewsRestauranteAttribute()
    {
        return $this->reviewsAsRestaurante()->count();
    }

    // Método para obtener el total de reseñas como repartidor
    public function getTotalReviewsRepartidorAttribute()
    {
        return $this->reviewsAsRepartidor()->count();
    }
}