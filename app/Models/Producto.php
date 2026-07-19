<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = ['barberia_id', 'nombre', 'descripcion', 'precio_compra', 'precio_venta', 'stock'];

    public function barberia()
    {
        return $this->belongsTo(Barberia::class);
    }

    public function ventas()
    {
        return $this->hasMany(VentaProducto::class);
    }
}
