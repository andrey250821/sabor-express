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
         * El comprobante generado para pruebas muestra una única zona:
         *
         * NUMERO DE PEDIDO
         * # 16
         *
         * El símbolo # puede ser interpretado por Tesseract como "4".
         * Por eso la extracción prioriza la etiqueta "NUMERO DE PEDIDO"
         * y permite que el número aparezca con #, separado por espacios
         * o en la línea siguiente.
         */

        $patronesPrioritarios = [
            // Ej.: "NUMERO DE PEDIDO # 16" o "NUMERO DE PEDIDO 16"
            '/\bNUMERO\s+DE\s+PEDIDO\b\s*[:\-]?\s*#?\s*(\d{1,8})\b/iu',

            // Ej.: OCR en dos líneas: "NUMERO DE PEDIDO" + "# 16"
            '/\bNUMERO\s+DE\s+PEDIDO\b\s*\R\s*#?\s*(\d{1,8})\b/iu',

            // Ej.: OCR confunde # con 4 y conserva el espacio: "4 16"
            // Se aplica únicamente después de la etiqueta completa.
            '/\bNUMERO\s+DE\s+PEDIDO\b\s*\R\s*4\s+(\d{1,8})\b/iu',

            // Otras formas conocidas de la etiqueta.
            '/\bID\s+DEL\s+PEDIDO\b\s*[:\-]?\s*#?\s*(\d{1,8})\b/iu',
            '/\bPEDIDO\s+ID\b\s*[:\-]?\s*#?\s*(\d{1,8})\b/iu',

            // Formato convencional "Pedido # 16", "Pedido N° 16", etc.
            '/\bPEDIDO\b\s*(?:N\s*[°º?oO0]\s*|Nro\.?\s*|No\.?\s*|#\s*)[:\-]?\s*(\d{1,8})\b/iu',
            '/\bPEDIDO\b\s*[:\-]?\s*#\s*(\d{1,8})\b/iu',
            '/\bPEDIDO\b\s*[:\-]?\s*(\d{1,8})\b/iu',
        ];

        foreach ($patronesPrioritarios as $patron) {
            if (preg_match($patron, $texto, $matches)) {
                return (int) $matches[1];
            }
        }

        /*
         * Fallback por líneas.
         *
         * Si OCR separa la etiqueta y el identificador:
         *
         * NUMERO DE PEDIDO
         * # 16
         *
         * también aceptamos una línea que contenga "# 16".
         */
        $lineas = preg_split('/\R+/u', $texto) ?: [];

        foreach ($lineas as $indice => $linea) {
            $lineaNormalizada = trim($linea);

            if (!preg_match('/\b(?:NUMERO\s+DE\s+PEDIDO|N\W*UMERO\s+DE\s+PEDIDO|ID\s+DEL\s+PEDIDO|PEDIDO\s+ID|PEDIDO)\b/iu', $lineaNormalizada)) {
                continue;
            }

            $siguiente = trim($lineas[$indice + 1] ?? '');

            if (preg_match('/^#\s*(\d{1,8})$/u', $siguiente, $matches)) {
                return (int) $matches[1];
            }

            /*
             * Caso específico de OCR donde "#" se convirtió en "4":
             * "# 16" -> "4 16".
             */
            if (preg_match('/^4\s+(\d{1,8})$/u', $siguiente, $matches)) {
                return (int) $matches[1];
            }

            if (preg_match('/^(\d{1,8})$/u', $siguiente, $matches)) {
                return (int) $matches[1];
            }

            if (preg_match('/(?:#|N\s*[°º?oO0]|No\.?|Nro\.?)\s*(\d{1,8})/iu', $lineaNormalizada, $matches)) {
                return (int) $matches[1];
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
