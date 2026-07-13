<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $fillable = [
        'barberia_id', 'cliente_nombre', 'barbero_id', 'servicio_id',
        'fecha_hora', 'estado', 'metodo_pago', 'pago_completado',
        'cierre_diario_id',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function barbero()
    {
        return $this->belongsTo(User::class, 'barbero_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function barberia()
    {
        return $this->belongsTo(Barberia::class, 'barberia_id');
    }

    // Scope: citas que aún no han sido procesadas en un cierre de caja
    public function scopeSinCerrar($query)
    {
        return $query->whereNull('cierre_diario_id');
    }

    // Scope: filtrar por barbería
    public function scopeDeBarberia($query, $barberiaId)
    {
        return $query->where('barberia_id', $barberiaId);
    }

    // Scope: sólo citas realmente cobradas (excluye fiados)
    public function scopeCobrables($query)
    {
        return $query->where('estado', '!=', 'fiado');
    }

    // Relación: cierre de caja al que pertenece esta cita
    public function cierreDiario()
    {
        return $this->belongsTo(CierreDiario::class);
    }

    // Ganancia del barbero por esta cita individual
    public function getGananciaBarberoAttribute()
    {
        // CORREGIDO: evita error fatal si la cita quedó sin servicio o sin barbero asignado
        if (!$this->servicio || !$this->barbero) {
            return 0;
        }

        // Si el corte fue fiado, el barbero NO cobra comisión (Penalización de cuenta)
        if ($this->estado === 'fiado') {
            return 0;
        }

        $precio = $this->servicio->precio;

        // Si el rol es 'admin' (el jefe), cobra el 100% completo
        if ($this->barbero->role === 'admin') {
            return $precio;
        }

        // Si es empleado, se le calcula su porcentaje de comisión establecido
        $porcentaje = $this->barberia->porcentaje_barbero ?? 60;
        return $precio * ($porcentaje / 100);
    }
}