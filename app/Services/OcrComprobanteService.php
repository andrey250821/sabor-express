<?php

namespace App\Services;

class OcrComprobanteService
{
    /**
     * Analiza el texto obtenido mediante OCR.
     */
    public function analizar(string $texto): array
    {
        $datos = [
            'pedido' => $this->extraerPedido($texto),
            'monto' => $this->extraerMonto($texto),
            'referencia' => $this->extraerReferencia($texto),
            'fecha' => $this->extraerFecha($texto),
            'banco' => $this->extraerBanco($texto),
        ];

        $datos['completo'] = $this->estaCompleto($datos);

        return $datos;
    }

    /**
     * Determina si están presentes los datos mínimos necesarios.
     *
     * El número de pedido es opcional porque un comprobante bancario
     * real puede no incluir el ID interno del pedido. Si aparece,
     * ValidarComprobantePagoService lo compara con el pedido real.
     */
    private function estaCompleto(array $datos): bool
    {
        return $datos['monto'] !== null
            && $datos['referencia'] !== null
            && $datos['fecha'] !== null
            && $datos['banco'] !== null;
    }

    /**
     * Extrae el número de pedido.
     */
    private function extraerPedido(string $texto): ?int
    {
        if (preg_match(
            '/Pedido\s*(?:N[°º?]?|\#)?\s*(\d+)/iu',
            $texto,
            $matches
        )) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Extrae el monto pagado.
     */
    private function extraerMonto(string $texto): ?float
    {
        if (preg_match(
            '/Monto\s+pagado\s+Bs\.?\s*([0-9]+(?:[.,][0-9]{1,2})?)/iu',
            $texto,
            $matches
        )) {
            $monto = str_replace(',', '.', $matches[1]);

            return (float) $monto;
        }

        return null;
    }

    /**
     * Extrae la referencia bancaria.
     */
    private function extraerReferencia(string $texto): ?string
    {
        if (preg_match(
            '/(?:N[°º?]?\s*de\s*)?referencia\s*:?\s*([0-9]{6,20})/iu',
            $texto,
            $matches
        )) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extrae la fecha.
     */
    private function extraerFecha(string $texto): ?string
    {
        if (preg_match(
            '/Fecha\s*:?\s*(\d{2}\/\d{2}\/\d{4})/iu',
            $texto,
            $matches
        )) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extrae el banco.
     */
    private function extraerBanco(string $texto): ?string
    {
        if (preg_match(
            '/Banco\s+(.+)/iu',
            $texto,
            $matches
        )) {
            return trim($matches[1]);
        }

        return null;
    }
}
