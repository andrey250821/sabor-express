<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use App\Services\FirebaseDeliveryLocationService;
use Illuminate\Console\Command;

class LimpiarUbicacionesEntregadas extends Command
{
    protected $signature = 'firebase:limpiar-ubicaciones-entregadas
                            {--pedido=* : IDs de pedidos adicionales a limpiar}';

    protected $description = 'Elimina de Firebase las ubicaciones de pedidos ya entregados';

    public function handle(FirebaseDeliveryLocationService $firebase): int
    {
        $pedidosEntregados = Pedido::query()
            ->where('estado', 'entregado')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $pedidosIndicados = collect($this->option('pedido'))
            ->flatMap(function ($valor) {
                return preg_split('/[,\s]+/', (string) $valor, -1, PREG_SPLIT_NO_EMPTY);
            })
            ->filter(fn ($id) => ctype_digit((string) $id))
            ->map(fn ($id) => (int) $id)
            ->all();

        $ids = collect(array_merge(
            $pedidosEntregados,
            $pedidosIndicados
        ))
            ->unique()
            ->sort()
            ->values();

        if ($ids->isEmpty()) {
            $this->warn('No se encontraron pedidos entregados para limpiar.');

            return self::SUCCESS;
        }

        $eliminados = 0;

        foreach ($ids as $pedidoId) {
            $this->line("Limpiando delivery_locations/{$pedidoId}...");

            if ($firebase->eliminarPorPedido($pedidoId)) {
                $this->info("✓ Pedido #{$pedidoId}: ubicación eliminada.");
                $eliminados++;
            } else {
                $this->error("✗ Pedido #{$pedidoId}: no se pudo eliminar la ubicación.");
            }
        }

        $this->newLine();
        $this->info(
            "Proceso terminado. {$eliminados} de {$ids->count()} ubicaciones fueron eliminadas."
        );

        return $eliminados === $ids->count()
            ? self::SUCCESS
            : self::FAILURE;
    }
}
