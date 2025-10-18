<?php

namespace App\Http\Controllers;

use App\Models\Faq; // Importa el modelo
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Obtiene todas las preguntas frecuentes de la base de datos.
     * Esta función reemplaza tu endpoint /api/faq de Flask.
     */
    public function index()
    {
        // 1. Simplemente obtiene todos los registros de la tabla 'faq'.
        $faqs = Faq::all();

        // 2. Devuelve los datos en formato JSON.
        return response()->json(['faqs' => $faqs]);
    }
}