<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\VentaProducto;
use App\Models\User;
use App\Models\Barberia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    private function obtenerBarberiaActiva()
    {
        return Barberia::firstOrCreate(
            ['slug' => 'barberia-principal'],
            ['nombre' => 'Mi Barbería Profesional', 'porcentaje_barbero' => 60]
        );
    }

    public function index()
    {
        $barberia = $this->obtenerBarberiaActiva();
        $productos = Producto::where('barberia_id', $barberia->id)->get();
        
        $barberos = User::where('barberia_id', $barberia->id)
            ->whereIn('role', ['admin', 'barbero'])
            ->get();

        $ventas = VentaProducto::with(['producto', 'barbero'])
            ->where('barberia_id', $barberia->id)
            ->latest()
            ->get();

        $totalProductos = $productos->count();
        $valorInventario = $productos->sum(function($p) {
            return $p->stock * $p->precio_venta;
        });
        
        $totalVendido = $ventas->sum(function($v) {
            return $v->cantidad * $v->precio_unitario;
        });

        return view('inventario.index', compact('productos', 'barberos', 'ventas', 'totalProductos', 'valorInventario', 'totalVendido'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
        ]);

        $barberia = $this->obtenerBarberiaActiva();

        Producto::create([
            'barberia_id'   => $barberia->id,
            'nombre'        => $request->nombre,
            'descripcion'   => $request->descripcion,
            'precio_compra' => $request->precio_compra,
            'precio_venta'  => $request->precio_venta,
            'stock'         => $request->stock,
        ]);

        return redirect()->route('inventario.index')->with('success', 'Producto agregado al inventario.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
        ]);

        $producto = Producto::findOrFail($id);
        $producto->update([
            'nombre'        => $request->nombre,
            'descripcion'   => $request->descripcion,
            'precio_compra' => $request->precio_compra,
            'precio_venta'  => $request->precio_venta,
            'stock'         => $request->stock,
        ]);

        return redirect()->route('inventario.index')->with('success', 'Producto actualizado.');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect()->route('inventario.index')->with('success', 'Producto eliminado.');
    }

    public function vender(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|integer|min:1',
            'barbero_id'  => 'nullable|exists:users,id',
            'metodo_pago' => 'required|in:efectivo_usd,efectivo_bs,transferencia',
        ]);

        $producto = Producto::findOrFail($request->producto_id);

        if ($producto->stock < $request->cantidad) {
            return redirect()->back()->with('error', "No hay suficiente stock disponible de '{$producto->nombre}'. Stock actual: {$producto->stock}.");
        }

        $barberia = $this->obtenerBarberiaActiva();

        DB::transaction(function() use ($request, $producto, $barberia) {
            // Descontar stock
            $producto->decrement('stock', $request->cantidad);

            // Calcular comisión del barbero si recomendó la venta (10% por defecto)
            $comisionBarbero = 0;
            if ($request->barbero_id) {
                $subtotal = $request->cantidad * $producto->precio_venta;
                $comisionBarbero = $subtotal * 0.10; // 10% de comisión
            }

            VentaProducto::create([
                'barberia_id'      => $barberia->id,
                'producto_id'      => $producto->id,
                'barbero_id'       => $request->barbero_id,
                'cantidad'         => $request->cantidad,
                'precio_unitario'  => $producto->precio_venta,
                'comision_barbero' => $comisionBarbero,
                'metodo_pago'      => $request->metodo_pago,
            ]);
        });

        return redirect()->route('inventario.index')->with('success', 'Venta registrada con éxito.');
    }
}
