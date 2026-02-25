<?php
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h2>Diagnóstico de Categorías por Tenant</h2>";

// Obtener todos los tenants
$sql_tenants = "SELECT id, slug, nombre FROM tenants ORDER BY id";
$result = $conn->query($sql_tenants);

if ($result) {
    while ($tenant = $result->fetch_assoc()) {
        echo "<h3>Tenant: {$tenant['nombre']} ({$tenant['slug']}) - ID: {$tenant['id']}</h3>";
        
        // Contar categorías para este tenant
        $sql = "SELECT COUNT(*) as total FROM categorias WHERE tenant_id = {$tenant['id']}";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        echo "  Categorías: <strong>{$row['total']}</strong><br>";
        
        // Contar subcategorías
        $sql = "SELECT COUNT(*) as total FROM subcategorias WHERE tenant_id = {$tenant['id']}";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        echo "  Subcategorías: <strong>{$row['total']}</strong><br>";
        
        // Listar categorías
        $sql = "SELECT id, nombre FROM categorias WHERE tenant_id = {$tenant['id']} LIMIT 5";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            echo "  <strong>Categorías:</strong> ";
            while ($cat = $res->fetch_assoc()) {
                echo "{$cat['nombre']}, ";
            }
            echo "<br>";
        }
    }
}

// Buscar categorías sin tenant_id o con tenant_id = 0/NULL
echo "<h3>⚠️ Categorías sin tenant_id definido:</h3>";
$sql = "SELECT id, nombre, tenant_id FROM categorias WHERE tenant_id IS NULL OR tenant_id = 0 OR tenant_id = ''";
$res = $conn->query($sql);
echo "  Total: <strong>{$res->num_rows}</strong><br>";
if ($res->num_rows > 0) {
    while ($cat = $res->fetch_assoc()) {
        echo "  - ID {$cat['id']}: {$cat['nombre']} (tenant_id: {$cat['tenant_id']})<br>";
    }
}

echo "<h3>⚠️ Subcategorías sin tenant_id definido:</h3>";
$sql = "SELECT id, nombre, tenant_id FROM subcategorias WHERE tenant_id IS NULL OR tenant_id = 0 OR tenant_id = ''";
$res = $conn->query($sql);
echo "  Total: <strong>{$res->num_rows}</strong><br>";
if ($res->num_rows > 0) {
    while ($sub = $res->fetch_assoc()) {
        echo "  - ID {$sub['id']}: {$sub['nombre']} (tenant_id: {$sub['tenant_id']})<br>";
    }
}
?>
