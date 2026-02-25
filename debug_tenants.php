<?php
$conn = new mysqli('localhost', 'root', '', 'catalogo_tienda');
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}

echo "=== TENANTS EN BD ===\n";
$sql = "SELECT id, nombre, slug, titulo_empresa, estado FROM tenants ORDER BY id";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "\nID: " . $row['id'];
    echo "\nNombre: " . $row['nombre'];
    echo "\nSlug: " . $row['slug'];
    echo "\nTítulo: " . $row['titulo_empresa'];
    echo "\nEstado: " . $row['estado'];
    echo "\n---";
}

echo "\n\n=== PRODUCTOS POR TENANT ===\n";
$sql = "SELECT t.id, t.nombre, COUNT(p.id) as total_productos
        FROM tenants t
        LEFT JOIN productos p ON t.id = p.tenant_id
        GROUP BY t.id
        ORDER BY t.id";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "\n" . $row['nombre'] . " (ID:" . $row['id'] . "): " . $row['total_productos'] . " productos";
}

echo "\n\n=== VERIFICAR CARACTERES EN DISTRIBUCIONES-EBS ===\n";
$sql = "SELECT id, nombre FROM productos WHERE tenant_id = 3 LIMIT 5";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "\n- " . $row['nombre'];
}

$conn->close();
?>
