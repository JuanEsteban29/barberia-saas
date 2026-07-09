<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'telefono', 'email'];

    /**
     * Relación con los cortes realizados al cliente.
     */
    public function cortes()
    {
        return $this->hasMany(Corte::class, 'cliente_id');
    }

    /**
     * Obtiene el barbero habitual del cliente (quien lo atiende habitualmente)
     * y el número de veces que ha sido atendido por él.
     */
    public function barberoHabitual()
    {
        $corteMasFrecuente = $this->cortes()
            ->select('barbero_id', \DB::raw('count(*) as total'))
            ->groupBy('barbero_id')
            ->orderByDesc('total')
            ->first();

        if ($corteMasFrecuente) {
            $barbero = User::find($corteMasFrecuente->barbero_id);
            return [
                'barbero' => $barbero,
                'visitas' => $corteMasFrecuente->total
            ];
        }

        return null;
    }
}
