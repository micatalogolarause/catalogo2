<?php
/**
 * Generador de imágenes de prueba
 * Crea imágenes placeholder para productos
 */

$imagesDir = dirname(dirname(__FILE__)) . '/public/images/productos';

// Crear directorio si no existe
if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0755, true);
}

// Lista de imágenes a crear
$imagenes = array(
    'iphone15.jpg' => array('color' => '#007AFF', 'text' => 'iPhone 15 Pro'),
    'samsung_s24.jpg' => array('color' => '#1428A0', 'text' => 'Samsung S24'),
    'macbook_pro.jpg' => array('color' => '#555555', 'text' => 'MacBook Pro'),
    'dell_xps.jpg' => array('color' => '#0078D4', 'text' => 'Dell XPS'),
    'camiseta_hombre.jpg' => array('color' => '#FF6B35', 'text' => 'Camiseta'),
    'pantalon_hombre.jpg' => array('color' => '#3A3F47', 'text' => 'Pantalón'),
    'vestido_mujer.jpg' => array('color' => '#FF1493', 'text' => 'Vestido'),
    'jeans_mujer.jpg' => array('color' => '#1E90FF', 'text' => 'Jeans'),
    'horno_electrico.jpg' => array('color' => '#FFD700', 'text' => 'Horno'),
    'juego_cama.jpg' => array('color' => '#FFFACD', 'text' => 'Juego Cama')
);

foreach ($imagenes as $nombreArchivo => $config) {
    $rutaArchivo = $imagesDir . '/' . $nombreArchivo;
    
    // No sobrescribir si ya existe
    if (!file_exists($rutaArchivo)) {
        generarImagenPlaceholder($rutaArchivo, $config['color'], $config['text']);
    }
}

/**
 * Generar imagen placeholder con GD
 */
function generarImagenPlaceholder($ruta, $colorHex, $texto) {
    // Crear imagen
    $imagen = imagecreatetruecolor(400, 400);
    
    // Convertir color hex a RGB
    $color = hexToRgb($colorHex);
    $colorFondo = imagecolorallocate($imagen, $color['r'], $color['g'], $color['b']);
    $colorTexto = imagecolorallocate($imagen, 255, 255, 255);
    
    // Llenar fondo
    imagefill($imagen, 0, 0, $colorFondo);
    
    // Agregar texto
    $fuente = 5;
    $anchoTexto = imagefontwidth($fuente) * strlen($texto);
    $altoTexto = imagefontheight($fuente);
    $x = (400 - $anchoTexto) / 2;
    $y = (400 - $altoTexto) / 2;
    
    imagestring($imagen, $fuente, $x, $y, $texto, $colorTexto);
    
    // Guardar imagen
    imagejpeg($imagen, $ruta, 90);
    imagedestroy($imagen);
}

/**
 * Convertir color HEX a RGB
 */
function hexToRgb($hex) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return array('r' => $r, 'g' => $g, 'b' => $b);
}

return true;
?>
