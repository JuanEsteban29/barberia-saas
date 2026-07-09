<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barberia extends Model
{
    protected $fillable = ['nombre', 'slug', 'direccion', 'telefono', 'logo', 'porcentaje_barbero'];

    // Barberos y admins (personal que corta) de esta barbería
    public function barberos()
    {
        return $this->hasMany(User::class, 'barberia_id')->whereIn('role', ['admin', 'barbero']);
    }

    // CORREGIDO: la clase se llama "Servicio" (singular), no "Servicios".
    // Antes esto rompía con "Class Servicios not found" apenas se usaba.
    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'barberia_id');
    }
}