<?php

namespace App\Services;

use App\Models\ComprobantePago;
use App\Models\Pedido;
use Illuminate\Support\Facades\Process;

class ValidarComprobantePagoService
{
    public function __construct(
        private OcrComprobanteService $ocr
    ) {
    }

    public function validar(Pedido $pedido, string $rutaImagen): array
    {
        $datosVacios = [
            'pedido' => null,
            'monto' => null,
            'referencia' => null,
            'fecha' => null,
            'banco' => null,
            'completo' => false,
        ];

        $tesseract = config('services.tesseract.path');

        if (!$tesseract || !file_exists($tesseract)) {
            return [
                'estado' => 'en_revision',
                'motivo_revision' => 'No fue posible ejecutar la validación OCR porque Tesseract no está disponible.',
                'datos' => $datosVacios,
            ];
        }

        if (!file_exists($rutaImagen)) {
            return [
                'estado' => 'en_revision',
                'motivo_revision' => 'No fue posible encontrar la imagen del comprobante para realizar la validación.',
                'datos' => $datosVacios,
            ];
        }

        $resultado = Process::timeout(30)->run([
            $tesseract,
            $rutaImagen,
            'stdout',
            '-l',
            'spa',
        ]);

        if ($resultado->failed()) {
            return [
                'estado' => 'en_revision',
                'motivo_revision' => 'La lectura OCR presentó un error y el comprobante requiere revisión manual.',
                'datos' => $datosVacios,
            ];
        }

        $texto = trim($resultado->output());

        if ($texto === '') {
            return [
                'estado' => 'en_revision',
                'motivo_revision' => 'El OCR no logró reconocer texto suficiente del comprobante.',
                'datos' => $datosVacios,
            ];
        }

        $datos = $this->ocr->analizar($texto);
        $motivos = [];

        if (!$datos['completo']) {
            $faltantes = [];

            if ($datos['monto'] === null) {
                $faltantes[] = 'monto';
            }

            if ($datos['referencia'] === null) {
                $faltantes[] = 'referencia bancaria';
            }

            if ($datos['fecha'] === null) {
                $faltantes[] = 'fecha';
            }

            if ($datos['banco'] === null) {
                $faltantes[] = 'banco';
            }

            $motivos[] = 'Faltan datos obligatorios: ' . implode(', ', $faltantes) . '.';
        }

        if ($datos['pedido'] !== null && $datos['pedido'] !== $pedido->id) {
            $motivos[] = 'El número de pedido del comprobante no coincide con el pedido.';
        }

        if (
            $datos['monto'] !== null
            && abs((float) $pedido->total - (float) $datos['monto']) > 0.01
        ) {
            $motivos[] = 'El monto del comprobante no coincide con el total real del pedido.';
        }

        if ($datos['referencia'] !== null) {
            $duplicada = ComprobantePago::where(
                'referencia_bancaria',
                $datos['referencia']
            )->exists();

            if ($duplicada) {
                $motivos[] = 'La referencia bancaria ya fue utilizada en otro comprobante.';
            }
        }

        if ($motivos !== []) {
            return [
                'estado' => 'en_revision',
                'motivo_revision' => implode(' ', $motivos),
                'datos' => $datos,
            ];
        }

        return [
            'estado' => 'aprobado',
            'motivo_revision' => null,
            'datos' => $datos,
        ];
    }
}
