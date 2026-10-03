document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;

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
       BUSCADOR EN TIEMPO REAL POR GMAIL DEL CLIENTE
       ========================================================= */

    const searchInput = document.getElementById('deliveryGmailSearch');
    const searchCards = [...document.querySelectorAll('[data-delivery-search-card]')];
    const searchResult = document.getElementById('deliverySearchResult');

    const actualizarBusqueda = () => {
        if (!searchInput) {
            return;
        }

        const termino = searchInput.value.trim().toLowerCase();

        let visibles = 0;

        searchCards.forEach((card) => {
            const gmail = (card.dataset.gmail || '').toLowerCase();
            const coincide = termino === '' || gmail.includes(termino);

            card.hidden = !coincide;

            if (coincide) {
                visibles++;
            }
        });

        if (searchResult) {
            if (termino === '') {
                searchResult.textContent = 'Escribe el Gmail del cliente para filtrar al instante.';
            } else if (visibles === 0) {
                searchResult.textContent = 'No hay pedidos de este día asociados a ese Gmail.';
            } else {
                searchResult.textContent =
                    visibles === 1
                        ? '1 pedido encontrado.'
                        : visibles + ' pedidos encontrados.';
            }
        }

        document.querySelectorAll('[data-delivery-search-panel]').forEach((panel) => {
            const cards = [...panel.querySelectorAll('[data-delivery-search-card]')];
            const empty = panel.querySelector('[data-delivery-search-empty]');

            if (!empty) {
                return;
            }

            const hayResultados = cards.some((card) => !card.hidden);
            empty.hidden = hayResultados;
        });
    };

    searchInput?.addEventListener('input', actualizarBusqueda);
    actualizarBusqueda();

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
