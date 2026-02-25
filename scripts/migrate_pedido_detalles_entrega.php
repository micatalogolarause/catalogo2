<?php
// Simple migration: add cantidad_entregada to pedido_detalles if missing
$mysqli = new mysqli('localhost','root','', 'catalogo_tienda');
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

$res = $mysqli->query("SHOW COLUMNS FROM pedido_detalles LIKE 'cantidad_entregada'");
if (!$res) {
    die("Error consultando columnas: " . $mysqli->error);
}
if ($res->num_rows === 0) {
    echo "Agregando columna cantidad_entregada...\n";
    if ($mysqli->query("ALTER TABLE pedido_detalles ADD COLUMN cantidad_entregada INT NOT NULL DEFAULT 0 AFTER subtotal")) {
        echo "✓ Columna agregada.\n";
        // Inicializar cantidades entregadas acorde a estado_preparacion
        $mysqli->query("UPDATE pedido_detalles SET cantidad_entregada = cantidad WHERE estado_preparacion = 'listo'");
        echo "✓ Valores iniciales establecidos.\n";
    } else {
        echo "Error alterando tabla: " . $mysqli->error . "\n";
    }
} else {
    echo "✓ La columna cantidad_entregada ya existe.\n";
}

echo "Listo.\n";
