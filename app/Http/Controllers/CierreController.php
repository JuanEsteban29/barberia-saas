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
        $citasPendientesCierre = Corte::with(['servicio', 'barbero', 'cliente', 'comision'])
            ->where('barberia_id', $barberia->id)
            ->where('pago_completado', true)
            ->sinCerrar()
            ->latest('fecha_hora')
            ->get();

        // Totales del pre-cierre calculados en memoria con efectivo dividido
        $totalEfectivoUsd    = $citasPendientesCierre->where('metodo_pago', 'efectivo_usd')->sum('precio');
        $totalEfectivoBs     = $citasPendientesCierre->where('metodo_pago', 'efectivo_bs')->sum('precio');
        $totalEfectivoLegacy = $citasPendientesCierre->where('metodo_pago', 'efectivo')->sum('precio');
        $totalEfectivo       = $totalEfectivoUsd + $totalEfectivoBs + $totalEfectivoLegacy;

        $totalTransferencia  = $citasPendientesCierre->where('metodo_pago', 'transferencia')->sum('precio');
        $totalIngresos       = $totalEfectivo + $totalTransferencia;

        // Fiados del día actual aún pendientes
        $totalFiado = Corte::where('barberia_id', $barberia->id)
            ->where('estado', 'fiado')
            ->where('pago_completado', false)
            ->sum('precio');

        // Gastos sin cierre asignado
        $gastosPendientes = Gasto::where('barberia_id', $barberia->id)->get();
        $totalGastos      = $gastosPendientes->sum('monto');

        // Sumar comisiones de las citas a cerrar
        $totalComisiones = $citasPendientesCierre->sum(function ($cita) {
            return $cita->comision ? $cita->comision->monto_barbero : 0;
        });

        // Neto real = ingresos cobrados - gastos operativos - comisiones barberos
        $netoReal = $totalIngresos - $totalGastos - $totalComisiones;

        // Historial de los últimos 7 cierres
        $historialCierres = CierreDiario::where('barberia_id', $barberia->id)
            ->latest('fecha')
            ->take(7)
            ->get();

        return view('finanzas.cierre', compact(
            'citasPendientesCierre',
            'totalEfectivo',
            'totalEfectivoUsd',
            'totalEfectivoBs',
            'totalEfectivoLegacy',
            'totalTransferencia',
            'totalIngresos',
            'totalFiado',
            'totalGastos',
            'totalComisiones',
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
            'notas'                   => 'nullable|string|max:500',
            'efectivo_usd_contado'    => 'nullable|numeric|min:0',
            'efectivo_bs_contado'     => 'nullable|numeric|min:0',
            'transferencia_contado'   => 'nullable|numeric|min:0',
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

        $citasElegibles = Corte::with(['servicio', 'barbero', 'cliente'])
            ->where('barberia_id', $barberia->id)
            ->where('pago_completado', true)
            ->sinCerrar()
            ->get();

        if ($citasElegibles->isEmpty()) {
            return redirect()->route('cierre.index')
                ->with('error', 'No hay cortes pendientes de cierre.');
        }

        $totalEfectivoUsd    = $citasElegibles->where('metodo_pago', 'efectivo_usd')->sum('precio');
        $totalEfectivoBs     = $citasElegibles->where('metodo_pago', 'efectivo_bs')->sum('precio');
        $totalEfectivoLegacy = $citasElegibles->where('metodo_pago', 'efectivo')->sum('precio');
        $totalEfectivo       = $totalEfectivoUsd + $totalEfectivoBs + $totalEfectivoLegacy;

        $totalTransferencia = $citasElegibles->where('metodo_pago', 'transferencia')->sum('precio');
        $totalIngresos      = $totalEfectivo + $totalTransferencia;

        $totalFiado = Corte::where('barberia_id', $barberia->id)
            ->where('estado', 'fiado')
            ->where('pago_completado', false)
            ->sum('precio');

        // --- Calcular diferencias de cuadre si se proporcionaron conteos físicos ---
        $efectivoUsdContado  = $request->filled('efectivo_usd_contado') ? (float)$request->efectivo_usd_contado : null;
        $efectivoBsContado   = $request->filled('efectivo_bs_contado')  ? (float)$request->efectivo_bs_contado  : null;
        $transferenciaContado = $request->filled('transferencia_contado') ? (float)$request->transferencia_contado : null;

        $diferencias = [];
        if ($efectivoUsdContado !== null) {
            $esperado = $totalEfectivoUsd + $totalEfectivoLegacy;
            $diff = $efectivoUsdContado - $esperado;
            if (abs($diff) > 0.005) {
                $diferencias[] = [
                    'metodo'   => 'Efectivo USD ($)',
                    'esperado' => $esperado,
                    'contado'  => $efectivoUsdContado,
                    'diff'     => $diff,
                    'sospechosos' => $citasElegibles
                        ->whereIn('metodo_pago', ['efectivo_usd', 'efectivo'])
                        ->sortByDesc('precio')
                        ->take(3)
                        ->map(fn($c) => [
                            'cliente' => $c->cliente->nombre ?? 'General',
                            'barbero' => $c->barbero->name ?? 'N/A',
                            'hora'    => $c->fecha_hora->format('H:i'),
                            'monto'   => $c->precio,
                        ])->values()->toArray(),
                ];
            }
        }
        if ($efectivoBsContado !== null) {
            $diff = $efectivoBsContado - $totalEfectivoBs;
            if (abs($diff) > 0.005) {
                $diferencias[] = [
                    'metodo'   => 'Efectivo Bs.',
                    'esperado' => $totalEfectivoBs,
                    'contado'  => $efectivoBsContado,
                    'diff'     => $diff,
                    'sospechosos' => $citasElegibles
                        ->where('metodo_pago', 'efectivo_bs')
                        ->sortByDesc('precio')
                        ->take(3)
                        ->map(fn($c) => [
                            'cliente' => $c->cliente->nombre ?? 'General',
                            'barbero' => $c->barbero->name ?? 'N/A',
                            'hora'    => $c->fecha_hora->format('H:i'),
                            'monto'   => $c->precio,
                        ])->values()->toArray(),
                ];
            }
        }
        if ($transferenciaContado !== null) {
            $diff = $transferenciaContado - $totalTransferencia;
            if (abs($diff) > 0.005) {
                $diferencias[] = [
                    'metodo'   => 'Banco / Transferencia',
                    'esperado' => $totalTransferencia,
                    'contado'  => $transferenciaContado,
                    'diff'     => $diff,
                    'sospechosos' => $citasElegibles
                        ->where('metodo_pago', 'transferencia')
                        ->sortByDesc('precio')
                        ->take(3)
                        ->map(fn($c) => [
                            'cliente' => $c->cliente->nombre ?? 'General',
                            'barbero' => $c->barbero->name ?? 'N/A',
                            'hora'    => $c->fecha_hora->format('H:i'),
                            'monto'   => $c->precio,
                        ])->values()->toArray(),
                ];
            }
        }

        DB::transaction(function () use (
            $barberia, $hoy, $citasElegibles,
            $totalEfectivo, $totalTransferencia, $totalIngresos, $totalFiado,
            $efectivoUsdContado, $efectivoBsContado, $transferenciaContado,
            $request
        ) {
            $cierre = CierreDiario::create([
                'barberia_id'          => $barberia->id,
                'fecha'                => $hoy,
                'total_citas'          => $citasElegibles->count(),
                'total_efectivo'       => $totalEfectivo,
                'total_transferencia'  => $totalTransferencia,
                'total_ingresos'       => $totalIngresos,
                'total_fiado'          => $totalFiado,
                'notas'                => $request->notas,
                'efectivo_usd_contado' => $efectivoUsdContado,
                'efectivo_bs_contado'  => $efectivoBsContado,
                'transferencia_contado'=> $transferenciaContado,
            ]);

            Corte::whereIn('id', $citasElegibles->pluck('id'))
                ->update([
                    'cierre_diario_id' => $cierre->id,
                    'estado'           => 'cerrada',
                ]);
        });

        $successMsg = "✅ Cierre de caja completado exitosamente.";
        if (!empty($diferencias)) {
            $successMsg .= " ⚠️ Se detectaron descuadres en " . count($diferencias) . " método(s) de pago.";
        }

        return redirect()->route('cierre.index')
            ->with('success', $successMsg)
            ->with('diferencias_cuadre', $diferencias);
    }
}