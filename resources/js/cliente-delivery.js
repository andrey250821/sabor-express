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
    | CALCULAR DISTANCIA ENTRE DOS COORDENADAS
    |--------------------------------------------------------------------------
    */

    function calcularDistanciaMetros(lat1, lon1, lat2, lon2) {

        const R = 6371000;
        const rad = Math.PI / 180;

        const dLat = (lat2 - lat1) * rad;
        const dLon = (lon2 - lon1) * rad;

        const a =
            Math.sin(dLat / 2) ** 2 +
            Math.cos(lat1 * rad) *
            Math.cos(lat2 * rad) *
            Math.sin(dLon / 2) ** 2;

        const c =
            2 * Math.atan2(
                Math.sqrt(a),
                Math.sqrt(1 - a)
            );

        return R * c;
    }

    /*
    |--------------------------------------------------------------------------
    | FORMATEAR DISTANCIA
    |--------------------------------------------------------------------------
    */

    function formatearDistancia(metros) {

        if (metros < 1000) {
            return `${Math.round(metros)} m`;
        }

        return `${(metros / 1000).toFixed(1)} km`;
    }

    /*
    |--------------------------------------------------------------------------
    | MENSAJE SEGÚN DISTANCIA
    |--------------------------------------------------------------------------
    */

    function obtenerMensajeDistancia(distanciaMetros) {

        if (distanciaMetros <= 50) {
            return `📍 El delivery está llegando · ${formatearDistancia(distanciaMetros)}`;
        }

        if (distanciaMetros <= 300) {
            return `🚴 ¡Tu delivery está cerca! · ${formatearDistancia(distanciaMetros)}`;
        }

        if (distanciaMetros <= 500) {
            return `Delivery se está acercando · ${formatearDistancia(distanciaMetros)}`;
        }

        return `Delivery en camino · ${formatearDistancia(distanciaMetros)}`;
    }

    /*
    |--------------------------------------------------------------------------
    | SOLO SEGUIMOS AL DELIVERY CUANDO ESTÁ EN CAMINO
    |--------------------------------------------------------------------------
    */

    if (estadoPedido !== 'en_camino') {

        if (estadoElemento) {
            estadoElemento.textContent =
                'El seguimiento en tiempo real estará disponible cuando el pedido esté en camino.';
        }

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFICAR COORDENADAS DEL DESTINO
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

    const iconoDestino = L.divIcon({
        className: 'cliente-mapa-icono-destino',
        html: '<i class="bi bi-geo-alt-fill"></i>',
        iconSize: [42, 42],
        iconAnchor: [21, 42],
        popupAnchor: [0, -42],
    });

    const marcadorDestino = L.marker([
        latitud,
        longitud
    ], {
        icon: iconoDestino
    })
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

    const iconoDelivery = L.divIcon({
        className: 'cliente-mapa-icono-delivery',
        html: '<i class="bi bi-bicycle"></i>',
        iconSize: [48, 48],
        iconAnchor: [24, 24],
        popupAnchor: [0, -24],
    });

    /*
    |--------------------------------------------------------------------------
    | RUTA DEL DELIVERY
    |--------------------------------------------------------------------------
    */

    let rutaDelivery = null;
    let ultimaRutaLatitud = null;
    let ultimaRutaLongitud = null;
    let solicitudRutaEnCurso = false;

    const DISTANCIA_MINIMA_ACTUALIZACION_RUTA = 30;
    /*
    |--------------------------------------------------------------------------
    | OBTENER RUTA REAL MEDIANTE OSRM
    |--------------------------------------------------------------------------
    */

    async function actualizarRuta(
    deliveryLatitud,
    deliveryLongitud
) {

    /*
    |--------------------------------------------------------------------------
    | EVITAR SOLICITUDES SIMULTÁNEAS
    |--------------------------------------------------------------------------
    */

    if (solicitudRutaEnCurso) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | NO RECALCULAR SI EL DELIVERY CASI NO SE MOVIÓ
    |--------------------------------------------------------------------------
    */

    if (
        ultimaRutaLatitud !== null &&
        ultimaRutaLongitud !== null
    ) {

        const movimientoDesdeUltimaRuta =
            calcularDistanciaMetros(
                ultimaRutaLatitud,
                ultimaRutaLongitud,
                deliveryLatitud,
                deliveryLongitud
            );

        if (
            movimientoDesdeUltimaRuta <
            DISTANCIA_MINIMA_ACTUALIZACION_RUTA
        ) {
            return;
        }
    }

    solicitudRutaEnCurso = true;

    const url =
        `https://router.project-osrm.org/route/v1/driving/` +
        `${deliveryLongitud},${deliveryLatitud};` +
        `${longitud},${latitud}` +
        `?overview=full&geometries=geojson`;

    try {

        const respuesta = await fetch(url);

        if (!respuesta.ok) {
            throw new Error(
                `Error HTTP ${respuesta.status}`
            );
        }

        const datos = await respuesta.json();

        if (
            datos.code !== 'Ok' ||
            !datos.routes ||
            !datos.routes.length
        ) {

            console.warn(
                'OSRM: no se encontró una ruta.',
                datos
            );

            return;
        }

        const ruta = datos.routes[0];

        /*
        |--------------------------------------------------------------------------
        | GUARDAR POSICIÓN UTILIZADA PARA ESTA RUTA
        |--------------------------------------------------------------------------
        */

        ultimaRutaLatitud = deliveryLatitud;
        ultimaRutaLongitud = deliveryLongitud;

        /*
        |--------------------------------------------------------------------------
        | ELIMINAR RUTA ANTERIOR
        |--------------------------------------------------------------------------
        */

        if (rutaDelivery) {

            mapa.removeLayer(
                rutaDelivery
            );

            rutaDelivery = null;
        }

        /*
        |--------------------------------------------------------------------------
        | DIBUJAR NUEVA RUTA
        |--------------------------------------------------------------------------
        */

        rutaDelivery = L.geoJSON(
            ruta.geometry,
            {
                style: {
                    weight: 6,
                    opacity: 0.85,
                }
            }
        ).addTo(mapa);

        /*
        |--------------------------------------------------------------------------
        | DATOS REALES DE LA RUTA
        |--------------------------------------------------------------------------
        */

        const distanciaRutaMetros =
            ruta.distance;

        const duracionRutaSegundos =
            ruta.duration;

        /*
        |--------------------------------------------------------------------------
        | FORMATEAR TIEMPO
        |--------------------------------------------------------------------------
        */

        const minutos =
            Math.ceil(
                duracionRutaSegundos / 60
            );

        let textoTiempo;

        if (minutos <= 1) {

            textoTiempo =
                'menos de 1 min';

        } else {

            textoTiempo =
                `${minutos} min`;
        }

        /*
        |--------------------------------------------------------------------------
        | MENSAJE PRINCIPAL
        |--------------------------------------------------------------------------
        */

        const mensajeRuta =
            obtenerMensajeDistancia(
                distanciaRutaMetros
            );

        /*
        |--------------------------------------------------------------------------
        | MOSTRAR DISTANCIA + ETA
        |--------------------------------------------------------------------------
        */

        if (estadoElemento) {

            const fecha =
                new Date();

            const hora =
                fecha.toLocaleTimeString(
                    'es-BO',
                    {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                    }
                );

            estadoElemento.innerHTML =
                `${mensajeRuta}<br>` +
                `⏱️ Llegada estimada: <strong>${textoTiempo}</strong>` +
                ` · Actualizado ${hora}`;
        }

        console.log(
            'Ruta real actualizada:',
            {
                distancia_ruta_metros:
                    Math.round(
                        distanciaRutaMetros
                    ),

                duracion_ruta_segundos:
                    Math.round(
                        duracionRutaSegundos
                    ),

                llegada_estimada:
                    textoTiempo,
            }
        );

    } catch (error) {

        console.error(
            'Cliente Delivery: error al obtener la ruta OSRM.',
            error
        );

    } finally {

        solicitudRutaEnCurso = false;
    }
}

    /*
    |--------------------------------------------------------------------------
    | FIREBASE
    |--------------------------------------------------------------------------
    */

    const ubicacionRef = ref(
        database,
        `delivery_locations/${pedidoId}`
    );

    /*
    |--------------------------------------------------------------------------
    | ESCUCHAR UBICACIÓN DEL DELIVERY
    |--------------------------------------------------------------------------
    */

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

                if (rutaDelivery) {
                    mapa.removeLayer(rutaDelivery);
                    rutaDelivery = null;
                }

                return;
            }

            const deliveryLatitud =
                Number(ubicacion.latitud);

            const deliveryLongitud =
                Number(ubicacion.longitud);

            if (
                !Number.isFinite(deliveryLatitud) ||
                !Number.isFinite(deliveryLongitud)
            ) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | CREAR MARCADOR LA PRIMERA VEZ
            |--------------------------------------------------------------------------
            */

            if (!marcadorDelivery) {

                marcadorDelivery = L.marker([
                    deliveryLatitud,
                    deliveryLongitud
                ], {
                    icon: iconoDelivery
                })
                    .addTo(mapa)
                    .bindPopup('Repartidor');

                /*
                 * Ajustar el mapa solamente
                 * la primera vez.
                 */

                mapa.fitBounds(
                    [
                        [latitud, longitud],
                        [
                            deliveryLatitud,
                            deliveryLongitud
                        ]
                    ],
                    {
                        padding: [40, 40]
                    }
                );

            } else {

                /*
                |--------------------------------------------------------------------------
                | MOVER MARCADOR EXISTENTE
                |--------------------------------------------------------------------------
                */

                marcadorDelivery.setLatLng([
                    deliveryLatitud,
                    deliveryLongitud
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | CALCULAR DISTANCIA EN LÍNEA RECTA
            |--------------------------------------------------------------------------
            */

            const distanciaMetros =
                calcularDistanciaMetros(
                    deliveryLatitud,
                    deliveryLongitud,
                    latitud,
                    longitud
                );

            /*
            |--------------------------------------------------------------------------
            | ACTUALIZAR RUTA REAL
            |--------------------------------------------------------------------------
            */

            actualizarRuta(
                deliveryLatitud,
                deliveryLongitud
            );

            /*
            |--------------------------------------------------------------------------
            | ESTADO VISUAL
            |--------------------------------------------------------------------------
            */

            if (estadoElemento) {

                const fecha = ubicacion.actualizado_en
                    ? new Date(
                        ubicacion.actualizado_en
                    )
                    : null;

                const hora = fecha
                    ? fecha.toLocaleTimeString(
                        'es-BO',
                        {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                        }
                    )
                    : '';

                if (!rutaDelivery) {

    if (estadoElemento) {

        const fecha = ubicacion.actualizado_en
            ? new Date(
                ubicacion.actualizado_en
            )
            : null;

        const hora = fecha
            ? fecha.toLocaleTimeString(
                'es-BO',
                {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                }
            )
            : '';

        const mensajeDistancia =
            obtenerMensajeDistancia(
                distanciaMetros
            );

        estadoElemento.textContent =
            hora
                ? `${mensajeDistancia} · Actualizado ${hora}`
                : mensajeDistancia;
    }
}
            }

            console.log(
                'Cliente: ubicación del delivery actualizada',
                {
                    ...ubicacion,

                    distancia_metros:
                        Math.round(
                            distanciaMetros
                        ),
                }
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