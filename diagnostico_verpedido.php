<?php
/**
 * TEST DIAGNÓSTICO: verPedido
 * Comprueba qué sucede al intentar acceder a un pedido
 */

require_once 'config/database.php';
require_once 'config/TenantResolver.php';

// Permitir ejecución por CLI forzando tenant si no viene por URL
if (php_sapi_name() === 'cli' && !isset($_GET['tenant'])) {
    $_GET['tenant'] = 'mauricio';
}

// Resolver tenant
TenantResolver::resolve();

echo "<h1>🔍 DIAGNÓSTICO: verPedido</h1>";
echo "<hr>";

// Mostrar tenant resuelto
echo "<h2>Tenant Resuelto</h2>";
echo "<pre>";
echo "TENANT_ID: " . (defined('TENANT_ID') ? TENANT_ID : 'NO DEFINIDO') . "\n";
echo "TENANT_SLUG: " . (defined('TENANT_SLUG') ? TENANT_SLUG : 'NO DEFINIDO') . "\n";
echo "TENANT_NAME: " . (defined('TENANT_NAME') ? TENANT_NAME : 'NO DEFINIDO') . "\n";
echo "Sesión tenant_id: " . ($_SESSION['tenant_id'] ?? 'NO SET') . "\n";
echo "</pre>";

// Parámetro ID
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<p>❌ Falta parámetro ID. Use: ?id=N</p>";
    exit;
}

echo "<h2>Búsqueda de Pedido</h2>";
echo "<p>Buscando pedido ID: <strong>$id</strong> en tenant_id: <strong>" . (defined('TENANT_ID') ? TENANT_ID : 'NO DEFINIDO') . "</strong></p>";

// Verificar si el pedido existe en el tenant actual
$sql = "SELECT id, cliente_id, estado, total, fecha_creacion, tenant_id
        FROM pedidos 
        WHERE id = ? AND tenant_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "<p>❌ Error en prepare: " . $conn->error . "</p>";
    exit;
}

$tenant_id = defined('TENANT_ID') ? TENANT_ID : 1;
$stmt->bind_param('ii', $id, $tenant_id);

if (!$stmt->execute()) {
    echo "<p>❌ Error en execute: " . $stmt->error . "</p>";
    exit;
}

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<p>❌ PEDIDO NO ENCONTRADO en tenant_id=$tenant_id</p>";
    
    // Buscar el pedido en cualquier tenant
    echo "<h3>🔎 Buscando en TODOS los tenants...</h3>";
    $sql_all = "SELECT id, cliente_id, estado, total, fecha_creacion, tenant_id
                FROM pedidos 
                WHERE id = ?";
    $stmt_all = $conn->prepare($sql_all);
    $stmt_all->bind_param('i', $id);
    $stmt_all->execute();
    $resultado_all = $stmt_all->get_result();
    
    if ($resultado_all->num_rows > 0) {
        $pedido = $resultado_all->fetch_assoc();
        echo "<p>✅ Pedido ENCONTRADO en tenant_id: <strong>" . $pedido['tenant_id'] . "</strong></p>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Columna</th><th>Valor</th></tr>";
        foreach ($pedido as $k => $v) {
            echo "<tr><td>$k</td><td>" . htmlspecialchars($v) . "</td></tr>";
        }
        echo "</table>";
        
        // Mostrar detalles del pedido
        echo "<h3>Detalles del Pedido</h3>";
        $sql_detalles = "SELECT pd.id, pd.producto_id, pd.cantidad, pd.precio_unitario, pr.nombre, pr.descripcion, pd.estado_preparacion
                         FROM pedido_detalles pd
                         LEFT JOIN productos pr ON pr.id = pd.producto_id
                         WHERE pd.pedido_id = ? AND pd.tenant_id = ?";
        $stmt_det = $conn->prepare($sql_detalles);
        $tenant_det = $pedido['tenant_id'];
        $stmt_det->bind_param('ii', $id, $tenant_det);
        $stmt_det->execute();
        $resultado_det = $stmt_det->get_result();
        
        if ($resultado_det->num_rows > 0) {
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>ID</th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Descripción</th><th>Estado Prep</th></tr>";
            while ($det = $resultado_det->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($det['id']) . "</td>";
                echo "<td>" . htmlspecialchars($det['nombre'] ?? 'SIN NOMBRE') . "</td>";
                echo "<td>" . htmlspecialchars($det['cantidad']) . "</td>";
                echo "<td>" . htmlspecialchars($det['precio_unitario']) . "</td>";
                echo "<td>" . htmlspecialchars($det['descripcion'] ?? '-') . "</td>";
                echo "<td>" . htmlspecialchars($det['estado_preparacion'] ?? '-') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No hay detalles para este pedido en tenant_id=" . $pedido['tenant_id'] . "</p>";
        }
    } else {
        echo "<p>❌ Pedido ID=$id NO EXISTE en ningún tenant</p>";
    }
} else {
    $pedido = $resultado->fetch_assoc();
    echo "<p>✅ PEDIDO ENCONTRADO en tenant_id=" . $pedido['tenant_id'] . "</p>";
    
    echo "<h3>Datos del Pedido</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Columna</th><th>Valor</th></tr>";
    foreach ($pedido as $k => $v) {
        echo "<tr><td>$k</td><td>" . htmlspecialchars($v) . "</td></tr>";
    }
    echo "</table>";
    
    // Mostrar detalles del pedido
    echo "<h3>Detalles del Pedido</h3>";
    $sql_detalles = "SELECT pd.id, pd.producto_id, pd.cantidad, pd.precio_unitario, pr.nombre, pr.descripcion, pd.estado_preparacion
                     FROM pedido_detalles pd
                     LEFT JOIN productos pr ON pr.id = pd.producto_id
                     WHERE pd.pedido_id = ? AND pd.tenant_id = ?";
    $stmt_det = $conn->prepare($sql_detalles);
    $stmt_det->bind_param('ii', $id, $tenant_id);
    $stmt_det->execute();
    $resultado_det = $stmt_det->get_result();
    
    if ($resultado_det->num_rows > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Descripción</th><th>Estado Prep</th></tr>";
        while ($det = $resultado_det->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($det['id']) . "</td>";
            echo "<td>" . htmlspecialchars($det['nombre'] ?? 'SIN NOMBRE') . "</td>";
            echo "<td>" . htmlspecialchars($det['cantidad']) . "</td>";
            echo "<td>" . htmlspecialchars($det['precio_unitario']) . "</td>";
            echo "<td>" . htmlspecialchars($det['descripcion'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($det['estado_preparacion'] ?? '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ No hay detalles para este pedido</p>";
    }
}

echo "<hr>";
echo "<p><a href='/catalogo2/diagnostico_tenants.php'>← Ver diagnóstico de tenants</a></p>";
?>
