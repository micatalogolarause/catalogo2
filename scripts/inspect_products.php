<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$tenantId = isset($argv[1]) ? (int)$argv[1] : 1;
// Seleccionar columnas reales (usar numero_producto si existe)
$rows = obtenerFilas("SELECT id, numero_producto, nombre, activo, categoria_id, tenant_id, fecha_creacion, fecha_actualizacion FROM productos WHERE tenant_id = ? LIMIT 20", "i", [$tenantId]);

foreach ($rows as $r) {
    echo json_encode($r) . "\n";
}

if (empty($rows)) echo "No products for tenant $tenantId\n";
