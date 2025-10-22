<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platillo extends Model
{
    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'precio',
        'imagen_url',
        'disponible',
    ];

    /**
     * Define la relación inversa: Un platillo pertenece a un usuario (restaurante).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}