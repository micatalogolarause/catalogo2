<?php
/**
 * Modelo de Subcategorías
++ * MULTI-TENANCY: Todas las queries filtran por tenant_id
 */
class SubcategoriaModel {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Obtener todas las subcategorías activas
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerTodas() {
        $sql = "SELECT sc.*, c.nombre as categoria FROM subcategorias sc 
            JOIN categorias c ON sc.categoria_id = c.id AND sc.tenant_id = c.tenant_id
            WHERE sc.tenant_id = ? AND sc.activa = 1 ORDER BY c.nombre, sc.nombre";
        return obtenerFilasScoped($sql);
    }
    
    /**
     * Obtener subcategoría por ID
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerPorId($id) {
        $sql = "SELECT sc.*, c.nombre as categoria FROM subcategorias sc 
            JOIN categorias c ON sc.categoria_id = c.id AND sc.tenant_id = c.tenant_id
            WHERE sc.tenant_id = ? AND sc.id = ? AND sc.activa = 1";
        return obtenerFilaScoped($sql, "i", array($id));
    }
    
    /**
     * Obtener subcategorías por categoría
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerPorCategoria($categoria_id) {
        $sql = "SELECT * FROM subcategorias WHERE tenant_id = ? AND categoria_id = ? AND activa = 1 ORDER BY nombre";
        return obtenerFilasScoped($sql, "i", array($categoria_id));
    }
    
    /**
     * Crear subcategoría
    ++ * Incluye tenant_id automáticamente
     */
    public function crear($categoria_id, $nombre, $descripcion = '') {
        $sql = "INSERT INTO subcategorias (tenant_id, categoria_id, nombre, descripcion) VALUES (?, ?, ?, ?)";
        $tipos = "iss";
        $params = array($categoria_id, $nombre, $descripcion);
        
        return ejecutarConsultaScoped($sql, $tipos, $params) ? obtenerUltimoId() : false;
    }
    
    /**
     * Actualizar subcategoría
    ++ * Valida tenant_id para seguridad
     */
    public function actualizar($id, $nombre, $descripcion = '') {
        $sql = "UPDATE subcategorias SET nombre = ?, descripcion = ? WHERE tenant_id = ? AND id = ?";
        $tipos = "ssi";
        $params = array($nombre, $descripcion, $id);
        
        return ejecutarConsultaScoped($sql, $tipos, $params) ? true : false;
    }
    
    /**
     * Eliminar subcategoría (soft delete)
    ++ * Valida tenant_id para seguridad
     */
    public function eliminar($id) {
        $sql = "UPDATE subcategorias SET activa = 0 WHERE tenant_id = ? AND id = ?";
        return ejecutarConsultaScoped($sql, "i", array($id)) ? true : false;
    }
}
?>
