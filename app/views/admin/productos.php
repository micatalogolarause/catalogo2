<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<?php
// Helper para resolver URL de imagen respetando rutas por tenant y legacy
function admin_producto_img_url($img) {
    if (!$img) return APP_URL . '/public/images/no-image.jpg';
    if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
        return $img; // URL de Cloudinary u externa
    }
    if (str_starts_with($img, 'public/tenants/')) {
        return APP_URL . '/' . $img;
    }
    return APP_URL . '/public/images/productos/' . $img;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Gestión de Productos</h3>
    <div class="btn-group">
        <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=crearProducto" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Producto
        </a>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download"></i> Exportar
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=reporteProductosPDF&filtro=<?php echo $_GET['estado'] ?? 'todos'; ?>" target="_blank">
                    <i class="bi bi-file-earmark-pdf text-danger"></i> Exportar PDF
                </a></li>
                <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=reporteProductosExcel&filtro=<?php echo $_GET['estado'] ?? 'todos'; ?>" target="_blank">
                    <i class="bi bi-file-earmark-excel text-success"></i> Exportar Excel
                </a></li>
            </ul>
        </div>
    </div>
</div>

<form method="GET" class="mb-4" id="productosFiltro">
    <input type="hidden" name="controller" value="admin">
    <input type="hidden" name="action" value="productos">
    <div class="row">
        <div class="col-md-4">
            <select name="estado" id="estadoFiltro" class="form-select" onchange="document.getElementById('productosFiltro').submit();">
                <option value="">Todos los productos</option>
                <option value="activo" <?php echo isset($_GET['estado']) && $_GET['estado'] === 'activo' ? 'selected' : ''; ?>>Solo Activos</option>
                <option value="inactivo" <?php echo isset($_GET['estado']) && $_GET['estado'] === 'inactivo' ? 'selected' : ''; ?>>Solo Inactivos</option>
            </select>
        </div>
        <div class="col-md-5">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, descripción o categoría..." 
                   value="<?php echo isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : ''; ?>">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
            <?php if ((isset($_GET['busqueda']) && !empty($_GET['busqueda'])) || (isset($_GET['estado']) && !empty($_GET['estado']))): ?>
            <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=productos" class="btn btn-secondary w-100 mt-2">
                <i class="bi bi-arrow-counterclockwise"></i> Limpiar
            </a>
            <?php endif; ?>
        </div>
    </div>
</form>

<div class="alert alert-info" role="alert">
    Mostrando <strong><?php echo count($productos); ?></strong> producto(s)
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Nro.</th>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Valor Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($productos)): ?>
            <tr>
                <td colspan="9" class="text-center text-muted py-4">No hay productos registrados</td>
            </tr>
            <?php else: ?>
                <?php foreach ($productos as $prod): ?>
                <tr>
                    <td><?php echo $prod['numero_producto'] ?? $prod['id']; ?></td>
                        <td class="text-center">
                            <?php 
                                $galeria = [];
                                $idx = 0;
                                if ($prod['imagen']) { $galeria[] = admin_producto_img_url($prod['imagen']); }
                                if (!empty($prod['imagen2'])) { $galeria[] = admin_producto_img_url($prod['imagen2']); }
                                if (!empty($prod['imagen3'])) { $galeria[] = admin_producto_img_url($prod['imagen3']); }
                                $galeriaAttr = addslashes(implode('|', $galeria));
                            ?>
                            <div class="d-flex gap-1" data-galeria="<?php echo $galeriaAttr; ?>">
                                <?php $i = 0; ?>
                                <?php if ($prod['imagen']): ?>
                                <img src="<?php echo admin_producto_img_url($prod['imagen']); ?>" 
                                    alt="<?php echo sanitizar($prod['nombre']); ?>" 
                                    style="width: 45px; height: 45px; object-fit: cover; cursor: pointer; border: 2px solid #ddd; border-radius: 4px;" 
                                    onclick="mostrarImagenDesde(this, <?php echo $i++; ?>)">
                                <?php endif; ?>
                                <?php if (!empty($prod['imagen2'])): ?>
                                <img src="<?php echo admin_producto_img_url($prod['imagen2']); ?>" 
                                    alt="Imagen 2" 
                                    style="width: 45px; height: 45px; object-fit: cover; cursor: pointer; border: 2px solid #ddd; border-radius: 4px;" 
                                    onclick="mostrarImagenDesde(this, <?php echo $i++; ?>)">
                                <?php endif; ?>
                                <?php if (!empty($prod['imagen3'])): ?>
                                <img src="<?php echo admin_producto_img_url($prod['imagen3']); ?>" 
                                    alt="Imagen 3" 
                                    style="width: 45px; height: 45px; object-fit: cover; cursor: pointer; border: 2px solid #ddd; border-radius: 4px;" 
                                    onclick="mostrarImagenDesde(this, <?php echo $i++; ?>)">
                                <?php endif; ?>
                            </div>
                    </td>
                    <td><strong><?php echo sanitizar($prod['nombre']); ?></strong></td>
                    <td><?php echo sanitizar($prod['categoria']); ?></td>
                    <td class="text-success">$<?php echo number_format($prod['precio'], 2); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $prod['stock'] > 0 ? 'success' : 'danger'; ?>">
                            <?php echo $prod['stock']; ?>
                        </span>
                    </td>
                    <td class="text-primary fw-bold">
                        $<?php echo number_format($prod['precio'] * $prod['stock'], 2); ?>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo $prod['activo'] ? 'success' : 'secondary'; ?>">
                            <?php echo $prod['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=editarProducto&id=<?php echo $prod['id']; ?>" 
                           class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" action="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=cambiarEstadoProducto" 
                                                            style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                                                        <input type="hidden" name="estado" value="<?php echo $prod['activo'] ? '0' : '1'; ?>">
                                                        <button type="submit" class="btn btn-sm btn-<?php echo $prod['activo'] ? 'secondary' : 'success'; ?>" 
                                                                        title="<?php echo $prod['activo'] ? 'Desactivar' : 'Activar'; ?>">
                                                                <i class="bi bi-<?php echo $prod['activo'] ? 'x-circle' : 'check-circle'; ?>"></i>
                                                        </button>
                                                </form>
                        <form method="POST" action="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=eliminarProducto" 
                              style="display:inline;" onsubmit="return confirm('¿Eliminar este producto?');">
                            <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para ver imágenes en grande -->
<div class="modal fade" id="imagenModal" tabindex="-1" aria-labelledby="imagenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagenModalLabel">Imagen del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center position-relative">
                <img id="imagenModalImg" src="" alt="Imagen" style="max-width: 100%; height: auto;">
                <button type="button" class="btn btn-light position-absolute top-50 start-0 translate-middle-y" style="opacity:0.8" onclick="anteriorImagen()"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="btn btn-light position-absolute top-50 end-0 translate-middle-y" style="opacity:0.8" onclick="siguienteImagen()"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>
    </div>

<script>
// Galería de imágenes por producto
let galeriaActual = [];
let imgIndex = 0;

function mostrarImagenDesde(el, index) {
    const cont = el.parentElement;
    const data = cont.getAttribute('data-galeria') || '';
    galeriaActual = data ? data.split('|') : [];
    imgIndex = index || 0;
    mostrarImagenIdx();
    const modal = new bootstrap.Modal(document.getElementById('imagenModal'));
    modal.show();
}

function mostrarImagenIdx() {
    if (!galeriaActual.length) return;
    const img = document.getElementById('imagenModalImg');
    img.src = galeriaActual[imgIndex];
}

function siguienteImagen() {
    if (!galeriaActual.length) return;
    imgIndex = (imgIndex + 1) % galeriaActual.length;
    mostrarImagenIdx();
}

function anteriorImagen() {
    if (!galeriaActual.length) return;
    imgIndex = (imgIndex - 1 + galeriaActual.length) % galeriaActual.length;
    mostrarImagenIdx();
}

// Auto-envía al cambiar el filtro de estado
(() => {
    const form = document.getElementById('productosFiltro');
    const estado = document.getElementById('estadoFiltro');
    if (form && estado) {
        estado.addEventListener('change', () => form.submit());
    }
})();
</script>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
