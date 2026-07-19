<?php

namespace App\Http\Controllers;

use App\Models\Barberia;
use App\Models\User;
use App\Models\Servicio;
use App\Models\Corte;
use App\Models\Cliente;
use App\Models\Comision;
use App\Models\Adelanto;
use App\Models\Gasto;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReporteController extends Controller
{
    private function obtenerBarberiaActiva()
    {
        $barberia = Barberia::firstOrCreate(
            ['slug' => 'barberia-principal'],
            ['nombre' => 'Mi Barbería Profesional', 'porcentaje_barbero' => 60]
        );

        if (\App\Models\Servicio::count() === 0) {
            \App\Models\Servicio::create([
                'barberia_id' => $barberia->id,
                'nombre' => 'Corte Degradado',
                'descripcion' => 'Corte moderno con degradado',
                'precio' => 10.00,
                'duracion' => 30,
            ]);

            \App\Models\Servicio::create([
                'barberia_id' => $barberia->id,
                'nombre' => 'Servicio de Barba',
                'descripcion' => 'Afeitado y perfilado de barba',
                'precio' => 5.00,
                'duracion' => 20,
            ]);

            \App\Models\Servicio::create([
                'barberia_id' => $barberia->id,
                'nombre' => 'Corte y Barba Completo',
                'descripcion' => 'Corte de cabello y perfilado de barba completo',
                'precio' => 12.00,
                'duracion' => 45,
            ]);
        }

        return $barberia;
    }

    /**
     * Dashboard del Administrador con filtros e inteligencia de negocio.
     */
    public function dashboardAdministrativo(Request $request)
    {
        $barberia = $this->obtenerBarberiaActiva();
        
        // Obtener rango de tiempo seleccionado
        $rango = $request->input('rango', 'semana'); // hoy, semana, mes
        $hoy = Carbon::today();
        
        switch ($rango) {
            case 'hoy':
                $fechaInicio = Carbon::today()->startOfDay();
                $fechaFin = Carbon::today()->endOfDay();
                $labelRango = 'Hoy';
                break;
            case 'mes':
                $fechaInicio = Carbon::now()->startOfMonth();
                $fechaFin = Carbon::now()->endOfMonth();
                $labelRango = 'Este Mes';
                break;
            case 'semana':
            default:
                $fechaInicio = Carbon::now()->startOfWeek();
                $fechaFin = Carbon::now()->endOfWeek();
                $labelRango = 'Esta Semana';
                break;
        }

        // 1. Métricas básicas del periodo seleccionado
        $cortesPeriodo = Corte::where('barberia_id', $barberia->id)
            ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->get();

        $ingresosPeriodo = $cortesPeriodo->where('pago_completado', true)->sum('precio');
        $totalCortes = $cortesPeriodo->count();

        // 2. Personal Activo
        $barberosActivos = User::where('barberia_id', $barberia->id)
            ->whereIn('role', ['admin', 'barbero'])
            ->count();

        // 3. Resumen Financiero (Corte de Caja del Periodo)
        $comisionesPeriodo = Comision::whereHas('corte', function ($query) use ($fechaInicio, $fechaFin, $barberia) {
            $query->where('barberia_id', $barberia->id)
                ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
                ->where('pago_completado', true);
        })->get();

        $totalComisionesPagadas = $comisionesPeriodo->sum('monto_barbero');
        $totalGananciaNegocio = $comisionesPeriodo->sum('monto_negocio');

        // 4. Comparativa de Desempeño por Barbero (Quién corta más y gana más)
        $barberosDesempeno = User::where('barberia_id', $barberia->id)
            ->whereIn('role', ['admin', 'barbero'])
            ->withCount(['cortesAtendidos' => function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
            }])
            ->get()
            ->map(function ($barbero) use ($fechaInicio, $fechaFin) {
                $comisiones = Comision::where('barbero_id', $barbero->id)
                    ->whereHas('corte', function ($query) use ($fechaInicio, $fechaFin) {
                        $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
                            ->where('pago_completado', true);
                    })->sum('monto_barbero');

                $barbero->total_comisiones = $comisiones;
                return $barbero;
            })->sortByDesc('cortes_atendidos_count');

        // 5. Producción Diaria (Últimos 7 días para la gráfica)
        $dias = [];
        $produccionDias = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $dias[] = $fecha->isoFormat('ddd');
            
            $produccionDias[] = Corte::where('barberia_id', $barberia->id)
                ->whereDate('fecha_hora', $fecha->toDateString())
                ->where('pago_completado', true)
                ->sum('precio');
        }

        // 5.1. Métodos de Pago del Periodo (Cortes + Ventas de Productos)
        $pagoUsd = Corte::where('barberia_id', $barberia->id)
            ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->where('metodo_pago', 'efectivo_usd')
            ->where('pago_completado', true)
            ->sum('precio');
            
        $pagoBs = Corte::where('barberia_id', $barberia->id)
            ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->where('metodo_pago', 'efectivo_bs')
            ->where('pago_completado', true)
            ->sum('precio');
            
        $pagoTrans = Corte::where('barberia_id', $barberia->id)
            ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->where('metodo_pago', 'transferencia')
            ->where('pago_completado', true)
            ->sum('precio');

        $pagoUsd += \App\Models\VentaProducto::where('barberia_id', $barberia->id)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('metodo_pago', 'efectivo_usd')
            ->sum(\Illuminate\Support\Facades\DB::raw('cantidad * precio_unitario'));
            
        $pagoBs += \App\Models\VentaProducto::where('barberia_id', $barberia->id)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('metodo_pago', 'efectivo_bs')
            ->sum(\Illuminate\Support\Facades\DB::raw('cantidad * precio_unitario'));
            
        $pagoTrans += \App\Models\VentaProducto::where('barberia_id', $barberia->id)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('metodo_pago', 'transferencia')
            ->sum(\Illuminate\Support\Facades\DB::raw('cantidad * precio_unitario'));

        // 6. Actividad Reciente (Últimos 5 cortes)
        $cortesRecientes = Corte::with(['barbero', 'servicio', 'cliente'])
            ->where('barberia_id', $barberia->id)
            ->latest()
            ->take(5)
            ->get();
            
        // 7. Cuentas por Cobrar (Fiados acumulados)
        $fiadosPendientesCount = Corte::where('barberia_id', $barberia->id)
            ->where('estado', 'fiado')
            ->where('pago_completado', false)
            ->count();
            
        $fiadosPendientesMonto = Corte::where('barberia_id', $barberia->id)
            ->where('estado', 'fiado')
            ->where('pago_completado', false)
            ->sum('precio');

        return view('dashboard', compact(
            'rango',
            'labelRango',
            'ingresosPeriodo', 
            'totalCortes', 
            'barberosActivos', 
            'totalComisionesPagadas',
            'totalGananciaNegocio',
            'barberosDesempeno',
            'dias', 
            'produccionDias', 
            'pagoUsd',
            'pagoBs',
            'pagoTrans',
            'cortesRecientes',
            'fiadosPendientesCount',
            'fiadosPendientesMonto'
        ));
    }

    /**
     * Vista de registro e historial de cortes.
     */
    public function vistaCortes()
{
    $barberia = $this->obtenerBarberiaActiva();
    
    $historialCortes = Corte::with(['servicio', 'barbero', 'cliente'])
        ->where('barberia_id', $barberia->id)
        ->orderBy('fecha_hora', 'desc')
        ->get();
            
    $barberosDisponibles = User::where('barberia_id', $barberia->id)
        ->whereIn('role', ['admin', 'barbero'])
        ->get();
            
    // Aseguramos que esta variable siempre exista
    $serviciosDisponibles = \App\Models\Servicio::all();

    // Definimos las variables de dinero explícitamente con efectivo dividido
    $dineroEfectivoUsd = Corte::where('barberia_id', $barberia->id)
        ->where('metodo_pago', 'efectivo_usd')
        ->where('pago_completado', true)
        ->sum('precio');

    $dineroEfectivoBs = Corte::where('barberia_id', $barberia->id)
        ->where('metodo_pago', 'efectivo_bs')
        ->where('pago_completado', true)
        ->sum('precio');

    $dineroEfectivoLegacy = Corte::where('barberia_id', $barberia->id)
        ->where('metodo_pago', 'efectivo')
        ->where('pago_completado', true)
        ->sum('precio');

    $dineroEfectivo = $dineroEfectivoUsd + $dineroEfectivoBs + $dineroEfectivoLegacy;
        
    $dineroTransferencia = Corte::where('barberia_id', $barberia->id)
        ->where('metodo_pago', 'transferencia')
        ->where('pago_completado', true)
        ->sum('precio');
        
    $totalRecaudado = $dineroEfectivo + $dineroTransferencia;

    return view('cortes.index', compact(
        'historialCortes', 
        'barberosDisponibles', 
        'serviciosDisponibles', 
        'dineroEfectivo', 
        'dineroEfectivoUsd', 
        'dineroEfectivoBs', 
        'dineroEfectivoLegacy', 
        'dineroTransferencia', 
        'totalRecaudado'
    ));
}

    /**
     * Vista de barberos y su nómina actual.
     */
    public function vistaBarberos()
    {
        $barberia = $this->obtenerBarberiaActiva();
        $barberos = User::where('barberia_id', $barberia->id)
            ->whereIn('role', ['admin', 'barbero'])
            ->get();
        
        $fechaInicioSemana = Carbon::now()->startOfWeek();
        $fechaFinSemana = Carbon::now()->endOfWeek();
        
        $nominaSabado = [];
        foreach ($barberos as $barbero) {
            $totalProducido = Corte::where('barbero_id', $barbero->id)
                ->whereBetween('fecha_hora', [$fechaInicioSemana, $fechaFinSemana])
                ->where('pago_completado', true)
                ->sum('precio');

            // Las comisiones se traen de la tabla comisiones para cortes pagados esta semana
            $suComisionCortes = Comision::where('barbero_id', $barbero->id)
                ->whereBetween('created_at', [$fechaInicioSemana, $fechaFinSemana])
                ->whereHas('corte', fn($q) => $q->where('pago_completado', true))
                ->sum('monto_barbero');

            // Comisiones de productos recomendados esta semana
            $suComisionProductos = \App\Models\VentaProducto::where('barbero_id', $barbero->id)
                ->whereBetween('created_at', [$fechaInicioSemana, $fechaFinSemana])
                ->sum('comision_barbero');

            $suComision = $suComisionCortes + $suComisionProductos;

            $descuentoAdelantos = Adelanto::where('barbero_id', $barbero->id)
                ->where('descontado', false)
                ->sum('monto');

            $pagoNeto = $suComision - $descuentoAdelantos;
            
            $nominaSabado[] = [
                'id' => $barbero->id, 
                'name' => $barbero->name,
                'role' => $barbero->role,
                'porcentaje_comision' => $barbero->porcentaje_comision,
                'cortes_totales' => Corte::where('barbero_id', $barbero->id)
                    ->whereBetween('fecha_hora', [$fechaInicioSemana, $fechaFinSemana])
                    ->count(),
                'total_producido' => number_format($totalProducido, 2),
                'su_comision' => number_format($suComision, 2),
                'descuento_adelantos' => number_format($descuentoAdelantos, 2),
                'pago_neto_este_sabado' => number_format(max($pagoNeto, 0), 2)
            ];
        }
        return view('barberos.index', compact('nominaSabado', 'barberia'));
    }

    /**
     * Vista de gastos y finanzas globales.
     */
    public function vistaFinanzas()
    {
        $barberia = $this->obtenerBarberiaActiva();
        $cortes = Corte::where('barberia_id', $barberia->id)
            ->where('pago_completado', true)
            ->get();
            
        $gastosSemanales = Gasto::where('barberia_id', $barberia->id)->latest()->get();
        
        $totalBruto = $cortes->sum('precio');
        $totalGastos = $gastosSemanales->sum('monto');
        
        $dineroEfectivo = $cortes->whereIn('metodo_pago', ['efectivo', 'efectivo_usd', 'efectivo_bs'])->sum('precio');
        $dineroTransferencia = $cortes->where('metodo_pago', 'transferencia')->sum('precio');

        // Sumatoria de todas las comisiones calculadas de cortes pagados
        $totalComisiones = Comision::whereHas('corte', function($q) use ($barberia) {
            $q->where('barberia_id', $barberia->id)->where('pago_completado', true);
        })->sum('monto_barbero');

        $gananciaNeta = $totalBruto - $totalGastos - $totalComisiones;

        $barberos = User::where('barberia_id', $barberia->id)
            ->whereIn('role', ['admin', 'barbero'])
            ->get();

        return view('finanzas.index', compact(
            'totalBruto', 
            'totalGastos', 
            'gananciaNeta', 
            'dineroEfectivo', 
            'dineroTransferencia', 
            'totalComisiones', 
            'gastosSemanales',
            'barberos'
        ));
    }

    /**
     * Vista de fiados.
     */
    public function vistaFiados()
    {
        $barberia = $this->obtenerBarberiaActiva();
        $fiadosPendientes = Corte::with(['barbero', 'servicio', 'cliente'])
            ->where('barberia_id', $barberia->id)
            ->where('estado', 'fiado')
            ->where('pago_completado', false)
            ->get();

        return view('cortes.fiados', compact('fiadosPendientes'));
    }

    /**
     * Pagar un fiado.
     */
    public function pagarFiado(Request $request, $id)
    {
        $corte = Corte::findOrFail($id);
        
        $corte->update([
            'pago_completado' => true,
            'estado' => 'completada',
            'metodo_pago' => $request->metodo_pago_real,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Fiado liquidado y registrado en caja.');
    }

    public function storeGasto(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'categoria'   => 'required|in:Insumos,Servicios,Local,Personal,Otro',
            'monto'       => 'required|numeric|min:0.01',
            'barbero_id'  => 'nullable|exists:users,id',
        ]);

        $barberia = $this->obtenerBarberiaActiva();

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $barberia) {
            // 1. Crear el Gasto General
            Gasto::create([
                'barberia_id' => $barberia->id,
                'descripcion' => $request->descripcion,
                'categoria'   => $request->categoria,
                'monto'       => $request->monto,
                'fecha_gasto' => now(),
            ]);

            // 2. Si la categoría es Personal y se seleccionó un barbero, crear el Adelanto
            if ($request->categoria === 'Personal' && $request->barbero_id) {
                Adelanto::create([
                    'barbero_id' => $request->barbero_id,
                    'monto'      => $request->monto,
                    'motivo'     => $request->descripcion,
                    'descontado' => false,
                ]);
            }
        });

        return redirect()->route('finanzas.index')->with('success', 'Gasto/Adelanto registrado correctamente.');
    }

    /**
     * Crear un nuevo barbero.
     */
    public function guardarBarbero(Request $request)
    {
        $request->validate([
            'nombre'              => 'required|string|max:255',
            'rol'                 => 'required|in:barbero,admin',
            'porcentaje_comision' => 'nullable|integer|between:0,100',
        ]);

        $barberia = $this->obtenerBarberiaActiva();

        $emailBase = \Illuminate\Support\Str::slug($request->nombre, '.');
        $email     = $emailBase . '.' . time() . '@barber.local';

        User::create([
            'name'                => $request->nombre,
            'email'               => $email,
            'password'            => bcrypt('barbero123'),
            'role'                => $request->rol,
            'barberia_id'         => $barberia->id,
            'porcentaje_comision' => $request->porcentaje_comision,
        ]);

        return redirect()->route('barberos.index')->with('success', "Barbero '{$request->nombre}' registrado exitosamente.");
    }

    public function deleteTrabajador($id) 
    { 
        User::findOrFail($id)->delete(); 
        return redirect()->back(); 
    }

    public function cerrarSemana(Request $request) 
    { 
        Adelanto::where('barbero_id', $request->barbero_id)->update(['descontado' => true]); 
        return redirect()->back(); 
    }

    /**
     * Dashboard del Barbero (Panel Restringido)
     */
    public function dashboardBarbero()
    {
        $user = Auth::user();
        $barberia = $this->obtenerBarberiaActiva();

        // 1. Historial personal de cortes realizados (excluyendo citas pendientes/reservas)
        $cortes = Corte::with(['servicio', 'cliente'])
            ->where('barbero_id', $user->id)
            ->where('estado', '!=', 'pendiente')
            ->orderBy('fecha_hora', 'desc')
            ->get();

        // 2. Próximas citas / Reservas pendientes asignadas a este barbero (Hoy vs Futuras)
        $citasHoy = Corte::with(['servicio', 'cliente'])
            ->where('barbero_id', $user->id)
            ->where('estado', 'pendiente')
            ->whereDate('fecha_hora', Carbon::today())
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $citasFuturas = Corte::with(['servicio', 'cliente'])
            ->where('barbero_id', $user->id)
            ->where('estado', 'pendiente')
            ->whereDate('fecha_hora', '>', Carbon::today())
            ->orderBy('fecha_hora', 'asc')
            ->get();

        // 3. Cálculo de comisiones en tiempo real (Semana en curso - Lunes a Domingo)
        $fechaInicioSemana = Carbon::now()->startOfWeek();
        $fechaFinSemana = Carbon::now()->endOfWeek();

        // Comisiones de Cortes de esta semana (solo cobrados)
        $comisionesCortesSemana = Comision::where('barbero_id', $user->id)
            ->whereBetween('created_at', [$fechaInicioSemana, $fechaFinSemana])
            ->whereHas('corte', fn($q) => $q->where('pago_completado', true))
            ->sum('monto_barbero');

        // Comisiones de Productos de esta semana
        $comisionesProductosSemana = \App\Models\VentaProducto::where('barbero_id', $user->id)
            ->whereBetween('created_at', [$fechaInicioSemana, $fechaFinSemana])
            ->sum('comision_barbero');

        // Adelantos de esta semana activos (no descontados aún)
        $adelantosSemana = \App\Models\Adelanto::where('barbero_id', $user->id)
            ->where('descontado', false)
            ->sum('monto');

        // Total de cortes completados esta semana
        $cortesSemana = Corte::where('barbero_id', $user->id)
            ->whereBetween('fecha_hora', [$fechaInicioSemana, $fechaFinSemana])
            ->where('pago_completado', true)
            ->count();

        // Pago Neto Estimado
        $pagoNetoSemana = ($comisionesCortesSemana + $comisionesProductosSemana) - $adelantosSemana;

        // Comisiones pendientes (Fiados por liquidar)
        $comisionesPendientes = Comision::where('barbero_id', $user->id)
            ->whereHas('corte', fn($q) => $q->where('pago_completado', false)->where('estado', 'fiado'))
            ->sum('monto_barbero');

        // 4. Clientes asociados
        $clientesAsociados = Cliente::whereHas('cortes', function ($q) use ($user) {
                $q->where('barbero_id', $user->id);
            })
            ->withCount(['cortes' => function ($q) use ($user) {
                $q->where('barbero_id', $user->id);
            }])
            ->get()
            ->map(function ($cliente) use ($user) {
                // Obtener fecha del último servicio
                $ultimoCorte = Corte::where('cliente_id', $cliente->id)
                    ->where('barbero_id', $user->id)
                    ->latest('fecha_hora')
                    ->first();

                $cliente->ultima_visita = $ultimoCorte ? $ultimoCorte->fecha_hora : null;
                return $cliente;
            })->sortByDesc('cortes_count');

        return view('barbero.dashboard', compact(
            'cortes',
            'citasHoy',
            'citasFuturas',
            'comisionesCortesSemana',
            'comisionesProductosSemana',
            'adelantosSemana',
            'cortesSemana',
            'pagoNetoSemana',
            'comisionesPendientes',
            'clientesAsociados',
            'barberia'
        ));
    }
    public function descargarReporte()
{
    $barberia = $this->obtenerBarberiaActiva();
    $historialCortes = \App\Models\Corte::with(['servicio', 'barbero', 'cliente'])
        ->where('barberia_id', $barberia->id)
        ->get();

    // Cargamos una vista específica para el PDF (la crearemos en el paso 3)
    $pdf = Pdf::loadView('reportes.pdf_corte', compact('historialCortes'));
    
    return $pdf->download('reporte_cortes_' . date('Y-m-d') . '.pdf');
}
}