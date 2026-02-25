<?php
/**
 * Modelo de Clientes
++ * MULTI-TENANCY: Todas las queries filtran por tenant_id
 */
class ClienteModel {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Crear cliente
    ++ * Incluye tenant_id automáticamente
     */
    public function crear($data) {
        $sql = "INSERT INTO clientes (tenant_id, nombre, email, telefono, whatsapp, ciudad, direccion) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $tipos = "ssssss";
        $params = array(
            $data['nombre'],
            $data['email'],
            $data['telefono'],
            $data['whatsapp'],
            $data['ciudad'],
            $data['direccion']
        );
        
        return ejecutarConsultaScoped($sql, $tipos, $params) ? obtenerUltimoId() : false;
    }
    
    /**
     * Obtener cliente por ID
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM clientes WHERE tenant_id = ? AND id = ?";
        return obtenerFilaScoped($sql, "i", array($id));
    }
    
    /**
     * Obtener cliente por email
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerPorEmail($email) {
        $sql = "SELECT * FROM clientes WHERE tenant_id = ? AND email = ?";
        return obtenerFilaScoped($sql, "s", array($email));
    }
    
    /**
     * Obtener todos los clientes
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerTodos() {
        $sql = "SELECT * FROM clientes WHERE tenant_id = ? ORDER BY fecha_registro DESC";
        return obtenerFilasScoped($sql);
    }
    
    /**
     * Actualizar cliente
    ++ * Valida tenant_id para seguridad
     */
    public function actualizar($id, $data) {
        $sql = "UPDATE clientes SET 
            nombre = ?, 
            email = ?, 
            telefono = ?, 
            whatsapp = ?, 
            ciudad = ?, 
            direccion = ? 
            WHERE tenant_id = ? AND id = ?";
        
        $tipos = "ssssssi";
        $params = array(
            $data['nombre'],
            $data['email'],
            $data['telefono'],
            $data['whatsapp'],
            $data['ciudad'],
            $data['direccion'],
            $id
        );
        
        return ejecutarConsultaScoped($sql, $tipos, $params) ? true : false;
    }
}
?>
