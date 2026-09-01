<?php
/**
 * DIAGNÓSTICO: Problemas de caracteres especiales en distribuciones-ebs
 */

header('Content-Type: text/html; charset=UTF-8');

require_once 'config/config.php';
require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Diagnóstico - Caracteres Especiales</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-left: 4px solid #0066cc; }
        .error { color: #d32f2f; }
        .success { color: #388e3c; }
        pre { background: #fff; padding: 10px; overflow: auto; border: 1px solid #ccc; }
    </style>
</head>
<body>
<h1>Diagnóstico: Caracteres Especiales - distribuciones-ebs</h1>

<div class='section'>
<h2>1. Base de Datos</h2>";

// Verificar conexión y charset
echo "<p><strong>Connection Charset:</strong> " . $conn->character_set_name() . "</p>";

// Verificar charset de las tablas
$sql = "SELECT TABLE_NAME, TABLE_COLLATION 
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA = 'catalogo_tienda' 
        LIMIT 10";
$result = $conn->query($sql);

echo "<p><strong>Collations:</strong></p><pre>";
while ($row = $result->fetch_assoc()) {
    echo $row['TABLE_NAME'] . " → " . $row['TABLE_COLLATION'] . "\n";
}
echo "</pre>";

echo "</div>";

// Verificar tenant específico
echo "<div class='section'>
<h2>2. Datos del Tenant distribuciones-ebs</h2>";

$sql = "SELECT * FROM tenants WHERE slug = 'distribuciones-ebs'";
$result = $conn->query($sql);
$tenant = $result->fetch_assoc();

if ($tenant) {
    echo "<p><strong>ID:</strong> " . $tenant['id'] . "</p>";
    echo "<p><strong>Nombre:</strong> " . htmlspecialchars($tenant['nombre']) . "</p>";
    echo "<p><strong>Nombre (hex):</strong> " . bin2hex($tenant['nombre']) . "</p>";
    echo "<p><strong>Slug:</strong> " . htmlspecialchars($tenant['slug']) . "</p>";
    echo "<p><strong>Título:</strong> " . htmlspecialchars($tenant['titulo_empresa']) . "</p>";
}

echo "</div>";

// Verificar categorías
echo "<div class='section'>
<h2>3. Categorías del Tenant</h2>";

$sql = "SELECT id, nombre FROM categorias WHERE tenant_id = 3 LIMIT 5";
$result = $conn->query($sql);

echo "<pre>";
while ($row = $result->fetch_assoc()) {
    echo "- " . $row['nombre'] . "\n";
}
echo "</pre>";

echo "</div>";

// Verificar subcategorías con caracteres especiales
echo "<div class='section'>
<h2>4. Subcategorías (verificar caracteres)</h2>";

$sql = "SELECT id, nombre FROM subcategorias WHERE tenant_id = 3 LIMIT 5";
$result = $conn->query($sql);

echo "<pre>";
while ($row = $result->fetch_assoc()) {
    echo "- " . $row['nombre'] . "\n";
}
echo "</pre>";

echo "</div>";

// Verificar productos con caracteres especiales
echo "<div class='section'>
<h2>5. Productos (verificar nombres con caracteres)</h2>";

$sql = "SELECT id, nombre, descripcion FROM productos WHERE tenant_id = 3 LIMIT 10";
$result = $conn->query($sql);

echo "<pre>";
$i = 0;
while ($row = $result->fetch_assoc()) {
    $i++;
    echo "\n$i. Nombre: " . $row['nombre'];
    if (strlen($row['descripcion']) > 0) {
        echo " (Desc: " . substr($row['descripcion'], 0, 50) . "...)";
    }
    // Detectar caracteres no-ASCII
    if (preg_match('/[^\x00-\x7F]/', $row['nombre'])) {
        echo " [CONTIENE CARACTERES ESPECIALES]";
    }
    echo "\n";
}
echo "</pre>";

echo "</div>";

// Verificar usuarios
echo "<div class='section'>
<h2>6. Usuarios del Tenant</h2>";

$sql = "SELECT id, usuario, nombre FROM usuarios WHERE tenant_id = 3 LIMIT 5";
$result = $conn->query($sql);

echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Usuario</th><th>Nombre</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "</div>";

// PHP info
echo "<div class='section'>
<h2>7. Información del Sistema</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>System Locale:</strong> " . setlocale(LC_ALL, 0) . "</p>";
echo "<p><strong>mbstring.language:</strong> " . ini_get('mbstring.language') . "</p>";
echo "</div>";

echo "</body></html>";

$conn->close();
?>
