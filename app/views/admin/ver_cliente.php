<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Detalle del Cliente</h3>
    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=clientes" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=editarCliente&id=<?php echo $cliente['id']; ?>" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Editar
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person"></i> Información Personal</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Usuario:</label>
                        <p><?php echo sanitizar($cliente['usuario']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Nombre:</label>
                        <p><?php echo sanitizar($cliente['nombre']); ?></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Email:</label>
                        <p><?php echo sanitizar($cliente['email']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Teléfono:</label>
                        <p><?php echo sanitizar($cliente['telefono']); ?></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">WhatsApp:</label>
                        <p><?php echo sanitizar($cliente['whatsapp']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Ciudad:</label>
                        <p><?php echo sanitizar($cliente['ciudad']); ?></p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Dirección:</label>
                    <p><?php echo sanitizar($cliente['direccion']); ?></p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="fw-bold">Estado:</label>
                        <p>
                            <span class="badge bg-<?php echo $cliente['activo'] ? 'success' : 'danger'; ?>">
                                <?php echo $cliente['activo'] ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Registrado:</label>
                        <p><?php echo date('d/m/Y H:i', strtotime($cliente['fecha_registro'])); ?></p>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="fw-bold">Calificación:</label>
                    <div>
                        <?php 
                        $rating = isset($cliente['calificacion']) ? (int)$cliente['calificacion'] : 0;
                        if ($rating > 0) {
                            echo '<div style="font-size: 2rem; letter-spacing: 4px;">';
                            for ($i = 1; $i <= 5; $i++) {
                                echo ($i <= $rating) ? '⭐' : '☆';
                            }
                            echo ' <span style="font-size: 1.2rem; color: #666; margin-left: 1rem;">' . $rating . ' / 5</span></div>';
                        } else {
                            echo '<p class="text-muted">Sin calificación</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear"></i> Acciones</h5>
            </div>
            <div class="card-body">
                    <form method="POST" action="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=desactivarCliente" 
                      onsubmit="return confirm('¿<?php echo $cliente['activo'] ? 'Desactivar' : 'Activar'; ?> este cliente?');">
                    <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
                    <button type="submit" class="btn btn-<?php echo $cliente['activo'] ? 'warning' : 'success'; ?> w-100">
                        <i class="bi bi-<?php echo $cliente['activo'] ? 'lock' : 'unlock'; ?>"></i> 
                        <?php echo $cliente['activo'] ? 'Desactivar' : 'Activar'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
