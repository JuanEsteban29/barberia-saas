<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'barberia_id'
    ];

    // Relación con la Barbería a la que pertenece
    public function barberia()
    {
        return $this->belongsTo(Barberia::class, 'barberia_id');
    }

    // Citas que ha atendido como barbero
    public function citasAsistidas()
    {
        return $this->hasMany(Cita::class, 'barbero_id');
    }

    // Cortes que ha realizado como barbero
    public function cortesAtendidos()
    {
        return $this->hasMany(Corte::class, 'barbero_id');
    }

    // Comisiones obtenidas
    public function comisiones()
    {
        return $this->hasMany(Comision::class, 'barbero_id');
    }

    // Adelantos de dinero solicitados
    public function adelantos()
    {
        return $this->hasMany(Adelanto::class, 'barbero_id');
    }
}