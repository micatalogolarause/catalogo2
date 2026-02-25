<?php
echo "<h2>Verificación de extensión GD</h2>";

if (extension_loaded('gd')) {
    echo "<p style='color: green;'><strong>✓ GD está HABILITADO</strong></p>";
    echo "<h3>Información de GD:</h3>";
    echo "<pre>";
    print_r(gd_info());
    echo "</pre>";
    
    echo "<h3>Formatos soportados:</h3>";
    echo "<ul>";
    if (function_exists('imagecreatefromjpeg')) echo "<li>✓ JPEG</li>";
    if (function_exists('imagecreatefrompng')) echo "<li>✓ PNG</li>";
    if (function_exists('imagecreatefromgif')) echo "<li>✓ GIF</li>";
    if (function_exists('imagecreatefromwebp')) echo "<li>✓ WEBP</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red;'><strong>✗ GD NO está habilitado</strong></p>";
    echo "<p>Debes habilitar la extensión GD en php.ini y reiniciar Apache.</p>";
}

echo "<h3>Extensiones cargadas:</h3>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";
?>
