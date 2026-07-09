<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Corte;
use App\Models\Cliente;
use App\Models\Comision;
use App\Models\Servicio;
use App\Models\Barberia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CorteController extends Controller
{
    private function obtenerBarberiaActiva()
    {
        return Barberia::firstOrCreate(
            ['slug' => 'barberia-principal'],
            ['nombre' => 'Mi Barbería Profesional', 'porcentaje_barbero' => 60]
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_nombre' => 'nullable|string|max:255',
            'barbero_id'     => 'required|exists:users,id',
            'servicio_id'    => 'required|exists:servicios,id',
            'metodo_pago'    => 'required|in:efectivo,transferencia,fiado',
        ]);

        $servicio = Servicio::findOrFail($request->servicio_id);
        $barberia = $this->obtenerBarberiaActiva();

        // Determinar o crear el cliente por nombre
        $nombreCliente = trim($request->cliente_nombre) ?: 'Cliente General';
        $cliente = Cliente::firstOrCreate(['nombre' => $nombreCliente]);

        DB::transaction(function () use ($request, $servicio, $barberia, $cliente) {
            $isFiado = ($request->metodo_pago === 'fiado');

            // 1. Guardar el Corte
            $corte = Corte::create([
                'barberia_id'     => $barberia->id,
                'cliente_id'      => $cliente->id,
                'barbero_id'      => $request->barbero_id,
                'servicio_id'     => $servicio->id,
                'precio'          => $servicio->precio,
                'fecha_hora'      => Carbon::now(),
                'metodo_pago'     => $isFiado ? null : $request->metodo_pago,
                'estado'          => $isFiado ? 'fiado' : 'completada',
                'pago_completado' => !$isFiado,
            ]);

            // 2. Calcular Comisiones: 60% barbero, 40% negocio
            $porcentajeBarbero = $barberia->porcentaje_barbero ?? 60.00;
            $montoBarbero = $servicio->precio * ($porcentajeBarbero / 100);
            $montoNegocio = $servicio->precio * ((100 - $porcentajeBarbero) / 100);

            Comision::create([
                'corte_id'           => $corte->id,
                'barbero_id'         => $request->barbero_id,
                'monto_barbero'      => $montoBarbero,
                'monto_negocio'      => $montoNegocio,
                'porcentaje_barbero' => $porcentajeBarbero,
            ]);
        });

        return redirect()->back()->with('success', '¡Corte registrado y comisión calculada correctamente!');
    }
}