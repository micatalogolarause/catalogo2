<?php
/**
 * Configuración General del Proyecto
 */

// Definir la raíz de la aplicación
define('APP_ROOT', dirname(dirname(__FILE__)));

// Auto-detectar APP_URL para XAMPP e IIS (con IP:puerto)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST']; // Incluye puerto si existe (ej: localhost, 192.168.1.100:8080)

// Detectar carpeta base desde la ruta del archivo actual
$base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_path = rtrim($base_path, '/');

// Si está en raíz del dominio/IP, base_path será vacío
if ($base_path === '' || $base_path === '/') {
    define('APP_URL', $protocol . '://' . $host);
} else {
    define('APP_URL', $protocol . '://' . $host . $base_path);
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
