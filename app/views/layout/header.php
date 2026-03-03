<?php
// Verificar sesión
if (!session_id()) session_start();
// Fallback: cargar categorías para el menú si no vienen del controlador
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitizar($_SESSION['tenant_data']['titulo_empresa'] ?? $_SESSION['tenant_data']['nombre'] ?? 'Tienda Virtual'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/temas.css">
</head>
<body class="tema-<?php $t=sanitizar($_SESSION['tenant_data']['tema']??'claro'); echo ($t==='default'?'claro':$t); ?> color-<?php $c=sanitizar($_SESSION['tenant_data']['tema_color']??'azul'); echo ($c?$c:'azul'); ?>">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>">
                <?php
                $logoVal = $_SESSION['tenant_data']['logo'] ?? '';
                if (!empty($logoVal)) {
                    if (str_starts_with($logoVal, 'http://') || str_starts_with($logoVal, 'https://')) {
                        // Forzar https para evitar Mixed Content
                        $logoSrc = str_replace('http://', 'https://', $logoVal);
                    } else {
                        $logoSrc = is_file(APP_ROOT . '/' . $logoVal) ? (APP_URL . '/' . $logoVal) : '';
                    }
                    if ($logoSrc):
                ?>
                    <img src="<?php echo sanitizar($logoSrc); ?>" alt="Logo" style="max-height: 40px; margin-right: 15px; border-radius: 3px;">
                <?php endif; } ?>
                <span><?php echo sanitizar($_SESSION['tenant_data']['titulo_empresa'] ?? $_SESSION['tenant_data']['nombre'] ?? 'Tienda Virtual'); ?></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>">
                            <i class="bi bi-house"></i> Inicio
                        </a>
                    </li>
                    <!-- DESKTOP: dropdown de categorías -->
                    <li class="nav-item dropdown d-none d-lg-block">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriasDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-list"></i> Categorías
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="categoriasDropdown">
                            <?php foreach ($categorias as $cat): ?>
                            <li>
                                <a class="dropdown-item fw-semibold" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=tienda&action=categoria&id=<?php echo $cat['id']; ?>">
                                    <?php echo sanitizar($cat['nombre']); ?>
                                </a>
                            </li>
                                <?php if (!empty($cat['subcategorias'])): ?>
                                    <?php foreach ($cat['subcategorias'] as $sub): ?>
                                    <li>
                                        <a class="dropdown-item ps-4" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=tienda&action=subcategoria&id=<?php echo $sub['id']; ?>">
                                            <i class="bi bi-chevron-right small"></i> <?php echo sanitizar($sub['nombre']); ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <!-- MÓVIL: categorías directas (sin dropdown, 1 toque) -->
                    <li class="nav-item d-lg-none">
                        <span class="nav-link fw-bold text-muted" style="font-size:0.85rem; padding-bottom:2px;">
                            <i class="bi bi-list"></i> Categorías
                        </span>
                        <?php foreach ($categorias as $cat): ?>
                        <a class="nav-link py-1 ps-3" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=tienda&action=categoria&id=<?php echo $cat['id']; ?>">
                            <i class="bi bi-tag"></i> <?php echo sanitizar($cat['nombre']); ?>
                        </a>
                            <?php if (!empty($cat['subcategorias'])): ?>
                                <?php foreach ($cat['subcategorias'] as $sub): ?>
                                <a class="nav-link py-1 ps-5 text-muted" style="font-size:0.9rem;" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=tienda&action=subcategoria&id=<?php echo $sub['id']; ?>">
                                    <i class="bi bi-chevron-right small"></i> <?php echo sanitizar($sub['nombre']); ?>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=tienda&action=carrito">
                            <i class="bi bi-bag" style="font-size: 1.3rem;"></i> Carrito
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle" id="carrito-badge" style="display:none;">0</span>
                        </a>
                    </li>
                    <?php if (isset($_SESSION['cliente_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo sanitizar($_SESSION['cliente_nombre']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=usuario&action=perfil">
                                <i class="bi bi-person"></i> Mi Perfil
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=usuario&action=misPedidos">
                                <i class="bi bi-receipt"></i> Mis Pedidos
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=usuario&action=logout">
                                <i class="bi bi-door-open"></i> Cerrar Sesión
                            </a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <!-- Login/registro ocultos -->
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/<?php echo sanitizar($_SESSION['tenant_slug'] ?? ''); ?>/index.php?controller=admin&action=login">
                            <i class="bi bi-gear"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido -->
    <main class="container my-4">
        <?php 
        if (isset($_SESSION['success'])): 
        ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php 
        if (isset($_SESSION['error'])): 
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
