<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Crear Nuevo Producto</h3>
    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=productos" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="categoria_id" class="form-label">Categoría *</label>
                            <select class="form-control" id="categoria_id" name="categoria_id" required onchange="cargarSubcategorias()">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo sanitizar($cat['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="subcategoria_id" class="form-label">Subcategoría *</label>
                            <select class="form-control" id="subcategoria_id" name="subcategoria_id" required>
                                <option value="">Seleccionar categoría primero</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Producto *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="precio" class="form-label">Precio *</label>
                            <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
                        </div>

                        <div class="col-md-6">
                            <label for="stock" class="form-label">Stock *</label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen Principal * (Obligatoria)</label>
                        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required>
                        <small class="text-muted">Formatos soportados: JPG, PNG, GIF, WebP (máx. 5MB)</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Crear Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function cargarSubcategorias() {
    let categoriaId = document.getElementById('categoria_id').value;
    let select = document.getElementById('subcategoria_id');
    
    if (!categoriaId) {
        select.innerHTML = '<option value="">Seleccionar categoría primero</option>';
        return;
    }

    fetch('<?php echo APP_URL; ?>/api/obtener_subcategorias.php?categoria_id=' + categoriaId)
        .then(response => response.json())
        .then(data => {
            select.innerHTML = '<option value="">Seleccionar subcategoría</option>';
            data.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub.id;
                option.textContent = sub.nombre;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            select.innerHTML = '<option value="">Error al cargar</option>';
        });
}
</script>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
