<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantDetail extends Model
{
    use HasFactory;

    // Campos que permitimos que se llenen masivamente
    protected $fillable = [
        'user_id',
        'restaurant_address',
        'cuisine_type',
        'contact_phone',
        'attention_schedule',
    ];

    // Relación inversa: Un detalle pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}