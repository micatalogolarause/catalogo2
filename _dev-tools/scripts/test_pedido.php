<?php
include 'config/database.php';
include 'config/config.php';

$conn = mysqli_connect('localhost', 'root', '', 'catalogo_tienda');
if (!$conn) die("Conexión fallida");

// Buscar un pedido de Mauricio (tenant 2)
$result = mysqli_query($conn, "SELECT id FROM pedidos WHERE tenant_id = 2 LIMIT 1");
$pedido = mysqli_fetch_assoc($result);

if (!$pedido) {
    echo "No hay pedidos para tenant 2\n";
    exit;
}

$pedido_id = $pedido['id'];
echo "Pedido ID: $pedido_id\n";
echo "============================\n\n";

// Ver los detalles del pedido
$sql = "SELECT pd.*, pr.nombre, pr.imagen, pr.tenant_id as prod_tenant 
        FROM pedido_detalles pd 
        JOIN productos pr ON pd.producto_id = pr.id 
        WHERE pd.pedido_id = $pedido_id";

$result = mysqli_query($conn, $sql);

echo "Detalles del pedido:\n";
while($row = mysqli_fetch_assoc($result)) {
    echo "Producto: " . $row['nombre'] . "\n";
    echo "Imagen en detalles: '" . $row['imagen'] . "'\n";
    echo "Tenant del producto: " . $row['prod_tenant'] . "\n";
    echo "---\n";
}
?>
