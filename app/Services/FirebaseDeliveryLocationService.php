<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FirebaseDeliveryLocationService
{
    /**
     * Eliminar la ubicación en tiempo real de un pedido.
     *
     * Firebase Realtime Database mantiene esta información
     * separada de MySQL, por lo que debe limpiarse explícitamente.
     */
    public function eliminarPorPedido(int $pedidoId): bool
    {
        $databaseUrl = rtrim(
            (string) config('services.firebase.database_url'),
            '/'
        );

        if ($databaseUrl === '') {
            Log::warning(
                'Firebase Delivery: no se configuró FIREBASE_DATABASE_URL.',
                ['pedido_id' => $pedidoId]
            );

            return false;
        }

        $url = $databaseUrl
            . '/delivery_locations/'
            . $pedidoId
            . '.json';

        try {
            $respuesta = Http::timeout(5)
                ->retry(3, 200)
                ->delete($url);

            if ($respuesta->successful()) {
                Log::info(
                    'Firebase Delivery: ubicación eliminada correctamente.',
                    ['pedido_id' => $pedidoId]
                );

                return true;
            }

            Log::warning(
                'Firebase Delivery: no se pudo eliminar la ubicación.',
                [
                    'pedido_id' => $pedidoId,
                    'status' => $respuesta->status(),
                ]
            );

            return false;
        } catch (Throwable $e) {
            Log::error(
                'Firebase Delivery: error al eliminar la ubicación.',
                [
                    'pedido_id' => $pedidoId,
                    'error' => $e->getMessage(),
                ]
            );

            return false;
        }
    }
}
