<?php
require_once __DIR__ . '/../config/database.php';

$sql = "SELECT tenant_id, COUNT(*) as total, MIN(numero_cuenta_cobro) as min_cc, MAX(numero_cuenta_cobro) as max_cc, GROUP_CONCAT(numero_cuenta_cobro ORDER BY numero_cuenta_cobro ASC SEPARATOR ',') as nums FROM pedidos GROUP BY tenant_id ORDER BY tenant_id";
$res = $conn->query($sql);
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}
header('Content-Type: application/json');
echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
