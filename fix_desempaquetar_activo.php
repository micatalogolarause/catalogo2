<?php
// Script para que el botón Desempaquetar siempre esté activo

$file = 'c:/xampp/htdocs/catalogo2/app/views/admin/pedidos.php';
$content = file_get_contents($file);

// Buscar y reemplazar el disabled del botón
$old = <<<'PHP'
                <button type="button" class="btn btn-lg btn-empaquetado-<?php echo $pedido['id']; ?>" 
                        style="flex: 1; max-width: 280px; background-color: #28a745; color: white; border: none; padding: 0.8rem 1.5rem; font-size: 0.95rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);"
                        onclick="<?php echo $pedido['estado'] === 'empaquetado' ? 'desempaquetar(' . $pedido['id'] . ')' : 'marcarEmpaquetadoLista(' . $pedido['id'] . ')'; ?>"
                        <?php echo !$puede_empaquetar ? 'disabled' : ''; ?>>
PHP;

$new = <<<'PHP'
                <button type="button" class="btn btn-lg btn-empaquetado-<?php echo $pedido['id']; ?>" 
                        style="flex: 1; max-width: 280px; background-color: #28a745; color: white; border: none; padding: 0.8rem 1.5rem; font-size: 0.95rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);"
                        onclick="<?php echo $pedido['estado'] === 'empaquetado' ? 'desempaquetar(' . $pedido['id'] . ')' : 'marcarEmpaquetadoLista(' . $pedido['id'] . ')'; ?>"
                        <?php echo ($pedido['estado'] !== 'empaquetado' && !$puede_marcar_empaquetado) ? 'disabled' : ''; ?>>
PHP;

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
    echo "✅ Botón Desempaquetar siempre activo\n";
} else {
    echo "❌ No se encontró el texto exacto\n";
}
?>
