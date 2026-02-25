<?php
/**
 * ARCHIVO PRINCIPAL - PUNTO DE ENTRADA DE LA APLICACIÓN
 * Tienda Virtual con PHP MVC + Multi-Tenancy
 */

// Incluir configuraciones
require_once 'config/config.php';

// Incluir conexión a BD (Supabase/PostgreSQL)
require_once 'config/database.php';

// Determinar la acción y controlador PRIMERO (antes de resolver tenant)
$action = isset($_GET['action']) ? trim($_GET['action']) : 'inicio';
$controlador = isset($_GET['controller']) ? trim($_GET['controller']) : 'tienda';

// Proteger contra inyección de código
$controlador = preg_replace('/[^a-zA-Z0-9_]/', '', $controlador);
$action = preg_replace('/[^a-zA-Z0-9_]/', '', $action);

// ========================================
// MULTI-TENANCY: Resolver tenant actual
// ========================================
// EXCEPCIÓN: No resolver tenant para super admin
if ($controlador !== 'superAdmin') {
    require_once 'config/TenantResolver.php';
    TenantResolver::resolve();
} else {
    // Para super admin, definir constantes dummy para evitar errores
    if (!defined('TENANT_ID')) {
        define('TENANT_ID', 0);
        define('TENANT_SLUG', '');
        define('TENANT_NAME', 'Super Admin');
        define('TENANT_WHATSAPP', '');
    }
}

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
