import {
    ref,
    onValue,
} from 'firebase/database';

import { database } from './firebase';

document.addEventListener('DOMContentLoaded', () => {

    const contenedor = document.getElementById('cliente-delivery-tracking');

    if (!contenedor) {
        return;
    }

    const pedidoId = contenedor.dataset.pedidoId;
    const estadoPedido = contenedor.dataset.estado;

    const latitud = Number(contenedor.dataset.latitud);
    const longitud = Number(contenedor.dataset.longitud);

    const mapaElemento = document.getElementById('cliente-delivery-map');
    const estadoElemento = document.getElementById('cliente-delivery-status');

    if (!pedidoId || !mapaElemento) {
        console.error('Cliente Delivery: faltan datos necesarios.');
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Solo seguimos al delivery cuando está EN CAMINO
    |--------------------------------------------------------------------------
    */

    if (estadoPedido !== 'en_camino') {
        if (estadoElemento) {
            estadoElemento.textContent = 'El seguimiento en tiempo real estará disponible cuando el pedido esté en camino.';
        }

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Verificar coordenadas del destino
    |--------------------------------------------------------------------------
    */

    if (!Number.isFinite(latitud) || !Number.isFinite(longitud)) {

        if (estadoElemento) {
            estadoElemento.textContent =
                'No hay una ubicación de entrega registrada.';
        }

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | MAPA
    |--------------------------------------------------------------------------
    */

    const mapa = L.map(mapaElemento).setView(
        [latitud, longitud],
        15
    );

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }
    ).addTo(mapa);

    /*
    |--------------------------------------------------------------------------
    | MARCADOR DEL DESTINO
    |--------------------------------------------------------------------------
    */

    const marcadorDestino = L.marker([
        latitud,
        longitud
    ])
        .addTo(mapa)
        .bindPopup('Tu dirección de entrega');

    /*
    |--------------------------------------------------------------------------
    | CÍRCULO DEL DESTINO
    |--------------------------------------------------------------------------
    */

    L.circle([
        latitud,
        longitud
    ], {
        radius: 30,
    }).addTo(mapa);

    /*
    |--------------------------------------------------------------------------
    | MARCADOR DEL DELIVERY
    |--------------------------------------------------------------------------
    */

    let marcadorDelivery = null;

    /*
    |--------------------------------------------------------------------------
    | FIREBASE
    |--------------------------------------------------------------------------
    */

    const ubicacionRef = ref(
        database,
        `delivery_locations/${pedidoId}`
    );

    onValue(
        ubicacionRef,
        (snapshot) => {

            const ubicacion = snapshot.val();

            /*
             * Si todavía no hay ubicación.
             */

            if (!ubicacion) {

                if (estadoElemento) {
                    estadoElemento.textContent =
                        'Esperando la ubicación del repartidor...';
                }

                if (marcadorDelivery) {
                    mapa.removeLayer(marcadorDelivery);
                    marcadorDelivery = null;
                }

                return;
            }

            const deliveryLatitud = Number(ubicacion.latitud);
            const deliveryLongitud = Number(ubicacion.longitud);

            if (
                !Number.isFinite(deliveryLatitud) ||
                !Number.isFinite(deliveryLongitud)
            ) {
                return;
            }

            /*
             * Crear marcador la primera vez.
             */

            if (!marcadorDelivery) {

                marcadorDelivery = L.marker([
                    deliveryLatitud,
                    deliveryLongitud
                ])
                    .addTo(mapa)
                    .bindPopup('Repartidor');

                mapa.fitBounds(
                    [
                        [latitud, longitud],
                        [deliveryLatitud, deliveryLongitud]
                    ],
                    {
                        padding: [40, 40]
                    }
                );

            } else {

                /*
                 * Mover marcador existente.
                 */

                marcadorDelivery.setLatLng([
                    deliveryLatitud,
                    deliveryLongitud
                ]);
            }

            /*
             * Estado visual.
             */

            if (estadoElemento) {

                const fecha = ubicacion.actualizado_en
                    ? new Date(ubicacion.actualizado_en)
                    : null;

                const hora = fecha
                    ? fecha.toLocaleTimeString('es-BO', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                    })
                    : '';

                estadoElemento.textContent =
                    hora
                        ? `Delivery en camino · Actualizado ${hora}`
                        : 'Delivery en camino · Ubicación actualizada';
            }

            console.log(
                'Cliente: ubicación del delivery actualizada',
                ubicacion
            );
        },
        (error) => {

            console.error(
                'Cliente Delivery: error al escuchar Firebase.',
                error
            );

            if (estadoElemento) {
                estadoElemento.textContent =
                    'No se pudo obtener la ubicación del repartidor.';
            }
        }
    );

});