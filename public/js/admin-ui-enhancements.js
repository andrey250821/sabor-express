/* ==========================================================================
   SABOR EXPRESS · INTERACCIÓN ADMIN
   Efectos ligeros y seguros para botones, formularios y elementos interactivos.
   No modifica rutas, lógica de negocio ni datos.
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    const adminContent = document.querySelector('.admin-content');

    if (!adminContent) {
        return;
    }

    const interactiveSelectors = [
        '.admin-content .btn:not(.btn-close)',
        '.admin-content .btn-producto',
        '.admin-content .btn-delivery',
        '.admin-content .cliente-btn',
        '.admin-content .btn-categoria-editar',
        '.admin-content .btn-categoria-eliminar',
        '.admin-content .btn-categoria-inactivar',
        '.admin-content .btn-categoria-activar'
    ].join(',');

    /*
     * Efecto ripple al hacer click.
     * Solo es visual; no detiene ni reemplaza la acción original.
     */
    adminContent.querySelectorAll(interactiveSelectors).forEach((element) => {
        element.addEventListener('click', (event) => {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            const rect = element.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height) * 1.35;
            const ripple = document.createElement('span');

            ripple.className = 'se-ripple';
            ripple.style.width = `${size}px`;
            ripple.style.height = `${size}px`;
            ripple.style.left = `${event.clientX - rect.left - size / 2}px`;
            ripple.style.top = `${event.clientY - rect.top - size / 2}px`;

            const computedPosition = window.getComputedStyle(element).position;

            if (computedPosition === 'static') {
                element.style.position = 'relative';
            }

            element.style.overflow = 'hidden';
            element.appendChild(ripple);

            window.setTimeout(() => ripple.remove(), 650);
        });
    });

    /*
     * Feedback visual al enviar formularios POST/PUT/PATCH/DELETE.
     * Los formularios GET quedan intactos porque se usan para filtros/búsqueda.
     */
    adminContent.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented) {
                return;
            }

            const methodInput = form.querySelector('input[name="_method"]');
            const method = (
                methodInput?.value ||
                form.getAttribute('method') ||
                'GET'
            ).toUpperCase();

            if (method === 'GET') {
                return;
            }

            form.classList.add('se-submitting');

            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
                button.setAttribute('aria-busy', 'true');
                button.disabled = true;
            });
        });
    });

    /*
     * Cuando Laravel devuelve errores de validación, enfocar el primer campo
     * inválido facilita corregir el formulario.
     */
    const firstInvalid = adminContent.querySelector(
        '.is-invalid, [aria-invalid="true"]'
    );

    if (firstInvalid && typeof firstInvalid.focus === 'function') {
        window.setTimeout(() => firstInvalid.focus({preventScroll: true}), 120);
    }
});
