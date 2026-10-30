<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfilePhotoController extends Controller
{
    /**
     * Servir la foto de perfil almacenada localmente o descargarla
     * desde Google y guardar una copia en el almacenamiento de Sabor Express.
     */
    public function show(User $user)
    {
        $foto = $user->foto_perfil;

        if (!$foto) {
            abort(404);
        }

        $disk = Storage::disk('public');

        // Las fotos de Google se descargan la primera vez que se solicitan.
        if (Str::startsWith($foto, ['http://', 'https://'])) {
            $host = strtolower((string) parse_url($foto, PHP_URL_HOST));
            $scheme = strtolower((string) parse_url($foto, PHP_URL_SCHEME));

            // Aceptar únicamente direcciones HTTPS alojadas en Google.
            if (
                !filter_var($foto, FILTER_VALIDATE_URL) ||
                $scheme !== 'https' ||
                !(
                    $host === 'googleusercontent.com' ||
                    Str::endsWith($host, '.googleusercontent.com')
                )
            ) {
                abort(404);
            }

            try {
                $respuesta = Http::timeout(15)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                    ])
                    ->get($foto);
            } catch (\Throwable $e) {
                abort(404);
            }

            $tipo = strtolower(
                trim(explode(';', $respuesta->header('Content-Type', ''))[0])
            );

            $extensiones = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
            ];

            $extension = $extensiones[$tipo] ?? null;
            $contenido = $respuesta->body();

            if (
                !$respuesta->successful() ||
                !$extension ||
                $contenido === '' ||
                strlen($contenido) > 5 * 1024 * 1024
            ) {
                abort(404);
            }

            // Guardar una copia local para que las siguientes visitas
            // ya no dependan del servidor de imágenes de Google.
            $disk->makeDirectory('perfiles/google');

            $rutaLocal = 'perfiles/google/'
                . $user->getKey()
                . '.'
                . $extension;

            if ($disk->put($rutaLocal, $contenido)) {
                $user->forceFill([
                    'foto_perfil' => $rutaLocal,
                ])->save();
            }

            return response($contenido, 200, [
                'Content-Type' => $tipo,
                'Cache-Control' => 'public, max-age=3600',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        // Mantener el funcionamiento de las fotos subidas manualmente.
        if (!$disk->exists($foto)) {
            abort(404);
        }

        $ruta = realpath($disk->path($foto));
        $raiz = realpath($disk->path(''));

        // Evitar que una ruta manipulada pueda salir del almacenamiento público.
        if (
            !$ruta ||
            !$raiz ||
            !is_file($ruta) ||
            !Str::startsWith($ruta, $raiz . DIRECTORY_SEPARATOR)
        ) {
            abort(404);
        }

        return response()->file($ruta, [
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
