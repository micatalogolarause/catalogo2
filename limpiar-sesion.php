<?php
/**
 * Script para limpiar sesiones de tenants inactivos
 */
session_start();

echo "<h2>Limpieza de Sesión</h2>";
echo "<p>Sesión anterior:</p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Limpiar sesión de tenant
unset($_SESSION['tenant_id']);
unset($_SESSION['tenant_slug']);
unset($_SESSION['tenant_data']);

// Destruir toda la sesión
session_destroy();

echo "<p style='color: green; font-weight: bold;'>✅ Sesión limpiada exitosamente</p>";
echo "<p><a href='http://localhost/catalogo2/tech-store'>Intentar acceder a tech-store nuevamente</a></p>";
echo "<p><a href='http://localhost/catalogo2/'>Ir al inicio</a></p>";
?>
