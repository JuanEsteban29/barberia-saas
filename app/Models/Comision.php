<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comision extends Model
{
    protected $table = 'comisiones';

    protected $fillable = [
        'corte_id',
        'barbero_id',
        'monto_barbero',
        'monto_negocio',
        'porcentaje_barbero',
        'pagado_al_barbero',
    ];

    protected $casts = [
        'monto_barbero' => 'decimal:2',
        'monto_negocio' => 'decimal:2',
        'porcentaje_barbero' => 'decimal:2',
        'pagado_al_barbero' => 'boolean',
    ];

    public function corte()
    {
        return $this->belongsTo(Corte::class, 'corte_id');
    }

    public function barbero()
    {
        return $this->belongsTo(User::class, 'barbero_id');
    }
}
