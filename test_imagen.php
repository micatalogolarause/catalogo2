<?php
include 'config/database.php';
include 'config/config.php';

$conn = mysqli_connect('localhost', 'root', '', 'catalogo_tienda');
if (!$conn) die("Conexión fallida: " . mysqli_connect_error());

$result = mysqli_query($conn, 'SELECT id, nombre, imagen FROM productos WHERE tenant_id = 2 LIMIT 5');
echo "Imágenes en BD para tenant 2:\n";
echo "================================\n";
while($row = mysqli_fetch_assoc($result)) {
    echo "ID: {$row['id']}\n";
    echo "Nombre: {$row['nombre']}\n";
    echo "Imagen en BD: {$row['imagen']}\n";
    echo "---\n";
}

// Verificar si existe
echo "\n\nVerificación de archivos:\n";
echo "================================\n";
$path_old = '/public/images/productos/' . 'upl_69618a47111e3_test-1x1.png';
$path_tenant = '/public/tenants/2/images/' . 'upl_69618a47111e3_test-1x1.png';

echo "Ruta vieja: " . APP_ROOT . $path_old . "\n";
echo "¿Existe? " . (file_exists(APP_ROOT . $path_old) ? "SÍ" : "NO") . "\n";
echo "---\n";
echo "Ruta tenant: " . APP_ROOT . $path_tenant . "\n";
echo "¿Existe? " . (file_exists(APP_ROOT . $path_tenant) ? "SÍ" : "NO") . "\n";
?>
