<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Detalle Tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .navbar-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand">
                <i class="bi bi-shield-lock-fill"></i> Super Administrador
            </span>
            <div class="d-flex align-items-center">                <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=usuariosTenant&tenant_id=<?php echo $tenant['id']; ?>" class="btn btn-outline-light btn-sm me-2">
                    <i class="bi bi-people"></i> Usuarios
                </a>                <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=tenants" class="btn btn-outline-light btn-sm me-2">
                    <i class="bi bi-arrow-left"></i> Volver
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
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><?php echo sanitizar($tenant['nombre']); ?></h2>
                <p class="text-muted">
                    <code><?php echo sanitizar($tenant['slug']); ?></code>
                    <span class="badge bg-<?php echo $tenant['estado'] === 'activo' ? 'success' : 'secondary'; ?> ms-2">
                        <?php echo ucfirst($tenant['estado']); ?>
                    </span>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo APP_URL; ?>/<?php echo $tenant['slug']; ?>" class="btn btn-primary" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> Visitar Tienda
                </a>
            </div>
        </div>

        <!-- Información del Tenant -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th>ID:</th>
                                <td><?php echo $tenant['id']; ?></td>
                            </tr>
                            <tr>
                                <th>Nombre:</th>
                                <td><?php echo sanitizar($tenant['nombre']); ?></td>
                            </tr>
                            <tr>
                                <th>Slug:</th>
                                <td><code><?php echo sanitizar($tenant['slug']); ?></code></td>
                            </tr>
                            <tr>
                                <th>WhatsApp:</th>
                                <td><?php echo sanitizar($tenant['whatsapp_phone']); ?></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="badge bg-<?php echo $tenant['estado'] === 'activo' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($tenant['estado']); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Creación:</th>
                                <td><?php echo date('d/m/Y H:i', strtotime($tenant['created_at'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-gear"></i> Acciones</h5>
                    </div>
                    <div class="card-body">
                        <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=editarTenant&id=<?php echo $tenant['id']; ?>" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-pencil-square"></i> Editar Tenant
                        </a>
                        
                        <?php if ($tenant['estado'] === 'activo'): ?>
                        <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=cambiarEstadoTenant" 
                              onsubmit="return confirm('¿Desactivar este tenant? Los clientes no podrán acceder.');">
                            <input type="hidden" name="tenant_id" value="<?php echo $tenant['id']; ?>">
                            <input type="hidden" name="estado" value="inactivo">
                            <button type="submit" class="btn btn-warning w-100 mb-2">
                                <i class="bi bi-pause-circle"></i> Desactivar Tenant
                            </button>
                        </form>
                        <?php else: ?>
                        <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=cambiarEstadoTenant">
                            <input type="hidden" name="tenant_id" value="<?php echo $tenant['id']; ?>">
                            <input type="hidden" name="estado" value="activo">
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-play-circle"></i> Activar Tenant
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=eliminarTenant" 
                              onsubmit="return confirm('¿ELIMINAR permanentemente este tenant? Esta acción no se puede deshacer.');">
                            <input type="hidden" name="tenant_id" value="<?php echo $tenant['id']; ?>">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> Eliminar Tenant
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-value"><?php echo $stats['productos']; ?></div>
                    <div class="text-muted">Productos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-value"><?php echo $stats['pedidos']; ?></div>
                    <div class="text-muted">Pedidos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-value"><?php echo $stats['clientes']; ?></div>
                    <div class="text-muted">Clientes</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-value">$<?php echo number_format($stats['ventas'] ?? 0, 0); ?></div>
                    <div class="text-muted">Ventas</div>
                </div>
            </div>
        </div>

        <!-- Pedidos Recientes -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Últimos Pedidos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($pedidos_recientes)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No hay pedidos registrados</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($pedidos_recientes as $pedido): ?>
                                        <tr>
                                            <td>#<?php echo $pedido['id']; ?></td>
                                            <td><?php echo sanitizar($pedido['cliente_nombre'] ?? 'N/A'); ?></td>
                                            <td>$<?php echo number_format($pedido['total'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo ucfirst($pedido['estado']); ?></span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_creacion'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
