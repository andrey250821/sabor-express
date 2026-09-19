<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerarComprobanteOcrService
{
    private string $chrome = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

    public function generarDesdePedido(
        Pedido $pedido,
        string $tipo,
        ?float $monto = null,
        ?string $referencia = null,
        ?string $fecha = null
    ): string {
        return $this->generar(
            numeroPedido: $pedido->id,
            cliente: $pedido->user?->name ?? 'Cliente Sabor Express',
            monto: $monto ?? (float) $pedido->total,
            referencia: $referencia ?? '98452217',
            fecha: $fecha ?? now()->format('d/m/Y'),
            tipo: $tipo
        );
    }

    public function generarParaCheckout(
        string $cliente,
        float $monto,
        ?string $referencia = null
    ): array {
        $referencia = $referencia ?? (string) random_int(10000000, 99999999);

        $rutaRelativa = $this->generar(
            numeroPedido: null,
            cliente: $cliente,
            monto: $monto,
            referencia: $referencia,
            fecha: now()->format('d/m/Y'),
            tipo: 'checkout'
        );

        return [
            'ruta' => $rutaRelativa,
            'archivo' => basename($rutaRelativa),
            'referencia' => $referencia,
        ];
    }

    private function generar(
        ?int $numeroPedido,
        string $cliente,
        float $monto,
        string $referencia,
        string $fecha,
        string $tipo
    ): string {
        if (!File::exists($this->chrome)) {
            throw new \RuntimeException(
                'No se encontró Google Chrome en: ' . $this->chrome
            );
        }

        $htmlDirectory = storage_path('app/ocr-test/html');
        $outputDirectory = storage_path('app/public/comprobantes_test');

        File::ensureDirectoryExists($htmlDirectory);
        File::ensureDirectoryExists($outputDirectory);

        $numeroTexto = $numeroPedido !== null
            ? (string) $numeroPedido
            : 'POR CONFIRMAR';

        $nombreArchivo = $numeroPedido !== null
            ? "pedido_{$numeroPedido}_comprobante_{$tipo}.png"
            : 'comprobante_prueba_checkout_' . now()->format('Ymd_His') . '_' . Str::lower(Str::random(6)) . '.png';

        $htmlNombre = pathinfo($nombreArchivo, PATHINFO_FILENAME) . '.html';
        $htmlPath = $htmlDirectory . DIRECTORY_SEPARATOR . $htmlNombre;
        $outputPath = $outputDirectory . DIRECTORY_SEPARATOR . $nombreArchivo;

        $clienteHtml = htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8');
        $referenciaHtml = $referencia !== ''
            ? htmlspecialchars($referencia, ENT_QUOTES, 'UTF-8')
            : '<span class="faltante">NO PRESENTADA</span>';

        $montoFormateado = number_format($monto, 2);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sabor Express - Comprobante</title>
    <style>
        * { box-sizing: border-box; }
        html, body {
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
            gap: 30px;
        }
        .fila:last-child { border-bottom: none; }
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
        .aviso-prueba {
            margin-top: 20px;
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background: #fff3cd;
            color: #664d03;
            border: 1px solid #ffecb5;
            font-size: 17px;
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
            <div class="logo">SABOR EXPRESS</div>
            <div class="subtitulo">COMPROBANTE DE PAGO</div>
            <div class="numero-pedido">Pedido {$numeroTexto}</div>
        </div>

        <div class="seccion">
            <div class="fila">
                <span class="etiqueta">Pedido</span>
                <span class="valor">{$numeroTexto}</span>
            </div>

            <div class="fila">
                <span class="etiqueta">Cliente</span>
                <span class="valor">{$clienteHtml}</span>
            </div>

            <div class="fila">
                <span class="etiqueta">Fecha</span>
                <span class="valor">{$fecha}</span>
            </div>

            <div class="fila">
                <span class="etiqueta">Banco</span>
                <span class="valor">Banco Unión</span>
            </div>

            <div class="fila">
                <span class="etiqueta">N° de referencia</span>
                <span class="valor">{$referenciaHtml}</span>
            </div>
        </div>

        <div class="total">
            <span>Monto pagado</span>
            <span>Bs {$montoFormateado}</span>
        </div>

        <div class="estado">PAGO REALIZADO</div>

        <div class="aviso-prueba">
            COMPROBANTE DE PRUEBA PARA RECONOCIMIENTO OCR
        </div>

        <div class="pie">
            Sabor Express
            <br>
            Comprobante de pago
        </div>

        <div class="nota">
            Documento generado automáticamente para pruebas de reconocimiento OCR.
        </div>
    </div>
</body>
</html>
HTML;

        File::put($htmlPath, $html);

        if (File::exists($outputPath)) {
            File::delete($outputPath);
        }

        $htmlUri = 'file:///' . str_replace('\\', '/', $htmlPath);

        $command = '"' . $this->chrome . '"'
            . ' --headless=new'
            . ' --disable-gpu'
            . ' --no-sandbox'
            . ' --hide-scrollbars'
            . ' --window-size=900,1200'
            . ' --screenshot="'
            . $outputPath
            . '" "'
            . $htmlUri
            . '"';

        $output = [];
        $resultCode = 0;

        exec($command . ' 2>&1', $output, $resultCode);

        if ($resultCode !== 0 || !File::exists($outputPath)) {
            $detalle = !empty($output)
                ? implode(' | ', $output)
                : 'Chrome no generó la imagen.';

            throw new \RuntimeException(
                'No se pudo generar el comprobante de prueba. ' . $detalle
            );
        }

        return 'comprobantes_test/' . $nombreArchivo;
    }
}
