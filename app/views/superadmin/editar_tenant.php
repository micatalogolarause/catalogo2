<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tenant - Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .navbar-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
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

    <div class="container py-4">
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="form-card">
                    <h2 class="mb-4">
                        <i class="bi bi-pencil-square"></i> Editar Tenant
                    </h2>

                    <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=actualizarTenant" enctype="multipart/form-data">
                        <input type="hidden" name="tenant_id" value="<?php echo $tenant['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Slug (URL única) *</label>
                            <input type="text" class="form-control" value="<?php echo sanitizar($tenant['slug']); ?>" disabled>
                            <small class="text-muted">El slug no se puede modificar</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre del Tenant *</label>
                            <input type="text" class="form-control" name="nombre" value="<?php echo sanitizar($tenant['nombre']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Título de la Empresa</label>
                            <input type="text" class="form-control" name="titulo_empresa" value="<?php echo sanitizar($tenant['titulo_empresa'] ?? ''); ?>" placeholder="ej: Tienda Virtual XYZ">
                            <small class="text-muted">Este nombre se mostrará en el header de la tienda</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Número de WhatsApp *</label>
                            <div class="input-group">
                                <span class="input-group-text">+57</span>
                                <input type="text" class="form-control" name="whatsapp_phone" value="<?php echo sanitizar(ltrim($tenant['whatsapp_phone'], '+57')); ?>" required placeholder="3001234567" pattern="[0-9]{10}" maxlength="10">
                            </div>
                            <small class="text-muted">Solo número de 10 dígitos (sin código de país)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo de la Tienda</label>
                            <?php if (!empty($tenant['logo']) && is_file(APP_ROOT . '/' . $tenant['logo'])): ?>
                                <div class="mb-2">
                                    <img src="<?php echo APP_URL . '/' . $tenant['logo']; ?>" alt="Logo actual" style="max-height: 80px; border-radius: 5px;">
                                    <br><small class="text-muted">Logo actual</small>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="logo" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">JPG, PNG o WebP. Máximo 2MB. Déjalo vacío para mantener el logo actual.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Estilo</label>
                                    <select class="form-select" name="tema">
                                        <option value="claro" <?php echo ($tenant['tema'] ?? 'claro') === 'claro' ? 'selected' : ''; ?>>Claro</option>
                                        <option value="oscuro" <?php echo ($tenant['tema'] ?? 'claro') === 'oscuro' ? 'selected' : ''; ?>>Oscuro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Color Principal</label>
                                    <select class="form-select" name="tema_color">
                                        <option value="azul" <?php echo ($tenant['tema_color'] ?? 'azul') === 'azul' ? 'selected' : ''; ?>>Azul</option>
                                        <option value="verde" <?php echo ($tenant['tema_color'] ?? 'azul') === 'verde' ? 'selected' : ''; ?>>Verde</option>
                                        <option value="rojo" <?php echo ($tenant['tema_color'] ?? 'azul') === 'rojo' ? 'selected' : ''; ?>>Rojo</option>
                                        <option value="morado" <?php echo ($tenant['tema_color'] ?? 'azul') === 'morado' ? 'selected' : ''; ?>>Morado</option>
                                        <option value="naranja" <?php echo ($tenant['tema_color'] ?? 'azul') === 'naranja' ? 'selected' : ''; ?>>Naranja</option>
                                        <option value="marino" <?php echo ($tenant['tema_color'] ?? 'azul') === 'marino' ? 'selected' : ''; ?>>Marino (corporativo)</option>
                                        <option value="grafito" <?php echo ($tenant['tema_color'] ?? 'azul') === 'grafito' ? 'selected' : ''; ?>>Grafito (corporativo)</option>
                                        <option value="petroleo" <?php echo ($tenant['tema_color'] ?? 'azul') === 'petroleo' ? 'selected' : ''; ?>>Petróleo (corporativo)</option>
                                        <option value="acero" <?php echo ($tenant['tema_color'] ?? 'azul') === 'acero' ? 'selected' : ''; ?>>Acero (corporativo)</option>
                                        <option value="gris" <?php echo ($tenant['tema_color'] ?? 'azul') === 'gris' ? 'selected' : ''; ?>>Gris (corporativo)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado">
                                        <option value="activo" <?php echo $tenant['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                        <option value="inactivo" <?php echo $tenant['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                        <option value="bloqueado" <?php echo $tenant['estado'] === 'bloqueado' ? 'selected' : ''; ?>>Bloqueado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Nota:</strong> Si cambias el estado a "Inactivo", los clientes no podrán acceder a esta tienda.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </button>
                            <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=verTenant&id=<?php echo $tenant['id']; ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
