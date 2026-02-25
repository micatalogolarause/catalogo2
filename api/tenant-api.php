<?php
/**
 * API - Tenant Management
 * Endpoints para crear y gestionar tenants
 */

header('Content-Type: application/json');
session_start();
require_once '../config/database.php';
require_once '../app/controllers/tenantAdminController.php';

$controller = new TenantAdminController();
$response = ['success' => false, 'message' => 'Sin acción especificada'];

// Obtener acción
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response = ['success' => false, 'message' => 'Método POST requerido'];
            break;
        }
        
        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'slug' => $_POST['slug'] ?? '',
            'whatsapp_phone' => $_POST['whatsapp_phone'] ?? '',
            'logo' => $_POST['logo'] ?? NULL,
            'tema' => $_POST['tema'] ?? 'claro',
            'estado' => $_POST['estado'] ?? 'activo',
            'admin_usuario' => $_POST['admin_usuario'] ?? '',
            'admin_email' => $_POST['admin_email'] ?? '',
            'admin_password' => $_POST['admin_password'] ?? 'admin123',
        ];
        
        $response = $controller->crearTenant($data);
        break;
    
    case 'listar':
        $filtro = [];
        if (!empty($_GET['estado'])) {
            $filtro['estado'] = $_GET['estado'];
        }
        
        $tenants = $controller->obtenerTenants($filtro);
        $response = [
            'success' => true,
            'data' => $tenants,
            'count' => count($tenants)
        ];
        break;
    
    case 'obtener':
        if (empty($_GET['id'])) {
            $response = ['success' => false, 'message' => 'ID de tenant requerido'];
            break;
        }
        
        $tenant = $controller->obtenerTenant($_GET['id']);
        if (!$tenant) {
            $response = ['success' => false, 'message' => 'Tenant no encontrado'];
        } else {
            $response = ['success' => true, 'data' => $tenant];
        }
        break;
    
    case 'estadisticas':
        if (empty($_GET['id'])) {
            $response = ['success' => false, 'message' => 'ID de tenant requerido'];
            break;
        }
        
        $stats = $controller->obtenerEstadisticas($_GET['id']);
        $response = ['success' => true, 'data' => $stats];
        break;
    
    case 'actualizar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response = ['success' => false, 'message' => 'Método POST requerido'];
            break;
        }
        
        if (empty($_POST['id'])) {
            $response = ['success' => false, 'message' => 'ID de tenant requerido'];
            break;
        }
        
        $data = [];
        if (!empty($_POST['nombre'])) $data['nombre'] = $_POST['nombre'];
        if (!empty($_POST['whatsapp_phone'])) $data['whatsapp_phone'] = $_POST['whatsapp_phone'];
        if (!empty($_POST['tema'])) $data['tema'] = $_POST['tema'];
        if (!empty($_POST['estado'])) $data['estado'] = $_POST['estado'];
        
        $response = $controller->actualizarTenant($_POST['id'], $data);
        break;
    
    default:
        $response = ['success' => false, 'message' => 'Acción no válida'];
}

echo json_encode($response);
?>
