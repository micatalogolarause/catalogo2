<?php
/**
 * Admin - Crear Nuevo Tenant
 * Formulario y lógica para provisioning de tenants
 */

session_start();
require_once '../config/database.php';
require_once '../app/controllers/tenantAdminController.php';

$controller = new TenantAdminController();
$message = '';
$message_type = '';
$new_tenant = null;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nombre' => sanitizar($_POST['nombre'] ?? ''),
        'slug' => sanitizar($_POST['slug'] ?? ''),
        'whatsapp_phone' => sanitizar($_POST['whatsapp_phone'] ?? ''),
        'logo' => sanitizar($_POST['logo'] ?? ''),
        'tema' => sanitizar($_POST['tema'] ?? 'claro'),
        'estado' => sanitizar($_POST['estado'] ?? 'activo'),
        'admin_usuario' => sanitizar($_POST['admin_usuario'] ?? ''),
        'admin_email' => sanitizar($_POST['admin_email'] ?? ''),
        'admin_password' => sanitizar($_POST['admin_password'] ?? ''),
    ];
    
    $resultado = $controller->crearTenant($data);
    
    if ($resultado['success']) {
        $message = "✅ " . $resultado['message'];
        $message_type = 'success';
        $new_tenant = $resultado;
    } else {
        $message = "❌ " . $resultado['message'];
        $message_type = 'error';
    }
}

// Obtener lista de tenants
$tenants = $controller->obtenerTenants();

function sanitizar($texto) {
    return htmlspecialchars(trim($texto));
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Crear Tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 0; }
        .container { max-width: 1200px; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-section { background: white; padding: 30px; border-radius: 8px; }
        .btn-primary { background: #667eea; border: none; }
        .btn-primary:hover { background: #764ba2; }
        .alert { border-radius: 8px; }
        .tenant-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; }
        .tenant-card { background: white; padding: 15px; border-radius: 8px; border: 2px solid #ddd; }
        .tenant-card:hover { border-color: #667eea; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .badge-estado { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-activo { background: #e8f5e9; color: #2e7d32; }
        .badge-inactivo { background: #fff3e0; color: #f57c00; }
        .success-box { background: #e8f5e9; border-left: 5px solid #4caf50; padding: 15px; border-radius: 5px; }
        .url-copy { background: #f5f5f5; padding: 10px; border-radius: 5px; word-break: break-all; }
        .form-input-group { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>🏢 Admin - Gestor de Tenants</h1>
            <p style="margin: 0;">Crear y gestionar nuevos tenants con provisioning automático</p>
        </div>
    </div>
    
    <div class="container py-4">
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($new_tenant): ?>
            <div class="success-box">
                <h4>✅ Tenant Creado Exitosamente</h4>
                <div style="margin-top: 15px;">
                    <div><strong>ID:</strong> <?php echo $new_tenant['tenant_id']; ?></div>
                    <div><strong>Slug:</strong> <?php echo $new_tenant['slug']; ?></div>
                    <div><strong>URL de Acceso:</strong></div>
                    <div class="url-copy"><?php echo $new_tenant['url']; ?></div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="form-section">
                    <h3>➕ Crear Nuevo Tenant</h3>
                    <hr>
                    
                    <form method="POST" class="needs-validation">
                        
                        <div class="form-input-group">
                            <label class="form-label"><strong>Nombre del Tenant *</strong></label>
                            <input type="text" name="nombre" class="form-control" placeholder="ej: Mi Tienda Online" required>
                            <small class="text-muted">Nombre visible del tenant</small>
                        </div>
                        
                        <div class="form-input-group">
                            <label class="form-label"><strong>Slug (URL) *</strong></label>
                            <input type="text" name="slug" class="form-control" placeholder="ej: mi-tienda" 
                                   pattern="^[a-z0-9-]+$" required>
                            <small class="text-muted">Parte de la URL: /catalogo2/<strong>slug</strong></small>
                        </div>
                        
                        <div class="form-input-group">
                            <label class="form-label"><strong>WhatsApp Business *</strong></label>
                            <input type="tel" name="whatsapp_phone" class="form-control" 
                                   placeholder="ej: 573112969569" required>
                            <small class="text-muted">Número para recibir pedidos (incluir código país)</small>
                        </div>
                        
                        <div class="form-input-group">
                            <label class="form-label"><strong>Tema</strong></label>
                            <select name="tema" class="form-select">
                                <option value="claro">Claro</option>
                                <option value="oscuro">Oscuro</option>
                            </select>
                        </div>
                        
                        <div class="form-input-group">
                            <label class="form-label"><strong>Estado</strong></label>
                            <select name="estado" class="form-select">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="bloqueado">Bloqueado</option>
                            </select>
                        </div>
                        
                        <hr>
                        <h5>Admin Inicial</h5>
                        
                        <div class="form-input-group">
                            <label class="form-label"><strong>Usuario Admin *</strong></label>
                            <input type="text" name="admin_usuario" class="form-control" placeholder="ej: admin" required>
                        </div>
                        
                        <div class="form-input-group">
                            <label class="form-label"><strong>Email Admin *</strong></label>
                            <input type="email" name="admin_email" class="form-control" placeholder="ej: admin@mitienda.com" required>
                        </div>
                        
                        <div class="form-input-group">
                            <label class="form-label"><strong>Contraseña Admin</strong></label>
                            <input type="password" name="admin_password" class="form-control" placeholder="admin123">
                            <small class="text-muted">Si no ingresa, usará "admin123"</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            🚀 Crear Tenant
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="form-section">
                    <h3>📋 Tenants Existentes</h3>
                    <hr>
                    
                    <?php if (empty($tenants)): ?>
                        <p class="text-muted">No hay tenants creados</p>
                    <?php else: ?>
                        <div class="tenant-grid">
                            <?php foreach ($tenants as $tenant): 
                                $stats = $controller->obtenerEstadisticas($tenant['id']);
                            ?>
                                <div class="tenant-card">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                        <div>
                                            <h6 style="margin: 0;"><?php echo $tenant['nombre']; ?></h6>
                                            <small class="text-muted"><?php echo $tenant['slug']; ?></small>
                                        </div>
                                        <span class="badge-estado badge-<?php echo $tenant['estado']; ?>">
                                            <?php echo ucfirst($tenant['estado']); ?>
                                        </span>
                                    </div>
                                    
                                    <div style="font-size: 12px; line-height: 1.8;">
                                        <div>📦 Productos: <strong><?php echo $stats['productos']; ?></strong></div>
                                        <div>👥 Clientes: <strong><?php echo $stats['clientes']; ?></strong></div>
                                        <div>📋 Pedidos: <strong><?php echo $stats['pedidos']; ?></strong></div>
                                        <div>💰 Ventas: <strong>$ <?php echo number_format($stats['ventas_total'], 0, ',', '.'); ?></strong></div>
                                    </div>
                                    
                                    <hr style="margin: 8px 0;">
                                    
                                    <a href="http://localhost/catalogo2/<?php echo $tenant['slug']; ?>" 
                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                        🔗 Acceder
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
