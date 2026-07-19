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

    /**
     * Muestra la vista de configuración de la barbería.
     */
    public function editSettings()
    {
        // En un entorno multi-inquilino real se usaría el de la sesión.
        // Aquí usamos el predeterminado por diseño de esta demo.
        $barberia = Barberia::firstOrCreate(
            ['slug' => 'barberia-principal'],
            ['nombre' => 'Mi Barbería Profesional', 'porcentaje_barbero' => 60, 'tasa_bcv_modo' => 'auto']
        );

        return view('configuracion.index', compact('barberia'));
    }

    /**
     * Guarda la configuración actualizada de la barbería.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'nombre'             => 'required|string|max:255',
            'porcentaje_barbero' => 'required|integer|between:0,100',
            'tasa_bcv_modo'      => 'required|in:auto,manual',
            'tasa_bcv_manual'    => 'nullable|numeric|min:0',
            'logo'               => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $barberia = Barberia::firstOrCreate(
            ['slug' => 'barberia-principal'],
            ['nombre' => 'Mi Barbería Profesional', 'porcentaje_barbero' => 60, 'tasa_bcv_modo' => 'auto']
        );

        $data = [
            'nombre'             => $request->nombre,
            'porcentaje_barbero' => $request->porcentaje_barbero,
            'tasa_bcv_modo'      => $request->tasa_bcv_modo,
            'tasa_bcv_manual'    => $request->tasa_bcv_manual,
        ];

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageData = base64_encode(file_get_contents($image->getRealPath()));
            $mimeType = $image->getClientMimeType();
            $data['logo'] = 'data:' . $mimeType . ';base64,' . $imageData;
        }

        $barberia->update($data);

        // Olvidar el caché para forzar la actualización inmediata de la tasa en el sistema
        \Illuminate\Support\Facades\Cache::forget('tasa_bcv_dia');

        return redirect()->back()->with('success', 'Configuración de la barbería guardada correctamente.');
    }
}