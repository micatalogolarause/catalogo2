<?php
/**
 * Agregar campos para múltiples imágenes en productos
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "=== AGREGANDO CAMPOS PARA MÚLTIPLES IMÁGENES ===\n";

global $conn;

// Verificar si las columnas ya existen
$check = $conn->query("SHOW COLUMNS FROM productos LIKE 'imagen2'");
if ($check->num_rows > 0) {
    echo "✓ Las columnas ya existen\n";
    exit;
}

// Agregar columnas imagen2 e imagen3
$sql1 = "ALTER TABLE productos ADD COLUMN imagen2 VARCHAR(255) NULL AFTER imagen";
$sql2 = "ALTER TABLE productos ADD COLUMN imagen3 VARCHAR(255) NULL AFTER imagen2";

if ($conn->query($sql1)) {
    echo "✓ Columna imagen2 agregada\n";
} else {
    echo "✗ Error al agregar imagen2: " . $conn->error . "\n";
}

if ($conn->query($sql2)) {
    echo "✓ Columna imagen3 agregada\n";
} else {
    echo "✗ Error al agregar imagen3: " . $conn->error . "\n";
}

echo "\nMigración completada.\n";
