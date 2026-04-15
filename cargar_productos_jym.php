<?php
require_once 'config/config.php';
require_once 'config/database.php';

echo "=== CARGA MASIVA PRODUCTOS JYM ===\n\n";

$tenantSlug = 'JYM';
$tenantStmt = $conn->prepare("SELECT id, slug FROM tenants WHERE LOWER(slug) = LOWER(?) LIMIT 1");
$tenantStmt->execute([$tenantSlug]);
$tenant = $tenantStmt->fetch(PDO::FETCH_ASSOC);

if (!$tenant) {
    echo "ERROR: No existe tenant con slug JYM\n";
    exit(1);
}

$tenantId = (int)$tenant['id'];
echo "Tenant encontrado: ID={$tenantId}, slug={$tenant['slug']}\n\n";

$items = [
    ['nombre' => 'Teatrical Crema Coreana Celulas Madre de Arroz 100 g', 'categoria' => 'Crema facial'],
    ['nombre' => 'Teatrical Crema Antiarrugas Celulas Madre de Arroz 200 g', 'categoria' => 'Crema facial'],
    ['nombre' => 'Teatrical Crema Aclaradora con Celulas Madre de Rosa Blanca 200 g', 'categoria' => 'Crema facial'],
    ['nombre' => 'Teatrical Crema Humectante con Celulas Madre de Aguacate 200 g', 'categoria' => 'Crema facial'],
    ['nombre' => 'Teatrical Aclarador Facial 75 ml', 'categoria' => 'Cuidado facial'],
    ['nombre' => "Pond's Crema Bio-Hidratante 200 g", 'categoria' => 'Crema facial'],
    ['nombre' => 'Acetaminofen AG Tabletas', 'categoria' => 'Analgesico'],
    ['nombre' => 'X Ray Dol Analgesico (Acetaminofen + Naproxeno + Cafeina) 48 tabletas', 'categoria' => 'Analgesico'],
    ['nombre' => 'X Ray Dol Analgesico Caja Pequena', 'categoria' => 'Analgesico'],
    ['nombre' => 'Ibuprofeno 800 mg 50 Tabletas Recubiertas Genfar', 'categoria' => 'Analgesico / Antiinflamatorio'],
    ['nombre' => 'Noraver Garganta Sabor Miel', 'categoria' => 'Pastillas para garganta'],
    ['nombre' => 'Noraver Garganta Menta Forte', 'categoria' => 'Pastillas para garganta'],
    ['nombre' => 'Noraver Garganta Sabor Cereza 12 Tabletas', 'categoria' => 'Pastillas para garganta'],
    ['nombre' => 'Florausi Esporas de Bacillus Clausii Suspension Oral 5 ml', 'categoria' => 'Probiotico'],
    ['nombre' => 'Vitamina E Procaps', 'categoria' => 'Vitamina'],
    ['nombre' => 'Dermaskin Crema Clotrimazol + Neomicina + Dexametasona 20 g', 'categoria' => 'Crema dermatologica'],
    ['nombre' => "Pond's Clarant B3 Crema Aclarante", 'categoria' => 'Crema facial'],
    ['nombre' => "Pond's Rejuveness Crema Antiarrugas", 'categoria' => 'Crema facial'],
    ['nombre' => "Pond's Crema S Humectante 400 g", 'categoria' => 'Crema hidratante'],
    ['nombre' => "Pond's Crema S FPS 30 Crema Facial con Proteccion Solar", 'categoria' => 'Protector solar facial'],
    ['nombre' => "Pond's Bright Miracle Ultimate Clarity Serum Facial 50x Niasorcinol", 'categoria' => 'Serum facial'],
    ['nombre' => "Pond's Clarant B3 Promocion Pague 100 g Lleve 150 g", 'categoria' => 'Crema facial'],
    ['nombre' => "Pond's Limpiador Facial Diario 50 g", 'categoria' => 'Limpiador facial'],
    ['nombre' => 'Nivea Facial 5 en 1 Cuidado Anti-Arrugas', 'categoria' => 'Crema facial'],
    ['nombre' => 'Nivea Facial 5 en 1 Cuidado Nutritivo', 'categoria' => 'Crema facial'],
];

try {
    $conn->beginTransaction();

    $catSel = $conn->prepare("SELECT id FROM categorias WHERE tenant_id = ? AND LOWER(nombre) = LOWER(?) LIMIT 1");
    $catIns = $conn->prepare("INSERT INTO categorias (tenant_id, nombre, descripcion, activa) VALUES (?, ?, '', 1) RETURNING id");

    $prodSel = $conn->prepare("SELECT id FROM productos WHERE tenant_id = ? AND LOWER(nombre) = LOWER(?) LIMIT 1");
    $prodIns = $conn->prepare("INSERT INTO productos (tenant_id, categoria_id, nombre, descripcion, precio, stock, imagen, imagen2, imagen3, numero_producto) VALUES (?, ?, ?, '', 0, 0, 'sin-imagen.jpg', '', '', ?) RETURNING id");

    $maxStmt = $conn->prepare("SELECT COALESCE(MAX(numero_producto), 0) FROM productos WHERE tenant_id = ?");
    $maxStmt->execute([$tenantId]);
    $numeroProducto = (int)$maxStmt->fetchColumn();

    $catsCreated = 0;
    $productsInserted = 0;
    $productsSkipped = 0;

    foreach ($items as $item) {
        $catSel->execute([$tenantId, $item['categoria']]);
        $categoriaId = (int)$catSel->fetchColumn();

        if ($categoriaId <= 0) {
            $catIns->execute([$tenantId, $item['categoria']]);
            $categoriaId = (int)$catIns->fetchColumn();
            $catsCreated++;
            echo "Categoria creada: {$item['categoria']} (ID {$categoriaId})\n";
        }

        $prodSel->execute([$tenantId, $item['nombre']]);
        $exists = (int)$prodSel->fetchColumn();
        if ($exists > 0) {
            $productsSkipped++;
            echo "Saltado (ya existe): {$item['nombre']}\n";
            continue;
        }

        $numeroProducto++;
        $prodIns->execute([$tenantId, $categoriaId, $item['nombre'], $numeroProducto]);
        $newId = (int)$prodIns->fetchColumn();

        $productsInserted++;
        echo "Insertado: {$item['nombre']} (ID {$newId})\n";
    }

    $conn->commit();

    echo "\n=== RESUMEN ===\n";
    echo "Categorias creadas: {$catsCreated}\n";
    echo "Productos insertados: {$productsInserted}\n";
    echo "Productos saltados: {$productsSkipped}\n";
    echo "Total procesados: " . count($items) . "\n";

} catch (Throwable $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "\nERROR: " . $e->getMessage() . "\n";
    exit(1);
}
