<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adelanto extends Model
{
    protected $fillable = ['barbero_id', 'monto', 'descontado', 'fecha_adelanto'];

    // Esta es la parte que falta y causa el error en image_10.png
    public function barbero()
    {
        return $this->belongsTo(User::class, 'barbero_id');
    }
}