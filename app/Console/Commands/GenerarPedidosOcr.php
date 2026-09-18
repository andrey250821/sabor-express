<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerarPedidosOcr extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ocr:generar-pedidos';

    /**
     * The console command description.
     */
    protected $description = 'Genera pedidos reales de prueba para las pruebas de OCR';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();

        try {
            // Buscar un cliente existente
            $cliente = User::where('role_id', 2)->first();

            if (!$cliente) {
                $this->error('No existe ningún usuario con role_id = 2 (Cliente).');
                return self::FAILURE;
            }

            // Crear o buscar categoría de pruebas
            $categoria = Categoria::firstOrCreate([
                'nombre' => 'Pruebas OCR',
            ]);

            // Crear o buscar productos de prueba
            $hamburguesa = Producto::firstOrCreate(
                ['nombre' => 'Hamburguesa OCR'],
                [
                    'categoria_id' => $categoria->id,
                    'descripcion' => 'Producto utilizado para pruebas de OCR.',
                    'precio' => 35.00,
                    'stock' => 100,
                    'estado' => 1,
                ]
            );

            $salchipapa = Producto::firstOrCreate(
                ['nombre' => 'Salchipapa OCR'],
                [
                    'categoria_id' => $categoria->id,
                    'descripcion' => 'Producto utilizado para pruebas de OCR.',
                    'precio' => 40.00,
                    'stock' => 100,
                    'estado' => 1,
                ]
            );

            $gaseosa = Producto::firstOrCreate(
                ['nombre' => 'Gaseosa OCR'],
                [
                    'categoria_id' => $categoria->id,
                    'descripcion' => 'Producto utilizado para pruebas de OCR.',
                    'precio' => 15.00,
                    'stock' => 100,
                    'estado' => 1,
                ]
            );

            /*
             * PEDIDO 1
             * 2 hamburguesas + 1 gaseosa
             * Total = 85 Bs
             */
            $pedido1 = Pedido::create([
                'user_id' => $cliente->id,
                'total' => 85.00,
                'estado' => 'comprobante_enviado',
                'direccion_entrega' => $cliente->direccion ?? 'Dirección de prueba OCR',
                'observacion_cliente' => 'Pedido generado para pruebas de OCR.',
                'referencia_delivery' => 'Prueba OCR',
            ]);

            DetallePedido::create([
                'pedido_id' => $pedido1->id,
                'producto_id' => $hamburguesa->id,
                'cantidad' => 2,
                'precio' => 35.00,
                'subtotal' => 70.00,
            ]);

            DetallePedido::create([
                'pedido_id' => $pedido1->id,
                'producto_id' => $gaseosa->id,
                'cantidad' => 1,
                'precio' => 15.00,
                'subtotal' => 15.00,
            ]);

            /*
             * PEDIDO 2
             * 1 salchipapa + 2 gaseosas
             * Total = 70 Bs
             */
            $pedido2 = Pedido::create([
                'user_id' => $cliente->id,
                'total' => 70.00,
                'estado' => 'comprobante_enviado',
                'direccion_entrega' => $cliente->direccion ?? 'Dirección de prueba OCR',
                'observacion_cliente' => 'Pedido generado para pruebas de OCR.',
                'referencia_delivery' => 'Prueba OCR',
            ]);

            DetallePedido::create([
                'pedido_id' => $pedido2->id,
                'producto_id' => $salchipapa->id,
                'cantidad' => 1,
                'precio' => 40.00,
                'subtotal' => 40.00,
            ]);

            DetallePedido::create([
                'pedido_id' => $pedido2->id,
                'producto_id' => $gaseosa->id,
                'cantidad' => 2,
                'precio' => 15.00,
                'subtotal' => 30.00,
            ]);

            /*
             * PEDIDO 3
             * 2 hamburguesas + 1 salchipapa + 1 gaseosa
             * Total = 125 Bs
             */
            $pedido3 = Pedido::create([
                'user_id' => $cliente->id,
                'total' => 125.00,
                'estado' => 'comprobante_enviado',
                'direccion_entrega' => $cliente->direccion ?? 'Dirección de prueba OCR',
                'observacion_cliente' => 'Pedido generado para pruebas de OCR.',
                'referencia_delivery' => 'Prueba OCR',
            ]);

            DetallePedido::create([
                'pedido_id' => $pedido3->id,
                'producto_id' => $hamburguesa->id,
                'cantidad' => 2,
                'precio' => 35.00,
                'subtotal' => 70.00,
            ]);

            DetallePedido::create([
                'pedido_id' => $pedido3->id,
                'producto_id' => $salchipapa->id,
                'cantidad' => 1,
                'precio' => 40.00,
                'subtotal' => 40.00,
            ]);

            DetallePedido::create([
                'pedido_id' => $pedido3->id,
                'producto_id' => $gaseosa->id,
                'cantidad' => 1,
                'precio' => 15.00,
                'subtotal' => 15.00,
            ]);

            DB::commit();

            $this->newLine();

            $this->info('Pedidos de prueba OCR creados correctamente.');

            $this->table(
                ['Pedido', 'Cliente', 'Total', 'Estado'],
                [
                    [
                        $pedido1->id,
                        $cliente->name,
                        'Bs ' . number_format($pedido1->total, 2),
                        $pedido1->estado,
                    ],
                    [
                        $pedido2->id,
                        $cliente->name,
                        'Bs ' . number_format($pedido2->total, 2),
                        $pedido2->estado,
                    ],
                    [
                        $pedido3->id,
                        $cliente->name,
                        'Bs ' . number_format($pedido3->total, 2),
                        $pedido3->estado,
                    ],
                ]
            );

            $this->newLine();
            $this->info('Ahora podemos generar los comprobantes OCR usando el ID real de cada pedido.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->error('No se pudieron crear los pedidos.');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
