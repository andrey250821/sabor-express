<?php

namespace Tests\Unit;

use App\Services\OcrComprobanteService;
use PHPUnit\Framework\TestCase;

class OcrComprobanteServiceTest extends TestCase
{
    public function test_detects_pedido_with_common_ocr_variants(): void
    {
        $ocr = new OcrComprobanteService();

        $casos = [
            'Pedido N° 15' => 15,
            'Pedido Nº 16' => 16,
            'Pedido N? 17' => 17,
            'Pedido No 18' => 18,
            'Pedido Nro. 19' => 19,
            'Pedido #20' => 20,
            'Pedido 21' => 21,
        ];

        foreach ($casos as $texto => $esperado) {
            $datos = $ocr->analizar($texto);

            $this->assertSame(
                $esperado,
                $datos['pedido'],
                "No se detectó correctamente el pedido en: {$texto}"
            );
        }
    }

    public function test_detects_pedido_when_ocr_splits_number_into_next_line(): void
    {
        $ocr = new OcrComprobanteService();

        $texto = "SABOR EXPRESS\nPedido N?\n22\nMonto pagado Bs 15.00";

        $datos = $ocr->analizar($texto);

        $this->assertSame(22, $datos['pedido']);
    }

    public function test_keeps_other_ocr_fields_unchanged(): void
    {
        $ocr = new OcrComprobanteService();

        $texto = <<<OCR
SABOR EXPRESS
Pedido N? 23
Monto pagado Bs 15.00
N° de referencia 98450004
Fecha 21/09/2026
Banco Banco Unión
OCR;

        $datos = $ocr->analizar($texto);

        $this->assertSame(23, $datos['pedido']);
        $this->assertSame(15.0, $datos['monto']);
        $this->assertSame('98450004', $datos['referencia']);
        $this->assertSame('21/09/2026', $datos['fecha']);
        $this->assertSame('Banco Banco Unión', $datos['banco']);
    }
}
