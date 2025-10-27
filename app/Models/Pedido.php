<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'cliente_id',
        'restaurante_id',
        'total',
        'estado',
        'direccion_entrega',
        'notas'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con el cliente
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    // Relación con el restaurante
    public function restaurante()
    {
        return $this->belongsTo(User::class, 'restaurante_id'); 
    }

    // Relación con los detalles del pedido (platillos)
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function repartidor()
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }
}