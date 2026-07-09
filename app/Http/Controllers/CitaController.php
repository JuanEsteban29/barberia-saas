<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    // Mostrar todas las citas con su información relacionada
    public function index()
    {
        $citas = Cita::with(['barberia', 'cliente', 'barbero', 'servicio'])->get();
        
        return response()->json($citas);
    }
}