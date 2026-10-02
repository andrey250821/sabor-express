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

        /*
         * Las imágenes finales se guardan únicamente en:
         * storage/app/public/comprobantes_test
         *
         * El HTML solo es un archivo temporal necesario para que Chrome
         * pueda renderizar el comprobante. Se crea en la carpeta temporal
         * del sistema y se elimina al terminar la generación.
         */
        $htmlDirectory = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'sabor-express-ocr-' . uniqid('', true);

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
        | Generar las 4 variantes de prueba
        |--------------------------------------------------------------------------
        */
        try {
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
        } finally {
            // El HTML se usa solo para renderizar las imágenes y no forma
            // parte de los archivos de prueba que conserva la aplicación.
            File::deleteDirectory($htmlDirectory);
        }
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

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sabor Express - Comprobante</title>

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #eceef2;
            font-family: Arial, Helvetica, sans-serif;
            color: #17191f;
        }

        body {
            padding: 30px;
        }

        .comprobante {
            width: 900px;
            min-height: 1180px;
            margin: 0 auto;
            padding: 46px;
            background: #ffffff;
            border: 1px solid #d8dbe2;
            border-radius: 18px;
            box-shadow: 0 18px 55px rgba(18, 20, 27, .13);
        }

        .encabezado {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            padding-bottom: 28px;
            border-bottom: 2px solid #e6e8ee;
        }

        .marca {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .marca-icono {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: linear-gradient(135deg, #8b1e45, #d92d69);
            color: #ffffff;
            font-size: 23px;
            font-weight: 900;
            letter-spacing: .5px;
        }

        .marca-nombre {
            font-size: 34px;
            font-weight: 900;
            letter-spacing: 1.5px;
            color: #8b1e45;
        }

        .marca-subtitulo {
            margin-top: 4px;
            color: #777c87;
            font-size: 15px;
            font-weight: 700;
        }

        .estado-cabecera {
            padding: 9px 14px;
            border: 1px solid #bfe8d3;
            border-radius: 999px;
            background: #eefbf4;
            color: #16744a;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .04em;
            white-space: nowrap;
        }

        .titulo-documento {
            margin-top: 34px;
            text-align: center;
        }

        .titulo-documento h1 {
            margin: 0;
            font-size: 30px;
            line-height: 1.2;
            letter-spacing: .03em;
        }

        .titulo-documento p {
            margin: 10px 0 0;
            color: #737781;
            font-size: 16px;
            font-weight: 700;
        }

        .pedido-pill {
            display: inline-block;
            margin-top: 16px;
            padding: 9px 16px;
            border-radius: 999px;
            background: #f8eaf0;
            border: 1px solid #f0c9da;
            color: #8b1e45;
            font-size: 15px;
            font-weight: 900;
        }

        .seccion {
            margin-top: 30px;
            padding: 24px;
            border: 1px solid #dfe2e8;
            border-radius: 14px;
            background: #fbfbfc;
        }

        .seccion-titulo {
            margin-bottom: 10px;
            color: #8b1e45;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .fila {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            padding: 17px 0;
            border-bottom: 1px solid #e8e9ed;
            font-size: 20px;
            line-height: 1.3;
        }

        .fila:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .etiqueta {
            color: #6b707b;
            font-weight: 800;
        }

        .valor {
            color: #20232a;
            font-weight: 900;
            text-align: right;
            word-break: break-word;
        }

        .total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin-top: 30px;
            padding: 26px 28px;
            border: 2px solid #8b1e45;
            border-radius: 14px;
            background: linear-gradient(135deg, #fff7fa, #f8edf2);
        }

        .total-label {
            color: #6c707a;
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .total-valor {
            color: #8b1e45;
            font-size: 37px;
            font-weight: 900;
            white-space: nowrap;
        }

        .estado {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 26px;
            padding: 19px 22px;
            border: 2px solid #a7dcbc;
            border-radius: 12px;
            background: #effaf3;
            color: #177448;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: .04em;
        }

        .estado-icono {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #1d9a60;
            color: #fff;
            font-size: 16px;
        }

        .faltante {
            color: #c5223e;
            font-weight: 900;
        }

        .pie {
            margin-top: 46px;
            padding-top: 22px;
            border-top: 2px dashed #d3d6dd;
            text-align: center;
            color: #646a75;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.6;
        }

        .nota {
            margin-top: 16px;
            text-align: center;
            color: #969ba4;
            font-size: 13px;
            line-height: 1.5;
        }

        .marca-prueba {
            margin-top: 18px;
            text-align: center;
            color: #a9adb5;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

    </style>

</head>

<body>

    <div class="comprobante">

        <div class="encabezado">

            <div class="marca">

                <div class="marca-icono">
                    SE
                </div>

                <div>

                    <div class="marca-nombre">
                        SABOR EXPRESS
                    </div>

                    <div class="marca-subtitulo">
                        Comprobantes de pago
                    </div>

                </div>

            </div>

            <div class="estado-cabecera">
                PAGO REGISTRADO
            </div>

        </div>


        <div class="titulo-documento">

            <h1>
                COMPROBANTE DE PAGO
            </h1>

            <p>
                Transferencia bancaria · Banco Unión
            </p>

            <div class="pedido-pill">
                NUMERO DE PEDIDO: {$numeroPedido}
            </div>

        </div>


        <div class="seccion">

            <div class="seccion-titulo">
                Datos de la operación
            </div>

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

            <div class="total-label">
                Monto pagado
            </div>

            <div class="total-valor">
                Bs {$montoFormateado}
            </div>

        </div>


        <div class="estado">

            <span class="estado-icono">
                ✓
            </span>

            PAGO REALIZADO

        </div>


        <div class="pie">

            Sabor Express

            <br>

            Comprobante de pago

        </div>


        <div class="nota">

            Documento generado automáticamente para pruebas
            de reconocimiento OCR.

        </div>


        <div class="marca-prueba">
            Documento sintético de pruebas · No corresponde a una transacción bancaria real
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
            $detalle = trim(implode(PHP_EOL, $output));

            throw new \RuntimeException(
                "No se pudo generar {$nombreArchivo}."
                . ($detalle !== '' ? " Detalle de Chrome: {$detalle}" : '')
            );
        }

        $this->line(
            "  ✓ {$nombreArchivo}"
        );

    }
}