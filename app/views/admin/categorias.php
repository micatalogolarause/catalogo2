<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Gestión de Categorías</h3>
    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=crearCategoria" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Categoría
    </a>
</div>

<form method="GET" class="mb-4">
    <input type="hidden" name="controller" value="admin">
    <input type="hidden" name="action" value="categorias">
    <div class="row">
        <div class="col-md-8">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre o descripción..." 
                   value="<?php echo isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : ''; ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
            <?php if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])): ?>
            <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=categorias" class="btn btn-secondary w-100 mt-2">
                <i class="bi bi-arrow-counterclockwise"></i> Limpiar
            </a>
            <?php endif; ?>
        </div>
    </div>
</form>

<div class="alert alert-info" role="alert">
    Mostrando <strong><?php echo count($categorias); ?></strong> categoría(s)
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categorias)): ?>
            <tr>
                <td colspan="5" class="text-center text-muted py-4">No hay categorías registradas</td>
            </tr>
            <?php else: ?>
                <?php foreach ($categorias as $cat): ?>
                <tr>
                    <td><?php echo $cat['id']; ?></td>
                    <td><strong><?php echo sanitizar($cat['nombre']); ?></strong></td>
                    <td><?php echo substr(sanitizar($cat['descripcion']), 0, 50); ?>...</td>
                    <td>
                        <span class="badge bg-<?php echo $cat['activa'] ? 'success' : 'danger'; ?>">
                            <?php echo $cat['activa'] ? 'Activa' : 'Inactiva'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=editarCategoria&id=<?php echo $cat['id']; ?>" 
                           class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=eliminarCategoria" 
                              style="display:inline;" onsubmit="return confirm('¿Eliminar esta categoría?');">
                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
