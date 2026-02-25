<?php
// Migración: agregar columna calificacion a clientes
$mysqli = new mysqli('localhost', 'root', '', 'catalogo_tienda');
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

$res = $mysqli->query("SHOW COLUMNS FROM clientes LIKE 'calificacion'");
if (!$res) {
    die("Error consultando columnas: " . $mysqli->error);
}

if ($res->num_rows === 0) {
    echo "Agregando columna calificacion...\n";
    if ($mysqli->query("ALTER TABLE clientes ADD COLUMN calificacion INT NOT NULL DEFAULT 0 AFTER activo")) {
        echo "✓ Columna calificacion agregada.\n";
    } else {
        echo "Error alterando tabla: " . $mysqli->error . "\n";
    }
} else {
    echo "✓ La columna calificacion ya existe.\n";
}

echo "Listo.\n";
?>
