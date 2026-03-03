<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Crear Subcategoría</h3>
    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=subcategorias" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-control" id="categoria_id" name="categoria_id" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo sanitizar($cat['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Electrónica" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" placeholder="Descripción de la subcategoría"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Crear Subcategoría
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
