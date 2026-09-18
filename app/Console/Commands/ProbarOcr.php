<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use App\Services\OcrComprobanteService;
use App\Models\Pedido;
use App\Models\ComprobantePago;

class ProbarOcr extends Command
{
    protected $signature = 'ocr:probar
                            {--pedido=1 : ID del pedido}
                            {--tipo=correcto : Tipo de comprobante}';

    protected $description = 'Prueba el reconocimiento OCR de un comprobante';

    public function handle(OcrComprobanteService $ocr): int
    {
        $pedidoId = $this->option('pedido');
        $tipo = $this->option('tipo');

        $archivo = "pedido_{$pedidoId}_comprobante_{$tipo}.png";

        $ruta = storage_path(
            "app/public/comprobantes_test/{$archivo}"
        );

        $this->info("Comprobante: {$archivo}");
        $this->info("Ruta: {$ruta}");
        $this->newLine();

        // Verificar que exista la imagen
        if (!file_exists($ruta)) {
            $this->error("No se encontró la imagen.");
            $this->line($ruta);

            return self::FAILURE;
        }

        // Ruta de Tesseract
        $tesseract = 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe';

        if (!file_exists($tesseract)) {
            $this->error("No se encontró Tesseract.");

            return self::FAILURE;
        }

        // Ejecutar OCR
        $this->info('Ejecutando Tesseract...');
        $this->newLine();

        $resultado = Process::run([
            $tesseract,
            $ruta,
            'stdout',
            '-l',
            'spa',
        ]);

        if ($resultado->failed()) {
            $this->error('Tesseract devolvió un error.');
            $this->error($resultado->errorOutput());

            return self::FAILURE;
        }

        $texto = trim($resultado->output());

        if ($texto === '') {
            $this->warn('Tesseract no reconoció ningún texto.');

            return self::FAILURE;
        }

        // Mostrar texto OCR
        $this->info('========== TEXTO OCR ==========');
        $this->line($texto);
        $this->info('===============================');

        // Analizar datos extraídos
        $datos = $ocr->analizar($texto);

        // Buscar pedido real
        $pedido = Pedido::find($pedidoId);

        if (!$pedido) {
            $this->error(
                "El pedido #{$pedidoId} no existe en la base de datos."
            );

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('========== VALIDACIÓN ==========');

        /*
        |--------------------------------------------------------------------------
        | 1. VALIDAR COMPROBANTE INCOMPLETO
        |--------------------------------------------------------------------------
        */

        if (!$datos['completo']) {
            $this->warn('⚠️ COMPROBANTE INCOMPLETO');
            $this->warn('Faltan uno o más datos obligatorios.');

            $this->line(
                'Pedido:     ' . ($datos['pedido'] ?? 'NO DETECTADO')
            );

            $this->line(
                'Monto:      ' .
                ($datos['monto'] !== null
                    ? 'Bs ' . number_format($datos['monto'], 2)
                    : 'NO DETECTADO')
            );

            $this->line(
                'Referencia: ' .
                ($datos['referencia'] ?? 'NO DETECTADA')
            );

            $this->line(
                'Fecha:      ' .
                ($datos['fecha'] ?? 'NO DETECTADA')
            );

            $this->line(
                'Banco:      ' .
                ($datos['banco'] ?? 'NO DETECTADO')
            );

            $this->info('================================');

            return self::SUCCESS;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. VALIDAR REFERENCIA DUPLICADA
        |--------------------------------------------------------------------------
        */

        $referencia = $datos['referencia'];

        $referenciaDuplicada = ComprobantePago::where(
            'referencia_bancaria',
            $referencia
        )->exists();

        if ($referenciaDuplicada) {
            $this->error('❌ REFERENCIA DUPLICADA');

            $this->warn(
                "La referencia bancaria {$referencia} ya fue utilizada."
            );

            $this->line(
                'Referencia OCR: ' . $referencia
            );

            $this->info('================================');

            return self::SUCCESS;
        }

        /*
        |--------------------------------------------------------------------------
        | 3. VALIDAR MONTO
        |--------------------------------------------------------------------------
        */

        $montoEsperado = (float) $pedido->total;
        $montoLeido = $datos['monto'];

        $this->line(
            'Total del pedido: Bs ' .
            number_format($montoEsperado, 2)
        );

        $this->line(
            'Monto OCR:        ' .
            ($montoLeido !== null
                ? 'Bs ' . number_format($montoLeido, 2)
                : 'NO DETECTADO')
        );

        if ($montoLeido === null) {
            $this->warn('⚠️ No se pudo detectar el monto.');

            $this->info('================================');

            return self::SUCCESS;
        }

        if (abs($montoEsperado - $montoLeido) > 0.01) {
            $this->error('❌ MONTO INCORRECTO');
        } else {
            $this->info('✅ MONTO CORRECTO');
        }

        $this->info('================================');

        return self::SUCCESS;
    }
}
