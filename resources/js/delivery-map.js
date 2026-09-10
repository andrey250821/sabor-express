import { ref, onValue } from 'firebase/database';

import { database } from './firebase';


document.addEventListener('DOMContentLoaded', () => {

    const contenedor = document.getElementById('mapa-entrega');

    if (!contenedor) {
        return;
    }

    if (typeof L === 'undefined') {
        console.error(
            'Sabor Express: Leaflet no está disponible.'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | DATOS DEL PEDIDO
    |--------------------------------------------------------------------------
    */

    const pedidoId = contenedor.dataset.pedidoId;

    const latitudDestino =
        Number(contenedor.dataset.latitud);

    const longitudDestino =
        Number(contenedor.dataset.longitud);

    const estadoPedido =
        contenedor.dataset.estado || 'listo';


    if (
        !pedidoId ||
        !Number.isFinite(latitudDestino) ||
        !Number.isFinite(longitudDestino)
    ) {
        console.error(
            'Sabor Express: datos inválidos para el mapa Delivery.',
            {
                pedidoId,
                latitudDestino,
                longitudDestino,
                estadoPedido,
            }
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR MAPA
    |--------------------------------------------------------------------------
    */

    const mapa = L.map(
        contenedor,
        {
            center: [
                latitudDestino,
                longitudDestino
            ],

            zoom: 16,

            zoomControl: true,

            scrollWheelZoom: true,

            dragging: true,

            doubleClickZoom: true,
        }
    );


    /*
    |--------------------------------------------------------------------------
    | OPENSTREETMAP
    |--------------------------------------------------------------------------
    */

    const capaOpenStreetMap =
        L.tileLayer(
            'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,

                attribution:
                    '&copy; OpenStreetMap contributors',
            }
        );

    capaOpenStreetMap.addTo(mapa);


    /*
    |--------------------------------------------------------------------------
    | ICONO DESTINO
    |--------------------------------------------------------------------------
    */

    const iconoDestino = L.divIcon({

        className:
            'delivery-mapa-icono-destino',

        html:
            '<i class="bi bi-geo-alt-fill"></i>',

        iconSize: [
            44,
            44
        ],

        iconAnchor: [
            22,
            44
        ],

        popupAnchor: [
            0,
            -44
        ],

    });


    /*
    |--------------------------------------------------------------------------
    | MARCADOR DESTINO
    |--------------------------------------------------------------------------
    */

    const marcadorDestino =
        L.marker(
            [
                latitudDestino,
                longitudDestino
            ],
            {
                icon: iconoDestino,
            }
        )
        .addTo(mapa);


    marcadorDestino.bindPopup(
        `
        <div class="delivery-leaflet-popup">

            <strong>
                Destino del pedido
            </strong>

            <span>
                Pedido #${pedidoId}
            </span>

        </div>
        `
    );


    /*
    |--------------------------------------------------------------------------
    | ICONO DELIVERY
    |--------------------------------------------------------------------------
    */

    const iconoDelivery = L.divIcon({

        className:
            'delivery-mapa-icono-delivery',

        html:
            '<i class="bi bi-bicycle"></i>',

        iconSize: [
            48,
            48
        ],

        iconAnchor: [
            24,
            24
        ],

        popupAnchor: [
            0,
            -24
        ],

    });


    /*
    |--------------------------------------------------------------------------
    | VARIABLES
    |--------------------------------------------------------------------------
    */

    let marcadorDelivery = null;

    let rutaDelivery = null;

    let ultimaRutaLatitud = null;

    let ultimaRutaLongitud = null;

    let solicitudRutaEnCurso = false;

    const DISTANCIA_MINIMA_RUTA = 30;


    /*
    |--------------------------------------------------------------------------
    | DISTANCIA HAVERSINE
    |--------------------------------------------------------------------------
    */

    function calcularDistanciaMetros(
        lat1,
        lon1,
        lat2,
        lon2
    ) {

        const radioTierra = 6371000;

        const gradosARadianes =
            grados => grados * Math.PI / 180;

        const diferenciaLatitud =
            gradosARadianes(lat2 - lat1);

        const diferenciaLongitud =
            gradosARadianes(lon2 - lon1);

        const a =
            Math.sin(diferenciaLatitud / 2) ** 2 +
            Math.cos(
                gradosARadianes(lat1)
            ) *
            Math.cos(
                gradosARadianes(lat2)
            ) *
            Math.sin(diferenciaLongitud / 2) ** 2;

        const c =
            2 *
            Math.atan2(
                Math.sqrt(a),
                Math.sqrt(1 - a)
            );

        return radioTierra * c;
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATEAR DISTANCIA
    |--------------------------------------------------------------------------
    */

    function formatearDistancia(
        distanciaMetros
    ) {

        if (distanciaMetros < 1000) {

            return `${Math.round(distanciaMetros)} m`;

        }

        return `${(
            distanciaMetros / 1000
        ).toFixed(1)} km`;
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR ESTADO VISUAL
    |--------------------------------------------------------------------------
    */

    function actualizarEstadoMapa(
        mensaje
    ) {

        const elemento =
            document.getElementById(
                'delivery-map-status'
            );

        if (!elemento) {
            return;
        }

        elemento.innerHTML =
            mensaje;
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR RUTA REAL
    |--------------------------------------------------------------------------
    */

    async function actualizarRuta(
        deliveryLatitud,
        deliveryLongitud
    ) {

        if (solicitudRutaEnCurso) {
            return;
        }


        if (
            ultimaRutaLatitud !== null &&
            ultimaRutaLongitud !== null
        ) {

            const movimiento =
                calcularDistanciaMetros(
                    ultimaRutaLatitud,
                    ultimaRutaLongitud,
                    deliveryLatitud,
                    deliveryLongitud
                );


            if (
                movimiento <
                DISTANCIA_MINIMA_RUTA
            ) {

                return;

            }

        }


        solicitudRutaEnCurso = true;


        const url =
            `https://router.project-osrm.org/route/v1/driving/` +
            `${deliveryLongitud},${deliveryLatitud};` +
            `${longitudDestino},${latitudDestino}` +
            `?overview=full&geometries=geojson`;


        try {

            const respuesta =
                await fetch(url);


            if (!respuesta.ok) {

                throw new Error(
                    `Error HTTP ${respuesta.status}`
                );

            }


            const datos =
                await respuesta.json();


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


            const ruta =
                datos.routes[0];


            ultimaRutaLatitud =
                deliveryLatitud;

            ultimaRutaLongitud =
                deliveryLongitud;


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

            rutaDelivery =
                L.geoJSON(
                    ruta.geometry,
                    {
                        style: {

                            weight: 6,

                            opacity: 0.85,

                        }
                    }
                )
                .addTo(mapa);


            /*
            |--------------------------------------------------------------------------
            | DISTANCIA Y ETA
            |--------------------------------------------------------------------------
            */

            const distanciaRuta =
                ruta.distance;

            const duracionRuta =
                ruta.duration;


            const minutos =
                Math.ceil(
                    duracionRuta / 60
                );


            let textoTiempo;


            if (minutos <= 1) {

                textoTiempo =
                    'menos de 1 min';

            } else {

                textoTiempo =
                    `${minutos} min`;

            }


            let mensaje;


            if (distanciaRuta <= 50) {

                mensaje =
                    `📍 Estás llegando al destino · ` +
                    `${formatearDistancia(distanciaRuta)}`;

            } else if (
                distanciaRuta <= 300
            ) {

                mensaje =
                    `🟢 Estás muy cerca · ` +
                    `${formatearDistancia(distanciaRuta)}`;

            } else if (
                distanciaRuta <= 1000
            ) {

                mensaje =
                    `🟡 Estás cerca del cliente · ` +
                    `${formatearDistancia(distanciaRuta)}`;

            } else {

                mensaje =
                    `🚴 En camino al destino · ` +
                    `${formatearDistancia(distanciaRuta)}`;

            }


            actualizarEstadoMapa(
                `${mensaje}<br>` +
                `⏱️ Tiempo estimado: ` +
                `<strong>${textoTiempo}</strong>`
            );


            console.log(
                'Delivery: ruta actualizada.',
                {
                    distancia:
                        Math.round(
                            distanciaRuta
                        ),

                    duracion:
                        Math.round(
                            duracionRuta
                        ),

                    eta:
                        textoTiempo,
                }
            );


        } catch (error) {

            console.error(
                'Delivery: error obteniendo ruta OSRM.',
                error
            );

        } finally {

            solicitudRutaEnCurso =
                false;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | FIREBASE
    |--------------------------------------------------------------------------
    */

    const ubicacionRef =
        ref(
            database,
            `delivery_locations/${pedidoId}`
        );


    onValue(
        ubicacionRef,
        snapshot => {

            const ubicacion =
                snapshot.val();


            /*
            |--------------------------------------------------------------------------
            | SIN UBICACIÓN
            |--------------------------------------------------------------------------
            */

            if (!ubicacion) {

                if (marcadorDelivery) {

                    mapa.removeLayer(
                        marcadorDelivery
                    );

                    marcadorDelivery = null;

                }


                actualizarEstadoMapa(
                    estadoPedido === 'en_camino'
                        ? '📡 Esperando la ubicación del repartidor...'
                        : '📍 El GPS se activará al iniciar la entrega.'
                );


                return;
            }


            const deliveryLatitud =
                Number(
                    ubicacion.latitud
                );

            const deliveryLongitud =
                Number(
                    ubicacion.longitud
                );


            if (
                !Number.isFinite(
                    deliveryLatitud
                ) ||
                !Number.isFinite(
                    deliveryLongitud
                )
            ) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | CREAR / ACTUALIZAR MARCADOR
            |--------------------------------------------------------------------------
            */

            if (!marcadorDelivery) {

                marcadorDelivery =
                    L.marker(
                        [
                            deliveryLatitud,
                            deliveryLongitud
                        ],
                        {
                            icon:
                                iconoDelivery,
                        }
                    )
                    .addTo(mapa);


                marcadorDelivery.bindPopup(
                    `
                    <div class="delivery-leaflet-popup">

                        <strong>
                            Tu ubicación
                        </strong>

                        <span>
                            Ubicación actual del repartidor
                        </span>

                    </div>
                    `
                );

            } else {

                marcadorDelivery.setLatLng(
                    [
                        deliveryLatitud,
                        deliveryLongitud
                    ]
                );

            }


            /*
            |--------------------------------------------------------------------------
            | CENTRAR MAPA
            |--------------------------------------------------------------------------
            */

            if (!rutaDelivery) {

                const limites =
                    L.latLngBounds([
                        [
                            latitudDestino,
                            longitudDestino
                        ],
                        [
                            deliveryLatitud,
                            deliveryLongitud
                        ]
                    ]);


                mapa.fitBounds(
                    limites,
                    {
                        padding: [
                            50,
                            50
                        ]
                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | RUTA
            |--------------------------------------------------------------------------
            */

            actualizarRuta(
                deliveryLatitud,
                deliveryLongitud
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | AJUSTAR MAPA
    |--------------------------------------------------------------------------
    */

    setTimeout(
        () => mapa.invalidateSize(true),
        100
    );

    setTimeout(
        () => mapa.invalidateSize(true),
        500
    );

    setTimeout(
        () => mapa.invalidateSize(true),
        1000
    );


    window.addEventListener(
        'resize',
        () => mapa.invalidateSize(true)
    );


    /*
    |--------------------------------------------------------------------------
    | REFERENCIA GLOBAL
    |--------------------------------------------------------------------------
    */

    window.mapaEntregaDelivery =
        mapa;


    console.log(
        'Sabor Express: mapa Delivery iniciado.',
        {
            pedidoId,
            estadoPedido,
        }
    );

});