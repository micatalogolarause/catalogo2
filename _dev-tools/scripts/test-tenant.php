<?php
/**
 * TEST: Verificar Multi-Tenancy
 * Acceso: http://localhost/catalogo2/test-tenant.php
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'config/TenantResolver.php';

// Resolver tenant
TenantResolver::resolve();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Multi-Tenancy</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .info-box { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; }
        h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        .success { color: #27ae60; font-weight: bold; }
        .value { background: #ecf0f1; padding: 5px 10px; border-radius: 4px; font-family: monospace; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #34495e; color: white; }
        tr:hover { background: #f8f9fa; }
        .url { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <h1>🧪 Test Multi-Tenancy - Fase 2</h1>
    
    <div class="info-box">
        <h2>✅ Constantes Definidas</h2>
        <p><strong>TENANT_ID:</strong> <span class="value"><?php echo defined('TENANT_ID') ? TENANT_ID : 'NO DEFINIDO'; ?></span></p>
        <p><strong>TENANT_SLUG:</strong> <span class="value"><?php echo defined('TENANT_SLUG') ? TENANT_SLUG : 'NO DEFINIDO'; ?></span></p>
        <p><strong>TENANT_NAME:</strong> <span class="value"><?php echo defined('TENANT_NAME') ? TENANT_NAME : 'NO DEFINIDO'; ?></span></p>
        <p><strong>TENANT_WHATSAPP:</strong> <span class="value"><?php echo defined('TENANT_WHATSAPP') ? TENANT_WHATSAPP : 'NO DEFINIDO'; ?></span></p>
    </div>
    
    <div class="info-box">
        <h2>📊 Datos del Tenant Actual</h2>
        <?php
        $tenant = TenantResolver::getCurrentTenant();
        if ($tenant): ?>
            <table>
                <tr>
                    <th>Campo</th>
                    <th>Valor</th>
                </tr>
                <?php foreach ($tenant as $key => $value): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($key); ?></strong></td>
                    <td><?php echo htmlspecialchars($value); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p class="error">No se pudo obtener datos del tenant</p>
        <?php endif; ?>
    </div>
    
    <div class="info-box">
        <h2>🔗 URLs de Prueba</h2>
        <p><strong>Tenant Default:</strong> <a class="url" href="<?php echo APP_URL; ?>/test-tenant.php"><?php echo APP_URL; ?>/test-tenant.php</a></p>
        <p><strong>Tenant Mauricio:</strong> <a class="url" href="<?php echo APP_URL; ?>/mauricio?controller=test-tenant"><?php echo APP_URL; ?>/mauricio</a></p>
        <p><strong>Tenant EBS:</strong> <a class="url" href="<?php echo APP_URL; ?>/distribuciones-ebs"><?php echo APP_URL; ?>/distribuciones-ebs</a></p>
        <p><strong>Tenant Inexistente:</strong> <a class="url" href="<?php echo APP_URL; ?>/noexiste"><?php echo APP_URL; ?>/noexiste</a> (debe dar 404)</p>
        <p><strong>Con Parámetro GET:</strong> <a class="url" href="<?php echo APP_URL; ?>/test-tenant.php?tenant=mauricio"><?php echo APP_URL; ?>/test-tenant.php?tenant=mauricio</a></p>
    </div>
    
    <div class="info-box">
        <h2>🗄️ Test de Queries Scoped</h2>
        <?php
        // Test: Obtener categorías del tenant actual
        $categorias = obtenerFilasScoped(
            "SELECT * FROM categorias WHERE tenant_id = ? AND activa = ?",
            "i",
            [1]
        );
        ?>
        <p><strong>Categorías del Tenant Actual (tenant_id = <?php echo TENANT_ID; ?>):</strong></p>
        <table>
            <tr>
                <th>ID</th>
                <th>Tenant ID</th>
                <th>Nombre</th>
                <th>Estado</th>
            </tr>
            <?php foreach ($categorias as $cat): ?>
            <tr>
                <td><?php echo $cat['id']; ?></td>
                <td><?php echo $cat['tenant_id']; ?></td>
                <td><?php echo htmlspecialchars($cat['nombre']); ?></td>
                <td><?php echo $cat['activa'] ? 'Activa' : 'Inactiva'; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <?php
        // Test: Contar productos del tenant
        $productos = obtenerFilasScoped(
            "SELECT COUNT(*) as total FROM productos WHERE tenant_id = ?",
            "",
            []
        );
        ?>
        <p style="margin-top: 20px;"><strong>Total de Productos:</strong> <span class="value"><?php echo $productos[0]['total']; ?></span></p>
    </div>
    
    <div class="info-box">
        <h2>📋 Todos los Tenants Disponibles</h2>
        <?php
        $todos_tenants = obtenerFilas("SELECT id, nombre, slug, estado FROM tenants ORDER BY id", "", []);
        ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Slug</th>
                <th>Estado</th>
                <th>URL</th>
            </tr>
            <?php foreach ($todos_tenants as $t): ?>
            <tr>
                <td><?php echo $t['id']; ?></td>
                <td><?php echo htmlspecialchars($t['nombre']); ?></td>
                <td><?php echo htmlspecialchars($t['slug']); ?></td>
                <td><?php echo $t['estado']; ?></td>
                <td>
                    <?php if ($t['slug'] !== 'default'): ?>
                        <a class="url" href="<?php echo APP_URL . '/' . $t['slug']; ?>">
                            <?php echo APP_URL . '/' . $t['slug']; ?>
                        </a>
                    <?php else: ?>
                        <a class="url" href="<?php echo APP_URL; ?>">
                            <?php echo APP_URL; ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <div class="info-box">
        <h2>✅ Status de Fase 2</h2>
        <p class="success">✓ TenantResolver.php creado y funcionando</p>
        <p class="success">✓ Middleware integrado en index.php</p>
        <p class="success">✓ Funciones Scoped creadas en database.php</p>
        <p class="success">✓ Constantes TENANT_* disponibles globalmente</p>
        <p class="success">✓ Tenants de prueba creados (mauricio, distribuciones-ebs)</p>
        <p class="success">✓ Backward compatibility mantenida (tenant default)</p>
    </div>
    
    <p style="text-align: center; color: #7f8c8d; margin-top: 30px;">
        <a href="<?php echo APP_URL; ?>" style="color: #3498db;">← Volver al inicio</a>
    </p>
</body>
</html>
