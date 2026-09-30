<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();

            $table->string('nombre_restaurante')
                ->default('Sabor Express');

            $table->string('telefono')
                ->nullable();

            $table->text('direccion')
                ->nullable();

            $table->decimal('latitud_restaurante', 10, 7)
                ->nullable();

            $table->decimal('longitud_restaurante', 10, 7)
                ->nullable();

            $table->decimal('tarifa_minima_delivery', 10, 2)
                ->default(5.00);

            $table->decimal('precio_km_delivery', 10, 2)
                ->default(3.00);

            $table->decimal('porcentaje_delivery', 5, 2)
                ->default(80.00);

            $table->decimal('porcentaje_restaurante_delivery', 5, 2)
                ->default(20.00);

            $table->string('logo')
                ->nullable();

            $table->string('qr_pago')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};
