<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportesTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuario(string $nombreRol, string $correo): User
    {
        $rol = Role::where('nombre', $nombreRol)->firstOrFail();

        return User::create([
            'role_id' => $rol->id,
            'name' => 'Usuario de prueba de reportes',
            'email' => $correo,
            'password' => 'password',
            'estado' => 'activo',
        ]);
    }

    public function test_administrador_puede_abrir_la_pantalla_de_reportes(): void
    {
        $administrador = $this->crearUsuario(
            'Administrador',
            'admin-reportes-index@example.com'
        );

        $this->actingAs($administrador)
            ->get(route('admin.reportes.index'))
            ->assertOk()
            ->assertSee('Reportes administrativos')
            ->assertSee('Reporte de pedidos')
            ->assertSee('Reporte de ventas')
            ->assertSee('Reporte de entregas')
            ->assertSee('Reporte de actividad del personal');
    }

    public function test_cliente_no_puede_acceder_a_los_reportes_administrativos(): void
    {
        $cliente = $this->crearUsuario(
            'Cliente',
            'cliente-reportes-acceso@example.com'
        );

        $this->actingAs($cliente)
            ->get(route('admin.reportes.index'))
            ->assertForbidden();
    }

    public function test_administrador_puede_descargar_los_cuatro_tipos_de_reporte_pdf(): void
    {
        $administrador = $this->crearUsuario(
            'Administrador',
            'admin-reportes-pdf@example.com'
        );

        $hoy = Carbon::now('America/La_Paz')->toDateString();
        $desde = Carbon::now('America/La_Paz')->startOfMonth()->toDateString();

        foreach (['pedidos', 'ventas', 'entregas', 'personal'] as $tipo) {
            $respuesta = $this->actingAs($administrador)->get(
                route('admin.reportes.pdf', [
                    'tipo' => $tipo,
                    'desde' => $desde,
                    'hasta' => $hoy,
                ])
            );

            $respuesta->assertOk();

            $tipoContenido = strtolower(
                explode(';', (string) $respuesta->headers->get('content-type'))[0]
            );

            $this->assertSame(
                'application/pdf',
                $tipoContenido,
                "El reporte {$tipo} debe descargarse como un archivo PDF."
            );

            $this->assertStringStartsWith(
                '%PDF',
                $respuesta->getContent(),
                "El contenido del reporte {$tipo} debe tener la cabecera de un documento PDF."
            );
        }
    }

    public function test_sistema_rechaza_un_periodo_con_fecha_final_anterior_a_la_inicial(): void
    {
        $administrador = $this->crearUsuario(
            'Administrador',
            'admin-reportes-rango@example.com'
        );

        $hoy = Carbon::now('America/La_Paz')->toDateString();
        $ayer = Carbon::now('America/La_Paz')->subDay()->toDateString();

        $this->actingAs($administrador)
            ->from(route('admin.reportes.index'))
            ->get(route('admin.reportes.pdf', [
                'tipo' => 'pedidos',
                'desde' => $hoy,
                'hasta' => $ayer,
            ]))
            ->assertRedirect(route('admin.reportes.index'))
            ->assertSessionHasErrors('hasta');
    }

    public function test_sistema_rechaza_periodos_que_incluyen_fechas_futuras(): void
    {
        $administrador = $this->crearUsuario(
            'Administrador',
            'admin-reportes-futuro@example.com'
        );

        $hoy = Carbon::now('America/La_Paz')->toDateString();
        $mañana = Carbon::now('America/La_Paz')->addDay()->toDateString();

        $this->actingAs($administrador)
            ->from(route('admin.reportes.index'))
            ->get(route('admin.reportes.pdf', [
                'tipo' => 'ventas',
                'desde' => $hoy,
                'hasta' => $mañana,
            ]))
            ->assertRedirect(route('admin.reportes.index'))
            ->assertSessionHasErrors('hasta');
    }
}
