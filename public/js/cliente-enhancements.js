document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('.cliente-app');

    if (!root) {
        return;
    }

    /*
     * Ripple táctil: responde inmediatamente al clic sin cambiar
     * la lógica del formulario o enlace.
     */
    const rippleSelectors = [
        '.cliente-app .btn',
        '.cliente-app .cliente-nav-link',
        '.cliente-app .cliente-logo',
        '.cliente-app .cliente-secondary-btn',
        '.cliente-app .cliente-btn-agregar',
        '.cliente-app .cliente-cantidad-btn',
        '.cliente-app .cliente-eliminar-btn',
        '.cliente-app .cliente-mis-pedidos-btn-productos',
        '.cliente-app .cliente-pedido-show-btn-volver',
        '.cliente-app .cliente-pedido-show-btn-mapa'
    ];

    root.querySelectorAll(rippleSelectors.join(',')).forEach((element) => {
        if (element.dataset.clienteRippleReady === '1') {
            return;
        }

        element.dataset.clienteRippleReady = '1';

        element.addEventListener('click', (event) => {
            if (
                window.matchMedia('(prefers-reduced-motion: reduce)').matches
            ) {
                return;
            }

            const rect = element.getBoundingClientRect();
            const ripple = document.createElement('span');

            ripple.className = 'cliente-ripple';
            ripple.style.left = String(event.clientX - rect.left) + 'px';
            ripple.style.top = String(event.clientY - rect.top) + 'px';

            element.appendChild(ripple);

            window.setTimeout(() => {
                ripple.remove();
            }, 620);
        });
    });

    /*
     * Aparición progresiva de bloques al entrar en pantalla.
     */
    if (
        'IntersectionObserver' in window &&
        !window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
        const revealItems = root.querySelectorAll(
            [
                '.cliente-dashboard-card',
                '.cliente-welcome-panel',
                '.cliente-info-panel',
                '.cliente-producto-card',
                '.cliente-carrito-item',
                '.cliente-mis-pedidos-card',
                '.cliente-pedido-show-card',
                '.cliente-calificaciones-review-card',
                '.cliente-notificaciones-page .card',
                '.cliente-configuracion-page .card'
            ].join(',')
        );

        revealItems.forEach((item) => {
            item.classList.add('cliente-reveal-item');
        });

        const observer = new IntersectionObserver((entries, instance) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('cliente-reveal-visible');
                instance.unobserve(entry.target);
            });
        }, {
            threshold: 0.08,
            rootMargin: '0px 0px -24px 0px'
        });

        revealItems.forEach((item) => observer.observe(item));
    }

    /*
     * Campo de búsqueda: refuerzo visual al escribir.
     */
    root.querySelectorAll('input[name="buscar"]').forEach((input) => {
        input.addEventListener('focus', () => {
            input.closest('.cliente-busqueda')?.classList.add(
                'cliente-busqueda-activa'
            );
        });

        input.addEventListener('blur', () => {
            input.closest('.cliente-busqueda')?.classList.remove(
                'cliente-busqueda-activa'
            );
        });
    });
});
