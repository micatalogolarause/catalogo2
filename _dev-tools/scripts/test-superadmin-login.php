<?php
// Script de diagnóstico para validar super-admin login

define('APP_URL', 'http://localhost/catalogo2');

require_once 'config/database.php';

echo "<h2>Test Super Admin Login</h2>";

// 1. Verificar usuario en BD
$sql = "SELECT id, usuario, nombre, rol, tenant_id, activo, password FROM usuarios WHERE rol='superadmin'";
$result = $conn->query($sql);

echo "<h3>Usuarios super-admin en BD:</h3>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<pre>";
        echo "ID: " . $row['id'] . "\n";
        echo "Usuario: " . $row['usuario'] . "\n";
        echo "Nombre: " . $row['nombre'] . "\n";
        echo "Rol: " . $row['rol'] . "\n";
        echo "Tenant ID: " . ($row['tenant_id'] ?? 'NULL') . "\n";
        echo "Activo: " . $row['activo'] . "\n";
        echo "Password (primeros 20 chars): " . substr($row['password'], 0, 20) . "...\n";
        echo "Password completo length: " . strlen($row['password']) . "\n";
        echo "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ No se encontró ningún usuario super-admin</p>";
}

// 2. Probar hash de contraseña
echo "<hr><h3>Test de Hash de Contraseña:</h3>";
$password_test = 'SuperAdmin123!';
$hash_generado = hash('sha256', $password_test);

echo "<pre>";
echo "Contraseña de prueba: {$password_test}\n";
echo "Hash generado: {$hash_generado}\n";
echo "Hash length: " . strlen($hash_generado) . "\n";
echo "</pre>";

// 3. Intentar login con los datos
echo "<hr><h3>Simulación de Login:</h3>";
$usuario = 'superadmin';
$password = 'SuperAdmin123!';
$password_hash = hash('sha256', $password);

$sql = "SELECT id, usuario, nombre, rol FROM usuarios 
        WHERE usuario = ? AND password = ? AND rol = 'superadmin' AND activo = 1 AND tenant_id IS NULL";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usuario, $password_hash);
$stmt->execute();
$result = $stmt->get_result();
$superadmin = $result->fetch_assoc();

if ($superadmin) {
    echo "<p style='color: green;'>✅ Login exitoso!</p>";
    echo "<pre>";
    print_r($superadmin);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ Login fallido</p>";
    
    // Probar sin tenant_id IS NULL
    $sql2 = "SELECT id, usuario, nombre, rol, tenant_id FROM usuarios 
            WHERE usuario = ? AND password = ? AND rol = 'superadmin' AND activo = 1";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ss", $usuario, $password_hash);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $superadmin2 = $result2->fetch_assoc();
    
    if ($superadmin2) {
        echo "<p style='color: orange;'>⚠️ Usuario encontrado pero tenant_id no es NULL:</p>";
        echo "<pre>";
        print_r($superadmin2);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>❌ Usuario no encontrado ni siquiera sin condición tenant_id</p>";
        
        // Verificar solo por usuario
        $sql3 = "SELECT id, usuario, password, rol, tenant_id, activo FROM usuarios WHERE usuario = ?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("s", $usuario);
        $stmt3->execute();
        $result3 = $stmt3->get_result();
        
        if ($result3->num_rows > 0) {
            echo "<p style='color: orange;'>Usuario existe, verificando diferencias:</p>";
            while ($row = $result3->fetch_assoc()) {
                echo "<pre>";
                echo "Usuario: " . $row['usuario'] . "\n";
                echo "Password BD: " . $row['password'] . "\n";
                echo "Password test: " . $password_hash . "\n";
                echo "Coinciden? " . ($row['password'] === $password_hash ? 'SI' : 'NO') . "\n";
                echo "Rol: " . $row['rol'] . "\n";
                echo "Tenant ID: " . ($row['tenant_id'] ?? 'NULL') . "\n";
                echo "Activo: " . $row['activo'] . "\n";
                echo "</pre>";
            }
        }
    }
}

$conn->close();
?>
