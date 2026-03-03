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

<div class="container-fluid px-4">
    <div class="row">
        <div class="col-lg-3">
            <div class="card sticky-top" style="top: 130px;">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-funnel"></i> Categorías</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo APP_URL; ?>" class="list-group-item list-group-item-action">
                            <i class="bi bi-house"></i> Todos
                        </a>
                        <?php foreach ($categorias as $cat): ?>
                        <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
                                type="button" data-bs-toggle="collapse" data-bs-target="#sub_<?php echo $cat['id']; ?>"
                                aria-expanded="<?php echo $cat['id'] == $categoria['id'] ? 'true' : 'false'; ?>">
                            <span><?php echo sanitizar($cat['nombre']); ?></span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="collapse <?php echo $cat['id'] == $categoria['id'] ? 'show' : ''; ?>" 
                             id="sub_<?php echo $cat['id']; ?>">
                            <div class="list-group list-group-flush">
                                <?php if (!empty($cat['subcategorias'])): ?>
                                    <?php foreach ($cat['subcategorias'] as $sub): ?>
                                    <a href="<?php echo APP_URL; ?>/index.php?controller=tienda&action=subcategoria&id=<?php echo $sub['id']; ?>" 
                                       class="list-group-item list-group-item-action ps-4">
                                       <i class="bi bi-dot"></i> <?php echo sanitizar($sub['nombre']); ?>
                                    </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>">Inicio</a></li>
                    <li class="breadcrumb-item active"><?php echo sanitizar($categoria['nombre']); ?></li>
                </ol>
            </nav>

            <!-- Encabezado de Categoría -->
            <div class="mb-5">
                <h1 class="mb-3" style="font-size: 2.5rem; font-weight: 700; color: #2c3e50;">
                    <i class="bi bi-tag-fill" style="color: #667eea;"></i> <?php echo sanitizar($categoria['nombre']); ?>
                </h1>
                <p class="text-muted mb-4" style="font-size: 1.1rem;"><?php echo sanitizar($categoria['descripcion']); ?></p>
                <div style="height: 4px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 200px; border-radius: 2px;"></div>
            </div>

            <!-- Título de Productos -->
            <div class="mb-5">
                <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px; color: #2c3e50;">
                    <i class="bi bi-box2" style="color: #667eea;"></i> Productos
                </h3>
            </div>

            <!-- Filtros y Búsqueda -->
            <div class="row mb-4 g-2">
                <div class="col-md-6">
                    <form method="GET" class="input-group">
                        <input type="hidden" name="controller" value="tienda">
                        <input type="hidden" name="action" value="categoria">
                        <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                        <input type="text" class="form-control" name="busqueda" placeholder="Buscar en categoría..." 
                               value="<?php echo isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : ''; ?>">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </div>
                <div class="col-md-6">
                    <select class="form-select" id="ordenSelect" onchange="cambiarOrden(this.value, '<?php echo $categoria['id']; ?>')">
                        <option value="">📊 Ordenar por...</option>
                        <option value="az" <?php echo isset($_GET['orden']) && $_GET['orden'] === 'az' ? 'selected' : ''; ?>>A → Z</option>
                        <option value="za" <?php echo isset($_GET['orden']) && $_GET['orden'] === 'za' ? 'selected' : ''; ?>>Z → A</option>
                        <option value="precio_menor" <?php echo isset($_GET['orden']) && $_GET['orden'] === 'precio_menor' ? 'selected' : ''; ?>>💰 Menor precio</option>
                        <option value="precio_mayor" <?php echo isset($_GET['orden']) && $_GET['orden'] === 'precio_mayor' ? 'selected' : ''; ?>>💰 Mayor precio</option>
                    </select>
                </div>
            </div>

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

            <!-- Productos -->
            <div class="row g-4 products-container view-columns">
                <?php if (empty($productos)): ?>
                    <div class="col-12">
                        <div class="alert alert-warning" style="padding: 40px; text-align: center; border-radius: 12px;">
                            <h4><i class="bi bi-exclamation-triangle"></i> Sin productos disponibles</h4>
                            <p style="margin: 10px 0 0 0;">No hay productos disponibles en esta categoría. Por favor, vuelve más tarde.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-12 text-muted mb-2">
                        <small>Se encontraron <strong><?php echo $total_productos; ?></strong> producto(s)</small>
                    </div>
                    <?php foreach ($productos as $producto): ?>
                    <?php 
                        $galeria = [];
                        if (!empty($producto['imagen'])) { $galeria[] = tienda_img_url($producto['imagen']); }
                        if (!empty($producto['imagen2'])) { $galeria[] = tienda_img_url($producto['imagen2']); }
                        if (!empty($producto['imagen3'])) { $galeria[] = tienda_img_url($producto['imagen3']); }
                        $galeriaAttr = htmlspecialchars(implode('|', $galeria), ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="col-6 col-md-6 col-lg-4 product-col">
                        <div class="product-card" data-galeria="<?php echo $galeriaAttr; ?>">
                            <div class="product-image-wrapper" onclick="abrirModalGaleria(this)">
                                  <img src="<?php echo $galeria[0] ?? tienda_img_url($producto['imagen']); ?>" 
                                      class="product-image" alt="<?php echo sanitizar($producto['nombre']); ?>">
                                <?php if ($producto['stock'] > 0): ?>
                                    <span class="product-badge"><i class="bi bi-check-circle"></i> Stock: <?php echo $producto['stock']; ?></span>
                                <?php else: ?>
                                    <span class="product-badge" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);"><i class="bi bi-x-circle"></i> Agotado</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h5 class="product-title">
                                    <?php echo sanitizar($producto['nombre']); ?>
                                </h5>
                                <p class="product-description">
                                    <?php echo substr(sanitizar($producto['descripcion']), 0, 100) . '...'; ?>
                                </p>
                                <div class="product-price">
                                    $<?php echo number_format($producto['precio'], 2); ?>
                                </div>
                                <div class="product-actions">
                                    <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=tienda&action=producto&id=<?php echo $producto['id']; ?>" 
                                       class="btn btn-primary" style="flex: 1.2;">
                                        <i class="bi bi-eye"></i> Detalles
                                    </a>
                                    <?php if ($producto['stock'] > 0): ?>
                                    <button class="btn btn-success agregar-carrito" 
                                            data-producto-id="<?php echo $producto['id']; ?>"
                                            data-nombre="<?php echo sanitizar($producto['nombre']); ?>">
                                        <i class="bi bi-bag-check"></i> Añadir
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>

            <!-- Modal galería catálogo -->
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
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Paginación -->
            <?php if (isset($total_paginas) && $total_paginas > 1): ?>
            <nav aria-label="Paginación" class="mt-5">
                <ul class="pagination justify-content-center">
                    <!-- Página anterior -->
                    <li class="page-item <?php echo $pagina_actual <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo $pagina_actual > 1 ? $this->construirUrlPaginacionCategoria($pagina_actual - 1, $categoria['id']) : '#'; ?>">
                            <i class="bi bi-chevron-left"></i> Anterior
                        </a>
                    </li>

                    <!-- Números de página -->
                    <?php 
                    $inicio = max(1, $pagina_actual - 2);
                    $fin = min($total_paginas, $pagina_actual + 2);
                    
                    if ($inicio > 1) {
                        echo '<li class="page-item"><a class="page-link" href="' . $this->construirUrlPaginacionCategoria(1, $categoria['id']) . '">1</a></li>';
                        if ($inicio > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    for ($i = $inicio; $i <= $fin; $i++) {
                        $activa = $i == $pagina_actual ? 'active' : '';
                        echo '<li class="page-item ' . $activa . '"><a class="page-link" href="' . $this->construirUrlPaginacionCategoria($i, $categoria['id']) . '">' . $i . '</a></li>';
                    }
                    
                    if ($fin < $total_paginas) {
                        if ($fin < $total_paginas - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="' . $this->construirUrlPaginacionCategoria($total_paginas, $categoria['id']) . '">' . $total_paginas . '</a></li>';
                    }
                    ?>

                    <!-- Página siguiente -->
                    <li class="page-item <?php echo $pagina_actual >= $total_paginas ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo $pagina_actual < $total_paginas ? $this->construirUrlPaginacionCategoria($pagina_actual + 1, $categoria['id']) : '#'; ?>">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function cambiarOrden(valor, categoriaId) {
    const url = new URL(window.location);
    const busqueda = document.querySelector('input[name="busqueda"]')?.value || '';
    
    url.searchParams.set('controller', 'tienda');
    url.searchParams.set('action', 'categoria');
    url.searchParams.set('id', categoriaId);
    if (valor) url.searchParams.set('orden', valor);
    if (busqueda) url.searchParams.set('busqueda', busqueda);
    
    window.location = url.toString();
}

// Galería catálogo
let galeriaCatalogo = [];
let idxCatalogo = 0;

function abrirModalGaleria(cardEl) {
    const modalEl = document.getElementById('modalGaleriaCatalogo');
    const imgEl = document.getElementById('modalCatalogoImg');
    if (!modalEl || !imgEl || !cardEl?.parentElement) return;

    const data = cardEl.parentElement.getAttribute('data-galeria') || '';
    galeriaCatalogo = data ? data.split('|').filter(Boolean) : [];
    if (!galeriaCatalogo.length) return;

    idxCatalogo = 0;
    actualizarCatalogoImg();
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function actualizarCatalogoImg() {
    const imgEl = document.getElementById('modalCatalogoImg');
    if (!imgEl || !galeriaCatalogo.length) return;

    idxCatalogo = ((idxCatalogo % galeriaCatalogo.length) + galeriaCatalogo.length) % galeriaCatalogo.length;
    imgEl.src = galeriaCatalogo[idxCatalogo] || '';
}

function navegarCatalogo(delta) {
    idxCatalogo += delta;
    actualizarCatalogoImg();
}
</script>


<?php
include APP_ROOT . '/app/views/layout/footer.php';
?>
