<?php
$regiones = ['us-east-1','us-west-1','eu-west-1','eu-central-1','ap-southeast-1','sa-east-1'];
$user = 'postgres.ebpvdrwptoqiainuvztx';
$pass = 'Larause820626';

foreach($regiones as $r) {
    $host = "aws-0-{$r}.pooler.supabase.com";
    $dsn  = "pgsql:host={$host};port=6543;dbname=postgres";
    try {
        $p = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $row = $p->query("SELECT COUNT(*) as n FROM tenants")->fetch();
        echo "✓ CONECTADO: {$host}\n";
        echo "  Tenants en BD: {$row['n']}\n";
        break;
    } catch(Exception $e) {
        $msg = substr($e->getMessage(), 0, 80);
        echo "✗ {$r}: {$msg}\n";
    }
}
