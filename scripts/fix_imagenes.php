<?php
/**
 * Script para arreglar imágenes de productos generados por seed
 */
require_once __DIR__ . '/../config/database.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    die("Error conexión: " . $db->connect_error);
}

$db->set_charset('utf8mb4');

echo "<h2>Arreglando imágenes de productos</h2>";
echo "<hr>";

// Actualizar productos con imágenes inventadas a NULL
$sql = "UPDATE productos SET imagen = NULL WHERE imagen LIKE 'producto_%'";
if ($db->query($sql)) {
    echo "✓ Productos actualizados: " . $db->affected_rows . "<br>";
} else {
    echo "✗ Error: " . $db->error . "<br>";
}

// Contar productos sin imagen por tenant
$sql = "SELECT t.nombre, COUNT(p.id) as sin_imagen 
        FROM tenants t 
        LEFT JOIN productos p ON t.id = p.tenant_id AND p.imagen IS NULL 
        GROUP BY t.id 
        ORDER BY t.id";
$result = $db->query($sql);

echo "<h3>Productos sin imagen por tenant:</h3>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li><strong>" . $row['nombre'] . ":</strong> " . $row['sin_imagen'] . " productos</li>";
}
echo "</ul>";

echo "<p><strong>Nota:</strong> Los productos sin imagen usarán la imagen por defecto del sistema.</p>";
echo "<p><a href='" . APP_URL . "'>Volver al inicio</a></p>";

$db->close();
?>
