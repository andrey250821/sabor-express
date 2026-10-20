<?php

namespace Tests\Feature\Cliente;

use App\Models\Categoria;
use App\Models\ComprobantePago;
use App\Models\DetallePedido;
use App\Models\Notificacion;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Role;
use App\Models\User;
use App\Services\CalcularDeliveryService;
use App\Services\ValidarComprobantePagoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    private function crearCliente(): User
    {
        $role = Role::firstOrCreate(['nombre' => 'Cliente']);

        return User::create([
            'role_id' => $role->id,
            'name' => 'Cliente Pruebas Pedidos',
            'email' => 'cliente-pedidos@example.com',
            'telefono' => '71234567',
            'password' => 'password',
            'estado' => 'activo',
        ]);
    }

    /**
     * @return array{0: Categoria, 1: Producto}
     */
    private function crearProducto(int $stock = 5): array
    {
        $categoria = Categoria::create([
            'nombre' => 'Categoría Pedidos',
            'estado' => 'activo',
        ]);

        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Hamburguesa para pedido',
            'descripcion' => 'Producto de prueba para registrar pedidos.',
            'precio' => 25.00,
            'stock' => $stock,
            'estado' => $stock > 0 ? 'disponible' : 'agotado',
        ]);

        return [$categoria, $producto];
    }

    /**
     * Genera un PNG pequeño y válido sin depender de la extensión GD.
     */
    private function crearComprobantePrueba(): UploadedFile
    {
        $contenido = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC'
        );

        $this->assertNotFalse($contenido);

        return UploadedFile::fake()->createWithContent(
            'comprobante.png',
            $contenido
        );
    }

    private function cotizacionPrueba(): array
    {
        return [
            'distancia_delivery_km' => 1.20,
            'tarifa_delivery' => 5.00,
            'porcentaje_delivery' => 80.00,
            'monto_delivery' => 4.00,
            'porcentaje_restaurante_delivery' => 20.00,
            'monto_restaurante_delivery' => 1.00,
        ];
    }

    public function test_checkout_vacio_redirige_al_catalogo(): void
    {
        $cliente = $this->crearCliente();

        $this->actingAs($cliente)
            ->get(route('cliente.pedidos.create'))
            ->assertRedirect(route('cliente.productos.index'))
            ->assertSessionHas('error', 'El carrito está vacío');
    }

    public function test_registro_de_pedido_valida_los_datos_obligatorios(): void
    {
        $cliente = $this->crearCliente();

        $this->actingAs($cliente)
            ->from(route('cliente.pedidos.create'))
            ->post(route('cliente.pedidos.store'), [])
            ->assertRedirect(route('cliente.pedidos.create'))
            ->assertSessionHasErrors([
                'direccion_entrega',
                'latitud',
                'longitud',
                'comprobante',
            ]);

        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_cliente_puede_registrar_un_pedido_y_se_actualiza_el_stock(): void
    {
        $cliente = $this->crearCliente();
        [, $producto] = $this->crearProducto(5);

        Storage::fake('public');

        $this->mock(
            CalcularDeliveryService::class,
            function (MockInterface $mock): void {
                $mock->shouldReceive('cotizar')
                    ->once()
                    ->andReturn($this->cotizacionPrueba());
            }
        );

        $this->mock(
            ValidarComprobantePagoService::class,
            function (MockInterface $mock): void {
                $mock->shouldReceive('validar')
                    ->once()
                    ->andReturn([
                        'estado' => 'aprobado',
                        'motivo_revision' => null,
                        'datos' => [
                            'pedido' => null,
                            'monto' => null,
                            'referencia' => 'REF-PRUEBA-001',
                            'fecha' => null,
                            'banco' => null,
                            'completo' => true,
                        ],
                    ]);
            }
        );

        $carrito = [
            $producto->id => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => (float) $producto->precio,
                'cantidad' => 2,
                'subtotal' => 50.00,
                'imagen' => null,
                'stock' => $producto->stock,
            ],
        ];

        $response = $this->actingAs($cliente)
            ->withSession(['carrito' => $carrito])
            ->from(route('cliente.pedidos.create'))
            ->post(route('cliente.pedidos.store'), [
                'direccion_entrega' => 'Calle de prueba N.º 123',
                'referencia_delivery' => 'Portón negro',
                'latitud' => '-17.393',
                'longitud' => '-66.157',
                'observacion_cliente' => 'Entregar en la puerta',
                'comprobante' => $this->crearComprobantePrueba(),
            ]);

        $pedido = Pedido::where('user_id', $cliente->id)->first();

        $this->assertNotNull($pedido);

        $response->assertRedirect(route('cliente.pedidos.show', $pedido->id));
        $response->assertSessionMissing('carrito');

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'user_id' => $cliente->id,
            'estado' => 'pagado',
        ]);

        $this->assertDatabaseHas('detalle_pedidos', [
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'cantidad' => 2,
        ]);

        $this->assertSame(3, (int) $producto->fresh()->stock);

        $this->assertDatabaseHas('comprobantes_pago', [
            'pedido_id' => $pedido->id,
            'estado' => 'aprobado',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $cliente->id,
            'pedido_id' => $pedido->id,
            'evento' => 'pedido_aceptado',
        ]);

        $comprobante = ComprobantePago::where('pedido_id', $pedido->id)->firstOrFail();
        Storage::disk('public')->assertExists($comprobante->imagen);
    }

    public function test_no_registra_pedido_ni_descuenta_stock_si_no_hay_unidades_suficientes(): void
    {
        $cliente = $this->crearCliente();
        [, $producto] = $this->crearProducto(1);

        Storage::fake('public');

        $this->mock(
            CalcularDeliveryService::class,
            function (MockInterface $mock): void {
                $mock->shouldReceive('cotizar')
                    ->once()
                    ->andReturn($this->cotizacionPrueba());
            }
        );

        $carrito = [
            $producto->id => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => (float) $producto->precio,
                'cantidad' => 2,
                'subtotal' => 50.00,
                'imagen' => null,
                'stock' => $producto->stock,
            ],
        ];

        $this->actingAs($cliente)
            ->withSession(['carrito' => $carrito])
            ->from(route('cliente.pedidos.create'))
            ->post(route('cliente.pedidos.store'), [
                'direccion_entrega' => 'Calle de prueba N.º 123',
                'referencia_delivery' => null,
                'latitud' => '-17.393',
                'longitud' => '-66.157',
                'observacion_cliente' => null,
                'comprobante' => $this->crearComprobantePrueba(),
            ])
            ->assertRedirect(route('cliente.pedidos.create'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('pedidos', 0);
        $this->assertDatabaseCount('detalle_pedidos', 0);
        $this->assertSame(1, (int) $producto->fresh()->stock);
        $this->assertSame('disponible', $producto->fresh()->estado);
        $this->assertSame(0, Notificacion::count());
    }
}
