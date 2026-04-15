<?php
require_once 'config/config.php';
require_once 'config/database.php';

echo "=== ACTUALIZAR CARACTERES ESPECIALES JYM ===\n\n";

function normalizar_texto($texto) {
    $texto = mb_strtolower(trim((string)$texto), 'UTF-8');
    $texto = str_replace(["'", '"', '.', ',', ';', ':', '(', ')', '/', '-', '  '], ' ', $texto);
    $texto = preg_replace('/\s+/', ' ', $texto);

    $convertido = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    if ($convertido !== false) {
        $texto = $convertido;
    }

    return trim($texto);
}

$tenantSlug = 'JYM';
$tenantStmt = $conn->prepare("SELECT id, slug FROM tenants WHERE LOWER(slug) = LOWER(?) LIMIT 1");
$tenantStmt->execute([$tenantSlug]);
$tenant = $tenantStmt->fetch(PDO::FETCH_ASSOC);

if (!$tenant) {
    echo "ERROR: no existe tenant JYM\n";
    exit(1);
}

$tenantId = (int)$tenant['id'];
echo "Tenant: {$tenant['slug']} (ID {$tenantId})\n\n";

$targets = [
    ['nombre' => 'Teatrical Crema Coreana Células Madre de Arroz 100 g', 'categoria' => 'Crema facial'],
    ['nombre' => 'Teatrical Crema Antiarrugas Células Madre de Arroz 200 g', 'categoria' => 'Crema facial'],
    ['nombre' => 'Teatrical Crema Aclaradora con Células Madre de Rosa Blanca 200 g', 'categoria' => 'Crema facial'],
    ['nombre' => 'Teatrical Crema Humectante con Células Madre de Aguacate 200 g', 'categoria' => 'Crema facial'],
    ['nombre' => 'Teatrical Aclarador Facial 75 ml', 'categoria' => 'Cuidado facial'],
    ['nombre' => "Pond's Crema Bio-Hidratante 200 g", 'categoria' => 'Crema facial'],
    ['nombre' => 'Acetaminofén AG Tabletas', 'categoria' => 'Analgésico'],
    ['nombre' => 'X Ray Dol Analgésico (Acetaminofén + Naproxeno + Cafeína) 48 tabletas', 'categoria' => 'Analgésico'],
    ['nombre' => 'X Ray Dol Analgésico Caja Pequeña', 'categoria' => 'Analgésico'],
    ['nombre' => 'Ibuprofeno 800 mg 50 Tabletas Recubiertas Genfar', 'categoria' => 'Analgésico / Antiinflamatorio'],
    ['nombre' => 'Noraver Garganta Sabor Miel', 'categoria' => 'Pastillas para garganta'],
    ['nombre' => 'Noraver Garganta Menta Forte', 'categoria' => 'Pastillas para garganta'],
    ['nombre' => 'Noraver Garganta Sabor Cereza 12 Tabletas', 'categoria' => 'Pastillas para garganta'],
    ['nombre' => 'Florausi Esporas de Bacillus Clausii Suspensión Oral 5 ml', 'categoria' => 'Probiótico'],
    ['nombre' => 'Vitamina E Procaps', 'categoria' => 'Vitamina'],
    ['nombre' => 'Dermaskin Crema Clotrimazol + Neomicina + Dexametasona 20 g', 'categoria' => 'Crema dermatológica'],
    ['nombre' => "Pond's Clarant B3 Crema Aclarante", 'categoria' => 'Crema facial'],
    ['nombre' => "Pond's Rejuveness Crema Antiarrugas", 'categoria' => 'Crema facial'],
    ['nombre' => "Pond's Crema S Humectante 400 g", 'categoria' => 'Crema hidratante'],
    ['nombre' => "Pond's Crema S FPS 30 Crema Facial con Protección Solar", 'categoria' => 'Protector solar facial'],
    ['nombre' => "Pond's Bright Miracle Ultimate Clarity Serum Facial 50x Niasorcinol", 'categoria' => 'Serum facial'],
    ['nombre' => "Pond's Clarant B3 Promoción Pague 100 g Lleve 150 g", 'categoria' => 'Crema facial'],
    ['nombre' => "Pond's Limpiador Facial Diario 50 g", 'categoria' => 'Limpiador facial'],
    ['nombre' => 'Nivea Facial 5 en 1 Cuidado Anti-Arrugas', 'categoria' => 'Crema facial'],
    ['nombre' => 'Nivea Facial 5 en 1 Cuidado Nutritivo', 'categoria' => 'Crema facial'],
];

try {
    $conn->beginTransaction();

    $catSelAll = $conn->prepare("SELECT id, nombre FROM categorias WHERE tenant_id = ? ORDER BY id");
    $catSelAll->execute([$tenantId]);
    $categorias = $catSelAll->fetchAll(PDO::FETCH_ASSOC);

    $catByNorm = [];
    foreach ($categorias as $c) {
        $catByNorm[normalizar_texto($c['nombre'])] = ['id' => (int)$c['id'], 'nombre' => $c['nombre']];
    }

    $catIns = $conn->prepare("INSERT INTO categorias (tenant_id, nombre, descripcion, activa) VALUES (?, ?, '', 1) RETURNING id");
    $catUpd = $conn->prepare("UPDATE categorias SET nombre = ? WHERE id = ? AND tenant_id = ?");

    $categoriasActualizadas = 0;
    $categoriasCreadas = 0;

    foreach ($targets as $t) {
        $norm = normalizar_texto($t['categoria']);
        if (isset($catByNorm[$norm])) {
            $cat = $catByNorm[$norm];
            if ($cat['nombre'] !== $t['categoria']) {
                $catUpd->execute([$t['categoria'], $cat['id'], $tenantId]);
                $categoriasActualizadas++;
                $catByNorm[$norm]['nombre'] = $t['categoria'];
                echo "Categoría actualizada: {$cat['nombre']} -> {$t['categoria']}\n";
            }
        } else {
            $catIns->execute([$tenantId, $t['categoria']]);
            $idNueva = (int)$catIns->fetchColumn();
            $catByNorm[$norm] = ['id' => $idNueva, 'nombre' => $t['categoria']];
            $categoriasCreadas++;
            echo "Categoría creada: {$t['categoria']} (ID {$idNueva})\n";
        }
    }

    $prodSelAll = $conn->prepare("SELECT id, nombre, categoria_id FROM productos WHERE tenant_id = ? ORDER BY id");
    $prodSelAll->execute([$tenantId]);
    $productos = $prodSelAll->fetchAll(PDO::FETCH_ASSOC);

    $productosPorNorm = [];
    foreach ($productos as $p) {
        $norm = normalizar_texto($p['nombre']);
        if (!isset($productosPorNorm[$norm])) {
            $productosPorNorm[$norm] = [];
        }
        $productosPorNorm[$norm][] = $p;
    }

    $prodUpd = $conn->prepare("UPDATE productos SET nombre = ?, categoria_id = ? WHERE id = ? AND tenant_id = ?");

    $actualizados = 0;
    $noEncontrados = 0;

    foreach ($targets as $t) {
        $normNombre = normalizar_texto($t['nombre']);
        $normCat = normalizar_texto($t['categoria']);

        if (empty($productosPorNorm[$normNombre])) {
            $noEncontrados++;
            echo "No encontrado: {$t['nombre']}\n";
            continue;
        }

        $producto = array_shift($productosPorNorm[$normNombre]);
        $catId = (int)$catByNorm[$normCat]['id'];

        $necesita = ($producto['nombre'] !== $t['nombre']) || ((int)$producto['categoria_id'] !== $catId);
        if ($necesita) {
            $prodUpd->execute([$t['nombre'], $catId, (int)$producto['id'], $tenantId]);
            $actualizados++;
            echo "Producto actualizado (ID {$producto['id']}): {$producto['nombre']} -> {$t['nombre']}\n";
        }
    }

    $conn->commit();

    echo "\n=== RESUMEN ===\n";
    echo "Categorías actualizadas: {$categoriasActualizadas}\n";
    echo "Categorías creadas: {$categoriasCreadas}\n";
    echo "Productos actualizados: {$actualizados}\n";
    echo "Productos no encontrados: {$noEncontrados}\n";

} catch (Throwable $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "\nERROR: " . $e->getMessage() . "\n";
    exit(1);
}
