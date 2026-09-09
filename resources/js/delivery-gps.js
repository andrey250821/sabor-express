import {
    ref,
    set,
    remove,
} from 'firebase/database';

import { database } from './firebase';


document.addEventListener('DOMContentLoaded', () => {

    const contenedor = document.getElementById('delivery-gps');

    // Si no estamos en una página que use GPS, no hacemos nada.
    if (!contenedor) {
        return;
    }

    const pedidoId = contenedor.dataset.pedidoId;
    const deliveryId = contenedor.dataset.deliveryId;
    const gpsActivo = contenedor.dataset.gpsActivo === '1';

    if (!pedidoId || !deliveryId) {
        console.error(
            'GPS Delivery: faltan pedido_id o delivery_id.'
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Referencia Firebase
    |--------------------------------------------------------------------------
    |
    | Cada pedido tendrá su propia ubicación:
    |
    | delivery_locations
    | └── pedido_id
    |     ├── delivery_id
    |     ├── latitud
    |     ├── longitud
    |     └── actualizado_en
    |
    */

    const ubicacionRef = ref(
        database,
        `delivery_locations/${pedidoId}`
    );


    /*
    |--------------------------------------------------------------------------
    | Mostrar estado
    |--------------------------------------------------------------------------
    */

    const estado = document.getElementById(
        'delivery-gps-status'
    );


    function mostrarEstado(
        mensaje,
        tipo = 'normal'
    ) {

        if (!estado) {
            return;
        }

        estado.textContent = mensaje;

        estado.dataset.tipo = tipo;
    }


    /*
    |--------------------------------------------------------------------------
    | Guardar ubicación en Firebase
    |--------------------------------------------------------------------------
    */

    async function guardarUbicacion(position) {

        const latitud = position.coords.latitude;
        const longitud = position.coords.longitude;
        const precision = position.coords.accuracy;

        try {

            await set(ubicacionRef, {

                delivery_id: Number(deliveryId),

                pedido_id: Number(pedidoId),

                latitud: latitud,

                longitud: longitud,

                precision: precision,

                actualizado_en: Date.now(),

            });

            mostrarEstado(
                'Ubicación actualizada',
                'success'
            );

            console.log(
                'GPS enviado a Firebase:',
                {
                    pedidoId,
                    deliveryId,
                    latitud,
                    longitud,
                    precision,
                }
            );

        } catch (error) {

            console.error(
                'GPS Delivery: error al guardar en Firebase.',
                error
            );

            mostrarEstado(
                'No se pudo actualizar la ubicación',
                'error'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Error de GPS
    |--------------------------------------------------------------------------
    */

    function manejarError(error) {

        console.error(
            'GPS Delivery:',
            error
        );

        switch (error.code) {

            case error.PERMISSION_DENIED:

                mostrarEstado(
                    'Permiso de ubicación denegado',
                    'error'
                );

                break;


            case error.POSITION_UNAVAILABLE:

                mostrarEstado(
                    'Ubicación no disponible',
                    'error'
                );

                break;


            case error.TIMEOUT:

                mostrarEstado(
                    'Tiempo de espera agotado',
                    'error'
                );

                break;


            default:

                mostrarEstado(
                    'No se pudo obtener la ubicación',
                    'error'
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Eliminar ubicación
    |--------------------------------------------------------------------------
    */

    async function eliminarUbicacion() {

        try {

            await remove(ubicacionRef);

            console.log(
                'Ubicación del delivery eliminada de Firebase.'
            );

        } catch (error) {

            console.error(
                'No se pudo eliminar la ubicación:',
                error
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | GPS NO ACTIVO
    |--------------------------------------------------------------------------
    */

    if (!gpsActivo) {

        mostrarEstado(
            'GPS detenido',
            'normal'
        );

        // Si el pedido ya no está en camino,
        // eliminamos cualquier ubicación anterior.
        eliminarUbicacion();

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Verificar navegador
    |--------------------------------------------------------------------------
    */

    if (!navigator.geolocation) {

        mostrarEstado(
            'Este navegador no permite ubicación GPS',
            'error'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | GPS ACTIVO
    |--------------------------------------------------------------------------
    */

    mostrarEstado(
        'Obteniendo ubicación...',
        'normal'
    );


    const opciones = {

        enableHighAccuracy: true,

        maximumAge: 5000,

        timeout: 15000,

    };


    /*
    |--------------------------------------------------------------------------
    | Escuchar ubicación continuamente
    |--------------------------------------------------------------------------
    */

    const watchId = navigator.geolocation.watchPosition(

        guardarUbicacion,

        manejarError,

        opciones

    );


    /*
    |--------------------------------------------------------------------------
    | Guardar referencia global
    |--------------------------------------------------------------------------
    */

    window.deliveryGpsWatchId = watchId;


    console.log(
        'GPS Delivery iniciado.',
        {
            pedidoId,
            deliveryId,
            watchId,
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Detener GPS cuando se abandona la página
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'beforeunload',
        () => {

            navigator.geolocation.clearWatch(
                watchId
            );

        }
    );

});