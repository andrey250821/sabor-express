<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerarComprobantesOcr extends Command
{
    protected $signature = 'ocr:generar-comprobantes
                            {--pedido= : ID del pedido para generar los comprobantes}';

    protected $description = 'Genera comprobantes de prueba OCR a partir de un pedido real';

    public function handle()
    {
        $pedidoId = $this->option('pedido');

        if (!$pedidoId) {
            $this->error('Debes indicar el ID del pedido.');
            $this->newLine();
            $this->line('Ejemplo:');
            $this->line('php artisan ocr:generar-comprobantes --pedido=1');

            return self::FAILURE;
        }

        // Obtener el pedido real junto con el cliente
        $pedido = Pedido::with('user')->find($pedidoId);

        if (!$pedido) {
            $this->error("No existe el pedido #{$pedidoId}.");

            return self::FAILURE;
        }

        // Comprobar Chrome
        $chrome = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

        if (!File::exists($chrome)) {
            $this->error('No se encontró Google Chrome en:');
            $this->line($chrome);

            return self::FAILURE;
        }

        // Directorios
        $htmlDirectory = storage_path('app/ocr-test/html');
        $outputDirectory = storage_path('app/public/comprobantes_test');

        File::ensureDirectoryExists($htmlDirectory);
        File::ensureDirectoryExists($outputDirectory);

        // Datos reales del pedido
        $numeroPedido = $pedido->id;
        $totalReal = (float) $pedido->total;
        $cliente = $pedido->user?->name ?? 'Cliente Sabor Express';
        $fecha = now()->format('d/m/Y');

        // Referencia utilizada en las pruebas
        $referenciaCorrecta = '98452217';

        /*
        |--------------------------------------------------------------------------
        | 1. COMPROBANTE CORRECTO
        |--------------------------------------------------------------------------
        */
        $this->generarComprobante(
            pedido: $pedido,
            tipo: 'correcto',
            monto: $totalReal,
            referencia: $referenciaCorrecta,
            fecha: $fecha,
            cliente: $cliente,
            chrome: $chrome,
            htmlDirectory: $htmlDirectory,
            outputDirectory: $outputDirectory
        );

        /*
        |--------------------------------------------------------------------------
        | 2. MONTO INCORRECTO
        |--------------------------------------------------------------------------
        */
        $montoIncorrecto = $totalReal + 20;

        $this->generarComprobante(
            pedido: $pedido,
            tipo: 'monto_incorrecto',
            monto: $montoIncorrecto,
            referencia: '98452218',
            fecha: $fecha,
            cliente: $cliente,
            chrome: $chrome,
            htmlDirectory: $htmlDirectory,
            outputDirectory: $outputDirectory
        );

        /*
        |--------------------------------------------------------------------------
        | 3. REFERENCIA DUPLICADA
        |--------------------------------------------------------------------------
        */
        $this->generarComprobante(
            pedido: $pedido,
            tipo: 'referencia_duplicada',
            monto: $totalReal,
            referencia: $referenciaCorrecta,
            fecha: $fecha,
            cliente: $cliente,
            chrome: $chrome,
            htmlDirectory: $htmlDirectory,
            outputDirectory: $outputDirectory
        );

        /*
        |--------------------------------------------------------------------------
        | 4. COMPROBANTE INCOMPLETO
        |--------------------------------------------------------------------------
        */
        $this->generarComprobante(
            pedido: $pedido,
            tipo: 'incompleto',
            monto: $totalReal,
            referencia: '',
            fecha: $fecha,
            cliente: $cliente,
            chrome: $chrome,
            htmlDirectory: $htmlDirectory,
            outputDirectory: $outputDirectory
        );

        $this->newLine();

        $this->info(
            "Comprobantes generados correctamente para el pedido #{$numeroPedido}."
        );

        $this->newLine();

        $this->table(
            ['Tipo', 'Monto', 'Referencia', 'Archivo'],
            [
                [
                    'Correcto',
                    'Bs ' . number_format($totalReal, 2),
                    $referenciaCorrecta,
                    "pedido_{$numeroPedido}_comprobante_correcto.png",
                ],
                [
                    'Monto incorrecto',
                    'Bs ' . number_format($montoIncorrecto, 2),
                    '98452218',
                    "pedido_{$numeroPedido}_comprobante_monto_incorrecto.png",
                ],
                [
                    'Referencia duplicada',
                    'Bs ' . number_format($totalReal, 2),
                    $referenciaCorrecta,
                    "pedido_{$numeroPedido}_comprobante_referencia_duplicada.png",
                ],
                [
                    'Incompleto',
                    'Bs ' . number_format($totalReal, 2),
                    'FALTANTE',
                    "pedido_{$numeroPedido}_comprobante_incompleto.png",
                ],
            ]
        );

        $this->newLine();

        $this->info('Ubicación:');
        $this->line($outputDirectory);

        return self::SUCCESS;
    }

    /**
     * Genera un comprobante HTML y posteriormente lo convierte
     * en una imagen PNG utilizando Google Chrome.
     */
    private function generarComprobante(
        Pedido $pedido,
        string $tipo,
        float $monto,
        string $referencia,
        string $fecha,
        string $cliente,
        string $chrome,
        string $htmlDirectory,
        string $outputDirectory
    ): void {
        $numeroPedido = $pedido->id;

        $montoFormateado = number_format($monto, 2);

        $nombreArchivo = "pedido_{$numeroPedido}_comprobante_{$tipo}.png";

        $htmlPath = $htmlDirectory
            . DIRECTORY_SEPARATOR
            . "pedido_{$numeroPedido}_{$tipo}.html";

        $outputPath = $outputDirectory
            . DIRECTORY_SEPARATOR
            . $nombreArchivo;

        // Preparar valores seguros para HTML
        $clienteHtml = htmlspecialchars(
            $cliente,
            ENT_QUOTES,
            'UTF-8'
        );

        $referenciaHtml = $referencia !== ''
            ? htmlspecialchars(
                $referencia,
                ENT_QUOTES,
                'UTF-8'
            )
            : '<span class="faltante">NO PRESENTADA</span>';

        $tipoTexto = match ($tipo) {
            'correcto' => 'COMPROBANTE DE PAGO',
            'monto_incorrecto' => 'COMPROBANTE DE PAGO',
            'referencia_duplicada' => 'COMPROBANTE DE PAGO',
            'incompleto' => 'COMPROBANTE DE PAGO',
            default => 'COMPROBANTE DE PAGO',
        };

        /*
        |--------------------------------------------------------------------------
        | HTML DEL COMPROBANTE
        |--------------------------------------------------------------------------
        */
        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Sabor Express - Comprobante</title>

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #eeeeee;
            font-family: Arial, Helvetica, sans-serif;
        }

        .comprobante {
            width: 900px;
            min-height: 1100px;
            margin: 0 auto;
            padding: 55px;
            background: #ffffff;
            color: #222222;
        }

        .encabezado {
            text-align: center;
            border-bottom: 3px solid #222222;
            padding-bottom: 25px;
            margin-bottom: 35px;
        }

        .logo {
            font-size: 42px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #8b1e45;
        }

        .subtitulo {
            margin-top: 10px;
            font-size: 24px;
            font-weight: bold;
        }

        .numero-pedido {
            margin-top: 15px;
            font-size: 20px;
            color: #555555;
        }

        .seccion {
            margin-top: 30px;
            border: 2px solid #dddddd;
            border-radius: 10px;
            padding: 25px;
        }

        .fila {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid #eeeeee;
            font-size: 22px;
        }

        .fila:last-child {
            border-bottom: none;
        }

        .etiqueta {
            font-weight: bold;
            color: #555555;
        }

        .valor {
            font-weight: bold;
            text-align: right;
        }

        .total {
            margin-top: 35px;
            padding: 25px;
            background: #f3f3f3;
            border-radius: 10px;

            display: flex;
            justify-content: space-between;

            font-size: 32px;
            font-weight: bold;
        }

        .estado {
            margin-top: 35px;
            text-align: center;

            border: 3px solid #198754;
            color: #198754;

            padding: 22px;

            font-size: 28px;
            font-weight: bold;

            border-radius: 10px;
        }

        .faltante {
            color: #dc3545;
            font-weight: bold;
        }

        .pie {
            margin-top: 50px;
            padding-top: 25px;

            border-top: 2px dashed #cccccc;

            text-align: center;

            color: #666666;
            font-size: 18px;
        }

        .nota {
            margin-top: 25px;

            text-align: center;

            font-size: 16px;
            color: #888888;
        }

    </style>

</head>

<body>

    <div class="comprobante">

        <div class="encabezado">

            <div class="logo">
                SABOR EXPRESS
            </div>

            <div class="subtitulo">
                {$tipoTexto}
            </div>

            <div class="numero-pedido">
                Pedido N° {$numeroPedido}
            </div>

        </div>


        <div class="seccion">

            <div class="fila">

                <span class="etiqueta">
                    Pedido
                </span>

                <span class="valor">
                    #{$numeroPedido}
                </span>

            </div>


            <div class="fila">

                <span class="etiqueta">
                    Cliente
                </span>

                <span class="valor">
                    {$clienteHtml}
                </span>

            </div>


            <div class="fila">

                <span class="etiqueta">
                    Fecha
                </span>

                <span class="valor">
                    {$fecha}
                </span>

            </div>


            <div class="fila">

                <span class="etiqueta">
                    Banco
                </span>

                <span class="valor">
                    Banco Unión
                </span>

            </div>


            <div class="fila">

                <span class="etiqueta">
                    N° de referencia
                </span>

                <span class="valor">
                    {$referenciaHtml}
                </span>

            </div>

        </div>


        <div class="total">

            <span>
                Monto pagado
            </span>

            <span>
                Bs {$montoFormateado}
            </span>

        </div>


        <div class="estado">
            PAGO REALIZADO
        </div>


        <div class="pie">

            Sabor Express

            <br>

            Comprobante de pago

        </div>


        <div class="nota">

            Documento generado automáticamente
            para pruebas de reconocimiento OCR.

        </div>

    </div>

</body>

</html>
HTML;

        // Guardar HTML temporal
        File::put($htmlPath, $html);

        // Eliminar imagen anterior
        if (File::exists($outputPath)) {
            File::delete($outputPath);
        }

        /*
        |--------------------------------------------------------------------------
        | Convertir HTML a PNG mediante Chrome
        |--------------------------------------------------------------------------
        */

        $htmlUri = 'file:///' . str_replace(
            '\\',
            '/',
            $htmlPath
        );

        $command =
            '"' . $chrome . '"'
            . ' --headless=new'
            . ' --disable-gpu'
            . ' --no-sandbox'
            . ' --hide-scrollbars'
            . ' --window-size=900,1200'
            . ' --screenshot="'
            . $outputPath
            . '"'
            . ' "'
            . $htmlUri
            . '"';

        $output = [];
        $resultCode = 0;

        exec(
            $command . ' 2>&1',
            $output,
            $resultCode
        );

        if (
            $resultCode !== 0 ||
            !File::exists($outputPath)
        ) {
            $this->error(
                "No se pudo generar {$nombreArchivo}"
            );

            if (!empty($output)) {
                foreach ($output as $line) {
                    $this->line($line);
                }
            }

            return;
        }

        $this->info(
            "Generado: {$nombreArchivo}"
        );
    }
}