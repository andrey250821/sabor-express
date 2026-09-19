<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use App\Services\GenerarComprobanteOcrService;
use Illuminate\Console\Command;

class GenerarComprobantesOcr extends Command
{
    protected $signature = 'ocr:generar-comprobantes
                            {--pedido= : ID del pedido para generar los comprobantes}';

    protected $description = 'Genera comprobantes de prueba OCR a partir de un pedido real';

    public function handle(GenerarComprobanteOcrService $generador): int
    {
        $pedidoId = $this->option('pedido');

        if (!$pedidoId) {
            $this->error('Debes indicar el ID del pedido.');
            $this->newLine();
            $this->line('Ejemplo:');
            $this->line('php artisan ocr:generar-comprobantes --pedido=1');

            return self::FAILURE;
        }

        $pedido = Pedido::with('user')->find($pedidoId);

        if (!$pedido) {
            $this->error("No existe el pedido #{$pedidoId}.");

            return self::FAILURE;
        }

        $totalReal = (float) $pedido->total;
        $referenciaCorrecta = '98452217';
        $fecha = now()->format('d/m/Y');

        $pruebas = [
            [
                'tipo' => 'correcto',
                'monto' => $totalReal,
                'referencia' => $referenciaCorrecta,
            ],
            [
                'tipo' => 'monto_incorrecto',
                'monto' => $totalReal + 20,
                'referencia' => '98452218',
            ],
            [
                'tipo' => 'referencia_duplicada',
                'monto' => $totalReal,
                'referencia' => $referenciaCorrecta,
            ],
            [
                'tipo' => 'incompleto',
                'monto' => $totalReal,
                'referencia' => '',
            ],
        ];

        foreach ($pruebas as $prueba) {
            try {
                $generador->generarDesdePedido(
                    pedido: $pedido,
                    tipo: $prueba['tipo'],
                    monto: $prueba['monto'],
                    referencia: $prueba['referencia'],
                    fecha: $fecha
                );

                $this->info(
                    "Generado: pedido_{$pedido->id}_comprobante_{$prueba['tipo']}.png"
                );
            } catch (\Throwable $e) {
                $this->error(
                    "No se pudo generar la prueba {$prueba['tipo']}: {$e->getMessage()}"
                );

                return self::FAILURE;
            }
        }

        $montoIncorrecto = $totalReal + 20;

        $this->newLine();
        $this->info(
            "Comprobantes generados correctamente para el pedido #{$pedido->id}."
        );

        $this->newLine();

        $this->table(
            ['Tipo', 'Monto', 'Referencia', 'Archivo'],
            [
                [
                    'Correcto',
                    'Bs ' . number_format($totalReal, 2),
                    $referenciaCorrecta,
                    "pedido_{$pedido->id}_comprobante_correcto.png",
                ],
                [
                    'Monto incorrecto',
                    'Bs ' . number_format($montoIncorrecto, 2),
                    '98452218',
                    "pedido_{$pedido->id}_comprobante_monto_incorrecto.png",
                ],
                [
                    'Referencia duplicada',
                    'Bs ' . number_format($totalReal, 2),
                    $referenciaCorrecta,
                    "pedido_{$pedido->id}_comprobante_referencia_duplicada.png",
                ],
                [
                    'Incompleto',
                    'Bs ' . number_format($totalReal, 2),
                    'FALTANTE',
                    "pedido_{$pedido->id}_comprobante_incompleto.png",
                ],
            ]
        );

        $this->newLine();

        $this->info('Ubicación:');
        $this->line(
            storage_path('app/public/comprobantes_test')
        );

        return self::SUCCESS;
    }
}
