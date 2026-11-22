(function () {
    'use strict';

    function iniciarMapaDashboard() {
        const elementoMapa = document.getElementById('cliente-dashboard-map');

        if (!elementoMapa) {
            return;
        }

        const estadoRuta = document.getElementById('cliente-dashboard-route-status');
        const botonRuta = document.getElementById('cliente-dashboard-como-llegar');

        function mostrarEstado(mensaje, esError) {
            if (!estadoRuta) return;

            estadoRuta.textContent = mensaje;
            estadoRuta.classList.toggle('is-error', Boolean(esError));
            estadoRuta.classList.toggle('is-success', !esError);
        }

        if (typeof window.L === 'undefined') {
            mostrarEstado(
                'No se pudo cargar el mapa. Comprueba tu conexión e inténtalo nuevamente.',
                true
            );
            if (botonRuta) botonRuta.disabled = true;
            return;
        }

        const numero = function (valor) {
            if (valor === null || valor === undefined || valor === '') {
                return null;
            }

            const resultado = Number(valor);
            return Number.isFinite(resultado) ? resultado : null;
        };

        const restaurante = {
            nombre: elementoMapa.dataset.restaurantName || 'Sabor Express',
            lat: numero(elementoMapa.dataset.restaurantLat),
            lng: numero(elementoMapa.dataset.restaurantLng)
        };

        const tieneRestaurante =
            elementoMapa.dataset.restaurantHasLocation === '1'
            && restaurante.lat !== null
            && restaurante.lng !== null;

        const clienteGuardado = {
            lat: numero(elementoMapa.dataset.clientLat),
            lng: numero(elementoMapa.dataset.clientLng),
            direccion: elementoMapa.dataset.clientAddress || ''
        };

        const tieneClienteGuardado =
            elementoMapa.dataset.clientHasLocation === '1'
            && clienteGuardado.lat !== null
            && clienteGuardado.lng !== null;

        const baseOSRM = (
            elementoMapa.dataset.osrmUrl || 'https://router.project-osrm.org'
        ).replace(/\/+$/, '');

        const centroRespaldo = [-17.3935, -66.1570];

        let mapa = null;
        let marcadorClienteActual = null;
        let capaRuta = null;
        let solicitudEnCurso = false;

        function crearContenidoPopup(titulo, detalle) {
            const contenedor = document.createElement('div');
            const encabezado = document.createElement('strong');
            encabezado.textContent = titulo;
            contenedor.appendChild(encabezado);

            if (detalle) {
                const descripcion = document.createElement('div');
                descripcion.textContent = detalle;
                contenedor.appendChild(descripcion);
            }

            return contenedor;
        }

        function crearIcono(clase, icono) {
            return L.divIcon({
                className: 'sx-dashboard-marker-wrapper',
                html: '<span class="sx-dashboard-marker ' + clase + '">' +
                    '<i class="bi ' + icono + '"></i></span>',
                iconSize: [48, 52],
                iconAnchor: [24, 48],
                popupAnchor: [0, -43]
            });
        }

        const puntosIniciales = [];

        mapa = L.map(elementoMapa, {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView(centroRespaldo, 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(mapa);

        if (tieneRestaurante) {
            const puntoRestaurante = [restaurante.lat, restaurante.lng];

            L.marker(puntoRestaurante, {
                icon: crearIcono('sx-marker-restaurant', 'bi-shop-window'),
                title: restaurante.nombre
            })
                .addTo(mapa)
                .bindPopup(crearContenidoPopup(
                    restaurante.nombre,
                    'Ubicación del restaurante'
                ));

            puntosIniciales.push(puntoRestaurante);
        }

        if (tieneClienteGuardado) {
            const puntoCliente = [clienteGuardado.lat, clienteGuardado.lng];

            L.marker(puntoCliente, {
                icon: crearIcono('sx-marker-client', 'bi-person-fill'),
                title: 'Última ubicación guardada'
            })
                .addTo(mapa)
                .bindPopup(crearContenidoPopup(
                    'Tu última ubicación guardada',
                    clienteGuardado.direccion
                ));

            puntosIniciales.push(puntoCliente);
        }

        if (puntosIniciales.length > 1) {
            mapa.fitBounds(L.latLngBounds(puntosIniciales).pad(0.18), {
                maxZoom: 16
            });
        } else if (puntosIniciales.length === 1) {
            mapa.setView(puntosIniciales[0], 16);
        }

        window.setTimeout(function () {
            mapa.invalidateSize();
        }, 300);

        async function trazarRuta(origen, origenTexto, avisoPrevio) {
            if (!tieneRestaurante) {
                mostrarEstado(
                    'El restaurante todavía no tiene coordenadas configuradas. Contacta con la administración.',
                    true
                );
                return;
            }

            if (capaRuta) {
                mapa.removeLayer(capaRuta);
                capaRuta = null;
            }

            const url =
                baseOSRM + '/route/v1/driving/'
                + origen.lng + ',' + origen.lat + ';'
                + restaurante.lng + ',' + restaurante.lat
                + '?overview=full&geometries=geojson';

            mostrarEstado(
                avisoPrevio || 'Calculando la ruta hasta ' + restaurante.nombre + '...',
                false
            );

            const respuesta = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });

            if (!respuesta.ok) {
                throw new Error('El servicio de rutas respondió con el código ' + respuesta.status + '.');
            }

            const datos = await respuesta.json();
            const ruta = datos.routes && datos.routes[0];

            if (
                datos.code !== 'Ok'
                || !ruta
                || !ruta.geometry
                || !Array.isArray(ruta.geometry.coordinates)
                || ruta.geometry.coordinates.length < 2
            ) {
                throw new Error('No se encontró una ruta por carretera entre tu ubicación y el restaurante.');
            }

            capaRuta = L.geoJSON({
                type: 'Feature',
                properties: {},
                geometry: ruta.geometry
            }, {
                style: {
                    color: '#ff4b87',
                    weight: 6,
                    opacity: 0.92,
                    lineCap: 'round',
                    lineJoin: 'round'
                }
            }).addTo(mapa);

            if (marcadorClienteActual) {
                mapa.removeLayer(marcadorClienteActual);
            }

            marcadorClienteActual = L.marker([origen.lat, origen.lng], {
                icon: crearIcono('sx-marker-current', 'bi-crosshair'),
                title: 'Punto de partida'
            })
                .addTo(mapa)
                .bindPopup(crearContenidoPopup('Tu punto de partida', origenTexto));

            mapa.fitBounds(capaRuta.getBounds().pad(0.16), {
                maxZoom: 16
            });
            mostrarEstado('', false);
        }

        function obtenerMensajeErrorGPS(error) {
            if (!error) {
                return 'No fue posible obtener tu ubicación actual.';
            }

            if (error.code === 1) {
                return 'No se concedió permiso para acceder a tu ubicación.';
            }

            if (error.code === 2) {
                return 'El dispositivo no pudo determinar tu ubicación.';
            }

            if (error.code === 3) {
                return 'La solicitud de ubicación tardó demasiado tiempo.';
            }

            return 'No fue posible obtener tu ubicación actual.';
        }

        function usarUltimaUbicacionSiExiste(mensajeError) {
            if (!tieneClienteGuardado) {
                mostrarEstado(
                    mensajeError + ' Permite el acceso al GPS o abre la aplicación mediante una conexión segura.',
                    true
                );
                return;
            }

            const aviso =
                mensajeError
                + ' Se utilizará la última ubicación que guardaste en un pedido.';

            trazarRuta({
                lat: clienteGuardado.lat,
                lng: clienteGuardado.lng
            }, 'tu última ubicación guardada', aviso).catch(function (error) {
                console.error('Error al calcular la ruta:', error);
                mostrarEstado(error.message || 'No se pudo calcular la ruta.', true);
            });
        }

        if (botonRuta) {
            if (!tieneRestaurante) {
                botonRuta.disabled = true;
            }

            botonRuta.addEventListener('click', function () {
                if (solicitudEnCurso || !tieneRestaurante) {
                    return;
                }

                if (!navigator.geolocation || typeof navigator.geolocation.getCurrentPosition !== 'function') {
                    usarUltimaUbicacionSiExiste(
                        'Este navegador no permite obtener la ubicación automática.'
                    );
                    return;
                }

                solicitudEnCurso = true;
                botonRuta.disabled = true;
                botonRuta.innerHTML =
                    '<i class="bi bi-hourglass-split"></i><span>Obteniendo ubicación...</span>';

                navigator.geolocation.getCurrentPosition(
                    function (posicion) {
                        const origen = {
                            lat: posicion.coords.latitude,
                            lng: posicion.coords.longitude
                        };

                        trazarRuta(origen, 'tu ubicación actual', '').catch(function (error) {
                            console.error('Error al calcular la ruta:', error);
                            mostrarEstado(
                                error.message || 'No se pudo calcular la ruta. Inténtalo nuevamente.',
                                true
                            );
                        }).finally(function () {
                            solicitudEnCurso = false;
                            botonRuta.disabled = false;
                            botonRuta.innerHTML =
                                '<i class="bi bi-sign-turn-right-fill"></i><span>Cómo llegar al restaurante</span>';
                        });
                    },
                    function (error) {
                        const mensaje = obtenerMensajeErrorGPS(error);

                        usarUltimaUbicacionSiExiste(mensaje);

                        solicitudEnCurso = false;
                        botonRuta.disabled = false;
                        botonRuta.innerHTML =
                            '<i class="bi bi-sign-turn-right-fill"></i><span>Cómo llegar al restaurante</span>';
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 20000,
                        maximumAge: 0
                    }
                );
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciarMapaDashboard, { once: true });
    } else {
        iniciarMapaDashboard();
    }
})();
