<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class LiveSearchTest extends TestCase
{
    private function crearAdministrador(): User
    {
        $role = Role::firstOrCreate(
            ['nombre' => 'Administrador'],
            ['descripcion' => 'Administrador del sistema']
        );

        return User::create([
            'role_id' => $role->id,
            'name' => 'Administrador de Prueba',
            'email' => 'admin-live-search-' . uniqid() . '@saborexpress.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);
    }

    public function test_administrador_puede_buscar_clientes_por_correo_en_tiempo_real(): void
    {
        $admin = $this->crearAdministrador();

        $cliente1 = User::create([
            'role_id' => Role::firstOrCreate(['nombre' => 'Cliente'], ['descripcion' => 'Cliente del sistema'])->id,
            'name' => 'Cliente Uno',
            'email' => 'juan.prueba@gmail.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        User::create([
            'role_id' => $cliente1->role_id,
            'name' => 'Cliente Dos',
            'email' => 'maria.prueba@gmail.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($admin)
            ->getJson('/admin/clientes?buscar=juan.prueba');

        $response
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertSee('juan.prueba@gmail.com')
            ->assertDontSee('maria.prueba@gmail.com');
    }

    public function test_administrador_puede_buscar_delivery_por_correo_en_tiempo_real(): void
    {
        $admin = $this->crearAdministrador();

        $role = Role::firstOrCreate(
            ['nombre' => 'Delivery'],
            ['descripcion' => 'Delivery del sistema']
        );

        User::create([
            'role_id' => $role->id,
            'name' => 'Delivery Uno',
            'email' => 'delivery.uno@gmail.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        User::create([
            'role_id' => $role->id,
            'name' => 'Delivery Dos',
            'email' => 'delivery.dos@gmail.com',
            'password' => 'password',
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($admin)
            ->getJson('/admin/deliverys?buscar=delivery.uno');

        $response
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertSee('delivery.uno@gmail.com')
            ->assertDontSee('delivery.dos@gmail.com');
    }
}
