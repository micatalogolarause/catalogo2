<?php
/**
 * Script para renombrar "Factura" a "Cuenta de Cobro"
 */

$base_path = dirname(__FILE__);

$replacements = [
    'Gestión de Facturas' => 'Gestión de Cuentas de Cobro',
    'No. Factura' => 'No. Cuenta de Cobro',
    'Factura o Nombre' => 'Cuenta de Cobro o Nombre',
    'Factura #' => 'Cuenta de Cobro #',
    'Descargar Factura' => 'Descargar Cuenta de Cobro',
    'factura_' => 'cuenta_cobro_',
    'Generador de Facturas' => 'Generador de Cuentas de Cobro',
];

$files = [
    'app/views/admin/facturas.php',
    'app/views/admin/ver_pedido.php',
    'app/views/admin/layout/header.php',
    'app/helpers/FacturaPDF.php',
    'app/helpers/FacturaExcel.php',
    'app/controllers/adminController.php',
];

$updated = 0;
$failed = 0;

foreach ($files as $file) {
    $full_path = $base_path . DIRECTORY_SEPARATOR . $file;
    
    if (!file_exists($full_path)) {
        echo "Warning: Archivo no encontrado: $file\n";
        $failed++;
        continue;
    }
    
    $content = file_get_contents($full_path);
    $original = $content;
    
    foreach ($replacements as $old => $new) {
        $content = str_replace($old, $new, $content);
    }
    
    if ($content !== $original) {
        if (file_put_contents($full_path, $content)) {
            echo "OK: Actualizado: $file\n";
            $updated++;
        } else {
            echo "ERROR al escribir: $file\n";
            $failed++;
        }
    } else {
        echo "Sin cambios: $file\n";
    }
}

echo "\nResumen: $updated archivos actualizados, $failed errores\n";
?>
