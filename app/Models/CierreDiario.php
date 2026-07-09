<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CierreDiario extends Model
{
    protected $table = 'cierres_diarios';
    protected $fillable = [
        'barberia_id',
        'fecha',
        'total_citas',
        'total_efectivo',
        'total_transferencia',
        'total_ingresos',
        'total_fiado',
        'notas',
    ];

    protected $casts = [
        'fecha'               => 'date',
        'total_efectivo'      => 'decimal:2',
        'total_transferencia' => 'decimal:2',
        'total_ingresos'      => 'decimal:2',
        'total_fiado'         => 'decimal:2',
    ];

    /**
     * Citas procesadas en este cierre (trazabilidad contable).
     */
    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    /**
     * Cortes procesados en este cierre.
     */
    public function cortes()
    {
        return $this->hasMany(Corte::class, 'cierre_diario_id');
    }

    /**
     * Barbería a la que pertenece este cierre.
     */
    public function barberia()
    {
        return $this->belongsTo(Barberia::class);
    }
}
