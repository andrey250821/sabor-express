<?php

namespace Tests\Feature\Database;

use App\Models\Notificacion;
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
    }

    public function test_asignaciones_delivery_solo_aceptan_estados_actuales(): void
    {
        $estado = Schema::getConnection()
            ->getSchemaBuilder()
            ->getColumnType('asignaciones_delivery', 'estado');

        $this->assertNotSame('', $estado);
    }

    public function test_notificaciones_vigentes_solo_consideran_los_ultimos_tres_dias(): void
    {
        $vieja = Notificacion::create([
            'user_id' => 1,
            'pedido_id' => null,
            'mensaje' => 'Notificación antigua',
            'tipo' => 'cliente',
            'evento' => 'prueba',
            'leido' => false,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(4),
        ]);

        $reciente = Notificacion::create([
            'user_id' => 1,
            'pedido_id' => null,
            'mensaje' => 'Notificación reciente',
            'tipo' => 'cliente',
            'evento' => 'prueba',
            'leido' => false,
            'created_at' => Carbon::now()->subDay(),
            'updated_at' => Carbon::now()->subDay(),
        ]);

        $ids = Notificacion::vigentes()
            ->pluck('id')
            ->all();

        $this->assertNotContains($vieja->id, $ids);
        $this->assertContains($reciente->id, $ids);
    }

    public function test_comando_elimina_notificaciones_con_mas_de_tres_dias(): void
    {
        $vieja = Notificacion::create([
            'user_id' => 1,
            'pedido_id' => null,
            'mensaje' => 'Notificación para borrar',
            'tipo' => 'cliente',
            'evento' => 'prueba',
            'leido' => false,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(4),
        ]);

        $reciente = Notificacion::create([
            'user_id' => 1,
            'pedido_id' => null,
            'mensaje' => 'Notificación para conservar',
            'tipo' => 'cliente',
            'evento' => 'prueba',
            'leido' => false,
            'created_at' => Carbon::now()->subDay(),
            'updated_at' => Carbon::now()->subDay(),
        ]);

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
