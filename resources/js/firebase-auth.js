import { getAuth, signInWithCustomToken } from 'firebase/auth';

import app from './firebase';

const auth = getAuth(app);
const pendingAuthentications = new Map();

/**
 * Solicita a Laravel un token de Firebase limitado al pedido actual.
 * El servidor comprueba la sesión, el rol y la relación con el pedido.
 */
export function autenticarFirebaseParaPedido(tokenUrl) {
    if (!tokenUrl) {
        return Promise.reject(
            new Error('Falta la URL de autenticación segura para el seguimiento.')
        );
    }

    if (pendingAuthentications.has(tokenUrl)) {
        return pendingAuthentications.get(tokenUrl);
    }

    const authentication = (async () => {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        if (!csrfToken) {
            throw new Error(
                'No se encontró el token de seguridad de Laravel. Recarga la página e inténtalo nuevamente.'
            );
        }

        const response = await fetch(tokenUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({}),
        });

        let data = {};

        try {
            data = await response.json();
        } catch {
            // La respuesta no contenía JSON válido.
        }

        if (!response.ok) {
            throw new Error(
                data.message || `Laravel rechazó la autenticación (HTTP ${response.status}).`
            );
        }

        if (!data.token) {
            throw new Error('Laravel no devolvió el token de autenticación de Firebase.');
        }

        await signInWithCustomToken(auth, data.token);

        return auth;
    })();

    pendingAuthentications.set(tokenUrl, authentication);

    authentication.catch(() => {
        pendingAuthentications.delete(tokenUrl);
    });

    return authentication;
}
