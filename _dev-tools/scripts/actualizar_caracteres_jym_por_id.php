<?php
require_once 'config/config.php';
require_once 'config/database.php';

echo "=== ACTUALIZACION PRECISA DE CARACTERES (JYM) ===\n\n";

$tenantId = 6;

$updates = [
    ['id' => 29, 'nombre' => 'Teatrical Crema Coreana Células Madre de Arroz 100 g', 'categoria_id' => 31],
    ['id' => 30, 'nombre' => 'Teatrical Crema Antiarrugas Células Madre de Arroz 200 g', 'categoria_id' => 31],
    ['id' => 31, 'nombre' => 'Teatrical Crema Aclaradora con Células Madre de Rosa Blanca 200 g', 'categoria_id' => 31],
    ['id' => 32, 'nombre' => 'Teatrical Crema Humectante con Células Madre de Aguacate 200 g', 'categoria_id' => 31],
    ['id' => 33, 'nombre' => 'Teatrical Aclarador Facial 75 ml', 'categoria_id' => 30],
    ['id' => 34, 'nombre' => "Pond's Crema Bio-Hidratante 200 g", 'categoria_id' => 31],
    ['id' => 35, 'nombre' => 'Acetaminofén AG Tabletas', 'categoria_id' => 42],
    ['id' => 36, 'nombre' => 'X Ray Dol Analgésico (Acetaminofén + Naproxeno + Cafeína) 48 tabletas', 'categoria_id' => 42],
    ['id' => 37, 'nombre' => 'X Ray Dol Analgésico Caja Pequeña', 'categoria_id' => 42],
    ['id' => 38, 'nombre' => 'Ibuprofeno 800 mg 50 Tabletas Recubiertas Genfar', 'categoria_id' => 43],
    ['id' => 39, 'nombre' => 'Noraver Garganta Sabor Miel', 'categoria_id' => 34],
    ['id' => 40, 'nombre' => 'Noraver Garganta Menta Forte', 'categoria_id' => 34],
    ['id' => 41, 'nombre' => 'Noraver Garganta Sabor Cereza 12 Tabletas', 'categoria_id' => 34],
    ['id' => 42, 'nombre' => 'Florausi Esporas de Bacillus Clausii Suspensión Oral 5 ml', 'categoria_id' => 44],
    ['id' => 43, 'nombre' => 'Vitamina E Procaps', 'categoria_id' => 36],
    ['id' => 44, 'nombre' => 'Dermaskin Crema Clotrimazol + Neomicina + Dexametasona 20 g', 'categoria_id' => 45],
    ['id' => 45, 'nombre' => "Pond's Clarant B3 Crema Aclarante", 'categoria_id' => 31],
    ['id' => 46, 'nombre' => "Pond's Rejuveness Crema Antiarrugas", 'categoria_id' => 31],
    ['id' => 47, 'nombre' => "Pond's Crema S Humectante 400 g", 'categoria_id' => 38],
    ['id' => 48, 'nombre' => "Pond's Crema S FPS 30 Crema Facial con Protección Solar", 'categoria_id' => 39],
    ['id' => 49, 'nombre' => "Pond's Bright Miracle Ultimate Clarity Serum Facial 50x Niasorcinol", 'categoria_id' => 40],
    ['id' => 50, 'nombre' => "Pond's Clarant B3 Promoción Pague 100 g Lleve 150 g", 'categoria_id' => 31],
    ['id' => 51, 'nombre' => "Pond's Limpiador Facial Diario 50 g", 'categoria_id' => 41],
    ['id' => 52, 'nombre' => 'Nivea Facial 5 en 1 Cuidado Anti-Arrugas', 'categoria_id' => 31],
    ['id' => 53, 'nombre' => 'Nivea Facial 5 en 1 Cuidado Nutritivo', 'categoria_id' => 31],
];

$sql = "UPDATE productos SET nombre = ?, categoria_id = ? WHERE id = ? AND tenant_id = ?";
$stmt = $conn->prepare($sql);

$ok = 0;
$fail = 0;

$conn->beginTransaction();
try {
    foreach ($updates as $u) {
        $stmt->execute([$u['nombre'], $u['categoria_id'], $u['id'], $tenantId]);
        if ($stmt->rowCount() >= 0) {
            $ok++;
        } else {
            $fail++;
        }
    }
    $conn->commit();

    echo "Registros procesados: " . count($updates) . "\n";
    echo "Actualizaciones aplicadas: {$ok}\n";
    echo "Fallidas: {$fail}\n\n";

    $verify = $conn->prepare("SELECT id, nombre FROM productos WHERE tenant_id = ? AND id BETWEEN 29 AND 53 ORDER BY id");
    $verify->execute([$tenantId]);
    foreach ($verify->fetchAll(PDO::FETCH_ASSOC) as $r) {
        echo $r['id'] . ' | ' . $r['nombre'] . "\n";
    }

} catch (Throwable $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
