document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;


    const pendingOrdersLink = document.getElementById('delivery-nav-my-orders');

    if (pendingOrdersLink) {
        const pendingBadge = document.getElementById('delivery-nav-pending-badge');
        const pendingCount = document.getElementById('delivery-nav-pending-count');
        const pendingCountUrl = pendingOrdersLink.dataset.pendingCountUrl;
        const isViewingMyOrders = pendingOrdersLink.dataset.isViewingMisPedidos === '1';

        const updatePendingOrdersIndicator = (value) => {
            const count = Math.max(0, Number.parseInt(value, 10) || 0);
            const hasPendingOrders = count > 0;

            if (pendingBadge) {
                pendingBadge.hidden = !hasPendingOrders;
                pendingBadge.title = count + (
                    count === 1
                        ? ' pedido asignado pendiente'
                        : ' pedidos asignados pendientes'
                );
            }

            if (pendingCount) {
                pendingCount.textContent = count > 99 ? '99+' : String(count);
            }

            pendingOrdersLink.classList.toggle(
                'delivery-nav-link--pending',
                hasPendingOrders && !isViewingMyOrders
            );

            pendingBadge?.classList.toggle(
                'delivery-nav-pending-badge--static',
                isViewingMyOrders
            );

            pendingOrdersLink.setAttribute(
                'aria-label',
                'Mis pedidos' + (
                    hasPendingOrders
                        ? ', ' + count + (count === 1 ? ' pedido nuevo asignado' : ' pedidos nuevos asignados')
                        : ''
                )
            );
        };

        const refreshPendingOrdersIndicator = async () => {
            if (!pendingCountUrl || document.visibilityState === 'hidden') {
                return;
            }

            try {
                const response = await fetch(pendingCountUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();

                if (Number.isFinite(Number(data.cantidad))) {
                    updatePendingOrdersIndicator(data.cantidad);
                }
            } catch (error) {
                console.warn('No se pudo actualizar el aviso de pedidos asignados.', error);
            }
        };

        refreshPendingOrdersIndicator();
        window.setInterval(refreshPendingOrdersIndicator, 8000);
        window.addEventListener('focus', refreshPendingOrdersIndicator);

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                refreshPendingOrdersIndicator();
            }
        });
    }


    /* =========================================================
       SIDEBAR RESPONSIVE
       ========================================================= */

    const sidebar = document.getElementById('deliverySidebar');
    const overlay = document.getElementById('deliveryOverlay');
    const menuToggle = document.getElementById('deliveryMenuToggle');
    const closeButton = document.getElementById('deliverySidebarClose');

    const closeSidebar = () => {
        sidebar?.classList.remove('show');
        overlay?.classList.remove('show');
        body.classList.remove('delivery-menu-open');
    };

    const openSidebar = () => {
        sidebar?.classList.add('show');
        overlay?.classList.add('show');
        body.classList.add('delivery-menu-open');
    };

    menuToggle?.addEventListener('click', openSidebar);
    closeButton?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    document.querySelectorAll('.delivery-nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });

    /* =========================================================
       PESTAÑAS DE MIS PEDIDOS
       ========================================================= */

    const tabs = document.querySelectorAll('.delivery-my-tab');
    const panels = document.querySelectorAll('.delivery-my-tab-panel');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const targetId = tab.dataset.target;

            tabs.forEach((item) => {
                item.classList.remove('active');
                item.setAttribute('aria-selected', 'false');
            });

            panels.forEach((panel) => {
                panel.classList.remove('active');
            });

            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');

            document.getElementById(targetId)?.classList.add('active');
        });
    });

    /* =========================================================
       BLOQUEAR DOBLE CLIC EN FORMULARIOS OPERATIVOS
       ========================================================= */

    document.querySelectorAll('form[data-disable-on-submit]').forEach((form) => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('button[type="submit"]');

            if (!button || button.disabled) {
                return;
            }

            button.disabled = true;
            button.classList.add('delivery-button-loading');

            const original = button.innerHTML;

            button.dataset.originalHtml = original;
            button.innerHTML =
                '<span class="delivery-spinner" aria-hidden="true"></span> Procesando...';
        });
    });

    /* =========================================================
       EFECTO DE PRESIÓN / CLICK
       ========================================================= */

    document.querySelectorAll('[data-delivery-interactive]').forEach((element) => {
        element.addEventListener('pointerdown', () => {
            element.classList.add('delivery-is-pressed');
        });

        const removePressed = () => {
            element.classList.remove('delivery-is-pressed');
        };

        element.addEventListener('pointerup', removePressed);
        element.addEventListener('pointerleave', removePressed);
        element.addEventListener('pointercancel', removePressed);
    });

    /* =========================================================
       RIPPLE / RETROALIMENTACIÓN VISUAL AL TOCAR
       ========================================================= */

    document.querySelectorAll('[data-delivery-interactive]').forEach((element) => {
        element.addEventListener('pointerdown', (event) => {
            if (element.disabled) {
                return;
            }

            const rect = element.getBoundingClientRect();
            const ripple = document.createElement('span');
            const size = Math.max(rect.width, rect.height);

            ripple.className = 'delivery-ripple';
            ripple.style.width = size + 'px';
            ripple.style.height = size + 'px';
            ripple.style.left = (event.clientX - rect.left) + 'px';
            ripple.style.top = (event.clientY - rect.top) + 'px';

            element.querySelectorAll('.delivery-ripple').forEach((item) => item.remove());
            element.appendChild(ripple);

            window.setTimeout(() => {
                ripple.remove();
            }, 550);
        });
    });

    /* =========================================================
       REVELADO DE ELEMENTOS AL ENTRAR EN PANTALLA
       ========================================================= */

    const animated = document.querySelectorAll('[data-delivery-animate]');

    if ('IntersectionObserver' in window && animated.length > 0) {
        const observer = new IntersectionObserver(
            (entries, instance) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('delivery-reveal-visible');
                    instance.unobserve(entry.target);
                });
            },
            {
                threshold: 0.08,
                rootMargin: '0px 0px -20px 0px',
            }
        );

        animated.forEach((element) => {
            element.classList.add('delivery-reveal');
            observer.observe(element);
        });
    } else {
        animated.forEach((element) => {
            element.classList.add('delivery-reveal-visible');
        });
    }

    /* =========================================================
       ALERTAS: CERRAR AUTOMÁTICAMENTE
       ========================================================= */

    window.setTimeout(() => {
        document.querySelectorAll('.delivery-alert').forEach((alert) => {
            if (!alert.classList.contains('show')) {
                return;
            }

            alert.classList.remove('show');

            window.setTimeout(() => {
                alert.remove();
            }, 250);
        });
    }, 6000);
});
