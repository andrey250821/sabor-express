<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('cocinero_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('total', 10, 2);

            $table->decimal('subtotal_productos', 10, 2)
                ->default(0.00);

            $table->decimal('tarifa_delivery', 10, 2)
                ->default(0.00);

            $table->decimal('distancia_delivery_km', 10, 2)
                ->nullable();

            $table->decimal('porcentaje_delivery', 5, 2)
                ->default(0.00);

            $table->decimal('monto_delivery', 10, 2)
                ->default(0.00);

            $table->decimal('porcentaje_restaurante_delivery', 5, 2)
                ->default(0.00);

            $table->decimal('monto_restaurante_delivery', 10, 2)
                ->default(0.00);

            $table->enum('estado', [
                'pendiente',
                'comprobante_enviado',
                'pagado',
                'preparando',
                'listo',
                'asignado',
                'en_camino',
                'entregado',
                'cancelado',
            ])->default('pendiente');

            // Marca temporal del momento en que el pedido queda listo para Delivery.
            // Permite mantener el orden FIFO sin crear una tabla adicional.
            $table->timestamp('fecha_listo')
                ->nullable();

            $table->decimal('latitud', 10, 7)
                ->nullable();

            $table->decimal('longitud', 10, 7)
                ->nullable();

            $table->text('direccion_entrega');

            $table->text('observacion_cliente')
                ->nullable();

            $table->text('referencia_delivery')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
