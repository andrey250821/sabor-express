<?php

namespace Tests\Unit\Rules;

use App\Rules\TelefonoBoliviano;
use PHPUnit\Framework\TestCase;

class TelefonoBolivianoTest extends TestCase
{
    public function test_acepta_telefonos_nacionales_e_internacionales(): void
    {
        $rule = new TelefonoBoliviano();

        foreach (['71234567', '+59171234567', '+591 7 123-4567', '2 234 5678'] as $telefono) {
            $normalizado = TelefonoBoliviano::normalizar($telefono);
            $mensaje = null;

            $rule->validate('telefono', $normalizado, function ($error) use (&$mensaje) {
                $mensaje = $error;
            });

            $this->assertNull($mensaje, "El número {$telefono} debería ser válido.");
        }
    }

    public function test_rechaza_letras_numeros_cortos_y_prefijos_incompletos(): void
    {
        $rule = new TelefonoBoliviano();

        foreach (['7123ABCD', '1234567', '+5917123456', '7123456789'] as $telefono) {
            $normalizado = TelefonoBoliviano::normalizar($telefono);
            $mensaje = null;

            $rule->validate('telefono', $normalizado, function ($error) use (&$mensaje) {
                $mensaje = $error;
            });

            $this->assertNotNull($mensaje, "El número {$telefono} debería rechazarse.");
        }
    }

    public function test_normaliza_prefijo_internacional_y_separadores(): void
    {
        $this->assertSame(
            '71234567',
            TelefonoBoliviano::normalizar('+591 7 123-4567')
        );
    }
}
