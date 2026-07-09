<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corte extends Model
{
    protected $fillable = [
        'barberia_id',
        'cliente_id',
        'barbero_id',
        'servicio_id',
        'precio',
        'fecha_hora',
        'estado',
        'metodo_pago',
        'pago_completado',
        'cierre_diario_id',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'precio' => 'decimal:2',
        'pago_completado' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function barbero()
    {
        return $this->belongsTo(User::class, 'barbero_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function comision()
    {
        return $this->hasOne(Comision::class, 'corte_id');
    }

    public function cierreDiario()
    {
        return $this->belongsTo(CierreDiario::class, 'cierre_diario_id');
    }

    public function barberia()
    {
        return $this->belongsTo(Barberia::class, 'barberia_id');
    }

    // Scope: cortes sin meter en cierre
    public function scopeSinCerrar($query)
    {
        return $query->whereNull('cierre_diario_id');
    }

    // Scope: por barberia
    public function scopeDeBarberia($query, $barberiaId)
    {
        return $query->where('barberia_id', $barberiaId);
    }

    // Scope: cobrados (excluye fiados pendientes)
    public function scopeCobrables($query)
    {
        return $query->where('estado', '!=', 'fiado');
    }
}
