<?php
/**
 * Script de debug para verificar estado del tenant en tiempo real
 */
session_start();
require_once 'config/database.php';

$tenant_slug = $_SESSION['tenant_slug'] ?? 'no hay sesión';

echo "<h2>Debug Tenant Estado</h2>";
echo "<p><strong>Slug en sesión:</strong> " . htmlspecialchars($tenant_slug) . "</p>";

if ($tenant_slug !== 'no hay sesión') {
    // Consultar BD
    $sql = "SELECT id, nombre, slug, estado FROM tenants WHERE slug = '$tenant_slug'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $tenant = $result->fetch_assoc();
        echo "<h3>Datos en BD:</h3>";
        echo "<pre>";
        print_r($tenant);
        echo "</pre>";
        
        if ($tenant['estado'] !== 'activo') {
            echo "<p style='color: red; font-weight: bold;'>❌ Este tenant está " . $tenant['estado'] . "</p>";
            echo "<p>Debería mostrarse mensaje de 'Tienda No Disponible'</p>";
        } else {
            echo "<p style='color: green; font-weight: bold;'>✅ Tenant activo</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Tenant no encontrado en BD</p>";
    }
}

echo "<hr>";
echo "<p><a href='limpiar-sesion.php'>Limpiar Sesión</a></p>";
echo "<p><a href='tech-store'>Ir a tech-store</a></p>";
?>
