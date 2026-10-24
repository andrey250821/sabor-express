<?php

namespace Tests\Feature\Cocinero;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstadoTest extends TestCase
{
    use RefreshDatabase;

    private function crearCocinero(string $correo, string $estado = 'activo'): User
    {
        $role = Role::firstOrCreate([
            'nombre' => 'Cocinero',
        ]);

        return User::create([
            'role_id' => $role->id,
            'name' => 'Cocinero de prueba',
            'email' => $correo,
            'password' => 'password',
            'estado' => $estado,
        ]);
    }

    public function test_cocinero_no_dispone_de_una_ruta_para_cambiar_su_estado(): void
    {
        $cocinero = $this->crearCocinero('cocinero-sin-toggle@test.com');

        $this->actingAs($cocinero)
            ->patch('/cocinero/estado')
            ->assertNotFound();

        $this->assertSame('activo', $cocinero->fresh()->estado);
    }

    public function test_cocinero_inactivo_es_desconectado_al_intentar_abrir_el_panel(): void
    {
        $cocinero = $this->crearCocinero(
            'cocinero-inactivo-panel@test.com',
            'inactivo'
        );

        $this->actingAs($cocinero)
            ->get('/cocinero/pedidos')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
