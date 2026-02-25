<?php
/**
 * Instalador automático del proyecto
 * Crea base de datos, tablas e inserta datos de prueba
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'catalogo_tienda');

// Crear conexión sin especificar BD
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// Crear base de datos
$sql_db = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!$conn->query($sql_db)) {
    die("Error creando BD: " . $conn->error);
}

// Seleccionar la BD
if (!$conn->select_db(DB_NAME)) {
    die("Error seleccionando BD: " . $conn->error);
}

// SQL para crear tablas
$sql_tablas = "
-- Tabla de Categorías
CREATE TABLE IF NOT EXISTS categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    activa INT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Subcategorías
CREATE TABLE IF NOT EXISTS subcategorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    categoria_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activa INT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subcategoria (categoria_id, nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Productos
CREATE TABLE IF NOT EXISTS productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    categoria_id INT NOT NULL,
    subcategoria_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    imagen VARCHAR(255),
    activo INT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (subcategoria_id) REFERENCES subcategorias(id),
    INDEX idx_categoria (categoria_id),
    INDEX idx_subcategoria (subcategoria_id),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    activo INT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    INDEX idx_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    nombre VARCHAR(150) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    whatsapp VARCHAR(20),
    ciudad VARCHAR(100),
    direccion TEXT,
    activo INT DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    estado ENUM('en_pedido', 'alistado', 'en_reparto', 'entregado', 'cancelado') DEFAULT 'en_pedido',
    total DECIMAL(10, 2) NOT NULL,
    notas_cliente TEXT,
    notas_admin TEXT,
    whatsapp_enviado INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Detalles de Pedidos
CREATE TABLE IF NOT EXISTS pedido_detalles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    INDEX idx_pedido (pedido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Carrito
CREATE TABLE IF NOT EXISTS carrito (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(255) NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_carrito (session_id, producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
-- Historial de estados de pedidos (trazabilidad)
CREATE TABLE IF NOT EXISTS pedido_historial (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pedido_id INT NOT NULL,
    estado ENUM('en_pedido', 'alistado', 'en_reparto', 'entregado', 'cancelado') NOT NULL,
    nota TEXT,
    usuario_id INT DEFAULT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    INDEX idx_historial_pedido (pedido_id),
    INDEX idx_historial_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// Ejecutar cada tabla por separado
$tablas = explode(';', $sql_tablas);
foreach ($tablas as $tabla) {
    $tabla = trim($tabla);
    if (!empty($tabla)) {
        if (!$conn->query($tabla)) {
            error_log("Error creando tabla: " . $conn->error);
        }
    }
}

// Verificar si ya existen datos
// Asegurar datos mínimos: admin, categorías, subcategorías y productos

// 1) Usuario administrador si no existe
$resultado = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE usuario='admin'");
$fila = $resultado ? $resultado->fetch_assoc() : ['total' => 0];
if ((int)$fila['total'] === 0) {
    $password_admin = hash('sha256', 'admin123');
    $conn->query("INSERT INTO usuarios (usuario, email, password, nombre, rol, activo) 
                  VALUES ('admin', 'admin@tienda.local', '$password_admin', 'Administrador', 'admin', 1)");
}

// 2) Categorías y subcategorías si no existen
$resCats = $conn->query("SELECT COUNT(*) as total FROM categorias");
$totalCats = $resCats ? (int)$resCats->fetch_assoc()['total'] : 0;

if ($totalCats === 0) {
    $categorias = array(
        array('Electrónica', 'Productos electrónicos de última tecnología'),
        array('Ropa', 'Prendas de vestir para hombre y mujer'),
        array('Hogar', 'Artículos y decoración para el hogar'),
    );
    $categoria_ids = array();
    foreach ($categorias as $cat) {
        $nombre = $conn->real_escape_string($cat[0]);
        $descripcion = $conn->real_escape_string($cat[1]);
        $conn->query("INSERT INTO categorias (nombre, descripcion) VALUES ('$nombre', '$descripcion')");
        $categoria_ids[$cat[0]] = $conn->insert_id;
    }

    $subcategorias = array(
        'Electrónica' => array(
            array('Smartphones', 'Teléfonos inteligentes de última generación'),
            array('Laptops', 'Computadoras portátiles para trabajo y entretenimiento')
        ),
        'Ropa' => array(
            array('Hombre', 'Ropa y accesorios para caballeros'),
            array('Mujer', 'Ropa y accesorios para damas')
        ),
        'Hogar' => array(
            array('Cocina', 'Electrodomésticos y utensilios de cocina'),
            array('Dormitorio', 'Muebles y accesorios para dormitorio')
        )
    );
    $subcategoria_ids = array();
    foreach ($subcategorias as $cat => $subs) {
        foreach ($subs as $sub) {
            $nombre = $conn->real_escape_string($sub[0]);
            $descripcion = $conn->real_escape_string($sub[1]);
            $cat_id = $categoria_ids[$cat];
            $conn->query("INSERT INTO subcategorias (categoria_id, nombre, descripcion) 
                         VALUES ($cat_id, '$nombre', '$descripcion')");
            $subcategoria_ids[$sub[0]] = array('id' => $conn->insert_id, 'cat_id' => $cat_id);
        }
    }

    // 3) Insertar 10 productos de prueba
    $productos = array(
        array(
            'nombre' => 'iPhone 15 Pro',
            'subcategoria' => 'Smartphones',
            'precio' => 999.99,
            'stock' => 50,
            'descripcion' => 'Último modelo de Apple con chip A17 Pro y cámara avanzada.',
            'imagen' => 'iphone15.jpg'
        ),
        array(
            'nombre' => 'Samsung Galaxy S24',
            'subcategoria' => 'Smartphones',
            'precio' => 899.99,
            'stock' => 45,
            'descripcion' => 'Teléfono Android con pantalla AMOLED y procesador Snapdragon.',
            'imagen' => 'samsung_s24.jpg'
        ),
        array(
            'nombre' => 'MacBook Pro 16',
            'subcategoria' => 'Laptops',
            'precio' => 2499.99,
            'stock' => 20,
            'descripcion' => 'Laptop de alta rendimiento con chip M3 Max para profesionales.',
            'imagen' => 'macbook_pro.jpg'
        ),
        array(
            'nombre' => 'Dell XPS 15',
            'subcategoria' => 'Laptops',
            'precio' => 1799.99,
            'stock' => 25,
            'descripcion' => 'Computadora portátil con procesador Intel y pantalla 4K.',
            'imagen' => 'dell_xps.jpg'
        ),
        array(
            'nombre' => 'Camiseta Premium Hombre',
            'subcategoria' => 'Hombre',
            'precio' => 49.99,
            'stock' => 100,
            'descripcion' => 'Camiseta de algodón 100% de alta calidad para hombre.',
            'imagen' => 'camiseta_hombre.jpg'
        ),
        array(
            'nombre' => 'Pantalón Casual Hombre',
            'subcategoria' => 'Hombre',
            'precio' => 79.99,
            'stock' => 80,
            'descripcion' => 'Pantalón casual de tela resistente, perfecto para uso diario.',
            'imagen' => 'pantalon_hombre.jpg'
        ),
        array(
            'nombre' => 'Vestido Casual Mujer',
            'subcategoria' => 'Mujer',
            'precio' => 89.99,
            'stock' => 60,
            'descripcion' => 'Vestido elegante y cómodo para cualquier ocasión casual.',
            'imagen' => 'vestido_mujer.jpg'
        ),
        array(
            'nombre' => 'Jeans Premium Mujer',
            'subcategoria' => 'Mujer',
            'precio' => 99.99,
            'stock' => 75,
            'descripcion' => 'Jeans de marca reconocida, cómodos y de excelente calidad.',
            'imagen' => 'jeans_mujer.jpg'
        ),
        array(
            'nombre' => 'Horno Eléctrico',
            'subcategoria' => 'Cocina',
            'precio' => 299.99,
            'stock' => 15,
            'descripcion' => 'Horno eléctrico con múltiples funciones para cocinar.',
            'imagen' => 'horno_electrico.jpg'
        ),
        array(
            'nombre' => 'Juego de Cama King',
            'subcategoria' => 'Dormitorio',
            'precio' => 199.99,
            'stock' => 30,
            'descripcion' => 'Juego de sábanas y almohadas tamaño king de algodón.',
            'imagen' => 'juego_cama.jpg'
        )
    );
    
    foreach ($productos as $prod) {
        $nombre = $conn->real_escape_string($prod['nombre']);
        $descripcion = $conn->real_escape_string($prod['descripcion']);
        $precio = $prod['precio'];
        $stock = $prod['stock'];
        $imagen = $prod['imagen'];
        $subcategoria = $prod['subcategoria'];

        $sub_id = $subcategoria_ids[$subcategoria]['id'];
        $cat_id = $subcategoria_ids[$subcategoria]['cat_id'];

        $conn->query("INSERT INTO productos (categoria_id, subcategoria_id, nombre, descripcion, precio, stock, imagen, activo) 
                     VALUES ($cat_id, $sub_id, '$nombre', '$descripcion', $precio, $stock, '$imagen', 1)");
    }
}

// 4) Insertar usuarios de prueba si no existen
$resCli = $conn->query("SELECT COUNT(*) as total FROM clientes");
$totalCli = $resCli ? (int)$resCli->fetch_assoc()['total'] : 0;
if ($totalCli === 0) {
    $usuarios_prueba = array(
        array(
            'usuario' => 'usuario1',
            'password' => hash('sha256', 'pass123'),
            'nombre' => 'Juan García',
            'email' => 'usuario1@tienda.local',
            'telefono' => '3001234567',
            'whatsapp' => '573001234567',
            'ciudad' => 'Bogotá',
            'direccion' => 'Calle 10 #20-30'
        ),
        array(
            'usuario' => 'usuario2',
            'password' => hash('sha256', 'pass123'),
            'nombre' => 'María López',
            'email' => 'usuario2@tienda.local',
            'telefono' => '3009876543',
            'whatsapp' => '573009876543',
            'ciudad' => 'Medellín',
            'direccion' => 'Carrera 45 #50-60'
        )
    );
    
    foreach ($usuarios_prueba as $usr) {
        $usuario = $conn->real_escape_string($usr['usuario']);
        $password = $usr['password'];
        $nombre = $conn->real_escape_string($usr['nombre']);
        $email = $conn->real_escape_string($usr['email']);
        $telefono = $conn->real_escape_string($usr['telefono']);
        $whatsapp = $conn->real_escape_string($usr['whatsapp']);
        $ciudad = $conn->real_escape_string($usr['ciudad']);
        $direccion = $conn->real_escape_string($usr['direccion']);
        
        $conn->query("INSERT INTO clientes (usuario, password, nombre, email, telefono, whatsapp, ciudad, direccion, activo) 
                     VALUES ('$usuario', '$password', '$nombre', '$email', '$telefono', '$whatsapp', '$ciudad', '$direccion', 1)");
    }
}

$conn->close();

return true;
?>
