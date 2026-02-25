<?php
/**
 * DIAGNÓSTICO DE TENANTS
 * Verifica qué tenants están en la BD y cómo se resuelven
 */

require_once 'config/database.php';
require_once 'config/TenantResolver.php';

// Permitir ejecución por CLI forzando tenant si no viene por URL
if (php_sapi_name() === 'cli' && !isset($_GET['tenant'])) {
    $_GET['tenant'] = 'mauricio';
}

// Resolver tenant actual
$resolved = TenantResolver::resolve();

// Mostrar información del tenant resuelto
echo "<h1>📊 DIAGNÓSTICO DE TENANTS</h1>";
echo "<hr>";

// Mostrar URL actual
echo "<h2>URL Actual</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'no set') . "\n";
echo "</pre>";

// Mostrar sesión actual
echo "<h2>Sesión Actual</h2>";
echo "<pre>";
echo "tenant_id: " . ($_SESSION['tenant_id'] ?? 'NO SET') . "\n";
echo "tenant_slug: " . ($_SESSION['tenant_slug'] ?? 'NO SET') . "\n";
echo "</pre>";

// Mostrar constantes definidas
echo "<h2>Constantes Definidas</h2>";
echo "<pre>";
echo "TENANT_ID: " . (defined('TENANT_ID') ? TENANT_ID : 'NO DEFINIDO') . "\n";
echo "TENANT_SLUG: " . (defined('TENANT_SLUG') ? TENANT_SLUG : 'NO DEFINIDO') . "\n";
echo "TENANT_NAME: " . (defined('TENANT_NAME') ? TENANT_NAME : 'NO DEFINIDO') . "\n";
echo "</pre>";

// Lista de todos los tenants en la BD
echo "<h2>Tenants en la Base de Datos</h2>";
$sql = "SELECT id, nombre, slug, estado FROM tenants ORDER BY id";
$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Slug</th><th>Estado</th></tr>";
    while ($row = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($row['slug']) . "</td>";
        echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ No se encontraron tenants en la BD</p>";
}

// Contar pedidos por tenant
echo "<h2>Pedidos por Tenant</h2>";
$sql = "SELECT t.id, t.slug, t.nombre, COUNT(p.id) as total_pedidos
        FROM tenants t
        LEFT JOIN pedidos p ON p.tenant_id = t.id
        GROUP BY t.id, t.slug, t.nombre
        ORDER BY t.id";
$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Slug</th><th>Nombre</th><th>Total Pedidos</th></tr>";
    while ($row = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['slug']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($row['total_pedidos']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ Error al contar pedidos</p>";
}

// Probar resolución de cada tenant
echo "<h2>Test: Resolver Cada Tenant</h2>";
$sql = "SELECT id, slug FROM tenants WHERE estado = 'activo'";
$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Slug</th><th>URL Esperada</th><th>Acción</th></tr>";
    while ($row = $resultado->fetch_assoc()) {
        $slug = htmlspecialchars($row['slug']);
        $url = "/catalogo2/{$slug}/index.php";
        echo "<tr>";
        echo "<td>$slug</td>";
        echo "<td><code>$url</code></td>";
        echo "<td><a href='$url' target='_blank'>📌 Abrir</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<p><a href='/catalogo2/index.php'>← Volver al Inicio</a></p>";
?>
