<?php
/**
 * Script de migración - Actualizar tabla clientes
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'catalogo_tienda');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

echo "Iniciando migración...\n\n";

// Verificar si la columna 'usuario' existe
$resultado = $conn->query("SHOW COLUMNS FROM clientes LIKE 'usuario'");

if ($resultado->num_rows == 0) {
    echo "Agregando columna 'usuario' a tabla clientes...\n";
    $conn->query("ALTER TABLE clientes ADD COLUMN usuario VARCHAR(50) UNIQUE AFTER id");
    echo "✓ Columna 'usuario' agregada\n";
} else {
    echo "✓ Columna 'usuario' ya existe\n";
}

// Verificar si la columna 'password' existe
$resultado = $conn->query("SHOW COLUMNS FROM clientes LIKE 'password'");

if ($resultado->num_rows == 0) {
    echo "Agregando columna 'password' a tabla clientes...\n";
    $conn->query("ALTER TABLE clientes ADD COLUMN password VARCHAR(255) AFTER usuario");
    echo "✓ Columna 'password' agregada\n";
} else {
    echo "✓ Columna 'password' ya existe\n";
}

// Verificar si la columna 'activo' existe
$resultado = $conn->query("SHOW COLUMNS FROM clientes LIKE 'activo'");

if ($resultado->num_rows == 0) {
    echo "Agregando columna 'activo' a tabla clientes...\n";
    $conn->query("ALTER TABLE clientes ADD COLUMN activo INT DEFAULT 1");
    echo "✓ Columna 'activo' agregada\n";
} else {
    echo "✓ Columna 'activo' ya existe\n";
}

// Verificar si la columna 'email' existe y es UNIQUE
$resultado = $conn->query("SHOW COLUMNS FROM clientes LIKE 'email'");
if ($resultado->num_rows == 0) {
    echo "Agregando columna 'email' a tabla clientes...\n";
    $conn->query("ALTER TABLE clientes ADD COLUMN email VARCHAR(100) UNIQUE");
    echo "✓ Columna 'email' agregada\n";
}

// Verificar si tenemos índices
echo "\nAñadiendo índices...\n";
// Intentar agregar índices si no existen
$conn->query("ALTER TABLE clientes ADD INDEX IF NOT EXISTS idx_usuario (usuario)");
$conn->query("ALTER TABLE clientes ADD INDEX IF NOT EXISTS idx_email (email)");
echo "✓ Índices verificados\n";

// Insertar usuarios de prueba si no existen
echo "\nInsertando usuarios de prueba...\n";

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
    
    // Verificar si ya existe
    $check = $conn->query("SELECT id FROM clientes WHERE usuario='$usuario' OR email='$email'");
    
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO clientes (usuario, password, nombre, email, telefono, whatsapp, ciudad, direccion, activo) 
                     VALUES ('$usuario', '$password', '$nombre', '$email', '$telefono', '$whatsapp', '$ciudad', '$direccion', 1)");
        echo "✓ Usuario '{$usuario}' creado\n";
    } else {
        echo "✓ Usuario '{$usuario}' ya existe\n";
    }
}

echo "\n¡Migración completada exitosamente!\n";

$conn->close();
?>
