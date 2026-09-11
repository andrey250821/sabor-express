import { ref, onValue } from 'firebase/database';
import { database } from './firebase';

document.addEventListener('DOMContentLoaded', () => {

    const contenedor = document.getElementById('admin-delivery-map');

    if (!contenedor) {
        return;
    }

    if (typeof L === 'undefined') {
        console.error('Sabor Express: Leaflet no está disponible.');
        return;
    }

    const pedidoId = contenedor.dataset.pedidoId;

    const latitudCliente = Number(contenedor.dataset.latitud);
    const longitudCliente = Number(contenedor.dataset.longitud);

    const estadoPedido = contenedor.dataset.estado || '';

    if (
        !pedidoId ||
        !Number.isFinite(latitudCliente) ||
        !Number.isFinite(longitudCliente)
    ) {
        console.error(
            'Sabor Express: datos inválidos para el mapa del administrador.',
            {
                pedidoId,
                latitudCliente,
                longitudCliente,
                estadoPedido
            }
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR MAPA
    |--------------------------------------------------------------------------
    */

    const mapa = L.map(contenedor, {
        center: [
            latitudCliente,
            longitudCliente
        ],

        zoom: 15,

        zoomControl: true,

        scrollWheelZoom: true,

        dragging: true,

        doubleClickZoom: true
    });


    /*
    |--------------------------------------------------------------------------
    | OPENSTREETMAP
    |--------------------------------------------------------------------------
    */

    L.tileLayer(
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,

            attribution:
                '&copy; OpenStreetMap contributors'
        }
    ).addTo(mapa);


    /*
    |--------------------------------------------------------------------------
    | ICONO CLIENTE
    |--------------------------------------------------------------------------
    */

    const iconoCliente = L.divIcon({

        className: 'admin-mapa-icono-cliente',

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
        ]

    });


    /*
    |--------------------------------------------------------------------------
    | MARCADOR CLIENTE
    |--------------------------------------------------------------------------
    */

    const marcadorCliente = L.marker(
        [
            latitudCliente,
            longitudCliente
        ],
        {
            icon: iconoCliente
        }
    ).addTo(mapa);


    marcadorCliente.bindPopup(`
        <div class="admin-leaflet-popup">

            <strong>
                Cliente
            </strong>

            <span>
                Destino del pedido #${pedidoId}
            </span>

        </div>
    `);


    /*
    |--------------------------------------------------------------------------
    | ICONO DELIVERY
    |--------------------------------------------------------------------------
    */

    const iconoDelivery = L.divIcon({

        className: 'admin-mapa-icono-delivery',

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
        ]

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
    | ELEMENTOS DE INFORMACIÓN
    |--------------------------------------------------------------------------
    */

    const estadoElemento =
        document.getElementById(
            'admin-delivery-map-status'
        );

    const distanciaElemento =
        document.getElementById(
            'admin-delivery-distance'
        );

    const tiempoElemento =
        document.getElementById(
            'admin-delivery-eta'
        );


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
            gradosARadianes(
                lat2 - lat1
            );

        const diferenciaLongitud =
            gradosARadianes(
                lon2 - lon1
            );

        const a =
            Math.sin(
                diferenciaLatitud / 2
            ) ** 2 +

            Math.cos(
                gradosARadianes(lat1)
            ) *

            Math.cos(
                gradosARadianes(lat2)
            ) *

            Math.sin(
                diferenciaLongitud / 2
            ) ** 2;

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
    | ACTUALIZAR MENSAJE
    |--------------------------------------------------------------------------
    */

    function actualizarEstado(
        mensaje
    ) {

        if (!estadoElemento) {
            return;
        }

        estadoElemento.innerHTML =
            mensaje;
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR RUTA
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
            `${longitudCliente},${latitudCliente}` +
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

                return;

            }


            const ruta =
                datos.routes[0];


            ultimaRutaLatitud =
                deliveryLatitud;

            ultimaRutaLongitud =
                deliveryLongitud;


            if (rutaDelivery) {

                mapa.removeLayer(
                    rutaDelivery
                );

            }


            rutaDelivery =
                L.geoJSON(
                    ruta.geometry,
                    {
                        style: {

                            weight: 5,

                            opacity: 0.8

                        }
                    }
                ).addTo(mapa);


            /*
            |--------------------------------------------------------------------------
            | DISTANCIA
            |--------------------------------------------------------------------------
            */

            const distancia =
                ruta.distance;

            const duracion =
                ruta.duration;


            if (distanciaElemento) {

                distanciaElemento.textContent =
                    formatearDistancia(
                        distancia
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | ETA
            |--------------------------------------------------------------------------
            */

            const minutos =
                Math.ceil(
                    duracion / 60
                );


            if (tiempoElemento) {

                tiempoElemento.textContent =
                    minutos <= 1
                        ? 'Menos de 1 min'
                        : `${minutos} min`;

            }


            /*
            |--------------------------------------------------------------------------
            | MENSAJE
            |--------------------------------------------------------------------------
            */

            if (distancia <= 50) {

                actualizarEstado(
                    '📍 El delivery está llegando al cliente'
                );

            } else if (distancia <= 300) {

                actualizarEstado(
                    '🟢 El delivery está muy cerca'
                );

            } else if (distancia <= 1000) {

                actualizarEstado(
                    '🟡 El delivery está cerca'
                );

            } else {

                actualizarEstado(
                    '🚴 El delivery está en camino'
                );

            }


        } catch (error) {

            console.error(
                'Administrador: error obteniendo ruta.',
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
            | SIN GPS
            |--------------------------------------------------------------------------
            */

            if (!ubicacion) {

                if (marcadorDelivery) {

                    mapa.removeLayer(
                        marcadorDelivery
                    );

                    marcadorDelivery = null;

                }


                if (rutaDelivery) {

                    mapa.removeLayer(
                        rutaDelivery
                    );

                    rutaDelivery = null;

                }


                if (distanciaElemento) {

                    distanciaElemento.textContent =
                        '--';

                }


                if (tiempoElemento) {

                    tiempoElemento.textContent =
                        '--';

                }


                actualizarEstado(
                    '📡 Esperando la ubicación del repartidor...'
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
            | MARCADOR DELIVERY
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
                                iconoDelivery
                        }
                    ).addTo(mapa);


                marcadorDelivery.bindPopup(`
                    <div class="admin-leaflet-popup">

                        <strong>
                            Delivery
                        </strong>

                        <span>
                            Ubicación actual
                        </span>

                    </div>
                `);

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
            | AJUSTAR MAPA
            |--------------------------------------------------------------------------
            */

            const limites =
                L.latLngBounds([
                    [
                        latitudCliente,
                        longitudCliente
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
                        45,
                        45
                    ],

                    maxZoom: 16
                }
            );


            /*
            |--------------------------------------------------------------------------
            | ACTUALIZAR RUTA
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


    window.mapaEntregaAdmin =
        mapa;


    console.log(
        'Sabor Express: mapa Admin iniciado.',
        {
            pedidoId,
            estadoPedido
        }
    );

});