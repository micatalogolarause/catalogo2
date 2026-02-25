<?php
/**
 * Configuración General del Proyecto
 */

// Definir la raíz de la aplicación
define('APP_ROOT', dirname(dirname(__FILE__)));

// Auto-detectar APP_URL
// Prioridades: 1) APP_URL env var (dominio custom en Vercel), 2) VERCEL_URL (auto Vercel),
//              3) auto-detección local (XAMPP / IIS)
if (getenv('APP_URL')) {
    define('APP_URL', rtrim(getenv('APP_URL'), '/'));
} elseif (getenv('VERCEL_URL')) {
    // VERCEL_URL lo inyecta Vercel automáticamente (sin https://)
    define('APP_URL', 'https://' . getenv('VERCEL_URL'));
} else {
    $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'];
    $base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base_path = rtrim($base_path, '/');
    if ($base_path === '' || $base_path === '/') {
        define('APP_URL', $protocol . '://' . $host);
    } else {
        define('APP_URL', $protocol . '://' . $host . $base_path);
    }
}

define('APP_UPLOADS', APP_ROOT . '/public/images');

// Configuración de sesión
define('SESSION_TIMEOUT', 3600); // 1 hora
define('REMEMBER_ME_DAYS', 30);

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

// Función de sanitización
function sanitizar($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

// Función para validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
