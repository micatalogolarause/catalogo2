<?php
/**
 * TENANT RESOLVER - Middleware de Resolución de Multi-Tenancy
 * Detecta y valida el tenant desde la URL
 */

class TenantResolver {
    private static $tenant = null;
    private static $tenant_id = null;
    
    /**
     * Resolver tenant desde la URL
     * Patrón soportado: /catalogo2/{slug} o /{slug}
     * 
     * @return bool True si se resolvió correctamente
     */
    public static function resolve() {
        // Ya resuelto previamente en esta request
        if (self::$tenant !== null) {
            return true;
        }
        
        // SIEMPRE intentar detectar desde URL primero (prioridad sobre sesión)
        $tenant_slug = self::detectTenantFromUrl();
        
        // Si hay slug en URL y es diferente al de sesión, limpiar sesión
        if (!empty($tenant_slug) && isset($_SESSION['tenant_slug']) && $_SESSION['tenant_slug'] !== $tenant_slug) {
            unset($_SESSION['tenant_id']);
            unset($_SESSION['tenant_slug']);
            unset($_SESSION['tenant_data']);
        }
        
        // Si hay slug en URL, usarlo (tiene prioridad)
        if (!empty($tenant_slug)) {
            // Primero intentar desde caché cookie (evita consulta BD en cada navegación en Vercel)
            $tenant = self::getCachedTenant($tenant_slug);
            if (!$tenant) {
                $tenant = self::getTenantBySlug($tenant_slug);
                if ($tenant && $tenant['estado'] === 'activo') {
                    self::setCachedTenant($tenant);
                }
            }
            
            if (!$tenant) {
                return self::handleTenantNotFound($tenant_slug);
            }
            
            // Validar estado del tenant
            if ($tenant['estado'] !== 'activo') {
                return self::handleInactiveTenant($tenant);
            }
            
            // Establecer tenant
            self::$tenant = $tenant;
            self::$tenant_id = $tenant['id'];
            
            // Guardar en sesión
            $_SESSION['tenant_id'] = $tenant['id'];
            $_SESSION['tenant_slug'] = $tenant['slug'];
            $_SESSION['tenant_data'] = $tenant;
            
            // Definir constantes globales
            define('TENANT_ID', $tenant['id']);
            define('TENANT_SLUG', $tenant['slug']);
            define('TENANT_NAME', $tenant['nombre']);
            define('TENANT_WHATSAPP', $tenant['whatsapp_phone']);
            
            return true;
        }
        
        // No hay slug en URL, verificar sesión
        if (isset($_SESSION['tenant_id']) && isset($_SESSION['tenant_slug'])) {
            // Revalidar estado del tenant desde BD
            $tenant = self::getTenantBySlug($_SESSION['tenant_slug']);
            
            if (!$tenant || $tenant['estado'] !== 'activo') {
                // Tenant desactivado o eliminado, limpiar sesión
                unset($_SESSION['tenant_id']);
                unset($_SESSION['tenant_slug']);
                unset($_SESSION['tenant_data']);
                
                if (!$tenant) {
                    return self::setDefaultTenant();
                } else {
                    return self::handleInactiveTenant($tenant);
                }
            }
            
            self::$tenant_id = $tenant['id'];
            self::$tenant = $tenant;
            $_SESSION['tenant_data'] = $tenant; // Actualizar datos en sesión
            
            if (!defined('TENANT_ID')) {
                define('TENANT_ID', self::$tenant_id);
                define('TENANT_SLUG', $tenant['slug']);
                define('TENANT_NAME', $tenant['nombre']);
                define('TENANT_WHATSAPP', $tenant['whatsapp_phone']);
            }
            
            return true;
        }
        
        // Si no hay slug ni sesión, usar tenant por defecto
        return self::setDefaultTenant();
    }
    
    /**
     * Detectar slug del tenant desde la URL
     * Soporta patrones:
     * - /catalogo2/mauricio → mauricio
     * - /catalogo2/mauricio/tienda → mauricio
     * - ?tenant=mauricio → mauricio
     * 
     * @return string|null Slug del tenant o null
     */
    private static function detectTenantFromUrl() {
        // 1. Prioridad: Parámetro GET explícito
        if (isset($_GET['tenant']) && !empty($_GET['tenant'])) {
            return self::sanitizeSlug($_GET['tenant']);
        }
        
        // 2. Detectar desde REQUEST_URI
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Remover query string
        $path = parse_url($request_uri, PHP_URL_PATH);
        
        // Remover base path (/catalogo2)
        $base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $base_path = rtrim($base_path, '/');
        
        if (!empty($base_path) && strpos($path, $base_path) === 0) {
            $path = substr($path, strlen($base_path));
        }
        
        // Limpiar path
        $path = trim($path, '/');
        
        // Si está vacío, no hay tenant en URL
        if (empty($path)) {
            return null;
        }
        
        // Obtener primer segmento (el slug del tenant)
        $segments = explode('/', $path);
        $first_segment = $segments[0];
        
        // Validar que no sea un controlador conocido (para evitar conflictos)
        $known_controllers = ['admin', 'tienda', 'auth', 'api', 'index.php'];
        if (in_array($first_segment, $known_controllers)) {
            return null;
        }
        
        // Validar formato de slug (solo letras, números, guiones)
        if (preg_match('/^[a-z0-9\-]+$/', $first_segment)) {
            return $first_segment;
        }
        
        return null;
    }
    
    /**
     * Obtener tenant desde BD por slug
     * 
     * @param string $slug Slug del tenant
     * @return array|null Datos del tenant o null
     */
    private static function getTenantBySlug($slug) {
        $sql = "SELECT id, nombre, slug, whatsapp_phone, logo, tema, tema_color, titulo_empresa, estado 
                FROM tenants 
                WHERE LOWER(slug) = LOWER(?) 
                LIMIT 1";
        return obtenerFila($sql, "s", [$slug]);
    }
    
    /**
     * Leer tenant cacheado en cookie (TTL 5 min)
     */
    private static function getCachedTenant($slug) {
        $cookieName = 'tc_' . preg_replace('/[^a-z0-9]/', '', $slug);
        if (empty($_COOKIE[$cookieName])) return null;
        $data = json_decode(base64_decode($_COOKIE[$cookieName]), true);
        if (!is_array($data) || empty($data['ts']) || empty($data['sig'])) return null;
        if (time() - (int)$data['ts'] > 300) return null; // expirado
        $secret = getenv('AUTH_COOKIE_SECRET') ?: 'catalogo2_auth_secret_change_me';
        $expected = hash_hmac('sha256', ($data['slug'] ?? '') . '|' . ($data['id'] ?? '') . '|' . $data['ts'], $secret);
        if (!hash_equals($expected, $data['sig'])) return null;
        return $data;
    }

    /**
     * Guardar tenant en cookie firmada (TTL 5 min)
     */
    private static function setCachedTenant($tenant) {
        $secret = getenv('AUTH_COOKIE_SECRET') ?: 'catalogo2_auth_secret_change_me';
        $ts = time();
        $sig = hash_hmac('sha256', $tenant['slug'] . '|' . $tenant['id'] . '|' . $ts, $secret);
        $payload = array_merge($tenant, ['ts' => $ts, 'sig' => $sig]);
        $cookieName = 'tc_' . preg_replace('/[^a-z0-9]/', '', $tenant['slug']);
        if (!headers_sent()) {
            setcookie($cookieName, base64_encode(json_encode($payload)), [
                'expires'  => $ts + 300,
                'path'     => '/',
                'secure'   => true,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
    }

    /**
     * Establecer tenant por defecto (backward compatibility)
     * NOTA: Ahora muestra error 404 en lugar de cargar un tenant por defecto
     * 
     * @return bool
     */
    private static function setDefaultTenant() {
        // Si hay exactamente un tenant activo, redirigir a él automáticamente
        $sql = "SELECT slug, titulo_empresa, nombre 
                FROM tenants 
                WHERE estado = 'activo'
                ORDER BY nombre ASC";
        
        $tenants_disponibles = obtenerFilas($sql) ?: [];

        if (count($tenants_disponibles) === 1) {
            // Un solo tenant activo: redirigir directamente
            $slug = $tenants_disponibles[0]['slug'];
            $base_url = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
            $base_path = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
            header('Location: ' . $base_url . $base_path . '/' . $slug);
            exit;
        }
        http_response_code(404);
        
        $base_url = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
        $base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $base_path = rtrim($base_path, '/');
        
        echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <title>Acceso Directo No Permitido</title>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 2em;
        }
        .icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .tenants-list {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .tenants-list h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        .tenant-link {
            display: block;
            padding: 12px 15px;
            margin: 8px 0;
            background: white;
            border: 2px solid #667eea;
            border-radius: 8px;
            color: #667eea;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }
        .tenant-link:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .tenant-name {
            display: block;
            font-size: 0.9em;
            opacity: 0.8;
            margin-top: 3px;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box strong {
            color: #1976d2;
        }
        code {
            background: #f5f5f5;
            padding: 2px 8px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #e91e63;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='icon'>🏪</div>
        <h1>Acceso Directo No Permitido</h1>
        <p>No puedes acceder directamente a la raíz del sistema. Este es un sistema multi-tenant que requiere que especifiques el nombre de la tienda en la URL.</p>
        
        <div class='info-box'>
            <strong>📍 Formato correcto:</strong><br>
            <code>{$base_url}{$base_path}/<strong>{nombre-tienda}</strong></code>
        </div>";
        
        if (!empty($tenants_disponibles)) {
            echo "<div class='tenants-list'>
                <h3>🛍️ Tiendas Disponibles:</h3>";
            
            foreach ($tenants_disponibles as $tenant) {
                $tenant_url = $base_url . $base_path . '/' . htmlspecialchars($tenant['slug']);
                $tenant_titulo = htmlspecialchars($tenant['titulo_empresa'] ?: $tenant['nombre']);
                $tenant_slug = htmlspecialchars($tenant['slug']);
                
                echo "<a href='{$tenant_url}' class='tenant-link'>
                    <strong>{$tenant_titulo}</strong>
                    <span class='tenant-name'>/{$tenant_slug}</span>
                </a>";
            }
            
            echo "</div>";
        }
        
        echo "<p style='margin-top: 30px; font-size: 0.9em; color: #999;'>
            Si eres administrador, accede al panel desde la URL específica de tu tienda.
        </p>
    </div>
</body>
</html>";
        exit;
    }
    
    /**
     * Manejar tenant no encontrado
     * 
     * @param string $slug
     * @return bool
     */
    private static function handleTenantNotFound($slug) {
        // Limpiar sesión de tenant anterior si existía
        unset($_SESSION['tenant_id']);
        unset($_SESSION['tenant_slug']);
        unset($_SESSION['tenant_data']);
        
        http_response_code(404);
        echo "<!DOCTYPE html>
<html>
<head>
    <title>Tienda No Encontrada</title>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
        .error-box { background: white; padding: 30px; border-radius: 10px; display: inline-block; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #e74c3c; }
        p { color: #666; }
    </style>
</head>
<body>
    <div class='error-box'>
        <h1>🏪 Tienda No Encontrada</h1>
        <p>La tienda <strong>\"" . htmlspecialchars($slug) . "\"</strong> no existe o no está disponible.</p>
    </div>
</body>
</html>";
        exit;
    }
    
    /**
     * Manejar tenant inactivo o bloqueado
     * 
     * @param array $tenant
     * @return bool
     */
    private static function handleInactiveTenant($tenant) {
        unset($_SESSION['tenant_id']);
        unset($_SESSION['tenant_slug']);
        unset($_SESSION['tenant_data']);
        
        $mensaje = $tenant['estado'] === 'bloqueado' 
            ? 'Esta tienda ha sido bloqueada temporalmente.' 
            : 'Esta tienda no está activa en este momento.';
        
        http_response_code(403);
        echo "<!DOCTYPE html>
<html>
<head>
    <title>Tienda No Disponible</title>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
        .error-box { background: white; padding: 30px; border-radius: 10px; display: inline-block; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #f39c12; }
        p { color: #666; }
    </style>
</head>
<body>
    <div class='error-box'>
        <h1>⚠️ Tienda No Disponible</h1>
        <p>" . $mensaje . "</p>
    </div>
</body>
</html>";
        exit;
    }
    
    /**
     * Sanitizar slug
     * 
     * @param string $slug
     * @return string
     */
    private static function sanitizeSlug($slug) {
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        return $slug;
    }
    
    /**
     * Obtener tenant actual
     * 
     * @return array|null
     */
    public static function getCurrentTenant() {
        return self::$tenant;
    }
    
    /**
     * Obtener tenant ID actual
     * 
     * @return int|null
     */
    public static function getTenantId() {
        return self::$tenant_id;
    }
    
    /**
     * Verificar si el tenant actual es el default
     * 
     * @return bool
     */
    public static function isDefaultTenant() {
        return self::$tenant_id === 1 || (self::$tenant && self::$tenant['slug'] === 'default');
    }
}
?>
