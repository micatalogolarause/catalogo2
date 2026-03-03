<?php
include APP_ROOT . '/app/views/layout/header.php';
?>

<?php
// Helper para resolver URL de imagen respetando rutas por tenant y legacy
function tienda_img_url($img) {
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

<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-search"></i> Búsqueda</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="<?php echo APP_URL; ?>/index.php?controller=tienda&action=buscar">
                    <input type="hidden" name="controller" value="tienda">
                    <input type="hidden" name="action" value="buscar">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" placeholder="Buscar..." value="<?php echo sanitizar($termino); ?>">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <h2>Resultados de Búsqueda</h2>
        <p class="text-muted">Búsqueda: "<strong><?php echo sanitizar($termino); ?></strong>"</p>

        <div class="d-flex justify-content-end mb-3">
            <div class="btn-group product-view-toggle" role="group" aria-label="Vista de productos">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-view="columnas">
                    <i class="bi bi-grid-3x3-gap"></i> Columnas
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-view="lista">
                    <i class="bi bi-list-ul"></i> Lista
                </button>
            </div>
        </div>

        <div class="row products-container view-columns">
            <?php if (empty($productos)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No se encontraron productos que coincidan con tu búsqueda.
                        <a href="<?php echo APP_URL; ?>" class="alert-link">Volver al inicio</a>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-muted mb-4">Se encontraron <?php echo count($productos); ?> producto(s)</p>
                <?php foreach ($productos as $producto): ?>
                <?php 
                    $galeria = [];
                    if (!empty($producto['imagen'])) { $galeria[] = tienda_img_url($producto['imagen']); }
                    if (!empty($producto['imagen2'])) { $galeria[] = tienda_img_url($producto['imagen2']); }
                    if (!empty($producto['imagen3'])) { $galeria[] = tienda_img_url($producto['imagen3']); }
                    $galeriaAttr = htmlspecialchars(implode('|', $galeria), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="col-6 col-md-6 col-lg-4 mb-4 product-col">
                    <div class="card h-100 product-item-card" data-galeria="<?php echo $galeriaAttr; ?>">
                            <img src="<?php echo $galeria[0] ?? tienda_img_url($producto['imagen']); ?>" 
                                class="card-img-top" alt="<?php echo sanitizar($producto['nombre']); ?>" style="height: 250px; object-fit: cover; cursor:pointer;" onclick="abrirModalGaleria(this)">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo sanitizar($producto['nombre']); ?></h5>
                            <p class="card-text text-muted"><?php echo substr(sanitizar($producto['descripcion']), 0, 80) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 text-primary">$<?php echo number_format($producto['precio'], 2); ?></span>
                                <span class="badge bg-<?php echo $producto['stock'] > 0 ? 'success' : 'danger'; ?>">
                                    <?php echo $producto['stock'] > 0 ? 'Stock: ' . $producto['stock'] : 'Agotado'; ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top">
                            <a href="<?php echo APP_URL; ?>/index.php?controller=tienda&action=producto&id=<?php echo $producto['id']; ?>" 
                               class="btn btn-primary btn-sm w-100 mb-2">
                                <i class="bi bi-eye"></i> Ver Detalles
                            </a>
                            <?php if ($producto['stock'] > 0): ?>
                            <button class="btn btn-success btn-sm w-100 agregar-carrito" 
                                    data-producto-id="<?php echo $producto['id']; ?>"
                                    data-nombre="<?php echo sanitizar($producto['nombre']); ?>">
                                <i class="bi bi-cart-plus"></i> Agregar
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal galería búsqueda -->
<div class="modal fade" id="modalGaleriaCatalogo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imagen del producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center position-relative">
                <img id="modalCatalogoImg" src="" alt="Imagen" style="max-width:100%; height:auto;">
                <button type="button" class="btn btn-light position-absolute top-50 start-0 translate-middle-y" style="opacity:0.8" onclick="navegarCatalogo(-1)"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="btn btn-light position-absolute top-50 end-0 translate-middle-y" style="opacity:0.8" onclick="navegarCatalogo(1)"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
// Galería catálogo (búsqueda)
let galeriaCatalogo = [];
let idxCatalogo = 0;

function abrirModalGaleria(imgEl) {
    const modalEl = document.getElementById('modalGaleriaCatalogo');
    const modalImgEl = document.getElementById('modalCatalogoImg');
    if (!modalEl || !modalImgEl || !imgEl?.parentElement) return;

    const data = imgEl.parentElement.getAttribute('data-galeria') || '';
    galeriaCatalogo = data ? data.split('|').filter(Boolean) : [];
    if (!galeriaCatalogo.length) return;

    idxCatalogo = 0;
    actualizarCatalogoImg();
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function actualizarCatalogoImg() {
    const modalImgEl = document.getElementById('modalCatalogoImg');
    if (!modalImgEl || !galeriaCatalogo.length) return;

    idxCatalogo = ((idxCatalogo % galeriaCatalogo.length) + galeriaCatalogo.length) % galeriaCatalogo.length;
    modalImgEl.src = galeriaCatalogo[idxCatalogo] || '';
}

function navegarCatalogo(delta) {
    idxCatalogo += delta;
    actualizarCatalogoImg();
}
</script>

<?php
include APP_ROOT . '/app/views/layout/footer.php';
?>
