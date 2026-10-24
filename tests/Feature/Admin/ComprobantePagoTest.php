<?php

namespace Tests\Feature\Admin;

use App\Models\Categoria;
use App\Models\ComprobantePago;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprobantePagoTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuario(string $nombreRol, string $correo): User
    {
        $rol = Role::where('nombre', $nombreRol)->firstOrFail();

        return User::create([
            'role_id' => $rol->id,
            'name' => 'Usuario de prueba',
            'email' => $correo,
            'password' => 'password',
            'estado' => 'activo',
        ]);
    }

    /**
     * Crea un pedido con un comprobante pendiente para probar su revisión.
     *
     * @return array{0: User, 1: Pedido, 2: ComprobantePago}
     */
    private function crearPedidoConComprobante(string $correoCliente): array
    {
        $cliente = $this->crearUsuario('Cliente', $correoCliente);

        $pedido = Pedido::create([
            'user_id' => $cliente->id,
            'total' => 50.00,
            'subtotal_productos' => 45.00,
            'tarifa_delivery' => 5.00,
            'estado' => 'comprobante_enviado',
            'direccion_entrega' => 'Dirección de prueba',
        ]);

        $comprobante = ComprobantePago::create([
            'pedido_id' => $pedido->id,
            'imagen' => 'comprobantes/comprobante-prueba.jpg',
            'referencia_bancaria' => 'REF-TEST-001',
            'estado' => 'en_revision',
        ]);

        return [$cliente, $pedido, $comprobante];
    }

    public function test_administrador_puede_aprobar_comprobante_pendiente(): void
    {
        $administrador = $this->crearUsuario(
            'Administrador',
            'admin-aprobar-comprobante@example.com'
        );

        [$cliente, $pedido, $comprobante] = $this->crearPedidoConComprobante(
            'cliente-aprobar-comprobante@example.com'
        );

        $respuesta = $this->actingAs($administrador)
            ->from('/admin/comprobantes/en_revision')
            ->put(route('admin.comprobantes.aprobar', $comprobante->id));

        $respuesta->assertRedirect('/admin/comprobantes/en_revision');
        $respuesta->assertSessionHas(
            'success',
            'Comprobante aprobado correctamente'
        );

        $this->assertDatabaseHas('comprobantes_pago', [
            'id' => $comprobante->id,
            'estado' => 'aprobado',
        ]);

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'estado' => 'pagado',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $cliente->id,
            'pedido_id' => $pedido->id,
            'evento' => 'pedido_aceptado',
        ]);
    }

    public function test_administrador_puede_rechazar_comprobante_y_se_restauran_existencias(): void
    {
        $administrador = $this->crearUsuario(
            'Administrador',
            'admin-rechazar-comprobante@example.com'
        );

        [$cliente, $pedido, $comprobante] = $this->crearPedidoConComprobante(
            'cliente-rechazar-comprobante@example.com'
        );

        $categoria = Categoria::create([
            'nombre' => 'Categoría comprobante prueba',
            'estado' => 'activo',
        ]);

        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Producto del pedido rechazado',
            'descripcion' => 'Producto de prueba para validar la devolución de stock.',
            'precio' => 25.00,
            'stock' => 3,
            'estado' => 'disponible',
        ]);

        DetallePedido::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio' => 25.00,
            'subtotal' => 50.00,
        ]);

        $respuesta = $this->actingAs($administrador)
            ->from('/admin/comprobantes/en_revision')
            ->put(route('admin.comprobantes.rechazar', $comprobante->id));

        $respuesta->assertRedirect('/admin/comprobantes/en_revision');
        $respuesta->assertSessionHas(
            'success',
            'Comprobante rechazado correctamente y stock liberado.'
        );

        $this->assertDatabaseHas('comprobantes_pago', [
            'id' => $comprobante->id,
            'estado' => 'rechazado',
        ]);

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'estado' => 'cancelado',
        ]);

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'stock' => 5,
            'estado' => 'disponible',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $cliente->id,
            'pedido_id' => $pedido->id,
            'evento' => 'comprobante_rechazado',
        ]);
    }

    public function test_comprobantes_en_revision_muestran_acciones_visibles_de_aprobar_y_rechazar(): void
    {
        $administrador = $this->crearUsuario(
            'Administrador',
            'admin-listado-comprobantes@example.com'
        );

        $this->crearPedidoConComprobante(
            'cliente-listado-comprobantes@example.com'
        );

        $fecha = now('America/La_Paz')->toDateString();

        $respuesta = $this->actingAs($administrador)
            ->get('/admin/comprobantes/en_revision?fecha=' . $fecha);

        $respuesta->assertOk()
            ->assertSee('Aprobar comprobante')
            ->assertSee('Rechazar comprobante')
            ->assertSee('btn-aprobar-comprobante')
            ->assertSee('btn-rechazar-comprobante');
    }
}
