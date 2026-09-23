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
         * El número de pedido es un dato crítico. La imagen de prueba
         * lo muestra en grande y con una etiqueta propia para que OCR
         * pueda reconocer números de dos o más dígitos (11, 12, 13...).
         *
         * Probamos primero las etiquetas más específicas para evitar
         * confundir el ID con otros números del comprobante.
         */
        $patronesPrioritarios = [
            '/\bNUMERO\s+DE\s+PEDIDO\s*[:\-]?\s*([0-9]{1,8})\b/iu',
            '/\bN(?:UMERO|ÚMERO)\s+DE\s+PEDIDO\s*[:\-]?\s*([0-9]{1,8})\b/iu',
            '/\bID\s+DEL\s+PEDIDO\s*[:\-]?\s*([0-9]{1,8})\b/iu',
            '/\bPEDIDO\s+ID\s*[:\-]?\s*([0-9]{1,8})\b/iu',
            '/\bPEDIDO\b\s*(?:N\s*(?:[°º?oO0])?\s*|Nro\.?\s*|No\.?\s*|#\s*)?[#:\-]?\s*([0-9]{1,8})\b/iu',
            '/\bPEDIDO\b\s*[:\-]?\s*#?\s*([0-9]{1,8})\b/iu',
        ];

        foreach ($patronesPrioritarios as $patron) {
            if (preg_match($patron, $texto, $matches)) {
                return (int) $matches[1];
            }
        }

        /*
         * Fallback por líneas. OCR puede separar la etiqueta del número:
         *
         * NUMERO DE PEDIDO
         * 11
         *
         * o:
         *
         * Pedido
         * 11
         *
         * También aceptamos una línea que contenga únicamente el número.
         */
        $lineas = preg_split('/\R+/u', $texto) ?: [];

        foreach ($lineas as $indice => $linea) {
            $lineaNormalizada = trim($linea);

            if (
                preg_match('/\b(?:NUMERO|N\W*UMERO)\s+DE\s+PEDIDO\b/iu', $lineaNormalizada)
                || preg_match('/\bID\s+DEL\s+PEDIDO\b/iu', $lineaNormalizada)
                || preg_match('/\bPEDIDO\b/iu', $lineaNormalizada)
            ) {
                $siguiente = trim($lineas[$indice + 1] ?? '');

                if (preg_match('/^(\d{1,8})$/u', $siguiente, $matches)) {
                    return (int) $matches[1];
                }

                if ($siguiente !== '' && preg_match('/(?:^|\D)(\d{1,8})(?:\D|$)/u', $siguiente, $matches)) {
                    return (int) $matches[1];
                }

                /*
                 * Si la propia línea contiene un número, conservar todos
                 * los dígitos contiguos en vez de quedarnos con un solo
                 * dígito por una coincidencia demasiado corta.
                 */
                if (preg_match('/(?:#|N\s*[°º?oO0]?|No\.?|Nro\.?)\s*([0-9]{1,8})/iu', $lineaNormalizada, $matches)) {
                    return (int) $matches[1];
                }

                if (preg_match('/\bPEDIDO\b[^0-9]{0,30}([0-9]{1,8})\b/iu', $lineaNormalizada, $matches)) {
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
