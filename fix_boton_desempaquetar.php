<?php
// Script para corregir la lógica del botón

$file = 'c:/xampp/htdocs/catalogo2/app/views/admin/pedidos.php';
$content = file_get_contents($file);

// Buscar y reemplazar la lógica
$old = <<<'PHP'
                    // Permitir empaquetar si está en en_pedido, alistado o empaquetado Y hay al menos un producto listo
                    // O si ya está empaquetado/verificado/en_reparto (para poder desempaquetar)
                    $puede_empaquetar = (in_array($pedido['estado'], ['en_pedido', 'alistado', 'empaquetado']) && $productos_listos > 0) 
                                        || in_array($pedido['estado'], ['empaquetado', 'verificado', 'en_reparto']);
PHP;

$new = <<<'PHP'
                    // Permitir Marcar Empaquetado si está en en_pedido, alistado o empaquetado Y hay al menos un producto listo
                    $puede_marcar_empaquetado = in_array($pedido['estado'], ['en_pedido', 'alistado', 'empaquetado']) && $productos_listos > 0;
                    
                    // Permitir Desempaquetar si está en empaquetado, verificado o en_reparto (siempre activo)
                    $puede_desempaquetar = in_array($pedido['estado'], ['empaquetado', 'verificado', 'en_reparto']);
                    
                    // Variable para usar en el botón
                    $puede_empaquetar = $pedido['estado'] === 'empaquetado' ? $puede_desempaquetar : $puede_marcar_empaquetado;
PHP;

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
    echo "✅ Lógica del botón corregida correctamente\n";
} else {
    echo "❌ No se encontró el texto exacto\n";
}
?>
