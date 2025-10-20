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
        'restaurant_name',
        'email',
        'password',
        'role',
        'restaurant_address',
        'restaurant_description',
        'cuisine_type',
        'opening_hours',
        'contact_phone',
        'vehicle_type',
        'profile_image_url',
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
}