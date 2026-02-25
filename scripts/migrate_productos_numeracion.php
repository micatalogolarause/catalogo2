<?php
/**
 * Migración: Agregar numero_producto secuencial por tenant
 * Cada tenant tendrá su propia numeración de productos comenzando desde 1
 */

include 'config/database.php';

echo "=== MIGRANDO PRODUCTOS A NUMERACIÓN POR TENANT ===\n\n";

// 1. Agregar columna numero_producto si no existe
$sql_check = "SHOW COLUMNS FROM productos LIKE 'numero_producto'";
$result = $GLOBALS['conn']->query($sql_check);

if ($result->num_rows == 0) {
    echo "Agregando columna numero_producto...\n";
    $sql_alter = "ALTER TABLE productos ADD COLUMN numero_producto INT NOT NULL DEFAULT 0 AFTER id";
    if ($GLOBALS['conn']->query($sql_alter)) {
        echo "✓ Columna numero_producto agregada\n";
    } else {
        echo "✗ Error al agregar columna: " . $GLOBALS['conn']->error . "\n";
        exit;
    }
} else {
    echo "✓ Columna numero_producto ya existe\n";
}

// 2. Obtener lista de tenants
echo "\nNumerando productos por tenant...\n";
$sql_tenants = "SELECT DISTINCT tenant_id FROM productos ORDER BY tenant_id";
$result_tenants = $GLOBALS['conn']->query($sql_tenants);

$updated_count = 0;
while ($tenant = $result_tenants->fetch_assoc()) {
    $tenant_id = $tenant['tenant_id'];
    
    // Obtener productos del tenant ordenados por ID (fecha de creación)
    $sql_productos = "SELECT id FROM productos WHERE tenant_id = ? ORDER BY id ASC";
    $stmt = $GLOBALS['conn']->prepare($sql_productos);
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $result_productos = $stmt->get_result();
    
    $numero = 1;
    while ($producto = $result_productos->fetch_assoc()) {
        $sql_update = "UPDATE productos SET numero_producto = ? WHERE id = ?";
        $stmt_update = $GLOBALS['conn']->prepare($sql_update);
        $stmt_update->bind_param("ii", $numero, $producto['id']);
        $stmt_update->execute();
        $numero++;
        $updated_count++;
    }
    
    echo "✓ Tenant $tenant_id: $numero productos numerados (1-" . ($numero - 1) . ")\n";
}

echo "\n✓ Total de productos renumerados: $updated_count\n";
echo "Migración completada exitosamente.\n";
?>
