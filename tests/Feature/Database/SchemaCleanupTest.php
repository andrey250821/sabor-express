<?php

namespace Tests\Feature\Database;

use App\Models\Notificacion;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SchemaCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_schema_no_contiene_columnas_obsoletas(): void
    {
        $this->assertFalse(Schema::hasColumn('roles', 'descripcion'));
        $this->assertFalse(Schema::hasColumn('categorias', 'descripcion'));
        $this->assertFalse(Schema::hasColumn('comprobantes_pago', 'fecha_revision'));
        $this->assertFalse(Schema::hasColumn('asignaciones_delivery', 'fecha_asignacion'));
        $this->assertFalse(Schema::hasColumn('asignaciones_delivery', 'fecha_respuesta'));
        $this->assertFalse(Schema::hasColumn('asignaciones_delivery', 'fecha_entrega'));
        $this->assertFalse(Schema::hasColumn('notificaciones', 'fecha_expiracion'));
        $this->assertFalse(Schema::hasColumn('pedidos', 'fecha_listo'));
        $this->assertFalse(Schema::hasColumn('asignaciones_delivery', 'estado'));
    }

    public function test_schema_no_contiene_tabla_de_recuperacion_de_contrasena(): void
    {
        $this->assertFalse(Schema::hasTable('password_reset_tokens'));
    }

    public function test_notificaciones_vigentes_solo_consideran_los_ultimos_tres_dias(): void
    {
        $role = Role::firstOrCreate([
            'nombre' => 'Cliente',
        ]);

        $user = User::create([
            'role_id' => $role->id,
            'name' => 'Usuario de prueba',
            'email' => 'schema-cleanup@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $vieja = Notificacion::create([
            'user_id' => $user->id,
            'pedido_id' => null,
            'mensaje' => 'Notificación antigua',
            'tipo' => 'cliente',
            'evento' => 'prueba',
            'leido' => false,
        ]);

        $fechaVieja = Carbon::now()->subDays(4);
        $vieja->forceFill([
            'created_at' => $fechaVieja,
            'updated_at' => $fechaVieja,
        ])->save();

        $reciente = Notificacion::create([
            'user_id' => $user->id,
            'pedido_id' => null,
            'mensaje' => 'Notificación reciente',
            'tipo' => 'cliente',
            'evento' => 'prueba',
            'leido' => false,
        ]);

        $fechaReciente = Carbon::now()->subDay();
        $reciente->forceFill([
            'created_at' => $fechaReciente,
            'updated_at' => $fechaReciente,
        ])->save();

        $ids = Notificacion::vigentes()
            ->pluck('id')
            ->all();

        $this->assertNotContains($vieja->id, $ids);
        $this->assertContains($reciente->id, $ids);
    }

    public function test_comando_elimina_notificaciones_con_mas_de_tres_dias(): void
    {
        $role = Role::firstOrCreate([
            'nombre' => 'Cliente',
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $vieja = Notificacion::create([
            'user_id' => $user->id,
            'pedido_id' => null,
            'mensaje' => 'Notificación para borrar',
            'tipo' => 'cliente',
            'evento' => 'prueba',
            'leido' => false,
        ]);

        $fechaVieja = Carbon::now()->subDays(4);
        $vieja->forceFill([
            'created_at' => $fechaVieja,
            'updated_at' => $fechaVieja,
        ])->save();

        $reciente = Notificacion::create([
            'user_id' => $user->id,
            'pedido_id' => null,
            'mensaje' => 'Notificación para conservar',
            'tipo' => 'cliente',
            'evento' => 'prueba',
            'leido' => false,
        ]);

        $fechaReciente = Carbon::now()->subDay();
        $reciente->forceFill([
            'created_at' => $fechaReciente,
            'updated_at' => $fechaReciente,
        ])->save();

        $this->artisan('notificaciones:limpiar')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('notificaciones', [
            'id' => $vieja->id,
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'id' => $reciente->id,
        ]);
    }
}
