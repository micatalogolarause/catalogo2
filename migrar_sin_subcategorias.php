<?php
/**
 * Migración: Eliminar subcategorías
 * 
 * Este script mueve los productos a la categoría padre de su subcategoría
 * y limpia el campo subcategoria_id.
 * 
 * EJECUTAR UNA SOLA VEZ. Borrar después de ejecutar.
 */

// Incluir configuración (ajusta si la ruta es diferente)
define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');
echo "<pre>\n";
echo "=== MIGRACIÓN: ELIMINAR SUBCATEGORÍAS ===\n\n";

// 1. Mostrar situación actual
$res = $conn->query("SELECT COUNT(*) as total FROM productos WHERE subcategoria_id IS NOT NULL AND subcategoria_id > 0");
$row = $res->fetch_assoc();
echo "Productos con subcategoria_id: " . $row['total'] . "\n\n";

// 2. Migrar productos: categoria_id = categoria_padre de la subcategoría
$sql_migrate = "
    UPDATE productos p
    JOIN subcategorias sc ON p.subcategoria_id = sc.id
    SET p.categoria_id = sc.categoria_id
    WHERE p.subcategoria_id IS NOT NULL AND p.subcategoria_id > 0
";

if ($conn->query($sql_migrate)) {
    echo "✓ Productos migrados a categoría padre: " . $conn->affected_rows . " filas actualizadas\n";
} else {
    echo "✗ Error al migrar: " . $conn->error . "\n";
    exit(1);
}

// 3. Limpiar subcategoria_id
$sql_clear = "UPDATE productos SET subcategoria_id = NULL";
if ($conn->query($sql_clear)) {
    echo "✓ subcategoria_id limpiado en todos los productos\n";
} else {
    echo "✗ Error al limpiar subcategoria_id: " . $conn->error . "\n";
    exit(1);
}

// 4. Verificación final
$res2 = $conn->query("SELECT p.id, p.nombre, c.nombre as categoria FROM productos p JOIN categorias c ON p.categoria_id = c.id LIMIT 20");
echo "\nProductos (primeros 20):\n";
while ($row = $res2->fetch_assoc()) {
    echo "  [{$row['id']}] {$row['nombre']} → {$row['categoria']}\n";
}

echo "\n=== MIGRACIÓN COMPLETADA ===\n";
echo "\nIMPORTANTE: Elimina este archivo del servidor después de ejecutarlo.\n";
echo "</pre>\n";
?>
