<?php

namespace App\Support;

use App\Models\User;

/**
 * Resuelve la ruta de inicio correspondiente al rol del usuario autenticado.
 */
final class UserDashboardRoute
{
    public static function nameFor(?User $user): string
    {
        return match ($user?->role?->nombre) {
            'Administrador' => 'admin.dashboard',
            'Delivery' => 'delivery.dashboard',
            'Cocinero' => 'cocinero.dashboard',
            default => 'cliente.dashboard.index',
        };
    }
}
