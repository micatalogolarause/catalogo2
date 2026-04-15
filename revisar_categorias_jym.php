<?php
require_once 'config/config.php';
require_once 'config/database.php';

$sql = "SELECT c.id, c.nombre, COUNT(p.id) AS total
        FROM categorias c
        LEFT JOIN productos p ON p.categoria_id = c.id AND p.tenant_id = c.tenant_id
        WHERE c.tenant_id = ?
        GROUP BY c.id, c.nombre
        ORDER BY c.id";

$stmt = $conn->prepare($sql);
$stmt->execute([6]);
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo $r['id'] . ' | ' . $r['nombre'] . ' | productos: ' . $r['total'] . PHP_EOL;
}
