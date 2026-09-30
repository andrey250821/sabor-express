<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use Illuminate\Console\Command;

class LimpiarNotificacionesExpiradas extends Command
{
    protected $signature = 'notificaciones:limpiar';

    protected $description = 'Elimina notificaciones con más de 3 días de antigüedad';

    public function handle(): int
    {
        $limite = now()->subDays(3);

        $eliminadas = Notificacion::query()
            ->where('created_at', '<=', $limite)
            ->delete();

        $this->info(
            "Notificaciones eliminadas: {$eliminadas}. Límite: {$limite->format('d/m/Y H:i:s')}."
        );

        return self::SUCCESS;
    }
}
