<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Las reservas temporales evitan vender una unidad que otro cliente
     * ya tiene en su carrito. El inventario físico solo se descuenta
     * definitivamente al confirmar el pedido.
     */
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('reservas_carrito');
    }
};
