<?php

namespace Tests\Feature\Admin;

use App\Models\Configuracion;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfiguracionTest extends TestCase
{
    use RefreshDatabase;

    private function crearAdministrador(): User
    {
        $rol = Role::where('nombre', 'Administrador')->firstOrFail();

        return User::create([
            'role_id' => $rol->id,
            'name' => 'Administrador de prueba',
            'email' => 'admin-configuracion-test@example.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);
    }

    private function crearConfiguracion(): Configuracion
    {
        return Configuracion::create([
            'nombre_restaurante' => 'Sabor Express',
            'telefono' => '70000000',
            'tarifa_minima_delivery' => 5.00,
            'precio_km_delivery' => 3.00,
            'porcentaje_delivery' => 80.00,
            'porcentaje_restaurante_delivery' => 20.00,
        ]);
    }

    public function test_administrador_puede_actualizar_configuracion_economica_de_delivery(): void
    {
        $administrador = $this->crearAdministrador();
        $configuracion = $this->crearConfiguracion();

        $respuesta = $this->actingAs($administrador)
            ->from('/admin/configuracion')
            ->put(route('admin.configuracion.delivery.update'), [
                'tarifa_minima_delivery' => '7.50',
                'precio_km_delivery' => '2.75',
                'porcentaje_delivery' => '75.00',
                'porcentaje_restaurante_delivery' => '25.00',
            ]);

        $respuesta->assertRedirect('/admin/configuracion');
        $respuesta->assertSessionHas(
            'success',
            'Configuración económica de Delivery actualizada correctamente.'
        );

        $this->assertDatabaseHas('configuraciones', [
            'id' => $configuracion->id,
            'tarifa_minima_delivery' => '7.50',
            'precio_km_delivery' => '2.75',
            'porcentaje_delivery' => '75.00',
            'porcentaje_restaurante_delivery' => '25.00',
        ]);
    }

    public function test_sistema_rechaza_porcentajes_de_delivery_que_no_suman_cien(): void
    {
        $administrador = $this->crearAdministrador();
        $configuracion = $this->crearConfiguracion();

        $respuesta = $this->actingAs($administrador)
            ->from('/admin/configuracion')
            ->put(route('admin.configuracion.delivery.update'), [
                'tarifa_minima_delivery' => '7.50',
                'precio_km_delivery' => '2.75',
                'porcentaje_delivery' => '70.00',
                'porcentaje_restaurante_delivery' => '20.00',
            ]);

        $respuesta->assertRedirect('/admin/configuracion');
        $respuesta->assertSessionHas(
            'error',
            'Los porcentajes de Delivery y restaurante deben sumar exactamente 100%.'
        );

        $this->assertDatabaseHas('configuraciones', [
            'id' => $configuracion->id,
            'tarifa_minima_delivery' => '5.00',
            'precio_km_delivery' => '3.00',
            'porcentaje_delivery' => '80.00',
            'porcentaje_restaurante_delivery' => '20.00',
        ]);
    }
}
