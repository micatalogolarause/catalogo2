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
    <div class="col-lg-4">
        <div class="card sticky-top">
            <?php
                $galeria = [];
                if (!empty($producto['imagen'])) { $galeria[] = tienda_img_url($producto['imagen']); }
                if (!empty($producto['imagen2'])) { $galeria[] = tienda_img_url($producto['imagen2']); }
                if (!empty($producto['imagen3'])) { $galeria[] = tienda_img_url($producto['imagen3']); }
                $galeriaAttr = htmlspecialchars(implode('|', $galeria), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="position-relative" data-galeria="<?php echo $galeriaAttr; ?>">
                <img id="detalleImagenPrincipal" src="<?php echo $galeria[0] ?? tienda_img_url($producto['imagen']); ?>" 
                     class="card-img-top" alt="<?php echo sanitizar($producto['nombre']); ?>" style="height: 400px; object-fit: cover; cursor:pointer;" 
                     onclick="abrirModalDetalle(0)">
                <?php if (count($galeria) > 1): ?>
                <button type="button" class="btn btn-light position-absolute top-50 start-0 translate-middle-y" style="opacity:0.8" onclick="navegarDetalle(-1)"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="btn btn-light position-absolute top-50 end-0 translate-middle-y" style="opacity:0.8" onclick="navegarDetalle(1)"><i class="bi bi-chevron-right"></i></button>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2 p-3">
                <?php foreach ($galeria as $idx => $imgUrl): ?>
                    <img src="<?php echo $imgUrl; ?>" alt="thumb" 
                         style="width:65px; height:65px; object-fit:cover; cursor:pointer; border:2px solid #ddd; border-radius:4px;" 
                         onclick="cambiarImagenDetalle(<?php echo $idx; ?>)">
                <?php endforeach; ?>
            </div>
            <div class="card-body">
                <h2 class="card-title"><?php echo sanitizar($producto['nombre']); ?></h2>
                <p class="text-muted">
                    <a href="<?php echo APP_URL; ?>/index.php?controller=tienda&action=categoria&id=<?php echo $producto['categoria_id']; ?>">
                        <?php echo sanitizar($producto['categoria']); ?>
                    </a>
                </p>

                <div class="alert alert-info">
                    <h3 class="mb-0">$<?php echo number_format($producto['precio'], 2); ?></h3>
                </div>

                <p class="mb-3">
                    <strong>Stock disponible:</strong> 
                    <span class="badge bg-<?php echo $producto['stock'] > 0 ? 'success' : 'danger'; ?>">
                        <?php echo $producto['stock'] > 0 ? $producto['stock'] . ' unidades' : 'Agotado'; ?>
                    </span>
                </p>

                <?php if ($producto['stock'] > 0): ?>
                <div class="input-group mb-3">
                    <button class="btn btn-outline-secondary" type="button" id="btn-menos">-</button>
                    <input type="number" class="form-control" id="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>">
                    <button class="btn btn-outline-secondary" type="button" id="btn-mas">+</button>
                </div>

                <button class="btn btn-success btn-lg w-100 agregar-carrito-detalle" data-producto-id="<?php echo $producto['id']; ?>">
                    <i class="bi bi-cart-plus"></i> Agregar al Carrito
                </button>
                <?php else: ?>
                <button class="btn btn-secondary btn-lg w-100" disabled>Agotado</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <h3>Descripción del Producto</h3>
        <p><?php echo nl2br(sanitizar($producto['descripcion'])); ?></p>

        <hr>

        <h3>Productos Relacionados</h3>
        <div class="row">
            <?php 
            require_once 'app/models/ProductoModel.php';
            global $conn;
            $productoModel = new ProductoModel($conn);
            $relacionados = $productoModel->obtenerPorCategoria($producto['categoria_id']);
            
            foreach ($relacionados as $prod):
                if ($prod['id'] === $producto['id']) continue;
            ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <img src="<?php echo tienda_img_url($prod['imagen']); ?>" 
                        class="card-img-top" alt="<?php echo sanitizar($prod['nombre']); ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h6 class="card-title"><?php echo sanitizar($prod['nombre']); ?></h6>
                        <p class="text-primary">$<?php echo number_format($prod['precio'], 2); ?></p>
                        <a href="<?php echo APP_URL; ?>/index.php?controller=tienda&action=producto&id=<?php echo $prod['id']; ?>" 
                           class="btn btn-sm btn-outline-primary w-100">
                            Ver Producto
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal de imagen detalle -->
<div class="modal fade" id="modalDetalleImagen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imagen del producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center position-relative">
                <img id="modalDetalleImg" src="" alt="Imagen" style="max-width:100%; height:auto;">
                <button type="button" class="btn btn-light position-absolute top-50 start-0 translate-middle-y" style="opacity:0.8" onclick="navegarDetalle(-1)"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="btn btn-light position-absolute top-50 end-0 translate-middle-y" style="opacity:0.8" onclick="navegarDetalle(1)"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btn-menos').addEventListener('click', function() {
    let input = document.getElementById('cantidad');
    if (input.value > 1) input.value--;
});

document.getElementById('btn-mas').addEventListener('click', function() {
    let input = document.getElementById('cantidad');
    if (input.value < input.max) input.value++;
});

document.querySelector('.agregar-carrito-detalle').addEventListener('click', function() {
    let cantidad = document.getElementById('cantidad').value;
    let btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-check-circle"></i> Agregado...';
    
    fetch('<?php echo APP_URL; ?>/index.php?controller=api&action=agregarAlCarrito', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'producto_id=' + this.dataset.productoId + '&cantidad=' + cantidad
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirigir al carrito con pequeño delay
            setTimeout(() => {
                window.location = '<?php echo APP_URL; ?>/index.php?controller=tienda&action=carrito';
            }, 600);
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cart-plus"></i> Agregar al Carrito';
        }
    });
});
</script>

<script>
let galeriaDetalle = <?php echo json_encode($galeria); ?>;
let indiceDetalle = 0;

function setImagenDetalle(idx) {
    if (!galeriaDetalle.length) return;
    indiceDetalle = ((idx % galeriaDetalle.length) + galeriaDetalle.length) % galeriaDetalle.length;
    const url = galeriaDetalle[indiceDetalle];
    const principal = document.getElementById('detalleImagenPrincipal');
    const modalImg = document.getElementById('modalDetalleImg');
    if (principal) principal.src = url;
    if (modalImg) modalImg.src = url;
}

function cambiarImagenDetalle(idx) {
    setImagenDetalle(idx);
}

function navegarDetalle(delta) {
    setImagenDetalle(indiceDetalle + delta);
}

function abrirModalDetalle(idx) {
    setImagenDetalle(idx);
    const modal = new bootstrap.Modal(document.getElementById('modalDetalleImagen'));
    modal.show();
}

// inicializar con la primera imagen
setImagenDetalle(0);
</script>

<?php
include APP_ROOT . '/app/views/layout/footer.php';
?>
