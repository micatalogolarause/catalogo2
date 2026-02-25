<?php
/**
 * Script de Validación Pre-Deployment
 * Verifica que el sistema esté listo para pasar a Windows Server 2019
 */

define('SEPARATOR', str_repeat('=', 70));
define('CHECK_OK', '✅ ');
define('CHECK_FAIL', '❌ ');
define('CHECK_WARN', '⚠️  ');

$status = [
    'errors' => [],
    'warnings' => [],
    'success' => []
];

// =============================================================================
// 1. VERIFICAR ESTRUCTURA DE CARPETAS
// =============================================================================
echo "\n" . SEPARATOR . "\n";
echo "📁 VERIFICACIÓN DE ESTRUCTURA DE CARPETAS\n";
echo SEPARATOR . "\n";

$folders = [
    'config' => 'Carpeta de configuración',
    'app' => 'Carpeta de aplicación',
    'app/controllers' => 'Controladores',
    'app/models' => 'Modelos',
    'app/views' => 'Vistas',
    'public' => 'Archivos públicos',
    'public/css' => 'Hojas de estilo',
    'public/js' => 'Scripts JavaScript',
    'public/images' => 'Imágenes generales',
    'public/tenants' => 'Carpetas por tenant'
];

foreach ($folders as $folder => $desc) {
    $path = dirname(__DIR__) . '/' . $folder;
    if (is_dir($path)) {
        echo CHECK_OK . "$desc\n";
        $status['success'][] = $folder;
    } else {
        echo CHECK_FAIL . "$desc (NO EXISTE: $path)\n";
        $status['errors'][] = "Falta carpeta: $folder";
    }
}

// =============================================================================
// 2. VERIFICAR ARCHIVOS CRÍTICOS
// =============================================================================
echo "\n" . SEPARATOR . "\n";
echo "📄 VERIFICACIÓN DE ARCHIVOS CRÍTICOS\n";
echo SEPARATOR . "\n";

$files = [
    'config/config.php' => 'Configuración principal',
    'config/database.php' => 'Configuración de BD',
    'config/TenantResolver.php' => 'Resolutor de tenants',
    'index.php' => 'Punto de entrada',
    '.htaccess' => 'Reescritura de URLs',
    'public/css/estilos.css' => 'Estilos principales',
    'public/css/temas.css' => 'Temas de color'
];

foreach ($files as $file => $desc) {
    $path = dirname(__DIR__) . '/' . $file;
    if (file_exists($path)) {
        echo CHECK_OK . "$desc\n";
        $status['success'][] = $file;
    } else {
        echo CHECK_FAIL . "$desc (NO EXISTE: $path)\n";
        $status['errors'][] = "Falta archivo: $file";
    }
}

// =============================================================================
// 3. VERIFICAR PERMISOS DE ESCRITURA
// =============================================================================
echo "\n" . SEPARATOR . "\n";
echo "🔒 VERIFICACIÓN DE PERMISOS DE ESCRITURA\n";
echo SEPARATOR . "\n";

$writable = [
    'public/images' => 'Imágenes',
    'public/tenants' => 'Uploads por tenant',
    'logs' => 'Carpeta de logs'
];

foreach ($writable as $folder => $desc) {
    $path = dirname(__DIR__) . '/' . $folder;
    
    // Crear carpeta si no existe
    if (!is_dir($path)) {
        if (!@mkdir($path, 0755, true)) {
            echo CHECK_WARN . "$desc (creada con permisos limitados)\n";
            $status['warnings'][] = "$folder permisos limitados";
        } else {
            echo CHECK_OK . "$desc (creada correctamente)\n";
        }
    }
    
    if (is_writable($path)) {
        echo CHECK_OK . "$desc es escribible\n";
        $status['success'][] = "$folder writable";
    } else {
        echo CHECK_FAIL . "$desc NO es escribible (ejecutar: icacls)\n";
        $status['errors'][] = "$folder no escribible";
    }
}

// =============================================================================
// 4. VERIFICAR CONEXIÓN A BD
// =============================================================================
echo "\n" . SEPARATOR . "\n";
echo "🗄️  VERIFICACIÓN DE BASE DE DATOS\n";
echo SEPARATOR . "\n";

require_once dirname(__DIR__) . '/config/database.php';

// Obtener credenciales de config
$db_config = [
    'host' => defined('DB_HOST') ? DB_HOST : 'localhost',
    'user' => defined('DB_USER') ? DB_USER : 'root',
    'pass' => defined('DB_PASS') ? DB_PASS : '',
    'name' => defined('DB_NAME') ? DB_NAME : 'catalogo_tienda'
];

echo "Intentando conexión a: {$db_config['host']}\n";

$conn = @new mysqli($db_config['host'], $db_config['user'], $db_config['pass']);

if ($conn->connect_error) {
    echo CHECK_FAIL . "No se pudo conectar a MySQL\n";
    echo "Error: " . $conn->connect_error . "\n";
    $status['errors'][] = "Conexión a MySQL fallida";
} else {
    echo CHECK_OK . "Conexión a MySQL exitosa\n";
    
    // Verificar BD
    $result = $conn->query("SHOW DATABASES LIKE '{$db_config['name']}'");
    if ($result && $result->num_rows > 0) {
        echo CHECK_OK . "Base de datos '{$db_config['name']}' existe\n";
        
        // Conectar a BD específica
        $conn->select_db($db_config['name']);
        
        // Contar tablas
        $tables = $conn->query("SHOW TABLES");
        if ($tables) {
            $count = $tables->num_rows;
            echo CHECK_OK . "BD contiene $count tablas\n";
            $status['success'][] = "BD OK with $count tables";
            
            // Verificar tablas críticas
            $critical_tables = ['tenants', 'usuarios', 'productos', 'pedidos'];
            foreach ($critical_tables as $table) {
                $check = $conn->query("SHOW TABLES LIKE '$table'");
                if ($check && $check->num_rows > 0) {
                    echo CHECK_OK . "Tabla '$table' existe\n";
                } else {
                    echo CHECK_FAIL . "Tabla crítica '$table' FALTA\n";
                    $status['errors'][] = "Falta tabla: $table";
                }
            }
        }
    } else {
        echo CHECK_FAIL . "Base de datos '{$db_config['name']}' no existe\n";
        $status['errors'][] = "BD no existe";
    }
    
    $conn->close();
}

// =============================================================================
// 5. VERIFICAR CONFIGURACIÓN PHP
// =============================================================================
echo "\n" . SEPARATOR . "\n";
echo "⚙️  VERIFICACIÓN DE CONFIGURACIÓN PHP\n";
echo SEPARATOR . "\n";

$php_config = [
    'memory_limit' => ['min' => '256M', 'current' => ini_get('memory_limit')],
    'max_execution_time' => ['min' => '300', 'current' => ini_get('max_execution_time')],
    'upload_max_filesize' => ['min' => '5M', 'current' => ini_get('upload_max_filesize')],
    'post_max_size' => ['min' => '5M', 'current' => ini_get('post_max_size')]
];

foreach ($php_config as $setting => $values) {
    echo "$setting: {$values['current']} (recomendado: {$values['min']})\n";
}

echo "\nExtensiones requeridas:\n";
$extensions = ['mysqli', 'curl', 'gd', 'mbstring', 'json'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo CHECK_OK . "$ext habilitada\n";
    } else {
        echo CHECK_FAIL . "$ext NO está habilitada\n";
        $status['errors'][] = "Extensión faltante: $ext";
    }
}

// =============================================================================
// 6. VERIFICAR CONFIGURACIÓN MULTI-TENANCY
// =============================================================================
echo "\n" . SEPARATOR . "\n";
echo "🏢 VERIFICACIÓN DE MULTI-TENANCY\n";
echo SEPARATOR . "\n";

if (defined('APP_URL')) {
    echo CHECK_OK . "APP_URL: " . APP_URL . "\n";
} else {
    echo CHECK_FAIL . "APP_URL no está definida\n";
    $status['errors'][] = "APP_URL no definida";
}

// Probar TenantResolver
if (file_exists(dirname(__DIR__) . '/config/TenantResolver.php')) {
    require_once dirname(__DIR__) . '/config/TenantResolver.php';
    echo CHECK_OK . "TenantResolver puede ser incluido\n";
}

// =============================================================================
// 7. VERIFICACIÓN DE SEGURIDAD
// =============================================================================
echo "\n" . SEPARATOR . "\n";
echo "🔐 VERIFICACIÓN DE SEGURIDAD\n";
echo SEPARATOR . "\n";

// .htaccess
if (file_exists(dirname(__DIR__) . '/.htaccess')) {
    echo CHECK_OK . ".htaccess existe (protege archivos sensibles)\n";
}

// Verificar que config no es accesible
$test_url = APP_URL . '/config/config.php';
echo "Verificar manualmente que no accede a: $test_url\n";

// =============================================================================
// 8. RESUMEN FINAL
// =============================================================================
echo "\n" . SEPARATOR . "\n";
echo "📊 RESUMEN DE VALIDACIÓN\n";
echo SEPARATOR . "\n";

$total_success = count($status['success']);
$total_warnings = count($status['warnings']);
$total_errors = count($status['errors']);

echo "✅ Verificaciones exitosas: $total_success\n";
echo "⚠️  Advertencias: $total_warnings\n";
echo "❌ Errores: $total_errors\n";

if ($total_errors > 0) {
    echo "\n❌ ERRORES ENCONTRADOS:\n";
    foreach ($status['errors'] as $error) {
        echo "  - $error\n";
    }
    echo "\n⚠️  El sistema NO está listo para producción.\n";
    echo "Resuelva los errores antes de continuar.\n";
    $final_status = 'FALLÓ';
} else if ($total_warnings > 0) {
    echo "\n⚠️  ADVERTENCIAS:\n";
    foreach ($status['warnings'] as $warning) {
        echo "  - $warning\n";
    }
    echo "\n✅ El sistema está casi listo para producción.\n";
    echo "Se recomienda revisar las advertencias.\n";
    $final_status = 'ADVERTENCIA';
} else {
    echo "\n✅ EL SISTEMA ESTÁ LISTO PARA PRODUCCIÓN\n";
    echo "Todas las verificaciones pasaron correctamente.\n";
    $final_status = 'ÉXITO';
}

echo "\nFecha de validación: " . date('Y-m-d H:i:s') . "\n";
echo SEPARATOR . "\n\n";

// =============================================================================
// 9. PASOS SIGUIENTES
// =============================================================================
if ($final_status === 'ÉXITO') {
    echo "📋 PASOS SIGUIENTES PARA DEPLOYMENT:\n\n";
    echo "1. Configurar XAMPP en puerto 81 (editar httpd.conf)\n";
    echo "2. Cambiar contraseña de MySQL: ALTER USER 'root' IDENTIFIED BY 'password';\n";
    echo "3. Copiar /catalogo2 a C:\\xampp\\htdocs\\catalogo2\n";
    echo "4. Configurar permisos: icacls C:\\xampp\\htdocs\\catalogo2\\public\\tenants /grant Users:M /T\n";
    echo "5. Reiniciar Apache: net stop Apache2.4 && net start Apache2.4\n";
    echo "6. Acceder a: http://34.193.89.155:81/catalogo2\n";
    echo "7. Revisar: " . APP_URL . "/scripts/deployment_check.php\n";
}

?>
