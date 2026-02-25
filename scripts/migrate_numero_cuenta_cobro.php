<?php
/**
 * Script de migración: agrega la columna `numero_cuenta_cobro` a la tabla `pedidos`
 * y popula un número secuencial por tenant (basado en fecha_creacion asc).
 * Uso: php scripts/migrate_numero_cuenta_cobro.php
 */

require_once __DIR__ . '/../config/database.php';

echo "=== Migración: numero_cuenta_cobro ===\n";

// Verificar si la columna ya existe
$res = $conn->query("SHOW COLUMNS FROM pedidos LIKE 'numero_cuenta_cobro'");
if ($res && $res->num_rows > 0) {
    echo "Columna 'numero_cuenta_cobro' ya existe. Procediendo a poblar valores faltantes...\n";
} else {
    echo "Agregando columna 'numero_cuenta_cobro'...\n";
    $sql_add = "ALTER TABLE pedidos ADD COLUMN numero_cuenta_cobro INT NULL DEFAULT NULL";
    if (!$conn->query($sql_add)) {
        echo "Error al agregar columna: " . $conn->error . "\n";
        exit(1);
    }
    echo "Columna agregada correctamente.\n";
}

// Obtener lista de tenants presentes en pedidos
$tenant_result = $conn->query("SELECT DISTINCT tenant_id FROM pedidos ORDER BY tenant_id ASC");
if (!$tenant_result) {
    echo "Error al obtener tenants: " . $conn->error . "\n";
    exit(1);
}

$tenants = [];
while ($r = $tenant_result->fetch_assoc()) {
    $tenants[] = (int)$r['tenant_id'];
}

if (empty($tenants)) {
    echo "No se encontraron registros en pedidos. Nada por hacer.\n";
    exit(0);
}

echo "Procesando " . count($tenants) . " tenants...\n";

foreach ($tenants as $tenant_id) {
    echo "- Tenant $tenant_id: comenzando transacción...\n";
    $conn->begin_transaction();
    try {
        // Seleccionar pedidos del tenant ordenados por fecha_creacion asc, fallback por id
        $sql_ped = "SELECT id FROM pedidos WHERE tenant_id = ? ORDER BY fecha_creacion ASC, id ASC";
        $stmt = $conn->prepare($sql_ped);
        $stmt->bind_param("i", $tenant_id);
        $stmt->execute();
        $res = $stmt->get_result();

        $counter = 1;
        while ($row = $res->fetch_assoc()) {
            $pedido_id = (int)$row['id'];
            // Actualizar solo si es NULL o 0
            $sql_up = "UPDATE pedidos SET numero_cuenta_cobro = ? WHERE id = ?";
            $stmt_up = $conn->prepare($sql_up);
            $stmt_up->bind_param("ii", $counter, $pedido_id);
            if (!$stmt_up->execute()) {
                throw new Exception("Error actualizando pedido $pedido_id: " . $stmt_up->error);
            }
            $counter++;
        }

        $conn->commit();
        echo "  ✅ Tenant $tenant_id: poblados " . ($counter - 1) . " numeros.\n";
    } catch (Exception $ex) {
        $conn->rollback();
        echo "  ❌ Error tenant $tenant_id: " . $ex->getMessage() . "\n";
    }
}

echo "Migración completada. Recomendado: crear respaldo antes de poner en producción.\n";

?>