<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_as_cliente(): void
    {
        Role::firstOrCreate(['nombre' => 'Cliente']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'telefono' => '71234567',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(
            route('cliente.dashboard.index', absolute: false)
        );

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'telefono' => '71234567',
            'email' => 'test@example.com',
            'role_id' => 2,
            'estado' => 'activo',
        ]);
    }

    public function test_new_user_must_provide_phone(): void
    {
        Role::firstOrCreate(['nombre' => 'Cliente']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('telefono');
        $this->assertGuest();
    }
    public function test_duplicate_email_validation_message_is_in_spanish(): void
    {
        app()->setLocale('es');

        Role::firstOrCreate(['nombre' => 'Cliente']);

        User::create([
            'role_id' => 2,
            'name' => 'Cliente Existente',
            'email' => 'existente@example.com',
            'telefono' => '71234567',
            'password' => Hash::make('Password123!'),
            'estado' => 'activo',
        ]);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Otro Cliente',
            'telefono' => '76543210',
            'email' => 'existente@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'email' => 'Este correo electrónico ya está registrado. Inicia sesión si ya tienes una cuenta.',
        ]);
    }


    public function test_registration_rejects_letters_in_phone_number(): void
    {
        Role::firstOrCreate(['nombre' => 'Cliente']);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'telefono' => '7123ABCD',
            'email' => 'phone-invalid@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('telefono');
        $this->assertDatabaseMissing('users', ['email' => 'phone-invalid@example.com']);
        $this->assertGuest();
    }

    public function test_registration_normalizes_a_valid_international_phone_number(): void
    {
        Role::firstOrCreate(['nombre' => 'Cliente']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'telefono' => '+591 7 123-4567',
            'email' => 'phone-valid@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('cliente.dashboard.index', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'phone-valid@example.com',
            'telefono' => '71234567',
            'role_id' => 2,
        ]);
    }

    public function test_profiles_of_client_delivery_and_cook_reject_letters_in_phone(): void
    {
        $casos = [
            ['Cliente', 'cliente.phone@example.com', 'cliente.configuracion.edit', 'cliente.configuracion.update'],
            ['Delivery', 'delivery.phone@example.com', 'delivery.perfil.edit', 'delivery.perfil.update'],
            ['Cocinero', 'cocinero.phone@example.com', 'cocinero.perfil.edit', 'cocinero.perfil.update'],
        ];

        foreach ($casos as [$rol, $email, $formRoute, $updateRoute]) {
            $role = Role::firstOrCreate(['nombre' => $rol]);
            $user = User::create([
                'role_id' => $role->id,
                'name' => 'Usuario de prueba',
                'email' => $email,
                'telefono' => '71234567',
                'password' => Hash::make('Password123!'),
                'estado' => 'activo',
            ]);

            $this->actingAs($user)
                ->from(route($formRoute))
                ->patch(route($updateRoute), [
                    'name' => 'Nombre actualizado',
                    'telefono' => '7123ABCD',
                ])
                ->assertRedirect(route($formRoute))
                ->assertSessionHasErrors('telefono');

            $this->assertSame('71234567', $user->fresh()->telefono);
        }
    }

    public function test_admin_cannot_create_delivery_or_cook_with_letters_in_phone(): void
    {
        $role = Role::firstOrCreate(['nombre' => 'Administrador']);
        $admin = User::create([
            'role_id' => $role->id,
            'name' => 'Administrador de prueba',
            'email' => 'admin.phone@example.com',
            'telefono' => '71234567',
            'password' => Hash::make('Password123!'),
            'estado' => 'activo',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.deliverys.create'))
            ->post(route('admin.deliverys.store'), [
                'name' => 'Delivery Prueba',
                'email' => 'delivery-invalid-phone@example.com',
                'telefono' => '7123LETRAS',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'estado' => 'activo',
            ])
            ->assertRedirect(route('admin.deliverys.create'))
            ->assertSessionHasErrors('telefono');

        $this->assertDatabaseMissing('users', ['email' => 'delivery-invalid-phone@example.com']);

        $this->actingAs($admin)
            ->from(route('admin.cocineros.create'))
            ->post(route('admin.cocineros.store'), [
                'name' => 'Cocinero Prueba',
                'email' => 'cook-invalid-phone@example.com',
                'telefono' => '7123LETRAS',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'estado' => 'activo',
            ])
            ->assertRedirect(route('admin.cocineros.create'))
            ->assertSessionHasErrors('telefono');

        $this->assertDatabaseMissing('users', ['email' => 'cook-invalid-phone@example.com']);
    }

    public function test_admin_cannot_save_letters_in_restaurant_phone(): void
    {
        $role = Role::firstOrCreate(['nombre' => 'Administrador']);
        $admin = User::create([
            'role_id' => $role->id,
            'name' => 'Administrador de configuración',
            'email' => 'admin.config.phone@example.com',
            'telefono' => '71234567',
            'password' => Hash::make('Password123!'),
            'estado' => 'activo',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.configuracion.index'))
            ->put(route('admin.configuracion.update'), [
                'nombre_restaurante' => 'Sabor Express',
                'telefono' => '7222ABCD',
            ])
            ->assertRedirect(route('admin.configuracion.index'))
            ->assertSessionHasErrors('telefono');

        $this->assertDatabaseMissing('configuraciones', ['telefono' => '7222ABCD']);
    }
}
