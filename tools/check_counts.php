<?php
require_once __DIR__ . '/../config/database.php';
$cats = obtenerFila('SELECT COUNT(*) AS total FROM categorias');
$subs = obtenerFila('SELECT COUNT(*) AS total FROM subcategorias');
$prods = obtenerFila('SELECT COUNT(*) AS total FROM productos');
echo "Categorias: " . ($cats ? $cats['total'] : 0) . "\n";
echo "Subcategorias: " . ($subs ? $subs['total'] : 0) . "\n";
echo "Productos: " . ($prods ? $prods['total'] : 0) . "\n";
