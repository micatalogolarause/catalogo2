<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Editar Cliente</h3>
    <div>
        <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=verCliente&id=<?php echo $cliente['id']; ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Datos del Cliente</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=actualizarCliente">
                    <input type="hidden" name="id" value="<?php echo (int)$cliente['id']; ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($cliente['email'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">WhatsApp</label>
                            <input type="text" class="form-control" name="whatsapp" value="<?php echo htmlspecialchars($cliente['whatsapp'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ciudad</label>
                            <input type="text" class="form-control" name="ciudad" value="<?php echo htmlspecialchars($cliente['ciudad'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Calificación</label>
                            <div class="rating-selector d-flex gap-1 align-items-center" style="flex-wrap: wrap;">
                                <?php 
                                $rating = isset($cliente['calificacion']) ? (int)$cliente['calificacion'] : 0;
                                for ($i = 1; $i <= 5; $i++) {
                                    $checked = ($i === $rating) ? 'checked' : '';
                                    echo '<div style="position: relative;">
                                        <input class="rating-star visually-hidden" type="radio" name="calificacion" id="star' . $i . '" value="' . $i . '" ' . $checked . '>
                                        <label for="star' . $i . '" class="star-label" data-star="' . $i . '" style="cursor: pointer; font-size: 2.5rem; margin: 0; padding: 0;">
                                            ☆
                                        </label>
                                    </div>';
                                }
                                echo '<span id="rating-text" style="margin-left: 1.5rem; font-size: 1rem; font-weight: 500;">' . ($rating > 0 ? $rating . ' estrella' . ($rating > 1 ? 's' : '') : 'Sin calificación') . '</span>';
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar cambios</button>
                        <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=verCliente&id=<?php echo $cliente['id']; ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingSelector = document.querySelector('.rating-selector');
    const starLabels = document.querySelectorAll('.star-label');
    const ratingText = document.getElementById('rating-text');

    function getCurrentRating() {
        return parseInt(document.querySelector('input[name="calificacion"]:checked')?.value || 0);
    }

    function updateStars(rating) {
        starLabels.forEach(label => {
            const star = parseInt(label.getAttribute('data-star'));
            label.textContent = (star <= rating) ? '⭐' : '☆';
        });
    }

    // Inicializar con valor actual
    updateStars(getCurrentRating());

    // Cambio real del radio (asegura envío en el form)
    document.querySelectorAll('.rating-star').forEach(radio => {
        radio.addEventListener('change', function() {
            const val = parseInt(this.value);
            updateStars(val);
            ratingText.textContent = val > 0 ? `${val} estrella${val > 1 ? 's' : ''}` : 'Sin calificación';
        });
    });

    // Hover preview
    starLabels.forEach(label => {
        label.addEventListener('mouseenter', function() {
            const hoverStar = parseInt(this.getAttribute('data-star'));
            updateStars(hoverStar);
        });
    });

    // Restaurar al salir del hover
    ratingSelector.addEventListener('mouseleave', function() {
        updateStars(getCurrentRating());
    });
});
</script>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
