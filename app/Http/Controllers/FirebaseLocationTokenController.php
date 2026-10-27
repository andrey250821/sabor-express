<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Services\FirebaseCustomTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class FirebaseLocationTokenController extends Controller
{
    /**
     * Entrega un token de Firebase limitado a un pedido al usuario que
     * ya está autorizado por la sesión de Laravel.
     */
    public function issue(
        Request $request,
        int $pedidoId,
        FirebaseCustomTokenService $tokens
    ): JsonResponse {
        $user = $request->user();
        $user->loadMissing('role');

        $pedido = Pedido::query()
            ->with('asignacionDelivery')
            ->findOrFail($pedidoId);

        $role = $user->role?->nombre;

        $authorized = match ($role) {
            'Administrador' => true,
            'Cliente' => (int) $pedido->user_id === (int) $user->id,
            'Delivery' => (int) ($pedido->asignacionDelivery?->delivery_id ?? 0) === (int) $user->id,
            default => false,
        };

        abort_unless($authorized, 403, 'No tienes permiso para consultar la ubicación de este pedido.');

        try {
            $token = $tokens->createToken((int) $user->id, [
                'laravel_user_id' => (int) $user->id,
                'role' => $role,
                'pedido_id' => (string) $pedido->id,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()
                ->json([
                    'message' => 'La autenticación de Firebase no está configurada en el servidor. Contacta al administrador del sistema.',
                ], 503)
                ->header('Cache-Control', 'no-store, private');
        }

        return response()
            ->json(['token' => $token])
            ->header('Cache-Control', 'no-store, private');
    }
}
