<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
$cols = obtenerFilas('SHOW COLUMNS FROM productos');
foreach ($cols as $c) echo $c['Field'] . ' | ' . $c['Type'] . "\n";
