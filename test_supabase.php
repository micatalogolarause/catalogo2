<?php
define('DB_HOST', 'aws-0-us-west-2.pooler.supabase.com');
define('DB_PORT', '5432');
define('DB_USER', 'postgres.ebpvdrwptoqiainuvztx');
define('DB_PASS', 'Larause820626');
define('DB_NAME', 'postgres');

$dsn = 'pgsql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME;
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $tenants = $pdo->query('SELECT id, nombre, slug FROM tenants ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    echo "CONEXION OK - Tenants: " . count($tenants) . "\n";
    foreach($tenants as $t) {
        echo "  [{$t['id']}] {$t['nombre']} ({$t['slug']})\n";
    }
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
