<?php
/**
 * Script para renombrar "Factura" a "Cuenta de Cobro" en todo el sistema
 * Nota: No renombra archivos, solo actualiza referencias y etiquetas
 */

$files_to_update = [
    'app/views/admin/layout/header.php',
    'app/views/admin/facturas.php',
    'app/views/admin/ver_pedido.php',
    'app/helpers/FacturaPDF.php',
    'app/helpers/FacturaExcel.php',
];

$replacements = [
    // Etiquetas de usuario
    'Gestión de Facturas' => 'Gestión de Cuentas de Cobro',
    'No. Factura' => 'No. Cuenta de Cobro',
    '<i class="bi bi-receipt"></i> Facturas' => '<i class="bi bi-receipt"></i> Cuentas de Cobro',
    'Factura #' => 'Cuenta de Cobro #',
    'Factura PDF' => 'Cuenta de Cobro PDF',
    'Factura Excel' => 'Cuenta de Cobro Excel',
    'Descargar Factura' => 'Descargar Cuenta de Cobro',
    
    // Comentarios en código
    'Generador de Facturas' => 'Generador de Cuentas de Cobro',
    'Generar factura' => 'Generar cuenta de cobro',
    'generarFacturaPDF' => 'generarCuentaCobroPDF',
    'generarFacturaExcel' => 'generarCuentaCobro Excel',
    'factura_' => 'cuenta_cobro_',
    'FacturaPDF::generar' => 'CuentaCobroPDF::generar',
];

$errors = [];
$updated_count = 0;

foreach ($files_to_update as $file) {
    $filepath = __DIR__ . '/' . $file;
    
    if (!file_exists($filepath)) {
        $errors[] = "Archivo no encontrado: $file";
        continue;
    }
    
    $content = file_get_contents($filepath);
    $original_content = $content;
    
    foreach ($replacements as $buscar => $reemplazar) {
        $content = str_replace($buscar, $reemplazar, $content);
    }
    
    if ($content !== $original_content) {
        if (file_put_contents($filepath, $content)) {
            $updated_count++;
            echo "✓ Actualizado: $file\n";
        } else {
            $errors[] = "No se pudo escribir en: $file";
        }
    } else {
        echo "- Sin cambios: $file\n";
    }
}

if (!empty($errors)) {
    echo "\n⚠️ Errores:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

echo "\n✅ Actualización completada. Archivos modificados: $updated_count\n";
?>
