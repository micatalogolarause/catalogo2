<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Gestión de Subcategorías</h3>
    <a href="<?php echo APP_URL; ?>/index.php?controller=admin&action=crearSubcategoria" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Subcategoría
    </a>
</div>

<form method="GET" class="mb-4">
    <input type="hidden" name="controller" value="admin">
    <input type="hidden" name="action" value="subcategorias">
    <div class="row">
        <div class="col-md-8">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, categoría o descripción..." 
                   value="<?php echo isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : ''; ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
            <?php if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])): ?>
            <a href="<?php echo APP_URL; ?>/index.php?controller=admin&action=subcategorias" class="btn btn-secondary w-100 mt-2">
                <i class="bi bi-arrow-counterclockwise"></i> Limpiar
            </a>
            <?php endif; ?>
        </div>
    </div>
</form>

<div class="alert alert-info" role="alert">
    Mostrando <strong><?php echo count($subcategorias); ?></strong> subcategoría(s)
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Categoría</th>
                <th>Subcategoría</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($subcategorias)): ?>
            <tr>
                <td colspan="6" class="text-center text-muted py-4">No hay subcategorías registradas</td>
            </tr>
            <?php else: ?>
                <?php foreach ($subcategorias as $sub): ?>
                <tr>
                    <td><?php echo $sub['id']; ?></td>
                    <td><strong><?php echo sanitizar($sub['categoria']); ?></strong></td>
                    <td><?php echo sanitizar($sub['nombre']); ?></td>
                    <td><?php echo substr(sanitizar($sub['descripcion']), 0, 50); ?>...</td>
                    <td>
                        <span class="badge bg-<?php echo $sub['activa'] ? 'success' : 'danger'; ?>">
                            <?php echo $sub['activa'] ? 'Activa' : 'Inactiva'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo APP_URL; ?>/index.php?controller=admin&action=editarSubcategoria&id=<?php echo $sub['id']; ?>" 
                           class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=admin&action=eliminarSubcategoria" 
                              style="display:inline;" onsubmit="return confirm('¿Eliminar esta subcategoría?');">
                            <input type="hidden" name="id" value="<?php echo $sub['id']; ?>">
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
