<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = ['barberia_id', 'nombre', 'descripcion', 'precio', 'duracion'];

    // NUEVO: evita problemas de redondeo/tipo con precios (float vs string)
    protected $casts = [
        'precio' => 'decimal:2',
    ];

    // Cada servicio pertenece a una barbería
    public function barberia()
    {
        return $this->belongsTo(Barberia::class);
    }

    // Un servicio puede estar en muchas citas
    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}