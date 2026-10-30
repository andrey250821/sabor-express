<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfilePhotoController extends Controller
{
    /**
     * Servir una foto local o mostrar la foto de Google mediante Laravel.
     *
     * Las fotos de Google se guardan temporalmente en el disco público
     * para evitar depender de la carga directa desde el navegador.
     * La caché no modifica ningún dato de la base de datos.
     */
    public function show(User $user)
    {
        $foto = $user->foto_perfil;

        if (!$foto) {
            abort(404);
        }

        $disk = Storage::disk('public');

        // Las fotos de Google se descargan y almacenan como caché local.
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

            $extensiones = [
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
            ];

            $prefijoCache = 'perfiles/google/' . $user->getKey();
            $fotoEnCache = null;

            // Buscar una copia previamente descargada.
            foreach (array_keys($extensiones) as $extension) {
                $candidata = $prefijoCache . '.' . $extension;

                if ($disk->exists($candidata)) {
                    $fotoEnCache = $candidata;
                    break;
                }
            }

            // Reutilizar la copia durante 24 horas.
            if (
                $fotoEnCache &&
                (time() - $disk->lastModified($fotoEnCache)) < 86400
            ) {
                return response()->file($disk->path($fotoEnCache), [
                    'Cache-Control' => 'public, max-age=3600',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }

            try {
                $respuesta = Http::timeout(15)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                    ])
                    ->get($foto);
            } catch (\Throwable $e) {
                // Si Google no responde, conservar la última copia disponible.
                if ($fotoEnCache) {
                    return response()->file($disk->path($fotoEnCache), [
                        'Cache-Control' => 'public, max-age=3600',
                        'X-Content-Type-Options' => 'nosniff',
                    ]);
                }

                abort(404);
            }

            $tipo = strtolower(
                trim(explode(';', $respuesta->header('Content-Type', ''))[0])
            );

            $extension = array_search($tipo, $extensiones, true);
            $contenido = $respuesta->body();

            if (
                !$respuesta->successful() ||
                !$extension ||
                $contenido === '' ||
                strlen($contenido) > 5 * 1024 * 1024
            ) {
                if ($fotoEnCache) {
                    return response()->file($disk->path($fotoEnCache), [
                        'Cache-Control' => 'public, max-age=3600',
                        'X-Content-Type-Options' => 'nosniff',
                    ]);
                }

                abort(404);
            }

            // Guardar la imagen en storage sin modificar la base de datos.
            $disk->makeDirectory('perfiles/google');

            $rutaLocal = $prefijoCache . '.' . $extension;

            foreach (array_keys($extensiones) as $extensionAnterior) {
                $rutaAnterior = $prefijoCache . '.' . $extensionAnterior;

                if ($rutaAnterior !== $rutaLocal && $disk->exists($rutaAnterior)) {
                    $disk->delete($rutaAnterior);
                }
            }

            $disk->put($rutaLocal, $contenido);

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
