<?php
$conn = new mysqli('localhost', 'root', '', 'catalogo_tienda');
$result = $conn->query('SELECT id, tenant_id, cliente_id, total, estado FROM pedidos WHERE id = 63');
if ($row = $result->fetch_assoc()) {
    echo "Pedido 63 encontrado:\n";
    echo "Tenant ID: " . $row['tenant_id'] . "\n";
    echo "Total: " . $row['total'] . "\n";
    echo "Estado: " . $row['estado'] . "\n";
} else {
    echo "Pedido 63 NO EXISTE\n";
}

echo "\nPrimer pedido en cada tenant:\n";
foreach ([1, 2, 3, 4] as $tid) {
    $result = $conn->query("SELECT id FROM pedidos WHERE tenant_id = $tid LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        echo "Tenant $tid: Pedido " . $row['id'] . "\n";
    } else {
        echo "Tenant $tid: SIN PEDIDOS\n";
    }
}
?>
