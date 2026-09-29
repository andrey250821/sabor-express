<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('subtotal_productos', 10, 2)
                ->default(0.00)
                ->after('total');

            $table->decimal('tarifa_delivery', 10, 2)
                ->default(0.00)
                ->after('subtotal_productos');

            $table->decimal('distancia_delivery_km', 10, 2)
                ->nullable()
                ->after('tarifa_delivery');

            $table->decimal('porcentaje_delivery', 5, 2)
                ->default(0.00)
                ->after('distancia_delivery_km');

            $table->decimal('monto_delivery', 10, 2)
                ->default(0.00)
                ->after('porcentaje_delivery');

            $table->decimal('porcentaje_restaurante_delivery', 5, 2)
                ->default(0.00)
                ->after('monto_delivery');

            $table->decimal('monto_restaurante_delivery', 10, 2)
                ->default(0.00)
                ->after('porcentaje_restaurante_delivery');
        });

        // Compatibilidad con pedidos existentes antes de implementar
        // el cálculo de Delivery: en este punto todos los registros
        // existentes corresponden al flujo anterior y su total representa
        // únicamente el importe de los productos.
        DB::table('pedidos')->update([
            'subtotal_productos' => DB::raw('total'),
            'tarifa_delivery' => 0,
            'distancia_delivery_km' => null,
            'porcentaje_delivery' => 0,
            'monto_delivery' => 0,
            'porcentaje_restaurante_delivery' => 0,
            'monto_restaurante_delivery' => 0,
        ]);
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal_productos',
                'tarifa_delivery',
                'distancia_delivery_km',
                'porcentaje_delivery',
                'monto_delivery',
                'porcentaje_restaurante_delivery',
                'monto_restaurante_delivery',
            ]);
        });
    }
};