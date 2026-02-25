<?php
require_once __DIR__ . '/../config/database.php';
$sql = "SELECT t.id, t.slug, t.nombre, COUNT(p.id) as total_pedidos, MIN(p.numero_cuenta_cobro) as min_cc, MAX(p.numero_cuenta_cobro) as max_cc
        FROM tenants t
        LEFT JOIN pedidos p ON p.tenant_id = t.id
        GROUP BY t.id
        ORDER BY t.id";
$res = $conn->query($sql);
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}
header('Content-Type: application/json');
echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
