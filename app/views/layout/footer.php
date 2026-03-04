<?php
// Fallback en el footer: asegurar $categorias disponible
if (!isset($categorias) || !is_array($categorias) || empty($categorias)) {
    try {
        require_once APP_ROOT . '/config/database.php';
        require_once APP_ROOT . '/app/models/CategoriaModel.php';
        require_once APP_ROOT . '/app/models/SubcategoriaModel.php';
        $categoriaModel = new CategoriaModel($conn);
        $subcategoriaModel = new SubcategoriaModel($conn);
        $categorias = $categoriaModel->obtenerTodas();
        foreach ($categorias as &$cat) {
            $cat['subcategorias'] = $subcategoriaModel->obtenerPorCategoria($cat['id']);
        }
        unset($cat);
    } catch (Throwable $e) {
        $categorias = array();
    }
}
?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <h5 style="font-weight: 700; margin-bottom: 15px;">
                        <i class="bi bi-shop"></i> Tienda Virtual
                    </h5>
                    <p style="opacity: 0.85;">
                        Tu tienda de confianza para comprar en línea. Productos de calidad garantizada con envío rápido y seguro.
                    </p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 style="font-weight: 700; margin-bottom: 15px;">
                        <i class="bi bi-list"></i> Categorías
                    </h5>
                    <ul class="list-unstyled">
                        <?php foreach ($categorias as $cat): ?>
                        <li style="margin-bottom: 8px;">
                            <a href="<?php echo APP_URL; ?>/index.php?controller=tienda&action=categoria&id=<?php echo $cat['id']; ?>" 
                               style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.3s;">
                                <i class="bi bi-chevron-right"></i> <?php echo sanitizar($cat['nombre']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 style="font-weight: 700; margin-bottom: 15px;">
                        <i class="bi bi-telephone"></i> Contacto
                    </h5>
                    <p style="opacity: 0.85; margin: 0;">
                        <strong>Email:</strong> info@tienda.local<br>
                        <strong>Teléfono:</strong> +57 300 000 0000<br>
                        <strong>Horario:</strong> Lun - Vie 9am - 6pm
                    </p>
                </div>
            </div>
            <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; text-align: center; opacity: 0.8;">
                <p style="margin: 0; font-size: 0.95rem;">
                    &copy; 2024 Tienda Virtual. Todos los derechos reservados. 
                    <i class="bi bi-shield-check"></i> Sitio seguro
                </p>
            </div>
        </div>
    </footer>

    <!-- Barra flotante móvil - Carrito y acceso rápido -->
    <div class="mobile-bottom-nav d-md-none">
        <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>" class="mobile-nav-item" title="Inicio">
            <i class="bi bi-house"></i>
            <span>Inicio</span>
        </a>
        <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=tienda&action=carrito" class="mobile-nav-item cart-item" title="Carrito">
            <div style="position: relative; display: inline-block;">
                <i class="bi bi-bag-fill"></i>
                <span class="badge-mobile" id="carrito-badge-mobile" style="display:none;">0</span>
            </div>
            <span>Carrito</span>
        </a>
        <?php if (isset($_SESSION['cliente_id'])): ?>
        <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=usuario&action=misPedidos" class="mobile-nav-item" title="Pedidos">
            <i class="bi bi-receipt"></i>
            <span>Pedidos</span>
        </a>
        <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=usuario&action=perfil" class="mobile-nav-item" title="Perfil">
            <i class="bi bi-person"></i>
            <span>Perfil</span>
        </a>
        <?php else: ?>
        <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=usuario&action=login" class="mobile-nav-item" title="Iniciar sesión">
            <i class="bi bi-door-open"></i>
            <span>Sesión</span>
        </a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var APP_BASE_URL = '<?php echo defined('TENANT_SLUG') && TENANT_SLUG ? rtrim(APP_URL, '/') . '/' . TENANT_SLUG : rtrim(APP_URL, '/'); ?>';
        var TENANT_SLUG  = '<?php echo defined('TENANT_SLUG') ? TENANT_SLUG : ''; ?>';
    </script>
    <script src="<?php echo APP_URL; ?>/public/js/main.js"></script>
    <script>
        // Actualizar badge del carrito en barra móvil
        function actualizarCarroBadgeMobile() {
            const badge = document.getElementById('carrito-badge-mobile');
            const badgeHeader = document.getElementById('carrito-badge');
            if (badge && badgeHeader) {
                badge.textContent = badgeHeader.textContent;
                if (badgeHeader.style.display !== 'none') {
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
        // Actualizar cada segundo
        setInterval(actualizarCarroBadgeMobile, 500);
    </script>
</body>
</html>
