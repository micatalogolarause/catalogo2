<?php
/**
 * Test - Crear Tenant vía API
 * Prueba de provisioning automático
 */

require_once 'config/database.php';
require_once 'app/controllers/tenantAdminController.php';

$controller = new TenantAdminController();

// Datos del nuevo tenant de prueba
$nuevo_tenant = [
    'nombre' => 'Tech Store - Prueba',
    'slug' => 'tech-store',
    'whatsapp_phone' => '573334567890',
    'logo' => NULL,
    'tema' => 'claro',
    'estado' => 'activo',
    'admin_usuario' => 'admin_tech',
    'admin_email' => 'admin@techstore.local',
    'admin_password' => 'Tech123!@'
];

// Crear tenant
$resultado = $controller->crearTenant($nuevo_tenant);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Crear Tenant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-width: 900px; }
        h1 { color: #333; margin-bottom: 30px; }
        .result-card { padding: 20px; border-radius: 8px; margin: 15px 0; }
        .result-card.success { background: #e8f5e9; border-left: 5px solid #4caf50; }
        .result-card.error { background: #ffebee; border-left: 5px solid #f44336; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd; }
        .label { font-weight: bold; color: #667eea; }
        .value { color: #333; }
        .url-box { background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .code { background: #f5f5f5; padding: 10px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; margin: 2px; }
        .badge-success { background: #4caf50; color: white; }
        .badge-info { background: #2196f3; color: white; }
        table { margin-top: 20px; }
        th { background: #667eea; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test - Crear Nuevo Tenant con Provisioning</h1>
        
        <div class="result-card <?php echo $resultado['success'] ? 'success' : 'error'; ?>">
            <h3><?php echo $resultado['success'] ? '✅ Éxito' : '❌ Error'; ?></h3>
            
            <div class="info-row">
                <span class="label">Mensaje:</span>
                <span class="value"><?php echo $resultado['message']; ?></span>
            </div>
            
            <?php if ($resultado['success']): ?>
                <div class="info-row">
                    <span class="label">Tenant ID:</span>
                    <span class="value"><strong><?php echo $resultado['tenant_id']; ?></strong></span>
                </div>
                
                <div class="info-row">
                    <span class="label">Slug:</span>
                    <span class="value"><strong><?php echo $resultado['slug']; ?></strong></span>
                </div>
                
                <div class="url-box">
                    <strong>URL de Acceso:</strong>
                    <div class="code"><?php echo $resultado['url']; ?></div>
                </div>
                
                <div>
                    <strong>Admin Creado:</strong>
                    <div style="margin: 10px 0;">
                        <span class="badge badge-info">Usuario: admin_tech</span>
                        <span class="badge badge-info">Email: admin@techstore.local</span>
                        <span class="badge badge-success">Rol: Admin</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <hr>
        
        <h3>📊 Verificación de Datos Creados</h3>
        
        <?php if ($resultado['success']): 
            $tenant_id = $resultado['tenant_id'];
            $stats = $controller->obtenerEstadisticas($tenant_id);
            $tenant_info = $controller->obtenerTenant($tenant_id);
        ?>
            
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información del Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="label">Nombre:</span>
                        <span class="value"><?php echo $tenant_info['nombre']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Slug:</span>
                        <span class="value"><?php echo $tenant_info['slug']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">WhatsApp:</span>
                        <span class="value"><?php echo $tenant_info['whatsapp_phone']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Tema:</span>
                        <span class="value"><?php echo ucfirst($tenant_info['tema']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Estado:</span>
                        <span class="value">
                            <span class="badge" style="background: <?php echo $tenant_info['estado'] === 'activo' ? '#4caf50' : '#ff9800'; ?>; color: white;">
                                <?php echo ucfirst($tenant_info['estado']); ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Datos Aprovisionados</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Concepto</th>
                            <th>Cantidad</th>
                            <th>Valor</th>
                        </tr>
                        <tr>
                            <td>📂 Categorías</td>
                            <td><?php echo $stats['categorias']; ?></td>
                            <td>Electrónica, Ropa, Hogar</td>
                        </tr>
                        <tr>
                            <td>📦 Subcategorías</td>
                            <td>6</td>
                            <td>Smartphones, Laptops, Hombre, Mujer, Cocina, Dormitorio</td>
                        </tr>
                        <tr>
                            <td>👤 Admin User</td>
                            <td>1</td>
                            <td>admin_tech (admin@techstore.local)</td>
                        </tr>
                        <tr>
                            <td>📋 Productos</td>
                            <td><?php echo $stats['productos']; ?></td>
                            <td>Listo para agregar</td>
                        </tr>
                        <tr>
                            <td>👥 Clientes</td>
                            <td><?php echo $stats['clientes']; ?></td>
                            <td>Esperando primeras compras</td>
                        </tr>
                        <tr>
                            <td>💰 Ventas</td>
                            <td><?php echo $stats['pedidos']; ?></td>
                            <td>$ <?php echo number_format($stats['ventas_total'], 2, '.', ','); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">✅ Próximos Pasos</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>Acceder a la tienda:</strong> <a href="<?php echo $resultado['url']; ?>" target="_blank"><?php echo $resultado['url']; ?></a></li>
                        <li><strong>Agregar productos:</strong> Ir a admin panel y cargar inventario</li>
                        <li><strong>Configurar:</strong> Actualizar logo, tema y datos bancarios</li>
                        <li><strong>Publicar:</strong> Activar tenant cuando esté listo</li>
                    </ol>
                </div>
            </div>
            
        <?php endif; ?>
        
        <hr style="margin-top: 40px;">
        
        <h3>📋 Resumen de Todos los Tenants</h3>
        
        <?php
            $todos_tenants = $controller->obtenerTenants();
        ?>
        
        <div style="overflow-x: auto;">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>WhatsApp</th>
                        <th>Estado</th>
                        <th>Productos</th>
                        <th>Clientes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($todos_tenants as $t): 
                        $t_stats = $controller->obtenerEstadisticas($t['id']);
                    ?>
                        <tr>
                            <td><?php echo $t['id']; ?></td>
                            <td><strong><?php echo $t['nombre']; ?></strong></td>
                            <td><code><?php echo $t['slug']; ?></code></td>
                            <td><?php echo $t['whatsapp_phone']; ?></td>
                            <td>
                                <span class="badge" style="background: <?php echo $t['estado'] === 'activo' ? '#4caf50' : '#ff9800'; ?>; color: white;">
                                    <?php echo ucfirst($t['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo $t_stats['productos']; ?></td>
                            <td><?php echo $t_stats['clientes']; ?></td>
                            <td>
                                <a href="http://localhost/catalogo2/<?php echo $t['slug']; ?>" 
                                   class="btn btn-sm btn-primary" target="_blank">
                                    Visitar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
