<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gasto;
use App\Models\Corte;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Métricas rápidas de hoy
        $hoy = Carbon::today();
        
        // Total producido hoy en cortes (ejemplo sumando los precios de las citas de hoy)
        $ingresosHoy = Corte::whereDate('fecha_hora', $hoy)->where('pago_completado', true)->sum('precio') ?? 0;
        
        // Total de cortes hechos hoy
        $cortesHoy = Corte::whereDate('fecha_hora', $hoy)->count();
        
        // Total barberos activos en el sistema
        $barberosActivos = User::where('role', 'barbero')->count();

        // 2. Datos para la Gráfica de los últimos 7 días
        $dias = [];
        $produccionDias = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            // Guardamos el nombre del día en español (Ej: Lun, Mar)
            $dias[] = $fecha->isoFormat('ddd'); 
            
            // Sumamos lo producido en ese día específico
            $produccionDias[] = Corte::whereDate('fecha_hora', $fecha->toDateString())->where('pago_completado', true)->sum('precio') ?? 0;
        }

        // 3. Traer los últimos 5 cortes recientes para mostrar actividad en tiempo real
        $cortesRecientes = Corte::with(['barbero', 'servicio', 'cliente'])->latest()->take(5)->get();

        return view('dashboard', compact(
            'ingresosHoy', 
            'cortesHoy', 
            'barberosActivos', 
            'dias', 
            'produccionDias',
            'cortesRecientes'
        ));
    }
}