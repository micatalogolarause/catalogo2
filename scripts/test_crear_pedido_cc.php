<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/PedidoModel.php';

// Usage: php test_crear_pedido_cc.php [tenant_id]
$tenant_id = isset($argv[1]) ? (int)$argv[1] : 1;
if (!defined('TENANT_ID')) {
    define('TENANT_ID', $tenant_id);
}
$results = [];

// Use single tenant for this run
// Find or create a test client for this tenant
//
    // Find or create a test client for this tenant
    $stmt = ejecutarConsulta("SELECT id FROM clientes WHERE tenant_id = ? LIMIT 1", "i", array($tenant_id));
    $cliente = null;
    if ($stmt) {
        $res = $stmt->get_result();
        $cliente = $res->fetch_assoc();
    }
    if (!$cliente) {
        $insert = ejecutarConsulta("INSERT INTO clientes (tenant_id, nombre, email, telefono, whatsapp, ciudad, direccion) VALUES (?, 'Test Cliente', 'test+".$tenant_id."@example.com', '3000000000', '3000000000', 'Test', 'Test')", "i", array($tenant_id));
        $cliente_id = $conn->insert_id;
    } else {
        $cliente_id = $cliente['id'];
    }

    $pedidoModel = new PedidoModel($conn);
    $total = 12345.67;
    $_SESSION['tenant_id'] = TENANT_ID;
    $pedido_id = $pedidoModel->crear($cliente_id, $total, 'Prueba automática');

    if ($pedido_id) {
        $stmt2 = ejecutarConsulta("SELECT id, tenant_id, numero_pedido, numero_cuenta_cobro FROM pedidos WHERE id = ?", "i", array($pedido_id));
        $row = $stmt2->get_result()->fetch_assoc();
        $results[TENANT_ID] = $row;
    } else {
        $results[TENANT_ID] = ['error' => 'No se pudo crear pedido'];
    }

header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
