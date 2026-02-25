<?php
/**
 * Controlador de Autenticación - Registro y Login de clientes
 */
class UsuarioController {
    private $clienteModel;
    
    public function __construct() {
        require_once 'app/models/ClienteModel.php';
        
        global $conn;
        $this->clienteModel = new ClienteModel($conn);
    }
    
    /**
     * Página de registro
     */
    public function registro() {
        // Registro deshabilitado
        http_response_code(404);
        include APP_ROOT . '/app/views/404.php';
        return;
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = sanitizar($_POST['usuario'] ?? '');
            $email = sanitizar($_POST['email'] ?? '');
            $nombre = sanitizar($_POST['nombre'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $telefono = sanitizar($_POST['telefono'] ?? '');
            $whatsapp_codigo = sanitizar($_POST['whatsapp_codigo'] ?? '');
            $whatsapp_numero = sanitizar($_POST['whatsapp'] ?? '');
            $whatsapp = $whatsapp_codigo . $whatsapp_numero;
            
            // Validaciones
            if (!$usuario || !$email || !$nombre || !$password) {
                $error = 'Por favor complete todos los campos obligatorios';
            } else if (strlen($usuario) < 4) {
                $error = 'El usuario debe tener al menos 4 caracteres';
            } else if (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } else if ($password !== $password_confirm) {
                $error = 'Las contraseñas no coinciden';
            } else if (!validarEmail($email)) {
                $error = 'El correo electrónico no es válido';
            } else if (!$whatsapp_codigo || strlen($whatsapp_numero) < 7) {
                $error = 'WhatsApp debe incluir código de país y al menos 7 dígitos';
            } else {
                // Verificar si usuario o email ya existen
                global $conn;
                $sql_check = "SELECT id FROM clientes WHERE usuario = ? OR email = ?";
                $existe = obtenerFila($sql_check, "ss", array(&$usuario, &$email));
                
                if ($existe) {
                    $error = 'El usuario o correo ya están registrados';
                } else {
                    // Crear cliente
                    $password_hash = hash('sha256', $password);
                    
                    $sql = "INSERT INTO clientes (usuario, password, nombre, email, telefono, activo) 
                            VALUES (?, ?, ?, ?, ?, 1)";
                    
                    $resultado = ejecutarConsulta($sql, "sssss", array(
                        &$usuario,
                        &$password_hash,
                        &$nombre,
                        &$email,
                        &$telefono
                    ));
                    
                    if ($resultado) {
                        $success = 'Registro exitoso. Ahora puedes iniciar sesión.';
                        // Limpiar formulario
                        $_POST = array();
                    } else {
                        $error = 'Error al registrarse. Intenta de nuevo.';
                    }
                }
            }
        }
        
        $categorias = array();
        require_once 'app/models/CategoriaModel.php';
        global $conn;
        $categoriaModel = new CategoriaModel($conn);
        $categorias = $categoriaModel->obtenerTodas();
        foreach ($categorias as &$cat) {
            require_once 'app/models/SubcategoriaModel.php';
            $subcategoriaModel = new SubcategoriaModel($conn);
            $cat['subcategorias'] = $subcategoriaModel->obtenerPorCategoria($cat['id']);
        }
        
        include APP_ROOT . '/app/views/tienda/registro.php';
    }
    
    /**
     * Página de login
     */
    public function login() {
        // Login de clientes deshabilitado
        http_response_code(404);
        include APP_ROOT . '/app/views/404.php';
        return;
    }
    
    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        header('Location: ' . APP_URL);
        exit;
    }
    
    /**
     * Perfil del usuario
     */
    public function perfil() {
        // Perfil deshabilitado
        http_response_code(404);
        include APP_ROOT . '/app/views/404.php';
        return;
        
        $cliente_id = $_SESSION['cliente_id'];
        global $conn;
        
        $sql = "SELECT * FROM clientes WHERE id = ?";
        $cliente = obtenerFila($sql, "i", array(&$cliente_id));
        
        if (!$cliente) {
            header('Location: ' . APP_URL . '/index.php?controller=usuario&action=logout');
            exit;
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'actualizar_perfil') {
                $nombre = sanitizar($_POST['nombre'] ?? '');
                $email = sanitizar($_POST['email'] ?? '');
                $telefono = sanitizar($_POST['telefono'] ?? '');
                $whatsapp_codigo = sanitizar($_POST['whatsapp_codigo'] ?? '');
                $whatsapp_numero = sanitizar($_POST['whatsapp'] ?? '');
                $whatsapp = $whatsapp_codigo . $whatsapp_numero;
                $ciudad = sanitizar($_POST['ciudad'] ?? '');
                $direccion = sanitizar($_POST['direccion'] ?? '');
                
                if (!$nombre || !$email) {
                    $error = 'Nombre y correo son obligatorios';
                } else if (!validarEmail($email)) {
                    $error = 'El correo electrónico no es válido';
                } else {
                    // Verificar si el email ya está usado por otro usuario
                    $sql_check = "SELECT id FROM clientes WHERE email = ? AND id != ?";
                    $existe = obtenerFila($sql_check, "si", array(&$email, &$cliente_id));
                    
                    if ($existe) {
                        $error = 'El correo ya está registrado por otro usuario';
                    } else {
                        $sql_update = "UPDATE clientes SET nombre = ?, email = ?, telefono = ?, whatsapp = ?, ciudad = ?, direccion = ? WHERE id = ?";
                        
                        if (ejecutarConsulta($sql_update, "ssssssi", array(
                            &$nombre,
                            &$email,
                            &$telefono,
                            &$whatsapp,
                            &$ciudad,
                            &$direccion,
                            &$cliente_id
                        ))) {
                            $_SESSION['cliente_nombre'] = $nombre;
                            $_SESSION['cliente_email'] = $email;
                            $success = 'Perfil actualizado correctamente';
                            
                            // Recargar datos
                            $sql = "SELECT * FROM clientes WHERE id = ?";
                            $cliente = obtenerFila($sql, "i", array(&$cliente_id));
                        } else {
                            $error = 'Error al actualizar el perfil';
                        }
                    }
                }
            } elseif ($action === 'cambiar_password') {
                $password_actual = $_POST['password_actual'] ?? '';
                $password_nueva = $_POST['password_nueva'] ?? '';
                $password_confirm = $_POST['password_confirm'] ?? '';
                
                if (!$password_actual || !$password_nueva) {
                    $error = 'Completa todos los campos';
                } else if (strlen($password_nueva) < 6) {
                    $error = 'La contraseña debe tener al menos 6 caracteres';
                } else if ($password_nueva !== $password_confirm) {
                    $error = 'Las contraseñas nuevas no coinciden';
                } else {
                    $hash_actual = hash('sha256', $password_actual);
                    
                    if ($cliente['password'] !== $hash_actual) {
                        $error = 'La contraseña actual es incorrecta';
                    } else {
                        $hash_nueva = hash('sha256', $password_nueva);
                        $sql_pwd = "UPDATE clientes SET password = ? WHERE id = ?";
                        
                        if (ejecutarConsulta($sql_pwd, "si", array(&$hash_nueva, &$cliente_id))) {
                            $success = 'Contraseña cambiada correctamente';
                        } else {
                            $error = 'Error al cambiar la contraseña';
                        }
                    }
                }
            }
        }
        
        $categorias = array();
        require_once 'app/models/CategoriaModel.php';
        $categoriaModel = new CategoriaModel($conn);
        $categorias = $categoriaModel->obtenerTodas();
        foreach ($categorias as &$cat) {
            require_once 'app/models/SubcategoriaModel.php';
            $subcategoriaModel = new SubcategoriaModel($conn);
            $cat['subcategorias'] = $subcategoriaModel->obtenerPorCategoria($cat['id']);
        }
        
        include APP_ROOT . '/app/views/tienda/perfil.php';
    }

    /**
     * Ver mis pedidos
     */
    public function misPedidos() {
        // Mis pedidos deshabilitado
        http_response_code(404);
        include APP_ROOT . '/app/views/404.php';
        return;

        $cliente_id = $_SESSION['cliente_id'];
        global $conn;

        // Obtener pedidos del cliente
        $sql = "SELECT p.* FROM pedidos p 
                INNER JOIN clientes c ON p.cliente_id = c.id 
                WHERE c.id = ? 
                ORDER BY p.fecha_creacion DESC";
        
        $pedidos = obtenerFilas($sql, "i", array(&$cliente_id));

        // Obtener categorías para el menú
        require_once APP_ROOT . '/app/models/CategoriaModel.php';
        require_once APP_ROOT . '/app/models/SubcategoriaModel.php';
        $categoriaModel = new CategoriaModel($conn);
        $subcategoriaModel = new SubcategoriaModel($conn);
        $categorias = $categoriaModel->obtenerTodas();
        foreach ($categorias as &$cat) {
            $cat['subcategorias'] = $subcategoriaModel->obtenerPorCategoria($cat['id']);
        }

        include APP_ROOT . '/app/views/tienda/mis_pedidos.php';
    }

    /**
     * Ver pedido específico
     */
    public function verPedido() {
        // Ver pedido deshabilitado
        http_response_code(404);
        include APP_ROOT . '/app/views/404.php';
        return;

        $pedido_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        global $conn;

        // Obtener el pedido verificando que pertenezca al cliente logueado
        $sql = "SELECT p.* FROM pedidos p 
                INNER JOIN clientes c ON p.cliente_id = c.id 
                WHERE p.id = ? AND c.id = ?";
        
        $pedido = obtenerFila($sql, "ii", array(&$pedido_id, &$_SESSION['cliente_id']));

        if (!$pedido) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }

        // Obtener detalles del pedido
        $sql_detalles = "SELECT pd.*, p.nombre, p.imagen FROM pedido_detalles pd 
                        INNER JOIN productos p ON pd.producto_id = p.id 
                        WHERE pd.pedido_id = ?";
        
        $detalles = obtenerFilas($sql_detalles, "i", array(&$pedido_id));

        // Obtener historial del pedido
        $sql_historial = "SELECT * FROM pedido_historial WHERE pedido_id = ? ORDER BY fecha DESC";
        $historial = obtenerFilas($sql_historial, "i", array(&$pedido_id));

        // Obtener categorías para el menú
        require_once APP_ROOT . '/app/models/CategoriaModel.php';
        require_once APP_ROOT . '/app/models/SubcategoriaModel.php';
        $categoriaModel = new CategoriaModel($conn);
        $subcategoriaModel = new SubcategoriaModel($conn);
        $categorias = $categoriaModel->obtenerTodas();
        foreach ($categorias as &$cat) {
            $cat['subcategorias'] = $subcategoriaModel->obtenerPorCategoria($cat['id']);
        }

        include APP_ROOT . '/app/views/tienda/ver_pedido.php';
    }
}
?>
