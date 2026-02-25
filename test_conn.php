<?php
// Intentar conexión directa resolviendo IPv4
$project = 'ebpvdrwptoqiainuvztx';
$pass    = 'Larause820626';

// Opción 1: Directo puerto 5432
$hosts = [
    ["db.{$project}.supabase.co",          5432, "postgres"],
    ["aws-0-us-east-1.pooler.supabase.com", 6543, "postgres.{$project}"],
    ["aws-0-us-east-1.pooler.supabase.com", 5432, "postgres.{$project}"],
    ["aws-0-us-west-1.pooler.supabase.com", 6543, "postgres.{$project}"],
    ["aws-0-eu-central-1.pooler.supabase.com", 6543, "postgres.{$project}"],
];

foreach($hosts as [$host, $port, $user]) {
    $dsn = "pgsql:host={$host};port={$port};dbname=postgres;connect_timeout=5";
    try {
        $p = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "✓ OK: {$host}:{$port} user={$user}\n";
        $r = $p->query("SELECT COUNT(*) as n FROM tenants")->fetch();
        echo "  Tenants: {$r['n']}\n";
        // Guardar config que funciona
        file_put_contents(__DIR__.'/config_ok.txt', "HOST={$host}\nPORT={$port}\nUSER={$user}\n");
        break;
    } catch(Exception $e) {
        echo "✗ {$host}:{$port} -> " . substr($e->getMessage(), 0, 70) . "\n";
    }
}
