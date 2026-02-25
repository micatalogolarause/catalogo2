<?php
if ($argc < 2) {
    echo "Uso: php debug_pedidos_tenant.php <tenant_id>\n";
    exit(1);
}
$tenant_id = (int)$argv[1];
require_once __DIR__ . '/../config/database.php';

$sql = "SELECT id, tenant_id, numero_pedido, numero_cuenta_cobro, fecha_creacion FROM pedidos WHERE tenant_id = ? ORDER BY fecha_creacion ASC, id ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}
header('Content-Type: application/json');
echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
