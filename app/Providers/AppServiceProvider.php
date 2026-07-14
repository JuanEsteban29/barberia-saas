<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use App\Models\Cita;
use App\Models\Barberia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https'); // Forzar HTTPS en todas las URLs generadas
        
        // Obtener la tasa BCV del día y compartirla en todas las vistas Blade
        try {
            $tasaBcv = \App\Services\BcvService::obtenerTasa();
        } catch (\Exception $e) {
            $tasaBcv = 45.50;
        }
        View::share('tasaBcv', $tasaBcv);

        View::composer('layouts.app', function ($view) {
            $barberia = Barberia::firstOrCreate(
                ['slug' => 'barberia-principal'],
                ['nombre' => 'Mi Barbería Profesional', 'porcentaje_barbero' => 60]
            );
            
            $fiadosCount = Cita::where('barberia_id', $barberia->id)
                ->where('estado', 'fiado')
                ->where('pago_completado', false)
                ->count();
                
            $view->with('fiadosCount', $fiadosCount);
        });
    }
}
