<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfilePhotoController extends Controller
{
    /**
     * Servir la foto de perfil almacenada localmente.
     *
     * Esto evita depender de public/storage como enlace simbólico:
     * Laravel entrega directamente el archivo guardado en el disco public.
     */
    public function show(User $user): BinaryFileResponse
    {
        $foto = $user->foto_perfil;

        // Las fotos externas (por ejemplo Google) no pasan por esta ruta.
        if (!$foto || Str::startsWith($foto, ['http://', 'https://'])) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($foto)) {
            abort(404);
        }

        $ruta = realpath($disk->path($foto));
        $raiz = realpath($disk->path(''));

        // Evitar que una ruta manipulada pueda salir del directorio público.
        if (
            !$ruta ||
            !$raiz ||
            !Str::startsWith($ruta, $raiz . DIRECTORY_SEPARATOR)
        ) {
            abort(404);
        }

        return response()->file($ruta, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
