<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barberia;
use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Corte;
use App\Models\Comision;
use App\Models\Adelanto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Barbería
        $barberia = Barberia::create([
            'nombre' => 'Barbería El Imperio',
            'slug' => 'barberia-el-imperio',
            'direccion' => 'Yaritagua, Yaracuy',
            'porcentaje_barbero' => 60, 
        ]);

        // 2. Crear Administrador (Dueño/Jefe)
        $admin = User::create([
            'name' => 'Jefe de la Barbería',
            'email' => 'admin@barberia.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'barberia_id' => $barberia->id,
        ]);

        // 3. Crear Barbero
        $barbero = User::create([
            'name' => 'Carlos Gómez (Barbero)',
            'email' => 'carlos@barberia.com',
            'password' => Hash::make('12345678'),
            'role' => 'barbero',
            'barberia_id' => $barberia->id,
        ]);

        // 4. Crear Clientes
        $cliente1 = Cliente::create([
            'nombre' => 'Pedro Pérez',
            'telefono' => '04125556677',
            'email' => 'pedro@gmail.com',
        ]);

        $cliente2 = Cliente::create([
            'nombre' => 'Juan Rodríguez',
            'telefono' => '04149998877',
            'email' => 'juan@gmail.com',
        ]);

        // 5. Crear Servicios
        $servicioCorte = Servicio::create([
            'barberia_id' => $barberia->id,
            'nombre' => 'Corte Degradado',
            'descripcion' => 'Corte moderno con degradado',
            'precio' => 10.00,
            'duracion' => 30,
        ]);

        $servicioBarba = Servicio::create([
            'barberia_id' => $barberia->id,
            'nombre' => 'Servicio de Barba',
            'descripcion' => 'Afeitado y perfilado de barba',
            'precio' => 5.00,
            'duracion' => 20,
        ]);

        // 6. Simular Cortes Realizados
        // Corte 1: Pedro Pérez con Carlos Gómez (Completado y pagado en efectivo)
        $corte1 = Corte::create([
            'barberia_id' => $barberia->id,
            'cliente_id' => $cliente1->id,
            'barbero_id' => $barbero->id,
            'servicio_id' => $servicioCorte->id,
            'precio' => $servicioCorte->precio,
            'fecha_hora' => now()->subDays(2)->setHour(10)->setMinute(0),
            'estado' => 'completada',
            'metodo_pago' => 'efectivo',
            'pago_completado' => true,
        ]);

        // Comisión 1: 60% Barbero / 40% Negocio
        Comision::create([
            'corte_id' => $corte1->id,
            'barbero_id' => $barbero->id,
            'monto_barbero' => 6.00,
            'monto_negocio' => 4.00,
            'porcentaje_barbero' => 60.00,
        ]);

        // Corte 2: Juan Rodríguez con Carlos Gómez (Completado y pagado por transferencia)
        $corte2 = Corte::create([
            'barberia_id' => $barberia->id,
            'cliente_id' => $cliente2->id,
            'barbero_id' => $barbero->id,
            'servicio_id' => $servicioCorte->id,
            'precio' => $servicioCorte->precio,
            'fecha_hora' => now()->subDays(1)->setHour(14)->setMinute(30),
            'estado' => 'completada',
            'metodo_pago' => 'transferencia',
            'pago_completado' => true,
        ]);

        // Comisión 2: 60% Barbero / 40% Negocio
        Comision::create([
            'corte_id' => $corte2->id,
            'barbero_id' => $barbero->id,
            'monto_barbero' => 6.00,
            'monto_negocio' => 4.00,
            'porcentaje_barbero' => 60.00,
        ]);

        // Corte 3: Pedro Pérez con Carlos Gómez (Fiado pendiente)
        $corte3 = Corte::create([
            'barberia_id' => $barberia->id,
            'cliente_id' => $cliente1->id,
            'barbero_id' => $barbero->id,
            'servicio_id' => $servicioBarba->id,
            'precio' => $servicioBarba->precio,
            'fecha_hora' => now()->setHour(11)->setMinute(15),
            'estado' => 'fiado',
            'metodo_pago' => null,
            'pago_completado' => false,
        ]);

        // Comisión 3: Se registra, pero está asociada a un corte no pagado (fiado)
        Comision::create([
            'corte_id' => $corte3->id,
            'barbero_id' => $barbero->id,
            'monto_barbero' => 3.00,
            'monto_negocio' => 2.00,
            'porcentaje_barbero' => 60.00,
        ]);

        // Corte 4: Juan Rodríguez con Carlos Gómez (Corte realizado hoy en efectivo)
        $corte4 = Corte::create([
            'barberia_id' => $barberia->id,
            'cliente_id' => $cliente2->id,
            'barbero_id' => $barbero->id,
            'servicio_id' => $servicioCorte->id,
            'precio' => $servicioCorte->precio,
            'fecha_hora' => now()->setHour(16)->setMinute(0),
            'estado' => 'completada',
            'metodo_pago' => 'efectivo',
            'pago_completado' => true,
        ]);

        // Comisión 4
        Comision::create([
            'corte_id' => $corte4->id,
            'barbero_id' => $barbero->id,
            'monto_barbero' => 6.00,
            'monto_negocio' => 4.00,
            'porcentaje_barbero' => 60.00,
        ]);

        // 7. Adelantos solicitados
        Adelanto::create([
            'barbero_id' => $barbero->id,
            'monto' => 3.00,
            'motivo' => 'Adelanto del jueves',
            'descontado' => false
        ]);
    }
}