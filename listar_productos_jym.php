<?php
require_once 'config/config.php';
require_once 'config/database.php';

$stmt = $conn->prepare('SELECT id, categoria_id, nombre FROM productos WHERE tenant_id = ? ORDER BY id DESC LIMIT 120');
$stmt->execute([6]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $r) {
    echo $r['id'] . '|' . $r['categoria_id'] . '|' . $r['nombre'] . PHP_EOL;
}
