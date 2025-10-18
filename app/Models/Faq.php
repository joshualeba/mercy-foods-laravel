<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    // Le dice a Laravel que no espere las columnas created_at y updated_at
    public $timestamps = false;

    // Especifica el nombre correcto de la tabla
    protected $table = 'faq';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question',
        'answer',
    ];
}