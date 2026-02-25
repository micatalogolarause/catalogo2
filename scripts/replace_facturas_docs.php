<?php
$dir = __DIR__ . '/../';
$files = [];
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    if (!in_array($ext, ['md', 'txt', 'html'])) continue;
    // skip invoices (already handled)
    if (strpos($path, DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'invoices' . DIRECTORY_SEPARATOR) !== false) continue;
    $files[] = $path;
}

$replacements = [
    'Factura' => 'Cuenta de Cobro',
    'factura' => 'cuenta de cobro',
    'FACTURA' => 'CUENTA DE COBRO',
    'facturas' => 'cuentas de cobro',
    'Facturas' => 'Cuentas de Cobro'
];

$updated = 0;
foreach ($files as $file) {
    $content = file_get_contents($file);
    // skip files that mention code filenames to avoid breaking references
    if (preg_match('/FacturaPDF|FacturaExcel|FacturaPDF.php|FacturaExcel.php|`Factura|`factura/', $content)) {
        echo "Skipped (code refs): " . substr($file, strlen(__DIR__)+1) . "\n";
        continue;
    }
    $new = str_replace(array_keys($replacements), array_values($replacements), $content);
    if ($new !== $content) {
        file_put_contents($file, $new);
        echo "Updated: " . substr($file, strlen(__DIR__)+1) . "\n";
        $updated++;
    } else {
        echo "No change: " . substr($file, strlen(__DIR__)+1) . "\n";
    }
}

echo "Done. Docs updated: $updated\n";
