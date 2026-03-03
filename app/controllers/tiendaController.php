<?php
/**
 * Controlador Tienda - Frontend de la tienda
 */
class TiendaController {
    private $categoriaModel;
    private $subcategoriaModel;
    private $productoModel;
    private $clienteModel;
    private $pedidoModel;
    
    /**
     * Construye array de categorías con sus subcategorías para menús desplegables
     */
    private function obtenerCategoriasConSubcategorias() {
        $categorias = $this->categoriaModel->obtenerTodas();
        foreach ($categorias as &$cat) {
            $cat['subcategorias'] = $this->subcategoriaModel->obtenerPorCategoria($cat['id']);
        }
        unset($cat); // romper referencia
        return $categorias;
    }
    
    public function __construct() {
        require_once APP_ROOT . '/app/models/CategoriaModel.php';
        require_once APP_ROOT . '/app/models/SubcategoriaModel.php';
        require_once APP_ROOT . '/app/models/ProductoModel.php';
        require_once APP_ROOT . '/app/models/ClienteModel.php';
        require_once APP_ROOT . '/app/models/PedidoModel.php';
        
        global $conn;
        $this->categoriaModel = new CategoriaModel($conn);
        $this->subcategoriaModel = new SubcategoriaModel($conn);
        $this->productoModel = new ProductoModel($conn);
        $this->clienteModel = new ClienteModel($conn);
        $this->pedidoModel = new PedidoModel($conn);
    }
    
    /**
     * Página de inicio - Mostrar todas las categorías y productos destacados
     */
    public function inicio() {
        $categorias = $this->obtenerCategoriasConSubcategorias();
        $productos = $this->productoModel->obtenerTodos();
        
        // Aplicar filtros y ordenamiento
        $productos = $this->aplicarFiltrosYOrdenamiento($productos);
        
        // Paginación
        $total_productos = count($productos);
        $productos_por_pagina = 24;
        $pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $total_paginas = ceil($total_productos / $productos_por_pagina);
        $pagina_actual = min($pagina_actual, $total_paginas);
        
        $offset = ($pagina_actual - 1) * $productos_por_pagina;
        $productos = array_slice($productos, $offset, $productos_por_pagina);
        
        include APP_ROOT . '/app/views/tienda/inicio.php';
    }
    
    /**
     * Ver categoría con sus productos
     */
    public function categoria() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        $categoria = $this->categoriaModel->obtenerConSubcategorias($id);
        if (!$categoria) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }
        
        $productos = $this->productoModel->obtenerPorCategoria($id);
        $productos = $this->aplicarFiltrosYOrdenamiento($productos);
        
        // Paginación
        $total_productos = count($productos);
        $productos_por_pagina = 24;
        $pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $total_paginas = ceil($total_productos / $productos_por_pagina);
        $pagina_actual = min($pagina_actual, $total_paginas);
        
        $offset = ($pagina_actual - 1) * $productos_por_pagina;
        $productos = array_slice($productos, $offset, $productos_por_pagina);
        
        $categorias = $this->obtenerCategoriasConSubcategorias();
        
        include APP_ROOT . '/app/views/tienda/categoria.php';
    }
    
    /**
     * Ver subcategoría con sus productos
     */
    public function subcategoria() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        $subcategoria = $this->subcategoriaModel->obtenerPorId($id);
        if (!$subcategoria) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }
        
        $productos = $this->productoModel->obtenerPorSubcategoria($id);
        $productos = $this->aplicarFiltrosYOrdenamiento($productos);
        
        // Paginación
        $total_productos = count($productos);
        $productos_por_pagina = 24;
        $pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $total_paginas = ceil($total_productos / $productos_por_pagina);
        $pagina_actual = min($pagina_actual, $total_paginas);
        
        $offset = ($pagina_actual - 1) * $productos_por_pagina;
        $productos = array_slice($productos, $offset, $productos_por_pagina);
        
        $categorias = $this->obtenerCategoriasConSubcategorias();
        
        include APP_ROOT . '/app/views/tienda/subcategoria.php';
    }
    
    /**
     * Ver detalle de producto
     */
    public function producto() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        $producto = $this->productoModel->obtenerPorId($id);
        if (!$producto) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }
        
        $categorias = $this->obtenerCategoriasConSubcategorias();
        
        include APP_ROOT . '/app/views/tienda/producto.php';
    }
    
    /**
     * Aplicar filtros y ordenamiento a productos
     */
    private function aplicarFiltrosYOrdenamiento($productos) {
        // Búsqueda por nombre
        $busqueda = isset($_GET['busqueda']) ? sanitizar($_GET['busqueda']) : '';
        if (!empty($busqueda)) {
            $productos = array_filter($productos, function($p) use ($busqueda) {
                return stripos($p['nombre'], $busqueda) !== false || stripos($p['descripcion'], $busqueda) !== false;
            });
        }
        
        // Ordenamiento
        $orden = isset($_GET['orden']) ? sanitizar($_GET['orden']) : 'default';
        switch ($orden) {
            case 'az':
                usort($productos, function($a, $b) {
                    return strcmp($a['nombre'], $b['nombre']);
                });
                break;
            case 'za':
                usort($productos, function($a, $b) {
                    return strcmp($b['nombre'], $a['nombre']);
                });
                break;
            case 'precio_menor':
                usort($productos, function($a, $b) {
                    return $a['precio'] - $b['precio'];
                });
                break;
            case 'precio_mayor':
                usort($productos, function($a, $b) {
                    return $b['precio'] - $a['precio'];
                });
                break;
        }
        
        return array_values($productos); // Resetear índices
    }
    
    /**
     * Construir URL con parámetros de paginación preservando otros parámetros
     */
    private function construirUrlPaginacion($pagina) {
        $url = $_SERVER['REQUEST_URI'];
        // Remover parámetro pagina si existe
        $url = preg_replace('/&?pagina=\d+/', '', $url);
        // Agregar nuevo parámetro
        $separator = strpos($url, '?') !== false ? '&' : '?';
        return $url . $separator . 'pagina=' . $pagina;
    }
    
    /**
     * Construir URL de paginación para categoría
     */
    private function construirUrlPaginacionCategoria($pagina, $categoriaId) {
        $url = APP_URL . '/' . sanitizar($_SESSION['tenant_slug']) . '/index.php?controller=tienda&action=categoria&id=' . $categoriaId;
        if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
            $url .= '&busqueda=' . urlencode($_GET['busqueda']);
        }
        if (isset($_GET['orden']) && !empty($_GET['orden'])) {
            $url .= '&orden=' . sanitizar($_GET['orden']);
        }
        $url .= '&pagina=' . $pagina;
        return $url;
    }
    
    /**
     * Construir URL de paginación para subcategoría
     */
    private function construirUrlPaginacionSubcategoria($pagina, $subcategoriaId) {
        $url = APP_URL . '/' . sanitizar($_SESSION['tenant_slug']) . '/index.php?controller=tienda&action=subcategoria&id=' . $subcategoriaId;
        if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
            $url .= '&busqueda=' . urlencode($_GET['busqueda']);
        }
        if (isset($_GET['orden']) && !empty($_GET['orden'])) {
            $url .= '&orden=' . sanitizar($_GET['orden']);
        }
        $url .= '&pagina=' . $pagina;
        return $url;
    }
    
    /**
     * Buscar productos
     */
    public function buscar() {
        $termino = isset($_GET['q']) ? trim($_GET['q']) : '';
        $productos = array();
        
        if (strlen($termino) >= 2) {
            $productos = $this->productoModel->buscar($termino);
        }
        
        $categorias = $this->obtenerCategoriasConSubcategorias();
        
        include APP_ROOT . '/app/views/tienda/buscar.php';
    }
    
    /**
     * Ver carrito
     */
    public function carrito() {
        $categorias = $this->obtenerCategoriasConSubcategorias();
        include APP_ROOT . '/app/views/tienda/carrito.php';
    }
    
    /**
     * Checkout - Procesar compra
     */
    public function checkout() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        try {
            global $conn;
            
            // Validar datos del formulario (obligatorios: nombre y WhatsApp)
            $nombre = sanitizar($_POST['nombre'] ?? '');
            $whatsapp_codigo = sanitizar($_POST['whatsapp_codigo'] ?? '');
            $whatsapp_numero = sanitizar($_POST['whatsapp'] ?? '');
            $whatsapp = $whatsapp_codigo . $whatsapp_numero;

            if (!$nombre || !$whatsapp_codigo || !$whatsapp_numero) {
                echo json_encode(['success' => false, 'message' => 'Por favor ingresa tu nombre y WhatsApp.']);
                return;
            }
            
            // Obtener session_id para uso posterior
            $session_id = session_id();
            
            // Obtener carrito desde JSON o desde base de datos
            $resultado = array();
            if (isset($_POST['carrito_json']) && !empty($_POST['carrito_json'])) {
                $resultado = json_decode($_POST['carrito_json'], true);
                if (!is_array($resultado)) {
                    throw new Exception('Formato de carrito inválido');
                }
            } else {
                // Fallback a base de datos
                $tenant_id = getTenantId();
                $sql = "SELECT c.*, p.precio, p.nombre as producto_nombre 
                    FROM carrito c 
                    JOIN productos p ON c.producto_id = p.id AND c.tenant_id = p.tenant_id
                    WHERE c.tenant_id = ? AND c.session_id = ?";
                $resultado = obtenerFilas($sql, "is", array($tenant_id, $session_id));
            }
            
            if (empty($resultado)) {
                echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
                return;
            }

        // Determinar cliente_id
        $cliente_id = null;
        $tenant_id_co = getTenantId();
        if (isset($_SESSION['cliente_id'])) {
            // Usuario logueado - actualizar su información
            $cliente_id = (int)$_SESSION['cliente_id'];
            $sql_update = "UPDATE clientes SET nombre = ?, whatsapp = ? WHERE tenant_id = ? AND id = ?";
            ejecutarConsulta($sql_update, "ssii", array($nombre, $whatsapp, $tenant_id_co, $cliente_id));
        } else {
            // Usuario invitado - buscar o crear cliente temporal
            $sql_check = "SELECT id FROM clientes WHERE tenant_id = ? AND whatsapp = ?";
            $cliente_existente = obtenerFila($sql_check, "is", array($tenant_id_co, $whatsapp));
            
            if ($cliente_existente) {
                $cliente_id = (int)$cliente_existente['id'];
                // Actualizar nombre si es diferente
                $sql_update = "UPDATE clientes SET nombre = ? WHERE tenant_id = ? AND id = ?";
                ejecutarConsulta($sql_update, "sii", array($nombre, $tenant_id_co, $cliente_id));
            } else {
                // Crear cliente temporal (invitado)
                $usuario = 'invitado_' . substr(md5($whatsapp . time()), 0, 10);
                $password_temp = hash('sha256', uniqid());
                $sql_insert = "INSERT INTO clientes (tenant_id, usuario, password, nombre, email, whatsapp, activo) VALUES (?, ?, ?, ?, '', ?, 1)";
                $stmtCliente = ejecutarConsulta($sql_insert, "", array($tenant_id_co, $usuario, $password_temp, $nombre, $whatsapp));
                if (!$stmtCliente) {
                    echo json_encode(['success' => false, 'message' => 'Error al crear cliente invitado (tenant=' . $tenant_id_co . ', whatsapp=' . $whatsapp . ')']);
                    return;
                }
                $cliente_id = (int)obtenerUltimoId();
                // Fallback: si obtenerUltimoId() falla, buscar el cliente recién insertado
                if (!$cliente_id) {
                    $fila = obtenerFila("SELECT id FROM clientes WHERE tenant_id = ? AND usuario = ?", "is", array($tenant_id_co, $usuario));
                    $cliente_id = $fila ? (int)$fila['id'] : 0;
                }
                if (!$cliente_id) {
                    echo json_encode(['success' => false, 'message' => 'No se pudo obtener id del cliente creado (usuario=' . $usuario . ')']);
                    return;
                }
            }
        }
        
        // Calcular total
        $total = 0;
        foreach ($resultado as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        // Crear pedido (sin pago en línea)
        $pedido_id = $this->pedidoModel->crear($cliente_id, $total, '');
        
        if (!$pedido_id) {
            echo json_encode(['success' => false, 'message' => 'Error al crear el pedido (cliente_id=' . $cliente_id . ', total=' . $total . ', tenant=' . getTenantId() . ')']);
            return;
        }
        
        // Agregar detalles del pedido
        foreach ($resultado as $item) {
            $subtotal = $item['precio'] * $item['cantidad'];
            $sql_detalle = "INSERT INTO pedido_detalles (tenant_id, pedido_id, producto_id, cantidad, precio_unitario, subtotal, cantidad_entregada) 
                            VALUES (?, ?, ?, ?, ?, ?, 0)";
            
            ejecutarConsultaScoped($sql_detalle, "iiddd", array(
                $pedido_id,
                $item['producto_id'],
                $item['cantidad'],
                $item['precio'],
                $subtotal
            ));
        }
        
        // Preparar datos para la cuenta de cobro - normalizar nombres de campos del carrito
        $productos_factura = array();
        foreach ($resultado as $item) {
            $productos_factura[] = array(
                'producto_nombre' => $item['nombre'] ?? $item['producto_nombre'] ?? 'Producto sin nombre',
                'precio' => $item['precio'],
                'cantidad' => $item['cantidad'],
                'subtotal' => $item['precio'] * $item['cantidad']
            );
        }
        
        // Generar cuenta de cobro HTML y crear enlace de WhatsApp
        $factura_url = $this->generarFacturaHtml($pedido_id, array(
            'nombre' => $nombre,
            'email' => '',
            'telefono' => '',
            'whatsapp' => $whatsapp,
            'ciudad' => '',
            'direccion' => ''
        ), $productos_factura, $total);

        // Generar enlace de WhatsApp
        $whatsapp_link = $this->generarEnlaceWhatsApp($whatsapp, $productos_factura, $total, $pedido_id, $factura_url);
        
        // Limpiar carrito de la base de datos
        $tenant_id = getTenantId();
        $sql_delete = "DELETE FROM carrito WHERE tenant_id = ? AND session_id = ?";
        ejecutarConsulta($sql_delete, "is", array($tenant_id, $session_id));
        
        // Limpiar carrito de la sesión
        if (isset($_SESSION['carrito'])) {
            unset($_SESSION['carrito']);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Pedido confirmado. Haz clic para recibir tu cuenta de cobro por WhatsApp.', 
            'pedido_id' => $pedido_id, 
            'factura_url' => $factura_url,
            'whatsapp_link' => $whatsapp_link
        ]);
        
        } catch (Exception $e) {
            error_log('Error en checkout: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al procesar el pedido: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Generar cuenta de cobro HTML del pedido y devolver URL pública
     */
    private function generarFacturaHtml($pedido_id, $cliente, $productos, $total) {
        // Crear carpeta si no existe
        $dir = APP_ROOT . '/public/invoices';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $fecha = date('Y-m-d H:i');
        $items_html = '';
        foreach ($productos as $item) {
            $items_html .= '<tr>' .
                '<td>' . htmlspecialchars($item['producto_nombre']) . '</td>' .
                '<td style="text-align:right">$' . number_format($item['precio'], 2) . '</td>' .
                '<td style="text-align:center">' . (int)$item['cantidad'] . '</td>' .
                '<td style="text-align:right">$' . number_format($item['subtotal'], 2) . '</td>' .
                '</tr>';
        }
        $html = "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Cuenta de Cobro Pedido #$pedido_id</title>" .
                "<style>body{font-family:Segoe UI,Arial,sans-serif;color:#333;padding:30px}h1{margin:0}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{border:1px solid #ddd;padding:10px}th{background:#f5f5f5;text-align:left} .totales{margin-top:20px;text-align:right;font-size:1.1rem}</style></head><body>" .
            "<h1>Cuenta de Cobro del Pedido #$pedido_id</h1><p>Fecha: $fecha</p>" .
                "<h3>Cliente</h3><p><strong>Nombre:</strong> " . htmlspecialchars($cliente['nombre']) . "<br>" .
                "<strong>Email:</strong> " . htmlspecialchars($cliente['email']) . "<br>" .
                "<strong>Teléfono:</strong> " . htmlspecialchars($cliente['telefono']) . "<br>" .
                "<strong>WhatsApp:</strong> " . htmlspecialchars($cliente['whatsapp']) . "<br>" .
                "<strong>Ciudad:</strong> " . htmlspecialchars($cliente['ciudad']) . "<br>" .
                "<strong>Dirección:</strong> " . htmlspecialchars($cliente['direccion']) . "</p>" .
                "<h3>Detalle</h3><table><thead><tr><th>Producto</th><th>Precio</th><th>Cant.</th><th>Subtotal</th></tr></thead><tbody>" .
                $items_html .
                "</tbody></table><div class='totales'><strong>Total:</strong> $" . number_format($total, 2) . "</div>" .
                "<p style='margin-top:30px'>Gracias por tu compra. Esta cuenta de cobro fue generada automáticamente.</p>" .
                "</body></html>";
        $file = $dir . "/pedido_" . $pedido_id . ".html";
        file_put_contents($file, $html);
        return APP_URL . "/public/invoices/pedido_" . $pedido_id . ".html";
    }

    /**
     * Generar enlace de WhatsApp con mensaje pre-llenado
     * Usa el WhatsApp del tenant actual (TENANT_WHATSAPP)
     */
    private function generarEnlaceWhatsApp($numero_cliente, $productos, $total, $pedido_id, $factura_url = '') {
        // Usar el número de WhatsApp del tenant (no del cliente)
        $numero = defined('TENANT_WHATSAPP') ? TENANT_WHATSAPP : $numero_cliente;
        
        // Obtener el nombre del tenant/empresa
        $nombre_empresa = defined('TENANT_NAME') ? TENANT_NAME : 'Tienda';
        
        // Obtener el nombre del cliente desde la sesión o parámetro
        $nombre_cliente = isset($_POST['nombre']) ? sanitizar($_POST['nombre']) : 'Cliente';
        $telefono_cliente = $numero_cliente;
        
        // Preparar detalle de productos
        $detalle_productos = '';
        foreach ($productos as $item) {
            $subtotal = $item['precio'] * $item['cantidad'];
            $nombre_prod = $item['nombre'] ?? $item['producto_nombre'] ?? 'Producto sin nombre';
            $detalle_productos .= "✔️ " . $nombre_prod . "\n";
            $detalle_productos .= "   Cantidad: " . (int)$item['cantidad'] . "\n";
            $detalle_productos .= "   Precio: $ " . number_format($item['precio'], 0, ',', '.') . "\n";
            $detalle_productos .= "   Subtotal: $ " . number_format($subtotal, 0, ',', '.') . "\n\n";
        }
        
        // Formato de fecha dd/m/yyyy
        $fecha = date('d/m/Y');
        
        // Construir el mensaje con nombre de la empresa
        $mensaje = "🛒 *¡NUEVO PEDIDO - " . strtoupper($nombre_empresa) . "!*\n\n";
        $mensaje .= "👤 Cliente: " . $nombre_cliente . "\n";
        $mensaje .= "📱 Teléfono: " . $telefono_cliente . "\n\n";
        $mensaje .= "📦 *PRODUCTOS SELECCIONADOS:*\n\n";
        $mensaje .= $detalle_productos;
        $mensaje .= "💰 *TOTAL: $ " . number_format($total, 0, ',', '.') . "*\n\n";
        $mensaje .= "📋 Nº DE PEDIDO: #" . $pedido_id . "\n";
        $mensaje .= "📅 FECHA: " . $fecha . "\n\n";
        $mensaje .= "✅ Pedido generado desde *" . $nombre_empresa . "*";
        
        // Codificar el mensaje para URL
        $mensaje_encoded = urlencode($mensaje);
        
        // Limpiar el número (quitar espacios, guiones, paréntesis)
        $numero_limpio = preg_replace('/[^0-9+]/', '', $numero);
        
        // Generar enlace de WhatsApp
        $whatsapp_link = "https://wa.me/" . $numero_limpio . "?text=" . $mensaje_encoded;
        
        return $whatsapp_link;
    }
}
?>
