<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Limpiar tenants auto-creados (IDs 4 y 5: tech-store y larause)
$to_delete = [4, 5];

echo "Limpiando categorías duplicadas...\n\n";

foreach ($to_delete as $tid) {
    echo "Limpiando tenant ID {$tid}...\n";
    
    // Primero eliminar productos (tienen FK a subcategorías)
    $sql = "DELETE FROM productos WHERE tenant_id = {$tid}";
    if ($conn->query($sql)) {
        $count = $conn->affected_rows;
        echo "  ✓ Productos: {$count} eliminados\n";
    }
    
    // Luego eliminar subcategorías
    $sql = "DELETE FROM subcategorias WHERE tenant_id = {$tid}";
    if ($conn->query($sql)) {
        $count = $conn->affected_rows;
        echo "  ✓ Subcategorías: {$count} eliminadas\n";
    }
    
    // Finalmente eliminar categorías
    $sql = "DELETE FROM categorias WHERE tenant_id = {$tid}";
    if ($conn->query($sql)) {
        $count = $conn->affected_rows;
        echo "  ✓ Categorías: {$count} eliminadas\n";
    }
}

echo "\n✅ Limpieza completada.\n";
echo "Ahora estos tenants NO tendrán categorías hasta que las creen desde admin.\n";
?>
