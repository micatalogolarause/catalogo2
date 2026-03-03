<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Editar Categoría</h3>
    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=categorias" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Categoría *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo sanitizar($categoria['nombre']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?php echo sanitizar($categoria['descripcion']); ?></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
