<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'cliente_id',
        'restaurante_id',
        'total',
        'subtotal',       
        'costo_envio',          
        'comision_plataforma', 
        'estado',
        'direccion_entrega',
        'notas'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',          
        'costo_envio' => 'decimal:2',         
        'comision_plataforma' => 'decimal:2',
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

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}