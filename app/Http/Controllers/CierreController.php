<?php

namespace App\Http\Controllers;

use App\Models\Barberia;
use App\Models\Corte;
use App\Models\CierreDiario;
use App\Models\Gasto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CierreController extends Controller
{
    private function obtenerBarberiaActiva(): Barberia
    {
        return Barberia::firstOrCreate(
            ['slug' => 'barberia-principal'],
            ['nombre' => 'Mi Barbería Profesional', 'porcentaje_barbero' => 60]
        );
    }

    /**
     * Muestra la pantalla de pre-cierre con los totales acumulados pendientes.
     */
    public function index()
    {
        $barberia = $this->obtenerBarberiaActiva();

        // Cortes cobrados que aún no han sido procesados en ningún cierre
        $citasPendientesCierre = Corte::with(['servicio', 'barbero', 'cliente'])
            ->where('barberia_id', $barberia->id)
            ->where('pago_completado', true)
            ->sinCerrar()
            ->latest('fecha_hora')
            ->get();

        // Totales del pre-cierre calculados en memoria
        $totalEfectivo      = $citasPendientesCierre->where('metodo_pago', 'efectivo')->sum('precio');
        $totalTransferencia = $citasPendientesCierre->where('metodo_pago', 'transferencia')->sum('precio');
        $totalIngresos      = $totalEfectivo + $totalTransferencia;

        // Fiados del día actual aún pendientes
        $totalFiado = Corte::where('barberia_id', $barberia->id)
            ->where('estado', 'fiado')
            ->where('pago_completado', false)
            ->sum('precio');

        // Gastos sin cierre asignado
        $gastosPendientes = Gasto::where('barberia_id', $barberia->id)->get();
        $totalGastos      = $gastosPendientes->sum('monto');

        // Neto real = ingresos cobrados - gastos operativos
        $netoReal = $totalIngresos - $totalGastos;

        // Historial de los últimos 7 cierres
        $historialCierres = CierreDiario::where('barberia_id', $barberia->id)
            ->latest('fecha')
            ->take(7)
            ->get();

        return view('finanzas.cierre', compact(
            'citasPendientesCierre',
            'totalEfectivo',
            'totalTransferencia',
            'totalIngresos',
            'totalFiado',
            'totalGastos',
            'netoReal',
            'historialCierres'
        ));
    }

    /**
     * Ejecuta el cierre de caja general de forma atómica.
     */
    public function store(Request $request)
    {
        $request->validate([
            'notas' => 'nullable|string|max:500',
        ]);

        $barberia = $this->obtenerBarberiaActiva();
        $hoy      = Carbon::today()->toDateString();

        $cierreExistente = CierreDiario::where('barberia_id', $barberia->id)
            ->where('fecha', $hoy)
            ->first();

        if ($cierreExistente) {
            return redirect()->route('cierre.index')
                ->with('error', "Ya existe un cierre de caja registrado para el día de hoy.");
        }

        $citasElegibles = Corte::with('servicio')
            ->where('barberia_id', $barberia->id)
            ->where('pago_completado', true)
            ->sinCerrar()
            ->get();

        if ($citasElegibles->isEmpty()) {
            return redirect()->route('cierre.index')
                ->with('error', 'No hay cortes pendientes de cierre.');
        }

        $totalEfectivo      = $citasElegibles->where('metodo_pago', 'efectivo')->sum('precio');
        $totalTransferencia = $citasElegibles->where('metodo_pago', 'transferencia')->sum('precio');
        $totalIngresos      = $totalEfectivo + $totalTransferencia;

        $totalFiado = Corte::where('barberia_id', $barberia->id)
            ->where('estado', 'fiado')
            ->where('pago_completado', false)
            ->sum('precio');

        DB::transaction(function () use (
            $barberia, $hoy, $citasElegibles,
            $totalEfectivo, $totalTransferencia, $totalIngresos, $totalFiado,
            $request
        ) {
            $cierre = CierreDiario::create([
                'barberia_id'         => $barberia->id,
                'fecha'               => $hoy,
                'total_citas'         => $citasElegibles->count(),
                'total_efectivo'      => $totalEfectivo,
                'total_transferencia' => $totalTransferencia,
                'total_ingresos'      => $totalIngresos,
                'total_fiado'         => $totalFiado,
                'notas'               => $request->notas,
            ]);

            Corte::whereIn('id', $citasElegibles->pluck('id'))
                ->update([
                    'cierre_diario_id' => $cierre->id,
                    'estado'           => 'cerrada',
                ]);
        });

        return redirect()->route('cierre.index')
            ->with('success', "Cierre de caja completado exitosamente.");
    }
}