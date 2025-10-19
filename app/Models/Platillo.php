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
}
