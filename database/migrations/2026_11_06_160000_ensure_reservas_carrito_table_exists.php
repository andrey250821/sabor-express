<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Recupera la tabla de reservas temporales cuando una base de datos
     * conserva el registro de la migración original, pero la tabla no existe.
     *
     * No altera productos, pedidos ni reservas existentes.
     */
    public function up(): void
    {
        if (Schema::hasTable('reservas_carrito')) {
            return;
        }

        Schema::create('reservas_carrito', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64);
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();
            $table->unsignedInteger('cantidad');
            $table->timestamp('expires_at')->index();
            $table->timestamps();

            $table->unique(['token', 'producto_id']);
            $table->index(['producto_id', 'expires_at']);
        });
    }

    /**
     * No se elimina esta tabla al revertir esta migración de reparación:
     * la tabla pertenece al flujo del carrito y su eliminación podría
     * borrar reservas creadas después de ejecutar la reparación.
     */
    public function down(): void
    {
        // Intencionalmente vacío para evitar pérdida de datos.
    }
};
