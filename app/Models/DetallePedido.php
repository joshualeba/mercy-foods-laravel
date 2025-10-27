<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $fillable = [
        'pedido_id',
        'platillo_id',
        'cantidad',
        'precio_unitario',
    ];

    public function platillo()
    {
        return $this->belongsTo(Platillo::class);
    }
}