<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BcvService
{
    /**
     * Obtiene la tasa del Dólar BCV desde la API oficial de DolarApi.
     * Guarda en caché el resultado por 6 horas para optimizar el rendimiento.
     */
    public static function obtenerTasa()
    {
        return Cache::remember('tasa_bcv_dia', 21600, function () {
            try {
                $response = Http::timeout(5)->get('https://ve.dolarapi.com/v1/dolares/oficial');
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['promedio'])) {
                        return (float) $data['promedio'];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error obteniendo tasa BCV desde API: ' . $e->getMessage());
            }

            // Tasa de contingencia si falla la conexión y no hay caché anterior
            return 45.50;
        });
    }
}
