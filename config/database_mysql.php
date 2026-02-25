<?php
/**
 * Configuración de Conexión a la Base de Datos
 * Compatible con XAMPP, Windows Server e IIS
 */

// Datos de conexión
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'catalogo_tienda');
define('DB_CHARSET', 'utf8mb4');

// Crear conexión con MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Configurar charset
$conn->set_charset(DB_CHARSET);

// Verificar conexión
if ($conn->connect_error) {
    // Log de error
    error_log("Error de conexión BD: " . $conn->connect_error);
    die("Error en la conexión a la base de datos. Por favor intente más tarde.");
}

// Configurar zona horaria
date_default_timezone_set('America/Bogota');

/**
 * Función helper para ejecutar consultas seguradas contra SQL Injection
 */
function ejecutarConsulta($sql, $tipos = "", $params = array()) {
    global $conn;
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Error en prepare: " . $conn->error);
        return false;
    }
    
    if ($tipos && !empty($params)) {
        $stmt->bind_param($tipos, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Error en execute: " . $stmt->error);
        return false;
    }
    
    return $stmt;
}

/**
 * Función para obtener una fila
 */
function obtenerFila($sql, $tipos = "", $params = array()) {
    $stmt = ejecutarConsulta($sql, $tipos, $params);
    if (!$stmt) return null;
    
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $stmt->close();
    return $fila;
}

/**
 * Función para obtener múltiples filas
 */
function obtenerFilas($sql, $tipos = "", $params = array()) {
    $stmt = ejecutarConsulta($sql, $tipos, $params);
    if (!$stmt) return array();
    
    $resultado = $stmt->get_result();
    $filas = array();
    while ($fila = $resultado->fetch_assoc()) {
        $filas[] = $fila;
    }
    $stmt->close();
    return $filas;
}

// ========================================
// FUNCIONES MULTI-TENANCY SCOPED
// ========================================

/**
 * Obtener tenant_id actual desde la constante global
 * 
 * @return int Tenant ID
 */
function getTenantId() {
    if (defined('TENANT_ID')) {
        $tid = (int)TENANT_ID;
        // Nunca permitir tenant_id 0 (sistema/superadmin) para queries normales
        // Si TENANT_ID es 0, significa que no hay tenant resuelto (error)
        if ($tid === 0) {
            error_log("WARNING: getTenantId() retorna 0 - No se resolvió tenant correctamente");
            // No retornar fallback inseguro; mejor fallar temprano
            return 0;
        }
        return $tid;
    }
    // Si TENANT_ID no está definido, hay un error de configuración
    error_log("CRITICAL: TENANT_ID no está definido");
    return 0;
}

/**
 * Ejecutar consulta scoped por tenant_id automáticamente
 * IMPORTANTE: La query debe tener un placeholder ? donde irá el tenant_id
 * 
 * Ejemplo:
 *   ejecutarConsultaScoped(
 *     "SELECT * FROM productos WHERE tenant_id = ? AND activo = ?",
 *     "ii",
 *     [1]
 *   )
 * 
 * @param string $sql Query SQL con placeholder para tenant_id
 * @param string $tipos Tipos de parámetros (sin contar tenant_id que se agrega automáticamente)
 * @param array $params Parámetros (sin contar tenant_id que se agrega automáticamente)
 * @return mixed Statement o false
 */
function ejecutarConsultaScoped($sql, $tipos = "", $params = array()) {
    $tenant_id = getTenantId();
    
    // Agregar tenant_id como primer parámetro
    $tipos = "i" . $tipos; // 'i' para integer (tenant_id)
    array_unshift($params, $tenant_id);
    
    return ejecutarConsulta($sql, $tipos, $params);
}

/**
 * Obtener una fila scoped por tenant
 * 
 * @param string $sql Query SQL con placeholder para tenant_id
 * @param string $tipos Tipos de parámetros adicionales
 * @param array $params Parámetros adicionales
 * @return array|null Fila o null
 */
function obtenerFilaScoped($sql, $tipos = "", $params = array()) {
    $tenant_id = getTenantId();
    
    // Validación de seguridad: no permitir tenant_id 0
    if ($tenant_id <= 0) {
        error_log("SECURITY: obtenerFilaScoped() rechaza tenant_id={$tenant_id}");
        return null; // Retornar null, no permitir consulta
    }
    
    $tipos = "i" . $tipos;
    array_unshift($params, $tenant_id);
    
    return obtenerFila($sql, $tipos, $params);
}

/**
 * Obtener múltiples filas scoped por tenant
 * 
 * @param string $sql Query SQL con placeholder para tenant_id
 * @param string $tipos Tipos de parámetros adicionales
 * @param array $params Parámetros adicionales
 * @return array Array de filas
 */
function obtenerFilasScoped($sql, $tipos = "", $params = array()) {
    $tenant_id = getTenantId();
    
    // Validación de seguridad: no permitir tenant_id 0
    if ($tenant_id <= 0) {
        error_log("SECURITY: obtenerFilasScoped() rechaza tenant_id={$tenant_id}");
        return array(); // Retornar array vacío, no permitir consulta
    }
    
    $tipos = "i" . $tipos;
    array_unshift($params, $tenant_id);
    
    return obtenerFilas($sql, $tipos, $params);
}

/**
 * Helper: Agregar WHERE tenant_id = ? a una query existente
 * 
 * Ejemplo:
 *   $sql = "SELECT * FROM productos WHERE activo = 1";
 *   $sql = agregarFiltroTenant($sql); // SELECT * FROM productos WHERE tenant_id = ? AND activo = 1
 * 
 * @param string $sql Query original
 * @return string Query con filtro de tenant
 */
function agregarFiltroTenant($sql) {
    // Detectar si ya tiene WHERE
    if (stripos($sql, 'WHERE') !== false) {
        // Ya tiene WHERE, agregar AND
        $sql = preg_replace('/WHERE/i', 'WHERE tenant_id = ? AND', $sql, 1);
    } else {
        // No tiene WHERE, agregarlo antes de GROUP BY, ORDER BY, LIMIT
        $sql = preg_replace(
            '/(GROUP BY|ORDER BY|LIMIT|$)/i',
            'WHERE tenant_id = ? $1',
            $sql,
            1
        );
    }
    return $sql;
}

/**
 * Helper: Validar que un registro pertenece al tenant actual
 * 
 * @param string $tabla Nombre de la tabla
 * @param int $id ID del registro
 * @return bool True si pertenece al tenant
 */
function validarPerteneceATenant($tabla, $id) {
    global $conn;
    
    $tenant_id = getTenantId();
    $tabla = $conn->real_escape_string($tabla);
    $id = (int)$id;
    
    $sql = "SELECT COUNT(*) as total FROM `$tabla` WHERE id = ? AND tenant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $tenant_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado) {
        $fila = $resultado->fetch_assoc();
        return $fila['total'] > 0;
    }
    
    return false;
}
?>
