<?php
$conn = mysqli_connect('localhost', 'root', '', 'catalogo_tienda');
if (!$conn) die("Conexión fallida");

// Ver todos los pedidos y sus detalles
echo "PEDIDOS CON TENANT_ID = 2 (Mauricio):\n";
echo "====================================\n\n";

$result = mysqli_query($conn, "SELECT id FROM pedidos WHERE tenant_id = 2 LIMIT 5");
while($pedido = mysqli_fetch_assoc($result)) {
    $pid = $pedido['id'];
    echo "Pedido ID: $pid\n";
    
    $det_result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM pedido_detalles WHERE pedido_id = $pid");
    $det_count = mysqli_fetch_assoc($det_result);
    echo "Detalles: " . $det_count['cnt'] . "\n";
    
    // Ver al menos uno
    $det_result = mysqli_query($conn, "SELECT pd.*, pr.nombre, pr.imagen FROM pedido_detalles pd JOIN productos pr ON pd.producto_id = pr.id WHERE pd.pedido_id = $pid LIMIT 1");
    if($row = mysqli_fetch_assoc($det_result)) {
        echo "  → Producto: " . $row['nombre'] . "\n";
        echo "  → Imagen: '" . $row['imagen'] . "'\n";
    }
    echo "\n";
}
?>
