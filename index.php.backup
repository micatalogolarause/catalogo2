<?php
/**
 * ARCHIVO PRINCIPAL - PUNTO DE ENTRADA DE LA APLICACIÓN
 * Tienda Virtual con PHP MVC + Multi-Tenancy
 */

// Incluir configuraciones
require_once 'config/config.php';

// Ejecutar instalación automática si no existe la BD
$conn_test = @new mysqli('localhost', 'root', '');
if ($conn_test->connect_error) {
    die("Error: No se pudo conectar a MySQL. Verifique que XAMPP/MySQL esté en ejecución.");
}

// Verificar si la BD existe
$resultado = $conn_test->query("SHOW DATABASES LIKE 'catalogo_tienda'");
if (!$resultado || $resultado->num_rows == 0) {
    // Ejecutar instalador
    require_once 'config/installer.php';
    require_once 'config/generate_images.php';
}
$conn_test->close();

// Incluir conexión a BD
require_once 'config/database.php';

// ========================================
// MULTI-TENANCY: Resolver tenant actual
// ========================================
require_once 'config/TenantResolver.php';

// Detectar y validar tenant desde URL
TenantResolver::resolve();

// A partir de aquí, TENANT_ID está disponible globalmente
// Todos los controllers deben usar esta constante en sus queries

// Determinar la acción y controlador
$action = isset($_GET['action']) ? trim($_GET['action']) : 'inicio';
$controlador = isset($_GET['controller']) ? trim($_GET['controller']) : 'tienda';

// Proteger contra inyección de código
$controlador = preg_replace('/[^a-zA-Z0-9_]/', '', $controlador);
$action = preg_replace('/[^a-zA-Z0-9_]/', '', $action);

// Ruta del controlador
$rutaControlador = APP_ROOT . '/app/controllers/' . $controlador . 'Controller.php';

// Verificar si existe el controlador
if (!file_exists($rutaControlador)) {
    http_response_code(404);
    include APP_ROOT . '/app/views/404.php';
    exit;
}

// Incluir el controlador
require_once $rutaControlador;

// Obtener el nombre de la clase del controlador
$nombreClase = ucfirst($controlador) . 'Controller';

// Verificar si la clase existe
if (!class_exists($nombreClase)) {
    http_response_code(500);
    include APP_ROOT . '/app/views/500.php';
    exit;
}

// Instanciar el controlador
$controller = new $nombreClase();

// Verificar si el método existe
if (!method_exists($controller, $action)) {
    http_response_code(404);
    include APP_ROOT . '/app/views/404.php';
    exit;
}

// Ejecutar la acción del controlador
$controller->$action();
?>
