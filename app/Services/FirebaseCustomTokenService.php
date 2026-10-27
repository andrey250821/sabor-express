<?php

namespace App\Services;

use RuntimeException;

class FirebaseCustomTokenService
{
    /**
     * Genera un token personalizado de Firebase para un usuario autenticado
     * por Laravel. Las credenciales privadas permanecen únicamente en el servidor.
     *
     * @param array<string, mixed> $claims
     */
    public function createToken(int $userId, array $claims = []): string
    {
        $credentialsPath = (string) config('services.firebase.credentials');

        if ($credentialsPath === '' || !is_file($credentialsPath) || !is_readable($credentialsPath)) {
            throw new RuntimeException('No se encontró el archivo de credenciales de Firebase.');
        }

        $contents = file_get_contents($credentialsPath);

        if ($contents === false) {
            throw new RuntimeException('No se pudo leer el archivo de credenciales de Firebase.');
        }

        try {
            $credentials = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new RuntimeException('El archivo de credenciales de Firebase no contiene JSON válido.', 0, $exception);
        }

        if (
            !is_array($credentials)
            || empty($credentials['client_email'])
            || empty($credentials['private_key'])
        ) {
            throw new RuntimeException('Al archivo de credenciales de Firebase le faltan campos obligatorios.');
        }

        $now = time();
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $payload = [
            'iss' => $credentials['client_email'],
            'sub' => $credentials['client_email'],
            'aud' => 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit',
            'iat' => $now,
            'exp' => $now + 3000,
            'uid' => 'sabor-express-user-' . $userId,
            'claims' => $claims,
        ];

        $unsignedToken = $this->base64UrlEncode(
            json_encode($header, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
        ) . '.' . $this->base64UrlEncode(
            json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
        );

        $privateKey = openssl_pkey_get_private($credentials['private_key']);

        if ($privateKey === false) {
            throw new RuntimeException('La clave privada de las credenciales de Firebase no es válida.');
        }

        $signatureCreated = openssl_sign(
            $unsignedToken,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        if (!$signatureCreated) {
            throw new RuntimeException('No se pudo firmar el token de autenticación de Firebase.');
        }

        return $unsignedToken . '.' . $this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
