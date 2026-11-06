<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelefonoBoliviano implements ValidationRule
{
    public static function normalizar(mixed $telefono): mixed
    {
        if ($telefono === null) return null;
        if (!is_string($telefono) && !is_numeric($telefono)) return $telefono;

        $normalizado = preg_replace('/[\s().-]+/u', '', trim((string) $telefono));

        if ($normalizado === null || $normalizado === '') return null;

        if (str_starts_with($normalizado, '+591')) {
            $normalizado = substr($normalizado, 4);
        } elseif (str_starts_with($normalizado, '591') && strlen($normalizado) === 11) {
            $normalizado = substr($normalizado, 3);
        }

        return $normalizado;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') return;

        if ((!is_string($value) && !is_numeric($value))
            || !preg_match('/\A[2-7][0-9]{7}\z/D', (string) $value)) {
            $fail('Ingresa un teléfono boliviano válido de 8 dígitos. Puedes incluir el prefijo +591.');
        }
    }
}
