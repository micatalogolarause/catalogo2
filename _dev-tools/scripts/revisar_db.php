<?php
require 'config/database.php';

echo "=== REVISAR ESTRUCTURA Y DATOS ===\n\n";

// 1. Estructura de tabla
echo "1. Estructura de tabla 'productos':\n";
$res = $conn->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'productos' ORDER BY ordinal_position");
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    echo "   " . $row['column_name'] . " -> " . $row['data_type'] . "\n";
}

// 2. Tenants disponibles
echo "\n2. Tenants disponibles:\n";
$res = $conn->query('SELECT id, slug, nombre, estado FROM tenants');
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    echo "   ID={$row['id']} slug={$row['slug']} nombre={$row['nombre']} estado={$row['estado']}\n";
}

// 3. Categorías disponibles
echo "\n3. Categorías:\n";
$res = $conn->query('SELECT id, tenant_id, nombre FROM categorias LIMIT 10');
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    echo "   ID={$row['id']} tenant_id={$row['tenant_id']} nombre={$row['nombre']}\n";
}

// 4. Productos creados (últimos 5)
echo "\n4. Últimos 5 productos:\n";
$res = $conn->query('SELECT id, tenant_id, categoria_id, nombre, precio, stock FROM productos ORDER BY id DESC LIMIT 5');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
if (count($rows) === 0) {
    echo "   (Sin productos)\n";
} else {
    foreach ($rows as $row) {
        echo "   ID={$row['id']} tenant_id={$row['tenant_id']} cat_id={$row['categoria_id']} nombre={$row['nombre']} precio={$row['precio']} stock={$row['stock']}\n";
    }
}

echo "\n=== FIN ===\n";
?>
