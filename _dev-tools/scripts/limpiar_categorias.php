<?php
/**
 * LIMPIEZA: Identificar y eliminar categorías/subcategorías "huérfanas"
 * que no fueron creadas intencionalmente por el tenant
 */
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h2>Limpieza de Categorías Duplicadas</h2>";

// Listado de categorías que se crean automáticamente por tenant (SEED)
$auto_categories = ['Electrónica', 'Ropa', 'Hogar'];
$auto_subcategories = ['Smartphones', 'Laptops', 'Hombre', 'Mujer', 'Cocina', 'Dormitorio'];

// Tenants especiales que DEBEN mantener sus categorías
$protected_tenants = [
    1, // default - tiene sus propias categorías
    2, // mauricio - tiene sus propias categorías
    3, // distribuciones-ebs - tiene "Distribución" específica
];

echo "<p>Para revisar qué tenants pueden tener sus categorías eliminadas:</p>";

$sql_tenants = "SELECT id, slug, nombre FROM tenants ORDER BY id";
$result = $conn->query($sql_tenants);

$tenants_to_clean = [];

if ($result) {
    while ($tenant = $result->fetch_assoc()) {
        $tid = $tenant['id'];
        
        // Contar categorías para este tenant
        $sql = "SELECT COUNT(*) as total FROM categorias WHERE tenant_id = {$tid}";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        $cat_count = $row['total'];
        
        // Contar cuántas de sus categorías son las "estándar"
        $sql = "SELECT COUNT(*) as total FROM categorias WHERE tenant_id = {$tid} AND nombre IN ('" . implode("', '", $auto_categories) . "')";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        $standard_count = $row['total'];
        
        // Si TODAS sus categorías son las estándar, probablemente fue auto-creado
        if ($cat_count > 0 && $cat_count == $standard_count && !in_array($tid, $protected_tenants)) {
            $tenants_to_clean[] = [
                'id' => $tid,
                'slug' => $tenant['slug'],
                'nombre' => $tenant['nombre'],
                'categorias' => $cat_count
            ];
        }
        
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px 0;'>";
        echo "<strong>{$tenant['nombre']}</strong> (ID: {$tid}, slug: {$tenant['slug']})<br>";
        echo "Total categorías: {$cat_count}, Estándar: {$standard_count}";
        
        if (in_array($tid, $protected_tenants)) {
            echo " <span style='color: green;'>[PROTEGIDO]</span>";
        } else if ($cat_count > 0 && $cat_count == $standard_count) {
            echo " <span style='color: orange;'>[POSIBLE PARA LIMPIAR]</span>";
        }
        echo "</div>";
    }
}

if (!empty($tenants_to_clean)) {
    echo "<h3>Tenants que podrían tener sus categorías eliminadas:</h3>";
    foreach ($tenants_to_clean as $t) {
        echo "<p>";
        echo "<strong>" . $t['nombre'] . "</strong> (ID: {$t['id']}, {$t['categorias']} categorías) - ";
        echo "<a href='?action=clean&tenant_id={$t['id']}' style='color: red; text-decoration: underline;'>Limpiar</a>";
        echo "</p>";
    }
}

// Realizar limpieza si se solicita
if (isset($_GET['action']) && $_GET['action'] == 'clean' && isset($_GET['tenant_id'])) {
    $tenant_id = (int)$_GET['tenant_id'];
    
    if (!in_array($tenant_id, $protected_tenants)) {
        echo "<hr>";
        echo "<h3>Eliminando categorías para tenant {$tenant_id}...</h3>";
        
        // Primero eliminar subcategorías
        $sql = "DELETE FROM subcategorias WHERE tenant_id = {$tenant_id}";
        if ($conn->query($sql)) {
            $deleted_subs = $conn->affected_rows;
            echo "<p>✓ Subcategorías eliminadas: {$deleted_subs}</p>";
        }
        
        // Luego eliminar categorías
        $sql = "DELETE FROM categorias WHERE tenant_id = {$tenant_id}";
        if ($conn->query($sql)) {
            $deleted_cats = $conn->affected_rows;
            echo "<p>✓ Categorías eliminadas: {$deleted_cats}</p>";
        }
        
        echo "<p><strong>Limpieza completada para tenant {$tenant_id}</strong></p>";
        echo "<p><a href='diagnostico_categorias.php'>Volver al diagnóstico</a></p>";
    } else {
        echo "<p style='color: red;'><strong>ERROR: Ese tenant está protegido y no puede limpiarse.</strong></p>";
    }
}
?>
