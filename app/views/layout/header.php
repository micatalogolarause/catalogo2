<?php
// Verificar sesión
if (!session_id()) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitizar($_SESSION['tenant_data']['titulo_empresa'] ?? $_SESSION['tenant_data']['nombre'] ?? 'Tienda Virtual'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/estilos.css?v=<?php echo filemtime(APP_ROOT . '/public/css/estilos.css'); ?>">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/temas.css?v=<?php echo filemtime(APP_ROOT . '/public/css/temas.css'); ?>">
    <link rel="icon" href="data:,">
</head>
<body class="tema-<?php $t=sanitizar($_SESSION['tenant_data']['tema']??'claro'); echo ($t==='default'?'claro':$t); ?> color-<?php $c=sanitizar($_SESSION['tenant_data']['tema_color']??'azul'); echo ($c?$c:'azul'); ?>">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo tenant_base_url(); ?>">
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
                        <a class="nav-link" href="<?php echo tenant_base_url(); ?>">
                            <i class="bi bi-house"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?php echo tenant_base_url(); ?>/index.php?controller=tienda&action=carrito">
                            <i class="bi bi-bag" style="font-size: 1.3rem;"></i> Carrito
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle" id="carrito-badge" style="display:none;">0</span>
                        </a>
                    </li>
                    <?php if (isset($_SESSION['cliente_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo sanitizar($_SESSION['cliente_nombre']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo tenant_base_url(); ?>/index.php?controller=usuario&action=perfil">
                                <i class="bi bi-person"></i> Mi Perfil
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo tenant_base_url(); ?>/index.php?controller=usuario&action=misPedidos">
                                <i class="bi bi-receipt"></i> Mis Pedidos
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo tenant_base_url(); ?>/index.php?controller=usuario&action=logout">
                                <i class="bi bi-door-open"></i> Cerrar Sesión
                            </a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <!-- Login/registro ocultos -->
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo tenant_base_url(); ?>/index.php?controller=admin&action=login">
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
