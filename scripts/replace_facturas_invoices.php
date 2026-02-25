<?php
$dir = __DIR__ . '/../public/invoices';
$files = glob($dir . '/*.html');
$replacements = [
    'Factura' => 'Cuenta de Cobro',
    'factura' => 'cuenta de cobro',
    'FACTURA' => 'CUENTA DE COBRO',
    'Esta factura fue generada automáticamente.' => 'Esta cuenta de cobro fue generada automáticamente.'
];
$updated = 0;
foreach ($files as $file) {
    $content = file_get_contents($file);
    $new = str_replace(array_keys($replacements), array_values($replacements), $content);
    if ($new !== $content) {
        file_put_contents($file, $new);
        echo "Updated: " . basename($file) . "\n";
        $updated++;
    } else {
        echo "No change: " . basename($file) . "\n";
    }
}
echo "Done. Files updated: $updated\n";
