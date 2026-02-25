<?php
if ($argc < 2) {
    echo "Uso: php check_tenant.php <tenant_id>\n";
    exit(1);
}
$tenant_id = (int)$argv[1];
require_once __DIR__ . '/../config/database.php';
$stmt = $conn->prepare("SELECT * FROM tenants WHERE id = ?");
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
header('Content-Type: application/json');
echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
