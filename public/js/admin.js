// JavaScript para el panel administrativo

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Confirmar eliminación
function confirmarEliminacion(nombre) {
    return confirm('¿Estás seguro de que deseas eliminar: ' + nombre + '?');
}

// Cargar subcategorías dinámicamente
function cargarSubcategorias() {
    let categoriaId = document.getElementById('categoria_id').value;
    let select = document.getElementById('subcategoria_id');
    
    if (!categoriaId) {
        select.innerHTML = '<option value="">Seleccionar categoría primero</option>';
        return;
    }

    fetch('/catalogo2/index.php?controller=api&action=obtenerSubcategorias&categoria_id=' + categoriaId)
        .then(response => response.json())
        .then(data => {
            let html = '<option value="">Seleccionar...</option>';
            if (data.subcategorias) {
                data.subcategorias.forEach(sub => {
                    html += '<option value="' + sub.id + '">' + sub.nombre + '</option>';
                });
            }
            select.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}

// Mostrar notificación
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
