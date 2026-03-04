<?php
/**
 * Configuración General del Proyecto
 */

// Definir la raíz de la aplicación
define('APP_ROOT', dirname(dirname(__FILE__)));

// Auto-detectar APP_URL
// Prioridades:
//   1) APP_URL env var (dominio custom en Vercel o manual)
//   2) VERCEL_PROJECT_PRODUCTION_URL (URL estable de producción, sin https://)
//   3) VERCEL_URL (URL única del deployment — cambia con cada deploy, usar solo como fallback)
//   4) auto-detección local (XAMPP / IIS)
if (getenv('APP_URL')) {
    define('APP_URL', rtrim(getenv('APP_URL'), '/'));
} elseif (getenv('VERCEL_PROJECT_PRODUCTION_URL')) {
    define('APP_URL', 'https://' . getenv('VERCEL_PROJECT_PRODUCTION_URL'));
} elseif (getenv('VERCEL_URL')) {
    define('APP_URL', 'https://' . getenv('VERCEL_URL'));
} else {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Si el host es .vercel.app, Railway, Render u otro proxy: siempre https, sin base_path
    $esProduccion = (
        strpos($host, '.vercel.app') !== false ||
        strpos($host, '.railway.app') !== false ||
        strpos($host, '.onrender.com') !== false ||
        getenv('VERCEL') !== false ||
        getenv('VERCEL_ENV') !== false ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    );

    if ($esProduccion) {
        define('APP_URL', 'https://' . $host);
    } else {
        // Local: detectar protocolo y base_path normalmente
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $protocol  = $isHttps ? 'https' : 'http';
        $base_path = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($base_path === '' || $base_path === '/') {
            define('APP_URL', $protocol . '://' . $host);
        } else {
            define('APP_URL', $protocol . '://' . $host . $base_path);
        }
    }
}

define('APP_UPLOADS', APP_ROOT . '/public/images');

// Configuración de sesión
define('SESSION_TIMEOUT', 3600); // 1 hora
define('REMEMBER_ME_DAYS', 30);
define('AUTH_COOKIE_NAME', 'catalogo_auth');
define('AUTH_COOKIE_SECRET', getenv('AUTH_COOKIE_SECRET') ?: 'catalogo2_auth_secret_change_me');

// Configuración de WhatsApp
define('WHATSAPP_API_URL', 'https://api.twilio.com');
define('WHATSAPP_ACCOUNT_SID', 'your_account_sid'); // Cambiar después
define('WHATSAPP_AUTH_TOKEN', 'your_auth_token'); // Cambiar después
define('WHATSAPP_PHONE_FROM', '+1234567890'); // Cambiar después

// Configuración de seguridad
define('ADMIN_PATH', '/admin');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', array('jpg', 'jpeg', 'png', 'gif', 'webp'));

// En Vercel el filesystem es de solo lectura; las sesiones deben ir a /tmp
if (isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    ini_set('session.save_path', '/tmp');
}

// Iniciador de sesión
if (!session_id()) {
    session_start();
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function set_auth_cookie(array $data) {
    $payload = [
        'uid' => (int)($data['uid'] ?? 0),
        'rol' => (string)($data['rol'] ?? ''),
        'tenant_slug' => (string)($data['tenant_slug'] ?? ''),
        'usuario' => (string)($data['usuario'] ?? ''),
        'nombre' => (string)($data['nombre'] ?? ''),
        'exp' => time() + (REMEMBER_ME_DAYS * 24 * 60 * 60)
    ];

    $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
    $payloadB64 = base64url_encode($payloadJson);
    $signature = hash_hmac('sha256', $payloadB64, AUTH_COOKIE_SECRET);
    $token = $payloadB64 . '.' . $signature;

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie(AUTH_COOKIE_NAME, $token, [
        'expires' => $payload['exp'],
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function clear_auth_cookie() {
    setcookie(AUTH_COOKIE_NAME, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function restore_session_from_auth_cookie() {
    if (isset($_SESSION['usuario_id']) && isset($_SESSION['rol'])) {
        return true;
    }

    if (empty($_COOKIE[AUTH_COOKIE_NAME])) {
        return false;
    }

    $token = $_COOKIE[AUTH_COOKIE_NAME];
    $parts = explode('.', $token);
    if (count($parts) !== 2) {
        return false;
    }

    $payloadB64 = $parts[0];
    $signature = $parts[1];
    $expected = hash_hmac('sha256', $payloadB64, AUTH_COOKIE_SECRET);

    if (!hash_equals($expected, $signature)) {
        return false;
    }

    $payloadJson = base64url_decode($payloadB64);
    $payload = json_decode($payloadJson, true);
    if (!is_array($payload)) {
        return false;
    }

    if (empty($payload['exp']) || (int)$payload['exp'] < time()) {
        return false;
    }

    if (empty($payload['uid']) || empty($payload['rol'])) {
        return false;
    }

    $_SESSION['usuario_id'] = (int)$payload['uid'];
    $_SESSION['rol'] = (string)$payload['rol'];
    $_SESSION['usuario'] = (string)($payload['usuario'] ?? '');
    $_SESSION['nombre'] = (string)($payload['nombre'] ?? '');

    if (!empty($payload['tenant_slug']) && empty($_SESSION['tenant_slug'])) {
        $_SESSION['tenant_slug'] = (string)$payload['tenant_slug'];
    }

    return true;
}

restore_session_from_auth_cookie();

// Función de sanitización
function sanitizar($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

// Función para validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
