<?php
/**
 * Modelo de Pedidos
++ * MULTI-TENANCY: Todas las queries filtran por tenant_id
 */
class PedidoModel {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Crear un nuevo pedido
    ++ * Incluye tenant_id automáticamente
     * Auto-asigna numero_pedido secuencial por tenant
     */
    public function crear($cliente_id, $total, $notas = '') {
        // Obtener siguiente numero_pedido y numero_cuenta_cobro para este tenant
        $tenant_id = getTenantId();
        $cliente_id = (int)$cliente_id;
        
        // Validar que el cliente pertenece al tenant actual
        $sql_validate = "SELECT id FROM clientes WHERE tenant_id = ? AND id = ?";
        $cliente = obtenerFila($sql_validate, "ii", array($tenant_id, $cliente_id));
        if (!$cliente) {
            error_log("PedidoModel::crear - cliente_id={$cliente_id} no existe para tenant_id={$tenant_id}");
            return false;
        }

        $sql_max = "SELECT COALESCE(MAX(numero_pedido), 0) + 1 as siguiente_numero FROM pedidos WHERE tenant_id = ?";
        $row = obtenerFila($sql_max, "i", array($tenant_id));
        $numero_pedido = $row['siguiente_numero'] ?? 1;

        // numero_cuenta_cobro: per-tenant sequential invoice number
        $sql_max_cc = "SELECT COALESCE(MAX(numero_cuenta_cobro), 0) + 1 as siguiente_cc FROM pedidos WHERE tenant_id = ?";
        $row2 = obtenerFila($sql_max_cc, "i", array($tenant_id));
        $numero_cuenta_cobro = $row2['siguiente_cc'] ?? 1;

        $sql = "INSERT INTO pedidos (tenant_id, cliente_id, total, notas_cliente, numero_pedido, numero_cuenta_cobro) VALUES (?, ?, ?, ?, ?, ?)";
        // types: tenant_id(i), cliente_id(i), total(d), notas(s), numero_pedido(i), numero_cuenta_cobro(i)
        $tipos = "idssii";
        $params = array($tenant_id, $cliente_id, $total, $notas, $numero_pedido, $numero_cuenta_cobro);

        if (ejecutarConsulta($sql, $tipos, $params)) {
            $pedido_id = (int)obtenerUltimoId();
            // Fallback: si obtenerUltimoId() falla, buscar el pedido recién creado
            if (!$pedido_id) {
                $fila = obtenerFila("SELECT id FROM pedidos WHERE tenant_id = ? AND numero_pedido = ?", "ii", array($tenant_id, $numero_pedido));
                $pedido_id = $fila ? (int)$fila['id'] : 0;
            }
            if (!$pedido_id) {
                error_log("PedidoModel::crear - no se pudo obtener pedido_id tras INSERT");
                return false;
            }
            // Registrar historial inicial
            $estado_inicial = 'en_pedido';
            $nota = 'Pedido creado';
            $usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;
            $this->registrarHistorial($pedido_id, $estado_inicial, $nota, $usuario_id);
            return $pedido_id;
        }
        return false;
    }
    
    /**
     * Obtener pedido por ID
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerPorId($id) {
        $sql = "SELECT p.*, c.nombre, c.email, c.whatsapp, c.telefono, c.direccion 
            FROM pedidos p 
            JOIN clientes c ON p.cliente_id = c.id AND p.tenant_id = c.tenant_id
            WHERE p.tenant_id = ? AND p.id = ?";
        
        $pedido = obtenerFilaScoped($sql, "i", array($id));
        
        if ($pedido) {
            // Obtener detalles del pedido
            $sql_detalles = "SELECT pd.*, pr.nombre, pr.imagen, pr.descripcion
                             FROM pedido_detalles pd 
                             JOIN productos pr ON pd.producto_id = pr.id AND pd.tenant_id = pr.tenant_id
                             WHERE pd.tenant_id = ? AND pd.pedido_id = ?";
            $pedido['detalles'] = obtenerFilasScoped($sql_detalles, "i", array($id));
        }
        
        return $pedido;
    }
    
    /**
     * Obtener todos los pedidos (para admin)
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerTodos($estado = null) {
        if ($estado) {
                $sql = "SELECT p.*, c.nombre, c.email, c.whatsapp 
                    FROM pedidos p 
                    JOIN clientes c ON p.cliente_id = c.id AND p.tenant_id = c.tenant_id
                    WHERE p.tenant_id = ? AND p.estado = ? 
                    ORDER BY p.fecha_creacion DESC";
                return obtenerFilasScoped($sql, "s", array($estado));
        } else {
                $sql = "SELECT p.*, c.nombre, c.email, c.whatsapp 
                    FROM pedidos p 
                    JOIN clientes c ON p.cliente_id = c.id AND p.tenant_id = c.tenant_id
                    WHERE p.tenant_id = ? 
                    ORDER BY p.fecha_creacion DESC";
                return obtenerFilasScoped($sql);
        }
    }
    
    /**
     * Actualizar estado del pedido
    ++ * Valida tenant_id para seguridad
     */
    public function actualizarEstado($id, $estado, $notas_admin = '') {
        $tenant_id = getTenantId();
        
        $sql = "UPDATE pedidos SET estado = ?, notas_admin = ? WHERE tenant_id = ? AND id = ?";
        $stmt = ejecutarConsulta($sql, "ssii", array($estado, $notas_admin, $tenant_id, $id));
        
        if ($stmt) {
            $usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;
            $this->registrarHistorial($id, $estado, $notas_admin, $usuario_id);
            return true;
        }
        
        return false;
    }
    
    /**
     * Marcar WhatsApp como enviado
    ++ * Valida tenant_id para seguridad
     */
    public function marcarWhatsAppEnviado($id) {
        $sql = "UPDATE pedidos SET whatsapp_enviado = 1 WHERE tenant_id = ? AND id = ?";
        return ejecutarConsultaScoped($sql, "i", array($id)) ? true : false;
    }

    /**
     * Registrar historial de cambio de estado
     * Incluye tenant_id automáticamente
     */
    public function registrarHistorial($pedido_id, $estado, $nota = '', $usuario_id = null) {
        $tenant_id = getTenantId();
        
        if ($usuario_id === null) {
            $sql = "INSERT INTO pedido_historial (tenant_id, pedido_id, estado, nota) VALUES (?, ?, ?, ?)";
            $stmt = ejecutarConsulta($sql, "iiss", array($tenant_id, $pedido_id, $estado, $nota));
        } else {
            $sql = "INSERT INTO pedido_historial (tenant_id, pedido_id, estado, nota, usuario_id) VALUES (?, ?, ?, ?, ?)";
            $uid = (int)$usuario_id;
            $stmt = ejecutarConsulta($sql, "iissi", array($tenant_id, $pedido_id, $estado, $nota, $uid));
        }
        
        if (!$stmt) {
            error_log("Error registrarHistorial: query fallida");
            return false;
        }
        
        return true;
    }

    /**
     * Obtener historial del pedido
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerHistorial($pedido_id) {
        $sql = "SELECT h.*, u.usuario AS usuario
            FROM pedido_historial h
            LEFT JOIN usuarios u ON h.usuario_id = u.id AND h.tenant_id = u.tenant_id
            WHERE h.tenant_id = ? AND h.pedido_id = ?
            ORDER BY h.fecha ASC";
        return obtenerFilasScoped($sql, "i", array($pedido_id));
    }
}
?>
