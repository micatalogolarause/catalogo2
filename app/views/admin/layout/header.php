<?php
if (!isset($_SESSION['usuario_id'])) {
    $tenantSlug = $_SESSION['tenant_slug'] ?? (defined('TENANT_SLUG') ? TENANT_SLUG : '');
    if (!empty($tenantSlug)) {
        header('Location: ' . APP_URL . '/' . $tenantSlug . '/index.php?controller=admin&action=login');
    } else {
        header('Location: ' . APP_URL . '/index.php?controller=admin&action=login');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - <?php echo sanitizar($_SESSION['tenant_data']['titulo_empresa'] ?? $_SESSION['tenant_data']['nombre'] ?? 'Tienda'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/admin.css">
</head>
<body>
    <!-- Navbar para móvil -->
    <nav class="navbar navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <span class="navbar-brand mb-0 h5 d-none d-md-inline">Panel Admin</span>
        </div>
    </nav>

    <!-- Offcanvas Sidebar -->
    <div class="offcanvas offcanvas-start bg-dark" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarLabel">
                <div class="offcanvas-header border-bottom border-secondary">
            <?php
            $logoVal = $_SESSION['tenant_data']['logo'] ?? '';
            if (!empty($logoVal)) {
                if (str_starts_with($logoVal, 'http://') || str_starts_with($logoVal, 'https://')) {
                    $logoSrc = $logoVal;
                } else {
                    $logoSrc = is_file(APP_ROOT . '/' . $logoVal) ? (APP_URL . '/' . $logoVal) : '';
                }
                if ($logoSrc):
            ?>
                <img src="<?php echo sanitizar($logoSrc); ?>" alt="Logo" style="max-height:40px; margin-right:10px;">
            <?php endif; } ?>
            <h5 class="offcanvas-title text-white" id="sidebarLabel">
                <i class="bi bi-house-gear"></i> <?php echo sanitizar($_SESSION['tenant_data']['titulo_empresa'] ?? $_SESSION['tenant_data']['nombre'] ?? 'Admin'); ?>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="position-sticky pt-3 w-100">
                <div class="text-center mb-4 pb-4 border-bottom border-secondary d-none d-md-block">
                    <small class="text-muted">Bienvenido, <?php echo sanitizar($_SESSION['nombre']); ?></small>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=inicio">
                            <i class="bi bi-graph-up"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=clientes">
                            <i class="bi bi-people"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=categorias">
                            <i class="bi bi-tags"></i> Categorías
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=subcategorias">
                            <i class="bi bi-tag"></i> Subcategorías
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=productos">
                            <i class="bi bi-box"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=pedidos">
                            <i class="bi bi-cart-check"></i> Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=facturas">
                            <i class="bi bi-receipt"></i> Cuentas de Cobro
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=configuracion">
                            <i class="bi bi-gear-fill"></i> Configuración
                        </a>
                    </li>
                </ul>

                <hr class="bg-secondary my-3">

                <div class="list-group list-group-flush">
                    <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=miPerfil" class="list-group-item list-group-item-action bg-dark text-white border-secondary">
                        <i class="bi bi-person-circle"></i> Mi Perfil
                    </a>
                    <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>" class="list-group-item list-group-item-action bg-dark text-white border-secondary">
                        <i class="bi bi-box-arrow-left"></i> Ir a la Tienda
                    </a>
                    <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=logout" class="list-group-item list-group-item-action bg-dark text-danger border-secondary">
                        <i class="bi bi-door-open"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar visible en desktop -->
            <nav class="col-md-2 bg-dark sidebar d-none d-md-block" style="min-height: 100vh; position: sticky; top: 0; overflow-y: auto;">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4 pb-4 border-bottom border-secondary">
                        <h5 class="text-white"><i class="bi bi-house-gear"></i> <?php echo sanitizar($_SESSION['tenant_data']['titulo_empresa'] ?? $_SESSION['tenant_data']['nombre'] ?? 'Admin'); ?></h5>
                        <small class="text-muted">Bienvenido, <?php echo sanitizar($_SESSION['nombre']); ?></small>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=inicio">
                                <i class="bi bi-graph-up"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=clientes">
                                <i class="bi bi-people"></i> Clientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=categorias">
                                <i class="bi bi-tags"></i> Categorías
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=subcategorias">
                                <i class="bi bi-tag"></i> Subcategorías
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=productos">
                                <i class="bi bi-box"></i> Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=pedidos">
                                <i class="bi bi-cart-check"></i> Pedidos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=facturas">
                                <i class="bi bi-receipt"></i> Cuentas de Cobro
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=configuracion">
                                <i class="bi bi-gear-fill"></i> Configuración
                            </a>
                        </li>
                    </ul>

                    <hr class="bg-secondary">

                    <div class="list-group">
                        <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=miPerfil" class="list-group-item list-group-item-action bg-dark text-white">
                            <i class="bi bi-person-circle"></i> Mi Perfil
                        </a>
                        <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>" class="list-group-item list-group-item-action bg-dark text-white">
                            <i class="bi bi-box-arrow-left"></i> Ir a la Tienda
                        </a>
                        <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>/index.php?controller=admin&action=logout" class="list-group-item list-group-item-action bg-dark text-danger">
                            <i class="bi bi-door-open"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-md-auto px-md-4">
                <!-- Top Bar -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Panel de Control</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <small class="text-muted d-none d-md-inline">Última actualización: <?php echo date('d/m/Y H:i'); ?></small>
                    </div>
                </div>

                <!-- Alertas -->
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> <?php echo $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); endif; ?>
