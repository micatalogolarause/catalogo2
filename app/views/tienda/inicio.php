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

<div class="row g-4">
    <!-- Contenido Principal -->
    <div class="col-12">


            <!-- Filtros y Búsqueda -->
            <div class="row mb-4 g-2">
                <div class="col-md-4">
                    <select class="form-select" id="categoriaSelect" onchange="cambiarCategoria(this.value)">
                        <option value="">📂 Todas las categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                        <optgroup label="<?php echo sanitizar($cat['nombre']); ?>">
                            <option value="categoria_<?php echo $cat['id']; ?>">
                                ▶ <?php echo sanitizar($cat['nombre']); ?>
                            </option>
                            <?php if (!empty($cat['subcategorias'])): ?>
                                <?php foreach ($cat['subcategorias'] as $sub): ?>
                                <option value="subcategoria_<?php echo $sub['id']; ?>">
                                    &nbsp;&nbsp;&nbsp;└ <?php echo sanitizar($sub['nombre']); ?>
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <form method="GET" class="input-group">
                        <input type="hidden" name="controller" value="tienda">
                        <input type="hidden" name="action" value="inicio">
                        <input type="text" class="form-control" name="busqueda" id="busquedaInput" placeholder="Buscar producto..." 
                               value="<?php echo isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : ''; ?>"
                               oninput="buscarEnVivo(this.value)">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="ordenSelect" onchange="cambiarOrden(this.value, 'inicio')">
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
                            <p style="margin: 10px 0 0 0;">No hay productos disponibles en este momento. Por favor, vuelve más tarde.</p>
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
                    <div class="col-md-6 col-lg-4 product-col">
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
                                    <a href="<?php echo APP_URL; ?>/index.php?controller=tienda&action=producto&id=<?php echo $producto['id']; ?>" 
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
                        <a class="page-link" href="<?php echo $pagina_actual > 1 ? $this->construirUrlPaginacion($pagina_actual - 1) : '#'; ?>">
                            <i class="bi bi-chevron-left"></i> Anterior
                        </a>
                    </li>

                    <!-- Números de página -->
                    <?php 
                    $inicio = max(1, $pagina_actual - 2);
                    $fin = min($total_paginas, $pagina_actual + 2);
                    
                    if ($inicio > 1) {
                        echo '<li class="page-item"><a class="page-link" href="' . $this->construirUrlPaginacion(1) . '">1</a></li>';
                        if ($inicio > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    for ($i = $inicio; $i <= $fin; $i++) {
                        $activa = $i == $pagina_actual ? 'active' : '';
                        echo '<li class="page-item ' . $activa . '"><a class="page-link" href="' . $this->construirUrlPaginacion($i) . '">' . $i . '</a></li>';
                    }
                    
                    if ($fin < $total_paginas) {
                        if ($fin < $total_paginas - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="' . $this->construirUrlPaginacion($total_paginas) . '">' . $total_paginas . '</a></li>';
                    }
                    ?>

                    <!-- Página siguiente -->
                    <li class="page-item <?php echo $pagina_actual >= $total_paginas ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo $pagina_actual < $total_paginas ? $this->construirUrlPaginacion($pagina_actual + 1) : '#'; ?>">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
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

<script>
let timerBusqueda;

function buscarEnVivo(texto) {
    clearTimeout(timerBusqueda);
    
    timerBusqueda = setTimeout(() => {
        const productos = document.querySelectorAll('.product-card');
        const textoBusqueda = texto.toLowerCase().trim();
        let encontrados = 0;
        
        productos.forEach(producto => {
            const nombre = producto.querySelector('.product-title').textContent.toLowerCase();
            const descripcion = producto.querySelector('.product-description').textContent.toLowerCase();
            
            if (textoBusqueda === '' || nombre.includes(textoBusqueda) || descripcion.includes(textoBusqueda)) {
                producto.closest('.col-md-6').style.display = '';
                encontrados++;
            } else {
                producto.closest('.col-md-6').style.display = 'none';
            }
        });
        
        // Mostrar mensaje si no hay resultados
        let mensajeNoResultados = document.getElementById('mensaje-no-resultados');
        if (encontrados === 0 && textoBusqueda !== '') {
            if (!mensajeNoResultados) {
                mensajeNoResultados = document.createElement('div');
                mensajeNoResultados.id = 'mensaje-no-resultados';
                mensajeNoResultados.className = 'col-12 alert alert-warning text-center';
                mensajeNoResultados.innerHTML = '<i class="bi bi-search"></i> No se encontraron productos que coincidan con "' + texto + '"';
                document.querySelector('.row.g-4').appendChild(mensajeNoResultados);
            }
        } else if (mensajeNoResultados) {
            mensajeNoResultados.remove();
        }
    }, 300); // Espera 300ms después de que el usuario deja de escribir
}

function cambiarCategoria(valor) {
    if (!valor) {
        window.location = '<?php echo APP_URL; ?>';
        return;
    }
    
    const parts = valor.split('_');
    const tipo = parts[0];
    const id = parts[1];
    
    if (tipo === 'categoria') {
        window.location = '<?php echo APP_URL; ?>/index.php?controller=tienda&action=categoria&id=' + id;
    } else if (tipo === 'subcategoria') {
        window.location = '<?php echo APP_URL; ?>/index.php?controller=tienda&action=subcategoria&id=' + id;
    }
}

function cambiarOrden(valor, accion) {
    const url = new URL(window.location);
    const busqueda = document.querySelector('input[name="busqueda"]')?.value || '';
    
    url.searchParams.set('controller', 'tienda');
    url.searchParams.set('action', accion);
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
