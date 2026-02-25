<?php
/**
 * Crear imagen placeholder para productos sin imagen
 */

// Crear imagen de 400x400
$width = 400;
$height = 400;
$image = imagecreatetruecolor($width, $height);

// Colores
$bg_color = imagecolorallocate($image, 240, 240, 240); // Gris claro
$text_color = imagecolorallocate($image, 150, 150, 150); // Gris medio

// Llenar fondo
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Texto
$text = 'Sin Imagen';
$font_size = 24;

// Calcular posición centrada (aproximada)
$text_x = 120;
$text_y = 210;

// Dibujar texto
imagestring($image, 5, $text_x, $text_y, $text, $text_color);

// Guardar imagen
$output_path = __DIR__ . '/../public/images/no-image.jpg';
imagejpeg($image, $output_path, 90);
imagedestroy($image);

echo "✓ Imagen placeholder creada: no-image.jpg\n";
echo "Ruta: $output_path\n";
?>
