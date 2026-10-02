document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('cocineroSidebar');
    const overlay = document.getElementById('cocineroOverlay');
    const openButton = document.getElementById('cocineroSidebarOpen');
    const closeButton = document.getElementById('cocineroSidebarClose');

    const isMobile = () => window.innerWidth < 992;

    const openSidebar = () => {
        if (!sidebar || !isMobile()) return;
        sidebar.classList.add('show');
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
            if (isMobile()) closeSidebar();
        });
    });

    window.addEventListener('resize', () => {
        if (!isMobile()) closeSidebar();
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirm;
            if (message && !window.confirm(message)) event.preventDefault();
        });
    });

    document.querySelectorAll('.cocinero-ripple-target').forEach((element) => {
        element.addEventListener('click', (event) => {
            if (event.target.closest('button, a, input, select, textarea, form')) return;
            const rect = element.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'cocinero-ripple';
            ripple.style.left = \`\${event.clientX - rect.left}px\`;
            ripple.style.top = \`\${event.clientY - rect.top}px\`;
            element.appendChild(ripple);
            window.setTimeout(() => ripple.remove(), 600);
        });
    });

    document.querySelectorAll('[data-pedido-url]').forEach((card) => {
        const goToPedido = (event) => {
            if (event.target.closest('button, a, form, input, select, textarea')) return;
            card.classList.add('is-opening');
            window.location.href = card.dataset.pedidoUrl;
        };
        card.addEventListener('click', goToPedido);
        card.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter' && event.key !== ' ') return;
            if (event.target.closest('button, a, form, input, select, textarea')) return;
            event.preventDefault();
            card.classList.add('is-opening');
            window.location.href = card.dataset.pedidoUrl;
        });
    });

    const elapsedElements = document.querySelectorAll('[data-order-time]');
    const updateElapsed = () => {
        const now = Math.floor(Date.now() / 1000);
        elapsedElements.forEach((element) => {
            const timestamp = Number(element.dataset.orderTime);
            if (!timestamp) return;
            const seconds = Math.max(0, now - timestamp);
            if (seconds < 60) {
                element.textContent = 'Hace unos segundos';
                return;
            }
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) {
                element.textContent = \`Hace \${minutes} \${minutes === 1 ? 'minuto' : 'minutos'}\`;
                return;
            }
            const hours = Math.floor(minutes / 60);
            element.textContent = \`Hace \${hours} \${hours === 1 ? 'hora' : 'horas'}\`;
        });
    };

    if (elapsedElements.length) {
        updateElapsed();
        window.setInterval(updateElapsed, 30000);
    }

    const photoInput = document.getElementById('foto_perfil');
    const photoPreview = document.querySelector('[data-photo-preview]');

    photoInput?.addEventListener('change', () => {
        const file = photoInput.files?.[0];
        if (!file || !photoPreview || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.addEventListener('load', () => {
            photoPreview.innerHTML = '';
            const image = document.createElement('img');
            image.src = reader.result;
            image.alt = 'Vista previa de la foto de perfil';
            photoPreview.appendChild(image);
        });
        reader.readAsDataURL(file);
    });

    document.querySelectorAll('form[data-loading]').forEach((form) => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('button[type="submit"]');
            if (!button || button.disabled) return;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Procesando...';
        });
    });

    document.querySelectorAll('.cocinero-alert.alert-success').forEach((alert) => {
        window.setTimeout(() => {
            if (document.body.contains(alert) && typeof bootstrap !== 'undefined') {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 5000);
    });
});
