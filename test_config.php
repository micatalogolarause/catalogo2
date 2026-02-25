<?php
/**
 * Script de prueba para verificar la configuración
 * Acceder en: http://localhost/catalogo2/test_config.php
 */

require_once 'config/config.php';

echo "<h2>🔧 Verificación de Configuración</h2>";
echo "<hr>";

echo "<h3>Configuración Actual:</h3>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>Variable</th><th>Valor</th></tr>";
echo "<tr><td><strong>APP_ROOT</strong></td><td>" . APP_ROOT . "</td></tr>";
echo "<tr><td><strong>APP_URL</strong></td><td>" . APP_URL . "</td></tr>";
echo "<tr><td><strong>APP_UPLOADS</strong></td><td>" . APP_UPLOADS . "</td></tr>";
echo "</table>";

echo "<h3>Variables del Servidor:</h3>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>Variable</th><th>Valor</th></tr>";
echo "<tr><td><strong>HTTP_HOST</strong></td><td>" . $_SERVER['HTTP_HOST'] . "</td></tr>";
echo "<tr><td><strong>SERVER_NAME</strong></td><td>" . $_SERVER['SERVER_NAME'] . "</td></tr>";
echo "<tr><td><strong>SERVER_PORT</strong></td><td>" . $_SERVER['SERVER_PORT'] . "</td></tr>";
echo "<tr><td><strong>SCRIPT_NAME</strong></td><td>" . $_SERVER['SCRIPT_NAME'] . "</td></tr>";
echo "<tr><td><strong>REQUEST_URI</strong></td><td>" . $_SERVER['REQUEST_URI'] . "</td></tr>";
echo "<tr><td><strong>DOCUMENT_ROOT</strong></td><td>" . $_SERVER['DOCUMENT_ROOT'] . "</td></tr>";
echo "<tr><td><strong>HTTPS</strong></td><td>" . (isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'no definido') . "</td></tr>";
echo "</table>";

echo "<h3>URLs Generadas:</h3>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>Ruta</th><th>URL Completa</th></tr>";
echo "<tr><td>Inicio</td><td><a href='" . APP_URL . "'>" . APP_URL . "</a></td></tr>";
echo "<tr><td>Admin Login</td><td><a href='" . APP_URL . "/index.php?controller=admin&action=login'>" . APP_URL . "/index.php?controller=admin&action=login</a></td></tr>";
echo "<tr><td>Usuario Registro</td><td><a href='" . APP_URL . "/index.php?controller=usuario&action=registro'>" . APP_URL . "/index.php?controller=usuario&action=registro</a></td></tr>";
echo "<tr><td>Carrito</td><td><a href='" . APP_URL . "/index.php?controller=tienda&action=carrito'>" . APP_URL . "/index.php?controller=tienda&action=carrito</a></td></tr>";
echo "</table>";

echo "<h3>Base de Datos:</h3>";
require_once 'config/database.php';
if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>✅ Conexión exitosa a MySQL</p>";
    
    // Verificar BD
    $result = $conn->query("SELECT DATABASE() as db");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Base de datos activa: <strong>" . $row['db'] . "</strong></p>";
    }
    
    // Verificar tablas
    $tables = ['categorias', 'subcategorias', 'productos', 'usuarios', 'clientes'];
    echo "<p>Tablas existentes:</p><ul>";
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<li style='color: green;'>✅ $table</li>";
        } else {
            echo "<li style='color: red;'>❌ $table (no existe)</li>";
        }
    }
    echo "</ul>";
}

echo "<h3>Archivos Críticos:</h3>";
$archivos = [
    '.htaccess' => APP_ROOT . '/.htaccess',
    'web.config' => APP_ROOT . '/web.config',
    'index.php' => APP_ROOT . '/index.php',
    'config.php' => APP_ROOT . '/config/config.php',
    'database.php' => APP_ROOT . '/config/database.php',
];

echo "<ul>";
foreach ($archivos as $nombre => $ruta) {
    if (file_exists($ruta)) {
        echo "<li style='color: green;'>✅ $nombre</li>";
    } else {
        echo "<li style='color: orange;'>⚠️ $nombre (no encontrado)</li>";
    }
}
echo "</ul>";

echo "<h3>Permisos de Escritura:</h3>";
$carpetas = [
    'public/images' => APP_ROOT . '/public/images',
];

echo "<ul>";
foreach ($carpetas as $nombre => $ruta) {
    if (is_writable($ruta)) {
        echo "<li style='color: green;'>✅ $nombre (escribible)</li>";
    } else {
        echo "<li style='color: red;'>❌ $nombre (sin permisos de escritura)</li>";
    }
}
echo "</ul>";

echo "<hr>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>PHP:</strong> " . PHP_VERSION . "</p>";
echo "<p><em>Nota: Este archivo es solo para pruebas. Elimínalo en producción.</em></p>";
?>
