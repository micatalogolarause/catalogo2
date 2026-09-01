<?php
/**
 * Diagnóstico Cloudinary — sólo para uso temporal, eliminar después
 * Accede a: https://catalogo2-khaki.vercel.app/test_cloudinary_diag.php
 */

// Seguridad básica
if (!isset($_GET['run'])) {
    echo '<h2>Diagnóstico Cloudinary</h2>';
    echo '<p>Añade <code>?run=1</code> a la URL para ejecutar el test.</p>';
    echo '<p><a href="?run=1">Ejecutar ahora</a></p>';
    exit;
}

// Leer credenciales del entorno (igual que hace cloudinary.php)
function readEnvClean(string $key): string {
    $v = getenv($key);
    if ($v === false) return '(no definida)';
    $v = trim($v);
    $v = trim($v, "\"'");
    $v = preg_replace('/[\x00-\x1F\x7F\x{00A0}\x{200B}-\x{200D}\x{FEFF}]/u', '', $v);
    return $v;
}

$cloudName = readEnvClean('CLOUDINARY_CLOUD_NAME');
$apiKey    = readEnvClean('CLOUDINARY_API_KEY');
$apiSecret = readEnvClean('CLOUDINARY_API_SECRET');
$preset    = readEnvClean('CLOUDINARY_UPLOAD_PRESET');

echo "<h2>Diagnóstico Cloudinary</h2>";
echo "<h3>Variables de entorno</h3><pre>";
echo "CLOUDINARY_CLOUD_NAME    = " . htmlspecialchars($cloudName) . "\n";
echo "CLOUDINARY_API_KEY       = " . htmlspecialchars($apiKey) . "\n";
echo "CLOUDINARY_API_SECRET    = " . htmlspecialchars($apiSecret) . "  (len=" . strlen($apiSecret) . ")\n";
echo "CLOUDINARY_UPLOAD_PRESET = " . htmlspecialchars($preset) . "\n";
echo "</pre>";

// Test 1: Unsigned upload con preset
echo "<h3>Test 1: Upload unsigned con preset '$preset'</h3>";
if ($preset === '' || $preset === '(no definida)') {
    echo "<p style='color:orange'>⚠️ CLOUDINARY_UPLOAD_PRESET no configurada — saltando test unsigned.</p>";
} else {
    // Crear imagen de prueba en memoria
    $tmpFile = tempnam(sys_get_temp_dir(), 'cld_test_');
    $img = imagecreatetruecolor(100, 100);
    $color = imagecolorallocate($img, 100, 150, 200);
    imagefill($img, 0, 0, $color);
    imagepng($img, $tmpFile);
    imagedestroy($img);

    $postFields = [
        'upload_preset' => $preset,
        'folder' => 'test_diag',
        'file' => new CURLFile($tmpFile),
    ];

    $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    @unlink($tmpFile);

    if ($err) {
        echo "<p style='color:red'>❌ cURL error: " . htmlspecialchars($err) . "</p>";
    } else {
        $data = json_decode($res, true);
        if ($code === 200 && isset($data['secure_url'])) {
            echo "<p style='color:green'>✅ Upload unsigned OK!</p>";
            echo "<p>URL: <a href='" . htmlspecialchars($data['secure_url']) . "' target='_blank'>" . htmlspecialchars($data['secure_url']) . "</a></p>";
        } else {
            $msg = $data['error']['message'] ?? "HTTP $code — $res";
            echo "<p style='color:red'>❌ Error: " . htmlspecialchars($msg) . "</p>";
        }
    }
}

// Test 2: Upload firmado
echo "<h3>Test 2: Upload firmado (signed)</h3>";
if ($apiSecret === '' || $apiSecret === '(no definida)') {
    echo "<p style='color:orange'>⚠️ API Secret no disponible.</p>";
} else {
    $tmpFile = tempnam(sys_get_temp_dir(), 'cld_test_');
    $img = imagecreatetruecolor(100, 100);
    $c2 = imagecolorallocate($img, 200, 100, 100);
    imagefill($img, 0, 0, $c2);
    imagepng($img, $tmpFile);
    imagedestroy($img);

    $timestamp = time();
    $folder    = 'test_diag';
    $toSign    = "folder={$folder}&timestamp={$timestamp}";
    $signature = sha1($toSign . $apiSecret);

    echo "<pre>String to sign : " . htmlspecialchars($toSign) . "\n";
    echo "API Secret (len): " . strlen($apiSecret) . "\n";
    echo "Signature       : " . $signature . "</pre>";

    $postFields = [
        'api_key'   => $apiKey,
        'timestamp' => $timestamp,
        'folder'    => $folder,
        'signature' => $signature,
        'file'      => new CURLFile($tmpFile),
    ];

    $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    @unlink($tmpFile);

    if ($err) {
        echo "<p style='color:red'>❌ cURL error: " . htmlspecialchars($err) . "</p>";
    } else {
        $data = json_decode($res, true);
        if ($code === 200 && isset($data['secure_url'])) {
            echo "<p style='color:green'>✅ Upload firmado OK!</p>";
            echo "<p>URL: <a href='" . htmlspecialchars($data['secure_url']) . "' target='_blank'>" . htmlspecialchars($data['secure_url']) . "</a></p>";
        } else {
            $msg = $data['error']['message'] ?? "HTTP $code — $res";
            echo "<p style='color:red'>❌ Error: " . htmlspecialchars($msg) . "</p>";
        }
    }
}

echo "<hr><p><strong>¿Qué hacer según los resultados?</strong></p>";
echo "<ul>";
echo "<li>✅ Test 1 OK → Upload unsigned funciona, no hay que tocar más nada.</li>";
echo "<li>❌ Test 1 'Upload preset not found' → El preset '$preset' no existe en tu Cloudinary. Ve a <a href='https://cloudinary.com/console/settings/upload' target='_blank'>Cloudinary → Settings → Upload Presets</a> y crea uno en modo Unsigned con nombre <strong>catalogo_upl</strong>. Luego actualiza la variable CLOUDINARY_UPLOAD_PRESET en Vercel.</li>";
echo "<li>❌ Test 2 'Invalid Signature' → El API Secret en Vercel es incorrecto. Ve a <a href='https://cloudinary.com/console/settings/api-keys' target='_blank'>Cloudinary → API Keys</a>, copia el secret correcto y actualiza CLOUDINARY_API_SECRET en Vercel.</li>";
echo "<li>✅ Test 2 OK pero Test 1 falla → Elimina CLOUDINARY_UPLOAD_PRESET de Vercel para que use el flujo firmado.</li>";
echo "</ul>";
