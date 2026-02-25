<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-person-circle"></i> Mi Perfil</h3>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Información Personal</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=admin&action=actualizarPerfil">
                    <input type="hidden" name="accion" value="info">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Usuario</label>
                        <input type="text" class="form-control" name="usuario" value="<?php echo sanitizar($usuario['usuario']); ?>" required>
                        <small class="text-muted">Nombre de usuario para iniciar sesión</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" class="form-control" name="nombre" value="<?php echo sanitizar($usuario['nombre']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" value="<?php echo sanitizar($usuario['email']); ?>" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Actualizar Información
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="bi bi-shield-lock"></i> Cambiar Contraseña</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=admin&action=actualizarPerfil">
                    <input type="hidden" name="accion" value="password">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contraseña Actual</label>
                        <input type="password" class="form-control" name="password_actual" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nueva Contraseña</label>
                        <input type="password" class="form-control" name="password_nueva" required minlength="6">
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-control" name="password_confirmar" required minlength="6">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key"></i> Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-image"></i> Logo del Tenant</h5>
            </div>
            <div class="card-body">
                <?php
                $lv = $_SESSION['tenant_data']['logo'] ?? '';
                if (!empty($lv)) {
                    if (str_starts_with($lv, 'http://') || str_starts_with($lv, 'https://')) {
                        $lsrc = $lv;
                    } else {
                        $lsrc = is_file(APP_ROOT . '/' . $lv) ? (APP_URL . '/' . $lv) : '';
                    }
                } else { $lsrc = ''; }
                if ($lsrc): ?>
                <div class="text-center mb-3">
                    <img src="<?php echo sanitizar($lsrc); ?>" 
                         alt="Logo actual" 
                         style="max-height: 120px; max-width: 100%; object-fit: contain;">
                    <p class="text-muted mt-2 mb-0"><small>Logo actual</small></p>
                </div>
                <?php else: ?>
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle"></i> No hay logo configurado
                </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=actualizarLogoTenant" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subir Nuevo Logo</label>
                        <input type="file" class="form-control" name="logo" accept="image/*" required>
                        <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB.</small>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload"></i> Actualizar Logo
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información de la Cuenta</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Rol:</strong> 
                    <span class="badge bg-success"><?php echo ucfirst($usuario['rol']); ?></span>
                </p>
                <p class="mb-2">
                    <strong>Estado:</strong> 
                    <span class="badge bg-<?php echo $usuario['activo'] ? 'success' : 'danger'; ?>">
                        <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                    </span>
                </p>
                <p class="mb-2">
                    <strong>Tenant:</strong> <?php echo sanitizar($_SESSION['tenant_data']['nombre']); ?>
                </p>
                <p class="mb-0">
                    <strong>Creado:</strong> <?php echo date('d/m/Y', strtotime($usuario['fecha_creacion'])); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
