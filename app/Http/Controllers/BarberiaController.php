<?php

namespace App\Http\Controllers;

use App\Models\Barberia;
use Illuminate\Http\Request;

class BarberiaController extends Controller
{
    // Listar todas las barberías del SaaS
    public function index()
    {
        $barberias = Barberia::all();
        return response()->json($barberias); // Por ahora devolvemos JSON para probar que todo funcione
    }

    // Mostrar una barbería específica con sus servicios gracias a la relación
    public function show($slug)
    {
        $barberia = Barberia::with('servicios')->where('slug', $slug)->firstOrFail();
        return response()->json($barberia);
    }
}