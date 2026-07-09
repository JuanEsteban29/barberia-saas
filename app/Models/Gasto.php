<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $fillable = ['barberia_id', 'descripcion', 'monto', 'categoria', 'fecha_gasto'];

    protected $casts = [
        'fecha_gasto' => 'datetime',
    ];
}