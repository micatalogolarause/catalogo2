<?php
/**
 * Modelo de Categorías
++ * MULTI-TENANCY: Todas las queries filtran por tenant_id
 */
class CategoriaModel {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Obtener todas las categorías activas
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerTodas() {
        $sql = "SELECT * FROM categorias WHERE tenant_id = ? AND activa = 1 ORDER BY nombre";
        return obtenerFilasScoped($sql);
    }
    
    /**
     * Obtener categoría por ID
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM categorias WHERE tenant_id = ? AND id = ? AND activa = 1";
        return obtenerFilaScoped($sql, "i", array($id));
    }
    
    /**
     * Crear categoría
    ++ * Incluye tenant_id automáticamente
     */
    public function crear($nombre, $descripcion = '') {
        $sql = "INSERT INTO categorias (tenant_id, nombre, descripcion) VALUES (?, ?, ?)";
        $tipos = "ss";
        $params = array($nombre, $descripcion);
        
        return ejecutarConsultaScoped($sql, $tipos, $params) ? obtenerUltimoId() : false;
    }
    
    /**
     * Actualizar categoría
    ++ * Valida tenant_id para seguridad
     */
    public function actualizar($id, $nombre, $descripcion = '') {
        $sql = "UPDATE categorias SET nombre = ?, descripcion = ? WHERE tenant_id = ? AND id = ?";
        $tipos = "ssi";
        $params = array($nombre, $descripcion, $id);
        
        return ejecutarConsultaScoped($sql, $tipos, $params) ? true : false;
    }
    
    /**
     * Eliminar categoría (soft delete)
    ++ * Valida tenant_id para seguridad
     */
    public function eliminar($id) {
        $sql = "UPDATE categorias SET activa = 0 WHERE tenant_id = ? AND id = ?";
        return ejecutarConsultaScoped($sql, "i", array($id)) ? true : false;
    }
    
    /**
     * Obtener categoría con subcategorías
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerConSubcategorias($id) {
        return $this->obtenerPorId($id);
    }
}
?>
