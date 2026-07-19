<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaProducto extends Model
{
    protected $table = 'ventas_productos';

    protected $fillable = ['barberia_id', 'producto_id', 'barbero_id', 'cierre_diario_id', 'cantidad', 'precio_unitario', 'comision_barbero', 'metodo_pago'];

    public function barberia()
    {
        return $this->belongsTo(Barberia::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function barbero()
    {
        return $this->belongsTo(User::class, 'barbero_id');
    }
}
