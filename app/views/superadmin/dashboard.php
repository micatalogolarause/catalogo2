<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .navbar-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .tenant-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .badge-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
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
        <div class="row mb-4">
            <div class="col-12">
                <h2><i class="bi bi-speedometer2"></i> Panel de Control Global</h2>
                <p class="text-muted">Gestión completa de todos los tenants del sistema</p>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Tenants</p>
                            <h3 class="mb-0"><?php echo $stats['total_tenants']; ?></h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                            <i class="bi bi-buildings"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Activos</p>
                            <h3 class="mb-0"><?php echo $stats['tenants_activos']; ?></h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(76, 175, 80, 0.1); color: #4caf50;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Productos</p>
                            <h3 class="mb-0"><?php echo number_format($stats['total_productos']); ?></h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(255, 152, 0, 0.1); color: #ff9800;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Ventas Totales</p>
                            <h3 class="mb-0">$<?php echo number_format($stats['ventas_total'], 2); ?></h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(76, 175, 80, 0.1); color: #4caf50;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navegación rápida -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="btn-group" role="group">
                    <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=dashboard" class="btn btn-primary">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=tenants" class="btn btn-outline-primary">
                        <i class="bi bi-buildings"></i> Gestionar Tenants
                    </a>
                    <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=formularioCrearTenant" class="btn btn-outline-success">
                        <i class="bi bi-plus-circle"></i> Crear Nuevo Tenant
                    </a>
                </div>
            </div>
        </div>

        <!-- Lista de Tenants -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Tenants Registrados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Slug</th>
                                        <th>WhatsApp</th>
                                        <th>Estado</th>
                                        <th>Productos</th>
                                        <th>Pedidos</th>
                                        <th>Clientes</th>
                                        <th>Ventas</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tenants as $tenant): ?>
                                    <tr>
                                        <td><?php echo $tenant['id']; ?></td>
                                        <td>
                                            <strong><?php echo sanitizar($tenant['nombre']); ?></strong>
                                        </td>
                                        <td>
                                            <code><?php echo sanitizar($tenant['slug']); ?></code>
                                        </td>
                                        <td><?php echo sanitizar($tenant['whatsapp_phone']); ?></td>
                                        <td>
                                            <?php
                                            $badge_class = 'success';
                                            if ($tenant['estado'] === 'inactivo') $badge_class = 'secondary';
                                            if ($tenant['estado'] === 'suspendido') $badge_class = 'warning';
                                            ?>
                                            <span class="badge bg-<?php echo $badge_class; ?>">
                                                <?php echo ucfirst($tenant['estado']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($tenant['stats']['productos']); ?></td>
                                        <td><?php echo number_format($tenant['stats']['pedidos']); ?></td>
                                        <td><?php echo number_format($tenant['stats']['clientes']); ?></td>
                                        <td>$<?php echo number_format($tenant['stats']['ventas'] ?? 0, 2); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=verTenant&id=<?php echo $tenant['id']; ?>" 
                                                   class="btn btn-info" title="Ver detalles">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo APP_URL; ?>/<?php echo $tenant['slug']; ?>" 
                                                   class="btn btn-primary" target="_blank" title="Visitar tienda">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
