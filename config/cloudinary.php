<?php
/**
 * Cloudinary - Configuración y helpers de subida de imágenes
 * Cloud: dc6in819o
 */

function cloudinaryEnv(string $key, string $fallback): string {
    $value = getenv($key);
    if ($value === false || $value === null) {
        return $fallback;
    }

    $value = trim((string)$value);
    $value = trim($value, "\"'");
    $value = preg_replace('/[\x00-\x1F\x7F\x{00A0}\x{200B}-\x{200D}\x{FEFF}]/u', '', $value);

    return $value !== '' ? $value : $fallback;
}

// Credenciales Cloudinary
// En Vercel se leen desde variables de entorno; en local usan valores directos como fallback
define('CLOUDINARY_FALLBACK_CLOUD_NAME', 'dc6in819o');
define('CLOUDINARY_FALLBACK_API_KEY', '216774864662758');
define('CLOUDINARY_FALLBACK_API_SECRET', 'XxQYYgHIBCe-muQDeWXw3IEw9P0');

define('CLOUDINARY_CLOUD_NAME', cloudinaryEnv('CLOUDINARY_CLOUD_NAME', CLOUDINARY_FALLBACK_CLOUD_NAME));
define('CLOUDINARY_API_KEY', cloudinaryEnv('CLOUDINARY_API_KEY', CLOUDINARY_FALLBACK_API_KEY));
define('CLOUDINARY_API_SECRET', cloudinaryEnv('CLOUDINARY_API_SECRET', CLOUDINARY_FALLBACK_API_SECRET));
define('CLOUDINARY_UPLOAD_PRESET', cloudinaryEnv('CLOUDINARY_UPLOAD_PRESET', ''));

function cloudinaryUnsignedUploadRequest(string $tmpPath, string $folder, ?string $publicId = null, string $preset = ''): array {
    $uploadPreset = trim($preset) !== '' ? trim($preset) : CLOUDINARY_UPLOAD_PRESET;
    if ($uploadPreset === '') {
        return ['success' => false, 'message' => 'Upload preset no configurado', 'url' => '', 'public_id' => '', 'http_code' => 0];
    }

    $postFields = [
        'upload_preset' => $uploadPreset,
        'folder' => $folder,
        'file' => new CURLFile($tmpPath),
    ];

    if (!empty($publicId)) {
        $postFields['public_id'] = $publicId;
    }

    $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        return ['success' => false, 'message' => 'Error cURL: ' . $curlErr, 'url' => '', 'public_id' => '', 'http_code' => 0];
    }

    $data = json_decode($response, true);
    if ($httpCode === 200 && isset($data['secure_url'])) {
        return [
            'success' => true,
            'message' => 'Subido correctamente',
            'url' => $data['secure_url'],
            'public_id' => $data['public_id'] ?? '',
            'http_code' => 200,
        ];
    }

    $errorMsg = $data['error']['message'] ?? "HTTP {$httpCode}";
    return ['success' => false, 'message' => 'Cloudinary unsigned error: ' . $errorMsg, 'url' => '', 'public_id' => '', 'http_code' => $httpCode];
}

function cloudinaryUploadRequest(string $tmpPath, array $params, string $cloudName, string $apiKey, string $apiSecret): array {
    ksort($params);
    $signParts = [];
    foreach ($params as $key => $value) {
        if ($value === null || $value === '') {
            continue;
        }
        $signParts[] = $key . '=' . $value;
    }
    $toSign = implode('&', $signParts);
    $signature = sha1($toSign . $apiSecret);

    $postFields = $params;
    $postFields['api_key'] = $apiKey;
    $postFields['signature'] = $signature;
    $postFields['file'] = new CURLFile($tmpPath);

    $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        return ['success' => false, 'message' => 'Error cURL: ' . $curlErr, 'url' => '', 'public_id' => '', 'http_code' => 0];
    }

    $data = json_decode($response, true);
    if ($httpCode === 200 && isset($data['secure_url'])) {
        return [
            'success' => true,
            'message' => 'Subido correctamente',
            'url' => $data['secure_url'],
            'public_id' => $data['public_id'],
            'http_code' => 200,
        ];
    }

    $errorMsg = $data['error']['message'] ?? "HTTP {$httpCode}";
    return ['success' => false, 'message' => 'Cloudinary error: ' . $errorMsg, 'url' => '', 'public_id' => '', 'http_code' => $httpCode];
}

/**
 * Sube un archivo al Cloudinary y retorna la URL segura.
 *
 * @param string      $tmpPath   Ruta temporal del archivo ($_FILES[...]['tmp_name'])
 * @param string      $folder    Carpeta/prefijo dentro de Cloudinary, ej. "tenants/3/images"
 * @param string|null $publicId  Public ID personalizado (sin extensión). Si null, Cloudinary genera uno.
 * @return array ['success', 'url', 'public_id', 'message']
 */
function uploadToCloudinary(string $tmpPath, string $folder = 'uploads', ?string $publicId = null): array {
    $cloudName  = CLOUDINARY_CLOUD_NAME;
    $apiKey     = CLOUDINARY_API_KEY;
    $apiSecret  = CLOUDINARY_API_SECRET;

    // 0) Si existe upload preset configurado, intentar subida unsigned (no requiere firma)
    if (CLOUDINARY_UPLOAD_PRESET !== '') {
        $unsigned = cloudinaryUnsignedUploadRequest($tmpPath, $folder, $publicId, CLOUDINARY_UPLOAD_PRESET);
        if ($unsigned['success']) {
            return $unsigned;
        }
        // El preset falló, continuar con flujo firmado como fallback
    }

    // 1) Intentar con SDK oficial de Cloudinary (firma interna)
    if (class_exists('\\Cloudinary\\Cloudinary')) {
        try {
            $cloudinary = new \Cloudinary\Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key' => $apiKey,
                    'api_secret' => $apiSecret,
                ],
                'url' => [
                    'secure' => true,
                ],
            ]);

            $options = [
                'folder' => $folder,
                'resource_type' => 'image',
            ];

            if ($publicId !== null && $publicId !== '') {
                $options['public_id'] = $publicId;
                $options['overwrite'] = true;
            }

            $result = $cloudinary->uploadApi()->upload($tmpPath, $options);

            if (!empty($result['secure_url'])) {
                return [
                    'success'   => true,
                    'message'   => 'Subido correctamente',
                    'url'       => $result['secure_url'],
                    'public_id' => $result['public_id'] ?? '',
                    'http_code' => 200,
                ];
            }
        } catch (\Throwable $e) {
            // Continuar con fallback cURL firmado
        }
    }

    // Parámetros de la solicitud
    $timestamp = time();
    $params    = [
        'folder'    => $folder,
        'timestamp' => $timestamp,
    ];
    if ($publicId !== null) {
        $params['public_id'] = $publicId;
    }

    $result = cloudinaryUploadRequest($tmpPath, $params, $cloudName, $apiKey, $apiSecret);

    if ($result['success']) {
        return $result;
    }

    $isInvalidSignature = stripos($result['message'], 'Invalid Signature') !== false;
    $usingFallback = ($cloudName === CLOUDINARY_FALLBACK_CLOUD_NAME)
        && ($apiKey === CLOUDINARY_FALLBACK_API_KEY)
        && ($apiSecret === CLOUDINARY_FALLBACK_API_SECRET);

    if ($isInvalidSignature && !$usingFallback) {
        $retry = cloudinaryUploadRequest(
            $tmpPath,
            $params,
            CLOUDINARY_FALLBACK_CLOUD_NAME,
            CLOUDINARY_FALLBACK_API_KEY,
            CLOUDINARY_FALLBACK_API_SECRET
        );

        if ($retry['success']) {
            return $retry;
        }
    }

    return $result;
}

/**
 * Elimina una imagen de Cloudinary por su public_id.
 * Solo se usa si quieres borrar imágenes viejas al reemplazarlas.
 *
 * @param string $publicId  El public_id devuelto por uploadToCloudinary()
 * @return bool
 */
function deleteFromCloudinary(string $publicId): bool {
    $cloudName = CLOUDINARY_CLOUD_NAME;
    $apiKey    = CLOUDINARY_API_KEY;
    $apiSecret = CLOUDINARY_API_SECRET;

    $timestamp = time();
    $toSign    = "public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
    $signature = sha1($toSign);

    $postFields = [
        'public_id' => $publicId,
        'timestamp' => $timestamp,
        'api_key'   => $apiKey,
        'signature' => $signature,
    ];

    $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $postFields);
    curl_setopt($ch, CURLOPT_TIMEOUT,        30);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return isset($data['result']) && $data['result'] === 'ok';
}
