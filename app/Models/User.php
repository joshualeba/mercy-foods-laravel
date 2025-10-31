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
}