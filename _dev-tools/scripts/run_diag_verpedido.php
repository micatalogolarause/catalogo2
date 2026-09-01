<?php
// Usage: php run_diag_verpedido.php 63 mauricio
$id = $argv[1] ?? null;
$tenant = $argv[2] ?? 'mauricio';
if (!$id) {
    echo "Falta ID de pedido\n";
    exit(1);
}
$_GET['id'] = $id;
$_GET['tenant'] = $tenant;
include 'diagnostico_verpedido.php';
