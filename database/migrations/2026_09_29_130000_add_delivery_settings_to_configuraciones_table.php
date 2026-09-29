<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuraciones', function (Blueprint $table) {
            $table->decimal('latitud_restaurante', 10, 7)
                ->nullable()
                ->after('direccion');

            $table->decimal('longitud_restaurante', 10, 7)
                ->nullable()
                ->after('latitud_restaurante');

            $table->decimal('tarifa_minima_delivery', 10, 2)
                ->default(5.00)
                ->after('longitud_restaurante');

            $table->decimal('precio_km_delivery', 10, 2)
                ->default(3.00)
                ->after('tarifa_minima_delivery');

            $table->decimal('porcentaje_delivery', 5, 2)
                ->default(80.00)
                ->after('precio_km_delivery');

            $table->decimal('porcentaje_restaurante_delivery', 5, 2)
                ->default(20.00)
                ->after('porcentaje_delivery');
        });
    }

    public function down(): void
    {
        Schema::table('configuraciones', function (Blueprint $table) {
            $table->dropColumn([
                'latitud_restaurante',
                'longitud_restaurante',
                'tarifa_minima_delivery',
                'precio_km_delivery',
                'porcentaje_delivery',
                'porcentaje_restaurante_delivery',
            ]);
        });
    }
};