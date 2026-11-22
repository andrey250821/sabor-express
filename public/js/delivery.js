document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const liveStateUrl = body.dataset.deliveryLiveStateUrl;

    const pendingOrdersLink = document.getElementById('delivery-nav-my-orders');
    const pendingBadge = document.getElementById('delivery-nav-pending-badge');
    const pendingCount = document.getElementById('delivery-nav-pending-count');
    const isViewingMyOrders = pendingOrdersLink?.dataset.isViewingMisPedidos === '1';

    const updatePendingIndicator = (value) => {
        const count = Math.max(0, Number.parseInt(value, 10) || 0);
        const hasPending = count > 0;

        if (pendingBadge) {
            pendingBadge.hidden = !hasPending;
            pendingBadge.title = count + (
                count === 1 ? ' pedido asignado pendiente' : ' pedidos asignados pendientes'
            );
        }

        if (pendingCount) pendingCount.textContent = count > 99 ? '99+' : String(count);

        pendingOrdersLink?.classList.toggle(
            'delivery-nav-link--pending',
            hasPending && !isViewingMyOrders
        );

        pendingBadge?.classList.toggle(
            'delivery-nav-pending-badge--static',
            Boolean(isViewingMyOrders)
        );
    };

    const setText = (id, value) => {
        const element = document.getElementById(id);
        if (element) element.textContent = String(value);
    };

    const updateDashboardCounters = (data) => {
        const dashboard = document.querySelector('[data-delivery-live-dashboard]');

        setText('delivery-dashboard-queue-floating', data.pedidosEnCola);
        setText('delivery-dashboard-active-floating', data.misPedidos);
        setText('delivery-dashboard-queue-stat', data.pedidosEnCola);
        setText('delivery-dashboard-orders-stat', data.misPedidos);

        if (dashboard?.dataset.liveToday === '1') {
            setText('delivery-dashboard-delivered-stat', data.entregasDia);
        }

        const title = document.getElementById('delivery-dashboard-status-title');
        const description = document.getElementById('delivery-dashboard-status-description');
        const pulse = document.getElementById('delivery-dashboard-status-pulse');

        if (title && description && pulse) {
            const count = Math.max(0, Number(data.pedidosEnCola) || 0);
            const hasQueue = count > 0;

            title.textContent = hasQueue
                ? count + (count === 1 ? ' pedido en cola' : ' pedidos en cola')
                : 'No hay pedidos en cola';

            description.textContent = hasQueue
                ? 'Hay pedidos listos en cocina esperando asignación automática.'
                : 'Actualmente no hay pedidos esperando asignación.';

            pulse.classList.toggle('warning', hasQueue);
            pulse.classList.toggle('success', !hasQueue);
        }
    };

    const updateQueueCounters = (data) => {
        if (!document.querySelector('[data-delivery-live-queue]')) return;

        const count = Math.max(0, Number(data.pedidosEnCola) || 0);
        const hasQueue = count > 0;

        setText('delivery-queue-header-count', count);
        setText('delivery-queue-main-count', count);
        setText('delivery-queue-main-label', count === 1 ? 'pedido en cola' : 'pedidos en cola');
        setText(
            'delivery-queue-summary',
            hasQueue ? 'Hay pedidos esperando asignación' : 'La cola está vacía'
        );

        const message = document.getElementById('delivery-queue-state-message');
        if (message) {
            message.className = hasQueue ? 'alert alert-info mb-0' : 'alert alert-success mb-0';
            message.innerHTML = hasQueue
                ? '<i class="bi bi-info-circle-fill me-2"></i>Cuando termines tu entrega actual, el sistema te asignará automáticamente el siguiente pedido de la cola.'
                : '<i class="bi bi-check-circle-fill me-2"></i>No hay pedidos esperando asignación en este momento.';
        }
    };

    let refreshingLiveState = false;

    const refreshDeliveryLiveState = async () => {
        if (refreshingLiveState || !liveStateUrl || document.visibilityState === 'hidden') return;

        refreshingLiveState = true;
        try {
            const response = await fetch(liveStateUrl, {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) return;

            const data = await response.json();

            if (Number.isFinite(Number(data.cantidad))) updatePendingIndicator(data.cantidad);

            if (Number.isFinite(Number(data.pedidosEnCola)) && Number.isFinite(Number(data.misPedidos))) {
                updateDashboardCounters(data);
                updateQueueCounters(data);
            }
        } catch (error) {
            console.warn('No se pudo actualizar el estado del Delivery.', error);
        } finally {
            refreshingLiveState = false;
        }
    };

    if (liveStateUrl) {
        refreshDeliveryLiveState();
        window.setInterval(refreshDeliveryLiveState, 5000);
        window.addEventListener('focus', refreshDeliveryLiveState);

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') refreshDeliveryLiveState();
        });
    }

    document.getElementById('deliveryQueueRefresh')?.addEventListener('click', async (event) => {
        const button = event.currentTarget;
        if (button.disabled) return;

        button.disabled = true;
        button.classList.add('delivery-refreshing');
        const icon = button.querySelector('i');
        icon?.classList.add('delivery-refresh-spin');

        await refreshDeliveryLiveState();

        window.setTimeout(() => {
            button.disabled = false;
            button.classList.remove('delivery-refreshing');
            icon?.classList.remove('delivery-refresh-spin');
        }, 300);
    });

    /* =========================================================
       MIS PEDIDOS: ACTUALIZAR SOLO LOS PANELES
       ========================================================= */

    const liveOrdersRoot = document.querySelector('[data-delivery-live-orders]');

    if (liveOrdersRoot) {
        const liveOrdersUrl = liveOrdersRoot.dataset.liveUrl;
        let refreshingOrders = false;
        let lastSignature = '';

        const bindControls = (root) => {
            root.querySelectorAll('form[data-disable-on-submit]').forEach((form) => {
                if (form.dataset.deliverySubmitBound === '1') return;
                form.dataset.deliverySubmitBound = '1';

                form.addEventListener('submit', () => {
                    const button = form.querySelector('button[type="submit"]');
                    if (!button || button.disabled) return;

                    button.disabled = true;
                    button.classList.add('delivery-button-loading');
                    button.dataset.originalHtml = button.innerHTML;
                    button.innerHTML = '<span class="delivery-spinner" aria-hidden="true"></span> Procesando...';
                });
            });

            root.querySelectorAll('[data-delivery-interactive]').forEach((element) => {
                if (element.dataset.deliveryInteractiveBound === '1') return;
                element.dataset.deliveryInteractiveBound = '1';

                element.addEventListener('pointerdown', () => element.classList.add('delivery-is-pressed'));
                const removePressed = () => element.classList.remove('delivery-is-pressed');
                element.addEventListener('pointerup', removePressed);
                element.addEventListener('pointerleave', removePressed);
                element.addEventListener('pointercancel', removePressed);

                element.addEventListener('pointerdown', (event) => {
                    if (element.disabled) return;

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
                    window.setTimeout(() => ripple.remove(), 550);
                });
            });

            root.querySelectorAll('[data-delivery-animate]').forEach((element) => {
                element.classList.add('delivery-reveal-visible');
            });
        };

        const updatePanel = (panelId, html) => {
            const current = document.getElementById(panelId);
            if (!current) return false;

            const template = document.createElement('template');
            template.innerHTML = html;
            const next = template.content.querySelector('#' + panelId);
            if (!next) return false;

            // Reemplazar solo el interior conserva la pestaña activa y el contenedor.
            current.innerHTML = next.innerHTML;
            return true;
        };

        const refreshMyOrders = async () => {
            if (refreshingOrders || !liveOrdersUrl || document.visibilityState === 'hidden') return;

            const focusedElement = document.activeElement;
            if (
                focusedElement &&
                liveOrdersRoot.contains(focusedElement) &&
                focusedElement.closest('form, button, a, input, select, textarea')
            ) {
                return;
            }

            refreshingOrders = true;
            const scrollPosition = window.scrollY;

            try {
                const response = await fetch(liveOrdersUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) return;

                const data = await response.json();
                if (typeof data.firma !== 'string' || data.firma === lastSignature) return;

                if (
                    !updatePanel('panel-asignados', data.panelAsignados) ||
                    !updatePanel('panel-entregados', data.panelEntregados)
                ) {
                    return;
                }

                lastSignature = data.firma;
                setText('delivery-live-assigned-tab-count', data.cantidadAsignados);
                setText('delivery-live-delivered-tab-count', data.cantidadEntregados);
                bindControls(liveOrdersRoot);

                // Mantener al Delivery exactamente en su desplazamiento actual.
                window.requestAnimationFrame(() => {
                    window.scrollTo({ top: scrollPosition, behavior: 'auto' });
                });
            } catch (error) {
                console.warn('No se pudieron actualizar los pedidos en segundo plano.', error);
            } finally {
                refreshingOrders = false;
            }
        };

        refreshMyOrders();
        window.setInterval(refreshMyOrders, 6000);
        window.addEventListener('focus', refreshMyOrders);
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
            if (window.innerWidth < 992) closeSidebar();
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) closeSidebar();
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

            panels.forEach((panel) => panel.classList.remove('active'));
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');
            document.getElementById(targetId)?.classList.add('active');
        });
    });

    /* =========================================================
       CONTROLES INTERACTIVOS INICIALES
       ========================================================= */

    const bindInitialControls = (root) => {
        root.querySelectorAll('form[data-disable-on-submit]').forEach((form) => {
            if (form.dataset.deliverySubmitBound === '1') return;
            form.dataset.deliverySubmitBound = '1';

            form.addEventListener('submit', () => {
                const button = form.querySelector('button[type="submit"]');
                if (!button || button.disabled) return;

                button.disabled = true;
                button.classList.add('delivery-button-loading');
                button.dataset.originalHtml = button.innerHTML;
                button.innerHTML = '<span class="delivery-spinner" aria-hidden="true"></span> Procesando...';
            });
        });

        root.querySelectorAll('[data-delivery-interactive]').forEach((element) => {
            if (element.dataset.deliveryInteractiveBound === '1') return;
            element.dataset.deliveryInteractiveBound = '1';

            element.addEventListener('pointerdown', () => element.classList.add('delivery-is-pressed'));
            const removePressed = () => element.classList.remove('delivery-is-pressed');
            element.addEventListener('pointerup', removePressed);
            element.addEventListener('pointerleave', removePressed);
            element.addEventListener('pointercancel', removePressed);

            element.addEventListener('pointerdown', (event) => {
                if (element.disabled) return;

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
                window.setTimeout(() => ripple.remove(), 550);
            });
        });
    };

    bindInitialControls(document);

    /* =========================================================
       REVELADO DE ELEMENTOS AL ENTRAR EN PANTALLA
       ========================================================= */

    const animated = document.querySelectorAll('[data-delivery-animate]');

    if ('IntersectionObserver' in window && animated.length > 0) {
        const observer = new IntersectionObserver(
            (entries, instance) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
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
        animated.forEach((element) => element.classList.add('delivery-reveal-visible'));
    }

    /* =========================================================
       ALERTAS: CERRAR AUTOMÁTICAMENTE
       ========================================================= */

    window.setTimeout(() => {
        document.querySelectorAll('.delivery-alert').forEach((alert) => {
            if (!alert.classList.contains('show')) return;
            alert.classList.remove('show');
            window.setTimeout(() => alert.remove(), 250);
        });
    }, 6000);
});
