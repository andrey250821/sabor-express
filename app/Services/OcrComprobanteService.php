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
     */
    private function estaCompleto(array $datos): bool
    {
        return $datos['pedido'] !== null
            && $datos['monto'] !== null
            && $datos['referencia'] !== null
            && $datos['fecha'] !== null
            && $datos['banco'] !== null;
    }

    /**
     * Extrae el número de pedido.
     *
     * Tesseract puede reconocer la etiqueta de distintas formas:
     * "Pedido N° 15", "Pedido Nº 15", "Pedido N? 15",
     * "Pedido No 15", "Pedido Nro. 15", "Pedido #15"
     * o incluso dejar la etiqueta y el número en líneas separadas.
     */
    private function extraerPedido(string $texto): ?int
    {
        /*
         * 1. Intentar primero las variantes normales en todo el texto.
         *
         * Se acepta:
         * - N°, Nº, N?, No, Nro, Nro.
         * - #, :, -
         * - "Pedido 15" sin prefijo
         */
        $patron = '/\bPedido\b\s*(?:N\s*(?:[°º?oO0])?\s*|Nro\.?\s*|#\s*)?[#:\-]?\s*(\d{1,8})\b/iu';

        if (preg_match($patron, $texto, $matches)) {
            return (int) $matches[1];
        }

        /*
         * 2. Fallback por líneas.
         *
         * Si OCR separa la etiqueta del número:
         *
         * Pedido N?
         * 15
         *
         * o:
         *
         * Pedido
         * 15
         *
         * buscamos un número limpio en la línea siguiente.
         */
        $lineas = preg_split('/\R+/u', $texto) ?: [];

        foreach ($lineas as $indice => $linea) {
            if (stripos($linea, 'pedido') === false) {
                continue;
            }

            $siguiente = $lineas[$indice + 1] ?? '';

            if (preg_match('/^\s*(\d{1,8})\s*$/u', trim($siguiente), $matches)) {
                return (int) $matches[1];
            }

            /*
             * También intentamos combinar la línea "Pedido ..." con
             * la siguiente cuando esta contiene el número junto a un
             * pequeño residuo de OCR.
             */
            if ($siguiente !== '') {
                $contexto = trim($linea . ' ' . $siguiente);

                if (preg_match(
                    '/\bPedido\b[^\d\r\n]{0,25}(\d{1,8})\b/iu',
                    $contexto,
                    $matches
                )) {
                    return (int) $matches[1];
                }
            }
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
