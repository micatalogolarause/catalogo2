<?php
/**
 * Configuración de Conexión a Supabase (PostgreSQL)
 * 
 * INSTRUCCIONES:
 * 1. Ve a tu proyecto en supabase.com
 * 2. En Settings → Database → Connection string → PHP (PDO)
 * 3. Copia los datos y pégalos aquí
 * 
 * Para activar: renombrar este archivo a database.php
 *               (guardar el original como database_mysql.php)
 */

// ─── SUPABASE CONNECTION SETTINGS ─────────────────────────────────────────────
// Reemplaza estos valores con los de tu proyecto Supabase:
//   Settings → Database → Connection parameters
define('DB_HOST',     'db.XXXXXXXXXXXXXXXX.supabase.co'); // tu host de Supabase
define('DB_PORT',     '5432');
define('DB_USER',     'postgres');
define('DB_PASS',     'TU_PASSWORD_SUPABASE');            // tu password de DB
define('DB_NAME',     'postgres');
define('DB_CHARSET',  'utf8');
// ──────────────────────────────────────────────────────────────────────────────

// DSN para PDO PostgreSQL
$dsn = "pgsql:host=" . DB_HOST
     . ";port=" . DB_PORT
     . ";dbname=" . DB_NAME
     . ";options='--client_encoding=UTF8'";

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log("Error de conexión Supabase: " . $e->getMessage());
    die("Error en la conexión a la base de datos. Por favor intente más tarde.");
}

// Alias global para uso en helpers/modelos ($conn compatible con código existente)
// NOTA: Este objeto es PDO, no mysqli. Ver funciones de compatibilidad abajo.
$conn = $pdo;

// Configurar zona horaria
date_default_timezone_set('America/Bogota');

// ============================================================
// CAPA DE COMPATIBILIDAD: reemplaza las funciones mysqli del
// archivo database.php original con equivalentes PDO/PostgreSQL
// ============================================================

/**
 * Ejecutar consulta preparada (equivalente a ejecutarConsulta)
 * Cambios vs MySQL:
 *  - Usa ? como placeholder (igual que MySQL)
 *  - No necesita bind_param con tipos
 */
function ejecutarConsulta($sql, $tipos = "", $params = array()) {
    global $pdo;

    // Convertir sintaxis MySQL a PostgreSQL si es necesario
    $sql = convertirSQL($sql);

    try {
        $stmt = $pdo->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare: " . implode(', ', $pdo->errorInfo()));
            return false;
        }

        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        return $stmt;
    } catch (PDOException $e) {
        error_log("Error en execute: " . $e->getMessage() . " | SQL: " . $sql);
        return false;
    }
}

/**
 * Obtener una fila
 */
function obtenerFila($sql, $tipos = "", $params = array()) {
    $stmt = ejecutarConsulta($sql, $tipos, $params);
    if (!$stmt) return null;
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/**
 * Obtener múltiples filas
 */
function obtenerFilas($sql, $tipos = "", $params = array()) {
    $stmt = ejecutarConsulta($sql, $tipos, $params);
    if (!$stmt) return array();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener tenant_id actual
 */
function getTenantId() {
    if (defined('TENANT_ID')) {
        $tid = (int)TENANT_ID;
        if ($tid === 0) {
            error_log("WARNING: getTenantId() retorna 0");
            return 0;
        }
        return $tid;
    }
    error_log("CRITICAL: TENANT_ID no está definido");
    return 0;
}

/**
 * Consulta scoped por tenant_id
 */
function ejecutarConsultaScoped($sql, $tipos = "", $params = array()) {
    $tenant_id = getTenantId();
    array_unshift($params, $tenant_id);
    return ejecutarConsulta($sql, "i" . $tipos, $params);
}

/**
 * Obtener fila scoped por tenant
 */
function obtenerFilaScoped($sql, $tipos = "", $params = array()) {
    $tenant_id = getTenantId();
    if ($tenant_id <= 0) {
        error_log("SECURITY: obtenerFilaScoped() rechaza tenant_id={$tenant_id}");
        return null;
    }
    array_unshift($params, $tenant_id);
    return obtenerFila($sql, "i" . $tipos, $params);
}

/**
 * Obtener filas scoped por tenant
 */
function obtenerFilasScoped($sql, $tipos = "", $params = array()) {
    $tenant_id = getTenantId();
    if ($tenant_id <= 0) {
        error_log("SECURITY: obtenerFilasScoped() rechaza tenant_id={$tenant_id}");
        return array();
    }
    array_unshift($params, $tenant_id);
    return obtenerFilas($sql, "i" . $tipos, $params);
}

/**
 * Agregar filtro tenant a query
 */
function agregarFiltroTenant($sql) {
    if (stripos($sql, 'WHERE') !== false) {
        $sql = preg_replace('/WHERE/i', 'WHERE tenant_id = ? AND', $sql, 1);
    } else {
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
 * Validar que un registro pertenece al tenant actual
 */
function validarPerteneceATenant($tabla, $id) {
    global $pdo;
    $tenant_id = getTenantId();
    // PostgreSQL usa comillas dobles para identificadores
    $sql = "SELECT COUNT(*) as total FROM \"$tabla\" WHERE id = ? AND tenant_id = ?";
    $stmt = ejecutarConsulta($sql, "ii", [$id, $tenant_id]);
    if ($stmt) {
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila['total'] > 0;
    }
    return false;
}

/**
 * Obtener el último ID insertado (equivalente a insert_id)
 */
function obtenerUltimoId() {
    global $pdo;
    return $pdo->lastInsertId();
}

/**
 * Compatibilidad: simula $conn->insert_id para código legado
 * En PDO se usa lastInsertId()
 */
function getLastInsertId() {
    global $pdo;
    return $pdo->lastInsertId();
}

/**
 * Convertir sintaxis MySQL a PostgreSQL
 * Maneja las diferencias más comunes automáticamente
 */
function convertirSQL($sql) {
    // Escapar backticks de MySQL → comillas dobles de PostgreSQL
    // Solo en nombres de tablas/campos (dentro de FROM, JOIN, WHERE, etc.)
    // Reemplazar `nombre` → "nombre"
    $sql = preg_replace('/`([^`]+)`/', '"$1"', $sql);

    // NOW() es igual en PostgreSQL

    // IFNULL → COALESCE
    $sql = preg_replace('/\bIFNULL\s*\(/i', 'COALESCE(', $sql);

    // GROUP_CONCAT → STRING_AGG  (básico, sin separador custom)
    $sql = preg_replace('/GROUP_CONCAT\s*\(([^)]+)\)/i', 'STRING_AGG($1::TEXT, \',\')', $sql);

    // DATE_FORMAT → TO_CHAR
    $sql = preg_replace_callback(
        '/DATE_FORMAT\s*\(([^,]+),\s*\'([^\']+)\'\)/i',
        function($matches) {
            $campo = $matches[1];
            $formato = $matches[2];
            // Convertir formato MySQL a PostgreSQL
            $formato = str_replace(
                ['%Y', '%m', '%d', '%H', '%i', '%s'],
                ['YYYY', 'MM', 'DD', 'HH24', 'MI', 'SS'],
                $formato
            );
            return "TO_CHAR($campo, '$formato')";
        },
        $sql
    );

    // LIMIT x, y → LIMIT y OFFSET x
    $sql = preg_replace_callback(
        '/LIMIT\s+(\d+)\s*,\s*(\d+)/i',
        function($m) {
            return "LIMIT {$m[2]} OFFSET {$m[1]}";
        },
        $sql
    );

    return $sql;
}
?>
