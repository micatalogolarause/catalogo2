<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/helpers/ProductosPDF.php';
require_once __DIR__ . '/../app/helpers/ProductosExcel.php';
require_once __DIR__ . '/../config/database.php';
$tenantId = isset($argv[1]) ? (int)$argv[1] : 1;

$productos = obtenerFilas("SELECT * FROM productos WHERE tenant_id = ? LIMIT 10", "i", [$tenantId]);
$tenant = obtenerFila("SELECT * FROM tenants WHERE id = ? LIMIT 1", "i", [$tenantId]);

if (!$tenant) {
    echo "Tenant $tenantId not found\n";
    exit(1);
}

$pdfUrl = ProductosPDF::generar($productos, $tenant, 'todos');
$xlsUrl = ProductosExcel::generar($productos, $tenant, 'todos');

echo "PDF: " . $pdfUrl . "\n";
echo "XLSX: " . $xlsUrl . "\n";
