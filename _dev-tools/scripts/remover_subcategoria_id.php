<?php
/**
 * SCRIPT: Remover columna subcategoria_id de la tabla productos
 * Ejecutar: php remover_subcategoria_id.php
 */

require_once 'config/config.php';
require_once 'config/database.php';

echo "=== REMOVER COLUMNA SUBCATEGORIA_ID ===\n\n";

$queries = array(
    "ALTER TABLE productos DROP COLUMN subcategoria_id"
);

foreach ($queries as $sql) {
    echo "Ejecutando: $sql\n";
    try {
        $conn->exec($sql);
        echo "✓ Éxito\n\n";
    } catch (PDOException $e) {
        echo "✗ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "=== VERIFICAR ESTRUCTURA ===\n";
$res = $conn->query("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = 'productos' AND column_name = 'subcategoria_id' ORDER BY ordinal_position");
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
if (count($rows) === 0) {
    echo "✓ Columna subcategoria_id ha sido removida\n";
} else {
    echo "✗ La columna aún existe:\n";
    foreach ($rows as $row) {
        echo "   " . $row['column_name'] . " (" . $row['data_type'] . ") nullable=" . $row['is_nullable'] . "\n";
    }
}

echo "\n=== COMPLETADO ===\n";
?>
