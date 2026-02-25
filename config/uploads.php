<?php
/**
 * Helpers de uploads por tenant
 */

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

    $safeName = uniqid('upl_') . '_' . sanitize_filename(pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;

    $baseDir = tenant_upload_base_dir();
    ensure_upload_dirs_for_tenant(defined('TENANT_ID') ? TENANT_ID : 0);

    $targetDir = rtrim($baseDir, '/\\') . '/' . $subdir;
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0775, true);
    }

    $targetPath = rtrim($targetDir, '/\\') . '/' . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'message' => 'No fue posible guardar el archivo'];
    }

    $url = tenant_upload_base_url() . '/' . $subdir . '/' . $safeName;
    $relPath = 'public/tenants/' . (defined('TENANT_ID') ? TENANT_ID : 0) . '/' . $subdir . '/' . $safeName;

    return [
        'success' => true,
        'message' => 'Archivo subido',
        'path' => $targetPath,
        'url' => $url,
        'name' => $safeName,
        'relPath' => $relPath
    ];
}
