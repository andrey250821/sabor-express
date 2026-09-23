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
         * Tesseract puede devolver variantes como:
         * - Pedido N° 16
         * - Pedido Nº 16
         * - Pedido N? 16
         * - Pedido No 16
         * - Pedido # 16
         * - NUMERO DE PEDIDO + 16 en la línea siguiente
         *
         * Primero normalizamos espacios invisibles y saltos de línea.
         */
        $textoNormalizado = str_replace(["\\u{00A0}", "\\u{200B}", "\\r"], [' ', '', ''], $texto);
        $textoNormalizado = preg_replace('/[ \\t]+/u', ' ', $textoNormalizado) ?? $textoNormalizado;

        /*
         * Patrones directos. El número debe aparecer inmediatamente
         * después de la etiqueta o de una identificación de pedido.
         */
        $patrones = [
            '/\\bNUMERO\\s+DE\\s+PEDIDO\\b\\s*[:\\-#]?\\s*(\\d{1,8})\\b/iu',
            '/\\bNUMERO\\s+DE\\s+PEDIDO\\b[^\\d\\r\\n]{0,20}(\\d{1,8})\\b/iu',
            '/\\bID\\s+DEL\\s+PEDIDO\\b\\s*[:\\-#]?\\s*(\\d{1,8})\\b/iu',
            '/\\bPEDIDO\\s+ID\\b\\s*[:\\-#]?\\s*(\\d{1,8})\\b/iu',
            '/\\bPEDIDO\\b\\s*(?:N\\s*[°º?oO0]\\s*|Nro\\.?\\s*|No\\.?\\s*|#\\s*)?[:\\-]?\\s*(\\d{1,8})\\b/iu',
            '/\\bPEDIDO\\b[^\\d\\r\\n]{0,20}(\\d{1,8})\\b/iu',
        ];

        foreach ($patrones as $patron) {
            if (preg_match($patron, $textoNormalizado, $matches)) {
                return (int) $matches[1];
            }
        }

        /*
         * Fallback por líneas.
         *
         * Caso típico:
         *   NUMERO DE PEDIDO
         *   16
         *
         * También acepta:
         *   Pedido
         *   # 16
         *
         * o pequeñas alteraciones producidas por OCR.
         */
        $lineas = preg_split('/\\R+/u', $texto) ?: [];

        foreach ($lineas as $indice => $linea) {
            $lineaNormalizada = trim(
                preg_replace('/[ \\t]+/u', ' ', str_replace("\\u{00A0}", ' ', $linea)) ?? $linea
            );

            $lineaMinusculas = mb_strtolower($lineaNormalizada, 'UTF-8');

            $esEtiquetaPedido =
                str_contains($lineaMinusculas, 'pedido')
                || str_contains($lineaMinusculas, 'numero de pedido')
                || str_contains($lineaMinusculas, 'numero  de pedido')
                || str_contains($lineaMinusculas, 'id del pedido')
                || str_contains($lineaMinusculas, 'pedido id');

            if (!$esEtiquetaPedido) {
                continue;
            }

            /*
             * Si la misma línea contiene un número, intentamos usarlo.
             * No tomamos fechas largas ni montos con decimales.
             */
            if (preg_match('/(?:#|N\\s*[°º?oO0]|No\\.?|Nro\\.?)?\\s*(\\d{1,8})(?![.,]\\d)/iu', $lineaNormalizada, $matches)) {
                return (int) $matches[1];
            }

            /*
             * Revisar hasta las siguientes 2 líneas. Esto cubre cuando
             * Tesseract separa la etiqueta y el número.
             */
            for ($siguienteIndice = $indice + 1; $siguienteIndice <= $indice + 2; $siguienteIndice++) {
                $siguiente = trim(
                    preg_replace('/[ \\t]+/u', ' ', str_replace("\\u{00A0}", ' ', $lineas[$siguienteIndice] ?? '')) ?? ''
                );

                /*
                 * Línea simple: 16
                 */
                if (preg_match('/^(\\d{1,8})$/u', $siguiente, $matches)) {
                    return (int) $matches[1];
                }

                /*
                 * Línea: # 16
                 */
                if (preg_match('/^#?\\s*(\\d{1,8})$/u', $siguiente, $matches)) {
                    return (int) $matches[1];
                }

                /*
                 * Línea: 4 16, una confusión frecuente al leer '# 16'.
                 * Aceptamos solo si termina en un único número válido.
                 */
                if (preg_match('/^4\\s+(\\d{1,8})$/u', $siguiente, $matches)) {
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
