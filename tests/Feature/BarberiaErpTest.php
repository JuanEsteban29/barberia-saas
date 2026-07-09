<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barberia;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Corte;
use App\Models\Comision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class BarberiaErpTest extends TestCase
{
    use RefreshDatabase;

    private $barberia;
    private $admin;
    private $barbero;
    private $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->barberia = Barberia::create([
            'nombre' => 'Test Barberia',
            'slug' => 'test-barberia',
            'porcentaje_barbero' => 60,
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'barberia_id' => $this->barberia->id,
        ]);

        $this->barbero = User::create([
            'name' => 'Barber User',
            'email' => 'barber@test.com',
            'password' => Hash::make('password123'),
            'role' => 'barbero',
            'barberia_id' => $this->barberia->id,
        ]);

        $this->servicio = Servicio::create([
            'barberia_id' => $this->barberia->id,
            'nombre' => 'Corte Test',
            'descripcion' => 'Corte de pelo de prueba',
            'precio' => 20.00,
            'duracion' => 30,
        ]);
    }

    /**
     * Test de redirección de invitados al login.
     */
    public function test_guests_are_redirected_to_login()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test de login de administrador y redirección al dashboard admin.
     */
    public function test_admin_can_login_and_access_dashboard()
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);

        $dashboardResponse = $this->actingAs($this->admin)->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Panel de Control General');
    }

    /**
     * Test de login de barbero y redirección al dashboard barbero.
     */
    public function test_barber_can_login_and_access_barber_dashboard()
    {
        $response = $this->post('/login', [
            'email' => 'barber@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/barbero/dashboard');
        $this->assertAuthenticatedAs($this->barbero);

        $dashboardResponse = $this->actingAs($this->barbero)->get('/barbero/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Panel de Personal');
    }

    /**
     * Test de restricción de accesos (Barbero no puede entrar a vistas de admin).
     */
    public function test_barber_cannot_access_admin_views()
    {
        $response = $this->actingAs($this->barbero)->get('/dashboard');
        
        // Debe ser redirigido a su propio panel con alerta
        $response->assertRedirect('/barbero/dashboard');
        $response->assertSessionHas('error', 'No tienes permiso para acceder a esta sección.');
    }

    /**
     * Test de guardado de corte y cálculo de comisiones (60/40).
     */
    public function test_registering_a_corte_calculates_commissions_automatically()
    {
        $this->actingAs($this->admin);

        $response = $this->post('/corte', [
            'cliente_nombre' => 'Nuevo Cliente de Prueba',
            'barbero_id' => $this->barbero->id,
            'servicio_id' => $this->servicio->id,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertRedirect();
        
        // Verificar que el cliente fue creado
        $cliente = Cliente::where('nombre', 'Nuevo Cliente de Prueba')->first();
        $this->assertNotNull($cliente);

        // Verificar que el corte fue registrado
        $corte = Corte::where('cliente_id', $cliente->id)
            ->where('barbero_id', $this->barbero->id)
            ->where('servicio_id', $this->servicio->id)
            ->first();
            
        $this->assertNotNull($corte);
        $this->assertEquals(20.00, $corte->precio);
        $this->assertEquals('completada', $corte->estado);
        $this->assertEquals('efectivo', $corte->metodo_pago);
        $this->assertTrue($corte->pago_completado);

        // Verificar que la comisión se calculó y guardó correctamente (60% / 40%)
        $comision = Comision::where('corte_id', $corte->id)->first();
        $this->assertNotNull($comision);
        $this->assertEquals(12.00, $comision->monto_barbero);  // 60% of $20.00
        $this->assertEquals(8.00, $comision->monto_negocio);   // 40% of $20.00
        $this->assertEquals(60.00, $comision->porcentaje_barbero);
    }
}
