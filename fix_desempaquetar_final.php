<?php
// Script para hacer el disabled más explícito

$file = 'c:/xampp/htdocs/catalogo2/app/views/admin/pedidos.php';
$content = file_get_contents($file);

// Buscar y reemplazar la lógica del disabled
$old = <<<'PHP'
                        <?php 
                            // Botón activo si: (1) es empaquetado/verificado/en_reparto O (2) puede marcar empaquetado
                            $desactivar_boton = !in_array($pedido['estado'], ['empaquetado', 'verificado', 'en_reparto']) && !$puede_marcar_empaquetado;
                            echo $desactivar_boton ? 'disabled' : ''; 
                        ?>>
PHP;

$new = <<<'PHP'
                        <?php 
                            // Si es empaquetado, verificado o en_reparto → SIEMPRE ACTIVO (para desempaquetar)
                            // Si NO es empaquetado → activo solo si puede marcar empaquetado
                            if (in_array($pedido['estado'], ['empaquetado', 'verificado', 'en_reparto'])) {
                                // Desempaquetar siempre activo
                                echo '';
                            } else {
                                // Marcar empaquetado solo si tiene productos listos
                                echo !$puede_marcar_empaquetado ? 'disabled' : '';
                            }
                        ?>>
PHP;

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
    echo "✅ Lógica corregida para desempaquetar siempre activo\n";
} else {
    echo "❌ No se encontró el texto exacto\n";
}
?>
