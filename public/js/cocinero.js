document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('cocineroSidebar');
    const overlay = document.getElementById('cocineroOverlay');
    const openButton = document.getElementById('cocineroSidebarOpen');
    const closeButton = document.getElementById('cocineroSidebarClose');

    const openSidebar = () => {
        sidebar?.classList.add('show');
        overlay?.classList.add('show');
        document.body.classList.add('sidebar-open');
    };

    const closeSidebar = () => {
        sidebar?.classList.remove('show');
        overlay?.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    };

    openButton?.addEventListener('click', openSidebar);
    closeButton?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    document.querySelectorAll('.cocinero-nav-link').forEach((link) => {
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

    // Tarjetas de pedido: permiten abrir el detalle haciendo clic en el cuerpo,
    // sin interferir con botones, enlaces o formularios.
    document.querySelectorAll('[data-order-url]').forEach((card) => {
        const goToOrder = () => {
            const url = card.dataset.orderUrl;
            if (url) {
                window.location.href = url;
            }
        };

        card.addEventListener('click', (event) => {
            if (event.target.closest('button, a, form, input, select, textarea')) {
                return;
            }

            goToOrder();
        });

        card.addEventListener('keydown', (event) => {
            if ((event.key === 'Enter' || event.key === ' ') &&
                !event.target.closest('button, a, form, input, select, textarea')) {
                event.preventDefault();
                goToOrder();
            }
        });
    });

    // Filtros que deben enviar su formulario al cambiar.
    document.querySelectorAll('[data-submit-form]').forEach((field) => {
        field.addEventListener('change', () => {
            field.form?.requestSubmit();
        });
    });

    // Confirmaciones centralizadas.
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirm;
            if (message && !window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    // Evitar dobles envíos mientras se procesa una acción.
    document.querySelectorAll('form[data-loading-text]').forEach((form) => {
        form.addEventListener('submit', () => {
            if (form.dataset.submitted === 'true') {
                return;
            }

            form.dataset.submitted = 'true';
            form.classList.add('cocinero-form-loading');

            const button = form.querySelector('button[type="submit"]');
            if (!button) {
                return;
            }

            const original = button.innerHTML;
            button.dataset.originalHtml = original;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>' +
                (form.dataset.loadingText || 'Procesando...');
        });
    });

    // Pequeño efecto de pulsación en botones destacados.
    document.querySelectorAll('.js-ripple').forEach((button) => {
        button.addEventListener('click', (event) => {
            const rect = button.getBoundingClientRect();
            const ripple = document.createElement('span');

            ripple.className = 'cocinero-ripple';
            ripple.style.left = (event.clientX - rect.left) + 'px';
            ripple.style.top = (event.clientY - rect.top) + 'px';

            button.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove());
        });
    });

    // Animación de entrada ligera al aparecer cada bloque.
    const revealItems = document.querySelectorAll('.cocinero-reveal');

    if ('IntersectionObserver' in window && revealItems.length) {
        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            });
        }, { threshold: 0.08 });

        revealItems.forEach((item) => observer.observe(item));
    } else {
        revealItems.forEach((item) => item.classList.add('is-visible'));
    }

    // Reloj de cocina actualizado en el cliente.
    const clock = document.getElementById('cocineroClock');

    if (clock) {
        const updateClock = () => {
            clock.textContent = new Intl.DateTimeFormat('es-BO', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }).format(new Date());
        };

        updateClock();
        window.setInterval(updateClock, 1000);
    }
});