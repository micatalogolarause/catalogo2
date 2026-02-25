<?php
/**
 * Helpers de uploads por tenant — almacenamiento en Cloudinary
 */

// Cargar helper de Cloudinary
if (!function_exists('uploadToCloudinary')) {
    require_once __DIR__ . '/cloudinary.php';
}

// Base URL del proyecto (ajústalo si cambia el subdirectorio)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..'); // c:\xampp\htdocs\catalogo2
}
if (!defined('BASE_URL')) {
    // En XAMPP local, el proyecto vive como /catalogo2
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('BASE_URL', $scheme . '://' . $host . '/catalogo2');
}

function tenant_upload_base_dir($tenantId = null) {
    $tid = $tenantId ?? (defined('TENANT_ID') ? TENANT_ID : 0);
    return BASE_PATH . '/public/tenants/' . $tid;
}

function tenant_upload_base_url($tenantId = null) {
    $tid = $tenantId ?? (defined('TENANT_ID') ? TENANT_ID : 0);
    return BASE_URL . '/public/tenants/' . $tid;
}

function ensure_upload_dirs_for_tenant($tenantId) {
    $base = tenant_upload_base_dir($tenantId);
    $dirs = [
        $base,
        $base . '/images',
        $base . '/docs',
        $base . '/temp'
    ];
    foreach ($dirs as $d) {
        if (!is_dir($d)) {
            @mkdir($d, 0775, true);
        }
        // Crear index.html para evitar listado de directorios
        $indexFile = rtrim($d, '/\\') . '/index.html';
        if (!file_exists($indexFile)) {
            @file_put_contents($indexFile, '<!doctype html><title>403</title>');
        }
    }
    return true;
}

function sanitize_filename($filename) {
    $filename = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $filename);
    return trim($filename, '_');
}

/**
 * Mover archivo subido a carpeta del tenant.
 * @param string $fieldName Nombre del input file
 * @param string $subdir 'images' | 'docs' | 'temp'
 * @param array $allowedExts Lista de extensiones permitidas (sin punto)
 * @param int $maxSizeMb Tamaño máximo en MB
 * @return array [success, message, path, url, name]
 */
function move_uploaded_file_tenant($fieldName, $subdir = 'images', $allowedExts = ['jpg','jpeg','png','gif','webp'], $maxSizeMb = 10) {
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Archivo no subido'];
    }

    $file = $_FILES[$fieldName];
    $sizeLimit = $maxSizeMb * 1024 * 1024;
    if ($file['size'] > $sizeLimit) {
        return ['success' => false, 'message' => 'El archivo excede el tamaño máximo permitido'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        return ['success' => false, 'message' => 'Extensión no permitida'];
    }

    $tid      = defined('TENANT_ID') ? TENANT_ID : 0;
    $safeName = uniqid('upl_') . '_' . sanitize_filename(pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;
    $folder   = 'tenants/' . $tid . '/' . $subdir;

    $upload = uploadToCloudinary($file['tmp_name'], $folder);

    if (!$upload['success']) {
        return ['success' => false, 'message' => $upload['message']];
    }

    return [
        'success'   => true,
        'message'   => 'Archivo subido a Cloudinary',
        'path'      => '',                  // sin ruta local
        'url'       => $upload['url'],
        'name'      => $upload['public_id'],
        'relPath'   => $upload['url'],      // se guarda la URL completa en BD
    ];
