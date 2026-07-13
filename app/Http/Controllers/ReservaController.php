<?php

namespace App\Http\Controllers;

use App\Models\Corte;
use App\Models\User;
use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Barberia;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Muestra el formulario de reserva público para una barbería específica.
     */
    public function createPublic($slug)
    {
        // Buscar la barbería activa por su slug (o crearla si es la principal y no existe)
        if ($slug === 'barberia-principal') {
            $barberia = Barberia::firstOrCreate(
                ['slug' => 'barberia-principal'],
                ['nombre' => 'Mi Barbería Profesional', 'porcentaje_barbero' => 60]
            );
        } else {
            $barberia = Barberia::where('slug', $slug)->firstOrFail();
        }

        // Obtener los barberos asociados a esta barbería
        $barberos = User::where('barberia_id', $barberia->id)
            ->whereIn('role', ['admin', 'barbero'])
            ->get();

        // Si la tabla de servicios está vacía, creamos los servicios por defecto
        if (Servicio::count() === 0) {
            Servicio::create([
                'barberia_id' => $barberia->id,
                'nombre' => 'Corte Degradado',
                'descripcion' => 'Corte moderno con degradado',
                'precio' => 10.00,
                'duracion' => 30,
            ]);

            Servicio::create([
                'barberia_id' => $barberia->id,
                'nombre' => 'Servicio de Barba',
                'descripcion' => 'Afeitado y perfilado de barba',
                'precio' => 5.00,
                'duracion' => 20,
            ]);

            Servicio::create([
                'barberia_id' => $barberia->id,
                'nombre' => 'Corte y Barba Completo',
                'descripcion' => 'Corte de cabello y perfilado de barba completo',
                'precio' => 12.00,
                'duracion' => 45,
            ]);
        }

        // Obtener los servicios asociados a esta barbería
        $servicios = Servicio::where('barberia_id', $barberia->id)->get();
        
        // Si no hay servicios para esta barbería, usar todos como fallback
        if ($servicios->isEmpty()) {
            $servicios = Servicio::all();
        }

        return view('reservas.create', compact('barberia', 'barberos', 'servicios'));
    }

    /**
     * Procesa y guarda la reserva realizada por el cliente de forma pública.
     */
    public function storePublic(Request $request, $slug)
    {
        // Buscar la barbería activa por su slug (o crearla si es la principal y no existe)
        if ($slug === 'barberia-principal') {
            $barberia = Barberia::firstOrCreate(
                ['slug' => 'barberia-principal'],
                ['nombre' => 'Mi Barbería Profesional', 'porcentaje_barbero' => 60]
            );
        } else {
            $barberia = Barberia::where('slug', $slug)->firstOrFail();
        }

        $request->validate([
            'cliente_nombre'   => 'required|string|max:255',
            'cliente_telefono' => 'nullable|string|max:50',
            'cliente_email'    => 'nullable|email|max:255',
            'barbero_id'       => 'required|exists:users,id',
            'servicio_id'      => 'required|exists:servicios,id',
            'fecha_hora'       => 'required|date|after:now',
        ]);

        $servicio = Servicio::findOrFail($request->servicio_id);

        // Buscar o registrar al cliente en la base de datos
        $nombreCliente = trim($request->cliente_nombre);
        
        // Intentar buscar cliente por nombre, si no existe lo crea con los datos de contacto
        $cliente = Cliente::firstOrCreate(
            ['nombre' => $nombreCliente],
            [
                'telefono' => $request->cliente_telefono,
                'email'    => $request->cliente_email
            ]
        );

        // Crear la cita/corte como reserva pendiente
        Corte::create([
            'barberia_id'     => $barberia->id,
            'cliente_id'      => $cliente->id,
            'barbero_id'      => $request->barbero_id,
            'servicio_id'     => $request->servicio_id,
            'precio'          => $servicio->precio,
            'fecha_hora'      => $request->fecha_hora,
            'estado'          => 'pendiente',
            'pago_completado' => false,
        ]);

        return redirect()->back()->with('success', '¡Tu reserva ha sido agendada con éxito!');
    }

    /**
     * Marca una reserva como completada y calcula la comisión correspondiente.
     */
    public function completar(Request $request, $id)
    {
        $request->validate([
            'metodo_pago' => 'required|in:efectivo,transferencia,fiado',
        ]);

        $corte = Corte::findOrFail($id);

        // Aseguramos que la reserva pertenece a la barbería del usuario autenticado
        if ($corte->barberia_id !== auth()->user()->barberia_id) {
            abort(403);
        }

        // Si ya está completada, no hacer nada
        if ($corte->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Esta cita ya no está pendiente.');
        }

        $isFiado = ($request->metodo_pago === 'fiado');

        \DB::transaction(function () use ($corte, $request, $isFiado) {
            // 1. Actualizar el estado de la reserva (Corte)
            $corte->update([
                'metodo_pago'     => $isFiado ? null : $request->metodo_pago,
                'estado'          => $isFiado ? 'fiado' : 'completada',
                'pago_completado' => !$isFiado,
            ]);

            // 2. Registrar comisiones: 60% barbero, 40% negocio
            $comisionExistente = \App\Models\Comision::where('corte_id', $corte->id)->first();
            if (!$comisionExistente) {
                $porcentajeBarbero = $corte->barberia->porcentaje_barbero ?? 60.00;
                $montoBarbero = $corte->precio * ($porcentajeBarbero / 100);
                $montoNegocio = $corte->precio * ((100 - $porcentajeBarbero) / 100);

                \App\Models\Comision::create([
                    'corte_id'           => $corte->id,
                    'barbero_id'         => $corte->barbero_id,
                    'monto_barbero'      => $montoBarbero,
                    'monto_negocio'      => $montoNegocio,
                    'porcentaje_barbero' => $porcentajeBarbero,
                ]);
            }
        });

        return redirect()->back()->with('success', '¡Cita completada y cobrada con éxito!');
    }
}
