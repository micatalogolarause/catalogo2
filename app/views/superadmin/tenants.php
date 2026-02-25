<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Gestionar Tenants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .navbar-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .tenant-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .tenant-card:hover { box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .tenant-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .tenant-stats {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-item .value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-item .label {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand">
                <i class="bi bi-shield-lock-fill"></i> Super Administrador
            </span>
            <div class="d-flex align-items-center">
                <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=dashboard" class="btn btn-outline-light btn-sm me-2">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <span class="text-white me-3">
                    <i class="bi bi-person-circle"></i> <?php echo sanitizar($_SESSION['nombre']); ?>
                </span>
                <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=logout" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="bi bi-buildings"></i> Gestión de Tenants</h2>
                <p class="text-muted">Administrar todos los tenants del sistema</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=formularioCrearTenant" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Crear Nuevo Tenant
                </a>
            </div>
        </div>

        <div class="row">
            <?php foreach ($tenants as $tenant): ?>
            <div class="col-md-6 col-lg-4">
                <div class="tenant-card">
                    <div class="tenant-header">
                        <div>
                            <h5 class="mb-1"><?php echo sanitizar($tenant['nombre']); ?></h5>
                            <code class="text-muted"><?php echo sanitizar($tenant['slug']); ?></code>
                        </div>
                        <div>
                            <?php
                            $badge_class = 'success';
                            $badge_icon = 'check-circle-fill';
                            if ($tenant['estado'] === 'inactivo') {
                                $badge_class = 'secondary';
                                $badge_icon = 'x-circle-fill';
                            }
                            if ($tenant['estado'] === 'suspendido') {
                                $badge_class = 'warning';
                                $badge_icon = 'pause-circle-fill';
                            }
                            ?>
                            <span class="badge bg-<?php echo $badge_class; ?>">
                                <i class="bi bi-<?php echo $badge_icon; ?>"></i> <?php echo ucfirst($tenant['estado']); ?>
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="bi bi-whatsapp"></i> <?php echo sanitizar($tenant['whatsapp_phone']); ?>
                        </small>
                    </div>

                    <div class="tenant-stats">
                        <div class="stat-item">
                            <div class="value"><?php echo $tenant['stats']['productos']; ?></div>
                            <div class="label">Productos</div>
                        </div>
                        <div class="stat-item">
                            <div class="value"><?php echo $tenant['stats']['pedidos']; ?></div>
                            <div class="label">Pedidos</div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=verTenant&id=<?php echo $tenant['id']; ?>" 
                           class="btn btn-sm btn-info flex-fill">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                        <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=usuariosTenant&tenant_id=<?php echo $tenant['id']; ?>" 
                           class="btn btn-sm btn-secondary flex-fill">
                            <i class="bi bi-people"></i> Usuarios
                        </a>
                        <a href="<?php echo APP_URL; ?>/<?php echo $tenant['slug']; ?>" 
                           class="btn btn-sm btn-primary flex-fill" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> Visitar
                        </a>
                        
                        <?php if ($tenant['estado'] === 'activo'): ?>
                        <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=cambiarEstadoTenant" 
                              style="display: inline;" onsubmit="return confirm('¿Desactivar este tenant?');">
                            <input type="hidden" name="tenant_id" value="<?php echo $tenant['id']; ?>">
                            <input type="hidden" name="estado" value="inactivo">
                            <button type="submit" class="btn btn-sm btn-warning" title="Desactivar">
                                <i class="bi bi-pause-circle"></i>
                            </button>
                        </form>
                        <?php else: ?>
                        <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=cambiarEstadoTenant" 
                              style="display: inline;">
                            <input type="hidden" name="tenant_id" value="<?php echo $tenant['id']; ?>">
                            <input type="hidden" name="estado" value="activo">
                            <button type="submit" class="btn btn-sm btn-success" title="Activar">
                                <i class="bi bi-play-circle"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
