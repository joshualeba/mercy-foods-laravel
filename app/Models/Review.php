<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'cliente_id',
        'restaurante_id',
        'repartidor_id',
        'rating_restaurante',
        'comentario_restaurante',
        'rating_repartidor',
        'comentario_repartidor',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function restaurante()
    {
        return $this->belongsTo(User::class, 'restaurante_id');
    }

    public function repartidor()
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }
}