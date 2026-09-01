<?php
/**
 * Script para actualizar el slug del tenant de "la77" a "JYM"
 * EJECUTAR UNA SOLA VEZ. Borrar después de ejecutar.
 */
define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');
echo "<pre>\n=== ACTUALIZAR SLUG: la77 → JYM ===\n\n";

// 1. Verificar estado actual
$res = $conn->query("SELECT id, nombre, slug, estado FROM tenants WHERE LOWER(slug) = 'la77' OR LOWER(slug) = 'jym'");
echo "Tenants encontrados:\n";
while ($row = $res->fetch_assoc()) {
    echo "  ID={$row['id']} nombre={$row['nombre']} slug={$row['slug']} estado={$row['estado']}\n";
}
echo "\n";

// 2. Actualizar slug la77 → JYM
$stmt = $conn->prepare("UPDATE tenants SET slug = 'JYM' WHERE LOWER(slug) = 'la77'");
$stmt->execute();
if ($stmt->affected_rows > 0) {
    echo "✓ Slug actualizado: la77 → JYM ({$stmt->affected_rows} fila)\n";
} else {
    echo "⚠ No se encontró tenant con slug 'la77'. Verifica si ya existe como 'JYM'.\n";
}
$stmt->close();

// 3. Actualizar nombre del tenant si también es "la77" o "gramas"
// Descomenta la siguiente línea si también quieres cambiar el nombre:
// $conn->query("UPDATE tenants SET nombre = 'JYM' WHERE LOWER(slug) = 'jym'");

// 4. Verificación final
echo "\nEstado final:\n";
$res2 = $conn->query("SELECT id, nombre, slug, estado FROM tenants WHERE LOWER(slug) = 'jym'");
while ($row = $res2->fetch_assoc()) {
    echo "  ✓ ID={$row['id']} nombre={$row['nombre']} slug={$row['slug']} estado={$row['estado']}\n";
}

echo "\n=== LISTO ===\n";
echo "Ahora accede a:\n";
echo "  Local:  http://localhost/catalogo2/JYM/\n";
echo "  Vercel: https://<tu-proyecto>.vercel.app/JYM/\n\n";
echo "IMPORTANTE: Elimina este archivo del servidor después de ejecutarlo.\n";
echo "</pre>\n";
?>
