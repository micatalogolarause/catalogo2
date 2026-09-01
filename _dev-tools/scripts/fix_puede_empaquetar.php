<?php
// Script para corregir $puede_empaquetar

$file = 'c:/xampp/htdocs/catalogo2/app/views/admin/pedidos.php';
$content = file_get_contents($file);

// Buscar y reemplazar la lógica
$old = '                    // Permitir empaquetar si está en en_pedido, alistado o empaquetado Y hay al menos un producto listo
                    $puede_empaquetar = in_array($pedido[\'estado\'], [\'en_pedido\', \'alistado\', \'empaquetado\']) && $productos_listos > 0;';

$new = '                    // Permitir empaquetar si está en en_pedido, alistado o empaquetado Y hay al menos un producto listo
                    // O si ya está empaquetado/verificado/en_reparto (para poder desempaquetar)
                    $puede_empaquetar = (in_array($pedido[\'estado\'], [\'en_pedido\', \'alistado\', \'empaquetado\']) && $productos_listos > 0) 
                                        || in_array($pedido[\'estado\'], [\'empaquetado\', \'verificado\', \'en_reparto\']);';

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
    echo "✅ Lógica de puede_empaquetar actualizada correctamente\n";
} else {
    echo "❌ No se encontró el texto exacto\n";
}
?>
