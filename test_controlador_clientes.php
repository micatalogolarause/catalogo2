<?php
include 'config/database.php';
include 'config/config.php';
include 'app/helpers/Helper.php';

// Simular session de mauricio tenant
$_SESSION['tenant_id'] = 1;
$_SESSION['tenant_slug'] = 'mauricio';

// Código exacto del controlador clientes()
$busqueda = '';

$sql = "SELECT * FROM clientes WHERE tenant_id = ? ORDER BY fecha_registro DESC";
$clientes = obtenerFilasScoped($sql);

echo "=== CLIENTES OBTENIDOS DEL CONTROLADOR ===\n";
echo "Total: " . count($clientes) . "\n\n";

foreach (array_slice($clientes, 0, 5) as $cliente) {
    echo "ID=" . $cliente['id'] . 
         " | nombre=" . $cliente['nombre'] . 
         " | calificacion_value=" . $cliente['calificacion'] .
         " | isset_check=" . (isset($cliente['calificacion']) ? 'true' : 'false') . "\n";
    
    // Simular el código de la vista
    $rating = isset($cliente['calificacion']) ? (int)$cliente['calificacion'] : 0;
    echo "  → rating=$rating | HTML: ";
    if ($rating > 0) {
        echo "⭐⭐⭐⭐⭐ (solo muestra si rating > 0)\n";
    } else {
        echo "Sin rating\n";
    }
}
?>
