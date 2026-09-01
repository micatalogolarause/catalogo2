<?php
// Script para simplificar la lógica del disabled

$file = 'c:/xampp/htdocs/catalogo2/app/views/admin/pedidos.php';
$content = file_get_contents($file);

// Buscar y reemplazar
$old = '                        <?php echo ($pedido[\'estado\'] !== \'empaquetado\' && !$puede_marcar_empaquetado) ? \'disabled\' : \'\'; ?>>';

$new = '                        <?php 
                            // Botón activo si: (1) es empaquetado/verificado/en_reparto O (2) puede marcar empaquetado
                            $desactivar_boton = !in_array($pedido[\'estado\'], [\'empaquetado\', \'verificado\', \'en_reparto\']) && !$puede_marcar_empaquetado;
                            echo $desactivar_boton ? \'disabled\' : \'\'; 
                        ?>>';

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
    echo "✅ Lógica del disabled simplificada\n";
} else {
    echo "❌ No se encontró el texto exacto\n";
    echo "Buscando: " . $old . "\n";
}
?>
