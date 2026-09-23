<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use App\Models\ComprobantePago;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerarComprobantesOcr extends Command
{
    protected $signature = 'ocr:generar-comprobantes
                            {--pedido= : Número del pedido}
                            {--total= : Total del pedido. Se usa si el pedido aún no existe en la BD}
                            {--cliente= : Nombre del cliente. Se usa si el pedido aún no existe en la BD}';

    protected $description = 'Genera comprobantes de prueba OCR a partir de un pedido real o de datos de checkout';

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

        // Intentar primero con un pedido real de la BD.
        $pedido = Pedido::with('user')->find($pedidoId);

        if (!$pedido) {
            /*
             * El pedido todavía puede no existir porque el flujo actual
             * solicita el comprobante antes de guardar el pedido.
             *
             * En ese caso permitimos generar una imagen sintética usando
             * los datos que conocemos en el checkout.
             */
            $total = $this->option('total');
            $cliente = $this->option('cliente');

            if ($total === null || $cliente === null || trim($cliente) === '') {
                $this->error("No existe el pedido #{$pedidoId} en la BD.");
                $this->newLine();
                $this->line('Para generar la imagen antes de crear el pedido debes indicar también:');
                $this->line('--total=...');
                $this->line('--cliente="..."');
                $this->newLine();
                $this->line('Ejemplo:');
                $this->line('php artisan ocr:generar-comprobantes --pedido=5 --total=85 --cliente="Juan"');

                return self::FAILURE;
            }

            if (!is_numeric($total) || (float) $total <= 0) {
                $this->error('El total debe ser un número mayor que 0.');

                return self::FAILURE;
            }

            // Pedido temporal: NO se guarda en la base de datos.
            $pedido = new Pedido();
            $pedido->id = (int) $pedidoId;
            $pedido->total = (float) $total;

            $clienteNombre = trim($cliente);

            $this->warn(
                "El pedido #{$pedidoId} todavía no existe en la BD. Se generarán imágenes de prueba usando los datos proporcionados."
            );
        } else {
            $clienteNombre = $pedido->user?->name ?? 'Cliente Sabor Express';
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
        // El nombre ya se resolvió desde la BD o desde --cliente.
        $fecha = now()->format('d/m/Y');

        // Generar una referencia numérica que no exista todavía en la base de datos.
        // La misma referencia se reutiliza únicamente en la imagen de
        // "referencia duplicada" para provocar ese caso de prueba.
        $referenciaCorrecta = $this->generarReferenciaDisponible($numeroPedido);

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
            cliente: $clienteNombre,
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
            cliente: $clienteNombre,
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
            cliente: $clienteNombre,
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
            cliente: $clienteNombre,
            chrome: $chrome,
            htmlDirectory: $htmlDirectory,
            outputDirectory: $outputDirectory
        );

        $this->info('Imágenes de prueba OCR creadas correctamente.');

        return self::SUCCESS;
    }

    /**
     * Genera una referencia bancaria numérica que no esté registrada.
     */
    private function generarReferenciaDisponible(int $pedidoId): string
    {
        $base = 98450000 + $pedidoId;

        for ($i = 0; $i < 1000; $i++) {
            $referencia = (string) ($base + $i);

            if (!ComprobantePago::where('referencia_bancaria', $referencia)->exists()) {
                return $referencia;
            }
        }

        do {
            $referencia = (string) random_int(10000000, 99999999);
        } while (ComprobantePago::where('referencia_bancaria', $referencia)->exists());

        return $referencia;
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

        /*
         * El nombre del cliente se incluye en el archivo para que sea
         * fácil identificar a qué pedido pertenece cada imagen.
         *
         * Ejemplo:
         * pedido_4_cliente_Juan_Perez_CORRECTO.png
         */
        $clienteArchivo = Str::slug(
            trim($cliente) !== '' ? $cliente : 'cliente',
            '_'
        );

        $tipoArchivo = match ($tipo) {
            'correcto' => 'CORRECTO',
            'monto_incorrecto' => 'MONTO_INCORRECTO',
            'referencia_duplicada' => 'REFERENCIA_DUPLICADA',
            'incompleto' => 'INCOMPLETO',
            default => Str::upper(Str::slug($tipo, '_')),
        };

        $nombreArchivo =
            "pedido_{$numeroPedido}_cliente_{$clienteArchivo}_{$tipoArchivo}.png";

        $nombreHtml =
            "pedido_{$numeroPedido}_cliente_{$clienteArchivo}_{$tipoArchivo}.html";

        $htmlPath = $htmlDirectory
            . DIRECTORY_SEPARATOR
            . $nombreHtml;

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
                NUMERO DE PEDIDO: {$numeroPedido}
            </div>

        </div>


        <div class="seccion">

            <div class="fila">

                <span class="etiqueta">
                    NUMERO DE PEDIDO
                </span>

                <span class="valor">
                    {$numeroPedido}
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

        $this->line(
            "  ✓ {$nombreArchivo}"
        );

    }
}