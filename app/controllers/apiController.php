<?php
/**
 * Controlador API - Manejo de peticiones AJAX
 */
class ApiController {
    private function buildImageUrl($img) {
        if (!$img) return '';
        if (str_starts_with($img, 'public/tenants/')) {
            return APP_URL . '/' . $img;
        }
        return APP_URL . '/public/images/productos/' . $img;
    }
    
    public function __construct() {
        // Asegurar que la sesión esté iniciada
        if (!session_id()) {
            session_start();
        }
        header('Content-Type: application/json');
    }
    
    /**
     * Agregar producto al carrito
     */
    public function agregarAlCarrito() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
        $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
        
        if ($producto_id <= 0 || $cantidad <= 0) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }
        
        $session_id = session_id();
        $tenant_id = getTenantId();
        
        // Verificar si el producto existe
        global $conn;
        $sql = "SELECT id, stock FROM productos WHERE tenant_id = ? AND id = ? AND activo = 1";
        $producto = obtenerFilaScoped($sql, "i", array($producto_id));
        
        if (!$producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }
        
        if ($cantidad > $producto['stock']) {
            echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
            return;
        }
        
        // Verificar si ya existe en el carrito
        $sql_existe = "SELECT id, cantidad FROM carrito WHERE tenant_id = ? AND session_id = ? AND producto_id = ?";
        $existe = obtenerFila($sql_existe, "isi", array($tenant_id, $session_id, $producto_id));
        
        if ($existe) {
            // Actualizar cantidad sumando lo agregado
            $nueva_cantidad = $existe['cantidad'] + $cantidad;
            // Validar stock disponible nuevamente (cantidad solicitada debe existir)
            if ($cantidad > $producto['stock']) {
                echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
                return;
            }
            $sql_update = "UPDATE carrito SET cantidad = ? WHERE tenant_id = ? AND session_id = ? AND producto_id = ?";
            ejecutarConsulta($sql_update, "iisi", array($nueva_cantidad, $tenant_id, $session_id, $producto_id));
            // Descontar stock por la cantidad agregada
            $sql_desc = "UPDATE productos SET stock = stock - ? WHERE tenant_id = ? AND id = ? AND stock >= ?";
            ejecutarConsulta($sql_desc, "iiii", array($cantidad, $tenant_id, $producto_id, $cantidad));
        } else {
            // Insertar nuevo
            $sql_insert = "INSERT INTO carrito (tenant_id, session_id, producto_id, cantidad) VALUES (?, ?, ?, ?)";
            ejecutarConsulta($sql_insert, "isii", array($tenant_id, $session_id, $producto_id, $cantidad));
            // Descontar stock
            $sql_desc = "UPDATE productos SET stock = stock - ? WHERE tenant_id = ? AND id = ? AND stock >= ?";
            ejecutarConsulta($sql_desc, "iiii", array($cantidad, $tenant_id, $producto_id, $cantidad));
        }
        
        // Obtener cantidad total en carrito
        $sql_total = "SELECT COUNT(DISTINCT producto_id) as items, SUM(cantidad) as cantidad FROM carrito WHERE tenant_id = ? AND session_id = ?";
        $carrito_info = obtenerFila($sql_total, "is", array($tenant_id, $session_id));
        
        echo json_encode([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'carrito_items' => $carrito_info['items'],
            'carrito_cantidad' => $carrito_info['cantidad']
        ]);
    }
    
    /**
     * Obtener carrito
     */
    public function obtenerCarrito() {
        $session_id = session_id();
        $tenant_id = getTenantId();
        
        global $conn;
        $sql = "SELECT c.*, p.nombre, p.precio, p.imagen, p.stock 
            FROM carrito c 
            JOIN productos p ON c.producto_id = p.id AND c.tenant_id = p.tenant_id
            WHERE c.tenant_id = ? AND c.session_id = ?";
        
        $items = obtenerFilas($sql, "is", array($tenant_id, $session_id));
        
        $total = 0;
        foreach ($items as &$item) {
            $item['subtotal'] = $item['precio'] * $item['cantidad'];
            $total += $item['subtotal'];
            // Agregar la ruta completa a la imagen (tenant-aware)
            $item['imagen'] = $this->buildImageUrl($item['imagen']);
        }
        
        echo json_encode([
            'success' => true,
            'items' => $items,
            'total' => number_format($total, 2)
        ]);
    }
    
    /**
     * Actualizar cantidad en carrito
     */
    public function actualizarCarrito() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
        $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
        
        if ($producto_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }
        
        $session_id = session_id();
        $tenant_id = getTenantId();
        
        if ($cantidad <= 0) {
            // Obtener cantidad actual para devolver stock
            $sql_get = "SELECT cantidad FROM carrito WHERE tenant_id = ? AND session_id = ? AND producto_id = ?";
            $row = obtenerFila($sql_get, "isi", array($tenant_id, $session_id, $producto_id));
            $cantAnterior = $row ? (int)$row['cantidad'] : 0;
            // Eliminar del carrito
            $sql = "DELETE FROM carrito WHERE tenant_id = ? AND session_id = ? AND producto_id = ?";
            ejecutarConsulta($sql, "isi", array($tenant_id, $session_id, $producto_id));
            // Devolver stock
            if ($cantAnterior > 0) {
                $sql_dev = "UPDATE productos SET stock = stock + ? WHERE tenant_id = ? AND id = ?";
                ejecutarConsulta($sql_dev, "iii", array($cantAnterior, $tenant_id, $producto_id));
            }
        } else {
            // Actualizar cantidad con ajuste de stock
            $sql_get = "SELECT cantidad FROM carrito WHERE tenant_id = ? AND session_id = ? AND producto_id = ?";
            $row = obtenerFila($sql_get, "isi", array($tenant_id, $session_id, $producto_id));
            $cantAnterior = $row ? (int)$row['cantidad'] : 0;
            $delta = $cantidad - $cantAnterior;
            if ($delta > 0) {
                // Verificar stock suficiente para incremento
                $sql_prod = "SELECT stock FROM productos WHERE tenant_id = ? AND id = ?";
                $prod = obtenerFilaScoped($sql_prod, "i", array($producto_id));
                if (!$prod || $delta > (int)$prod['stock']) {
                    echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
                    return;
                }
                $sql_desc = "UPDATE productos SET stock = stock - ? WHERE tenant_id = ? AND id = ? AND stock >= ?";
                ejecutarConsulta($sql_desc, "iiii", array($delta, $tenant_id, $producto_id, $delta));
            } else if ($delta < 0) {
                // Devolver stock por reducción
                $sql_dev = "UPDATE productos SET stock = stock + ? WHERE tenant_id = ? AND id = ?";
                ejecutarConsulta($sql_dev, "iii", array(abs($delta), $tenant_id, $producto_id));
            }
            // Actualizar cantidad
            $sql = "UPDATE carrito SET cantidad = ? WHERE tenant_id = ? AND session_id = ? AND producto_id = ?";
            ejecutarConsulta($sql, "iisi", array($cantidad, $tenant_id, $session_id, $producto_id));
        }
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * Eliminar producto del carrito
     */
    public function eliminarDelCarrito() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
        
        if ($producto_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }
        
        $session_id = session_id();
        $tenant_id = getTenantId();

        // Obtener cantidad para devolver stock
        $sql_get = "SELECT cantidad FROM carrito WHERE tenant_id = ? AND session_id = ? AND producto_id = ?";
        $row = obtenerFila($sql_get, "isi", array($tenant_id, $session_id, $producto_id));
        $cantAnterior = $row ? (int)$row['cantidad'] : 0;

        $sql = "DELETE FROM carrito WHERE tenant_id = ? AND session_id = ? AND producto_id = ?";
        ejecutarConsulta($sql, "isi", array($tenant_id, $session_id, $producto_id));
        
        if ($cantAnterior > 0) {
            $sql_dev = "UPDATE productos SET stock = stock + ? WHERE tenant_id = ? AND id = ?";
            ejecutarConsulta($sql_dev, "iii", array($cantAnterior, $tenant_id, $producto_id));
        }
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * Vaciar carrito
     */
    public function vaciarCarrito() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $session_id = session_id();
        $tenant_id = getTenantId();
        
        // Recuperar items para devolver stock
        $sql_items = "SELECT producto_id, cantidad FROM carrito WHERE tenant_id = ? AND session_id = ?";
        $items = obtenerFilas($sql_items, "is", array($tenant_id, $session_id));
        foreach ($items as $it) {
            $sql_dev = "UPDATE productos SET stock = stock + ? WHERE tenant_id = ? AND id = ?";
            ejecutarConsulta($sql_dev, "iii", array((int)$it['cantidad'], $tenant_id, (int)$it['producto_id']));
        }
        
        $sql = "DELETE FROM carrito WHERE tenant_id = ? AND session_id = ?";
        ejecutarConsulta($sql, "is", array($tenant_id, $session_id));
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * Obtener subcategorías por categoría
     */
    public function obtenerSubcategorias() {
        $categoria_id = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;
        
        if ($categoria_id <= 0) {
            echo json_encode(['success' => false, 'subcategorias' => array()]);
            return;
        }
        
        $sql = "SELECT id, nombre FROM subcategorias WHERE tenant_id = ? AND categoria_id = ? AND activa = 1 ORDER BY nombre";
        $subcategorias = obtenerFilasScoped($sql, "i", array($categoria_id));
        
        echo json_encode(['success' => true, 'subcategorias' => $subcategorias]);
    }
}
?>
