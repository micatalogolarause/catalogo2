// JavaScript para la tienda frontend

document.addEventListener('DOMContentLoaded', function() {
    actualizarCarroBadge();
    marcarProductosEnCarrito();
    inicializarVistaProductos();
    
    // Delegar click en botones agregar al carrito
    document.addEventListener('click', function(e) {
        if (e.target.closest('.agregar-carrito')) {
            const btn = e.target.closest('.agregar-carrito');
            agregarAlCarrito(btn.dataset.productoId, 1);
        }
    });

    // Cerrar navbar móvil al hacer clic en cualquier enlace de navegación
    const navbarNav = document.getElementById('navbarNav');
    if (navbarNav) {
        navbarNav.addEventListener('click', function(e) {
            const link = e.target.closest('a[href]');
            // Solo cerrar si el enlace navega (no es toggle de dropdown ni #)
            if (link && link.href && !link.href.endsWith('#') && !link.hasAttribute('data-bs-toggle')) {
                const bsCollapse = typeof bootstrap !== 'undefined' && bootstrap.Collapse.getInstance(navbarNav);
                if (bsCollapse) bsCollapse.hide();
            }
        });
    }
});

function getBaseUrl() {
    if (typeof APP_BASE_URL !== 'undefined' && APP_BASE_URL) {
        return APP_BASE_URL;
    }
    // Fallback: derivar de la URL actual (origen + primer segmento = slug)
    let parts = window.location.pathname.split('/').filter(p => p && p !== 'index.php');
    return window.location.origin + (parts.length > 0 ? '/' + parts[0] : '');
}

function agregarAlCarrito(productoId, cantidad) {
    let formData = new FormData();
    formData.append('producto_id', productoId);
    formData.append('cantidad', cantidad || 1);

    let baseUrl = getBaseUrl();
    
    fetch(baseUrl + '/index.php?controller=api&action=agregarAlCarrito', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion('✓ Producto agregado al carrito', 'success');
            marcarProductoAgregado(productoId);
            actualizarCarroBadge();
            setTimeout(() => {
                window.location = getBaseUrl() + '/index.php?controller=tienda&action=carrito';
            }, 600);
        } else {
            mostrarNotificacion(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al agregar producto', 'danger');
    });
}

function marcarProductoAgregado(productoId) {
    // Encontrar todos los botones de este producto y marcarlos
    const botones = document.querySelectorAll(`.agregar-carrito[data-producto-id="${productoId}"]`);
    botones.forEach(btn => {
        btn.classList.remove('btn-success');
        btn.classList.add('btn-secondary');
        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Agregado';
        btn.disabled = false; // Permitir agregar más
    });
}

function marcarProductosEnCarrito() {
    let baseUrl = getBaseUrl();
    fetch(baseUrl + '/index.php?controller=api&action=obtenerCarrito', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.items.length > 0) {
            data.items.forEach(item => {
                marcarProductoAgregado(item.producto_id);
            });
        }
    })
    .catch(error => {
        console.error('Error al marcar productos:', error);
    });
}

function actualizarCarroBadge() {
    let baseUrl = getBaseUrl();
    fetch(baseUrl + '/index.php?controller=api&action=obtenerCarrito', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        // Actualizar badge en el header
        let badge = document.getElementById('carrito-badge');
        if (badge) {
            const total = data.items.length > 0 ? data.items.reduce((sum, item) => sum + item.cantidad, 0) : 0;
            badge.textContent = total;
            // Mostrar/ocultar badge según si hay productos
            if (total > 0) {
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
        
        // Actualizar badge móvil
        let badgeMobile = document.getElementById('carrito-badge-mobile');
        if (badgeMobile) {
            const total = data.items.length > 0 ? data.items.reduce((sum, item) => sum + item.cantidad, 0) : 0;
            badgeMobile.textContent = total;
            if (total > 0) {
                badgeMobile.style.display = 'inline-block';
            } else {
                badgeMobile.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Error al actualizar badge:', error);
    });
}

function mostrarNotificacion(mensaje, tipo = 'info') {
    let alert = document.createElement('div');
    alert.className = `alert alert-${tipo} alert-dismissible fade show`;
    alert.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    let main = document.querySelector('main');
    if (main) {
        main.insertBefore(alert, main.firstChild);
        setTimeout(() => alert.remove(), 5000);
    }
}

function inicializarVistaProductos() {
    const toggles = document.querySelectorAll('.product-view-toggle');
    const containers = document.querySelectorAll('.products-container');

    if (!toggles.length || !containers.length) {
        return;
    }

    const vistaGuardada = localStorage.getItem('catalogo_vista_productos') || 'columnas';
    aplicarVistaProductos(vistaGuardada, toggles, containers);

    toggles.forEach(toggle => {
        toggle.querySelectorAll('button[data-view]').forEach(btn => {
            btn.addEventListener('click', function() {
                const vista = this.getAttribute('data-view') || 'columnas';
                localStorage.setItem('catalogo_vista_productos', vista);
                aplicarVistaProductos(vista, toggles, containers);
            });
        });
    });
}

function aplicarVistaProductos(vista, toggles, containers) {
    const esLista = vista === 'lista';

    containers.forEach(container => {
        container.classList.toggle('view-list', esLista);
        container.classList.toggle('view-columns', !esLista);
    });

    toggles.forEach(toggle => {
        toggle.querySelectorAll('button[data-view]').forEach(btn => {
            const activa = btn.getAttribute('data-view') === (esLista ? 'lista' : 'columnas');
            btn.classList.toggle('active', activa);
            btn.classList.toggle('btn-primary', activa);
            btn.classList.toggle('btn-outline-secondary', !activa);
        });
    });
}
