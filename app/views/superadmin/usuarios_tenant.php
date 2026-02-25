<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Usuarios del Tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .navbar-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .user-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
                <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=verTenant&id=<?php echo $tenant['id']; ?>" class="btn btn-outline-light btn-sm me-2">
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
                <h2><i class="bi bi-people"></i> Usuarios de <?php echo sanitizar($tenant['nombre']); ?></h2>
                <p class="text-muted">
                    <code><?php echo sanitizar($tenant['slug']); ?></code>
                    <span class="badge bg-<?php echo $tenant['estado'] === 'activo' ? 'success' : 'secondary'; ?> ms-2">
                        <?php echo ucfirst($tenant['estado']); ?>
                    </span>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                    <i class="bi bi-person-plus"></i> Crear Usuario
                </button>
            </div>
        </div>

        <div class="row">
            <?php if (empty($usuarios)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No hay usuarios registrados para este tenant.
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="user-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1"><?php echo sanitizar($usuario['nombre']); ?></h5>
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> <?php echo sanitizar($usuario['usuario']); ?>
                                </small>
                            </div>
                            <div>
                                <?php if ($usuario['activo']): ?>
                                <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-envelope"></i> <?php echo sanitizar($usuario['email']); ?>
                            </small><br>
                            <small class="text-muted">
                                <i class="bi bi-shield"></i> Rol: <strong><?php echo ucfirst($usuario['rol']); ?></strong>
                            </small><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> Creado: <?php echo date('d/m/Y', strtotime($usuario['fecha_creacion'])); ?>
                            </small>
                        </div>

                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                    data-bs-target="#resetPasswordModal<?php echo $usuario['id']; ?>">
                                <i class="bi bi-key"></i> Reset Pass
                            </button>
                            
                            <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=cambiarEstadoUsuario" style="display: inline;">
                                <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                <input type="hidden" name="tenant_id" value="<?php echo $tenant['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-<?php echo $usuario['activo'] ? 'secondary' : 'success'; ?>">
                                    <i class="bi bi-<?php echo $usuario['activo'] ? 'pause' : 'play'; ?>-circle"></i>
                                    <?php echo $usuario['activo'] ? 'Desactivar' : 'Activar'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Reset Password -->
                <div class="modal fade" id="resetPasswordModal<?php echo $usuario['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Resetear Contraseña</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=resetearPasswordUsuario">
                                <div class="modal-body">
                                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                    <input type="hidden" name="tenant_id" value="<?php echo $tenant['id']; ?>">
                                    
                                    <p><strong>Usuario:</strong> <?php echo sanitizar($usuario['usuario']); ?></p>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control" name="nueva_password" required minlength="6">
                                        <small class="text-muted">Mínimo 6 caracteres</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-key"></i> Resetear Contraseña
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Crear Usuario -->
    <div class="modal fade" id="crearUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Crear Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=crearUsuarioTenant">
                    <div class="modal-body">
                        <input type="hidden" name="tenant_id" value="<?php echo $tenant['id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Usuario *</label>
                            <input type="text" class="form-control" name="usuario" required>
                            <small class="text-muted">Sin espacios, solo letras y números</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" name="password" required minlength="6">
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Rol *</label>
                            <select class="form-select" name="rol" required>
                                <option value="admin">Admin</option>
                                <option value="editor">Editor</option>
                                <option value="viewer">Visualizador</option>
                            </select>
                        </div>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle"></i> 
                                El usuario será creado para el tenant: <strong><?php echo sanitizar($tenant['nombre']); ?></strong>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
