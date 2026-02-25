<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Crear Tenant</title>
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
        .section-title {
            border-left: 4px solid #667eea;
            padding-left: 15px;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .btn-create { background: #667eea; border: none; }
        .btn-create:hover { background: #764ba2; }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand">
                <i class="bi bi-shield-lock-fill"></i> Super Administrador
            </span>
            <div class="d-flex align-items-center">
                <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=tenants" class="btn btn-outline-light btn-sm me-2">
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
                        <i class="bi bi-building"></i> Crear Nuevo Tenant
                    </h2>

                    <form method="POST" action="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=crearTenant" enctype="multipart/form-data">
                        <!-- Información Básica del Tenant -->
                        <h5 class="section-title">Información Básica</h5>

                        <div class="mb-3">
                            <label class="form-label">Nombre del Tenant *</label>
                            <input type="text" class="form-control" name="nombre" required placeholder="ej: Mi Tienda Online">
                            <small class="text-muted">Nombre visible en el panel de administración</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Título de la Empresa</label>
                            <input type="text" class="form-control" name="titulo_empresa" placeholder="ej: Tienda Virtual XYZ">
                            <small class="text-muted">Este nombre se mostrará en el header de la tienda (deja vacío para usar el nombre del tenant)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug (URL única) *</label>
                            <input type="text" class="form-control" name="slug" required placeholder="ej: mi-tienda">
                            <small class="text-muted">Solo letras minúsculas, números y guiones. Ej: http://localhost/catalogo2/slug</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Número de WhatsApp *</label>
                            <div class="input-group">
                                <span class="input-group-text">+57</span>
                                <input type="text" class="form-control" name="whatsapp_phone" required placeholder="3001234567" pattern="[0-9]{10}" maxlength="10">
                            </div>
                            <small class="text-muted">Escribe solo los 10 dígitos del celular (Colombia). El prefijo +57 se agrega automáticamente.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo de la Tienda</label>
                            <input type="file" class="form-control" name="logo" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">JPG, PNG o WebP. Máximo 2MB. Se mostrará en el header de la tienda.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estilo</label>
                                    <select class="form-select" name="tema">
                                        <option value="claro">Claro</option>
                                        <option value="oscuro">Oscuro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Color Principal</label>
                                    <select class="form-select" name="tema_color">
                                        <option value="azul" selected>Azul</option>
                                        <option value="verde">Verde</option>
                                        <option value="rojo">Rojo</option>
                                        <option value="morado">Morado</option>
                                        <option value="naranja">Naranja</option>
                                        <option value="marino">Marino (corporativo)</option>
                                        <option value="grafito">Grafito (corporativo)</option>
                                        <option value="petroleo">Petróleo (corporativo)</option>
                                        <option value="acero">Acero (corporativo)</option>
                                        <option value="gris">Gris (corporativo)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado">
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Datos del Admin del Tenant -->
                        <h5 class="section-title">Usuario Administrador del Tenant</h5>

                        <div class="mb-3">
                            <label class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" name="admin_usuario" required placeholder="ej: Juan Pérez">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="admin_email" required placeholder="ej: admin@mitienda.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" name="admin_password" required minlength="6">
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>El sistema creará automáticamente:</strong>
                            <ul class="mb-0 mt-2">
                                <li>3 categorías iniciales (Electrónica, Ropa, Hogar)</li>
                                <li>6 subcategorías (Smartphones, Laptops, Hombre, Mujer, Cocina, Dormitorio)</li>
                                <li>Usuario administrador del tenant</li>
                                <li>Carpetas de uploads seguras (images, docs, temp)</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-create btn-lg">
                                <i class="bi bi-check-circle"></i> Crear Tenant
                            </button>
                            <a href="<?php echo APP_URL; ?>/index.php?controller=superAdmin&action=tenants" class="btn btn-outline-secondary">
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
