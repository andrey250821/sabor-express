<?php

namespace App\Services;

use App\Models\Configuracion;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CalcularDeliveryService
{
    /**
     * Calcular distancia por carretera y tarifa de Delivery.
     *
     * El origen siempre es la ubicación guardada en Configuración.
     * La cotización no modifica la base de datos y puede utilizarse
     * tanto en el checkout como al crear el pedido definitivo.
     */
    public function cotizar(float $latitudDestino, float $longitudDestino): array
    {
        $configuracion = Configuracion::first();

        if (!$configuracion) {
            throw new RuntimeException(
                'No existe la configuración del restaurante.'
            );
        }

        if (
            $configuracion->latitud_restaurante === null
            || $configuracion->longitud_restaurante === null
        ) {
            throw new RuntimeException(
                'La ubicación del restaurante todavía no está configurada. Un administrador debe guardar la ubicación en Configuración.'
            );
        }

        $tarifaMinima = round(
            (float) ($configuracion->tarifa_minima_delivery ?? 5.00),
            2
        );

        $precioKm = round(
            (float) ($configuracion->precio_km_delivery ?? 3.00),
            2
        );

        $porcentajeDelivery = round(
            (float) ($configuracion->porcentaje_delivery ?? 80.00),
            2
        );

        $porcentajeRestaurante = round(
            (float) ($configuracion->porcentaje_restaurante_delivery ?? 20.00),
            2
        );

        if ($tarifaMinima < 0 || $precioKm < 0) {
            throw new RuntimeException(
                'La configuración de Delivery contiene valores negativos no válidos.'
            );
        }

        if (round($porcentajeDelivery + $porcentajeRestaurante, 2) !== 100.00) {
            throw new RuntimeException(
                'La configuración de porcentajes de Delivery debe sumar exactamente 100%.'
            );
        }

        $latitudOrigen = (float) $configuracion->latitud_restaurante;
        $longitudOrigen = (float) $configuracion->longitud_restaurante;

        $baseUrl = rtrim(
            (string) config('services.osrm.url', 'https://router.project-osrm.org'),
            '/'
        );

        $url = $baseUrl
            . '/route/v1/driving/'
            . $longitudOrigen . ',' . $latitudOrigen
            . ';'
            . $longitudDestino . ',' . $latitudDestino;

        $respuesta = Http::timeout(12)
            ->retry(2, 250)
            ->acceptJson()
            ->get($url, [
                'overview' => 'false',
                'alternatives' => 'false',
                'steps' => 'false',
            ]);

        if (!$respuesta->successful()) {
            throw new RuntimeException(
                'No fue posible calcular la ruta por carretera en este momento.'
            );
        }

        $resultado = $respuesta->json();

        if (
            ($resultado['code'] ?? null) !== 'Ok'
            || empty($resultado['routes'])
            || !isset($resultado['routes'][0]['distance'])
        ) {
            throw new RuntimeException(
                'No existe una ruta válida entre el restaurante y la ubicación de entrega.'
            );
        }

        $distanciaKm = round(
            ((float) $resultado['routes'][0]['distance']) / 1000,
            2
        );

        $tarifaCalculada = round(
            max($tarifaMinima, $distanciaKm * $precioKm),
            2
        );

        $montoDelivery = round(
            $tarifaCalculada * ($porcentajeDelivery / 100),
            2
        );

        // El restante evita diferencias de redondeo y garantiza
        // que ambas partes sumen exactamente la tarifa congelada.
        $montoRestaurante = round(
            $tarifaCalculada - $montoDelivery,
            2
        );

        return [
            'distancia_delivery_km' => $distanciaKm,
            'tarifa_delivery' => $tarifaCalculada,
            'porcentaje_delivery' => $porcentajeDelivery,
            'monto_delivery' => $montoDelivery,
            'porcentaje_restaurante_delivery' => $porcentajeRestaurante,
            'monto_restaurante_delivery' => $montoRestaurante,
            'tarifa_minima_delivery' => $tarifaMinima,
            'precio_km_delivery' => $precioKm,
        ];
    }
}