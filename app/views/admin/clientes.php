<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Gestión de Clientes</h3>
    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=crearCliente" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nuevo Cliente
    </a>
</div>

<form method="GET" class="mb-4">
    <input type="hidden" name="controller" value="admin">
    <input type="hidden" name="action" value="clientes">
    <div class="row">
        <div class="col-md-8">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, email, teléfono o empresa..." 
                   value="<?php echo isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : ''; ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
            <?php if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])): ?>
            <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=clientes" class="btn btn-secondary w-100 mt-2">
                <i class="bi bi-arrow-counterclockwise"></i> Limpiar
            </a>
            <?php endif; ?>
        </div>
    </div>
</form>

<div class="alert alert-info" role="alert">
    Mostrando <strong><?php echo count($clientes); ?></strong> cliente(s)
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>WhatsApp</th>
                <th>Calificación</th>
                <th>Estado</th>
                <th>Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clientes)): ?>
            <tr>
                <td colspan="10" class="text-center text-muted py-4">No hay clientes registrados</td>
            </tr>
            <?php else: ?>
                <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo $cliente['id']; ?></td>
                    <td><strong><?php echo sanitizar($cliente['usuario']); ?></strong></td>
                    <td><?php echo sanitizar($cliente['nombre']); ?></td>
                    <td><?php echo sanitizar($cliente['email']); ?></td>
                    <td><?php echo sanitizar($cliente['telefono']); ?></td>
                    <td><?php echo sanitizar($cliente['whatsapp']); ?></td>
                    <td>
                        <?php 
                        $rating = isset($cliente['calificacion']) ? (int)$cliente['calificacion'] : 0;
                        if ($rating > 0) {
                            echo '<span style="font-size: 1rem; letter-spacing: 2px;" title="Calificación: ' . $rating . '/5">';
                            for ($i = 1; $i <= 5; $i++) {
                                echo ($i <= $rating) ? '⭐' : '☆';
                            }
                            echo '</span> <span style="font-size: 0.85rem; color: #666; margin-left: 0.5rem;">' . $rating . '/5</span>';
                        } else {
                            echo '<span class="text-muted" style="font-size: 0.85rem;">Sin rating</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo $cliente['activo'] ? 'success' : 'danger'; ?>">
                            <?php echo $cliente['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($cliente['fecha_registro'])); ?></td>
                    <td>
                                <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=verCliente&id=<?php echo $cliente['id']; ?>" 
                           class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=editarCliente&id=<?php echo $cliente['id']; ?>" 
                                    class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=desactivarCliente" 
                              style="display:inline;" onsubmit="return confirm('¿<?php echo $cliente['activo'] ? 'Desactivar' : 'Activar'; ?> este cliente?');">
                            <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-<?php echo $cliente['activo'] ? 'warning' : 'success'; ?>">
                                <i class="bi bi-<?php echo $cliente['activo'] ? 'lock' : 'unlock'; ?>"></i>
                            </button>
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
