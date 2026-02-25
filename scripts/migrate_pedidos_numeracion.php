<?php
/**
 * Migración: Agregar numero_pedido secuencial por tenant
 * Cada tenant tendrá su propia numeración comenzando desde 1
 */

include 'config/database.php';

echo "=== MIGRANDO PEDIDOS A NUMERACIÓN POR TENANT ===\n\n";

// Modo dry-run: solo muestra cambios sin escribir en BD (ejecutar con --dry-run)
$dryRun = (isset($argv) && in_array('--dry-run', $argv));
if ($dryRun) {
    echo "[DRY-RUN] No se realizarán cambios en la base de datos.\n\n";
}

// 1. Agregar columna numero_pedido si no existe
$sql_check = "SHOW COLUMNS FROM pedidos LIKE 'numero_pedido'";
$result = $GLOBALS['conn']->query($sql_check);

if ($result->num_rows == 0) {
    echo "Agregando columna numero_pedido...\n";
    $sql_alter = "ALTER TABLE pedidos ADD COLUMN numero_pedido INT NOT NULL DEFAULT 0 AFTER id";
    if ($GLOBALS['conn']->query($sql_alter)) {
        echo "✓ Columna numero_pedido agregada\n";
    } else {
        echo "✗ Error al agregar columna: " . $GLOBALS['conn']->error . "\n";
        exit;
    }
} else {
    echo "✓ Columna numero_pedido ya existe\n";
}

// 2. Obtener lista de tenants
echo "\nNumerando pedidos por tenant...\n";
$sql_tenants = "SELECT DISTINCT tenant_id FROM pedidos ORDER BY tenant_id";
$result_tenants = $GLOBALS['conn']->query($sql_tenants);

$updated_count = 0;
while ($tenant = $result_tenants->fetch_assoc()) {
    $tenant_id = $tenant['tenant_id'];
    
    // Obtener pedidos del tenant ordenados por fecha_creacion asc, id asc (cronológico)
    $sql_pedidos = "SELECT id FROM pedidos WHERE tenant_id = ? ORDER BY fecha_creacion ASC, id ASC";
    $stmt = $GLOBALS['conn']->prepare($sql_pedidos);
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $result_pedidos = $stmt->get_result();
    
    // Transacción por tenant para consistencia
    $GLOBALS['conn']->begin_transaction();

    // Reusar prepared statement de UPDATE dentro del bucle
    $stmt_update_pedido = $GLOBALS['conn']->prepare("UPDATE pedidos SET numero_pedido = ? WHERE id = ?");

    $numero = 1;
    while ($pedido = $result_pedidos->fetch_assoc()) {
        if ($dryRun) {
            echo "   -> Pedido {$pedido['id']} => numero_pedido {$numero}\n";
        } else {
            $stmt_update_pedido->bind_param("ii", $numero, $pedido['id']);
            if (!$stmt_update_pedido->execute()) {
                $GLOBALS['conn']->rollback();
                echo "✗ Error actualizando numero_pedido: " . $stmt_update_pedido->error . "\n";
                exit(1);
            }
        }
        $numero++;
        $updated_count++;
    }
    // Commit/rollback según modo
    if ($dryRun) {
        $GLOBALS['conn']->rollback();
    } else {
        $GLOBALS['conn']->commit();
    }

    if (isset($stmt_update_pedido)) { $stmt_update_pedido->close(); }
    
    echo "✓ Tenant $tenant_id: $numero pedidos numerados (1-" . ($numero - 1) . ")\n";
}

echo "\n✓ Total de pedidos renumerados: $updated_count\n";
echo "Migración completada exitosamente.\n";
// ------------------------------------------------------------------
// Además, renumerar `numero_cuenta_cobro` por tenant para asegurar que
// la numeración de cuentas de cobro comience en 1 y sea consecutiva.
// Esto ordena por `fecha_creacion` (fallback por `id`) y sobrescribe
// cualquier valor existente.
// ------------------------------------------------------------------

echo "\nAhora renumerando numero_cuenta_cobro por tenant...\n";
$sql_tenants2 = "SELECT DISTINCT tenant_id FROM pedidos ORDER BY tenant_id";
$result_tenants2 = $GLOBALS['conn']->query($sql_tenants2);
$updated_cc = 0;
while ($tenant = $result_tenants2->fetch_assoc()) {
    $tenant_id = $tenant['tenant_id'];
    // Obtener pedidos del tenant ordenados por fecha_creacion asc, id asc
    $sql_pedidos = "SELECT id FROM pedidos WHERE tenant_id = ? ORDER BY fecha_creacion ASC, id ASC";
    $stmt = $GLOBALS['conn']->prepare($sql_pedidos);
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $result_ped = $stmt->get_result();

    // Transacción por tenant
    $GLOBALS['conn']->begin_transaction();
    $stmt_update_cc = $GLOBALS['conn']->prepare("UPDATE pedidos SET numero_cuenta_cobro = ? WHERE id = ?");

    $counter = 1;
    while ($pedido = $result_ped->fetch_assoc()) {
        if ($dryRun) {
            echo "   -> Pedido {$pedido['id']} => numero_cuenta_cobro {$counter}\n";
        } else {
            $stmt_update_cc->bind_param("ii", $counter, $pedido['id']);
            if (!$stmt_update_cc->execute()) {
                $GLOBALS['conn']->rollback();
                echo "✗ Error actualizando numero_cuenta_cobro: " . $stmt_update_cc->error . "\n";
                exit(1);
            }
        }
        $counter++;
        $updated_cc++;
    }
    if ($dryRun) {
        $GLOBALS['conn']->rollback();
    } else {
        $GLOBALS['conn']->commit();
    }
    if (isset($stmt_update_cc)) { $stmt_update_cc->close(); }

    echo "✓ Tenant $tenant_id: numero_cuenta_cobro renumerados (1-" . ($counter - 1) . ")\n";
}

echo "\n✓ Total numero_cuenta_cobro renumerados: $updated_cc\n";

?>
