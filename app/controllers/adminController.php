<?php
/**
 * Controlador Admin - Panel de administración
 */
class AdminController {
    private $categoriaModel;
    private $subcategoriaModel;
    private $productoModel;
    private $pedidoModel;
    
    public function __construct() {
        // Permitir acceder a login sin sesión
        $accionActual = isset($_GET['action']) ? $_GET['action'] : '';
        if ($accionActual !== 'login') {
            $this->verificarAutenticacion();
        }
        
        require_once APP_ROOT . '/app/models/CategoriaModel.php';
        require_once APP_ROOT . '/app/models/SubcategoriaModel.php';
        require_once APP_ROOT . '/app/models/ProductoModel.php';
        require_once APP_ROOT . '/app/models/PedidoModel.php';
        require_once APP_ROOT . '/config/uploads.php';
        require_once APP_ROOT . '/app/helpers/FacturaPDF.php';
        require_once APP_ROOT . '/app/helpers/FacturaExcel.php';
        require_once APP_ROOT . '/app/helpers/ProductosPDF.php';
        require_once APP_ROOT . '/app/helpers/ProductosExcel.php';
        require_once APP_ROOT . '/app/helpers/PedidosPDF.php';
        require_once APP_ROOT . '/app/helpers/PedidosExcel.php';
        
        global $conn;
        $this->categoriaModel = new CategoriaModel($conn);
        $this->subcategoriaModel = new SubcategoriaModel($conn);
        $this->productoModel = new ProductoModel($conn);
        $this->pedidoModel = new PedidoModel($conn);
    }
    
    /**
     * Verificar autenticación del usuario
     */
    private function verificarAutenticacion() {
        restore_session_from_auth_cookie();

        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=login');
            exit;
        }
    }
    
    /**
     * Login del administrador
     */
    public function login() {
        if (isset($_SESSION['usuario_id']) && $_SESSION['rol'] === 'admin') {
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=inicio');
            exit;
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = sanitizar($_POST['usuario'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (!$usuario || !$password) {
                $error = 'Por favor complete todos los campos';
            } else {
                global $conn;
                $password_hash = hash('sha256', $password);
                
                $sql = "SELECT id, usuario, nombre, rol FROM usuarios 
                        WHERE usuario = ? AND password = ? AND rol = 'admin' AND activo = 1";
                
                $admin = obtenerFila($sql, "ss", array(&$usuario, &$password_hash));
                
                if ($admin) {
                    $_SESSION['usuario_id'] = $admin['id'];
                    $_SESSION['usuario'] = $admin['usuario'];
                    $_SESSION['nombre'] = $admin['nombre'];
                    $_SESSION['rol'] = $admin['rol'];

                    set_auth_cookie([
                        'uid' => $admin['id'],
                        'rol' => $admin['rol'],
                        'tenant_slug' => TENANT_SLUG,
                        'usuario' => $admin['usuario'],
                        'nombre' => $admin['nombre']
                    ]);
                    
                    // Actualizar último acceso
                    $sql_update = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
                    ejecutarConsulta($sql_update, "i", array(&$admin['id']));
                    
                    header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=inicio');
                    exit;
                } else {
                    $error = 'Usuario o contraseña incorrectos';
                }
            }
        }
        
        include APP_ROOT . '/app/views/admin/login.php';
    }
    
    /**
     * Logout
     */
    public function logout() {
        // Guardar el slug del tenant antes de destruir la sesión
        $tenant_slug = $_SESSION['tenant_slug'] ?? '';

        clear_auth_cookie();
        
        session_destroy();
        
        // Redirigir a la página de inicio del tenant
        if ($tenant_slug) {
            header('Location: ' . APP_URL . '/' . $tenant_slug);
        } else {
            header('Location: ' . APP_URL);
        }
        exit;
    }
    
    /**
     * Dashboard principal
     */
    public function inicio() {
        $total_productos = count($this->productoModel->obtenerTodos());
        $total_pedidos = count($this->pedidoModel->obtenerTodos());
        $pedidos_recientes = $this->pedidoModel->obtenerTodos();
        $pedidos_recientes = array_slice($pedidos_recientes, 0, 5);
        
        include APP_ROOT . '/app/views/admin/inicio.php';
    }
    
    /**
     * Gestión de categorías
     */
    public function categorias() {
        $busqueda = isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : '';
        
        // Siempre filtrar por tenant; usar scoped helper
        if (!empty($busqueda)) {
            $sql = "SELECT * FROM categorias WHERE tenant_id = ? AND activa = 1 AND (nombre LIKE ? OR descripcion LIKE ? )";
            $termino = "%$busqueda%";
            $categorias = obtenerFilasScoped($sql, "ss", array($termino, $termino));
        } else {
            $categorias = $this->categoriaModel->obtenerTodas();
        }
        
        include APP_ROOT . '/app/views/admin/categorias.php';
    }
    
    /**
     * Crear categoría
     */
    public function crearCategoria() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = sanitizar($_POST['nombre'] ?? '');
            $descripcion = sanitizar($_POST['descripcion'] ?? '');
            
            if (!$nombre) {
                $_SESSION['error'] = 'El nombre de la categoría es requerido';
            } else {
                $resultado = $this->categoriaModel->crear($nombre, $descripcion);
                if ($resultado) {
                    $_SESSION['success'] = 'Categoría creada exitosamente';
                    header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=categorias');
                    exit;
                } else {
                    $_SESSION['error'] = 'Error al crear la categoría';
                }
            }
        }
        
        include APP_ROOT . '/app/views/admin/crear_categoria.php';
    }
    
    /**
     * Editar categoría
     */
    public function editarCategoria() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $categoria = $this->categoriaModel->obtenerPorId($id);
        
        if (!$categoria) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = sanitizar($_POST['nombre'] ?? '');
            $descripcion = sanitizar($_POST['descripcion'] ?? '');
            
            if (!$nombre) {
                $_SESSION['error'] = 'El nombre de la categoría es requerido';
            } else {
                $resultado = $this->categoriaModel->actualizar($id, $nombre, $descripcion);
                if ($resultado) {
                    $_SESSION['success'] = 'Categoría actualizada exitosamente';
                    header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=categorias');
                    exit;
                }
            }
        }
        
        include APP_ROOT . '/app/views/admin/editar_categoria.php';
    }
    
    /**
     * Eliminar categoría
     */
    public function eliminarCategoria() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if ($this->categoriaModel->eliminar($id)) {
            $_SESSION['success'] = 'Categoría eliminada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar la categoría';
        }
        
        header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=categorias');
        exit;
    }
    
    /**
     * Gestión de subcategorías
     */
    public function subcategorias() {
        $busqueda = isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : '';
        
        if (!empty($busqueda)) {
            $sql = "SELECT sc.*, c.nombre as categoria FROM subcategorias sc 
                    JOIN categorias c ON sc.categoria_id = c.id AND sc.tenant_id = c.tenant_id
                    WHERE sc.tenant_id = ? AND sc.activa = 1 AND (sc.nombre LIKE ? OR sc.descripcion LIKE ? OR c.nombre LIKE ?)";
            $termino = "%$busqueda%";
            $subcategorias = obtenerFilasScoped($sql, "sss", array($termino, $termino, $termino));
        } else {
            $subcategorias = $this->subcategoriaModel->obtenerTodas();
        }
        
        include APP_ROOT . '/app/views/admin/subcategorias.php';
    }
    
    /**
     * Crear subcategoría
     */
    public function crearSubcategoria() {
        $categorias = $this->categoriaModel->obtenerTodas();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria_id = isset($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : 0;
            $nombre = sanitizar($_POST['nombre'] ?? '');
            $descripcion = sanitizar($_POST['descripcion'] ?? '');
            
            if (!$nombre || !$categoria_id) {
                $_SESSION['error'] = 'El nombre de la subcategoría y la categoría son requeridos';
            } else {
                $resultado = $this->subcategoriaModel->crear($categoria_id, $nombre, $descripcion);
                if ($resultado) {
                    $_SESSION['success'] = 'Subcategoría creada exitosamente';
                    header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=subcategorias');
                    exit;
                } else {
                    $_SESSION['error'] = 'Error al crear la subcategoría';
                }
            }
        }
        
        include APP_ROOT . '/app/views/admin/crear_subcategoria.php';
    }
    
    /**
     * Editar subcategoría
     */
    public function editarSubcategoria() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $subcategoria = $this->subcategoriaModel->obtenerPorId($id);
        
        if (!$subcategoria) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }
        
        $categorias = $this->categoriaModel->obtenerTodas();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria_id = isset($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : 0;
            $nombre = sanitizar($_POST['nombre'] ?? '');
            $descripcion = sanitizar($_POST['descripcion'] ?? '');
            
            if (!$nombre || !$categoria_id) {
                $_SESSION['error'] = 'El nombre de la subcategoría y la categoría son requeridos';
            } else {
                $resultado = $this->subcategoriaModel->actualizar($id, $nombre, $descripcion);
                if ($resultado) {
                    $_SESSION['success'] = 'Subcategoría actualizada exitosamente';
                    header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=subcategorias');
                    exit;
                }
            }
        }
        
        include APP_ROOT . '/app/views/admin/editar_subcategoria.php';
    }
    
    /**
     * Eliminar subcategoría
     */
    public function eliminarSubcategoria() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if ($this->subcategoriaModel->eliminar($id)) {
            $_SESSION['success'] = 'Subcategoría eliminada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar la subcategoría';
        }
        
        header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=subcategorias');
        exit;
    }

    /**
     * Gestión de productos
     */
    public function productos() {
        $busqueda = isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : '';
        $filtro_estado = isset($_GET['estado']) ? sanitizar($_GET['estado']) : '';
        
        global $conn;
        $sql = "SELECT p.*, c.nombre as categoria, sc.nombre as subcategoria 
                FROM productos p 
                JOIN categorias c ON p.categoria_id = c.id 
                JOIN subcategorias sc ON p.subcategoria_id = sc.id 
                WHERE p.tenant_id = ?";
        
        $params = array($_SESSION['tenant_id']);
        $types = "i";
        
        // Filtro de búsqueda
        if (!empty($busqueda)) {
            $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ? OR c.nombre LIKE ?)";
            $termino = "%$busqueda%";
            $params[] = $termino;
            $params[] = $termino;
            $params[] = $termino;
            $types .= "sss";
        }
        
        // Filtro de estado
        if ($filtro_estado === 'activo') {
            $sql .= " AND p.activo = 1";
        } elseif ($filtro_estado === 'inactivo') {
            $sql .= " AND p.activo = 0";
        }
        
        $sql .= " ORDER BY p.id DESC";
        
        // Convertir array de parámetros a referencias para bind_param
        $bind_params = array();
        foreach ($params as $key => $value) {
            $bind_params[$key] = &$params[$key];
        }
        
        $productos = obtenerFilas($sql, $types, $bind_params);
        
        include APP_ROOT . '/app/views/admin/productos.php';
    }
    
    /**
     * Cambiar estado de producto (activar/desactivar)
     */
    public function cambiarEstadoProducto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $nuevo_estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;
        
        $sql = "UPDATE productos SET activo = ? WHERE id = ? AND tenant_id = ?";
        
        if (ejecutarConsulta($sql, "iii", [$nuevo_estado, $id, $_SESSION['tenant_id']])) {
            $_SESSION['success'] = $nuevo_estado ? 'Producto activado exitosamente' : 'Producto desactivado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al cambiar el estado del producto';
        }
        
        header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=productos');
        exit;
    }
    
    /**
     * Crear producto
     */
    public function crearProducto() {
        $categorias = $this->categoriaModel->obtenerTodas();
        $subcategorias = array();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria_id = isset($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : 0;
            $subcategoria_id = isset($_POST['subcategoria_id']) ? (int)$_POST['subcategoria_id'] : 0;
            $nombre = sanitizar($_POST['nombre'] ?? '');
            $descripcion = sanitizar($_POST['descripcion'] ?? '');
            $precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0;
            $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
            
            $imagen = '';
                $imagen2 = '';
                $imagen3 = '';
            $advertenciasUpload = [];
            
            // Procesar imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $upload = $this->guardarImagen('imagen');
                if ($upload['success']) {
                    $imagen = $upload['relPath'];
                } else {
                    $advertenciasUpload[] = $upload['message'] ?: 'No se pudo subir la imagen principal';
                }
            }
            
                // Procesar imagen2
                if (isset($_FILES['imagen2']) && $_FILES['imagen2']['error'] === 0) {
                    $upload = $this->guardarImagen('imagen2');
                    if ($upload['success']) {
                        $imagen2 = $upload['relPath'];
                    } else {
                        $advertenciasUpload[] = $upload['message'] ?: 'No se pudo subir la imagen 2';
                    }
                }
            
                // Procesar imagen3
                if (isset($_FILES['imagen3']) && $_FILES['imagen3']['error'] === 0) {
                    $upload = $this->guardarImagen('imagen3');
                    if ($upload['success']) {
                        $imagen3 = $upload['relPath'];
                    } else {
                        $advertenciasUpload[] = $upload['message'] ?: 'No se pudo subir la imagen 3';
                    }
                }
            
            if (!$nombre || !$categoria_id || !$subcategoria_id || !$precio) {
                $_SESSION['error'] = 'Por favor complete todos los campos obligatorios';
            } else {
                $data = array(
                    'categoria_id' => $categoria_id,
                    'subcategoria_id' => $subcategoria_id,
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'precio' => $precio,
                    'stock' => $stock,
                        'imagen' => $imagen,
                        'imagen2' => $imagen2,
                        'imagen3' => $imagen3
                );
                
                if ($this->productoModel->crear($data)) {
                    if (!empty($advertenciasUpload)) {
                        $_SESSION['success'] = 'Producto creado con advertencias: ' . implode(' | ', array_unique($advertenciasUpload));
                    } else {
                        $_SESSION['success'] = 'Producto creado exitosamente';
                    }
                    header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=productos');
                    exit;
                } else {
                    $_SESSION['error'] = 'Error al crear el producto';
                }
            }
        }
        
        crear_producto_vista:
        include APP_ROOT . '/app/views/admin/crear_producto.php';
    }
    
    /**
     * Editar producto
     */
    public function editarProducto() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $producto = $this->productoModel->obtenerPorId($id);
        
        if (!$producto) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }
        
        $categorias = $this->categoriaModel->obtenerTodas();
        $subcategorias = $this->subcategoriaModel->obtenerPorCategoria($producto['categoria_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria_id = isset($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : 0;
            $subcategoria_id = isset($_POST['subcategoria_id']) ? (int)$_POST['subcategoria_id'] : 0;
            $nombre = sanitizar($_POST['nombre'] ?? '');
            $descripcion = sanitizar($_POST['descripcion'] ?? '');
            $precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0;
            $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
            
            $imagen = $producto['imagen'];
                $imagen2 = $producto['imagen2'] ?? '';
                $imagen3 = $producto['imagen3'] ?? '';
            $advertenciasUpload = [];
            
            // Procesar nueva imagen si existe
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $upload = $this->guardarImagen('imagen');
                if ($upload['success']) {
                    $imagen = $upload['relPath'];
                    // Eliminar imagen anterior (soporta rutas legacy y por tenant)
                    if ($producto['imagen']) {
                        $oldPath = $producto['imagen'];
                        if (str_starts_with($oldPath, 'public/tenants/')) {
                            @unlink(APP_ROOT . '/' . $oldPath);
                        } else {
                            @unlink(APP_ROOT . '/public/images/productos/' . $oldPath);
                        }
                    }
                } else {
                    $advertenciasUpload[] = $upload['message'] ?: 'No se pudo subir la imagen principal';
                }
            }
            
                // Procesar imagen2 si existe
                if (isset($_FILES['imagen2']) && $_FILES['imagen2']['error'] === 0) {
                    $upload = $this->guardarImagen('imagen2');
                    if ($upload['success']) {
                        $imagen2 = $upload['relPath'];
                        // Eliminar imagen2 anterior
                        if (!empty($producto['imagen2'])) {
                            $oldPath = $producto['imagen2'];
                            if (str_starts_with($oldPath, 'public/tenants/')) {
                                @unlink(APP_ROOT . '/' . $oldPath);
                            } else {
                                @unlink(APP_ROOT . '/public/images/productos/' . $oldPath);
                            }
                        }
                    } else {
                        $advertenciasUpload[] = $upload['message'] ?: 'No se pudo subir la imagen 2';
                    }
                }
            
                // Procesar imagen3 si existe
                if (isset($_FILES['imagen3']) && $_FILES['imagen3']['error'] === 0) {
                    $upload = $this->guardarImagen('imagen3');
                    if ($upload['success']) {
                        $imagen3 = $upload['relPath'];
                        // Eliminar imagen3 anterior
                        if (!empty($producto['imagen3'])) {
                            $oldPath = $producto['imagen3'];
                            if (str_starts_with($oldPath, 'public/tenants/')) {
                                @unlink(APP_ROOT . '/' . $oldPath);
                            } else {
                                @unlink(APP_ROOT . '/public/images/productos/' . $oldPath);
                            }
                        }
                    } else {
                        $advertenciasUpload[] = $upload['message'] ?: 'No se pudo subir la imagen 3';
                    }
                }
            
            if (!$nombre || !$categoria_id || !$subcategoria_id || !$precio) {
                $_SESSION['error'] = 'Por favor complete todos los campos obligatorios';
            } else {
                $data = array(
                    'categoria_id' => $categoria_id,
                    'subcategoria_id' => $subcategoria_id,
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'precio' => $precio,
                    'stock' => $stock,
                        'imagen' => $imagen,
                        'imagen2' => $imagen2,
                        'imagen3' => $imagen3
                );
                
                if ($this->productoModel->actualizar($id, $data)) {
                    if (!empty($advertenciasUpload)) {
                        $_SESSION['success'] = 'Producto actualizado con advertencias: ' . implode(' | ', array_unique($advertenciasUpload));
                    } else {
                        $_SESSION['success'] = 'Producto actualizado exitosamente';
                    }
                    header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=productos');
                    exit;
                } else {
                    $_SESSION['error'] = 'Error al actualizar el producto';
                }
            }
        }
        
        editar_producto_vista:
        include APP_ROOT . '/app/views/admin/editar_producto.php';
    }
    
    /**
     * Eliminar producto
     */
    public function eliminarProducto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if ($this->productoModel->eliminar($id)) {
            $_SESSION['success'] = 'Producto eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el producto';
        }
        
        header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=productos');
        exit;
    }
    
    /**
     * Gestión de pedidos
     */
    public function pedidos() {
        // Obtener filtros
        $cliente = isset($_GET['cliente']) ? sanitizar($_GET['cliente']) : '';
        $desde = isset($_GET['desde']) ? sanitizar($_GET['desde']) : '';
        $hasta = isset($_GET['hasta']) ? sanitizar($_GET['hasta']) : '';
        $estado = isset($_GET['estado']) ? sanitizar($_GET['estado']) : '';
        
        // Construir query con filtros (SCOPED por tenant)
        $sql = "SELECT p.*, c.nombre as cliente_nombre 
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                WHERE p.tenant_id = ?";
        
        $params = array();
        $types = "i"; // tenant_id es integer
        $params[] = TENANT_ID;
        
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
        
        $pedidos = obtenerFilas($sql, $types, $params);
        
        // Obtener detalles de cada pedido para mostrar productos
        foreach ($pedidos as &$pedido) {
            $sql_detalles = "SELECT pd.*, pr.nombre, pr.imagen 
                             FROM pedido_detalles pd 
                             JOIN productos pr ON pd.producto_id = pr.id 
                             WHERE pd.pedido_id = ?";
            $pedido['detalles'] = obtenerFilas($sql_detalles, "i", array(&$pedido['id']));
        }
        unset($pedido);
        
        include APP_ROOT . '/app/views/admin/pedidos.php';
    }
    
    /**
     * Ver detalle de pedido
     */
    public function verPedido() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Debug: Log de tenant actual
        error_log("verPedido - Pedido ID: $id, TENANT_ID: " . (defined('TENANT_ID') ? TENANT_ID : 'NO DEFINIDO') . ", TENANT_SLUG: " . (defined('TENANT_SLUG') ? TENANT_SLUG : 'NO DEFINIDO'));
        
        $pedido = $this->pedidoModel->obtenerPorId($id);
        
        // Debug: Log de resultado
        error_log("verPedido - Pedido encontrado: " . ($pedido ? 'SÍ' : 'NO'));
        if ($pedido) {
            error_log("verPedido - Detalles count: " . count($pedido['detalles']));
        }
        
        $historial = $this->pedidoModel->obtenerHistorial($id);
        
        if (!$pedido) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }
        
        // Los detalles ya están cargados en el modelo desde obtenerPorId()
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $estado = sanitizar($_POST['estado'] ?? '');
            $notas_admin = sanitizar($_POST['notas_admin'] ?? '');
            
            error_log("Intentando cambiar estado pedido #$id a: $estado");
            error_log("Tenant ID: " . $_SESSION['tenant_id']);
            
            $resultado = $this->pedidoModel->actualizarEstado($id, $estado, $notas_admin);
            error_log("Resultado actualizarEstado: " . ($resultado ? 'true' : 'false'));
            
            if ($resultado) {
                $_SESSION['success'] = 'Pedido actualizado exitosamente';
                header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=verPedido&id=' . $id);
                exit;
            } else {
                $_SESSION['error'] = 'Error al actualizar el pedido';
                header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=verPedido&id=' . $id);
                exit;
            }
        }
        
        include APP_ROOT . '/app/views/admin/ver_pedido.php';
    }
    
    /**
     * Guardar imagen subida
     */
    private function guardarImagen($fieldName) {
        // Usa helper multi-tenant para guardar en carpeta del tenant
        $maxMb = MAX_UPLOAD_SIZE > 0 ? ceil(MAX_UPLOAD_SIZE / (1024 * 1024)) : 5;
        return move_uploaded_file_tenant($fieldName, 'images', ALLOWED_EXTENSIONS, $maxMb);
    }
    
    /**
     * Gestión de clientes
     */
    public function clientes() {
        $busqueda = isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : '';
        
        if (!empty($busqueda)) {
            $sql = "SELECT * FROM clientes WHERE tenant_id = ? AND (nombre LIKE ? OR email LIKE ? OR telefono LIKE ?) ORDER BY fecha_registro DESC";
            $termino = "%$busqueda%";
            $clientes = obtenerFilasScoped($sql, "sss", array($termino, $termino, $termino));
        } else {
            $sql = "SELECT * FROM clientes WHERE tenant_id = ? ORDER BY fecha_registro DESC";
            $clientes = obtenerFilasScoped($sql);
        }
        
        include APP_ROOT . '/app/views/admin/clientes.php';
    }

    /**
     * Formulario para crear cliente
     */
    public function crearCliente() {
        include APP_ROOT . '/app/views/admin/crear_cliente.php';
    }

    /**
     * Guardar nuevo cliente (POST)
     */
    public function guardarCliente() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $usuario = sanitizar($_POST['usuario'] ?? '');
        $nombre = sanitizar($_POST['nombre'] ?? '');
        $email = sanitizar($_POST['email'] ?? '');
        $telefono = sanitizar($_POST['telefono'] ?? '');
        $whatsapp = sanitizar($_POST['whatsapp'] ?? '');
        $ciudad = sanitizar($_POST['ciudad'] ?? '');
        $direccion = sanitizar($_POST['direccion'] ?? '');

        if (empty($usuario) || empty($nombre)) {
            $_SESSION['error'] = 'Usuario y nombre son requeridos';
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=crearCliente');
            exit;
        }

        // Insertar cliente asociado al tenant actual
        $sql = "INSERT INTO clientes (tenant_id, usuario, nombre, email, telefono, whatsapp, ciudad, direccion, fecha_registro, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)";
        $ok = ejecutarConsulta($sql, "isssssss", array(TENANT_ID, $usuario, $nombre, $email, $telefono, $whatsapp, $ciudad, $direccion));

        if ($ok) {
            $_SESSION['success'] = 'Cliente creado correctamente';
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=clientes');
        } else {
            $_SESSION['error'] = 'Error al crear cliente';
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=crearCliente');
        }
        exit;
    }
    
    /**
     * Ver detalle de cliente
     */
    public function verCliente() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Restringir por tenant
        $sql = "SELECT * FROM clientes WHERE tenant_id = ? AND id = ?";
        $cliente = obtenerFilaScoped($sql, "i", array($id));
        
        if (!$cliente) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }
        
        include APP_ROOT . '/app/views/admin/ver_cliente.php';
    }

    /**
     * Editar cliente (formulario)
     */
    public function editarCliente() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $sql = "SELECT * FROM clientes WHERE tenant_id = ? AND id = ?";
        $cliente = obtenerFilaScoped($sql, "i", array($id));
        if (!$cliente) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }
        include APP_ROOT . '/app/views/admin/editar_cliente.php';
    }

    /**
     * Actualizar cliente (POST)
     */
    public function actualizarCliente() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $nombre = sanitizar($_POST['nombre'] ?? '');
        $email = sanitizar($_POST['email'] ?? '');
        $telefono = sanitizar($_POST['telefono'] ?? '');
        $whatsapp = sanitizar($_POST['whatsapp'] ?? '');
        $ciudad = sanitizar($_POST['ciudad'] ?? '');
        $direccion = sanitizar($_POST['direccion'] ?? '');
        $calificacion = isset($_POST['calificacion']) ? (int)$_POST['calificacion'] : 0;
        if ($calificacion < 0) $calificacion = 0;
        if ($calificacion > 5) $calificacion = 5;

        // Validar existencia y pertenencia
        $existe = validarPerteneceATenant('clientes', $id);
        if (!$existe) {
            $_SESSION['error'] = 'Cliente no encontrado';
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=clientes');
            exit;
        }

        $sql = "UPDATE clientes SET nombre = ?, email = ?, telefono = ?, whatsapp = ?, ciudad = ?, direccion = ?, calificacion = ? WHERE tenant_id = ? AND id = ?";
        // Enviamos tenant_id explícitamente para respetar el orden de placeholders (no usar scoped aquí)
        $ok = ejecutarConsulta($sql, "ssssssiii", array($nombre, $email, $telefono, $whatsapp, $ciudad, $direccion, $calificacion, TENANT_ID, $id));
        if ($ok) {
            $_SESSION['success'] = 'Cliente actualizado correctamente';
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=verCliente&id=' . $id);
        } else {
            $_SESSION['error'] = 'No fue posible actualizar el cliente';
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=editarCliente&id=' . $id);
        }
        exit;
    }
    
    /**
     * Desactivar/Activar cliente
     */
    public function desactivarCliente() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        $sql = "SELECT activo FROM clientes WHERE tenant_id = ? AND id = ?";
        $cliente = obtenerFilaScoped($sql, "i", array($id));
        
        if ($cliente) {
            $nuevo_estado = $cliente['activo'] ? 0 : 1;
            $sql_update = "UPDATE clientes SET activo = ? WHERE tenant_id = ? AND id = ?";
            
            // Ejecutar con tenant scope
            if (ejecutarConsultaScoped($sql_update, "ii", array($nuevo_estado, $id))) {
                $_SESSION['success'] = 'Cliente actualizado exitosamente';
            } else {
                $_SESSION['error'] = 'Error al actualizar el cliente';
            }
        }
        
        header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=clientes');
        exit;
    }

    /**
     * Actualizar estado de preparación de un producto en pedido
     */
    public function actualizarEstadoPreparacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $pedido_id = isset($_POST['pedido_id']) ? (int)$_POST['pedido_id'] : 0;
        $detalle_id = isset($_POST['detalle_id']) ? (int)$_POST['detalle_id'] : 0;
        $estado = isset($_POST['estado']) ? sanitizar($_POST['estado']) : '';
        $cant_entregada = isset($_POST['cantidad_entregada']) ? (int)$_POST['cantidad_entregada'] : null;

        if (!$pedido_id || !$detalle_id || !in_array($estado, ['pendiente', 'listo'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Parámetros inválidos']);
            return;
        }

        global $conn;
        // Si viene cantidad_entregada, ajustar estado en función de la cantidad
        if ($cant_entregada !== null) {
            // Obtener cantidad pedida para calcular límites
            $sql_info = "SELECT cantidad FROM pedido_detalles WHERE id = ? AND pedido_id = ?";
            $info = obtenerFila($sql_info, "ii", array(&$detalle_id, &$pedido_id));
            if ($info) {
                if ($cant_entregada < 0) $cant_entregada = 0;
                if ($cant_entregada > (int)$info['cantidad']) $cant_entregada = (int)$info['cantidad'];
                $estado = ($cant_entregada >= (int)$info['cantidad']) ? 'listo' : 'pendiente';
            }
            $sql = "UPDATE pedido_detalles SET estado_preparacion = ?, cantidad_entregada = ? WHERE id = ? AND pedido_id = ?";
            $ok = ejecutarConsulta($sql, "siii", array(&$estado, &$cant_entregada, &$detalle_id, &$pedido_id));
        } else {
            $sql = "UPDATE pedido_detalles SET estado_preparacion = ? WHERE id = ? AND pedido_id = ?";
            $ok = ejecutarConsulta($sql, "sii", array(&$estado, &$detalle_id, &$pedido_id));
        }
        
        if ($ok) {
            // Calcular progreso
            $sql_total = "SELECT 
                            COUNT(*) as total, 
                            SUM(CASE WHEN estado_preparacion = 'listo' THEN 1 ELSE 0 END) as listos,
                            SUM(cantidad) as total_cant,
                            SUM(COALESCE(cantidad_entregada, CASE WHEN estado_preparacion='listo' THEN cantidad ELSE 0 END)) as total_entregada
                          FROM pedido_detalles WHERE pedido_id = ?";
            $result = obtenerFila($sql_total, "i", array(&$pedido_id));
            
            $porcentaje = $result['total'] > 0 ? round(($result['listos'] / $result['total']) * 100) : 0;
            $faltantes = max(0, (int)$result['total_cant'] - (int)$result['total_entregada']);
            
            echo json_encode([
                'success' => true,
                'porcentaje' => $porcentaje,
                'listos' => $result['listos'],
                'total' => $result['total'],
                'total_cant' => (int)$result['total_cant'],
                'total_entregada' => (int)$result['total_entregada'],
                'faltantes' => $faltantes
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar']);
        }
        exit;
    }

    /**
     * Actualizar estado del pedido (empaquetado/verificado)
     */
    public function actualizarEstadoPedido() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $pedido_id = isset($_POST['pedido_id']) ? (int)$_POST['pedido_id'] : 0;
        $estado = isset($_POST['estado']) ? sanitizar($_POST['estado']) : '';
        $observaciones = isset($_POST['observaciones']) ? sanitizar($_POST['observaciones']) : '';

        if (!$pedido_id || !in_array($estado, ['empaquetado', 'verificado', 'en_reparto', 'alistado'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Parámetros inválidos']);
            return;
        }

        global $conn;

        $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
        if (!ejecutarConsulta($sql, "si", array(&$estado, &$pedido_id))) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar estado']);
            exit;
        }

        $sql_historial = "INSERT INTO pedido_historial (pedido_id, estado, nota, fecha) VALUES (?, ?, ?, NOW())";
        $nota = $observaciones ?: "Estado cambiado a " . $estado;
        ejecutarConsulta($sql_historial, "iss", array(&$pedido_id, &$estado, &$nota));

        echo json_encode(['success' => true]);
        exit;
    }
    
    /**
     * Configuración del tenant
     */
    public function configuracion() {
        global $conn;
        
        // Obtener datos actuales del tenant
        $sql = "SELECT * FROM tenants WHERE id = ?";
        $tenant = obtenerFila($sql, "i", array(&$_SESSION['tenant_id']));
        
        if (!$tenant) {
            $_SESSION['error'] = 'Error al cargar configuración';
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=inicio');
            exit;
        }
        
        include APP_ROOT . '/app/views/admin/configuracion.php';
    }
    
    /**
     * Actualizar configuración del tenant
     */
    public function actualizarConfiguracion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        $titulo_empresa = sanitizar($_POST['titulo_empresa'] ?? '');
        $whatsapp_phone = sanitizar($_POST['whatsapp_phone'] ?? '');
        
        // Agregar siempre el prefijo +57 al número de WhatsApp
        if (!empty($whatsapp_phone)) {
            $whatsapp_phone = '+57' . preg_replace('/[^0-9\s]/', '', $whatsapp_phone);
        }
        
        $tema = sanitizar($_POST['tema'] ?? 'claro');
        $tema_color = sanitizar($_POST['tema_color'] ?? 'azul');
        
        $sql = "UPDATE tenants SET titulo_empresa = ?, whatsapp_phone = ?, tema = ?, tema_color = ?, updated_at = NOW() WHERE id = ?";
        
        if (ejecutarConsulta($sql, "ssssi", [$titulo_empresa, $whatsapp_phone, $tema, $tema_color, $_SESSION['tenant_id']])) {
            // Recargar datos completos del tenant desde BD
            global $conn;
            $sql_tenant = "SELECT * FROM tenants WHERE id = ?";
            $tenant_actualizado = obtenerFila($sql_tenant, "i", array(&$_SESSION['tenant_id']));
            
            if ($tenant_actualizado) {
                $_SESSION['tenant_data'] = $tenant_actualizado;
            }
            
            $_SESSION['success'] = 'Configuración actualizada exitosamente. Los cambios se verán reflejados en la tienda.';
        } else {
            $_SESSION['error'] = 'Error al actualizar la configuración';
        }
        
        header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=configuracion');
        exit;
    }
    
    /**
     * Ver perfil del administrador
     */
    public function miPerfil() {
        global $conn;
        
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $usuario = obtenerFila($sql, "i", array(&$_SESSION['usuario_id']));
        
        if (!$usuario) {
            $_SESSION['error'] = 'Error al cargar perfil';
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=inicio');
            exit;
        }
        
        include APP_ROOT . '/app/views/admin/mi_perfil.php';
    }
    
    /**
     * Actualizar perfil del administrador
     */
    public function actualizarPerfil() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        global $conn;
        $accion = $_POST['accion'] ?? '';
        
        if ($accion === 'info') {
            // Actualizar información personal
            $usuario = sanitizar($_POST['usuario'] ?? '');
            $nombre = sanitizar($_POST['nombre'] ?? '');
            $email = sanitizar($_POST['email'] ?? '');
            
            if (empty($usuario) || empty($nombre) || empty($email)) {
                $_SESSION['error'] = 'Todos los campos son requeridos';
                header('Location: ' . APP_URL . '/index.php?controller=admin&action=miPerfil');
                exit;
            }
            
            // Verificar si el usuario ya existe (excepto el actual)
            $sql = "SELECT id FROM usuarios WHERE usuario = ? AND id != ? AND tenant_id = ?";
            if (obtenerFila($sql, "sii", [$usuario, $_SESSION['usuario_id'], $_SESSION['tenant_id']])) {
                $_SESSION['error'] = 'El nombre de usuario ya está en uso';
                header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=miPerfil');
                exit;
            }
            
            // Verificar si el email ya existe (excepto el actual)
            $sql = "SELECT id FROM usuarios WHERE email = ? AND id != ? AND tenant_id = ?";
            if (obtenerFila($sql, "sii", [$email, $_SESSION['usuario_id'], $_SESSION['tenant_id']])) {
                $_SESSION['error'] = 'El correo electrónico ya está en uso';
                header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=miPerfil');
                exit;
            }
            
            // Actualizar
            $sql = "UPDATE usuarios SET usuario = ?, nombre = ?, email = ? WHERE id = ?";
            
            if (ejecutarConsulta($sql, "sssi", [$usuario, $nombre, $email, $_SESSION['usuario_id']])) {
                $_SESSION['usuario'] = $usuario;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['success'] = 'Información actualizada exitosamente';
            } else {
                $_SESSION['error'] = 'Error al actualizar la información';
            }
            
        } elseif ($accion === 'password') {
            // Cambiar contraseña
            $password_actual = $_POST['password_actual'] ?? '';
            $password_nueva = $_POST['password_nueva'] ?? '';
            $password_confirmar = $_POST['password_confirmar'] ?? '';
            
            if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
                $_SESSION['error'] = 'Todos los campos son requeridos';
                header('Location: ' . APP_URL . '/index.php?controller=admin&action=miPerfil');
                exit;
            }
            
            if (strlen($password_nueva) < 6) {
                $_SESSION['error'] = 'La nueva contraseña debe tener al menos 6 caracteres';
                header('Location: ' . APP_URL . '/index.php?controller=admin&action=miPerfil');
                exit;
            }
            
            if ($password_nueva !== $password_confirmar) {
                $_SESSION['error'] = 'Las contraseñas no coinciden';
                header('Location: ' . APP_URL . '/index.php?controller=admin&action=miPerfil');
                exit;
            }
            
            // Verificar contraseña actual
            $hash_actual = hash('sha256', $password_actual);
            $sql = "SELECT id FROM usuarios WHERE id = ? AND password = ?";
            if (!obtenerFila($sql, "is", [$_SESSION['usuario_id'], $hash_actual])) {
                $_SESSION['error'] = 'La contraseña actual es incorrecta';
                header('Location: ' . APP_URL . '/index.php?controller=admin&action=miPerfil');
                exit;
            }
            
            // Actualizar contraseña
            $hash_nueva = hash('sha256', $password_nueva);
            $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
            
            if (ejecutarConsulta($sql, "si", [$hash_nueva, $_SESSION['usuario_id']])) {
                $_SESSION['success'] = 'Contraseña actualizada exitosamente';
            } else {
                $_SESSION['error'] = 'Error al actualizar la contraseña';
            }
        }
        
        header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=miPerfil');
        exit;
    }

    /**
     * Actualizar logo del tenant desde Mi Perfil
     */
    public function actualizarLogoTenant() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        // Verificar que se subió un archivo
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Error al subir el archivo';
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=miPerfil');
            exit;
        }

        $file = $_FILES['logo'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed)) {
            $_SESSION['error'] = 'Formato de archivo no permitido';
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=miPerfil');
            exit;
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            $_SESSION['error'] = 'El archivo es demasiado grande (máximo 5MB)';
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=miPerfil');
            exit;
        }

        // Subir logo a Cloudinary
        if (!function_exists('uploadToCloudinary')) {
            require_once APP_ROOT . '/config/cloudinary.php';
        }
        $upload = uploadToCloudinary($file['tmp_name'], 'tenants/logos', 'logo_tenant_' . TENANT_ID);
        if (!$upload['success']) {
            $_SESSION['error'] = 'Error al subir el logo: ' . $upload['message'];
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=miPerfil');
            exit;
        }

        // Actualizar en la base de datos
        $logoPath = $upload['url'];
        $tenantId = TENANT_ID;
        $sql = "UPDATE tenants SET logo = ? WHERE id = ?";
        
        if (ejecutarConsulta($sql, "si", [$logoPath, $tenantId])) {
            $_SESSION['tenant_data']['logo'] = $logoPath;
            $_SESSION['success'] = 'Logo actualizado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al actualizar el logo en la base de datos';
        }

        header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/index.php?controller=admin&action=miPerfil');
        exit;
    }

    /**
     * Generar cuenta de cobro PDF de un pedido
     */
    public function generarFacturaPDF() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID de pedido inválido';
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=pedidos');
            exit;
        }
        
        // Obtener datos del pedido
        $pedido = $this->pedidoModel->obtenerPorId($id);
        
        if (!$pedido) {
            $_SESSION['error'] = 'Pedido no encontrado';
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=pedidos');
            exit;
        }
        
        // Obtener detalles
        global $conn;
        $sql_detalles = "SELECT pd.*, pr.nombre 
                         FROM pedido_detalles pd 
                         JOIN productos pr ON pd.producto_id = pr.id 
                         WHERE pd.pedido_id = ?";
        $detalles = obtenerFilas($sql_detalles, "i", array($id));
        
        // Obtener datos del tenant
        $tenant_id = getTenantId();
        $sql_tenant = "SELECT * FROM tenants WHERE id = ?";
        $tenant = obtenerFilaScoped($sql_tenant, "", array());
        
        if (!$tenant) {
            $_SESSION['error'] = 'Error al obtener datos del tenant';
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=verPedido&id=' . $id);
            exit;
        }
        
        try {
            // Generar PDF
            $rutaPDF = FacturaPDF::generar($pedido, $detalles, $tenant);
            
            // Redirigir al PDF
            header('Location: ' . $rutaPDF);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al generar la cuenta de cobro: ' . $e->getMessage();
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=verPedido&id=' . $id);
            exit;
        }
    }

    /**
     * Gestión de Cuentas de Cobro
     */
    public function facturas() {
        // Obtener parámetros de filtro
        $busqueda = isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : '';
        $desde = isset($_GET['desde']) ? sanitizar($_GET['desde']) : '';
        $hasta = isset($_GET['hasta']) ? sanitizar($_GET['hasta']) : '';
        $estado = isset($_GET['estado']) ? sanitizar($_GET['estado']) : '';
        
        global $conn;
        $tenant_id = getTenantId();
        
        // Construir consulta con filtros
        $sql = "SELECT p.*, c.nombre as cliente_nombre, c.whatsapp
                FROM pedidos p 
                LEFT JOIN clientes c ON p.cliente_id = c.id AND p.tenant_id = c.tenant_id
                WHERE p.tenant_id = ?";
        
        $params = array($tenant_id);
        $types = "i";
        
        if (!empty($busqueda)) {
            // Buscar por número de cuenta de cobro o nombre de cliente
            $numero_limpio = str_replace('#', '', $busqueda);
            if (is_numeric($numero_limpio)) {
                // Si es número, buscar por ID o por nombre que contenga ese número
                $sql .= " AND (p.id = ? OR c.nombre LIKE ?)";
                $params[] = (int)$numero_limpio;
                $params[] = "%$busqueda%";
                $types .= "is";
            } else {
                // Si no es número, solo buscar por nombre
                $sql .= " AND c.nombre LIKE ?";
                $params[] = "%$busqueda%";
                $types .= "s";
            }
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
        
        if (!empty($params)) {
            $facturas = obtenerFilas($sql, $types, $params);
        } else {
            $facturas = obtenerFilas($sql);
        }
        
        include APP_ROOT . '/app/views/admin/facturas.php';
    }

    /**
     * Generar cuenta de cobro Excel de un pedido
     */
    public function generarFacturaExcel() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID de pedido inválido';
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=pedidos');
            exit;
        }
        
        // Obtener datos del pedido
        $pedido = $this->pedidoModel->obtenerPorId($id);
        
        if (!$pedido) {
            $_SESSION['error'] = 'Pedido no encontrado';
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=pedidos');
            exit;
        }
        
        // Obtener detalles
        global $conn;
        $sql_detalles = "SELECT pd.*, pr.nombre 
                         FROM pedido_detalles pd 
                         JOIN productos pr ON pd.producto_id = pr.id 
                         WHERE pd.pedido_id = ?";
        $detalles = obtenerFilas($sql_detalles, "i", array($id));
        
        // Obtener datos del tenant
        $tenant_id = getTenantId();
        $sql_tenant = "SELECT * FROM tenants WHERE id = ?";
        $tenant = obtenerFilaScoped($sql_tenant, "", array());
        
        if (!$tenant) {
            $_SESSION['error'] = 'Error al obtener datos del tenant';
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=verPedido&id=' . $id);
            exit;
        }
        
        try {
            // Generar Excel
            $rutaExcel = FacturaExcel::generar($pedido, $detalles, $tenant);
            
            // Redirigir al Excel
            header('Location: ' . $rutaExcel);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al generar factura Excel: ' . $e->getMessage();
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=verPedido&id=' . $id);
            exit;
        }
    }

    /**
     * Generar reporte de productos en PDF
     */
    public function reporteProductosPDF() {
        $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';
        
        // Obtener productos según filtro
        $productos = $this->obtenerProductosFiltrados($filtro);
        
        // Obtener datos del tenant
        $tenant_id = getTenantId();
        $sql_tenant = "SELECT * FROM tenants WHERE id = ?";
        $tenant = obtenerFilaScoped($sql_tenant, "", array());
        
        if (!$tenant) {
            $_SESSION['error'] = 'Error al obtener datos del tenant';
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=productos');
            exit;
        }
        
        try {
            $rutaPDF = ProductosPDF::generar($productos, $tenant, $filtro);
            header('Location: ' . $rutaPDF);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al generar reporte PDF: ' . $e->getMessage();
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=productos');
            exit;
        }
    }

    /**
     * Generar reporte de productos en Excel
     */
    public function reporteProductosExcel() {
        $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';
        
        // Obtener productos según filtro
        $productos = $this->obtenerProductosFiltrados($filtro);
        
        // Obtener datos del tenant
        $tenant_id = getTenantId();
        $sql_tenant = "SELECT * FROM tenants WHERE id = ?";
        $tenant = obtenerFilaScoped($sql_tenant, "", array());
        
        if (!$tenant) {
            $_SESSION['error'] = 'Error al obtener datos del tenant';
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=productos');
            exit;
        }
        
        try {
            $rutaExcel = ProductosExcel::generar($productos, $tenant, $filtro);
            header('Location: ' . $rutaExcel);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al generar reporte Excel: ' . $e->getMessage();
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=productos');
            exit;
        }
    }

    /**
     * Obtener productos filtrados
     */
    private function obtenerProductosFiltrados($filtro) {
        $where = "";
        $params = array();
        $types = "";
        
        if ($filtro === 'activo') {
            $where = " AND estado = 'activo'";
        } elseif ($filtro === 'inactivo') {
            $where = " AND estado = 'inactivo'";
        }
        
        $sql = "SELECT p.*, c.nombre as categoria 
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE 1=1" . $where . "
                ORDER BY p.id DESC";
        
        return obtenerFilas($sql);
    }

    /**
     * Generar reporte de pedidos en PDF
     */
    public function reportePedidosPDF() {
        // Obtener filtros
        $cliente = isset($_GET['cliente']) ? sanitizar($_GET['cliente']) : '';
        $desde = isset($_GET['desde']) ? sanitizar($_GET['desde']) : '';
        $hasta = isset($_GET['hasta']) ? sanitizar($_GET['hasta']) : '';
        $estado = isset($_GET['estado']) ? sanitizar($_GET['estado']) : '';
        
        // Obtener pedidos filtrados
        $pedidos = $this->obtenerPedidosFiltrados($cliente, $desde, $hasta, $estado);
        
        // Obtener detalles de cada pedido
        foreach ($pedidos as &$pedido) {
            $sql_detalles = "SELECT pd.*, pr.nombre 
                             FROM pedido_detalles pd 
                             JOIN productos pr ON pd.producto_id = pr.id 
                             WHERE pd.pedido_id = ?";
            $pedido['detalles'] = obtenerFilas($sql_detalles, "i", array(&$pedido['id']));
        }
        unset($pedido);
        
        // Obtener datos del tenant
        $tenant_id = getTenantId();
        $sql_tenant = "SELECT * FROM tenants WHERE id = ?";
        $tenant = obtenerFilaScoped($sql_tenant, "", array());
        
        if (!$tenant) {
            $_SESSION['error'] = 'Error al obtener datos del tenant';
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=pedidos');
            exit;
        }
        
        // Preparar filtros para el reporte
        $filtros = [
            'cliente' => $cliente,
            'desde' => $desde,
            'hasta' => $hasta,
            'estado' => $estado
        ];
        
        try {
            $rutaPDF = PedidosPDF::generar($pedidos, $tenant, $filtros);
            header('Location: ' . $rutaPDF);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al generar reporte PDF: ' . $e->getMessage();
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=pedidos');
            exit;
        }
    }

    /**
     * Generar reporte de pedidos en Excel
     */
    public function reportePedidosExcel() {
        // Obtener filtros
        $cliente = isset($_GET['cliente']) ? sanitizar($_GET['cliente']) : '';
        $desde = isset($_GET['desde']) ? sanitizar($_GET['desde']) : '';
        $hasta = isset($_GET['hasta']) ? sanitizar($_GET['hasta']) : '';
        $estado = isset($_GET['estado']) ? sanitizar($_GET['estado']) : '';
        
        // Obtener pedidos filtrados
        $pedidos = $this->obtenerPedidosFiltrados($cliente, $desde, $hasta, $estado);
        
        // Obtener detalles de cada pedido
        foreach ($pedidos as &$pedido) {
            $sql_detalles = "SELECT pd.*, pr.nombre 
                             FROM pedido_detalles pd 
                             JOIN productos pr ON pd.producto_id = pr.id 
                             WHERE pd.pedido_id = ?";
            $pedido['detalles'] = obtenerFilas($sql_detalles, "i", array(&$pedido['id']));
        }
        unset($pedido);
        
        // Obtener datos del tenant
        $tenant_id = getTenantId();
        $sql_tenant = "SELECT * FROM tenants WHERE id = ?";
        $tenant = obtenerFilaScoped($sql_tenant, "", array());
        
        if (!$tenant) {
            $_SESSION['error'] = 'Error al obtener datos del tenant';
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=pedidos');
            exit;
        }
        
        // Preparar filtros para el reporte
        $filtros = [
            'cliente' => $cliente,
            'desde' => $desde,
            'hasta' => $hasta,
            'estado' => $estado
        ];
        
        try {
            $rutaExcel = PedidosExcel::generar($pedidos, $tenant, $filtros);
            header('Location: ' . $rutaExcel);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al generar reporte Excel: ' . $e->getMessage();
            header('Location: ' . APP_URL . '/index.php?controller=admin&action=pedidos');
            exit;
        }
    }

    /**
     * Obtener pedidos filtrados
     */
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
}

?>
