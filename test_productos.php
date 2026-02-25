<?php
$conn = mysqli_connect('localhost', 'root', '', 'catalogo_tienda');

// Ver productos de tenant 2 y sus imágenes
$result = mysqli_query($conn, "SELECT id, nombre, imagen FROM productos WHERE tenant_id = 2 ORDER BY id LIMIT 20");

echo "PRODUCTOS TENANT 2:\n";
echo "==================\n\n";

while($row = mysqli_fetch_assoc($result)) {
    $img = !empty($row['imagen']) ? "'" . $row['imagen'] . "'" : "VACÍO";
    echo "ID {$row['id']}: {$row['nombre']} → Imagen: {$img}\n";
}
?>
