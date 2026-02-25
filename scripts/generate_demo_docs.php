<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/PedidoModel.php';
require_once __DIR__ . '/../app/helpers/FacturaPDF.php';
require_once __DIR__ . '/../app/helpers/FacturaExcel.php';

if ($argc < 2) {
    echo "Uso: php generate_demo_docs.php <pedido_id>\n";
    exit(1);
}
$pedido_id = (int)$argv[1];

// Obtener pedido
$stmt = $conn->prepare("SELECT p.*, c.nombre, c.email, c.whatsapp, c.telefono, c.direccion, c.ciudad FROM pedidos p LEFT JOIN clientes c ON p.cliente_id = c.id AND p.tenant_id = c.tenant_id WHERE p.id = ?");
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$res = $stmt->get_result();
$pedido = $res->fetch_assoc();
if (!$pedido) {
    echo "Pedido no encontrado: $pedido_id\n";
    exit(1);
}

// Obtener tenant
$tenant_id = (int)$pedido['tenant_id'];
$stmt2 = $conn->prepare("SELECT * FROM tenants WHERE id = ?");
$stmt2->bind_param("i", $tenant_id);
$stmt2->execute();
$tenant = $stmt2->get_result()->fetch_assoc();
if (!$tenant) {
    echo "Tenant no encontrado: $tenant_id\n";
    exit(1);
}

// Obtener detalles
$stmt3 = $conn->prepare("SELECT pd.*, pr.nombre, pr.descripcion FROM pedido_detalles pd LEFT JOIN productos pr ON pd.producto_id = pr.id WHERE pd.pedido_id = ?");
$stmt3->bind_param("i", $pedido_id);
$stmt3->execute();
$detalles = array();
$resd = $stmt3->get_result();
while ($r = $resd->fetch_assoc()) $detalles[] = $r;

// Generar PDF
echo "Generando PDF...\n";
$pdfUrl = FacturaPDF::generar($pedido, $detalles, $tenant);
echo "PDF: $pdfUrl\n";

// Generar Excel
echo "Generando Excel...\n";
$excelUrl = FacturaExcel::generar($pedido, $detalles, $tenant);
echo "Excel: $excelUrl\n";

echo "Listo.\n";
