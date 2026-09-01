<?php
include 'config/database.php';

// Verificar qué datos tiene el cliente "Mauricio" en mauricio tenant
$sql = "SELECT id, tenant_id, nombre, calificacion FROM clientes WHERE nombre = 'Mauricio' ORDER BY id";
$result = $GLOBALS['conn']->query($sql);

echo "=== CLIENTES LLAMADOS MAURICIO EN BD ===\n";
while ($row = $result->fetch_assoc()) {
    echo "ID=" . $row['id'] . " | tenant_id=" . $row['tenant_id'] . " | nombre=" . $row['nombre'] . " | calificacion=" . $row['calificacion'] . "\n";
}

echo "\n=== TODOS LOS CLIENTES CON TENANT_ID=1 ===\n";
$sql = "SELECT id, tenant_id, nombre, calificacion FROM clientes WHERE tenant_id = 1";
$result = $GLOBALS['conn']->query($sql);
while ($row = $result->fetch_assoc()) {
    echo "ID=" . $row['id'] . " | tenant_id=" . $row['tenant_id'] . " | nombre=" . $row['nombre'] . " | calificacion=" . $row['calificacion'] . "\n";
}
?>
