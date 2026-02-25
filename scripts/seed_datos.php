<?php
/**
 * Script para generar datos de prueba en cada tenant
 * Ejecutar desde: http://localhost/catalogo2/scripts/seed_datos.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/CategoriaModel.php';
require_once __DIR__ . '/../app/models/SubcategoriaModel.php';
require_once __DIR__ . '/../app/models/ProductoModel.php';
require_once __DIR__ . '/../app/models/PedidoModel.php';

// Conectar a BD
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    die("Error conexión: " . $db->connect_error);
}

$db->set_charset('utf8mb4');

// Obtener todos los tenants
$queryTenants = "SELECT id, nombre FROM tenants WHERE estado = 'activo'";
$resultTenants = $db->query($queryTenants);

if (!$resultTenants) {
    die("Error: " . $db->error);
}

$seedCount = 0;

echo "<h2>Generador de Datos de Prueba</h2>";
echo "<hr>";

while ($tenant = $resultTenants->fetch_assoc()) {
    $tenantId = $tenant['id'];
    $tenantNombre = $tenant['nombre'];
    
    echo "<h3>Procesando tenant: {$tenantNombre} (ID: {$tenantId})</h3>";
    
    // Array de datos
    $categorias = [
        ['nombre' => 'Electrónica', 'descripcion' => 'Productos electrónicos'],
        ['nombre' => 'Ropa', 'descripcion' => 'Prendas de vestir'],
        ['nombre' => 'Hogar', 'descripcion' => 'Artículos para el hogar']
    ];
    
    $subcategoriasPorCategoria = [
        'Electrónica' => [
            ['nombre' => 'Smartphones', 'descripcion' => 'Teléfonos móviles'],
            ['nombre' => 'Laptops', 'descripcion' => 'Computadoras portátiles'],
            ['nombre' => 'Accesorios', 'descripcion' => 'Accesorios electrónicos']
        ],
        'Ropa' => [
            ['nombre' => 'Hombres', 'descripcion' => 'Ropa para hombres'],
            ['nombre' => 'Mujeres', 'descripcion' => 'Ropa para mujeres'],
            ['nombre' => 'Niños', 'descripcion' => 'Ropa para niños']
        ],
        'Hogar' => [
            ['nombre' => 'Cocina', 'descripcion' => 'Artículos de cocina'],
            ['nombre' => 'Muebles', 'descripcion' => 'Muebles para el hogar'],
            ['nombre' => 'Decoración', 'descripcion' => 'Artículos de decoración']
        ]
    ];
    
    // Crear categorías
    $categoriasIds = [];
    foreach ($categorias as $cat) {
        $nombre = $db->real_escape_string($cat['nombre']);
        $desc = $db->real_escape_string($cat['descripcion']);
        
        $checkCat = "SELECT id FROM categorias WHERE tenant_id = {$tenantId} AND nombre = '{$nombre}'";
        $resCat = $db->query($checkCat);
        
        if ($resCat->num_rows == 0) {
            $insertCat = "INSERT INTO categorias (tenant_id, nombre, descripcion, activa, fecha_creacion) 
                         VALUES ({$tenantId}, '{$nombre}', '{$desc}', 1, NOW())";
            if ($db->query($insertCat)) {
                $categoriasIds[$nombre] = $db->insert_id;
                echo "✓ Categoría creada: {$nombre}<br>";
            } else {
                echo "✗ Error creando categoría: " . $db->error . "<br>";
            }
        } else {
            $row = $resCat->fetch_assoc();
            $categoriasIds[$nombre] = $row['id'];
            echo "ℹ Categoría ya existe: {$nombre}<br>";
        }
    }
    
    // Crear subcategorías
    $subcategoriasIds = [];
    foreach ($categoriasIds as $catNombre => $catId) {
        foreach ($subcategoriasPorCategoria[$catNombre] as $sub) {
            $nombre = $db->real_escape_string($sub['nombre']);
            $desc = $db->real_escape_string($sub['descripcion']);
            
            $checkSub = "SELECT id FROM subcategorias WHERE tenant_id = {$tenantId} AND categoria_id = {$catId} AND nombre = '{$nombre}'";
            $resSub = $db->query($checkSub);
            
            if ($resSub->num_rows == 0) {
                $insertSub = "INSERT INTO subcategorias (tenant_id, categoria_id, nombre, descripcion, activa, fecha_creacion) 
                             VALUES ({$tenantId}, {$catId}, '{$nombre}', '{$desc}', 1, NOW())";
                if ($db->query($insertSub)) {
                    $subcategoriasIds[$nombre] = $db->insert_id;
                    echo "  ✓ Subcategoría creada: {$nombre}<br>";
                } else {
                    echo "  ✗ Error: " . $db->error . "<br>";
                }
            } else {
                $row = $resSub->fetch_assoc();
                $subcategoriasIds[$nombre] = $row['id'];
                echo "  ℹ Subcategoría ya existe: {$nombre}<br>";
            }
        }
    }
    
    // Crear 10 productos por subcategoría (90 productos totales)
    $productos = [
        'Smartphones' => [
            'Samsung Galaxy S21', 'iPhone 13 Pro', 'Xiaomi 12', 'Google Pixel 6',
            'OnePlus 9', 'Motorola Edge', 'Realme GT', 'Nothing Phone',
            'Sony Xperia', 'OPPO Find X3'
        ],
        'Laptops' => [
            'MacBook Pro 16', 'Dell XPS 13', 'HP Pavilion 15', 'Lenovo ThinkPad',
            'ASUS VivoBook', 'Acer Aspire 5', 'MSI GS66', 'Razer Blade',
            'LG Gram', 'ROG Gaming Laptop'
        ],
        'Accesorios' => [
            'Cargador Rápido', 'Cable USB-C', 'Protector Pantalla', 'Funda Teléfono',
            'Audífonos Inalámbricos', 'Power Bank', 'Soporte Móvil', 'Protector Cámara',
            'Anillo Soporte', 'Cable HDMI'
        ],
        'Hombres' => [
            'Camiseta Básica', 'Pantalón Denim', 'Camisa Social', 'Polo Premium',
            'Shorts Deportivos', 'Chaqueta Casual', 'Suéter Lana', 'Bermudas',
            'Camiseta Deportiva', 'Pantalón Cargo'
        ],
        'Mujeres' => [
            'Blusa Elegante', 'Jeans Skinny', 'Vestido Casual', 'Top Deportivo',
            'Falda Midi', 'Chaqueta Mezclilla', 'Leggings Premium', 'Cardigan',
            'Blusa Floral', 'Pantalón Palazzo'
        ],
        'Niños' => [
            'Camiseta Niño', 'Pantalón Niño', 'Sudadera Infantil', 'Shorts Niño',
            'Falda Niña', 'Blusa Niña', 'Pantalón Niña', 'Chaqueta Infantil',
            'Buzo Infantil', 'Leggings Niña'
        ],
        'Cocina' => [
            'Licuadora', 'Microondas', 'Cafetera', 'Tostador',
            'Olla Arrocera', 'Plancha Eléctrica', 'Freidora Aire', 'Batidora',
            'Exprimidor', 'Hervidor Eléctrico'
        ],
        'Muebles' => [
            'Sofá 3 Puestos', 'Mesa Comedor', 'Silla Oficina', 'Cama Queen',
            'Closet Madera', 'Biblioteca', 'Mesita Noche', 'Escritorio',
            'Estantería', 'Modular Tv'
        ],
        'Decoración' => [
            'Cuadro Moderno', 'Espejo Decorativo', 'Lámpara Piso', 'Cojín',
            'Cortina Premium', 'Tapete', 'Jarrón Cerámica', 'Alfombra',
            'Vela Aromática', 'Reloj Pared'
        ]
    ];
    
    $productoCount = 0;
    foreach ($subcategoriasIds as $subNombre => $subId) {
        $categoriaPrueba = null;
        foreach (array_keys($subcategoriasPorCategoria) as $catNombre) {
            if (in_array($subNombre, array_column($subcategoriasPorCategoria[$catNombre], 'nombre'))) {
                $categoriaPrueba = $catNombre;
                break;
            }
        }
        
        if (!isset($productos[$subNombre])) {
            continue;
        }
        
        foreach ($productos[$subNombre] as $idx => $nomProducto) {
            $nombre = $db->real_escape_string($nomProducto);
            $checkProd = "SELECT id FROM productos WHERE tenant_id = {$tenantId} AND nombre = '{$nombre}'";
            $resProd = $db->query($checkProd);
            
            if ($resProd->num_rows == 0) {
                $precio = rand(50, 500);
                $stock = rand(10, 50);
                $imagen = "producto_" . uniqid() . ".jpg";
                
                $insertProd = "INSERT INTO productos (tenant_id, categoria_id, subcategoria_id, nombre, descripcion, precio, stock, imagen, activo, fecha_creacion) 
                             VALUES ({$tenantId}, {$categoriasIds[$categoriaPrueba]}, {$subId}, '{$nombre}', 'Descripción de {$nombre}', {$precio}, {$stock}, '{$imagen}', 1, NOW())";
                
                if ($db->query($insertProd)) {
                    $productoCount++;
                    echo "  ✓ Producto creado: {$nomProducto}<br>";
                } else {
                    echo "  ✗ Error: " . $db->error . "<br>";
                }
            }
        }
    }
    
    echo "Total productos creados: {$productoCount}<br>";
    
    // Crear 10 clientes primero
    $clientesIds = [];
    for ($i = 0; $i < 10; $i++) {
        $nombre = "Cliente Prueba " . ($i + 1);
        $email = "cliente" . $i . "@test.com";
        $whatsapp = "573" . str_pad(rand(0, 9999999), 7, "0", STR_PAD_LEFT);
        $direccion = "Dirección " . ($i + 1) . ", Apto " . rand(1, 500);
        
        $nombreEsc = $db->real_escape_string($nombre);
        $emailEsc = $db->real_escape_string($email);
        $dirEsc = $db->real_escape_string($direccion);
        
        $checkCliente = "SELECT id FROM clientes WHERE tenant_id = {$tenantId} AND email = '{$emailEsc}'";
        $resCliente = $db->query($checkCliente);
        
        if ($resCliente->num_rows == 0) {
            $insertCliente = "INSERT INTO clientes (tenant_id, nombre, email, whatsapp, direccion, fecha_registro) 
                            VALUES ({$tenantId}, '{$nombreEsc}', '{$emailEsc}', '{$whatsapp}', '{$dirEsc}', NOW())";
            if ($db->query($insertCliente)) {
                $clientesIds[] = $db->insert_id;
                echo "  ✓ Cliente creado: {$nombre}<br>";
            }
        } else {
            $row = $resCliente->fetch_assoc();
            $clientesIds[] = $row['id'];
            echo "  ℹ Cliente ya existe: {$nombre}<br>";
        }
    }
    
    // Crear 10 pedidos
    $pedidoCount = 0;
    $queryProductos = "SELECT id, precio FROM productos WHERE tenant_id = {$tenantId} LIMIT 20";
    $resProductos = $db->query($queryProductos);
    $productosDisponibles = [];
    while ($prod = $resProductos->fetch_assoc()) {
        $productosDisponibles[] = $prod;
    }
    
    for ($i = 0; $i < 10; $i++) {
        $clienteId = $clientesIds[$i];
        $total = 0;
        
        $estados = ['en_pedido', 'alistado', 'empaquetado', 'verificado', 'en_reparto', 'entregado'];
        $estado = $estados[array_rand($estados)];
        
        $insertPedido = "INSERT INTO pedidos (tenant_id, cliente_id, total, notas_cliente, fecha_creacion) 
                       VALUES ({$tenantId}, {$clienteId}, 0, 'Pedido de prueba', NOW())";
        
        if ($db->query($insertPedido)) {
            $pedidoId = $db->insert_id;
            
            // Agregar 3-5 productos al pedido
            $cantProductos = rand(3, 5);
            for ($j = 0; $j < $cantProductos; $j++) {
                $prod = $productosDisponibles[array_rand($productosDisponibles)];
                $cantidad = rand(1, 3);
                $subtotal = $prod['precio'] * $cantidad;
                $total += $subtotal;
                
                $insertDetalle = "INSERT INTO pedido_detalles (tenant_id, pedido_id, producto_id, cantidad, precio_unitario, subtotal) 
                                VALUES ({$tenantId}, {$pedidoId}, {$prod['id']}, {$cantidad}, {$prod['precio']}, {$subtotal})";
                $db->query($insertDetalle);
            }
            
            // Actualizar total en pedido
            $updatePedido = "UPDATE pedidos SET total = {$total} WHERE id = {$pedidoId}";
            $db->query($updatePedido);
            
            $pedidoCount++;
            echo "  ✓ Pedido #{$pedidoId} creado (Total: \${$total})<br>";
        } else {
            echo "  ✗ Error: " . $db->error . "<br>";
        }
    }
    
    echo "Total pedidos creados: {$pedidoCount}<br>";
    echo "<hr>";
    $seedCount++;
}

echo "<h2>✓ Seed completado para {$seedCount} tenant(s)</h2>";
echo "<p><a href='" . APP_URL . "'>Ir al inicio</a></p>";

$db->close();
?>
