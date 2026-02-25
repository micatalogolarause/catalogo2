<?php
/**
 * Controlador Super Admin - Gestión global de tenants
 */
class SuperAdminController {
    
    public function __construct() {
        // Permitir acceder a login sin sesión
        $accionActual = isset($_GET['action']) ? $_GET['action'] : '';
        if ($accionActual !== 'login') {
            $this->verificarAutenticacion();
        }
    }
    
    /**
     * Verificar autenticación del super admin
     */
    private function verificarAutenticacion() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'superadmin') {
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=login');
            exit;
        }
    }
    
    /**
     * Login del super administrador
     */
    public function login() {
        if (isset($_SESSION['usuario_id']) && $_SESSION['rol'] === 'superadmin') {
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=dashboard');
            exit;
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = sanitizar($_POST['usuario'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (!$usuario || !$password) {
                $error = 'Por favor complete todos los campos';
            } else {
                $password_hash = hash('sha256', $password);
                
                $sql = "SELECT id, usuario, nombre, rol FROM usuarios 
                        WHERE usuario = ? AND password = ? AND rol = 'superadmin' AND activo = 1 AND tenant_id IS NULL";
                
                $superadmin = obtenerFila($sql, "ss", [$usuario, $password_hash]);
                
                if ($superadmin) {
                    $_SESSION['usuario_id'] = $superadmin['id'];
                    $_SESSION['usuario'] = $superadmin['usuario'];
                    $_SESSION['nombre'] = $superadmin['nombre'];
                    $_SESSION['rol'] = $superadmin['rol'];
                    
                    // Actualizar último acceso
                    ejecutarConsulta("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?", "i", [$superadmin['id']]);
                    
                    header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=dashboard');
                    exit;
                } else {
                    $error = 'Usuario o contraseña incorrectos';
                }
            }
        }
        
        include APP_ROOT . '/app/views/superadmin/login.php';
    }
    
    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=login');
        exit;
    }
    
    /**
     * Dashboard principal del super admin
     */
    public function dashboard() {
        // Estadísticas generales
        $stats = [
            'total_tenants' => 0,
            'tenants_activos' => 0,
            'tenants_inactivos' => 0,
            'total_productos' => 0,
            'total_pedidos' => 0,
            'total_clientes' => 0,
            'ventas_total' => 0
        ];
        
        // Total tenants
        $sql = "SELECT COUNT(*) as total FROM tenants";
        $stats['total_tenants'] = obtenerFila($sql)['total'] ?? 0;
        
        // Tenants activos/inactivos
        $rows = obtenerFilas("SELECT estado, COUNT(*) as total FROM tenants GROUP BY estado");
        foreach ($rows as $row) {
            if ($row['estado'] === 'activo') {
                $stats['tenants_activos'] = $row['total'];
            } else {
                $stats['tenants_inactivos'] = $row['total'];
            }
        }
        
        // Total productos
        $stats['total_productos'] = obtenerFila("SELECT COUNT(*) as total FROM productos WHERE activo = 1")['total'] ?? 0;
        
        // Total pedidos
        $stats['total_pedidos'] = obtenerFila("SELECT COUNT(*) as total FROM pedidos")['total'] ?? 0;
        
        // Total clientes
        $stats['total_clientes'] = obtenerFila("SELECT COUNT(*) as total FROM clientes WHERE activo = 1")['total'] ?? 0;
        
        // Total ventas
        $row = obtenerFila("SELECT SUM(total) as ventas FROM pedidos WHERE estado NOT IN ('cancelado')");
        $stats['ventas_total'] = $row['ventas'] ?? 0;
        
        // Listar todos los tenants con sus estadísticas
        $tenants_raw = obtenerFilas("SELECT * FROM tenants ORDER BY created_at DESC");
        $tenants = [];
        foreach ($tenants_raw as $tenant) {
            $tenant_id = $tenant['id'];
            
            $sql_stats = "SELECT 
                (SELECT COUNT(*) FROM productos WHERE tenant_id = ? AND activo = 1) as productos,
                (SELECT COUNT(*) FROM pedidos WHERE tenant_id = ?) as pedidos,
                (SELECT COUNT(*) FROM clientes WHERE tenant_id = ? AND activo = 1) as clientes,
                (SELECT SUM(total) FROM pedidos WHERE tenant_id = ? AND estado NOT IN ('cancelado')) as ventas
            ";
            $tenant['stats'] = obtenerFila($sql_stats, "iiii", [$tenant_id, $tenant_id, $tenant_id, $tenant_id]);
            
            $tenants[] = $tenant;
        }
        
        include APP_ROOT . '/app/views/superadmin/dashboard.php';
    }
    
    /**
     * Gestión de tenants
     */
    public function tenants() {
        $tenants_raw = obtenerFilas("SELECT * FROM tenants ORDER BY created_at DESC");
        $tenants = [];
        
        foreach ($tenants_raw as $tenant) {
            $tenant_id = $tenant['id'];
            
            // Estadísticas rápidas
            $sql_stats = "SELECT 
                (SELECT COUNT(*) FROM productos WHERE tenant_id = ?) as productos,
                (SELECT COUNT(*) FROM pedidos WHERE tenant_id = ?) as pedidos
            ";
            $tenant['stats'] = obtenerFila($sql_stats, "ii", [$tenant_id, $tenant_id]);
            
            $tenants[] = $tenant;
        }
        
        include APP_ROOT . '/app/views/superadmin/tenants.php';
    }
    
    /**
     * Cambiar estado de tenant (activar/desactivar)
     */
    public function cambiarEstadoTenant() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        $tenant_id = isset($_POST['tenant_id']) ? (int)$_POST['tenant_id'] : 0;
        $nuevo_estado = isset($_POST['estado']) ? sanitizar($_POST['estado']) : '';
        
        if (!$tenant_id || !in_array($nuevo_estado, ['activo', 'inactivo', 'suspendido'])) {
            $_SESSION['error'] = 'Datos inválidos';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
            exit;
        }
        
        $sql = "UPDATE tenants SET estado = ?, updated_at = NOW() WHERE id = ?";
        
        if (ejecutarConsulta($sql, "si", [$nuevo_estado, $tenant_id])) {
            $_SESSION['success'] = 'Estado del tenant actualizado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al actualizar el estado';
        }
        
        header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
        exit;
    }
    
    /**
     * Ver detalle de un tenant
     */
    public function verTenant() {
        $tenant_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        $sql = "SELECT * FROM tenants WHERE id = ?";
        $tenant = obtenerFila($sql, "i", [$tenant_id]);
        
        if (!$tenant) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }
        
        // Estadísticas detalladas
        $sql_stats = "SELECT 
            (SELECT COUNT(*) FROM productos WHERE tenant_id = ? AND activo = 1) as productos,
            (SELECT COUNT(*) FROM categorias WHERE tenant_id = ?) as categorias,
            (SELECT COUNT(*) FROM subcategorias WHERE tenant_id = ?) as subcategorias,
            (SELECT COUNT(*) FROM pedidos WHERE tenant_id = ?) as pedidos,
            (SELECT COUNT(*) FROM clientes WHERE tenant_id = ? AND activo = 1) as clientes,
            (SELECT SUM(total) FROM pedidos WHERE tenant_id = ? AND estado NOT IN ('cancelado')) as ventas,
            (SELECT COUNT(*) FROM usuarios WHERE tenant_id = ? AND rol = 'admin') as admins
        ";
        $stats = obtenerFila($sql_stats, "iiiiiii", [$tenant_id, $tenant_id, $tenant_id, $tenant_id, $tenant_id, $tenant_id, $tenant_id]);
        
        // Últimos pedidos
        $sql_pedidos = "SELECT p.*, c.nombre as cliente_nombre 
                        FROM pedidos p 
                        LEFT JOIN clientes c ON p.cliente_id = c.id 
                        WHERE p.tenant_id = ? 
                        ORDER BY p.fecha_creacion DESC 
                        LIMIT 10";
        $pedidos_recientes = obtenerFilas($sql_pedidos, "i", [$tenant_id]);
        
        include APP_ROOT . '/app/views/superadmin/ver_tenant.php';
    }
    
    /**
     * Eliminar tenant (soft delete)
     */
    public function eliminarTenant() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        $tenant_id = isset($_POST['tenant_id']) ? (int)$_POST['tenant_id'] : 0;
        
        if (!$tenant_id) {
            $_SESSION['error'] = 'ID de tenant inválido';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
            exit;
        }
        
        // Cambiar estado a eliminado
        $sql = "UPDATE tenants SET estado = 'eliminado', updated_at = NOW() WHERE id = ?";
        
        if (ejecutarConsulta($sql, "i", [$tenant_id])) {
            $_SESSION['success'] = 'Tenant eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el tenant';
        }
        
        header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
        exit;
    }
    
    /**
     * Formulario para editar tenant
     */
    public function editarTenant() {
        $tenant_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$tenant_id) {
            $_SESSION['error'] = 'ID de tenant inválido';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
            exit;
        }
        
        $sql = "SELECT * FROM tenants WHERE id = ?";
        $tenant = obtenerFila($sql, "i", [$tenant_id]);
        
        if (!$tenant) {
            $_SESSION['error'] = 'Tenant no encontrado';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
            exit;
        }
        
        $temas = ['default' => 'Default', 'claro' => 'Claro', 'oscuro' => 'Oscuro'];
        include APP_ROOT . '/app/views/superadmin/editar_tenant.php';
    }
    
    /**
     * Actualizar tenant
     */
    public function actualizarTenant() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
            exit;
        }
        
        $tenant_id = isset($_POST['tenant_id']) ? (int)$_POST['tenant_id'] : 0;
        
        if (!$tenant_id) {
            $_SESSION['error'] = 'ID de tenant inválido';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
            exit;
        }
        
        $nombre = sanitizar($_POST['nombre'] ?? '');
        $titulo_empresa = sanitizar($_POST['titulo_empresa'] ?? '');
        $whatsapp_phone = sanitizar($_POST['whatsapp_phone'] ?? '');
        $tema = sanitizar($_POST['tema'] ?? 'claro');
        $tema_color = sanitizar($_POST['tema_color'] ?? 'azul');
        $estado = sanitizar($_POST['estado'] ?? 'activo');
        
        // Concatenar código de país si no está presente
        if (!empty($whatsapp_phone) && !str_starts_with($whatsapp_phone, '+')) {
            $whatsapp_phone = '+57' . $whatsapp_phone;
        }
        
        // Validaciones
        if (empty($nombre)) {
            $_SESSION['error'] = 'El nombre del tenant es requerido';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=editarTenant&id=' . $tenant_id);
            exit;
        }
        
        if (empty($whatsapp_phone)) {
            $_SESSION['error'] = 'El número de WhatsApp es requerido';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=editarTenant&id=' . $tenant_id);
            exit;
        }
        
        // Obtener datos actuales del tenant
        $sql = "SELECT slug, logo FROM tenants WHERE id = ?";
        $tenantActual = obtenerFila($sql, "i", [$tenant_id]);
        
        if (!$tenantActual) {
            $_SESSION['error'] = 'Tenant no encontrado';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=editarTenant&id=' . $tenant_id);
            exit;
        }
        
        $logo = $tenantActual['logo'];
        
        // Procesar logo si se subió uno nuevo
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logoUpload = $this->guardarLogoTenant('logo', $tenantActual['slug']);
            if ($logoUpload['success']) {
                $logo = $logoUpload['relPath'];
            } else {
                $_SESSION['error'] = 'Error al subir el logo: ' . $logoUpload['message'];
                header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=editarTenant&id=' . $tenant_id);
                exit;
            }
        }
        
        // Actualizar tenant
        $sql = "UPDATE tenants SET nombre = ?, titulo_empresa = ?, whatsapp_phone = ?, logo = ?, tema = ?, tema_color = ?, estado = ?, updated_at = NOW() WHERE id = ?";
        
        if (ejecutarConsulta($sql, "sssssssi", [$nombre, $titulo_empresa, $whatsapp_phone, $logo, $tema, $tema_color, $estado, $tenant_id])) {
            $_SESSION['success'] = 'Tenant actualizado exitosamente';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=verTenant&id=' . $tenant_id);
        } else {
            $_SESSION['error'] = 'Error al actualizar el tenant';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=editarTenant&id=' . $tenant_id);
        }
        exit;
    }
    
    /**
     * Gestionar usuarios de un tenant
     */
    public function usuariosTenant() {
        $tenant_id = isset($_GET['tenant_id']) ? (int)$_GET['tenant_id'] : 0;
        
        if (!$tenant_id) {
            $_SESSION['error'] = 'ID de tenant inválido';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
            exit;
        }
        
        // Obtener info del tenant
        $sql = "SELECT * FROM tenants WHERE id = ?";
        $tenant = obtenerFila($sql, "i", [$tenant_id]);
        
        if (!$tenant) {
            http_response_code(404);
            include APP_ROOT . '/app/views/404.php';
            return;
        }
        
        // Obtener usuarios del tenant
        $sql = "SELECT * FROM usuarios WHERE tenant_id = ? ORDER BY fecha_creacion DESC";
        $usuarios = obtenerFilas($sql, "i", [$tenant_id]);
        
        include APP_ROOT . '/app/views/superadmin/usuarios_tenant.php';
    }
    
    /**
     * Crear usuario para un tenant
     */
    public function crearUsuarioTenant() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        $tenant_id = isset($_POST['tenant_id']) ? (int)$_POST['tenant_id'] : 0;
        $usuario = sanitizar($_POST['usuario'] ?? '');
        $email = sanitizar($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $nombre = sanitizar($_POST['nombre'] ?? '');
        $rol = sanitizar($_POST['rol'] ?? 'admin');
        
        if (!$tenant_id || !$usuario || !$email || !$password || !$nombre) {
            $_SESSION['error'] = 'Todos los campos son requeridos';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=usuariosTenant&tenant_id=' . $tenant_id);
            exit;
        }
        
        // Verificar que el tenant existe
        $sql = "SELECT id FROM tenants WHERE id = ?";
        if (!obtenerFila($sql, "i", [$tenant_id])) {
            $_SESSION['error'] = 'Tenant no encontrado';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
            exit;
        }
        
        // Verificar que el usuario no existe
        $sql = "SELECT id FROM usuarios WHERE usuario = ? OR email = ?";
        if (obtenerFila($sql, "ss", [$usuario, $email])) {
            $_SESSION['error'] = 'El usuario o email ya existe';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=usuariosTenant&tenant_id=' . $tenant_id);
            exit;
        }
        
        // Crear usuario
        $password_hash = hash('sha256', $password);
        $sql = "INSERT INTO usuarios (tenant_id, usuario, email, password, nombre, rol, activo) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        
        if (ejecutarConsulta($sql, "issss", [$tenant_id, $usuario, $email, $password_hash, $nombre, $rol])) {
            $_SESSION['success'] = 'Usuario creado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al crear el usuario';
        }
        
        header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=usuariosTenant&tenant_id=' . $tenant_id);
        exit;
    }
    
    /**
     * Cambiar estado de usuario (activar/desactivar)
     */
    public function cambiarEstadoUsuario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        $usuario_id = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : 0;
        $tenant_id = isset($_POST['tenant_id']) ? (int)$_POST['tenant_id'] : 0;
        
        if (!$usuario_id || !$tenant_id) {
            $_SESSION['error'] = 'Datos inválidos';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
            exit;
        }
        
        // Obtener estado actual
        $sql = "SELECT activo FROM usuarios WHERE id = ? AND tenant_id = ?";
        $usuario = obtenerFila($sql, "ii", [$usuario_id, $tenant_id]);
        
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=usuariosTenant&tenant_id=' . $tenant_id);
            exit;
        }
        
        // Cambiar estado
        $nuevo_estado = $usuario['activo'] ? 0 : 1;
        
        if (ejecutarConsulta("UPDATE usuarios SET activo = ? WHERE id = ?", "ii", [$nuevo_estado, $usuario_id])) {
            $_SESSION['success'] = 'Estado del usuario actualizado';
        } else {
            $_SESSION['error'] = 'Error al actualizar el usuario';
        }
        
        header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=usuariosTenant&tenant_id=' . $tenant_id);
        exit;
    }
    
    /**
     * Resetear password de usuario
     */
    public function resetearPasswordUsuario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        
        $usuario_id = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : 0;
        $tenant_id = isset($_POST['tenant_id']) ? (int)$_POST['tenant_id'] : 0;
        $nueva_password = $_POST['nueva_password'] ?? '';
        
        if (!$usuario_id || !$tenant_id || !$nueva_password) {
            $_SESSION['error'] = 'Datos inválidos';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=usuariosTenant&tenant_id=' . $tenant_id);
            exit;
        }
        
        // Verificar que el usuario pertenece al tenant
        $sql = "SELECT id FROM usuarios WHERE id = ? AND tenant_id = ?";
        if (!obtenerFila($sql, "ii", [$usuario_id, $tenant_id])) {
            $_SESSION['error'] = 'Usuario no encontrado';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=usuariosTenant&tenant_id=' . $tenant_id);
            exit;
        }
        
        // Actualizar password
        $password_hash = hash('sha256', $nueva_password);
        
        if (ejecutarConsulta("UPDATE usuarios SET password = ? WHERE id = ?", "si", [$password_hash, $usuario_id])) {
            $_SESSION['success'] = 'Contraseña reseteada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al resetear la contraseña';
        }
        
        header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=usuariosTenant&tenant_id=' . $tenant_id);
        exit;
    }
    
    /**
     * Formulario para crear nuevo tenant
     */
    public function formularioCrearTenant() {
        $temas = ['default' => 'Default', 'claro' => 'Claro', 'oscuro' => 'Oscuro'];
        include APP_ROOT . '/app/views/superadmin/crear_tenant.php';
    }
    
    /**
     * Crear nuevo tenant con provisioning automático
     */
    public function crearTenant() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=formularioCrearTenant');
            exit;
        }
        
        $data = [
            'nombre' => sanitizar($_POST['nombre'] ?? ''),
            'titulo_empresa' => sanitizar($_POST['titulo_empresa'] ?? ''),
            'slug' => sanitizar($_POST['slug'] ?? ''),
            'whatsapp_phone' => sanitizar($_POST['whatsapp_phone'] ?? ''),
            'logo' => '',
            'tema' => sanitizar($_POST['tema'] ?? 'claro'),
            'tema_color' => sanitizar($_POST['tema_color'] ?? 'azul'),
            'estado' => sanitizar($_POST['estado'] ?? 'activo'),
            'admin_usuario' => sanitizar($_POST['admin_usuario'] ?? ''),
            'admin_email' => sanitizar($_POST['admin_email'] ?? ''),
            'admin_password' => $_POST['admin_password'] ?? '',
        ];

        // Normalizar número de WhatsApp: esperar solo dígitos y agregar +57
        $rawPhoneDigits = preg_replace('/\D+/', '', $data['whatsapp_phone']);
        if (!empty($rawPhoneDigits)) {
            if (strlen($rawPhoneDigits) === 10) {
                $data['whatsapp_phone'] = '+57' . $rawPhoneDigits;
            } else if (str_starts_with($data['whatsapp_phone'], '+')) {
                // Si ya viene con +, usar tal cual
                // Mantener el valor saneado original
            } else {
                // Fallback: prefijar +57 a lo que se envíe
                $data['whatsapp_phone'] = '+57' . $rawPhoneDigits;
            }
        }
        
        // Validaciones
        if (empty($data['nombre'])) {
            $_SESSION['error'] = 'El nombre del tenant es requerido';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=formularioCrearTenant');
            exit;
        }
        
        if (empty($data['slug'])) {
            $_SESSION['error'] = 'El slug es requerido';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=formularioCrearTenant');
            exit;
        }
        
        if (!preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $_SESSION['error'] = 'El slug solo puede contener letras minúsculas, números y guiones';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=formularioCrearTenant');
            exit;
        }
        
        if (empty($data['whatsapp_phone'])) {
            $_SESSION['error'] = 'El número de WhatsApp es requerido';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=formularioCrearTenant');
            exit;
        }
        
        // Procesar logo si existe
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logoUpload = $this->guardarLogoTenant('logo', $data['slug']);
            if ($logoUpload['success']) {
                $data['logo'] = $logoUpload['relPath'];
            } else {
                $_SESSION['error'] = 'Error al subir el logo: ' . $logoUpload['message'];
                header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=formularioCrearTenant');
                exit;
            }
        }
        
        // Verificar slug único
        $sql = "SELECT id FROM tenants WHERE slug = ?";
        if (obtenerFila($sql, "s", [$data['slug']])) {
            $_SESSION['error'] = 'El slug ya existe';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=formularioCrearTenant');
            exit;
        }
        
        try {
            global $pdo;
            $pdo->beginTransaction();
            
            // 1. Crear tenant
            $sql = "INSERT INTO tenants (nombre, titulo_empresa, slug, whatsapp_phone, logo, tema, tema_color, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            ejecutarConsulta($sql, "ssssssss", [$data['nombre'], $data['titulo_empresa'], $data['slug'], $data['whatsapp_phone'], $data['logo'], $data['tema'], $data['tema_color'], $data['estado']]);
            $tenant_id = obtenerUltimoId();
            
            // 2. Crear categorías iniciales
            $categorias = [
                ['nombre' => 'Electrónica', 'descripcion' => 'Productos electrónicos y gadgets'],
                ['nombre' => 'Ropa', 'descripcion' => 'Prendas de vestir y accesorios'],
                ['nombre' => 'Hogar', 'descripcion' => 'Artículos y decoración para el hogar']
            ];
            
            $categoria_ids = [];
            foreach ($categorias as $cat) {
                $sql = "INSERT INTO categorias (tenant_id, nombre, descripcion, activa, fecha_creacion, fecha_actualizacion) VALUES (?, ?, ?, 1, NOW(), NOW())";
                ejecutarConsulta($sql, "iss", [$tenant_id, $cat['nombre'], $cat['descripcion']]);
                $categoria_ids[$cat['nombre']] = obtenerUltimoId();
            }
            
            // 3. Crear subcategorías iniciales
            $subcategorias = [
                ['categoria' => 'Electrónica', 'nombre' => 'Smartphones', 'descripcion' => 'Teléfonos inteligentes'],
                ['categoria' => 'Electrónica', 'nombre' => 'Laptops', 'descripcion' => 'Computadoras portátiles'],
                ['categoria' => 'Ropa', 'nombre' => 'Hombre', 'descripcion' => 'Ropa para caballeros'],
                ['categoria' => 'Ropa', 'nombre' => 'Mujer', 'descripcion' => 'Ropa para damas'],
                ['categoria' => 'Hogar', 'nombre' => 'Cocina', 'descripcion' => 'Electrodomésticos de cocina'],
                ['categoria' => 'Hogar', 'nombre' => 'Dormitorio', 'descripcion' => 'Muebles de dormitorio']
            ];
            
            foreach ($subcategorias as $subcat) {
                $categoria_id = $categoria_ids[$subcat['categoria']] ?? null;
                if (!$categoria_id) continue;
                
                $sql = "INSERT INTO subcategorias (tenant_id, categoria_id, nombre, descripcion, activa, fecha_creacion, fecha_actualizacion) VALUES (?, ?, ?, ?, 1, NOW(), NOW())";
                ejecutarConsulta($sql, "iiss", [$tenant_id, $categoria_id, $subcat['nombre'], $subcat['descripcion']]);
            }
            
            // 4. Crear usuario admin del tenant
            if (!empty($data['admin_email']) && !empty($data['admin_usuario'])) {
                $password_hash = hash('sha256', $data['admin_password'] ?? 'admin123');
                $sql = "INSERT INTO usuarios (tenant_id, usuario, email, password, nombre, rol, activo) VALUES (?, ?, ?, ?, ?, 'admin', 1)";
                ejecutarConsulta($sql, "issss", [$tenant_id, $data['admin_usuario'], $data['admin_email'], $password_hash, $data['nombre']]);
            }
            
            // 5. Crear directorio de uploads para el tenant
            $upload_dir = APP_ROOT . '/public/tenants/' . $tenant_id;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
                mkdir($upload_dir . '/images', 0755, true);
                mkdir($upload_dir . '/docs', 0755, true);
                mkdir($upload_dir . '/temp', 0755, true);
                
                // Crear .htaccess de seguridad
                $htaccess = <<<'EOT'
<FilesMatch "\.php$">
    Deny from all
</FilesMatch>
<FilesMatch "\.phtml$">
    Deny from all
</FilesMatch>
Options -Indexes
EOT;
                file_put_contents($upload_dir . '/.htaccess', $htaccess);
                file_put_contents($upload_dir . '/index.html', '');
            }
            
            // Confirmar transacción
            $pdo->commit();
            
            $_SESSION['success'] = 'Tenant creado exitosamente';
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=tenants');
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Error al crear tenant: ' . $e->getMessage();
            header('Location: ' . APP_URL . '/index.php?controller=superAdmin&action=formularioCrearTenant');
            exit;
        }
    }
    
    /**
     * Guardar logo de tenant en Cloudinary
     */
    private function guardarLogoTenant($fieldName, $slug) {
        if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No se subió archivo'];
        }

        $file = $_FILES[$fieldName];
        $maxSizeMb = 2;
        $sizeLimit = $maxSizeMb * 1024 * 1024;

        if ($file['size'] > $sizeLimit) {
            return ['success' => false, 'message' => "El archivo excede {$maxSizeMb}MB"];
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts)) {
            return ['success' => false, 'message' => 'Formato no permitido (JPG, PNG, WebP)'];
        }

        if (!function_exists('uploadToCloudinary')) {
            require_once APP_ROOT . '/config/cloudinary.php';
        }

        $folder   = 'tenants/logos';
        $publicId = 'logo_' . $slug;
        $upload   = uploadToCloudinary($file['tmp_name'], $folder, $publicId);

        if (!$upload['success']) {
            return ['success' => false, 'message' => $upload['message']];
        }

        return [
            'success' => true,
            'path'    => '',
            'relPath' => $upload['url'],   // URL completa de Cloudinary guardada en BD
        ];
    }
}
?>
