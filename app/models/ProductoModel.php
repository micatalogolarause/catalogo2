<?php
/**
 * Modelo de Productos
++ * MULTI-TENANCY: Todas las queries filtran por tenant_id
 */
class ProductoModel {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Obtener todos los productos activos
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerTodos() {
        $sql = "SELECT p.*, c.nombre as categoria, sc.nombre as subcategoria 
            FROM productos p 
            JOIN categorias c ON p.categoria_id = c.id AND p.tenant_id = c.tenant_id
            JOIN subcategorias sc ON p.subcategoria_id = sc.id AND p.tenant_id = sc.tenant_id
            WHERE p.tenant_id = ? AND p.activo = 1 
            ORDER BY p.fecha_creacion DESC";
        
        return obtenerFilasScoped($sql);
    }
    
    /**
     * Obtener producto por ID
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerPorId($id) {
        $sql = "SELECT p.*, c.nombre as categoria, sc.nombre as subcategoria 
            FROM productos p 
            JOIN categorias c ON p.categoria_id = c.id AND p.tenant_id = c.tenant_id
            JOIN subcategorias sc ON p.subcategoria_id = sc.id AND p.tenant_id = sc.tenant_id
            WHERE p.tenant_id = ? AND p.id = ? AND p.activo = 1";
        
        return obtenerFilaScoped($sql, "i", array($id));
    }
    
    /**
     * Obtener productos por categoría
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerPorCategoria($categoria_id) {
        $sql = "SELECT p.*, c.nombre as categoria, sc.nombre as subcategoria 
            FROM productos p 
            JOIN categorias c ON p.categoria_id = c.id AND p.tenant_id = c.tenant_id
            JOIN subcategorias sc ON p.subcategoria_id = sc.id AND p.tenant_id = sc.tenant_id
            WHERE p.tenant_id = ? AND p.categoria_id = ? AND p.activo = 1 
            ORDER BY p.nombre";
        
        return obtenerFilasScoped($sql, "i", array($categoria_id));
    }
    
    /**
     * Obtener productos por subcategoría
    ++ * Filtrado por tenant_id actual
     */
    public function obtenerPorSubcategoria($subcategoria_id) {
        $sql = "SELECT p.*, c.nombre as categoria, sc.nombre as subcategoria 
            FROM productos p 
            JOIN categorias c ON p.categoria_id = c.id AND p.tenant_id = c.tenant_id
            JOIN subcategorias sc ON p.subcategoria_id = sc.id AND p.tenant_id = sc.tenant_id
            WHERE p.tenant_id = ? AND p.subcategoria_id = ? AND p.activo = 1 
            ORDER BY p.nombre";
        
        return obtenerFilasScoped($sql, "i", array($subcategoria_id));
    }
    
    /**
     * Crear producto
    ++ * Incluye tenant_id automáticamente
     */
    public function crear($data) {
        // Obtener siguiente numero_producto para este tenant
        $tenant_id = getTenantId();
        $sql_max = "SELECT COALESCE(MAX(numero_producto), 0) + 1 as siguiente_numero FROM productos WHERE tenant_id = ?";
        $row = obtenerFila($sql_max, "i", array($tenant_id));
        $numero_producto = $row['siguiente_numero'] ?? 1;
        
        $sql = "INSERT INTO productos (tenant_id, categoria_id, subcategoria_id, nombre, descripcion, precio, stock, imagen, imagen2, imagen3, numero_producto) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $tipos = "iiissdisssi"; // tenant_id(i), categoria_id(i), subcategoria_id(i), nombre(s), descripcion(s), precio(d), stock(i), imagen(s), imagen2(s), imagen3(s), numero_producto(i)
        $params = array(
            $tenant_id,
            $data['categoria_id'],
            $data['subcategoria_id'],
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['stock'],
            $data['imagen'],
            $data['imagen2'] ?? '',
            $data['imagen3'] ?? '',
            $numero_producto
        );
        
        $stmt = ejecutarConsulta($sql, $tipos, $params);
        return $stmt ? obtenerUltimoId() : false;
    }
    
    /**
     * Actualizar producto
    ++ * Valida tenant_id para seguridad
     */
    public function actualizar($id, $data) {
        $sql = "UPDATE productos SET 
            categoria_id = ?, 
            subcategoria_id = ?, 
            nombre = ?, 
            descripcion = ?, 
            precio = ?, 
            stock = ?, 
                imagen = ?,
                imagen2 = ?,
                imagen3 = ?
            WHERE tenant_id = ? AND id = ?";
        
            $tipos = "iissdisssii";
        $params = array(
            $data['categoria_id'],
            $data['subcategoria_id'],
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['stock'],
            $data['imagen'],
                $data['imagen2'] ?? '',
                $data['imagen3'] ?? '',
            getTenantId(),
            $id
        );
        
        return ejecutarConsulta($sql, $tipos, $params) ? true : false;
    }
    
    /**
     * Eliminar producto (soft delete)
    ++ * Valida tenant_id para seguridad
     */
    public function eliminar($id) {
        $sql = "UPDATE productos SET activo = 0 WHERE tenant_id = ? AND id = ?";
        return ejecutarConsultaScoped($sql, "i", array($id)) ? true : false;
    }
    
    /**
     * Buscar productos
    ++ * Filtrado por tenant_id actual
     */
    public function buscar($termino) {
        $termino = "%$termino%";
        $sql = "SELECT p.*, c.nombre as categoria, sc.nombre as subcategoria 
            FROM productos p 
            JOIN categorias c ON p.categoria_id = c.id AND p.tenant_id = c.tenant_id
            JOIN subcategorias sc ON p.subcategoria_id = sc.id AND p.tenant_id = sc.tenant_id
            WHERE p.tenant_id = ? AND (p.nombre LIKE ? OR p.descripcion LIKE ?) AND p.activo = 1 
            ORDER BY p.nombre";
        
        return obtenerFilasScoped($sql, "ss", array($termino, $termino));
    }
}
?>
