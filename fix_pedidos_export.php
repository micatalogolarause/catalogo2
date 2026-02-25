<?php
// Script para corregir obtenerPedidosFiltrados

$file = 'c:/xampp/htdocs/catalogo2/app/controllers/adminController.php';
$content = file_get_contents($file);

// Buscar y reemplazar la función
$old = <<<'PHP'
    private function obtenerPedidosFiltrados($cliente = '', $desde = '', $hasta = '', $estado = '') {
        $sql = "SELECT p.*, c.nombre as cliente_nombre 
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                WHERE 1=1";
        
        $params = array();
        $types = "";
        
        if (!empty($cliente)) {
            $sql .= " AND c.nombre LIKE ?";
            $params[] = "%$cliente%";
            $types .= "s";
        }
        
        if (!empty($desde)) {
            $sql .= " AND DATE(p.fecha_creacion) >= ?";
            $params[] = $desde;
            $types .= "s";
        }
        
        if (!empty($hasta)) {
            $sql .= " AND DATE(p.fecha_creacion) <= ?";
            $params[] = $hasta;
            $types .= "s";
        }
        
        if (!empty($estado)) {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
            $types .= "s";
        }
        
        $sql .= " ORDER BY p.fecha_creacion DESC";
        
        if (!empty($types)) {
            return obtenerFilas($sql, $types, $params);
        } else {
            return obtenerFilas($sql);
        }
    }
PHP;

$new = <<<'PHP'
    private function obtenerPedidosFiltrados($cliente = '', $desde = '', $hasta = '', $estado = '') {
        $tenant_id = getTenantId();
        
        $sql = "SELECT p.*, c.nombre as cliente_nombre 
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                WHERE p.tenant_id = ?";
        
        $params = array($tenant_id);
        $types = "i";
        
        if (!empty($cliente)) {
            $sql .= " AND c.nombre LIKE ?";
            $params[] = "%$cliente%";
            $types .= "s";
        }
        
        if (!empty($desde)) {
            $sql .= " AND DATE(p.fecha_creacion) >= ?";
            $params[] = $desde;
            $types .= "s";
        }
        
        if (!empty($hasta)) {
            $sql .= " AND DATE(p.fecha_creacion) <= ?";
            $params[] = $hasta;
            $types .= "s";
        }
        
        if (!empty($estado)) {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
            $types .= "s";
        }
        
        $sql .= " ORDER BY p.fecha_creacion DESC";
        
        return obtenerFilas($sql, $types, $params);
    }
PHP;

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
    echo "✅ Función obtenerPedidosFiltrados actualizada correctamente\n";
} else {
    echo "❌ No se encontró la función exacta\n";
}
?>
